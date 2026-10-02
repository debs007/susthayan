<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use App\Jobs\DownloadProductImageJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Handles a catalogue file the size of the real one (~96MB, ~1 million
 * rows) - every design choice here exists because of that scale:
 *  - fgetcsv() streams one row at a time, never json_decode/file_get_contents
 *    the whole file into memory.
 *  - Rows are bulk-inserted in chunks (CHUNK_SIZE), not one at a time -
 *    a million individual INSERT statements would take hours.
 *  - Product IDs after a bulk insert are looked up by slug, not assumed
 *    from auto-increment, since that assumption breaks under any
 *    concurrent write to the products table.
 *  - Query logging is disabled up front - Laravel's query log otherwise
 *    accumulates every query in memory for the life of the request/job,
 *    which would itself exhaust memory over a million queries.
 */
class ProcessProductImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CHUNK_SIZE = 1000;

    /**
     * Generous on purpose - a real 1-million-row file processing at even
     * a modest rate could genuinely take over an hour. The queue WORKER's
     * own --timeout flag also needs to be raised to match (see the
     * accompanying manual step) - this property alone doesn't override a
     * shorter worker-level timeout.
     */
    public int $timeout = 14400; // 4 hours

    public int $tries = 1; // a partial import isn't safe to blindly retry - see failure handling below

    public function __construct(private readonly ProductImport $import) {}

    /** name (lowercased) => id, built once at job start - never queried per-row, since that would mean up to a million category lookups for what's normally a few dozen categories total. */
    private array $categoryIdsByName = [];

    /** The "Medicines" category's id, used whenever a row has no category column at all or an empty/unmatched value for it. Left null (falls back to no category, not a hard failure) if that category genuinely doesn't exist in this database. */
    private ?int $defaultCategoryId = null;

    /**
     * Every product name seen so far (lowercased, trimmed), as a set -
     * keys only, values unused. Loaded once from every existing product
     * at job start, then grown as this run's own rows are accepted, so
     * a later row in the same file that repeats an earlier one in this
     * same run is caught too, not just names that predate this import.
     * Kept as full name strings rather than a hash of each name - a hash
     * would use less memory at this scale, but risks a collision
     * wrongly treating two different medicines as the same one, which
     * matters more here than the memory saving.
     */
    private array $seenNames = [];

    public function handle(): void
    {
        DB::connection()->disableQueryLog();

        $this->import->update(['status' => 'processing', 'started_at' => now()]);

        $absolutePath = Storage::disk('local')->path($this->import->stored_path);

        try {
            $this->loadCategoryCache();
            $this->loadExistingNames();
            $this->import->update(['total_rows' => $this->countDataRows($absolutePath)]);
            $this->processFile($absolutePath);

            $this->import->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('Product import failed', ['import_id' => $this->import->id, 'error' => $e->getMessage()]);

            $this->import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }

    private function loadCategoryCache(): void
    {
        $this->categoryIdsByName = Category::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->all();

        $this->defaultCategoryId = $this->categoryIdsByName['medicines'] ?? null;

        if ($this->defaultCategoryId === null) {
            Log::warning('Product import: "Medicines" category not found - imported products will have no category unless the file specifies one that matches an existing category.', ['import_id' => $this->import->id]);
        }
    }

    private function loadExistingNames(): void
    {
        foreach (Product::select('name')->cursor() as $product) {
            $this->seenNames[strtolower(trim($product->name))] = true;
        }
    }

    /** Fast line-count pass so the progress bar has a real denominator from the very first poll, not just once rows start actually processing. */
    private function countDataRows(string $path): int
    {
        $handle = fopen($path, 'r');
        $count = -1; // starts at -1 so the header row itself isn't counted as data

        while (fgets($handle) !== false) {
            $count++;
        }

        fclose($handle);

        return max(0, $count);
    }

    private function processFile(string $path): void
    {
        $handle = fopen($path, 'r');

        try {
            $header = fgetcsv($handle);
            $columns = $this->mapColumns($header);

            $batch = [];
            $rowNumber = 0;
            $processedSinceFlush = 0;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                $rawName = $this->cell($row, $columns['name']);
                $normalizedName = $rawName !== null ? strtolower($rawName) : null;

                if ($normalizedName !== null && isset($this->seenNames[$normalizedName])) {
                    $this->import->increment('duplicate_count');
                    $processedSinceFlush++;
                    continue;
                }

                $product = $this->buildProductRow($row, $columns, $rowNumber);

                if ($product === null) {
                    $this->import->increment('skipped_count');
                } else {
                    if ($normalizedName !== null) {
                        $this->seenNames[$normalizedName] = true;
                    }
                    $batch[] = $product;
                }

                $processedSinceFlush++;

                if (count($batch) >= self::CHUNK_SIZE) {
                    $this->flushBatch($batch);
                    $this->import->increment('processed_rows', $processedSinceFlush);
                    $batch = [];
                    $processedSinceFlush = 0;
                }
            }

            if (! empty($batch) || $processedSinceFlush > 0) {
                $this->flushBatch($batch);
                $this->import->increment('processed_rows', $processedSinceFlush);
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Detects however many short_compositionN / useN columns actually
     * exist in this file's header (2 in the demo file, possibly 3 or more
     * in the real one) rather than assuming a fixed count - this is what
     * "use all of them, however many there are" actually means in code.
     */
    /**
     * Fixed positions (0-indexed), per the exact column order confirmed
     * directly rather than guessed from header text: 0=id (ignored),
     * 1=name, 2=price, 3=manufacturer, 4=unit, 5&6=salt composition,
     * 7&8=use of medicine, 9=side effects, 10=image.
     */
    private function mapColumns(array $header): array
    {
        $lower = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $find = function (string $exact) use ($lower) {
            $idx = array_search(strtolower($exact), $lower, true);
            return $idx === false ? null : $idx;
        };

        return [
            'name' => 1,
            'price' => 2,
            'manufacturer' => 3,
            'pack_size' => 4,
            'composition' => [5, 6],
            'uses' => [7, 8],
            'side_effects' => 9,
            'image' => 10,
            // Not part of the confirmed 11-column layout - stays
            // name-based as a bonus in case a future file adds one;
            // simply won't match (correctly falling back to the
            // default category) otherwise.
            'category' => $find('category'),
            // Optional, last column in the file per the actual layout -
            // detected by name rather than assumed position, so this
            // still works even if a future file's column order differs.
            'image' => $find('image'),
        ];
    }

    /** Returns null for a row that can't produce a valid product (caller counts it as skipped) rather than throwing and failing the whole import over one bad row. */
    private function buildProductRow(array $row, array $columns, int $rowNumber): ?array
    {
        $name = $this->cell($row, $columns['name']);
        $priceRaw = $this->cell($row, $columns['price']);

        if ($name === null || $name === '' || $priceRaw === null || ! is_numeric($priceRaw)) {
            return null;
        }

        $price = (float) $priceRaw;
        if ($price < 0) {
            return null;
        }

        $composition = collect($columns['composition'])
            ->map(fn ($idx) => $this->cell($row, $idx))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->implode(' + ');

        $uses = collect($columns['uses'])
            ->map(fn ($idx) => $this->cell($row, $idx))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->implode('; ');

        $sideEffects = $this->cell($row, $columns['side_effects']);

        $descriptionSections = [];
        if ($uses !== '') {
            $descriptionSections[] = "Use of medicine\n{$uses}";
        }
        if ($sideEffects !== null) {
            $descriptionSections[] = "Side effects\n{$sideEffects}";
        }
        $description = ! empty($descriptionSections) ? implode("\n\n", $descriptionSections) : null;

        return [
            'name' => $name,
            // Unique even for a deliberately repeated name (the source
            // data allows the same medicine to appear more than once) -
            // the row number alone guarantees this regardless of any
            // duplication in the name itself.
            'slug' => Str::slug($name).'-'.$rowNumber,
            'category_id' => $this->resolveCategoryId($this->cell($row, $columns['category'] ?? null)),
            'salt_composition' => $composition !== '' ? $composition : null,
            'manufacturer' => $this->cell($row, $columns['manufacturer']),
            'unit' => $this->cell($row, $columns['pack_size']),
            'description' => $description,
            'drug_schedule' => 'otc',
            'prescription_required' => false,
            'is_active' => true,
            '_price' => $price, // stripped before insert - see flushBatch()
            '_image_url' => $this->cell($row, $columns['image'] ?? null),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /** Missing column, empty value, and an unrecognized category name are all treated the same way - fall back to the default "Medicines" category, exactly as asked: "if category is not mentioned then the category will be Medicines always." */
    private function resolveCategoryId(?string $categoryName): ?int
    {
        if ($categoryName === null) {
            return $this->defaultCategoryId;
        }

        return $this->categoryIdsByName[strtolower($categoryName)] ?? $this->defaultCategoryId;
    }

    private function cell(array $row, ?int $index): ?string
    {
        if ($index === null || ! array_key_exists($index, $row)) {
            return null;
        }

        $value = trim((string) $row[$index]);

        return $value === '' ? null : $value;
    }

    /**
     * Bulk-inserts this chunk's products, then looks up each one's real id
     * by its (unique) slug to build the matching product_prices rows -
     * never assumed from auto-increment, which would silently corrupt
     * pricing under any concurrent write to the products table during
     * the import.
     */
    private function flushBatch(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $today = now()->toDateString();
        $slugToPrice = [];
        $slugToImageUrl = [];
        $insertRows = [];

        foreach ($rows as $row) {
            $slugToPrice[$row['slug']] = $row['_price'];
            if ($row['_image_url'] !== null) {
                $slugToImageUrl[$row['slug']] = $row['_image_url'];
            }
            unset($row['_price'], $row['_image_url']);
            $insertRows[] = $row;
        }

        DB::table('products')->insert($insertRows);

        $idsBySlug = Product::whereIn('slug', array_keys($slugToPrice))->pluck('id', 'slug');

        $priceRows = [];
        foreach ($idsBySlug as $slug => $productId) {
            $price = $slugToPrice[$slug];
            $priceRows[] = [
                'product_id' => $productId,
                'franchise_id' => null, // the global default price - no franchise override, per the actual requirement
                'mrp' => $price,
                'selling_price' => $price,
                'tax_percentage' => 0,
                'effective_from' => $today,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($priceRows)) {
            DB::table('product_prices')->insert($priceRows);
        }

        foreach ($idsBySlug as $slug => $productId) {
            if (isset($slugToImageUrl[$slug])) {
                DownloadProductImageJob::dispatch($productId, $slugToImageUrl[$slug])->onQueue('images');
            }
        }

        $this->import->increment('imported_count', count($idsBySlug));
    }
}

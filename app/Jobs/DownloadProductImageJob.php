<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Deliberately its own job, not inline logic in the import - a bulk
 * import can involve up to a million of these, and downloading each
 * synchronously (one HTTP fetch per row, waiting on however fast or
 * slow each server responds) inside the main import would make the
 * whole import extremely slow, exactly what the original "no images
 * during bulk import" decision was meant to avoid. Dispatched to its
 * own 'images' queue (see the accompanying manual step) so this volume
 * of jobs doesn't compete with or crowd out any other queued work.
 */
class DownloadProductImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 30;

    public int $tries = 2;

    public int $backoff = 10;

    public function __construct(
        private readonly int $productId,
        private readonly string $imageUrl,
    ) {}

    public function handle(): void
    {
        $product = Product::find($this->productId);

        if ($product === null) {
            return; // product itself no longer exists - nothing to attach an image to
        }

        try {
            $response = Http::timeout(20)->get($this->imageUrl);
        } catch (Throwable $e) {
            Log::warning('Product image download failed', ['product_id' => $this->productId, 'url' => $this->imageUrl, 'error' => $e->getMessage()]);
            return; // left with no image, same as before - not a reason to fail/retry the whole import
        }

        if (! $response->successful()) {
            Log::warning('Product image download returned a non-success status', ['product_id' => $this->productId, 'url' => $this->imageUrl, 'status' => $response->status()]);
            return;
        }

        $contentType = $response->header('Content-Type');
        $extension = $this->extensionFor($contentType);

        if ($extension === null) {
            Log::warning('Product image URL did not return image content', ['product_id' => $this->productId, 'url' => $this->imageUrl, 'content_type' => $contentType]);
            return;
        }

        $oldPath = $product->image_path;
        $path = 'products/'.Str::uuid().'.'.$extension;

        Storage::disk('r2')->put($path, $response->body());
        $product->update(['image_path' => $path]);

        if ($oldPath !== null) {
            Storage::disk('r2')->delete($oldPath);
        }
    }

    private function extensionFor(?string $contentType): ?string
    {
        return match ($contentType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => null,
        };
    }
}

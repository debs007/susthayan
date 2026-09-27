<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessProductImportJob;
use App\Models\ProductImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductImportController extends Controller
{
    public function index(Request $request): View
    {
        $imports = ProductImport::with('uploader:id,name')->latest()->paginate(20);

        return view('admin.products.import-index', compact('imports'));
    }

    public function create(): View
    {
        return view('admin.products.import-create');
    }

    /**
     * Sized for the real file (~96MB), not just this demo one - max here
     * is in kilobytes, ~195MB, comfortably above that. The upload itself
     * still depends on php.ini's own upload_max_filesize/post_max_size
     * actually allowing a file this size through in the first place; see
     * the accompanying manual step.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:200000'],
        ]);

        $file = $request->file('file');
        $storedPath = $file->store('product-imports');

        $import = ProductImport::create([
            'uploaded_by' => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'status' => 'pending',
        ]);

        ProcessProductImportJob::dispatch($import);

        return response()->json([
            'import_id' => $import->id,
            'status_url' => route('admin.products.import.status', $import),
        ], 201);
    }

    /** Polled by the frontend loader - the entire progress bar and counts come from this one endpoint. */
    public function status(ProductImport $import): JsonResponse
    {
        return response()->json([
            'id' => $import->id,
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'imported_count' => $import->imported_count,
            'skipped_count' => $import->skipped_count,
            'duplicate_count' => $import->duplicate_count,
            'progress_percentage' => $import->progressPercentage(),
            'error_message' => $import->error_message,
        ]);
    }

    /** Frees disk space once the file's actual data is safely in the database and there's no further reason to keep the raw upload around. */
    public function destroy(ProductImport $import): RedirectResponse
    {
        if (in_array($import->status, ['completed', 'failed'], true)) {
            Storage::disk('local')->delete($import->stored_path);
        }

        $import->delete();

        return back()->with('success', 'Import record removed.');
    }
}

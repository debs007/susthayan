<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Bulk upload products']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Bulk upload products']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <a href="<?php echo e(route('admin.products.index')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to products
    </a>

    <div class="max-w-2xl space-y-6">
        <div class="rounded-xl border border-border bg-canvas-raised p-8">
            <h1 class="font-display text-lg font-semibold">Bulk upload products</h1>
            <p class="mt-1 text-sm text-ink-muted">
                Upload a CSV of the catalogue. Expected columns: <code class="text-xs">name</code>,
                <code class="text-xs">price</code>, <code class="text-xs">manufacturer_name</code>,
                <code class="text-xs">pack_size_label</code>, one or more
                <code class="text-xs">short_composition1/2/3...</code> columns, one or more
                <code class="text-xs">use0/1/2...</code> columns, and
                <code class="text-xs">Consolidated_Side_Effects</code>. Every product is created without an image and
                priced with no franchise override - both are applied the same way to every row.
                Rows with the same name are all imported separately, not merged or skipped.
            </p>

            <form id="import-form" class="mt-6 space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="mb-1 block text-xs font-medium text-ink-muted">CSV file</label>
                    <input type="file" name="file" id="file-input" accept=".csv,text/csv" required
                        class="block w-full rounded-lg border border-border px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-canvas file:px-3 file:py-1.5 file:text-sm file:font-medium">
                    <p class="mt-1 text-xs text-ink-muted">Large files (up to ~190MB) are supported - processing happens in the background, this page will show live progress.</p>
                </div>

                <button type="submit" id="submit-btn" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600 disabled:opacity-50">
                    Start upload
                </button>
            </form>
        </div>

        <div id="progress-card" class="hidden rounded-xl border border-border bg-canvas-raised p-8">
            <div id="progress-heading" class="flex items-center gap-2 text-sm font-medium">
                <svg id="spinner" class="h-4 w-4 animate-spin text-primary-500" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"/>
                    <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <span id="progress-status-text">Uploading file...</span>
            </div>

            <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-canvas">
                <div id="progress-bar" class="h-full rounded-full bg-primary-500 transition-all duration-300" style="width: 0%"></div>
            </div>

            <div id="progress-counts" class="mt-3 grid grid-cols-3 gap-3 text-center text-sm hidden">
                <div>
                    <p class="font-semibold" id="count-processed">0</p>
                    <p class="text-xs text-ink-muted">Processed</p>
                </div>
                <div>
                    <p class="font-semibold text-emerald-600" id="count-imported">0</p>
                    <p class="text-xs text-ink-muted">Imported</p>
                </div>
                <div>
                    <p class="font-semibold text-amber-600" id="count-skipped">0</p>
                    <p class="text-xs text-ink-muted">Skipped</p>
                </div>
            </div>

            <p id="progress-error" class="mt-3 hidden text-sm text-red-600"></p>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('import-form');
            const submitBtn = document.getElementById('submit-btn');
            const fileInput = document.getElementById('file-input');
            const progressCard = document.getElementById('progress-card');
            const progressBar = document.getElementById('progress-bar');
            const progressStatusText = document.getElementById('progress-status-text');
            const progressCounts = document.getElementById('progress-counts');
            const progressError = document.getElementById('progress-error');
            const spinner = document.getElementById('spinner');
            const countProcessed = document.getElementById('count-processed');
            const countImported = document.getElementById('count-imported');
            const countSkipped = document.getElementById('count-skipped');

            let pollTimer = null;

            function setStatusText(text) {
                progressStatusText.textContent = text;
            }

            function stopPolling() {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            }

            function pollStatus(statusUrl) {
                pollTimer = setInterval(async () => {
                    try {
                        const response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
                        const data = await response.json();

                        progressBar.style.width = data.progress_percentage + '%';
                        progressCounts.classList.remove('hidden');
                        countProcessed.textContent = data.total_rows
                            ? `${data.processed_rows} / ${data.total_rows}`
                            : data.processed_rows;
                        countImported.textContent = data.imported_count;
                        countSkipped.textContent = data.skipped_count;

                        if (data.status === 'processing' || data.status === 'pending') {
                            setStatusText(data.status === 'pending' ? 'Starting...' : 'Processing rows...');
                        } else if (data.status === 'completed') {
                            stopPolling();
                            spinner.classList.add('hidden');
                            progressBar.style.width = '100%';
                            setStatusText(`Done - ${data.imported_count} products imported${data.skipped_count > 0 ? `, ${data.skipped_count} rows skipped` : ''}.`);
                        } else if (data.status === 'failed') {
                            stopPolling();
                            spinner.classList.add('hidden');
                            progressError.textContent = data.error_message || 'The import failed unexpectedly.';
                            progressError.classList.remove('hidden');
                            setStatusText('Import failed.');
                        }
                    } catch (err) {
                        // A single failed poll shouldn't stop the whole thing - the
                        // next interval tick just tries again.
                    }
                }, 2000);
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!fileInput.files.length) return;

                submitBtn.disabled = true;
                progressCard.classList.remove('hidden');
                setStatusText('Uploading file...');

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                try {
                    const response = await fetch('<?php echo e(route('admin.products.import.store')); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { Accept: 'application/json' },
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || 'Upload failed.');
                    }

                    const data = await response.json();
                    setStatusText('Starting...');
                    pollStatus(data.status_url);
                } catch (err) {
                    spinner.classList.add('hidden');
                    progressError.textContent = err.message || 'Upload failed.';
                    progressError.classList.remove('hidden');
                    setStatusText('Upload failed.');
                    submitBtn.disabled = false;
                }
            });
        })();
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/products/import-create.blade.php ENDPATH**/ ?>
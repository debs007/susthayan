<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($added): ?>
        <span class="flex items-center justify-center gap-1.5 rounded-full bg-mist px-4 py-2 text-sm font-medium text-forest">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none"><path d="m4 10 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
            Added
        </span>
    <?php else: ?>
        <button
            wire:click="add"
            <?php if(! $inStock): echo 'disabled'; endif; ?>
            wire:loading.attr="disabled"
            wire:target="add"
            class="w-full rounded-full bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-hover disabled:bg-ink-faint"
        >
            <span wire:loading.remove wire:target="add"><?php echo e($inStock ? 'Add to Cart' : 'Out of Stock'); ?></span>
            <span wire:loading wire:target="add">Adding&hellip;</span>
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/add-to-cart.blade.php ENDPATH**/ ?>
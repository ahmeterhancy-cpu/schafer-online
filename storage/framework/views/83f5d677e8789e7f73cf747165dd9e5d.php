
<div class="urun-serit" data-serit>
    <button type="button" class="serit-ok serit-ok-sol" data-serit-yon="-1" aria-label="Önceki ürünler">
        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-sol'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </button>
    <div class="urun-serit-ray" tabindex="0" aria-label="Ürünler">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urunler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="urun-serit-oge">
                <?php echo $__env->make('vitrin.parca.urun-kart', ['urun' => $urun], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <button type="button" class="serit-ok serit-ok-sag" data-serit-yon="1" aria-label="Sonraki ürünler">
        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-sag'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </button>
</div>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/parca/urun-serit.blade.php ENDPATH**/ ?>
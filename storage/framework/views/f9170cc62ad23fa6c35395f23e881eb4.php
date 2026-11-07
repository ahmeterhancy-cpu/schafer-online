
<?php
    $etiket = $afis['adres'] ? 'a' : 'div';
    $siniflar = 'afis ' . ($genis ?? false ? 'afis-genis ' : '') . ($afis['gorsel'] ? 'afis-gorselli' : 'afis-duz afis-ton-' . (($sira - 1) % 3 + 1));
?>
<<?php echo e($etiket); ?> class="<?php echo e($siniflar); ?>"
    <?php if($afis['adres']): ?> href="<?php echo e($afis['adres']); ?>" <?php if($afis['dis']): ?> rel="noopener" target="_blank" <?php endif; ?> <?php endif; ?>
    <?php if($afis['gorsel']): ?> style="background-image: url('<?php echo e($afis['gorsel']); ?>')" <?php endif; ?>>
    <span class="afis-metin">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($afis['ust']): ?>
            <span class="afis-ust"><?php echo e($afis['ust']); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="afis-baslik"><?php echo e($afis['baslik']); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($afis['alt']): ?>
            <span class="afis-alt"><?php echo e($afis['alt']); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($afis['adres'] && $afis['dugme']): ?>
            <span class="afis-bag"><?php echo e($afis['dugme']); ?> <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-sag'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $afis['gorsel'] && ! empty($afis['ikon'])): ?>
        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => $afis['ikon'], 'sinif' => 'afis-ikon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</<?php echo e($etiket); ?>>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/parca/afis.blade.php ENDPATH**/ ?>
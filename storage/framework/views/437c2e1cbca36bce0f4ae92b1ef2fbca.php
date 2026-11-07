<?php $__env->startSection('baslik', $belge->title . ' — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => $belge->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap kap-dar yasal-sayfa">

    <p class="yasal-surum">
        Yürürlük sürümü: <?php echo e($belge->version); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($belge->published_at): ?>
            · <?php echo e($belge->published_at->translatedFormat('d F Y')); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </p>

    
    <div class="yasal-govde"><?php echo $belge->body; ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/sayfa.blade.php ENDPATH**/ ?>
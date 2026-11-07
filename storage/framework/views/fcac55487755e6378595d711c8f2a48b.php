<?php $__env->startSection('konu', $metin['konu']); ?>

<?php $__env->startSection('icerik'); ?>
    <h1 style="margin:0 0 6px; font-size:28px; font-weight:normal; color:#1c1a15;">
        <?php echo e($metin['baslik']); ?>

    </h1>

    <p style="margin:0 0 22px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        Fatura no <strong style="color:#1c1a15;"><?php echo e($order->invoice_number); ?></strong>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->invoice_date): ?>
            · <?php echo e($order->invoice_date->format('d.m.Y')); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($metin['metin'])): ?>
        <p style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.7; color:#554f44;">
            <?php echo nl2br(e($metin['metin'])); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->make('eposta.parca.siparis-dugmesi', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($metin['not'])): ?>
        <p style="margin:26px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#8a8275;">
            <?php echo nl2br(e($metin['not'])); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/fatura.blade.php ENDPATH**/ ?>
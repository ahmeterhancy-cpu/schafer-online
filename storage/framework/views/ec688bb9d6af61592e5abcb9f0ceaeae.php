<?php $__env->startSection('konu', $metin['konu']); ?>

<?php $__env->startSection('icerik'); ?>
    <h1 style="margin:0 0 6px; font-size:28px; font-weight:normal; color:#1c1a15;">
        <?php echo e($metin['baslik']); ?>

    </h1>

    <p style="margin:0 0 22px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        Sipariş numaranız
        <strong style="color:#1c1a15;"><?php echo e($order->number); ?></strong>
        · <?php echo e($order->created_at->format('d.m.Y H:i')); ?>

    </p>

    <p style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        <?php echo nl2br(e($metin['metin'])); ?>

    </p>

    <?php echo $__env->make('eposta.parca.siparis-tablosu', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('eposta.parca.siparis-dugmesi', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($metin['not'])): ?>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:30px 0 0;">
            <tr>
                <td style="padding:16px 18px; background:#faf6ea; border-left:2px solid #bc9c51; font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:1.7; color:#554f44;">
                    <?php echo nl2br(e($metin['not'])); ?>

                </td>
            </tr>
        </table>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/siparis-alindi.blade.php ENDPATH**/ ?>
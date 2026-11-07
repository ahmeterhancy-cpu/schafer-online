<?php $__env->startSection('konu', $metin['konu']); ?>

<?php $__env->startSection('icerik'); ?>
    <h1 style="margin:0 0 6px; font-size:28px; font-weight:normal; color:#1c1a15;">
        <?php echo e($metin['baslik']); ?>

    </h1>

    <p style="margin:0 0 22px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        Sipariş numaranız
        <strong style="color:#1c1a15;"><?php echo e($order->number); ?></strong>
    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($metin['metin'])): ?>
        <p style="margin:0 0 18px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44; line-height:1.7;"><?php echo nl2br(e($metin['metin'])); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 6px;">
        <tr>
            <td style="padding:18px 20px; background:#faf6ea; border:1px solid #e6dfd2; font-family:Arial,Helvetica,sans-serif;">
                <p style="margin:0 0 6px; color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase;">
                    Kargo bilgisi
                </p>
                <p style="margin:0; font-size:15px; color:#1c1a15;">
                    <?php echo e($order->shipping_carrier ?: 'Kargo firması'); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->tracking_number): ?>
                        <br>
                        <span style="color:#554f44; font-size:13px;">Takip numarası</span>
                        <strong style="letter-spacing:1px;"><?php echo e($order->tracking_number); ?></strong>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </td>
        </tr>
    </table>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($metin['not'])): ?>
        <p style="margin:18px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#8a8275;">
            <?php echo nl2br(e($metin['not'])); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->make('eposta.parca.siparis-dugmesi', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('eposta.parca.siparis-tablosu', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/siparis-kargoda.blade.php ENDPATH**/ ?>
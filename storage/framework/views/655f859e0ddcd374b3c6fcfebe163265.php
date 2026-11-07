<?php $__env->startSection('konu', $metin['konu']); ?>

<?php $__env->startSection('icerik'); ?>
    <h1 style="margin:0 0 16px; font-size:28px; font-weight:normal; color:#1c1a15;">
        <?php echo e($metin['baslik']); ?>

    </h1>

    <p style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        <?php echo nl2br(e($metin['metin'])); ?>

    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0 0;">
        <tr>
            <td style="background:#1c1a15;">
                <a href="<?php echo e($baglanti); ?>"
                   style="display:inline-block; padding:15px 30px; color:#ffffff; text-decoration:none; font-family:Arial,Helvetica,sans-serif; font-size:12px; letter-spacing:2px; text-transform:uppercase;">
                    Yeni parola belirle
                </a>
            </td>
        </tr>
    </table>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($metin['not'])): ?>
        <p style="margin:26px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#8a8275;">
            <?php echo nl2br(e($metin['not'])); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/parola-sifirlama.blade.php ENDPATH**/ ?>
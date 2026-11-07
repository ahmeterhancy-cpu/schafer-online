<?php $__env->startSection('konu', $variant->stock < 1 ? 'Ürün tükendi' : 'Stok azaldı'); ?>

<?php $__env->startSection('icerik'); ?>
    <h1 style="margin:0 0 6px; font-size:28px; font-weight:normal; color:#1c1a15;">
        <?php echo e($variant->stock < 1 ? 'Ürün tükendi' : 'Stok azaldı'); ?>

    </h1>

    <p style="margin:0 0 22px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        <strong style="color:#1c1a15;"><?php echo e($variant->product->name); ?></strong>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->label): ?>
            · <?php echo e($variant->label); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <br>
        <span style="color:#8a8275;">SKU <?php echo e($variant->sku); ?></span>
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
           style="border-top:1px solid #e6dfd0; border-bottom:1px solid #e6dfd0; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#1c1a15;">
        <tr>
            <td style="padding:12px 0;">Raftaki adet</td>
            <td style="padding:12px 0; text-align:right;"><strong><?php echo e($variant->stock); ?></strong></td>
        </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->reserved > 0): ?>
            <tr>
                <td style="padding:0 0 12px; color:#554f44;">Ödemesi beklenen siparişte ayrılmış</td>
                <td style="padding:0 0 12px; text-align:right; color:#554f44;"><?php echo e($variant->reserved); ?></td>
            </tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <tr>
            <td style="padding:0 0 12px; color:#554f44;">Uyarı eşiği</td>
            <td style="padding:0 0 12px; text-align:right; color:#554f44;"><?php echo e((int) config('shop.dusuk_stok_esigi')); ?></td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0 0;">
        <tr>
            <td style="background:#1c1a15;">
                <a href="<?php echo e(url('/admin/products/'.$variant->product_id.'/edit')); ?>"
                   style="display:inline-block; padding:15px 30px; color:#ffffff; text-decoration:none; font-family:Arial,Helvetica,sans-serif; font-size:12px; letter-spacing:2px; text-transform:uppercase;">
                    Panelde aç
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:26px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#8a8275;">
        Bu uyarı stok eşiğin ALTINA İNDİĞİ anda bir kez gönderilir. Eşik ve
        adres panelde Site Ayarları'ndan değiştirilir.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/dusuk-stok.blade.php ENDPATH**/ ?>
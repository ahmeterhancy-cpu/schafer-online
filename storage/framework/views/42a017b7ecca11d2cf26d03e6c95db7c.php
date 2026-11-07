
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('konu', config('shop.ad')); ?></title>
</head>
<body style="margin:0; padding:0; background:#faf7f0;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#faf7f0;">
    <tr>
        <td align="center" style="padding:28px 16px;">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px; background:#ffffff; border:1px solid #e6dfd2;">

                
                <tr>
                    <td align="center" style="padding:34px 28px 24px; border-bottom:1px solid #e6dfd2;">
                        <a href="<?php echo e(url('/')); ?>" style="text-decoration:none;">
                            <img src="<?php echo e(url('img/schafer/logo.png')); ?>"
                                 alt="<?php echo e(config('shop.ad')); ?>"
                                 width="168"
                                 style="display:block; width:168px; max-width:60%; height:auto; border:0;">
                        </a>
                    </td>
                </tr>

                
                <tr>
                    <td style="padding:30px 28px 34px; font-family:Georgia,'Times New Roman',serif; color:#1c1a15; font-size:16px; line-height:1.65;">
                        <?php echo $__env->yieldContent('icerik'); ?>
                    </td>
                </tr>

                
                <tr>
                    <td style="padding:22px 28px 26px; background:#faf7f0; border-top:1px solid #e6dfd2; font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:1.7; color:#8a8275;">
                        <p style="margin:0 0 8px; color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase;">
                            <?php echo e(config('shop.ad')); ?>

                        </p>
                        <p style="margin:0 0 6px;"><?php echo e(config('shop.satici.adres')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.telefon')): ?>
                            <p style="margin:0 0 6px;"><?php echo e(config('shop.satici.telefon')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p style="margin:0;">
                            <a href="https://instagram.com/<?php echo e(config('shop.sosyal.instagram')); ?>"
                               style="color:#6e5a33; text-decoration:none;">
                                &#64;<?php echo e(config('shop.sosyal.instagram')); ?>

                            </a>
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/duzen.blade.php ENDPATH**/ ?>
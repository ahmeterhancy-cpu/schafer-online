<?php $__env->startSection('konu', $metin['konu']); ?>

<?php $__env->startSection('icerik'); ?>
    <h1 style="margin:0 0 6px; font-size:26px; font-weight:normal; color:#1c1a15;">
        <?php echo e($metin['baslik']); ?>

    </h1>

    <p style="margin:0 0 20px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        <?php echo nl2br(e($metin['metin'])); ?>

    </p>

    
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:1.7; color:#554f44;">
        <tr>
            <td style="padding:14px 16px; background:#faf6ea; border:1px solid #e6dfd2;">
                <strong style="color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase;">Satıcı</strong><br>
                <?php echo e(config('shop.satici.unvan') ?: config('shop.ad')); ?><br>
                <?php echo e(config('shop.satici.adres')); ?><br>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.telefon')): ?> <?php echo e(config('shop.satici.telefon')); ?> · <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e(config('shop.satici.eposta')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.mersis')): ?><br>MERSİS: <?php echo e(config('shop.satici.mersis')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <br><br>
                <strong style="color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase;">Alıcı</strong><br>
                <?php echo e($order->customer_name); ?> · <?php echo e($order->customer_email); ?> · <?php echo e($order->customer_phone); ?>

                <br><br>
                <strong style="color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase;">Sipariş ve onay</strong><br>
                Sipariş tarihi: <?php echo e(\App\Support\Tarih::goster($order->created_at)); ?><br>
                Sözleşme onayı: <?php echo e(\App\Support\Tarih::goster($order->contract_accepted_at)); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->contract_ip): ?> · IP <?php echo e($order->contract_ip); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </td>
        </tr>
    </table>

    <?php echo $__env->make('eposta.parca.siparis-tablosu', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [$onBilgi, $sozlesme]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $belge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($belge): ?>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:34px 0 0;">
                <tr>
                    <td style="padding:0 0 10px; border-bottom:1px solid #bc9c51;">
                        <span style="font-size:22px; color:#1c1a15;"><?php echo e($belge->title); ?></span><br>
                        <span style="font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#8a8275;">
                            Sürüm <?php echo e($belge->version); ?>

                        </span>
                    </td>
                </tr>
                <tr>
                    
                    <td style="padding:14px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:1.7; color:#554f44;">
                        <?php echo $belge->body; ?>

                    </td>
                </tr>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $onBilgi || ! $sozlesme): ?>
        <p style="margin:26px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#8a8275;">
            Belgelerin güncel hâline sitemizdeki yasal sayfalardan ulaşabilirsiniz.
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/sozlesme-belgeleri.blade.php ENDPATH**/ ?>
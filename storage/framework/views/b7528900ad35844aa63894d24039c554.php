<?php $__env->startSection('konu', $metin['konu'] ?? 'İade talebiniz — ' . $talep->number); ?>

<?php $__env->startSection('icerik'); ?>
    <?php ($order = $talep->order); ?>

    
    <h1 style="margin:0 0 6px; font-size:28px; font-weight:normal; color:#1c1a15;">
        <?php echo e($metin['baslik'] ?? 'Talebiniz güncellendi'); ?>

    </h1>

    <p style="margin:0 0 22px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44;">
        Talep numarası <strong style="color:#1c1a15;"><?php echo e($talep->number); ?></strong>
        · Sipariş <?php echo e($order->number); ?>

    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($metin): ?>
        <p style="margin:0 0 16px; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#554f44; line-height:1.7;">
            <?php echo nl2br(e($metin['metin'])); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($talep->status === 'rejected' && $talep->admin_note): ?>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="padding:16px 18px; background:#fdf3f3; border-left:2px solid #a32b2b; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.7; color:#7d2020;">
                    <?php echo e($talep->admin_note); ?>

                </td>
            </tr>
        </table>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($metin['not'])): ?>
        <p style="margin:16px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#8a8275;"><?php echo nl2br(e($metin['not'])); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="border-collapse:collapse; margin:26px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:14px;">
        <tr>
            <td colspan="2" style="color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase; padding-bottom:10px;">
                Talebe konu ürünler
            </td>
        </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $talep->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kalem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <tr>
                <td style="padding:10px 0; border-bottom:1px solid #e6dfd2; color:#1c1a15;">
                    <?php echo e($kalem->orderItem->name); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->orderItem->variant_label): ?>
                        <br><span style="color:#8a8275; font-size:12px;"><?php echo e($kalem->orderItem->variant_label); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->exchangeVariant): ?>
                        <br><span style="color:#6e5a33; font-size:12px;">
                            Değişim → <?php echo e($kalem->exchangeVariant->label); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td align="right" style="padding:10px 0; border-bottom:1px solid #e6dfd2; color:#554f44; white-space:nowrap;">
                    × <?php echo e($kalem->quantity); ?>

                </td>
            </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('eposta.duzen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/iade-sonucu.blade.php ENDPATH**/ ?>
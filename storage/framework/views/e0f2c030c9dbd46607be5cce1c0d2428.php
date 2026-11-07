
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="border-collapse:collapse; margin:22px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:14px;">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kalem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <tr>
            <td style="padding:11px 0; border-bottom:1px solid #e6dfd2; color:#1c1a15;">
                <?php echo e($kalem->name); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->variant_label): ?>
                    <br><span style="color:#8a8275; font-size:12px;"><?php echo e($kalem->variant_label); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <br><span style="color:#8a8275; font-size:11px;"><?php echo e($kalem->sku); ?></span>
            </td>
            <td align="center" style="padding:11px 10px; border-bottom:1px solid #e6dfd2; color:#554f44; white-space:nowrap;">
                × <?php echo e($kalem->quantity); ?>

            </td>
            <td align="right" style="padding:11px 0; border-bottom:1px solid #e6dfd2; color:#1c1a15; white-space:nowrap;">
                <?php echo e(number_format((float) $kalem->line_total, 2, ',', '.')); ?> TL
            </td>
        </tr>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    <tr>
        <td colspan="2" style="padding:11px 0 4px; color:#554f44;">Ara toplam</td>
        <td align="right" style="padding:11px 0 4px; color:#554f44; white-space:nowrap;">
            <?php echo e(number_format((float) $order->subtotal, 2, ',', '.')); ?> TL
        </td>
    </tr>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $order->discount_total > 0): ?>
        <tr>
            <td colspan="2" style="padding:4px 0; color:#2f6f4a;">
                İndirim<?php echo e($order->coupon_code ? ' ('.$order->coupon_code.')' : ''); ?>

            </td>
            <td align="right" style="padding:4px 0; color:#2f6f4a; white-space:nowrap;">
                -<?php echo e(number_format((float) $order->discount_total, 2, ',', '.')); ?> TL
            </td>
        </tr>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <tr>
        <td colspan="2" style="padding:4px 0; color:#554f44;">Kargo</td>
        <td align="right" style="padding:4px 0; color:#554f44; white-space:nowrap;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $order->shipping_total > 0): ?>
                <?php echo e(number_format((float) $order->shipping_total, 2, ',', '.')); ?> TL
            <?php else: ?>
                Ücretsiz
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding:13px 0 0; border-top:1px solid #cdc2ad; color:#1c1a15; font-weight:bold;">
            Toplam
        </td>
        <td align="right" style="padding:13px 0 0; border-top:1px solid #cdc2ad; color:#1c1a15; font-weight:bold; white-space:nowrap;">
            <?php echo e(number_format((float) $order->grand_total, 2, ',', '.')); ?> TL
        </td>
    </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="margin:26px 0 0; font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:1.7;">
    <tr>
        <td style="color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase; padding-bottom:8px;">
            Teslimat adresi
        </td>
    </tr>
    <tr>
        <td style="color:#554f44;">
            <?php ($adres = $order->shipping_address ?? []); ?>
            <?php echo e($adres['name'] ?? ''); ?><br>
            <?php echo e(trim(($adres['line1'] ?? '').' '.($adres['line2'] ?? ''))); ?><br>
            <?php echo e(($adres['district'] ?? '').' / '.($adres['city'] ?? '')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($adres['postal_code'])): ?>
                <br><?php echo e($adres['postal_code']); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($adres['phone'])): ?>
                <br><?php echo e($adres['phone']); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </td>
    </tr>

    <?php ($fatura = $order->billing_address ?? []); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($fatura)): ?>
        <tr>
            <td style="color:#6e5a33; font-size:11px; letter-spacing:2px; text-transform:uppercase; padding:18px 0 8px;">
                Fatura
            </td>
        </tr>
        <tr>
            <td style="color:#554f44;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($fatura['invoice_type'] ?? null) === 'corporate'): ?>
                    <?php echo e($fatura['company_name'] ?? ''); ?><br>
                    <?php echo e($fatura['tax_office'] ?? ''); ?> V.D. · <?php echo e($fatura['tax_number'] ?? ''); ?><br>
                <?php else: ?>
                    <?php echo e($fatura['name'] ?? ''); ?> (bireysel)<br>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($fatura['tax_number'])): ?>
                        T.C. kimlik no: •••••••<?php echo e(substr($fatura['tax_number'], -4)); ?><br>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e(trim(($fatura['line1'] ?? '').' '.($fatura['line2'] ?? ''))); ?><br>
                <?php echo e(($fatura['district'] ?? '').' / '.($fatura['city'] ?? '')); ?>

            </td>
        </tr>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</table>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/eposta/parca/siparis-tablosu.blade.php ENDPATH**/ ?>
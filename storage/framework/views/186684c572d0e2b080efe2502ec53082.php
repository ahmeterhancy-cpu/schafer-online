<?php $__env->startSection('baslik', 'Sipariş ' . $order->number . ' — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Sipariş ' . $order->number, 'konum' => ['Hesabım' => route('account.index'), $order->number => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap hesap-duzen">
    <?php echo $__env->make('vitrin.parca.hesap-menu', ['aktif' => 'siparisler'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="hesap-icerik siparis-sayfa">

    <div class="siparis-basi">
        <div>
            <h2><?php echo e($order->number); ?></h2>
            <p class="siparis-tarih"><?php echo e($order->created_at->format('d.m.Y H:i')); ?></p>
        </div>
        <span class="siparis-durum siparis-durum-<?php echo e($order->status); ?>"><?php echo e($order->status_label); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->invoice_pdf): ?>
        <p class="fatura-bag">
            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'eposta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            Faturanız hazır (<?php echo e($order->invoice_number); ?>) —
            <a href="<?php echo e(\Illuminate\Support\Facades\URL::signedRoute('order.invoice', ['order' => $order->number])); ?>">PDF olarak indirin</a>
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->tracking_number): ?>
        <div class="kargo-kutu">
            <span class="etiket">Kargo</span>
            <p><?php echo e($order->shipping_carrier); ?> · <strong><?php echo e($order->tracking_number); ?></strong></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="bolum">
        <div class="bolum-basi"><h2>Ürünler</h2></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kalem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="siparis-kalem">
                <div class="sepet-gorsel <?php echo e($kalem->image ? '' : 'urun-gorsel-yok'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->image): ?>
                        <img src="<?php echo e(asset('storage/' . $kalem->image)); ?>" alt="<?php echo e($kalem->name); ?>">
                    <?php else: ?>
                        <span class="urun-harf" aria-hidden="true">Z</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="sepet-bilgi">
                    <p class="sepet-ad"><?php echo e($kalem->name); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->variant_label): ?>
                        <p class="sepet-varyant"><?php echo e($kalem->variant_label); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="sepet-tutar">
                    <span>× <?php echo e($kalem->quantity); ?></span>
                    <span><?php echo e(number_format((float) $kalem->line_total, 2, ',', '.')); ?> TL</span>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <div class="siparis-toplam">
            <div class="ozet-satir ozet-toplam">
                <span>Toplam</span>
                <span><?php echo e(number_format((float) $order->grand_total, 2, ',', '.')); ?> TL</span>
            </div>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_status === 'paid'): ?>
        <p style="margin-top:26px">
            <a class="dugme dugme-cizgi"
               href="<?php echo e(\Illuminate\Support\Facades\URL::signedRoute('order.show', ['order' => $order->number])); ?>">
                İade / değişim talebi
            </a>
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/hesap/siparis.blade.php ENDPATH**/ ?>
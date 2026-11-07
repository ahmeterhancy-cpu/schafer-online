<?php $__env->startSection('baslik', 'Hesabım — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Hesabım', 'konum' => ['Hesabım' => route('account.index'), 'Hesabım' => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap hesap-duzen">
    <?php echo $__env->make('vitrin.parca.hesap-menu', ['aktif' => 'siparisler'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="hesap-icerik">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('bilgi')): ?>
        <p class="uyari"><?php echo e(session('bilgi')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <h2>Siparişlerim</h2>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siparisler->isEmpty()): ?>
        <div class="bos-durum">
            <p>Henüz siparişiniz yok.</p>
            <a class="dugme" href="<?php echo e(route('collections.index')); ?>">Koleksiyonları gör</a>
        </div>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $siparisler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siparis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a class="hesap-siparis" href="<?php echo e(route('account.order', $siparis->number)); ?>">
                <div>
                    <p class="hesap-siparis-no"><?php echo e($siparis->number); ?></p>
                    <p class="hesap-siparis-tarih">
                        <?php echo e($siparis->created_at->format('d.m.Y')); ?> ·
                        <?php echo e($siparis->items_count); ?> ürün
                    </p>
                </div>
                <div class="hesap-siparis-sag">
                    <span class="hesap-siparis-tutar">
                        <?php echo e(number_format((float) $siparis->grand_total, 2, ',', '.')); ?> TL
                    </span>
                    <span class="siparis-durum siparis-durum-<?php echo e($siparis->status); ?>">
                        <?php echo e($siparis->status_label); ?>

                    </span>
                </div>
            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <div class="sayfalama"><?php echo e($siparisler->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/hesap/ozet.blade.php ENDPATH**/ ?>


<?php $__env->startSection('baslik', 'Mağaza — ' . config('shop.ad')); ?>
<?php $__env->startSection('aciklama', 'Schafer Kıbrıs tüm ürünler: sofra, mutfak, küçük ev aletleri ve ev tekstili.'); ?>

<?php $__env->startSection('icerik'); ?>
    <?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Mağaza', 'konum' => ['Tüm Ürünler' => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($koleksiyonlar->isNotEmpty() && ! request()->hasAny(['sirala', 'fiyat_min', 'fiyat_max', 'page'])): ?>
        <section class="kap" style="margin-bottom:44px">
            <h2 class="gorunmez">Koleksiyonlar</h2>
            <div class="afisler afisler-<?php echo e(min(3, $koleksiyonlar->count())); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $koleksiyonlar->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $koleksiyon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a class="afis <?php echo e($koleksiyon->image ? 'afis-gorselli' : 'afis-duz afis-ton-' . $loop->iteration); ?>"
                       href="<?php echo e(route('collections.show', $koleksiyon->slug)); ?>"
                       <?php if($koleksiyon->image): ?> style="background-image: url('<?php echo e(asset('storage/' . $koleksiyon->image)); ?>')" <?php endif; ?>>
                        <span class="afis-metin">
                            <span class="afis-ust"><?php echo e($koleksiyon->products_count); ?> parça</span>
                            <span class="afis-baslik"><?php echo e($koleksiyon->name); ?></span>
                            <span class="afis-bag">Koleksiyonu gör <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-sag'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                        </span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->make('vitrin.parca.katalog', [
        'urunler' => $urunler,
        'bosMesaj' => request()->hasAny(['fiyat_min', 'fiyat_max']) || request('sirala') === 'indirim'
            ? 'Bu süzgeçle eşleşen ürün yok.'
            : 'Henüz ürün eklenmedi.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/koleksiyonlar.blade.php ENDPATH**/ ?>
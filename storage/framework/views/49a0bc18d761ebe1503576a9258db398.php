<?php $__env->startSection('baslik', $kategori->name . ' — ' . config('shop.ad')); ?>
<?php $__env->startSection('aciklama', $kategori->meta_description ?: ($kategori->description ?: $kategori->name . ' koleksiyonu.')); ?>

<?php $__env->startSection('icerik'); ?>
    <?php echo $__env->make('vitrin.parca.sayfa-basi', [
        'baslik' => $kategori->name,
        'konum' => array_filter([
            'Mağaza' => route('collections.index'),
            $kategori->parent?->name => $kategori->parent ? route('catalog.category', $kategori->parent->slug) : null,
            $kategori->name => null,
        ], fn ($v, $k) => $k !== '', ARRAY_FILTER_USE_BOTH),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('vitrin.parca.katalog', [
        'urunler' => $urunler,
        'aktifKategori' => $kategori,
        'altKategoriler' => $kategori->children->where('is_active', true),
        'aciklama' => $kategori->description,
        'bosMesaj' => 'Bu kategoride henüz ürün yok.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/kategori.blade.php ENDPATH**/ ?>
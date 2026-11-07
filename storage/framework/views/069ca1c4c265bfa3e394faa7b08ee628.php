<?php $__env->startSection('baslik', $koleksiyon->name . ' — ' . config('shop.ad')); ?>
<?php $__env->startSection('aciklama', $koleksiyon->description ?: $koleksiyon->name . ' koleksiyonu.'); ?>

<?php $__env->startSection('icerik'); ?>
    <?php echo $__env->make('vitrin.parca.sayfa-basi', [
        'baslik' => $koleksiyon->name,
        'konum' => ['Koleksiyonlar' => route('collections.index'), $koleksiyon->name => null],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('vitrin.parca.katalog', [
        'urunler' => $urunler,
        'aktifKoleksiyon' => $koleksiyon,
        'aciklama' => $koleksiyon->description,
        'bosMesaj' => 'Bu koleksiyonda henüz ürün yok.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/koleksiyon.blade.php ENDPATH**/ ?>
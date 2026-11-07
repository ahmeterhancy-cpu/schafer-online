<?php $__env->startSection('baslik', ($sorgu ? $sorgu . ' — Arama' : 'Arama') . ' — ' . config('shop.ad')); ?>

<?php $__env->startSection('katalog_ust'); ?>
    <form method="GET" action="<?php echo e(route('catalog.search')); ?>" class="arama-formu">
        <label class="gorunmez" for="q">Ne arıyorsunuz?</label>
        <input type="search" id="q" name="q" value="<?php echo e($sorgu); ?>"
               placeholder="Ürün adı ya da SKU" class="metin-girdi">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kategori): ?>
            <input type="hidden" name="kategori" value="<?php echo e($kategori->slug); ?>">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <button type="submit" class="dugme"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'arama'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Ara</button>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sorgu === ''): ?>
        <div class="bos-durum">
            <p>Aramak istediğiniz ürünün adını yazın.</p>
        </div>
    <?php elseif($urunler->isNotEmpty()): ?>
        <p class="arama-ozet">
            “<?php echo e($sorgu); ?>”<?php echo e($kategori ? ' · ' . $kategori->name . ' kategorisinde' : ''); ?> için <?php echo e($urunler->total()); ?> sonuç
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('icerik'); ?>
    <?php echo $__env->make('vitrin.parca.sayfa-basi', [
        'baslik' => $sorgu ? '“' . $sorgu . '” için sonuçlar' : 'Arama',
        'konum' => ['Arama' => null],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('vitrin.parca.katalog', [
        'urunler' => $urunler,
        'aktifKategori' => $kategori,
        'bosMesaj' => '“' . $sorgu . '” için sonuç bulunamadı.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/arama.blade.php ENDPATH**/ ?>
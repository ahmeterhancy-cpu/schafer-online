<?php $__env->startSection('baslik', 'Ödeme (benzetim) — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Ödeme benzetimi'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap kap-dar odeme-cerceve-sayfa">
    <p class="uyari uyari-hata">
        <strong>Bu ekran yalnızca yerel geliştirme içindir.</strong>
        PayTR kimlik bilgileri tanımlı olmadığı için gerçek ödeme sayfası
        açılamadı. Canlı ortamda bu ekran görünmez.
    </p>


    <p class="odeme-cerceve-not">
        Sipariş <strong><?php echo e($order->number); ?></strong> ·
        Tutar <strong><?php echo e(number_format((float) $order->grand_total, 2, ',', '.')); ?> TL</strong>
    </p>

    <div class="benzetim-dugmeler">
        <form method="POST" action="<?php echo e(route('payment.simulate')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="order" value="<?php echo e($order->number); ?>">
            <input type="hidden" name="sonuc" value="basarili">
            <button type="submit" class="dugme">Ödemeyi başarılı say</button>
        </form>

        <form method="POST" action="<?php echo e(route('payment.simulate')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="order" value="<?php echo e($order->number); ?>">
            <input type="hidden" name="sonuc" value="basarisiz">
            <button type="submit" class="dugme dugme-cizgi">Ödemeyi başarısız say</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/odeme-benzetim.blade.php ENDPATH**/ ?>
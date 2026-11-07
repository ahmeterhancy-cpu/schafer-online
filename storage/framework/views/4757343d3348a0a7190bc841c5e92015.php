

<?php $__env->startSection('baslik', 'Ödeme — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Ödeme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap kap-dar odeme-cerceve-sayfa">

    <p class="odeme-cerceve-not">
        Sipariş numaranız <strong><?php echo e($order->number); ?></strong>.
        Kart bilgilerinizi aşağıdaki güvenli alanda giriyorsunuz;
        bu bilgiler bizim sunucumuzdan geçmez.
    </p>

    
    <iframe src="<?php echo e($iframeUrl); ?>"
            id="paytrIframe"
            frameborder="0"
            title="Güvenli ödeme"
            class="odeme-cerceve"></iframe>

    <p class="odeme-cerceve-not odeme-cerceve-kucuk">
        Ödeme tamamlandığında bu sayfadan otomatik olarak yönlendirileceksiniz.
        Sayfayı kapatmayın.
    </p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('betik'); ?>
    <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
    <script>
        // PayTR'nin kendi betigi; cerceveyi icerik boyuna gore buyutur.
        if (window.iFrameResize) {
            iFrameResize({ checkOrigin: false }, '#paytrIframe');
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/odeme-iframe.blade.php ENDPATH**/ ?>
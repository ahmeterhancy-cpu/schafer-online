
<?php $esik = (float) config('shop.kargo.ucretsiz_esigi'); ?>
<section class="kap bolum">
    <ul class="ozellikler">
        <li>
            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kamyon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div>
                <h3><?php echo e($esik > 0 ? 'Ücretsiz Kargo' : 'Türkiye Geneli Kargo'); ?></h3>
                <p><?php echo e($esik > 0 ? number_format($esik, 0, ',', '.') . ' TL ve üzeri siparişlerde' : 'Edirne\'den tüm Türkiye\'ye'); ?></p>
            </div>
        </li>
        <li>
            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kalkan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div>
                <h3>Güvenli Ödeme</h3>
                <p>Kart bilgileriniz PayTR altyapısında</p>
            </div>
        </li>
        <li>
            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'iade'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div>
                <h3><?php echo e(config('shop.cayma_hakki_gun')); ?> Gün İade</h3>
                <p>Teslimden sonra gerekçesiz cayma hakkı</p>
            </div>
        </li>
        <li>
            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'destek'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div>
                <h3>Mağaza Desteği</h3>
                <p>Sorularınız için Instagram ve telefon</p>
            </div>
        </li>
    </ul>
</section>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/parca/ozellikler.blade.php ENDPATH**/ ?>
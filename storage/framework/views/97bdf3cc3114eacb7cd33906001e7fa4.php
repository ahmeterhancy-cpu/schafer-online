

<?php $__env->startSection('baslik', 'İletişim — ' . config('shop.ad')); ?>
<?php $__env->startSection('aciklama', 'Schafer Kıbrıs — Gönyeli/Lefkoşa mağaza adresi, telefon, WhatsApp ve yol tarifi.'); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'İletişim'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $adres = config('shop.satici.adres');
    $telefon = config('shop.satici.telefon');
    $eposta = config('shop.satici.eposta');
    $saatler = config('shop.iletisim.calisma_saatleri');
    $wa = \App\Support\Iletisim::whatsappBaglanti();
    $waGosterim = \App\Support\Iletisim::whatsappGosterim();
    $harita = \App\Support\Iletisim::haritaGomme();
    $yolTarifi = \App\Support\Iletisim::yolTarifi();
?>

<div class="kap iletisim-sayfa">

    <div class="iletisim-duzen">

        
        <div class="iletisim-kanallar">
            <p class="iletisim-giris">
                Gönyeli’deki mağazamıza bekleriz. Ürün, stok ve teslimat sorularınız için
                en hızlı yanıtı WhatsApp’tan alırsınız.
            </p>

            <div class="iletisim-kart">
                <span class="iletisim-ikon"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'konum'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                <div>
                    <h2>Mağaza</h2>
                    <address><?php echo e($adres); ?></address>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($yolTarifi): ?>
                        <a class="iletisim-bag" href="<?php echo e($yolTarifi); ?>" target="_blank" rel="noopener noreferrer">
                            Yol tarifi al
                            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-sag'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wa): ?>
                <div class="iletisim-kart">
                    <span class="iletisim-ikon"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'destek'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                    <div>
                        <h2>WhatsApp</h2>
                        <p><a href="<?php echo e($wa); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($waGosterim); ?></a></p>
                        <p class="iletisim-not">Mesajınıza mağaza saatleri içinde dönüş yapılır.</p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($telefon): ?>
                <div class="iletisim-kart">
                    <span class="iletisim-ikon"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'telefon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                    <div>
                        <h2>Telefon</h2>
                        <p><a href="tel:<?php echo e(preg_replace('/\D/', '', $telefon)); ?>"><?php echo e($telefon); ?></a></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($eposta): ?>
                <div class="iletisim-kart">
                    <span class="iletisim-ikon"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'eposta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                    <div>
                        <h2>E-posta</h2>
                        <p><a href="mailto:<?php echo e($eposta); ?>"><?php echo e($eposta); ?></a></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($saatler): ?>
                <div class="iletisim-kart">
                    <span class="iletisim-ikon"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'saat'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                    <div>
                        <h2>Çalışma saatleri</h2>
                        <p><?php echo e($saatler); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="iletisim-kart">
                <span class="iletisim-ikon"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'instagram'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
                <div>
                    <h2>Instagram</h2>
                    <p>
                        <a href="https://instagram.com/<?php echo e(config('shop.sosyal.instagram')); ?>"
                           rel="noopener noreferrer" target="_blank">
                            &#64;<?php echo e(config('shop.sosyal.instagram')); ?>

                        </a>
                    </p>
                </div>
            </div>
        </div>

        
        <div class="iletisim-harita-kutu">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($harita): ?>
                
                <div class="harita-cerceve">
                    <iframe src="<?php echo e($harita); ?>"
                            title="Schafer Kıbrıs — mağaza konumu"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen></iframe>
                </div>
            <?php else: ?>
                <div class="harita-cerceve harita-yok">
                    <p>Mağaza adresi girildiğinde harita burada görünür.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="iletisim-yonlendirme">
        <a class="yonlendirme-kart" href="<?php echo e(route('order.lookup.form')); ?>">
            <span>Siparişimi sorgula</span>
            <small>Sipariş numarası ve e-posta ile durumu görün.</small>
        </a>
        <a class="yonlendirme-kart" href="<?php echo e(url('/sayfa/iade-degisim')); ?>">
            <span>İade ve değişim</span>
            <small>Ürünü nasıl iade edeceğinizi ya da değiştireceğinizi anlatır.</small>
        </a>
        <a class="yonlendirme-kart" href="<?php echo e(url('/sayfa/mesafeli-satis')); ?>">
            <span>Mesafeli satış sözleşmesi</span>
            <small>Satış koşulları ve cayma hakkı.</small>
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.unvan')): ?>
        <div class="iletisim-satici">
            <h2>Satıcı bilgileri</h2>
            <p>
                <?php echo e(config('shop.satici.unvan')); ?><br>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.mersis')): ?>
                    MERSİS: <?php echo e(config('shop.satici.mersis')); ?><br>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.vergi_no')): ?>
                    Vergi No: <?php echo e(config('shop.satici.vergi_no')); ?>

                    (<?php echo e(config('shop.satici.vergi_dairesi')); ?>)
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/iletisim.blade.php ENDPATH**/ ?>
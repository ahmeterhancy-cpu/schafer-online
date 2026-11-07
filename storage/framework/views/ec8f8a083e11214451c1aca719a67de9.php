<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('baslik', config('shop.ad')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('aciklama', 'Schafer Kıbrıs — mutfak, sofra, ev tekstili ve küçük ev aletleri. KKTC online mağaza.'); ?>">
    <link rel="icon" href="<?php echo e(asset('img/schafer/favicon.png')); ?>" type="image/png">
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

    
    <meta property="og:site_name" content="<?php echo e(config('shop.ad')); ?>">
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_tur', 'website'); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('baslik', config('shop.ad')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('aciklama', 'Schafer Kıbrıs — mutfak, sofra, ev tekstili ve küçük ev aletleri. KKTC online mağaza.'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('og_gorsel', asset('img/schafer/logo.png')); ?>">
    <meta property="og:locale" content="tr_TR">
    <meta name="twitter:card" content="summary_large_image">

    <?php echo $__env->yieldPushContent('yapisal_veri'); ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body id="ust">
    <a href="#icerik" class="gorunmez">İçeriğe atla</a>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.bakim_modu') && auth()->user()?->panelde()): ?>
        <div class="bakim-seridi">
            Bakım perdesi AÇIK — ziyaretçiler "Çok yakında" sayfasını görüyor.
            <a href="<?php echo e(url('/admin/site-ayarlari')); ?>">Ayarlardan kapat</a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="ust-serit">
        <div class="kap ust-serit-ic">
            <ul class="ust-serit-sol">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.telefon')): ?>
                    <li>
                        <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', config('shop.satici.telefon'))); ?>">
                            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'telefon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <strong>DESTEK HATTI:</strong> <?php echo e(config('shop.satici.telefon')); ?>

                        </a>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) config('shop.kargo.ucretsiz_esigi') > 0): ?>
                    <li class="ust-serit-duyuru">
                        <?php echo e(number_format((float) config('shop.kargo.ucretsiz_esigi'), 0, ',', '.')); ?> TL üzeri ücretsiz teslimat
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>

            <ul class="ust-serit-sag">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuler['ust-serit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><a href="<?php echo e($bag['adres']); ?>" <?php if($bag['yeni_sekme']): ?> target="_blank" rel="noopener" <?php endif; ?>><?php echo e($bag['etiket']); ?></a></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    </div>

    <header class="baslik">
        <div class="kap baslik-ic">
            
            <details class="mobil-menu">
                <summary aria-label="Menüyü aç">
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'menu'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </summary>
                <div class="mobil-menu-panel">
                    <div class="mobil-menu-ust"><span>Menü</span></div>
                    <form class="mobil-menu-arama" method="GET" action="<?php echo e(route('catalog.search')); ?>" role="search">
                        <label class="gorunmez" for="mobil-q">Ürün ara</label>
                        <input type="search" id="mobil-q" name="q" placeholder="Ürün ara…">
                        <button type="submit" aria-label="Ara"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'arama'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></button>
                    </form>
                    <nav aria-label="Mobil menü">
                        <a href="<?php echo e(url('/')); ?>">Ana Sayfa</a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuKategorileri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('catalog.category', $kat->slug)); ?>"><?php echo e($kat->name); ?></a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <a class="mobil-menu-alt" href="<?php echo e(route('catalog.category', $alt->slug)); ?>"><?php echo e($alt->name); ?></a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuler['ana']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e($bag['adres']); ?>" <?php if($bag['yeni_sekme']): ?> target="_blank" rel="noopener" <?php endif; ?>><?php echo e($bag['etiket']); ?></a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('account.index')); ?>">Hesabım</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>">Giriş / Üye Ol</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </nav>
                </div>
            </details>

            <form class="baslik-arama" method="GET" action="<?php echo e(route('catalog.search')); ?>" role="search">
                <label class="gorunmez" for="baslik-q">Ürün ara</label>
                <input type="search" id="baslik-q" name="q" value="<?php echo e(request('q')); ?>"
                       placeholder="Ürün ya da ürün kodu ara…" autocomplete="off">
                <button type="submit" aria-label="Ara">
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'arama'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </button>
            </form>

            <a href="<?php echo e(url('/')); ?>" class="baslik-logo" aria-label="<?php echo e(config('shop.ad')); ?> ana sayfa">
                <img src="<?php echo e(asset('img/schafer/logo.png')); ?>" alt="<?php echo e(config('shop.ad')); ?>" width="300" height="94">
            </a>

            <div class="baslik-ikonlar">
                <a href="<?php echo e(route('catalog.search')); ?>" class="baslik-ikon baslik-ikon-arama" aria-label="Ara">
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'arama'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </a>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('account.index')); ?>" class="baslik-ikon baslik-hesap">
                        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kullanici'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <span class="baslik-ikon-metin">Hesabım</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="baslik-ikon baslik-hesap">
                        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kullanici'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <span class="baslik-ikon-metin">Giriş / Kayıt</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <a href="<?php echo e(url('/sepet')); ?>" class="baslik-ikon sepet-bag" aria-label="Sepet, <?php echo e($sepetAdedi); ?> ürün">
                    <span class="baslik-ikon-kutu">
                        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'sepet'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <span class="sepet-sayi"><?php echo e($sepetAdedi); ?></span>
                    </span>
                    <span class="baslik-ikon-metin sepet-tutar"><?php echo e(number_format($sepetTutari, 2, ',', '.')); ?> ₺</span>
                </a>
            </div>
        </div>

        
        <nav class="menu-bant" aria-label="Ana menü">
            <ul class="kap ana-menu">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuKategorileri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['alti-var' => $kat->children->isNotEmpty()]); ?>">
                        <a href="<?php echo e(route('catalog.category', $kat->slug)); ?>">
                            <?php echo e($kat->name); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kat->children->isNotEmpty()): ?>
                                <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-asagi'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kat->children->isNotEmpty()): ?>
                            <div class="mega">
                                <div class="mega-ic">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="mega-sutun">
                                            <a class="mega-baslik" href="<?php echo e(route('catalog.category', $alt->slug)); ?>"><?php echo e($alt->name); ?></a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alt->children->isNotEmpty()): ?>
                                                <ul>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $alt->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $torun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <li><a href="<?php echo e(route('catalog.category', $torun->slug)); ?>"><?php echo e($torun->name); ?></a></li>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </ul>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuler['ana']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><a href="<?php echo e($bag['adres']); ?>" <?php if($bag['yeni_sekme']): ?> target="_blank" rel="noopener" <?php endif; ?>><?php echo e($bag['etiket']); ?></a></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </nav>
    </header>

    <main id="icerik">
        <?php echo $__env->yieldContent('icerik'); ?>
    </main>

    
    <section class="guven-serit" aria-label="Alışveriş güvenceleri">
        <div class="kap guven-serit-ic">
            <div>
                <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kamyon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span><strong>Hızlı Teslimat</strong><?php echo e((float) config('shop.kargo.ucretsiz_esigi') > 0 ? number_format((float) config('shop.kargo.ucretsiz_esigi'), 0, ',', '.') . ' TL üzeri ücretsiz' : 'KKTC geneline'); ?></span>
            </div>
            <div>
                <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kalkan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span><strong>Güvenli Alışveriş</strong>256 bit SSL ile korunan ödeme</span>
            </div>
            <div>
                <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'iade'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span><strong>İade &amp; Değişim</strong><?php echo e(config('shop.cayma_hakki_gun')); ?> gün içinde kolay iade</span>
            </div>
            <div>
                <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kart'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span><strong>Taksitli Alışveriş</strong>Kredi kartına taksit imkânı</span>
            </div>
        </div>
    </section>

    <footer class="alt">
        <div class="alt-ana">
            <div class="kap alt-izgara">
                <div class="alt-marka">
                    <img src="<?php echo e(asset('img/schafer/logo-beyaz.png')); ?>" alt="<?php echo e(config('shop.ad')); ?>" width="200" height="63" loading="lazy">
                    <p>Schafer'in mutfak, sofra, ev tekstili ve küçük ev aletleri ürünleri Kıbrıs'ta, güvenle kapınızda.</p>
                </div>

                <div>
                    <h3>Hızlı Linkler</h3>
                    <ul>
                        <li><a href="<?php echo e(url('/')); ?>">Ana Sayfa</a></li>
                        <li><a href="<?php echo e(url('/sayfa/hakkimizda')); ?>">Hakkımızda</a></li>
                        <li><a href="<?php echo e(url('/iletisim')); ?>">İletişim</a></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                            <li><a href="<?php echo e(route('register')); ?>">Üye Ol</a></li>
                            <li><a href="<?php echo e(route('login')); ?>">Giriş Yap</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo e(route('account.index')); ?>">Hesabım</a></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <li><a href="<?php echo e(url('/siparis-sorgula')); ?>">Sipariş Takibi</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Kurallar</h3>
                    <ul>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuler['alt-yardim']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><a href="<?php echo e($bag['adres']); ?>" <?php if($bag['yeni_sekme']): ?> target="_blank" rel="noopener" <?php endif; ?>><?php echo e($bag['etiket']); ?></a></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <li><a href="<?php echo e(url('/sayfa/mesafeli-satis')); ?>">Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="<?php echo e(url('/sayfa/kvkk')); ?>">Gizlilik ve Kişisel Veriler</a></li>
                        <li><a href="<?php echo e(url('/sayfa/cerez')); ?>">Çerez Politikası</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Kategoriler</h3>
                    <ul>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuKategorileri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><a href="<?php echo e(route('catalog.category', $kat->slug)); ?>"><?php echo e($kat->name); ?></a></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <li><a href="<?php echo e(url('/ara?q=çeyiz')); ?>">Evlilik Paketi</a></li>
                    </ul>
                </div>

                <div>
                    <h3>İletişim</h3>
                    <ul class="alt-iletisim">
                        <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'konum'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <address><?php echo e(config('shop.satici.adres')); ?></address></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_filter([config('shop.satici.telefon'), config('shop.satici.telefon2')]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'telefon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $tel)); ?>"><?php echo e($tel); ?></a></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.satici.eposta')): ?>
                            <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'eposta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <a href="mailto:<?php echo e(config('shop.satici.eposta')); ?>"><?php echo e(config('shop.satici.eposta')); ?></a></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('shop.sosyal.instagram')): ?>
                            <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'instagram'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <a href="https://instagram.com/<?php echo e(config('shop.sosyal.instagram')); ?>" rel="noopener noreferrer" target="_blank">&#64;<?php echo e(config('shop.sosyal.instagram')); ?></a></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="alt-kunye">
            <div class="kap alt-kunye-ic">
                <span>&copy; <?php echo e(now()->year); ?> <?php echo e(config('shop.alan_adi') ?: config('shop.ad')); ?> · Tüm hakları saklıdır.</span>
                <span class="alt-imza">
                    Web Site by <a href="https://www.amesis.com.tr" rel="noopener" target="_blank">Amesis Dijital Ajans</a>
                </span>
                <span class="alt-odeme">
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kalkan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    Kredi ve banka kartıyla güvenli ödeme
                </span>
            </div>
        </div>
    </footer>

    
    <nav class="mobil-alt" aria-label="Hızlı erişim">
        <a href="<?php echo e(url('/koleksiyonlar')); ?>"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'magaza'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><span>Mağaza</span></a>
        <a href="<?php echo e(route('catalog.search')); ?>"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'arama'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><span>Ara</span></a>
        <a href="<?php echo e(url('/sepet')); ?>">
            <span class="baslik-ikon-kutu">
                <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'sepet'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span class="sepet-sayi"><?php echo e($sepetAdedi); ?></span>
            </span>
            <span>Sepet</span>
        </a>
        <a href="<?php echo e(route('account.index')); ?>"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kullanici'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><span>Hesap</span></a>
    </nav>

    
    <?php ($waBaglanti = \App\Support\Iletisim::whatsappBaglanti()); ?>
    <div class="sabit-kose">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($waBaglanti): ?>
            <a class="wa-dugme" href="<?php echo e($waBaglanti); ?>" target="_blank" rel="noopener noreferrer"
               aria-label="WhatsApp ile yazın">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.450 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.910-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.13h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.220 8.22 0 0 1-1.26-4.36c0-4.54 3.7-8.23 8.25-8.23 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.21-8.24 8.21Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.15.16-.29.18-.53.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.47c-.16 0-.43.06-.65.31-.22.25-.85.84-.85 2.04s.87 2.37.99 2.53c.12.16 1.71 2.61 4.15 3.66.58.25 1.03.4 1.39.51.58.19 1.11.16 1.53.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29Z"/>
                </svg>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <a href="#ust" class="yukari" aria-label="Sayfa başına dön"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-yukari'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></a>
    </div>

    <?php echo $__env->yieldPushContent('betik'); ?>
</body>
</html>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/layout/vitrin.blade.php ENDPATH**/ ?>
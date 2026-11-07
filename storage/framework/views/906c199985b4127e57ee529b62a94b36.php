

<?php $__env->startSection('baslik', $urun->name . ' — ' . config('shop.ad')); ?>
<?php $__env->startSection('aciklama', $urun->short_description ?: $urun->name); ?>

<?php $__env->startSection('og_tur', 'product'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->hero_image): ?>
    <?php $__env->startSection('og_gorsel', asset('storage/' . $urun->hero_image)); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->startPush('yapisal_veri'); ?>
    <?php echo $__env->make('vitrin.parca.jsonld', ['tur' => 'urun', 'urun' => $urun, 'yorumlar' => $yorumlar], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('icerik'); ?>
<?php
    // TUZAK: @json(...) içine parantezli/çok satırlı ifade yazılmaz.
    // Değerler burada hazırlanıp aşağıda tek değişken olarak veriliyor.
    $varyantVerisi = $varyantlar->all();
    $galeriVerisi = $renkGalerisi->all();
    $genelGaleri = $genelYollar->all();
    $ilkGorsel = $genelYollar->first();
?>


<div class="kap">
    <nav class="iz iz-sol" aria-label="Konum">
        <a href="<?php echo e(route('home')); ?>">Ana Sayfa</a>
        <span aria-hidden="true">/</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->collection): ?>
            <a href="<?php echo e(route('collections.show', $urun->collection->slug)); ?>"><?php echo e($urun->collection->name); ?></a>
        <?php else: ?>
            <a href="<?php echo e(route('collections.index')); ?>">Mağaza</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span aria-hidden="true">/</span>
        <span aria-current="page"><?php echo e($urun->name); ?></span>
    </nav>
</div>

<div class="kap urun-sayfa">

    <div class="urun-duzen">

        
        <div class="urun-galeri <?php echo e($genelYollar->count() > 1 ? 'urun-galeri-coklu' : ''); ?>" id="galeri">
            <div class="urun-galeri-ana <?php echo e($ilkGorsel ? '' : 'urun-gorsel-yok'); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ilkGorsel): ?>
                    
                    <img id="galeri-ana"
                         src="<?php echo e(asset('storage/' . $ilkGorsel)); ?>"
                         data-taban="<?php echo e(rtrim(asset('storage'), '/')); ?>/"
                         data-ilk="<?php echo e(asset('storage/' . $ilkGorsel)); ?>"
                         alt="<?php echo e($urun->name); ?>">
                <?php else: ?>
                    <span class="urun-harf" aria-hidden="true">Z</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="urun-rozetler">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->badge): ?>
                        <span class="rozet rozet-yeni"><?php echo e($urun->badge); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            
            <div class="galeri-serit" id="galeri-serit" <?php if($genelYollar->count() < 2): ?> hidden <?php endif; ?>>
                <button type="button" class="serit-ok serit-ok-geri" data-yon="-1"
                        aria-label="Önceki görseller" hidden>
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-yukari'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </button>

                <div class="urun-galeri-kucuk" id="galeri-kucukler">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $genelYollar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="galeri-kucuk-dugme"
                                data-gorsel="<?php echo e(asset('storage/' . $yol)); ?>">
                            <img src="<?php echo e(asset('storage/' . $yol)); ?>"
                                 alt="<?php echo e($urun->name); ?> görsel <?php echo e($loop->iteration); ?>" loading="lazy">
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <button type="button" class="serit-ok serit-ok-ileri" data-yon="1"
                        aria-label="Sonraki görseller" hidden>
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-asagi'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </button>
            </div>
        </div>

        
        <div class="urun-panel">
            <h1><?php echo e($urun->name); ?></h1>

            <div class="urun-panel-ust">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->review_count > 0): ?>
                    <a href="#urun-sekmeler" class="puan-ozet" data-sekme-ac="sekme-yorumlar">
                        <span class="yildiz" aria-hidden="true"><?php echo e(str_repeat('★', (int) round((float) $urun->rating))); ?><span class="yildiz-bos"><?php echo e(str_repeat('★', 5 - (int) round((float) $urun->rating))); ?></span></span>
                        <span><?php echo e(number_format((float) $urun->rating, 1, ',', '')); ?> / 5 · <?php echo e($urun->review_count); ?> değerlendirme</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->base_sku): ?>
                    <span class="urun-panel-sku">Ürün kodu: <?php echo e($urun->base_sku); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <p class="urun-panel-fiyat" id="secim-fiyat">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->has_price_range): ?>
                    <?php echo e(number_format((float) $urun->min_price, 2, ',', '.')); ?> –
                    <?php echo e(number_format((float) $urun->max_price, 2, ',', '.')); ?> TL
                <?php else: ?>
                    <?php echo e(number_format((float) $urun->min_price, 2, ',', '.')); ?> TL
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->short_description): ?>
                <p class="urun-panel-ozet"><?php echo e($urun->short_description); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('hata')): ?>
                <p class="uyari uyari-hata"><?php echo e(session('hata')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form method="POST" action="<?php echo e(route('cart.add')); ?>" id="sepet-formu">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="variant_id" id="variant-id" value="">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urun->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eksen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    
                    <?php if($eksen->values->isEmpty()): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?>
                    <fieldset class="eksen" data-eksen="<?php echo e($eksen->id); ?>">
                        <legend class="eksen-baslik">
                            <span><?php echo e($eksen->name); ?></span>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bedenTablosu && ! $eksen->is_color && $loop->first): ?>
                                <button type="button" class="beden-tablosu-ac">Ölçü tablosu</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </legend>

                        <div class="eksen-secenekler <?php echo e($eksen->is_color ? 'eksen-renk' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $eksen->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <label class="secenek <?php echo e($eksen->is_color ? 'secenek-renk' : ''); ?>">
                                    <input type="radio" name="eksen_<?php echo e($eksen->id); ?>"
                                           value="<?php echo e($deger->id); ?>" class="gorunmez eksen-girdi">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($eksen->is_color): ?>
                                        <span class="secenek-nokta"
                                              style="background: <?php echo e($deger->color_hex ?: 'transparent'); ?>"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="secenek-ad"><?php echo e($deger->value); ?></span>
                                </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </fieldset>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <p class="secim-durum" id="secim-durum" role="status"></p>

                <div class="sepete-satir">
                    <label class="gorunmez" for="adet">Adet</label>
                    <input type="number" name="quantity" id="adet" value="1" min="1" max="20" class="adet-girdi">
                    <button type="submit" class="dugme sepete-dugme" id="sepete-dugme" disabled>
                        Seçim yapın
                    </button>
                </div>
            </form>

            
            <div class="haber-ver" id="haber-ver" hidden>
                <p class="haber-ver-baslik">Bu ürün tükendi</p>
                <p class="haber-ver-metin">
                    Yeniden stoğa girdiğinde size haber verelim.
                </p>

                <form method="POST" action="<?php echo e(route('stock.inquiry')); ?>" class="haber-ver-formu">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="variant_id" id="haber-ver-varyant" value="">
                    <label class="gorunmez" for="haber-ver-eposta">E-posta</label>
                    <input type="email" id="haber-ver-eposta" name="eposta"
                           placeholder="E-posta adresiniz" class="metin-girdi" required>
                    <button type="submit" class="dugme dugme-cizgi">Haber ver</button>
                </form>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('bilgi')): ?>
                <p class="uyari"><?php echo e(session('bilgi')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <ul class="urun-meta">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->collection): ?>
                    <li><span>Koleksiyon:</span> <a href="<?php echo e(route('collections.show', $urun->collection->slug)); ?>"><?php echo e($urun->collection->name); ?></a></li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->categories->isNotEmpty()): ?>
                    <li><span>Kategori:</span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urun->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('catalog.category', $kat->slug)); ?>"><?php echo e($kat->name); ?></a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $loop->last): ?>,<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>

            <ul class="urun-guvence">
                <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kamyon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) config('shop.kargo.ucretsiz_esigi') > 0): ?>
                        <?php echo e(number_format((float) config('shop.kargo.ucretsiz_esigi'), 0, ',', '.')); ?> TL üzeri kargo ücretsiz
                    <?php else: ?>
                        Türkiye geneli kargo
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
                <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'iade'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Teslimden sonra <?php echo e(config('shop.cayma_hakki_gun')); ?> gün iade hakkı</li>
                <li><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kalkan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> PayTR ile güvenli ödeme</li>
            </ul>
        </div>
    </div>

    
    <section class="urun-sekmeler bolum" id="urun-sekmeler" data-sekmeler>
        <div class="sekmeler" role="tablist" aria-label="Ürün bilgileri">
            <button type="button" class="sekme" role="tab" id="sekme-aciklama" aria-controls="panel-aciklama" aria-selected="true">Açıklama</button>
            <button type="button" class="sekme" role="tab" id="sekme-bilgi" aria-controls="panel-bilgi" aria-selected="false">Ek Bilgi</button>
            <button type="button" class="sekme" role="tab" id="sekme-yorumlar" aria-controls="panel-yorumlar" aria-selected="false">Değerlendirmeler (<?php echo e($urun->review_count); ?>)</button>
        </div>

        <div class="sekme-panel" role="tabpanel" id="panel-aciklama" aria-labelledby="sekme-aciklama">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->description): ?>
                <div class="urun-aciklama"><?php echo nl2br(e($urun->description)); ?></div>
            <?php else: ?>
                <p class="urun-aciklama"><?php echo e($urun->short_description ?: 'Bu ürün için ayrıntılı açıklama henüz eklenmedi.'); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="sekme-panel" role="tabpanel" id="panel-bilgi" aria-labelledby="sekme-bilgi">
            <table class="urun-detay">
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urun->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eksen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr><th scope="row"><?php echo e($eksen->name); ?></th><td><?php echo e($eksen->values->pluck('value')->implode(', ')); ?></td></tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->material): ?>
                        <tr><th scope="row">Kumaş</th><td><?php echo e($urun->material); ?></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->model_note): ?>
                        <tr><th scope="row">Model ölçüsü</th><td><?php echo e($urun->model_note); ?></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->care_notes): ?>
                        <tr><th scope="row">Bakım</th><td><?php echo e($urun->care_notes); ?></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <tr>
                        <th scope="row">Kargo ve iade</th>
                        <td>
                            <?php echo e(number_format((float) config('shop.kargo.ucretsiz_esigi'), 0, ',', '.')); ?> TL üzeri kargo ücretsiz.
                            Teslimden sonra <?php echo e(config('shop.cayma_hakki_gun')); ?> gün iade hakkınız var.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="sekme-panel yorumlar" role="tabpanel" id="panel-yorumlar" aria-labelledby="sekme-yorumlar">
            <div id="yorumlar"></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($yorumlar->isNotEmpty()): ?>
                <p class="yorumlar-ozet">
                    <strong><?php echo e(number_format((float) $urun->rating, 1, ',', '')); ?></strong> / 5
                    · <?php echo e($urun->review_count); ?> değerlendirme
                </p>

                <ul class="yorum-liste">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $yorumlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yorum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li class="yorum">
                            <p class="yorum-ust">
                                <span class="yildiz" aria-label="<?php echo e($yorum->rating); ?> / 5"><?php echo e(str_repeat('★', $yorum->rating)); ?><span class="yildiz-bos"><?php echo e(str_repeat('★', 5 - $yorum->rating)); ?></span></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($yorum->title): ?>
                                    <strong><?php echo e($yorum->title); ?></strong>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p class="yorum-metin"><?php echo nl2br(e($yorum->body)); ?></p>
                            <p class="yorum-alt">
                                <?php echo e($yorum->author_name); ?> · <?php echo e(($yorum->approved_at ?? $yorum->created_at)->translatedFormat('d F Y')); ?>

                                · <span class="yorum-dogrulandi">Satın aldı</span>
                            </p>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            <?php else: ?>
                <p>Bu ürün için henüz değerlendirme yok.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <p class="yorum-not">
                Değerlendirmeyi yalnız ürünü satın alan müşterilerimiz, teslimattan sonra sipariş sayfalarından yazabilir.
            </p>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bedenTablosu): ?>
        <dialog class="beden-tablosu" id="beden-tablosu">
            <div class="beden-tablosu-ic">
                <div class="beden-tablosu-ust">
                    <h2><?php echo e($bedenTablosu->name); ?> — Ölçü Tablosu</h2>
                    <button type="button" class="beden-tablosu-kapat" aria-label="Kapat">&times;</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bedenTablosu->columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sutun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <th scope="col"><?php echo e($sutun); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bedenTablosu->rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satir): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $satir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hucre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <td><?php echo e($hucre); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bedenTablosu->note): ?>
                    <p class="beden-tablosu-not"><?php echo e($bedenTablosu->note); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </dialog>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->related->isNotEmpty()): ?>
        <section class="bolum">
            <div class="bolum-baslik"><h2>Birlikte kullanın</h2></div>
            <?php echo $__env->make('vitrin.parca.urun-serit', ['urunler' => $urun->related], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($benzerler->isNotEmpty()): ?>
        <section class="bolum">
            <div class="bolum-baslik"><h2>Benzer ürünler</h2></div>
            <?php echo $__env->make('vitrin.parca.urun-serit', ['urunler' => $benzerler], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php $__env->startPush('betik'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/urun.js']); ?>
<?php $__env->stopPush(); ?>

<script type="application/json" id="varyant-verisi"><?php echo json_encode($varyantVerisi, 15, 512) ?></script>
<script type="application/json" id="galeri-verisi"><?php echo json_encode($galeriVerisi, 15, 512) ?></script>
<script type="application/json" id="genel-galeri-verisi"><?php echo json_encode($genelGaleri, 15, 512) ?></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/urun.blade.php ENDPATH**/ ?>
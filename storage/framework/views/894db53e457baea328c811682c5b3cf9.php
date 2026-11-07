

<?php $__env->startSection('baslik', 'Sipariş ' . $order->number . ' — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php
    // Değişim için seçilebilecek varyantlar kalem bazında hazırlanıyor;
    // Blade içinde sorgu döndürmemek için burada toplanıyor.
    $degisimSecenekleri = [];

    foreach ($order->items as $kalem) {
        $urun = $kalem->variant?->product;

        $degisimSecenekleri[$kalem->id] = $urun
            ? $urun->variants()->where('is_active', true)->with('optionValues.option')->get()
            : collect();
    }
?>

<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Sipariş ' . $order->number, 'konum' => ['Sipariş Sorgula' => route('order.lookup.form'), $order->number => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap kap-dar siparis-sayfa">

    <div class="siparis-basi">
        <div>
            <h2><?php echo e($order->number); ?></h2>
            <p class="siparis-tarih"><?php echo e($order->created_at->format('d.m.Y H:i')); ?></p>
        </div>
        <span class="siparis-durum siparis-durum-<?php echo e($order->status); ?>"><?php echo e($order->status_label); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->invoice_pdf): ?>
        <p class="fatura-bag">
            <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'eposta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            Faturanız hazır (<?php echo e($order->invoice_number); ?>) —
            <a href="<?php echo e(\Illuminate\Support\Facades\URL::signedRoute('order.invoice', ['order' => $order->number])); ?>">PDF olarak indirin</a>
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('bilgi')): ?>
        <p class="uyari"><?php echo e(session('bilgi')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('hata')): ?>
        <p class="uyari uyari-hata"><?php echo e(session('hata')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->tracking_number): ?>
        <div class="kargo-kutu">
            <span class="etiket">Kargo</span>
            <p>
                <?php echo e($order->shipping_carrier); ?> ·
                <strong><?php echo e($order->tracking_number); ?></strong>
            </p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->shipped_at): ?>
                <p class="kargo-tarih"><?php echo e($order->shipped_at->format('d.m.Y')); ?> tarihinde kargoya verildi.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="bolum">
        <div class="bolum-basi">
            <h2>Ürünler</h2>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kalem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="siparis-kalem">
                <div class="sepet-gorsel <?php echo e($kalem->image ? '' : 'urun-gorsel-yok'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->image): ?>
                        <img src="<?php echo e(asset('storage/' . $kalem->image)); ?>" alt="<?php echo e($kalem->name); ?>">
                    <?php else: ?>
                        <span class="urun-harf" aria-hidden="true">Z</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="sepet-bilgi">
                    <p class="sepet-ad"><?php echo e($kalem->name); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->variant_label): ?>
                        <p class="sepet-varyant"><?php echo e($kalem->variant_label); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <p class="sepet-sku"><?php echo e($kalem->sku); ?></p>
                </div>

                <div class="sepet-tutar">
                    <span>× <?php echo e($kalem->quantity); ?></span>
                    <span><?php echo e(number_format((float) $kalem->line_total, 2, ',', '.')); ?> TL</span>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <div class="siparis-toplam">
            <div class="ozet-satir">
                <span>Ara toplam</span>
                <span><?php echo e(number_format((float) $order->subtotal, 2, ',', '.')); ?> TL</span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $order->discount_total > 0): ?>
                <div class="ozet-satir ozet-indirim">
                    <span>İndirim</span>
                    <span>-<?php echo e(number_format((float) $order->discount_total, 2, ',', '.')); ?> TL</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="ozet-satir">
                <span>Kargo</span>
                <span>
                    <?php echo e((float) $order->shipping_total > 0
                        ? number_format((float) $order->shipping_total, 2, ',', '.') . ' TL'
                        : 'Ücretsiz'); ?>

                </span>
            </div>
            <div class="ozet-satir ozet-toplam">
                <span>Toplam</span>
                <span><?php echo e(number_format((float) $order->grand_total, 2, ',', '.')); ?> TL</span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $order->refunded_total > 0): ?>
                <div class="ozet-satir ozet-indirim">
                    <span>İade edilen</span>
                    <span><?php echo e(number_format((float) $order->refunded_total, 2, ',', '.')); ?> TL</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($degerlendirme->isNotEmpty()): ?>
        <section class="bolum" id="degerlendirme">
            <div class="bolum-basi">
                <h2>Ürünleri Değerlendirin</h2>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $degerlendirme; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="degerlendir">
                    <p class="degerlendir-ad"><?php echo e($d['ad']); ?></p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d['yorum']): ?>
                        <p class="degerlendir-durum">
                            <span class="yildiz" aria-label="<?php echo e($d['yorum']->rating); ?> / 5"><?php echo e(str_repeat('★', $d['yorum']->rating)); ?><span class="yildiz-bos"><?php echo e(str_repeat('★', 5 - $d['yorum']->rating)); ?></span></span>
                            <?php echo e($d['yorum']->status === 'approved' ? 'Yayında — teşekkürler.' : ($d['yorum']->status === 'rejected' ? 'Yayımlanmadı.' : 'Onay bekliyor.')); ?>

                        </p>
                    <?php else: ?>
                        <details class="degerlendir-ac" <?php if(old('product_id') == $d['product_id']): ?> open <?php endif; ?>>
                            <summary>Değerlendirme yaz</summary>

                            <form method="POST"
                                  action="<?php echo e(\Illuminate\Support\Facades\URL::signedRoute('order.review', ['order' => $order->number])); ?>"
                                  class="degerlendir-formu">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="product_id" value="<?php echo e($d['product_id']); ?>">

                                <fieldset class="puan-sec">
                                    <legend class="etiket">Puanınız</legend>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($p = 5; $p >= 1; $p--): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <input type="radio" name="puan" value="<?php echo e($p); ?>"
                                               id="puan-<?php echo e($d['product_id']); ?>-<?php echo e($p); ?>"
                                               <?php if(old('product_id') == $d['product_id'] && old('puan') == $p): echo 'checked'; endif; ?> required>
                                        <label for="puan-<?php echo e($d['product_id']); ?>-<?php echo e($p); ?>" title="<?php echo e($p); ?> / 5">
                                            <span class="gorunmez"><?php echo e($p); ?> yıldız</span>★
                                        </label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </fieldset>

                                <div class="alan">
                                    <label for="baslik-<?php echo e($d['product_id']); ?>">Başlık (isteğe bağlı)</label>
                                    <input type="text" id="baslik-<?php echo e($d['product_id']); ?>" name="baslik" maxlength="120" class="metin-girdi"
                                           value="<?php echo e(old('product_id') == $d['product_id'] ? old('baslik') : ''); ?>">
                                </div>

                                <div class="alan">
                                    <label for="yorum-<?php echo e($d['product_id']); ?>">Yorumunuz</label>
                                    <textarea id="yorum-<?php echo e($d['product_id']); ?>" name="yorum" rows="4" minlength="10" maxlength="1500" required class="metin-girdi"><?php echo e(old('product_id') == $d['product_id'] ? old('yorum') : ''); ?></textarea>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(old('product_id') == $d['product_id'] && $errors->any()): ?>
                                    <p class="uyari uyari-hata"><?php echo e($errors->first()); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <p class="degerlendir-not">
                                    Ürün sayfasında adınız "<?php echo e(\App\Models\ProductReview::gorunenAd($order->customer_name)); ?>" olarak görünür.
                                    Yorumlar onaydan sonra yayımlanır.
                                </p>

                                <button type="submit" class="dugme">Gönder</button>
                            </form>
                        </details>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->returnRequests->isNotEmpty()): ?>
        <section class="bolum">
            <div class="bolum-basi">
                <h2>İade / Değişim Talepleriniz</h2>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->returnRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="talep-satir">
                    <div>
                        <p class="talep-no"><?php echo e($talep->number); ?></p>
                        <p class="talep-tur">
                            <?php echo e($talep->is_exchange ? 'Değişim' : 'İade'); ?> ·
                            <?php echo e($talep->created_at->format('d.m.Y')); ?>

                        </p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($talep->status === 'rejected' && $talep->admin_note): ?>
                            <p class="talep-ret"><?php echo e($talep->admin_note); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="talep-durum"><?php echo e($talep->status_label); ?></span>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="bolum">
        <div class="bolum-basi">
            <h2>İade / Değişim</h2>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $iadeAcilabilir): ?>
            <div class="bos-durum">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_status !== 'paid'): ?>
                    <p>Ödemesi tamamlanmamış sipariş için talep açılamaz.</p>
                <?php else: ?>
                    <p>
                        Cayma hakkı süresi dolmuş.
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($caymaSonu): ?>
                            Son gün <?php echo e($caymaSonu->format('d.m.Y')); ?> idi.
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        <?php elseif($iadeEdilebilir->sum() < 1): ?>
            <div class="bos-durum">
                <p>Bu siparişteki tüm ürünler için talep açılmış.</p>
            </div>

        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($caymaSonu): ?>
                <p class="cayma-not">
                    Talep açma süreniz <strong><?php echo e($caymaSonu->format('d.m.Y')); ?></strong> tarihinde doluyor.
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <form method="POST" action="<?php echo e(\Illuminate\Support\Facades\URL::signedRoute('order.return', ['order' => $order->number])); ?>" class="iade-formu">
                <?php echo csrf_field(); ?>

                <fieldset class="form-blok">
                    <legend class="etiket">Ürünler</legend>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kalem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php ($kalan = $iadeEdilebilir[$kalem->id] ?? 0); ?>

                        <div class="iade-kalem <?php echo e($kalan < 1 ? 'iade-kalem-kapali' : ''); ?>">
                            <label class="onay-kutu">
                                <input type="checkbox"
                                       name="kalemler[<?php echo e($kalem->id); ?>][sec]"
                                       value="1"
                                       <?php if($kalan < 1): echo 'disabled'; endif; ?>>
                                <span>
                                    <strong><?php echo e($kalem->name); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalem->variant_label): ?>
                                        <br><?php echo e($kalem->variant_label); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalan < 1): ?>
                                        <br><em>Bu ürün için talep açılmış.</em>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </label>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalan > 0): ?>
                                <div class="iade-kalem-alanlar">
                                    <label>
                                        Adet
                                        <input type="number"
                                               name="kalemler[<?php echo e($kalem->id); ?>][adet]"
                                               value="1" min="1" max="<?php echo e($kalan); ?>"
                                               class="adet-girdi">
                                    </label>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($degisimSecenekleri[$kalem->id]->isNotEmpty()): ?>
                                        <label class="degisim-secim">
                                            Değişimde istediğiniz ürün / renk
                                            <select name="kalemler[<?php echo e($kalem->id); ?>][degisim_varyant]"
                                                    class="metin-girdi">
                                                <option value="">Seçiniz (yalnızca değişimde)</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $degisimSecenekleri[$kalem->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $varyant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <option value="<?php echo e($varyant->id); ?>"
                                                            <?php if($varyant->available_stock < 1): echo 'disabled'; endif; ?>>
                                                        <?php echo e($varyant->label); ?>

                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($varyant->available_stock < 1): ?> — tükendi <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </select>
                                        </label>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </fieldset>

                <fieldset class="form-blok">
                    <legend class="etiket">Talep</legend>

                    <div class="alan-ikili">
                        <div class="alan">
                            <label for="tur">Ne yapmak istiyorsunuz?</label>
                            <select id="tur" name="tur" class="metin-girdi" required>
                                <option value="return">İade — ücret iadesi</option>
                                <option value="exchange">Değişim — başka renk / model</option>
                            </select>
                        </div>

                        <div class="alan">
                            <label for="gerekce">Gerekçe</label>
                            <select id="gerekce" name="gerekce" class="metin-girdi" required>
                                <option value="beden">Ölçü / renk uygun değil</option>
                                <option value="kusurlu">Ürün kusurlu</option>
                                <option value="yanlis-urun">Yanlış ürün geldi</option>
                                <option value="vazgectim">Vazgeçtim</option>
                                <option value="diger">Diğer</option>
                            </select>
                        </div>
                    </div>

                    <div class="alan">
                        <label for="not">Eklemek istedikleriniz (isteğe bağlı)</label>
                        <textarea id="not" name="not" rows="3" class="metin-girdi"></textarea>
                    </div>
                </fieldset>

                <button type="submit" class="dugme">Talep oluştur</button>

                <p class="ozet-not" style="text-align:left; margin-top:14px">
                    Talebinizi aldıktan sonra ürünü nasıl göndereceğinizi e-posta ile ileteceğiz.
                    Ürünün kullanılmamış ve etiketlerinin sökülmemiş olması gerekir.
                </p>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/siparis.blade.php ENDPATH**/ ?>
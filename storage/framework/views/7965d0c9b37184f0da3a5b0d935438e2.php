

<?php $__env->startSection('baslik', 'Sepetim — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Sepetim'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap sepet-sayfa">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('bilgi')): ?>
        <p class="uyari uyari-bilgi"><?php echo e(session('bilgi')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('hata')): ?>
        <p class="uyari uyari-hata"><?php echo e(session('hata')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($satirlar->isEmpty()): ?>
        <div class="bos-durum">
            <p>Sepetiniz henüz boş.</p>
            <a class="dugme" href="<?php echo e(route('collections.index')); ?>">Koleksiyonları gör</a>
        </div>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($satirlar->contains('adjusted', true)): ?>
            <p class="uyari uyari-hata">
                Sepetinizdeki bazı ürünlerin stoğu değişti. Aşağıda işaretli satırları
                gözden geçirip güncelleyin.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="sepet-duzen">
            <div class="sepet-satirlar">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $satirlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satir): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="sepet-satir <?php echo e($satir['adjusted'] ? 'sepet-satir-uyari' : ''); ?>">
                        <?php ($satirGorsel = $satir['variant']->image ?: $satir['product']->hero_image); ?>
                        <div class="sepet-gorsel <?php echo e($satirGorsel ? '' : 'urun-gorsel-yok'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($satirGorsel): ?>
                                <img src="<?php echo e(asset('storage/' . $satirGorsel)); ?>"
                                     alt="<?php echo e($satir['product']->name); ?>">
                            <?php else: ?>
                                <span class="urun-harf" aria-hidden="true">Z</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="sepet-bilgi">
                            <a class="sepet-ad" href="<?php echo e(route('products.show', $satir['product']->slug)); ?>">
                                <?php echo e($satir['product']->name); ?>

                            </a>
                            <p class="sepet-varyant"><?php echo e($satir['label']); ?></p>
                            <p class="sepet-sku"><?php echo e($satir['variant']->sku); ?></p>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($satir['sold_out']): ?>
                                <p class="sepet-uyari-metin">Bu ürün tükendi — sepetten çıkarın.</p>
                            <?php elseif($satir['adjusted']): ?>
                                <p class="sepet-uyari-metin">
                                    Stok <?php echo e($satir['quantity']); ?> adete düştü
                                    (siz <?php echo e($satir['requested']); ?> adet istemiştiniz).
                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="sepet-adet">
                            <form method="POST" action="<?php echo e(route('cart.update')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="variant_id" value="<?php echo e($satir['variant']->id); ?>">
                                <label class="gorunmez" for="adet-<?php echo e($satir['variant']->id); ?>">Adet</label>
                                <input type="number" id="adet-<?php echo e($satir['variant']->id); ?>" name="quantity"
                                       value="<?php echo e($satir['quantity']); ?>" min="0"
                                       max="<?php echo e(max(1, $satir['variant']->available_stock)); ?>" class="adet-girdi">
                                <button type="submit" class="metin-dugme">Güncelle</button>
                            </form>
                        </div>

                        <div class="sepet-tutar">
                            <span><?php echo e(number_format($satir['line_total'], 2, ',', '.')); ?> TL</span>
                            <form method="POST" action="<?php echo e(route('cart.remove')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="variant_id" value="<?php echo e($satir['variant']->id); ?>">
                                <button type="submit" class="metin-dugme metin-dugme-sil">Çıkar</button>
                            </form>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <aside class="sepet-ozet">
                <h3>Sipariş Özeti</h3>

                <div class="ozet-satir">
                    <span>Ara toplam</span>
                    <span><?php echo e(number_format($araToplam, 2, ',', '.')); ?> TL</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($indirim > 0): ?>
                    <div class="ozet-satir ozet-indirim">
                        <span>İndirim<?php echo e($kupon ? ' (' . $kupon->code . ')' : ''); ?></span>
                        <span>-<?php echo e(number_format($indirim, 2, ',', '.')); ?> TL</span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ozet-satir">
                    <span>Kargo</span>
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kargo > 0): ?>
                            <?php echo e(number_format($kargo, 2, ',', '.')); ?> TL
                        <?php else: ?>
                            Ücretsiz
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kalanKargo > 0): ?>
                    <p class="ozet-tesvik">
                        Ücretsiz kargoya <?php echo e(number_format($kalanKargo, 2, ',', '.')); ?> TL kaldı.
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ozet-satir ozet-toplam">
                    <span>Toplam</span>
                    <span><?php echo e(number_format($toplam, 2, ',', '.')); ?> TL</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kupon): ?>
                    <form method="POST" action="<?php echo e(route('cart.coupon.forget')); ?>" class="kupon-formu">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="metin-dugme">Kuponu kaldır</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('cart.coupon')); ?>" class="kupon-formu">
                        <?php echo csrf_field(); ?>
                        <label class="gorunmez" for="kupon">Kupon kodu</label>
                        <input type="text" id="kupon" name="code" placeholder="Kupon kodu" class="metin-girdi">
                        <button type="submit" class="dugme dugme-cizgi">Uygula</button>
                    </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <a class="dugme sepet-odeme" href="<?php echo e(route('checkout.form')); ?>">Ödemeye geç</a>
            </aside>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/sepet.blade.php ENDPATH**/ ?>
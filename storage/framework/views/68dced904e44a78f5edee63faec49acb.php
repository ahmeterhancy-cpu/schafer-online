
<?php
    $urunAdresi = url('/urun/' . $urun->slug);
    $ikinci = $urun->ikinci_gorsel;
    $tek = $urun->tek_varyant;
?>
<article class="urun-kart">
    <div class="urun-gorsel <?php echo e($urun->hero_image ? '' : 'urun-gorsel-yok'); ?>">
        <a href="<?php echo e($urunAdresi); ?>" class="urun-gorsel-bag" tabindex="-1" aria-hidden="true">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->hero_image): ?>
                <img src="<?php echo e(asset('storage/' . $urun->hero_image)); ?>" alt="" loading="lazy" class="urun-gorsel-on">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ikinci): ?>
                    <img src="<?php echo e(asset('storage/' . $ikinci)); ?>" alt="" loading="lazy" class="urun-gorsel-arka">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <span class="urun-harf">S</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>

        <div class="urun-rozetler">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->indirim_orani): ?>
                <span class="rozet rozet-indirim">-%<?php echo e($urun->indirim_orani); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->badge): ?>
                <span class="rozet rozet-yeni urun-rozet"><?php echo e($urun->badge); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->total_stock < 1): ?>
                <span class="rozet rozet-tukendi">Tükendi</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="urun-bilgi">
        <h3 class="urun-ad"><a href="<?php echo e($urunAdresi); ?>"><?php echo e($urun->name); ?></a></h3>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->review_count > 0): ?>
            <p class="urun-puan" aria-label="<?php echo e(number_format((float) $urun->rating, 1, ',', '')); ?> / 5, <?php echo e($urun->review_count); ?> değerlendirme">
                <span class="yildiz" aria-hidden="true"><?php echo e(str_repeat('★', (int) round((float) $urun->rating))); ?><span class="yildiz-bos"><?php echo e(str_repeat('★', 5 - (int) round((float) $urun->rating))); ?></span></span>
                <span aria-hidden="true">(<?php echo e($urun->review_count); ?>)</span>
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <p class="urun-fiyat">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->kart_eski_fiyat): ?>
                <del><?php echo e(number_format($urun->kart_eski_fiyat, 2, ',', '.')); ?> ₺</del>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->has_price_range): ?>
                    <?php echo e(number_format((float) $urun->min_price, 2, ',', '.')); ?> – <?php echo e(number_format((float) $urun->max_price, 2, ',', '.')); ?> ₺
                <?php else: ?>
                    <?php echo e(number_format((float) $urun->min_price, 2, ',', '.')); ?> ₺
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </strong>
        </p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->renkler->isNotEmpty()): ?>
            <div class="urun-renkler" aria-label="Renkler: <?php echo e($urun->renkler->pluck('value')->implode(', ')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urun->renkler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $renk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <span class="urun-renk" style="background: <?php echo e($renk->color_hex ?: 'transparent'); ?>"
                          title="<?php echo e($renk->value); ?>"></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urun->total_stock < 1): ?>
            <a href="<?php echo e($urunAdresi); ?>" class="urun-sepet" aria-label="<?php echo e($urun->name); ?> — stoğa girince haber ver">Gelince haber ver</a>
        <?php elseif($tek): ?>
            <form method="POST" action="<?php echo e(route('cart.add')); ?>" class="urun-sepet-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="variant_id" value="<?php echo e($tek->id); ?>">
                <button type="submit" class="urun-sepet" aria-label="<?php echo e($urun->name); ?> — sepete ekle">
                    <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'sepet'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Sepete Ekle
                </button>
            </form>
        <?php else: ?>
            <a href="<?php echo e($urunAdresi); ?>" class="urun-sepet" aria-label="<?php echo e($urun->name); ?> — seçenekleri gör">Seçenekleri gör</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</article>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/parca/urun-kart.blade.php ENDPATH**/ ?>
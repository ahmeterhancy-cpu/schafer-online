<?php $__env->startSection('baslik', 'Sipariş Sorgula — ' . config('shop.ad')); ?>
<?php $__env->startSection('aciklama', 'Sipariş numaranız ve e-posta adresinizle siparişinizi sorgulayın.'); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Sipariş Sorgula'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap kap-dar yasal-sayfa">

    <p style="color: var(--ink-soft); margin-bottom: 28px">
        Sipariş numaranızı ve sipariş sırasında kullandığınız e-posta adresini girin.
        Sipariş numarası onay e-postanızda yazıyor.
    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('hata')): ?>
        <p class="uyari uyari-hata"><?php echo e(session('hata')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('order.lookup')); ?>">
        <?php echo csrf_field(); ?>

        <div class="alan">
            <label for="siparis_no">Sipariş numarası</label>
            <input type="text" id="siparis_no" name="siparis_no"
                   value="<?php echo e(old('siparis_no')); ?>" placeholder="ZEY-260921-0001"
                   class="metin-girdi" required>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['siparis_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="alan">
            <label for="eposta">E-posta</label>
            <input type="email" id="eposta" name="eposta" value="<?php echo e(old('eposta')); ?>"
                   class="metin-girdi" required autocomplete="email">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['eposta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <button type="submit" class="dugme" style="margin-top:10px">Siparişi göster</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/siparis-sorgula.blade.php ENDPATH**/ ?>
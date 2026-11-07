<?php $__env->startSection('baslik', 'Giriş Yap — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Giriş Yap', 'konum' => ['Hesabım' => route('account.index'), 'Giriş Yap' => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap kap-dar yasal-sayfa kutu-sayfa">

    <p style="color: var(--ink-soft); margin-bottom: 28px">
        Hesabınız yoksa <a href="<?php echo e(route('register')); ?>">hesap oluşturabilirsiniz</a>.
        Hesap açmadan da alışveriş yapabilir, siparişinizi
        <a href="<?php echo e(route('order.lookup.form')); ?>">sipariş numarasıyla sorgulayabilirsiniz</a>.
    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('bilgi')): ?>
        <p class="uyari"><?php echo e(session('bilgi')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>

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

        <div class="alan">
            <label for="parola">Parola</label>
            <input type="password" id="parola" name="parola"
                   class="metin-girdi" required autocomplete="current-password">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['parola'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <label class="onay-kutu">
            <input type="checkbox" name="beni_hatirla" value="1">
            <span>Beni hatırla</span>
        </label>

        <button type="submit" class="dugme" style="margin-top:14px">Giriş yap</button>
    </form>

    <p style="margin-top:22px"><a href="<?php echo e(route('password.request')); ?>">Parolamı unuttum</a></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/hesap/giris.blade.php ENDPATH**/ ?>
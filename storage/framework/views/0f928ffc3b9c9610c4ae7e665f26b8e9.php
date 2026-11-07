
<nav class="hesap-menu" aria-label="Hesap menüsü">
    <div class="hesap-menu-kim">
        <strong><?php echo e(auth()->user()->name); ?></strong>
        <span><?php echo e(auth()->user()->email); ?></span>
    </div>
    <a href="<?php echo e(route('account.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['hesap-menu-aktif' => $aktif === 'siparisler']); ?>">
        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'sepet'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Siparişlerim
    </a>
    <a href="<?php echo e(route('account.addresses')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['hesap-menu-aktif' => $aktif === 'adresler']); ?>">
        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'konum'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Adreslerim
    </a>
    <a href="<?php echo e(route('account.data')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['hesap-menu-aktif' => $aktif === 'verilerim']); ?>">
        <?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'kalkan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Verilerim
    </a>
    <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit"><?php echo $__env->make('vitrin.parca.ikon', ['ad' => 'ok-sol'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Çıkış yap</button>
    </form>
</nav>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/parca/hesap-menu.blade.php ENDPATH**/ ?>
<?php $__env->startSection('baslik', 'Verilerim — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Verilerim', 'konum' => ['Hesabım' => route('account.index'), 'Verilerim' => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap hesap-duzen">
    <?php echo $__env->make('vitrin.parca.hesap-menu', ['aktif' => 'verilerim'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="hesap-icerik">


    <section class="bolum bolum-ilk">
        <div class="bolum-basi"><h2>Verilerimi indir</h2></div>

        <p style="color: var(--ink-soft); max-width: var(--measure)">
            KVKK kapsamında hakkınızda tuttuğumuz kişisel verilerin bir kopyasını
            indirebilirsiniz: hesap bilgileri, adresler, siparişler, iade talepleri,
            ürün değerlendirmeleri ve stok bildirim kayıtları.
        </p>

        <a class="dugme dugme-cizgi" href="<?php echo e(route('account.data.export')); ?>">Verilerimi indir (JSON)</a>
    </section>

    <section class="bolum">
        <div class="bolum-basi"><h2>Hesabımı sil</h2></div>

        <div class="uyari">
            <p style="margin:0 0 8px"><strong>Silinecekler:</strong> hesabınız, kayıtlı adresleriniz, ürün değerlendirmeleriniz ve stok bildirim kayıtlarınız.</p>
            <p style="margin:0">
                <strong>Silinmeyecekler:</strong> sipariş ve fatura kayıtları. Vergi mevzuatı
                gereği yasal süre boyunca saklanmak zorundadır; hesabınızdan ayrılır ama
                silinmez. Bu siparişleri sipariş numaranız ve e-postanızla sorgulamaya
                devam edebilirsiniz.
            </p>
        </div>

        <form method="POST" action="<?php echo e(route('account.destroy')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>

            <div class="alan">
                <label for="parola">Onaylamak için parolanız</label>
                <input type="password" id="parola" name="parola" class="metin-girdi"
                       required autocomplete="current-password">
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
                <input type="checkbox" name="onay" value="1" required>
                <span>Hesabımın kalıcı olarak silineceğini anlıyorum.</span>
            </label>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['onay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <button type="submit" class="dugme dugme-sil" style="margin-top:14px">Hesabımı sil</button>
        </form>
    </section>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/hesap/verilerim.blade.php ENDPATH**/ ?>
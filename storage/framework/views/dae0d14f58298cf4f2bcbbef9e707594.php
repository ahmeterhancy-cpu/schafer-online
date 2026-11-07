

<?php $__env->startSection('baslik', 'Adreslerim — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Adreslerim', 'konum' => ['Hesabım' => route('account.index'), 'Adreslerim' => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap hesap-duzen">
    <?php echo $__env->make('vitrin.parca.hesap-menu', ['aktif' => 'adresler'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="hesap-icerik">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('bilgi')): ?>
        <p class="uyari"><?php echo e(session('bilgi')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $adresler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adres): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="adres-karti">
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adres->title): ?>
                    <p class="adres-baslik"><?php echo e($adres->title); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adres->is_default): ?>
                    <span class="etiket">Varsayılan</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <address>
                    <?php echo e($adres->name); ?><br>
                    <?php echo e(trim($adres->line1 . ' ' . $adres->line2)); ?><br>
                    <?php echo e($adres->district); ?> / <?php echo e($adres->city); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adres->postal_code): ?> · <?php echo e($adres->postal_code); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <br><?php echo e($adres->phone); ?>

                </address>
            </div>

            <form method="POST" action="<?php echo e(route('account.address.destroy', $adres)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="metin-dugme metin-dugme-sil">Sil</button>
            </form>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    <section class="bolum">
        <div class="bolum-basi"><h2>Yeni adres</h2></div>

        <form method="POST" action="<?php echo e(route('account.address.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="alan-ikili">
                <div class="alan">
                    <label for="baslik">Başlık (isteğe bağlı)</label>
                    <input type="text" id="baslik" name="baslik" value="<?php echo e(old('baslik')); ?>"
                           placeholder="Ev / İş" class="metin-girdi">
                </div>
                <div class="alan">
                    <label for="ad">Ad Soyad</label>
                    <input type="text" id="ad" name="ad" value="<?php echo e(old('ad', auth()->user()->name)); ?>"
                           class="metin-girdi" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="alan">
                <label for="telefon">Telefon</label>
                <input type="tel" id="telefon" name="telefon" value="<?php echo e(old('telefon')); ?>"
                       class="metin-girdi" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['telefon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="alan">
                <label for="adres">Adres</label>
                <input type="text" id="adres" name="adres" value="<?php echo e(old('adres')); ?>"
                       class="metin-girdi" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['adres'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="alan">
                <label for="adres2">Apartman, daire (isteğe bağlı)</label>
                <input type="text" id="adres2" name="adres2" value="<?php echo e(old('adres2')); ?>" class="metin-girdi">
            </div>

            <div class="alan-uclu">
                <div class="alan">
                    <label for="ilce">Bölge / köy</label>
                    <input type="text" id="ilce" name="ilce" value="<?php echo e(old('ilce')); ?>"
                           class="metin-girdi" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ilce'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="alan">
                    <label for="il">Şehir</label>
                    <input type="text" id="il" name="il" value="<?php echo e(old('il')); ?>"
                           class="metin-girdi" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['il'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="alan">
                    <label for="posta_kodu">Posta kodu (isteğe bağlı)</label>
                    <input type="text" id="posta_kodu" name="posta_kodu" value="<?php echo e(old('posta_kodu')); ?>"
                           class="metin-girdi">
                </div>
            </div>

            <label class="onay-kutu">
                <input type="checkbox" name="varsayilan" value="1">
                <span>Varsayılan adresim olsun</span>
            </label>

            <button type="submit" class="dugme" style="margin-top:12px">Adresi kaydet</button>
        </form>
    </section>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/hesap/adresler.blade.php ENDPATH**/ ?>
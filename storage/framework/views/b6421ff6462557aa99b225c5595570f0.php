<?php $__env->startSection('baslik', 'Ödeme — ' . config('shop.ad')); ?>

<?php $__env->startSection('icerik'); ?>
<?php echo $__env->make('vitrin.parca.sayfa-basi', ['baslik' => 'Ödeme', 'konum' => ['Sepetim' => route('cart.index'), 'Ödeme' => null]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="kap odeme-sayfa">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
        <p class="uyari">
            Hesabınız varsa <a href="<?php echo e(route('login')); ?>">giriş yapın</a>,
            adres bilgileriniz otomatik dolsun. Hesap açmadan da devam edebilirsiniz.
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('hata')): ?>
        <p class="uyari uyari-hata"><?php echo e(session('hata')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sozlesme_onay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="uyari uyari-hata"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('checkout.store')); ?>" class="odeme-duzen">
        <?php echo csrf_field(); ?>

        <div class="odeme-form">
            <fieldset class="form-blok">
                <legend class="etiket">İletişim</legend>

                <div class="alan">
                    <label for="ad">Ad Soyad</label>
                    <input type="text" id="ad" name="ad" value="<?php echo e(old('ad', $kayitliAdres->name ?? auth()->user()->name ?? '')); ?>"
                           class="metin-girdi" required autocomplete="name">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="alan-ikili">
                    <div class="alan">
                        <label for="eposta">E-posta</label>
                        <input type="email" id="eposta" name="eposta" value="<?php echo e(old('eposta', auth()->user()->email ?? '')); ?>"
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
                        <label for="telefon">Telefon</label>
                        <input type="tel" id="telefon" name="telefon" value="<?php echo e(old('telefon', $kayitliAdres->phone ?? '')); ?>"
                               class="metin-girdi" required autocomplete="tel">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['telefon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-blok">
                <legend class="etiket">Teslimat Adresi</legend>

                <div class="alan">
                    <label for="adres">Adres</label>
                    <input type="text" id="adres" name="adres" value="<?php echo e(old('adres', $kayitliAdres->line1 ?? '')); ?>"
                           class="metin-girdi" required autocomplete="address-line1">
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
                    <input type="text" id="adres2" name="adres2" value="<?php echo e(old('adres2', $kayitliAdres->line2 ?? '')); ?>"
                           class="metin-girdi" autocomplete="address-line2">
                </div>

                <div class="alan-uclu">
                    <div class="alan">
                        <label for="ilce">Bölge / köy</label>
                        <input type="text" id="ilce" name="ilce" value="<?php echo e(old('ilce', $kayitliAdres->district ?? '')); ?>"
                               class="metin-girdi" required autocomplete="address-level2">
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
                        <input type="text" id="il" name="il" value="<?php echo e(old('il', $kayitliAdres->city ?? '')); ?>"
                               class="metin-girdi" required autocomplete="address-level1" list="kktc-sehirler">
                        <datalist id="kktc-sehirler">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Lefkoşa', 'Girne', 'Gazimağusa', 'Güzelyurt', 'İskele', 'Lefke']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sehir): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($sehir); ?>"></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </datalist>
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
                        <input type="text" id="posta_kodu" name="posta_kodu" value="<?php echo e(old('posta_kodu', $kayitliAdres->postal_code ?? '')); ?>"
                               class="metin-girdi" autocomplete="postal-code">
                    </div>
                </div>

                <div class="alan">
                    <label for="not">Sipariş notu (isteğe bağlı)</label>
                    <textarea id="not" name="not" rows="3" class="metin-girdi"><?php echo e(old('not')); ?></textarea>
                </div>
            </fieldset>

            <?php
                $faturaTipi = old('fatura_tipi', ($kayitliAdres->invoice_type ?? null) === 'corporate' ? 'kurumsal' : 'bireysel');
                $kayitliKurumsal = ($kayitliAdres->invoice_type ?? null) === 'corporate';
            ?>

            
            <fieldset class="form-blok fatura-blok">
                <legend class="etiket">Fatura</legend>

                <div class="fatura-tipi">
                    <label class="onay-kutu">
                        <input type="radio" name="fatura_tipi" value="bireysel" <?php if($faturaTipi === 'bireysel'): echo 'checked'; endif; ?>>
                        <span>Bireysel</span>
                    </label>
                    <label class="onay-kutu">
                        <input type="radio" name="fatura_tipi" value="kurumsal" <?php if($faturaTipi === 'kurumsal'): echo 'checked'; endif; ?>>
                        <span>Kurumsal (şirket adına)</span>
                    </label>
                </div>

                <div class="bireysel-alanlar">
                    <div class="alan">
                        <label for="tckn">Kimlik no (isteğe bağlı)</label>
                        <input type="text" id="tckn" name="tckn" inputmode="numeric" maxlength="11"
                               value="<?php echo e(old('tckn', ! $kayitliKurumsal ? ($kayitliAdres->tax_number ?? '') : '')); ?>"
                               class="metin-girdi" autocomplete="off">
                        <span class="alan-ipucu">KKTC ya da T.C. kimlik numarası. Faturanızda yer alır; boş bırakabilirsiniz.</span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tckn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="kurumsal-alanlar">
                    <div class="alan">
                        <label for="firma_unvani">Firma unvanı</label>
                        <input type="text" id="firma_unvani" name="firma_unvani"
                               value="<?php echo e(old('firma_unvani', $kayitliAdres->company_name ?? '')); ?>"
                               class="metin-girdi" autocomplete="organization">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['firma_unvani'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="alan-ikili">
                        <div class="alan">
                            <label for="vergi_dairesi">Vergi dairesi</label>
                            <input type="text" id="vergi_dairesi" name="vergi_dairesi"
                                   value="<?php echo e(old('vergi_dairesi', $kayitliAdres->tax_office ?? '')); ?>"
                                   class="metin-girdi">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['vergi_dairesi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="alan">
                            <label for="vkn">Vergi no</label>
                            <input type="text" id="vkn" name="vkn" maxlength="15"
                                   value="<?php echo e(old('vkn', $kayitliKurumsal ? ($kayitliAdres->tax_number ?? '') : '')); ?>"
                                   class="metin-girdi">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['vkn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                <label class="onay-kutu">
                    <input type="checkbox" name="farkli_fatura_adresi" value="1" <?php if(old('farkli_fatura_adresi')): echo 'checked'; endif; ?>>
                    <span>Fatura adresim teslimat adresinden farklı</span>
                </label>

                <div class="fatura-adres-alanlari">
                    <div class="alan">
                        <label for="fatura_adres">Fatura adresi</label>
                        <input type="text" id="fatura_adres" name="fatura_adres" value="<?php echo e(old('fatura_adres')); ?>" class="metin-girdi">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fatura_adres'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="alan-ikili">
                        <div class="alan">
                            <label for="fatura_ilce">Bölge / köy</label>
                            <input type="text" id="fatura_ilce" name="fatura_ilce" value="<?php echo e(old('fatura_ilce')); ?>" class="metin-girdi">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fatura_ilce'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="alan">
                            <label for="fatura_il">Şehir</label>
                            <input type="text" id="fatura_il" name="fatura_il" value="<?php echo e(old('fatura_il')); ?>" class="metin-girdi" list="kktc-sehirler">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fatura_il'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="alan-hata"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <aside class="odeme-ozet">
            <h3>Sipariş Özeti</h3>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $satirlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satir): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="ozet-urun">
                    <span>
                        <?php echo e($satir['product']->name); ?>

                        <em><?php echo e($satir['label']); ?> × <?php echo e($satir['quantity']); ?></em>
                    </span>
                    <span><?php echo e(number_format($satir['line_total'], 2, ',', '.')); ?> TL</span>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            <div class="ozet-satir">
                <span>Ara toplam</span>
                <span><?php echo e(number_format($araToplam, 2, ',', '.')); ?> TL</span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($indirim > 0): ?>
                <div class="ozet-satir ozet-indirim">
                    <span>İndirim</span>
                    <span>-<?php echo e(number_format($indirim, 2, ',', '.')); ?> TL</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ozet-satir">
                <span>Kargo</span>
                <span><?php echo e($kargo > 0 ? number_format($kargo, 2, ',', '.') . ' TL' : 'Ücretsiz'); ?></span>
            </div>

            <div class="ozet-satir ozet-toplam">
                <span>Toplam</span>
                <span><?php echo e(number_format($toplam, 2, ',', '.')); ?> TL</span>
            </div>

            <label class="onay-kutu">
                <input type="checkbox" name="sozlesme_onay" value="1" required>
                <span>
                    <a href="<?php echo e(route('legal', 'on-bilgilendirme')); ?>" target="_blank" rel="noopener">Ön Bilgilendirme Formu</a>'nu
                    ve
                    <a href="<?php echo e(route('legal', 'mesafeli-satis')); ?>" target="_blank" rel="noopener">Mesafeli Satış Sözleşmesi</a>'ni
                    okudum, onaylıyorum.
                </span>
            </label>

            
            <input type="hidden" name="sozlesme_surum" value="<?php echo e($sozlesme?->version); ?>">
            <input type="hidden" name="onbilgi_surum" value="<?php echo e($onBilgi?->version); ?>">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sozlesme): ?>
                <p class="ozet-not">Onayladığınız sürüm: <?php echo e($sozlesme->version); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <button type="submit" class="dugme odeme-dugme">Ödemeye geç</button>

            <p class="ozet-not">
                Kart bilgileriniz ödeme kuruluşunun güvenli sayfasında girilir,
                tarafımızca görülmez ve saklanmaz.
            </p>
        </aside>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.vitrin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/odeme.blade.php ENDPATH**/ ?>
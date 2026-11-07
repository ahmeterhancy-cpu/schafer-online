
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Sipariş <?php echo e($order->number); ?> — <?php echo e(config('shop.ad')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body>
    <main class="odeme-donus">
        <a href="<?php echo e(url('/')); ?>"><img src="<?php echo e(asset('img/schafer/logo.png')); ?>" alt="<?php echo e(config('shop.ad')); ?>" width="110" height="81" style="margin:0 auto 26px"></a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($durum === 'basarili'): ?>
            <h1>Siparişiniz alındı</h1>
            <p>Sipariş numaranız <strong><?php echo e($order->number); ?></strong>.</p>
            <p>Onay e-postası <?php echo e($order->customer_email); ?> adresine gönderildi.</p>
        <?php elseif($durum === 'isleniyor'): ?>
            <h1>Ödemeniz işleniyor</h1>
            <p>
                Sipariş numaranız <strong><?php echo e($order->number); ?></strong>.
                Bankadan onay bekleniyor; bu birkaç saniye sürebilir.
                Sayfayı yenileyerek durumu görebilirsiniz.
            </p>
        <?php else: ?>
            <h1>Ödeme tamamlanamadı</h1>
            <p>
                <strong><?php echo e($order->number); ?></strong> numaralı siparişin ödemesi alınamadı.
                Kartınızdan para çekilmedi.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <p style="margin-top:26px"><a class="dugme" href="<?php echo e(url('/')); ?>">Alışverişe devam et</a></p>
    </main>
</body>
</html>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/odeme/donus.blade.php ENDPATH**/ ?>
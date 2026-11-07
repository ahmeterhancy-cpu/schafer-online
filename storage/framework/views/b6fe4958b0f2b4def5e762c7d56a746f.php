<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?php echo e(config('shop.ad')); ?> — Yakında</title>
    <link rel="icon" href="<?php echo e(asset('img/schafer/favicon.png')); ?>" type="image/png">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body>
    
    <main class="bakim">
        <img src="<?php echo e(asset('img/schafer/logo.png')); ?>" alt="<?php echo e(config('shop.ad')); ?>" width="300" height="221">

        <h1>Çok yakında</h1>

        <p>
            Çevrim içi mağazamızı hazırlıyoruz. Bu sürede Gönyeli'deki mağazamızda
            sizi bekliyoruz.
        </p>

        <address><?php echo e(config('shop.satici.adres')); ?></address>

        <p>
            <a href="https://instagram.com/<?php echo e(config('shop.sosyal.instagram')); ?>" rel="noopener noreferrer">
                &#64;<?php echo e(config('shop.sosyal.instagram')); ?>

            </a>
        </p>
    </main>
</body>
</html>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/vitrin/bakim.blade.php ENDPATH**/ ?>
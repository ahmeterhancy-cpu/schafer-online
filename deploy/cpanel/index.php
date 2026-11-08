<?php

/*
 * public_html/index.php — cPanel yerlesimi icin giris dosyasi.
 *
 * Ana alan adinin kok dizini (public_html) cPanel'de degistirilemiyor.
 * Laravel'in tamami web'e KAPALI olan /home/KULLANICI/schafer_app/
 * klasorunde durur; web kokunde yalniz public/ icerigi ve bu dosya bulunur.
 *
 * .cpanel.yml her deploy'da bu dosyayi public_html/index.php olarak kopyalar.
 * Laravel'in kendi public/index.php dosyasi oldugu gibi duruyor; yerel
 * gelistirme onu kullanmaya devam ediyor.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// public_html'in bir ustu = ana dizin; uygulama onun altinda
$app_base = dirname(__DIR__).'/schafer_app';

// Bakim kipi (php artisan down) — Laravel'in kendi perdesi
if (file_exists($maintenance = $app_base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $app_base.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $app_base.'/bootstrap/app.php';

$app->handleRequest(Request::capture());

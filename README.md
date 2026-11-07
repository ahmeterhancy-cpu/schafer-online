# Schafer Kıbrıs — Online Mağaza

Schafer ürünleri (sofra, mutfak, küçük ev aletleri, ev tekstili) için KKTC
online mağazası ve yönetim paneli. Laravel 13 + Filament 5.

- Durum, kararlar, bekleyenler: [DURUM.md](DURUM.md)
- Sunucuya kurulum: [DEPLOY.md](DEPLOY.md)

## Yerelde çalıştırma

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan db:seed --class=LegalDocumentSeeder
php artisan storage:link
php artisan woo:ice-aktar          # storage/app/woo kaynak dosyalarıyla
php artisan schafer:yonetici       # panel kullanıcısı
php -S 127.0.0.1:8190 -t public server.php
```

Testler: `php artisan test`

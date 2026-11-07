<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(config('shop.ad'))
            ->brandLogo(asset('img/schafer/logo.png'))
            ->brandLogoHeight('2.2rem')
            ->favicon(asset('img/schafer/favicon.png'))
            ->colors([
                /*
                 * TUZAK: Color::hex() KULLANMAYIN — yalnız hue'yu alıp kendi
                 * rampasını kurar, marka kırmızısını pembeye kaydırır.
                 *
                 * Merdiven Schafer logosunun kırmızısı (#EB1C22) çevresinde elle
                 * yazıldı; 600 = vitrindeki --vurgu (#D21C22, beyaz yazıyla 5.2:1).
                 */
                'primary' => [
                    50 => '254, 242, 242',
                    100 => '253, 226, 227',
                    200 => '251, 199, 200',
                    300 => '247, 158, 160',
                    400 => '241, 99, 103',
                    500 => '235, 28, 34',
                    600 => '210, 28, 34',
                    700 => '168, 22, 27',
                    800 => '139, 21, 25',
                    900 => '115, 22, 25',
                    950 => '63, 8, 10',
                ],
            ])
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.cipler-stil'))
            ->navigationGroups([
                'Katalog',
                'Satış',
                'Vitrin',
                'Ayarlar',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

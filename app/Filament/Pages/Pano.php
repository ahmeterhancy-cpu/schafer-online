<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

/**
 * Pano: üstte yapılacak işler (MagazaOzeti), altında satış grafiği,
 * son siparişler + çok satanlar yan yana, en altta kritik stok.
 */
class Pano extends Dashboard
{
    protected static ?string $title = 'Pano';

    protected static ?string $navigationLabel = 'Pano';

    public function getColumns(): int|array
    {
        return ['default' => 1, 'xl' => 3];
    }
}

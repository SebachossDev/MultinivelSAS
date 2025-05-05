<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory; 
use App\Models\User; 

class VendedorInventoryOverview extends BaseWidget
{
    protected static ?int $columns = 2;
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = '3s';

    protected function shouldBeVisible(): bool
    {
        $user = Auth::user();

        return $user && $user->hasRole('Vendedor');
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        $totalQuantity = 0;
        $totalValue = 0;

        $inventoryItems = Inventory::where('user_id', $user->id)->get();

        foreach ($inventoryItems as $item) {
            $totalQuantity += $item->quantity;

            $totalValue += ($item->quantity * $item->price);
        }

        return [
            Stat::make('Cantidad de los productos', $totalQuantity)
                ->description('Unidades totales en tu inventario.')
                ->color('success') 
                ->descriptionIcon('heroicon-o-archive-box'), 

            Stat::make('Valor Total del Inventario', '$' . number_format($totalValue, 0, '.', ',')) 
                ->description('Valor total estimado de tu inventario.')
                ->color('warning') 
                ->descriptionIcon('heroicon-o-currency-dollar'), 
        ];
    }

    // Opcional: Título para el widget (aparece encima de las tarjetas)
    
}
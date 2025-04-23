<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Inventory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InventoryStats extends BaseWidget

{
    protected static ?int $sort = 3;
    protected function getCards(): array
    {
        $totalStock = Product::sum('stock');
        $totalSold = Inventory::sum('quantity');
        $totalInventoryValue = Product::sum(DB::raw('price * stock'));
        $totalSalesValue = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->sum(DB::raw('inventories.quantity * products.price'));

        return [
            Stat::make('Total de Productos en Bodega', $totalStock)
                ->description('Productos disponibles')
                ->color('success'),
            Stat::make('Total de Productos Vendidos', $totalSold)
                ->description('Productos vendidos')
                ->color('danger'),
            Stat::make('Valor Total en Bodega', '$' . number_format($totalInventoryValue, 2))
                ->description('Valor del inventario actual')
                ->color('info'),
            Stat::make('Valor Total Vendido', '$' . number_format($totalSalesValue, 2))
                ->description('Valor total de las ventas')
                ->color('warning'),
        ];
    }
}

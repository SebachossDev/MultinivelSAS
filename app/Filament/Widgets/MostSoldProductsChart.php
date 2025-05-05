<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MostSoldProductsChart extends BarChartWidget
{
    protected static ?string $heading = 'Productos Más Vendidos';
    protected static ?int $sort = 4;
    protected static string $color = 'info';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        // Obtener los productos más vendidos
        $data = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(inventories.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->take(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad Vendida',
                    'data' => $data->pluck('total_sold')->toArray(),
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '3S'; // Actualizar cada 10 segundos
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasRole(['Admin']);
    }
}

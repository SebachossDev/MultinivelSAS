<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlySalesChart extends LineChartWidget
{
    protected static ?string $heading = 'Ventas Mensuales';
    protected static ?int $sort = 4;
    protected static string $color = 'warning'; 
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        // Obtener el total de ventas por mes
        $data = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select(
                DB::raw('DATE_FORMAT(inventories.created_at, "%Y-%m") as month'),
                DB::raw('SUM(inventories.quantity * products.price) as total_sales')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total de Ventas',
                    'data' => $data->pluck('total_sales')->toArray(),
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '3s'; // Actualizar cada 10 segundos
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

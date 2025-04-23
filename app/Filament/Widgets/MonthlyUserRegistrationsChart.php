<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MonthlyUserRegistrationsChart extends LineChartWidget
{
    protected static ?string $heading = 'Usuarios Registrados por Mes';
    protected static ?int $sort = 2;
    protected static string $color = 'danger';

    protected function getData(): array
    {
        // Obtener el número de usuarios registrados por mes
        $data = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_users')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Usuarios Registrados',
                    'data' => $data->pluck('total_users')->toArray(),
                    'borderColor' => '#FF5733', // Color de la línea
                    'backgroundColor' => 'rgba(255, 87, 51, 0.2)', // Color de fondo
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '10s'; // Actualizar cada 10 segundos
    }
}

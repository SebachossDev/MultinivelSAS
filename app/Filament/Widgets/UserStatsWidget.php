<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getCards(): array
    {
        $totalUsers = User::count();

        return [
            Stat::make('Usuarios Totales', $totalUsers)
                ->description('Usuarios registrados en el sistema')
                ->color('primary'),
        ];
    }
}

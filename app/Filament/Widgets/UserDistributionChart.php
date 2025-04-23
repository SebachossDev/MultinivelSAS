<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\User;
use App\Models\Department;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class UserDistributionChart extends BarChartWidget
{
    protected static ?string $heading = 'Ubicaciónes con el mayor número de usuarios';
    protected static ?int $sort = 2;

    public ?string $filter = 'departments';

    protected function getFilters(): array
    {
        return [
            'departments' => 'Departamentos',
            'cities' => 'Ciudades',
        ];
    }

    protected function getData(): array
    {
        if ($this->filter === 'departments') {
            // Obtener los 10 departamentos con más usuarios
            $data = User::join('states', 'users.department_id', '=', 'states.id')
                ->select('states.name as name', DB::raw('COUNT(users.id) as total_users'))
                ->groupBy('states.name')
                ->orderByDesc('total_users')
                ->take(10)
                ->get();
        } else {
            // Obtener las 10 ciudades con más usuarios
            $data = User::join('cities', 'users.city_id', '=', 'cities.id')
                ->select('cities.name as name', DB::raw('COUNT(users.id) as total_users'))
                ->groupBy('cities.name')
                ->orderByDesc('total_users')
                ->take(10)
                ->get();
        }

        return [
            'datasets' => [
                [
                    'label' => $this->filter === 'departments' ? 'Usuarios por Departamento' : 'Usuarios por Ciudad',
                    'data' => $data->pluck('total_users')->toArray(),
                    'backgroundColor' => $this->filter === 'departments' ? 'rgba(76, 175, 80, 0.5)' : 'rgba(33, 150, 243, 0.5)',
                    'borderColor' => $this->filter === 'departments' ? 'rgba(56, 142, 60, 1)' : 'rgba(25, 118, 210, 1)',
                    'borderWidth' => 2, 
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

}

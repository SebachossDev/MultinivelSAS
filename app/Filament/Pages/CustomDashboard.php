<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class CustomDashboard extends BaseDashboard
{
  

    public function getTitle(): string
    {
        $user = Auth::user();

        if ($user) {
            return 'Bienvenido ' . $user->name;
        }

        return 'Dashboard';
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user(); // Obtenemos el usuario autenticado

        // Verificamos que el usuario esté autenticado
        if ($user) {
            // Usamos los métodos de Spatie para verificar los roles
            if ($user->hasAnyRole(['Admin', 'Promotor'])) {
                return 'Este es tu panel de gestión principal. Aquí encontrarás todas las herramientas y estadísticas.';
            } elseif ($user->hasRole('Vendedor')) {
                return 'Espero que estes bien, gracias por tu trabajo y gracias por ser parte del equipo de MultiSAS.';
            } else {
                // Opcional: Un mensaje por defecto para usuarios sin estos roles específicos
                return 'Bienvenido a tu Dashboard. Explora las secciones disponibles.';
            }
        }

        // Si no hay usuario autenticado o no se cumple ninguna condición, no mostramos subtítulo
        return null;
    }

    

}
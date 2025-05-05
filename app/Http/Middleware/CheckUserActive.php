<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\Logins; 

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->active) {
            Auth::logout();

            Notification::make()
                ->title('Acceso denegado')
                ->body('Tu cuenta no está activa.')
                ->danger()
                ->send();

                return redirect(Filament::getLoginUrl()); // Redirige al login de Filament
        }

        return $next($request);
    }
}
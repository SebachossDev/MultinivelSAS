<?php

namespace App\Livewire;

use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException as ExceptionsTooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\RegistrationResponse;
use Illuminate\Http\Exceptions\TooManyRequestsException;
use Illuminate\Database\Eloquent\Model;

class CustomRegister extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (ExceptionsTooManyRequestsException $exception) {
            if (method_exists($this, 'getRateLimitedNotification')) {
                $this->getRateLimitedNotification($exception)?->send();
            } else {
                if (class_exists(Notification::class)) {
                    Notification::make()
                        ->title('Demasiadas peticiones')
                        ->body('Has realizado demasiadas peticiones. Inténtalo de nuevo en unos momentos.')
                        ->danger()
                        ->send();
                }
            }
            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');
            $data = $this->form->getState();
            $this->callHook('afterValidate');

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            // Asignar el rol 'Vendedor'
            $roleName = 'Vendedor';
            $vendedorRole = Role::where('name', $roleName)->first();

            if ($vendedorRole) {
                try {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $vendedorRole->id,
                        'model_type' => $user->getMorphClass(),
                        'model_id' => $user->id,
                    ]);
                } catch (\Exception $e) {
                    throw $e;
                }
            }

            // Asignar el nivel 'Iniciante'
            $user->level = 'Iniciante';

            // Establecer la columna 'active' a 1
            $user->active = 1;

            // Guardar los cambios en el modelo de usuario
            try {
                $user->save();
            } catch (\Exception $e) {
                throw $e;
            }

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));
        $this->sendEmailVerificationNotification($user);
        Filament::auth()->login($user);
        session()->regenerate();

        return app(RegistrationResponse::class);
    }
}

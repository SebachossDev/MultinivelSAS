<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements HasAvatar
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';


    protected $fillable = [
        'name',
        'email',
        'password',
        'number_cellphone',
        'number_phone',
        'neighborhood',
        'city',
        'address',
        'level',
        'active',
        'custom_fields',
        'avatar_url',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'custom_fields' => 'array'
        ];
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->wherePivot('model_type', 'App\Models\User');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
        return $this->$avatarColumn ? \Illuminate\Support\Facades\Storage::url($this->$avatarColumn) : null;
        Log::info('URL del avatar:', ['url' => $this->getFilamentAvatarUrl()]);
    }

    public function updateProfileFields(array $fields): void
    {
        $customFields = $this->custom_fields ?? []; // Obtén los datos existentes en custom_fields

        foreach ($fields as $key => $value) {
            // Actualiza los campos personalizados
            $customFields[$key] = $value;

            // Si el campo coincide con una columna de la tabla `users`, actualízalo
            if (in_array($key, $this->fillable)) {
                $this->$key = $value;
            }
        }

        $this->custom_fields = $customFields; // Actualiza la columna custom_fields
        $this->save(); // Guarda los cambios en la base de datos
    }

    protected static function booted()
    {
        static::saving(function ($user) {
            if ($user->custom_fields) {
                foreach ($user->custom_fields as $key => $value) {
                    if (in_array($key, $user->fillable)) {
                        $user->$key = $value;
                    }
                }
            }
        });
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }
}

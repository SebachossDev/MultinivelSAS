<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Form;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Joaopaulolndev\FilamentEditProfile\Livewire\BaseProfileForm;

class DepartmentCityProfileComponent extends BaseProfileForm
{
    public array $cities = [];
    protected string $view = 'livewire.department-city-profile-component';
    protected static int $sort = 50; 

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('department_id')
                    ->label('Departamento')
                    ->options(State::pluck('name', 'id')->toArray())
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Cargar ciudades cuando se seleccione un departamento
                        $set('city_id', null);
                        $this->cities = City::where('state_id', $state)->pluck('name', 'id')->toArray();
                    }),

                Forms\Components\Select::make('city_id')
                    ->label('Ciudad')
                    ->options(fn () => $this->cities ?? [])
                    ->required(),
            ]);
    }

    public function save()
    {
        $data = $this->form->getState();

        // Guardar los datos en el modelo del usuario
        Auth::user()->update([
            'department_id' => $data['department_id'],
            'city_id' => $data['city_id'],
        ]);

        $this->notify('success', 'Perfil actualizado correctamente.');
    }
}
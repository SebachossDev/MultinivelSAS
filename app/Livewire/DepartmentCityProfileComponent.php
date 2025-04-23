<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Form;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Livewire\Component;

class DepartmentCityProfileComponent extends Component implements Forms\Contracts\HasForms
{

    use InteractsWithForms;
    use HasSort;

    public array $cities = [];
    public array $data = [];

    protected static int $sort = 50;

    public Model $email;

    public function mount(): void
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        // Cargar los datos iniciales del usuario
        $this->form->fill([
            'department_id' => $user->department_id,
            'city_id' => $user->city_id,
            'neighborhood' => $user->neighborhood,
            'address' => $user->address,
        ]);

        // Cargar las ciudades correspondientes al departamento del usuario
        if ($user->department_id) {
            $this->cities = City::where('state_id', $user->department_id)->pluck('name', 'id')->toArray();
        }
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos de ubicación')
                    ->aside()
                    ->description('Actualiza tu información de ubicación')
                    ->schema([
                        Select::make('department_id')
                            ->label('Departamento')
                            ->searchable()
                            ->options(function () {
                                $colombia = \Altwaireb\World\Models\Country::where('name', 'Colombia')->first(); // Busca por nombre
                                return \Altwaireb\World\Models\State::where('country_id', $colombia->id)->pluck('name', 'id');
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('city_id', null); // Reiniciar la ciudad seleccionada
                                $this->cities = City::where('state_id', $state)->pluck('name', 'id')->toArray(); // Cargar las ciudades
                            }),
                        Select::make('city_id')
                            ->searchable()
                            ->label('Ciudad')
                            ->options(fn() => $this->cities) // Usar las ciudades cargadas dinámicamente
                            ->required(),
                        TextInput::make('neighborhood')
                            ->label('Barrio')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->required()
                            ->columnSpan(1),
                    ])
            ])
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        // Guardar los datos en el modelo del usuario
        Auth::user()->update([
            'department_id' => $data['department_id'],
            'city_id' => $data['city_id'],
            'neighborhood' => $data['neighborhood'],
            'address' => $data['address'],
        ]);

        
        Notification::make()
            ->title('Datos actualizados')
            ->body('Los datos de ubicación han sido actualizados exitosamente.')
            ->success()
            ->send();
    }
}

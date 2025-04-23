<?php

namespace App\Filament\Resources;

use Altwaireb\World\Models\City as ModelsCity;
use Altwaireb\World\Models\State as ModelsState;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use World\Countries\Models\State;
use World\Countries\Models\City;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Gestión de Usuarios';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Nombre')->required(),
                    TextInput::make('email')->label('Correo')->email()->required(),
                    TextInput::make('number_cellphone')->label('Celular')->required(),
                    TextInput::make('number_phone')->label('Teléfono')->nullable(),
                    TextInput::make('neighborhood')->label('Barrio')->nullable(),

                    TextInput::make('address')->label('Dirección')->required(),
                    TextInput::make('level')->label('Nivel')->nullable(),
                    Toggle::make('active')->label('Activo')->default(false),
                    TextInput::make('password')->label('Contraseña')->password()->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Correo')->searchable(),
                TextColumn::make('number_cellphone')->label('Celular')->searchable(),
                TextColumn::make('number_phone')->label('Teléfono')->searchable(),
                TextColumn::make('neighborhood')->label('Barrio')->searchable(),
                TextColumn::make('department.name')->label('Departamento')->searchable(),
                TextColumn::make('city.name')->label('Ciudad')->searchable(),
                TextColumn::make('address')->label('Dirección')->searchable(),
                TextColumn::make('level')->label('Nivel')->searchable()->badge()
                    ->colors([
                        'info' => 'Iniciante',
                        'warning' => 'Aprendiz',
                        'danger' => 'Intermedio',
                        'gray' => 'Avanzado',
                        'primary' => 'Experto',
                    ]),
                TextColumn::make('active')
                    ->label('Activo')
                    ->formatStateUsing(fn($state) => $state ? 'Activo' : 'Inactivo')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger'),
                TextColumn::make('created_at')->label('Fecha Creado')->dateTime(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('active')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activos',
                        '0' => 'Inactivos',
                    ])
                    ->default('1'),
                Tables\Filters\SelectFilter::make('level')
                    ->label('Nivel')
                    ->options([
                        'Iniciante' => 'Iniciante',
                        'Aprendiz' => 'Aprendiz',
                        'Intermedio' => 'Intermedio',
                        'Avanzado' => 'Avanzado',
                        'Experto' => 'Experto',
                    ])
                    ->placeholder('Todos los niveles'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar Usuario')
                    ->modalWidth('2xl')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nombre')->required(),
                            TextInput::make('email')->label('Correo')->email()->required(),
                            TextInput::make('number_cellphone')->label('Celular')->required(),
                            TextInput::make('number_phone')->label('Teléfono')->nullable(),
                            TextInput::make('neighborhood')->label('Barrio')->nullable(),
                            TextInput::make('address')->label('Dirección')->required(),
                            Select::make('department_id')
                                ->label('Departamento')
                                ->options(function () {
                                    $colombia = \Altwaireb\World\Models\Country::where('name', 'Colombia')->first(); // Busca por nombre
                                    return \Altwaireb\World\Models\State::where('country_id', $colombia->id)->pluck('name', 'id');
                                })
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set) => $set('city_id', null)),
                            Select::make('city_id')
                                ->label('Ciudad')
                                ->options(fn(callable $get) => \Altwaireb\World\Models\City::where('state_id', $get('department_id'))->pluck('name', 'id')),
                            Select::make('level')
                                ->label('Nivel')
                                ->options([
                                    'Iniciante' => 'Iniciante',
                                    'Aprendiz' => 'Aprendiz',
                                    'Intermedio' => 'Intermedio',
                                    'Avanzado' => 'Avanzado',
                                    'Experto' => 'Experto',
                                ])
                                ->placeholder('Seleccione un nivel')
                                ->required()
                                ->default(fn($record) => $record?->level)
                                ->afterStateHydrated(function (callable $set, $record) {
                                    if ($record) {
                                        $set('level', $record->level);
                                    }
                                }),
                            Toggle::make('active')->label('Activo')->default(false)->onColor('success')->offColor('danger'),
                            Select::make('roles')
                                ->multiple()
                                ->options(fn() => Role::pluck('name', 'id')->toArray())
                                ->label('Roles')
                                ->searchable()
                                ->preload()
                                ->default(fn($record) => $record?->roles->pluck('id')->toArray() ?? [])
                                ->afterStateHydrated(function (callable $set, $record) {
                                    if ($record) {
                                        $set('roles', $record->roles->pluck('id')->toArray());
                                    }
                                })
                                ->dehydrated(fn($state) => filled($state))
                                ->saveRelationshipsUsing(function ($state, $record) {
                                    $record->roles()->syncWithPivotValues($state, ['model_type' => User::class]);
                                })
                                ->placeholder('Seleccione un rol'),
                            TextInput::make('password')
                                ->label('Contraseña')
                                ->password()
                                ->dehydrated(fn($state) => !empty($state))
                                ->placeholder('Dejar en blanco si no hay cambios'),
                        ]),
                    ])
                    ->action(function (array $data, $record) {
                        $updateData = [
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'number_cellphone' => $data['number_cellphone'],
                            'number_phone' => $data['number_phone'],
                            'neighborhood' => $data['neighborhood'],
                            'address' => $data['address'],
                            'department_id' => $data['department_id'],
                            'city_id' => $data['city_id'],
                            'level' => $data['level'],
                            'active' => $data['active'],
                        ];

                        if (!empty($data['password'])) {
                            $updateData['password'] = bcrypt($data['password']);
                        }

                        $record->update($updateData);

                        if (isset($data['roles'])) {
                            $record->roles()->sync($data['roles']);
                        }

                        Notification::make()
                            ->title('Usuario Modificado')
                            ->body('El usuario ha sido modificado exitosamente.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->icon('heroicon-o-trash'),
                ExportBulkAction::make('Exportar')->visible(fn() => \Illuminate\Support\Facades\Auth::user()->can(['Admin'])),
                Tables\Actions\BulkAction::make('toggleActive')
                    ->label('Activar/Desactivar Usuarios')
                    ->icon('')
                    ->form([
                        Toggle::make('active')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            $record->update(['active' => $data['active']]);
                        }

                        Notification::make()
                            ->title('Usuarios Actualizados')
                            ->body('El estado de los usuarios seleccionados ha sido actualizado.')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Añadir Usuario')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->modalHeading('Agregar Nuevo Usuario')
                    ->modalWidth('2xl')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nombre')->required()->columnSpan(1),
                            TextInput::make('email')->label('Correo')->email()->required()->columnSpan(1),
                            TextInput::make('password')->label('Contraseña')->password()->required()->columnSpan(1),
                            TextInput::make('address')->label('Dirección')->required()->columnSpan(1),
                            TextInput::make('neighborhood')->label('Barrio')->required()->columnSpan(1),
                            Select::make('department_id')
                                ->label('Departamento')
                                ->required()
                                ->searchable()
                                ->options(function () {
                                    $colombia = \Altwaireb\World\Models\Country::where('name', 'Colombia')->first(); // Busca por nombre
                                    return \Altwaireb\World\Models\State::where('country_id', $colombia->id)->pluck('name', 'id');
                                })
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set) => $set('city_id', null)),
                            Select::make('city_id')
                                ->label('Ciudad')
                                ->required()
                                ->helperText('Seleccione el departamento primero')
                                ->searchable()
                                ->options(fn(callable $get) => \Altwaireb\World\Models\City::where('state_id', $get('department_id'))->pluck('name', 'id')),
                            Select::make('roles')
                                ->multiple()
                                ->options(fn() => Role::pluck('name', 'id')->toArray())
                                ->label('Roles')
                                ->searchable()
                                ->preload()
                                ->afterStateHydrated(function ($state, $record) {
                                    if ($record) {
                                        $record->load('roles'); // Carga la relación si no está cargada
                                        $state = $record->roles->pluck('id')->toArray();
                                    }
                                })
                                ->dehydrated(fn($state) => filled($state))
                                ->saveRelationshipsUsing(function ($state, $record) {
                                    $record->roles()->syncWithPivotValues($state, ['model_type' => User::class]);
                                })
                                ->placeholder('Seleccione un rol')
                                ->default(fn($record) => $record?->roles->pluck('id')->toArray() ?? []),
                            TextInput::make('number_phone')->label('Teléfono')->numeric()->nullable(),
                            TextInput::make('number_cellphone')->label('Celular')->numeric()->required(),
                            Select::make('level')
                                ->label('Nivel')
                                ->options([
                                    'Iniciante' => 'Iniciante',
                                    'Aprendiz' => 'Aprendiz',
                                    'Intermedio' => 'Intermedio',
                                    'Avanzado' => 'Avanzado',
                                    'Experto' => 'Experto',
                                ])
                                ->placeholder('Seleccione un nivel')
                                ->required(),
                            Toggle::make('active')->label('Activo')->required()
                                ->dehydrated(true)
                                ->onColor('success')
                                ->offColor('danger')
                                ->afterStateHydrated(fn($state, $record) => $state = $record ? (bool) $record->active : false)
                                ->mutateDehydratedStateUsing(fn($state) => $state ? 1 : 0),
                        ]),
                    ])
                    ->action(function (array $data) {
                        $user = User::create([
                            'email' => $data['email'],
                            'name' => $data['name'],
                            'neighborhood' => $data['neighborhood'],
                            'number_phone' => $data['number_phone'],
                            'number_cellphone' => $data['number_cellphone'],
                            'address' => $data['address'],
                            'department_id' => $data['department_id'],
                            'city_id' => $data['city_id'],
                            'level' => $data['level'],
                            'active' => $data['active'],
                            'password' => bcrypt($data['password']),
                        ]);

                        // Asignar roles al usuario después de crearlo
                        if (isset($data['roles']) && !empty($data['roles'])) {
                            $user->roles()->sync($data['roles']);
                        }

                        Notification::make()
                            ->title('Usuario Creado')
                            ->body('El usuario ha sido creado exitosamente.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}

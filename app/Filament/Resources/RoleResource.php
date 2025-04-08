<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers\RolePermissionsRelationManager;
use App\Filament\Resources\RoleResource\RelationManagers\RoleUsersRelationManager;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Notifications\Notification;

use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    public static function canCreate(): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\select::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->required()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->heading('Gestión de Roles')
            ->columns([
                TextColumn::make('name')->label('Rol')->sortable(),
                TextColumn::make('permissions.name')
                    ->label('Permisos')
                    ->badge()
                    ->separator(', ')
                    ->toggleable('false'),

            ])
            ->actions([
                Action::make('gestionar')
                    ->label('Gestionar')
                    ->modalHeading('Administrar Usuarios en el Rol')
                    ->form(fn(Role $record) => [
                        Tabs::make('Roles y Permisos')
                            ->tabs([
                                Tabs\Tab::make('Usuarios Asignados')
                                    ->schema([
                                        CheckboxList::make('users')
                                            ->label('Seleccionar Usuarios')
                                            ->relationship('users', 'id')
                                            ->options(User::where('active', true)->pluck('name', 'id'))
                                            ->default($record->users()->pluck('users.id')->toArray())
                                            ->columns(3),
                                    ]),
                                Tabs\Tab::make('Roles Asignados')
                                    ->schema([
                                        CheckboxList::make('permissions')
                                            ->label('Permisos Asociados')
                                            ->relationship('permissions', 'id')
                                            ->options(Permission::pluck('name', 'id'))
                                            ->default($record->permissions()->pluck('id')->toArray())
                                            ->columns(2),
                                    ])
                            ]),
                    ])
                    ->modalWidth('3xl')
                    ->modalSubmitActionLabel('Guardar')
                    ->action(function (array $data, Role $record) {
                        if (isset($data['users'])) {
                            $record->users()->syncWithoutDetaching($data['users']); 
                        }

                        Notification::make()
                            ->title('Usuarios Actualizados')
                            ->body('Los usuarios han sido asignados correctamente.')
                            ->success()
                            ->send();
                    }),
                Action::make('editar')
                    ->label('Editar')
                    ->color('warning')
                    ->modalHeading('Editar Rol')
                    ->modalWidth('2xl')
                    ->form(fn(Role $record) => [
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nombre del Rol')
                                ->default($record->name)
                                ->required(),

                            select::make('guard_name')
                                ->options([
                                    'web' => 'Web',
                                    'api' => 'API',
                                ])
                                ->required()
                        ]),
                    ])
                    ->modalSubmitActionLabel('Actualizar')
                    ->action(function (array $data, Role $record) {
                        $record->update([
                            'name' => $data['name'],
                            'ward' => $data['ward'],
                        ]);

                        Notification::make()
                            ->title('Rol Actualizado')
                            ->body('El rol ha sido actualizado correctamente.')
                            ->success()
                            ->send();
                    }),

            ])
            ->bulkActions([
                DeleteBulkAction::make()->icon('heroicon-o-trash'),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Crear Rol')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->modalHeading('Crear Nuevo Rol')
                    ->modalWidth('2xl')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nombre del Rol')->required(),
                            Select::make('permissions')
                                ->relationship('permissions', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->label('Permisos'),
                        ]),
                    ])
                    ->action(function (array $data) {
                        $role = Role::create([
                            'name' => $data['name'],
                            'guard_name' => 'web',
                        ]);

                        if (isset($data['permissions'])) {
                            $role->syncPermissions($data['permissions']);
                        }

                        Notification::make()
                            ->title('Rol Creado')
                            ->body('El rol ha sido creado exitosamente.')
                            ->success()
                            ->send();
                    })
                    ->modalSubmitActionLabel('Guardar Rol')->color('success')
                    //->visible(fn () => \Illuminate\Support\Facades\Auth::user()->can('crear')),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            RolePermissionsRelationManager::class,
            RoleUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
        ];
    }
}

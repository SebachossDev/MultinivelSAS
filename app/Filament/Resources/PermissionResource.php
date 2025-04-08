<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-o-lock-open';
    protected static ?string $navigationLabel = 'Permisos';
    protected static ?int $navigationSort = 3;
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
                    ->label('Nombre del Permiso')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('guard_name')
                    ->label('Guard')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Permiso')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable(),
            ])
            ->actions([
                Action::make('editar')
                    ->label('Editar')
                    ->color('warning')
                    ->modalHeading('Editar Permiso')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('2xl')
                    ->form(fn(Permission $record) => [
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Permiso')
                            ->default($record->name)
                            ->required(),
                        Forms\Components\Select::make('guard_name')
                            ->options([
                                'web' => 'Web',
                                'api' => 'API',
                            ])
                            ->default($record->guard_name)
                            ->required(),
                    ])
                    ->modalSubmitActionLabel('Actualizar')
                    ->action(function (array $data, Permission $record) {
                        $record->update($data);
                        Notification::make()
                            ->title('Permiso Actualizado')
                            ->body('El permiso ha sido actualizado correctamente.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->icon('heroicon-o-trash')
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Crear Permiso')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->modalHeading('Crear Nuevo Permiso')
                    ->modalWidth('2xl')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Permiso')
                            ->required(),
                        Forms\Components\Select::make('guard_name')
                            ->options([
                                'web' => 'Web',
                                'api' => 'API',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        Permission::create($data);
                        Notification::make()
                            ->title('Permiso Creado')
                            ->body('El permiso ha sido creado exitosamente.')
                            ->success()
                            ->send();
                    })
                    ->modalSubmitActionLabel('Guardar Permiso')
                    ->color('success'),
                    //->visible(fn () => Auth::user()->can('crear-permiso')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
        ];
    }
}

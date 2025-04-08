<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class RolePermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('permission_id')
                ->relationship('permissions', 'name')
                ->searchable()
                ->preload()
                ->label('Asignar Permiso'),
        ]);
    }
}

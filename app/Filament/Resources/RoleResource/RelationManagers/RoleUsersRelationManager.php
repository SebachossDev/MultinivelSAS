<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class RoleUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->relationship('users', 'name') 
                ->searchable()
                ->preload()
                ->label('Asignar Usuario'),
        ]);
    }
}

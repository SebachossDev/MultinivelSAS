<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
    protected static ?string $title = 'Lista de Productos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Crear Producto')
            ->icon('heroicon-o-plus')
            ->visible(fn() => \Illuminate\Support\Facades\Auth::user()->canAny(['Admin']) ?? false),

        ];
    }
}

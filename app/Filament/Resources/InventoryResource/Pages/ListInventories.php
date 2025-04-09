<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use App\Models\Inventory;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;
    protected static ?string $title = 'Mi Inventario';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Inventory::query()->where('user_id', Auth::user()->id);
    }
}

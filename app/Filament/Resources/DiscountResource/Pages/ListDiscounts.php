<?php

namespace App\Filament\Resources\DiscountResource\Pages;

use App\Filament\Resources\DiscountResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;


class ListDiscounts extends ListRecords
{
    protected static string $resource = DiscountResource::class;
    protected static ?string $title = 'Descuentos de Productos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }

}

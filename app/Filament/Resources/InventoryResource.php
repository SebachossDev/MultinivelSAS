<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-m-square-3-stack-3d';
    protected static ?string $navigationLabel = 'Mi Inventario';
    protected static ?int $navigationSort = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->disabled(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Inventory::query()->when(
                    !Filament::auth()->user()?->hasRole('Admin'), // Si el usuario no es administrador
                    fn($query) => $query->where('user_id', Filament::auth()->user()?->id) // Filtra por el ID del agente
                )
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Producto')->searchable(),
                Tables\Columns\TextColumn::make('product.price')->label('Precio')->money('COP'),
                Tables\Columns\TextColumn::make('quantity')->label('Cantidad'),
                Tables\Columns\ImageColumn::make('product.image')->label('Imagen')->disk('public')->size(100),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([
                ExportBulkAction::make('Exportar')->visible(fn() => \Illuminate\Support\Facades\Auth::user()->can(['Admin'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasAnyRole(['Vendedor', 'Admin']);
    }
}

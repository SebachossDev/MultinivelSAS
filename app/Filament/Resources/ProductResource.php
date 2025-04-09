<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?int $navigationSort = 1; 

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción'),
                Forms\Components\TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('Imagen')
                    ->image()
                    ->directory('products')
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Precio')->money('COP'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->formatStateUsing(function ($state) {
                        return $state > 0 ? $state : 'Fuera de Stock';
                    })
                    ->color(function ($state) {
                        return $state > 0 ? 'success' : 'danger';
                    }),
                Tables\Columns\ImageColumn::make('image')
                ->label('Imagen')
                ->disk('public')
                ->size(100),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn() => \Illuminate\Support\Facades\Auth::user()->canAny(['Admin']) ?? false),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                ->visible(fn() => \Illuminate\Support\Facades\Auth::user()->canAny(['Admin']) ?? false),
                Tables\Actions\BulkAction::make('separateProducts')
                    ->label('Separar Productos')
                    ->icon('heroicon-o-shopping-cart')
                    ->action(function (Collection $records, array $data) { 
                        $cart = [];
                        $total = 0;

                        $userId = \Illuminate\Support\Facades\Auth::id(); // ID del usuario autenticado
                        $userLevel = \Illuminate\Support\Facades\Auth::user()->level;

                        $discount = \App\Models\Discount::where('level', $userLevel)->value('discount') ?? 0;

                        foreach ($records as $record) { // Itera directamente sobre la colección
                            $quantity = collect($data['quantities'])->firstWhere('product_id', $record->id)['quantity'] ?? 0;                            if ($quantity > 0) {
                                if ($record->stock >= $quantity) {
                                    $record->decrement('stock', $quantity); // Reducir el stock
                                } else {
                                    Notification::make()
                                        ->title('Error')
                                        ->body("El producto '{$record->name}' no tiene suficiente stock.")
                                        ->danger()
                                        ->send();
                                    continue;
                                }

                                $subtotal = $quantity * $record->price;
                                $discountedSubtotal = $subtotal - ($subtotal * ($discount / 100));

                                // Registrar el producto en la tabla inventories
                                \App\Models\Inventory::updateOrCreate(
                                    [
                                        'user_id' => $userId,
                                        'product_id' => $record->id,
                                    ],
                                    [
                                        'quantity' => DB::raw("quantity + $quantity"), // Incrementar la cantidad si ya existe
                                    ]
                                );

                                $cart[] = [
                                    'product' => $record->name,
                                    'quantity' => $quantity,
                                    'price' => $record->price,
                                    'subtotal' => $discountedSubtotal,
                                ];
                                $total += $discountedSubtotal;
                            }
                        }

                        Notification::make()
                            ->title('Productos Separados')
                            ->body("Total con descuento: $" . number_format($total, 2))
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Repeater::make('quantities')
                            ->label('Productos a Separar')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Producto')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->price);
                                        }
                                    }),
                                Forms\Components\TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $quantity = $get('quantity') ?? 0;
                                        $price = $get('price') ?? 0;

                                        $userLevel = \Illuminate\Support\Facades\Auth::user()->level;
                                        $discount = \App\Models\Discount::where('level', $userLevel)->value('discount') ?? 0;

                                        $subtotal = $quantity * $price;
                                        $discountedSubtotal = $subtotal - ($subtotal * ($discount / 100));

                                        $set('subtotal', $discountedSubtotal);
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal con Descuento')
                                    ->numeric()
                                    ->disabled(),
                            ])
                        
                            ->columns(4)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $userLevel = \Illuminate\Support\Facades\Auth::user()->level;
                                $discount = \App\Models\Discount::where('level', $userLevel)->value('discount') ?? 0;

                                $total = collect($state ?? [])
                                    ->sum(fn($item) => ($item['quantity'] ?? 0) * ($item['price'] ?? 0));

                                $totalWithDiscount = $total - ($total * ($discount / 100));

                                $set('total', $total);
                                $set('total_with_discount', $totalWithDiscount);
                            }),

                        Forms\Components\TextInput::make('total')
                            ->label('Total (sin descuento)')
                            ->numeric()
                            ->disabled()
                            ->reactive(),

                        Forms\Components\TextInput::make('total_with_discount')
                            ->label('Total con Descuento')
                            ->numeric()
                            ->disabled()
                            ->reactive(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
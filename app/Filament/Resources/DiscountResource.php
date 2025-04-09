<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Descuentos';
    public static function canCreate(): bool{return false;}
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('level')
                ->label('Nivel')
                ->options([
                    'Iniciante' => 'Iniciante',
                    'Aprendiz' => 'Aprendiz',
                    'Intermedio' => 'Intermedio',
                    'Avanzado' => 'Avanzado',
                    'Experto' => 'Experto',
                ])
                ->required(),
                Forms\Components\TextInput::make('discount')
                    ->label('Descuento (%)')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level')->label('Nivel'),
                Tables\Columns\TextColumn::make('discount')->label('Descuento (%)'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar Descuento')
                    ->modalWidth('md'),
            ])
            ->headerActions([
                Action::make('crearDescuento')
                    ->label('Aplicar Nuevo Descuento')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Crear Nuevo Descuento')
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Select::make('level')
                            ->label('Nivel')
                            ->options([
                                'Iniciante' => 'Iniciante',
                                'Aprendiz' => 'Aprendiz',
                                'Intermedio' => 'Intermedio',
                                'Avanzado' => 'Avanzado',
                                'Experto' => 'Experto',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('discount')
                            ->label('Descuento (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->action(function (array $data) {
                        // Guardar los datos manualmente
                        Discount::create($data);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
        ];
    }
}

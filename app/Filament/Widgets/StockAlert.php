<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StockAlert extends BaseWidget
{
    protected static ?string $heading = 'Alertas de Estoque Baixo';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereColumn('stock', '<=', 'min_stock_alert')
                    ->where('is_active', true)
                    ->orderBy('stock')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('')
                    ->getStateUsing(fn($record) => $record->images[0] ?? null)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')->label('Produto')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Categoria'),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->placeholder('-'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Estoque Atual')
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('min_stock_alert')
                    ->label('Mínimo'),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends BaseWidget
{
    protected static ?string $heading = 'Pedidos Recentes';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('Nº Pedido'),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => ['pending', 'awaiting_payment'],
                        'info'    => ['paid', 'processing'],
                        'success' => ['shipped', 'delivered'],
                        'danger'  => ['cancelled', 'refunded'],
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => 'Aguardando', 'awaiting_payment' => 'Ag. Pgto',
                        'paid' => 'Pago', 'processing' => 'Em Preparo',
                        'shipped' => 'Enviado', 'delivered' => 'Entregue',
                        'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('BRL'),
                Tables\Columns\TextColumn::make('created_at')->label('Data')->dateTime('d/m H:i'),
            ]);
    }
}

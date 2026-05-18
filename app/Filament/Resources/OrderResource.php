<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Atualizar Pedido')->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'          => 'Aguardando',
                        'awaiting_payment' => 'Aguardando Pagamento',
                        'paid'             => 'Pago',
                        'processing'       => 'Em Preparo',
                        'shipped'          => 'Enviado',
                        'delivered'        => 'Entregue',
                        'cancelled'        => 'Cancelado',
                        'refunded'         => 'Reembolsado',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('tracking_code')
                    ->label('Código de Rastreio'),

                Forms\Components\Textarea::make('admin_notes')
                    ->label('Notas Internas')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Dados do Pedido')->schema([
                Infolists\Components\TextEntry::make('order_number')->label('Nº Pedido'),
                Infolists\Components\TextEntry::make('status_label')->label('Status')
                    ->badge()
                    ->color(fn($record) => $record->status_color),
                Infolists\Components\TextEntry::make('total')->label('Total')->money('BRL'),
                Infolists\Components\TextEntry::make('created_at')->label('Data')->dateTime('d/m/Y H:i'),
            ])->columns(4),

            Infolists\Components\Section::make('Cliente')->schema([
                Infolists\Components\TextEntry::make('customer_name')->label('Nome'),
                Infolists\Components\TextEntry::make('customer_email')->label('E-mail'),
                Infolists\Components\TextEntry::make('customer_phone')->label('Telefone'),
                Infolists\Components\TextEntry::make('customer_cpf')->label('CPF')->placeholder('-'),
            ])->columns(2),

            Infolists\Components\Section::make('Endereço de Entrega')->schema([
                Infolists\Components\TextEntry::make('shipping_address.street')->label('Rua'),
                Infolists\Components\TextEntry::make('shipping_address.number')->label('Número'),
                Infolists\Components\TextEntry::make('shipping_address.district')->label('Bairro'),
                Infolists\Components\TextEntry::make('shipping_address.city')->label('Cidade'),
                Infolists\Components\TextEntry::make('shipping_address.state')->label('UF'),
                Infolists\Components\TextEntry::make('shipping_address.zip_code')->label('CEP'),
            ])->columns(3),

            Infolists\Components\Section::make('Itens do Pedido')->schema([
                Infolists\Components\RepeatableEntry::make('items')->schema([
                    Infolists\Components\TextEntry::make('product_name')->label('Produto'),
                    Infolists\Components\TextEntry::make('quantity')->label('Qtd'),
                    Infolists\Components\TextEntry::make('unit_price')->label('Preço Unit.')->money('BRL'),
                    Infolists\Components\TextEntry::make('total')->label('Total')->money('BRL'),
                ])->columns(4),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_email')
                    ->label('E-mail')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => ['pending', 'awaiting_payment'],
                        'info'    => ['paid', 'processing'],
                        'success' => ['shipped', 'delivered'],
                        'danger'  => ['cancelled', 'refunded'],
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'          => 'Aguardando',
                        'awaiting_payment' => 'Ag. Pagamento',
                        'paid'             => 'Pago',
                        'processing'       => 'Em Preparo',
                        'shipped'          => 'Enviado',
                        'delivered'        => 'Entregue',
                        'cancelled'        => 'Cancelado',
                        'refunded'         => 'Reembolsado',
                        default            => $state,
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Itens')
                    ->counts('items'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'          => 'Aguardando',
                        'awaiting_payment' => 'Ag. Pagamento',
                        'paid'             => 'Pago',
                        'processing'       => 'Em Preparo',
                        'shipped'          => 'Enviado',
                        'delivered'        => 'Entregue',
                        'cancelled'        => 'Cancelado',
                    ]),
                Tables\Filters\Filter::make('today')
                    ->label('Hoje')
                    ->query(fn($query) => $query->whereDate('created_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

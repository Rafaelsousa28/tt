<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produtos';
    protected static ?string $modelLabel = 'Produto';
    protected static ?string $pluralModelLabel = 'Produtos';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->tabs([
                Forms\Components\Tabs\Tab::make('Informações Básicas')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome do Produto')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('category_id')
                        ->label('Categoria')
                        ->options(Category::active()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('sku')
                        ->label('SKU')
                        ->unique(ignoreRecord: true),

                    Forms\Components\Textarea::make('short_description')
                        ->label('Descrição Curta')
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('description')
                        ->label('Descrição Completa')
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Preço & Estoque')->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Preço')
                        ->numeric()
                        ->prefix('R$')
                        ->required(),

                    Forms\Components\TextInput::make('sale_price')
                        ->label('Preço Promocional')
                        ->numeric()
                        ->prefix('R$')
                        ->nullable(),

                    Forms\Components\TextInput::make('stock')
                        ->label('Estoque')
                        ->numeric()
                        ->required()
                        ->default(0),

                    Forms\Components\TextInput::make('min_stock_alert')
                        ->label('Alerta de Estoque Mínimo')
                        ->numeric()
                        ->default(5),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Cuidados')->schema([
                    Forms\Components\Select::make('care_level')
                        ->label('Nível de Cuidado')
                        ->options([
                            'facil'   => 'Fácil',
                            'medio'   => 'Médio',
                            'dificil' => 'Difícil',
                        ])
                        ->required(),

                    Forms\Components\Select::make('light_requirement')
                        ->label('Luminosidade')
                        ->options([
                            'baixa'    => 'Baixa',
                            'media'    => 'Média',
                            'alta'     => 'Alta',
                            'pleno_sol'=> 'Pleno Sol',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('water_frequency')
                        ->label('Frequência de Rega'),

                    Forms\Components\TextInput::make('height_cm')
                        ->label('Altura (cm)'),

                    Forms\Components\TextInput::make('pot_size_cm')
                        ->label('Tamanho do Vaso (cm)'),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Imagens')->schema([
                    Forms\Components\FileUpload::make('images')
                        ->label('Fotos do Produto')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->directory('products')
                        ->columnSpanFull(),
                ]),

                Forms\Components\Tabs\Tab::make('Configurações')->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Produto Ativo')
                        ->default(true),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Produto em Destaque'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('')
                    ->getStateUsing(fn($record) => $record->images[0] ?? null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Promoção')
                    ->money('BRL')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Estoque')
                    ->sortable()
                    ->color(fn($record) => $record->stock <= $record->min_stock_alert ? 'danger' : 'success'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo'),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destaque')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->options(Category::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')->label('Ativo'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Destaque'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Estoque Baixo')
                    ->query(fn($query) => $query->whereColumn('stock', '<=', 'min_stock_alert')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar Selecionados')
                        ->action(fn($records) => $records->each->update(['is_active' => true])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

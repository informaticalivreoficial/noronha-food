<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $slug = 'pedidos';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Informações do Pedido')->schema([
                        Select::make('user_id')
                            ->label('Cliente')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_method')
                            ->label('Método de pagamento')
                            ->options([
                                'stripe' => 'Stripe',
                                'pix' => 'Pix',
                                'cod' => 'Pagar na entrega',
                            ])->required(),
                        Select::make('payment_status')
                            ->label('Status do pagamento')
                            ->options([
                                'pending' => 'Pendente',
                                'paid' => 'Pago',
                                'failed' => 'Falha',
                            ])->default('pending')->required(),
                        ToggleButtons::make('status')
                            ->inline()
                            ->label('Status do pedido')
                            ->options([
                                'new' => 'Novo',
                                'processing' => 'Processando',
                                'shipped' => 'Enviado',
                                'delivered' => 'Entregue',
                                'canceled' => 'Cancelado',
                            ])->colors([
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'success',
                                'delivered' => 'success',
                                'canceled' => 'danger',
                            ])->icons([
                                'new' => 'heroicon-m-sparkles',
                                'processing' => 'heroicon-m-arrow-path',
                                'shipped' => 'heroicon-m-truck',
                                'delivered' => 'heroicon-m-check-badge',
                                'canceled' => 'heroicon-m-x-circle',
                            ]),
                        Select::make('shipping_method')
                            ->label('Método de envio')
                            ->options([
                                'frete' => 'Noronha Express',
                                'universo' => 'Universo',
                                'agemar' => 'Agemar',
                                'alfamares' => 'Alfamares'
                            ]),
                        Textarea::make('notes')
                            ->label('Notas adicionais')
                            ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Itens do Pedido')->schema([
                        Repeater::make('items')
                            ->relationship()->schema([
                                Select::make('product_id')
                                    ->label('Produto')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(4)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set) 
                                            => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn ($state, Set $set) 
                                            => $set('total_amount', Product::find($state)?->price ?? 0)),
                                TextInput::make('quantity')
                                    ->label('Quantidade')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) 
                                            => $set('total_amount', $state * $get('unit_amount'))),
                                TextInput::make('unit_amount')
                                    ->label('Preço Unitário')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),
                                TextInput::make('total_amount')
                                    ->label('Preço total')
                                    ->numeric()
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpan(3),
                            ])->columns(12),

                            Placeholder::make('grand_total_placeholder')
                                ->label('Total Geral')
                                ->content(function (Get $get, Set $set){
                                    $total = 0;
                                    if(!$repeaters = $get('items')){
                                        return $total;
                                    }

                                    $set('grand_total', $total);
                                    foreach($repeaters as $key => $repeater){
                                        $total += $get("items.{$key}.total_amount");
                                    }

                                    return Number::currency($total, 'BRL');
                                }),

                                Hidden::make('grand_total')
                                    ->default(0)
                    ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('grand_total')
                    ->label('Valor Total')
                    ->numeric()
                    ->sortable()
                    ->money('BRL'),
                TextColumn::make('payment_method')
                    ->label('Gateway')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Pagamento')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable()
                    ->options([
                        'new' => 'Novo',
                        'processing' => 'Processando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregue',
                        'canceled' => 'Cancelado',
                    ])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
                Tables\Actions\EditAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Pedido');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Pedidos');
    }
}

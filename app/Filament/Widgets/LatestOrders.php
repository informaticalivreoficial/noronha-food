<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Últimos Pedidos')
            ->query(OrderResource::getEloquentQuery())
                ->defaultPaginationPageOption(5)
                ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cliente'),
                TextColumn::make('grand_total')
                    ->label('Valor Total')
                    ->numeric()
                    ->money('BRL'),
                TextColumn::make('payment_method')
                    ->label('Gateway'),
                TextColumn::make('payment_status')
                    ->label('Pagamento'),
                    TextColumn::make('status')
                    ->label('Pedido')
                    ->badge()
                    ->color(fn (string $state):string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'canceled' => 'danger',
                    })
                    ->icon(fn (string $state):string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'canceled' => 'heroicon-m-x-circle',
                    })->sortable(),
                    TextColumn::make('created_at')->label('Data')->dateTime('d/m/Y')->sortable(),
            ])
            ->actions([
                Action::make('Ver Pedido')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record]))
                    ->color('info')
                    ->icon('heroicon-m-eye'),
            ]);
    }
}

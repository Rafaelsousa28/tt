<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\RecentOrders;
use App\Filament\Widgets\RevenueStats;
use App\Filament\Widgets\StockAlert;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            RevenueStats::class,
            OrdersChart::class,
            StockAlert::class,
            RecentOrders::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RevenueStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today      = Carbon::today();
        $thisMonth  = Carbon::now()->startOfMonth();
        $lastMonth  = Carbon::now()->subMonth()->startOfMonth();

        $revenueThisMonth = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', $thisMonth)
            ->sum('total');

        $revenueLastMonth = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('total');

        $ordersToday = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::whereIn('status', ['paid', 'processing'])->count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock_alert')->where('is_active', true)->count();

        $revenueChange = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Receita do Mês', 'R$ ' . number_format($revenueThisMonth, 2, ',', '.'))
                ->description($revenueChange >= 0 ? "+{$revenueChange}% vs mês anterior" : "{$revenueChange}% vs mês anterior")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueChart()),

            Stat::make('Pedidos Hoje', $ordersToday)
                ->description('Pedidos recebidos')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Aguardando Envio', $pendingOrders)
                ->description('Pedidos pagos/em preparo')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Estoque Baixo', $lowStockCount)
                ->description('Produtos com estoque crítico')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }

    private function getRevenueChart(): array
    {
        return Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }
}

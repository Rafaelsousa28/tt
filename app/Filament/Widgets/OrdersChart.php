<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Vendas dos Últimos 30 Dias';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data   = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date     = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[]   = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                ->whereDate('created_at', $date)
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Receita (R$)',
                    'data'            => $data,
                    'borderColor'     => '#3BC117',
                    'backgroundColor' => 'rgba(59,193,23,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
        Stat::make('Critical', Compliance::where('status', 'critical')->count()),
        Stat::make('Pending', Compliance::where('status', 'pending')->count()),
        Stat::make('Under Process', Compliance::where('status', 'process')->count()),
        Stat::make('Renewal Due', Compliance::where('renewal_due', true)->count()),
    ];
    }
}

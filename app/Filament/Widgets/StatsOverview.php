<?php

namespace App\Filament\Widgets;

use App\Models\BranchComplianceRecord;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Critical', BranchComplianceRecord::where('status', 'critical')->count())
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),
            Stat::make('Pending', BranchComplianceRecord::where('status', 'pending')->count())
                ->color('warning')
                ->icon('heroicon-o-clock'),
            Stat::make('Under Process', BranchComplianceRecord::where('status', 'process')->count())
                ->color('info')
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Renewal Due', BranchComplianceRecord::where('renewal_due', true)->count())
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}

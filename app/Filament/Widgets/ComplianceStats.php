<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use App\Models\BranchComplianceRecord;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ComplianceStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Critical', BranchComplianceRecord::where('status', 'critical')->count()),

            Stat::make('Pending', BranchComplianceRecord::where('status', 'pending')->count()),

            Stat::make('Under Process', BranchComplianceRecord::where('status', 'process')->count()),

            Stat::make('Renewal Due', BranchComplianceRecord::where('renewal_due', true)->count()),
        ];
    }
}

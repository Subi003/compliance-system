<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\BranchComplianceRecord;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BranchStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    /** Currently selected branch ID (null = all allowed branches) */
    public ?int $selectedBranch = null;

    /**
     * Override content() to inject a branch selector above the stats.
     */
    public function content(Schema $schema): Schema
    {
        $user = Auth::user();

        // Build branch options scoped to the user
        $branchOptions = $user && ! $user->hasRole('admin')
            ? $user->branches()->orderBy('title')->pluck('title', 'id')->toArray()
            : Branch::orderBy('title')->pluck('title', 'id')->toArray();

        return $schema->components([
            // Branch filter select
            Select::make('selectedBranch')
                ->label('Filter by Branch')
                ->options($branchOptions)
                ->placeholder('All Branches')
                ->live()
                ->afterStateUpdated(function () {
                    // Reset cached stats so getStats() is called fresh
                    $this->cachedStats = null;
                })
                ->columnSpanFull(),

            // The stats section (calls getStats())
            $this->getSectionContentComponent(),
        ]);
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        // Base query — always scoped to user's branches
        $query = BranchComplianceRecord::query();

        if ($user && ! $user->hasRole('admin')) {
            $allowedIds = $user->branches()->pluck('branches.id')->toArray();
            $query->whereIn('branch_id', $allowedIds);
        }

        // Further filter by selected branch
        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $branchLabel = $this->selectedBranch
            ? Branch::find($this->selectedBranch)?->title ?? 'Selected Branch'
            : 'All Branches';

        return [
            Stat::make('⚠ Critical', (clone $query)->where('status', 'critical')->count())
                ->description('To Date within 15 days — ' . $branchLabel)
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('⏳ Approval Pending', (clone $query)->where('status', 'pending')->count())
                ->description('Waiting for approver — ' . $branchLabel)
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('✓ Approved', (clone $query)->where('status', 'approved')->count())
                ->description($branchLabel)
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('↻ Under Process', (clone $query)->where('status', 'process')->count())
                ->description('No From/To dates set — ' . $branchLabel)
                ->color('info')
                ->icon('heroicon-o-arrow-path'),

            Stat::make('↺ Renewal Due', (clone $query)->where('status', 'renewal')->count())
                ->description('To Date expired — ' . $branchLabel)
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Total Records', (clone $query)->count())
                ->description($branchLabel)
                ->color('gray')
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    protected function getColumns(): int|array|null
    {
        return 3;
    }
}

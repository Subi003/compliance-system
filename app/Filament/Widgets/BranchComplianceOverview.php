<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BranchComplianceOverview extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Branch-wise Compliance Overview';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query(function () use ($user) {
                $query = Branch::query()
                    ->withCount([
                        'complianceRecords',
                        'complianceRecords as critical_count' => fn (Builder $q) => $q->where('status', 'critical'),
                        'complianceRecords as pending_count'  => fn (Builder $q) => $q->where('status', 'pending'),
                        'complianceRecords as approved_count' => fn (Builder $q) => $q->where('status', 'approved'),
                        'complianceRecords as process_count'  => fn (Builder $q) => $q->where('status', 'process'),
                        'complianceRecords as renewal_count'  => fn (Builder $q) => $q->where('status', 'renewal'),
                    ])
                    ->with('company');

                // Scope to user's assigned branches (admins see all)
                if ($user && ! $user->hasRole('admin')) {
                    $branchIds = $user->branches()->pluck('branches.id')->toArray();
                    $query->whereIn('id', $branchIds);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('title')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('compliance_records_count')
                    ->label('Total')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('critical_count')
                    ->label('⚠ Critical')
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('pending_count')
                    ->label('⏳ Pending')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('approved_count')
                    ->label('✓ Approved')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('process_count')
                    ->label('↻ In Process')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('renewal_count')
                    ->label('↺ Renewal Due')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50]);
    }
}

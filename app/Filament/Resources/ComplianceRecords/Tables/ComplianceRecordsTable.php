<?php

namespace App\Filament\Resources\ComplianceRecords\Tables;

use App\Models\Branch;
use App\Models\BranchComplianceRecord;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ComplianceRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            // ── Branch-scoped query ──────────────────────────────────────────
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user && ! $user->hasRole('admin')) {
                    $branchIds = $user->branches()->pluck('branches.id')->toArray();
                    $query->whereIn('branch_id', $branchIds);
                }
            })

            // ── Columns ──────────────────────────────────────────────────────
            ->columns([
                TextColumn::make('branch.title')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('compliance.name')
                    ->label('Compliance')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('from_date')
                    ->label('From Date')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('to_date')
                    ->label('To Date')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable()
                    ->color(fn (BranchComplianceRecord $record): ?string => match (true) {
                        $record->isExpired()                                 => 'danger',
                        $record->to_date?->lte(now()->addDays(15)) ?? false  => 'warning',
                        default                                              => null,
                    }),

                // Uses computed accessor — always reflects live date logic
                TextColumn::make('computed_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'process'  => 'info',
                        'renewal'  => 'primary',
                        'rejected' => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'critical' => '⚠ Critical',
                        'pending'  => '⏳ Approval Pending',
                        'approved' => '✓ Approved',
                        'process'  => '↻ Under Process',
                        'renewal'  => '↺ Renewal Due',
                        'rejected' => '✕ Rejected',
                        default    => $state,
                    })
                    ->sortable(false),

                // Rejection reason — only visible when present
                TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->placeholder('—')
                    ->limit(40)
                    ->tooltip(fn (BranchComplianceRecord $record) => $record->rejection_reason)
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('renewal_due')
                    ->label('Renewal Due')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),
            ])

            ->defaultSort('to_date', 'asc')

            // ── Filters ──────────────────────────────────────────────────────
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'title'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'process'  => '↻ Under Process',
                        'pending'  => '⏳ Approval Pending',
                        'critical' => '⚠ Critical',
                        'renewal'  => '↺ Renewal Due',
                        'approved' => '✓ Approved',
                        'rejected' => '✕ Rejected',
                    ]),

                TernaryFilter::make('renewal_due')
                    ->label('Renewal Due'),
            ])

            // ── Row actions ──────────────────────────────────────────────────
            ->recordActions([
                // ✅ APPROVE
                // Visible only when: not yet final (not approved, not rejected) AND user is approver
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve this compliance record?')
                    ->modalDescription('This will permanently mark the record as Approved. This action cannot be undone.')
                    ->visible(fn (BranchComplianceRecord $record) =>
                        ! $record->isFinalDecision()
                        && static::isApproverOf($record)
                    )
                    ->action(fn (BranchComplianceRecord $record) =>
                        $record->updateQuietly([
                            'status'           => 'approved',
                            'rejection_reason' => null,
                        ])
                    ),

                // ❌ REJECT
                // Visible only when: not yet final AND user is approver
                // Opens a modal form requiring a rejection reason
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Reject this compliance record')
                    ->modalDescription('Please provide a reason for rejection. This is required and will be visible to the team.')
                    ->modalSubmitActionLabel('Confirm Rejection')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->placeholder('Enter the reason for rejecting this compliance record...')
                            ->rows(4)
                            ->required()
                            ->minLength(10)
                            ->maxLength(1000),
                    ])
                    ->visible(fn (BranchComplianceRecord $record) =>
                        ! $record->isFinalDecision()
                        && static::isApproverOf($record)
                    )
                    ->action(function (BranchComplianceRecord $record, array $data) {
                        $record->updateQuietly([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    }),

                EditAction::make(),

                // Delete admin only
                DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('admin') ?? false),
            ])

            // ── Bulk actions — admin only ─────────────────────────────────────
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->hasRole('admin') ?? false),
                ]),
            ]);
    }

    /**
     * True if logged-in user is admin OR first_approver of this record's branch.
     */
    private static function isApproverOf(BranchComplianceRecord $record): bool
    {
        $user = Auth::user();
        if (! $user) return false;
        if ($user->hasRole('admin')) return true;

        return Branch::where('id', $record->branch_id)
            ->where('first_approver_id', $user->id)
            ->exists();
    }
}

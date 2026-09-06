<?php

namespace App\Filament\Resources\ComplianceRecords;

use App\Filament\Resources\ComplianceRecords\Pages\CreateComplianceRecord;
use App\Filament\Resources\ComplianceRecords\Pages\EditComplianceRecord;
use App\Filament\Resources\ComplianceRecords\Pages\ListComplianceRecords;
use App\Filament\Resources\ComplianceRecords\Schemas\ComplianceRecordForm;
use App\Filament\Resources\ComplianceRecords\Tables\ComplianceRecordsTable;
use App\Models\BranchComplianceRecord;
use App\Models\UserPagePermission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComplianceRecordResource extends Resource
{
    protected static ?string $model = BranchComplianceRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Compliance Records';

    protected static ?string $modelLabel = 'Compliance Record';

    protected static ?string $pluralModelLabel = 'Compliance Records';

    public static function form(Schema $schema): Schema
    {
        return ComplianceRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplianceRecordsTable::configure($table);
    }

    /**
     * Scope records to user's assigned branches. Admins see all.
     * Also enforced in the table's modifyQueryUsing — this covers
     * direct URL access (edit/view routes).
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if ($user && ! $user->hasRole('admin')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            $query->whereIn('branch_id', $branchIds);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canView($user->id, 'compliance-records');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'compliance-records');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'compliance-records');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return $user->hasRole('admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListComplianceRecords::route('/'),
            'create' => CreateComplianceRecord::route('/create'),
            'edit'   => EditComplianceRecord::route('/{record}/edit'),
        ];
    }
}

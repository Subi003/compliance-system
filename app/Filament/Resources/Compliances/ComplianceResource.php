<?php

namespace App\Filament\Resources\Compliances;

use App\Filament\Resources\Compliances\Pages\CreateCompliance;
use App\Filament\Resources\Compliances\Pages\EditCompliance;
use App\Filament\Resources\Compliances\Pages\ListCompliances;
use App\Filament\Resources\Compliances\Schemas\ComplianceForm;
use App\Filament\Resources\Compliances\Tables\CompliancesTable;
use App\Models\Compliance;
use App\Models\UserPagePermission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComplianceResource extends Resource
{
    protected static ?string $model = Compliance::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ComplianceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompliancesTable::configure($table);
    }

    /**
     * Scope to compliances that belong to the user's assigned branches.
     * Admins see all.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if ($user && ! $user->hasRole('admin')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            $query->whereHas('branches', fn (Builder $q) =>
                $q->whereIn('branches.id', $branchIds)
            );
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canView($user->id, 'compliances');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'compliances');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'compliances');
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
            'index'  => ListCompliances::route('/'),
            'create' => CreateCompliance::route('/create'),
            'edit'   => EditCompliance::route('/{record}/edit'),
        ];
    }
}

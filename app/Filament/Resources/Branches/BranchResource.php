<?php

namespace App\Filament\Resources\Branches;

use App\Filament\Resources\Branches\Pages\CreateBranch;
use App\Filament\Resources\Branches\Pages\EditBranch;
use App\Filament\Resources\Branches\Pages\ListBranches;
use App\Filament\Resources\Branches\Schemas\BranchForm;
use App\Filament\Resources\Branches\Tables\BranchesTable;
use App\Models\Branch;
use App\Models\UserPagePermission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
    }

    /** Scope to branches assigned to the current user (admins see all). */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if ($user && ! $user->hasRole('admin')) {
            $ids = $user->branches()->pluck('branches.id')->toArray();
            $query->whereIn('id', $ids);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canView($user->id, 'branches');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'branches');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'branches');
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
            'index'  => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit'   => EditBranch::route('/{record}/edit'),
        ];
    }
}

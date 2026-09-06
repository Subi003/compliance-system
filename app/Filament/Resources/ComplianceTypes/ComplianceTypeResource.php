<?php

namespace App\Filament\Resources\ComplianceTypes;

use App\Filament\Resources\ComplianceTypes\Pages\CreateComplianceType;
use App\Filament\Resources\ComplianceTypes\Pages\EditComplianceType;
use App\Filament\Resources\ComplianceTypes\Pages\ListComplianceTypes;
use App\Filament\Resources\ComplianceTypes\Schemas\ComplianceTypeForm;
use App\Filament\Resources\ComplianceTypes\Tables\ComplianceTypesTable;
use App\Models\ComplianceType;
use App\Models\UserPagePermission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ComplianceTypeResource extends Resource
{
    protected static ?string $model = ComplianceType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ComplianceTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplianceTypesTable::configure($table);
    }

    /**
     * ComplianceTypes are global (not branch-specific).
     * Access controlled purely by page permission + role.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canView($user->id, 'compliance-types');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'compliance-types');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return UserPagePermission::canEdit($user->id, 'compliance-types');
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
            'index'  => ListComplianceTypes::route('/'),
            'create' => CreateComplianceType::route('/create'),
            'edit'   => EditComplianceType::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\ComplianceTypes;

use App\Filament\Resources\ComplianceTypes\Pages\CreateComplianceType;
use App\Filament\Resources\ComplianceTypes\Pages\EditComplianceType;
use App\Filament\Resources\ComplianceTypes\Pages\ListComplianceTypes;
use App\Filament\Resources\ComplianceTypes\Schemas\ComplianceTypeForm;
use App\Filament\Resources\ComplianceTypes\Tables\ComplianceTypesTable;
use App\Models\ComplianceType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ComplianceTypeResource extends Resource
{
    protected static ?string $model = ComplianceType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ComplianceTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplianceTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComplianceTypes::route('/'),
            'create' => CreateComplianceType::route('/create'),
            'edit' => EditComplianceType::route('/{record}/edit'),
        ];
    }
}

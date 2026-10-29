<?php

namespace App\Filament\Resources\Compliances\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ComplianceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('compliance_type_id')
                    ->label('Compliance Type')
                    ->relationship('complianceType', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}

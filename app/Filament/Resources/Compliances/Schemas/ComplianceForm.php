<?php

namespace App\Filament\Resources\Compliances\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ComplianceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}

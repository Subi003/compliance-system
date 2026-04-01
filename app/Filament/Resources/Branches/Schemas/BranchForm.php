<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title') // ✅ better than name
                    ->required(),

                TextInput::make('location'),

                Select::make('company_id') // 🔥 NEW
                ->relationship('company', 'name')
                ->required(),

                Select::make('responsible') // 🔥
                ->options([
                    'rahul' => 'Rahul',
                    'amit' => 'Amit',
                    'neha' => 'Neha',
                ])
                ->required(),

            Select::make('first_approver') // 🔥
                ->options([
                    'manager1' => 'Manager 1',
                    'manager2' => 'Manager 2',
                ])
                ->required(),


                CheckboxList::make('compliances')
                    ->relationship('compliances', 'name')
                    ->columns(2),
            ]);
    }
}
<?php

namespace App\Filament\Resources\ComplianceRecords\Schemas;

use App\Models\Branch;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ComplianceRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->columns(2)
            ->components([
                Select::make('branch_id')
                    ->label('Branch')
                    ->options(function () use ($user) {
                        if ($user?->hasRole('admin')) {
                            return Branch::orderBy('title')->pluck('title', 'id');
                        }
                        return $user?->branches()->orderBy('title')->pluck('title', 'id') ?? [];
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('compliance_id')
                    ->label('Compliance')
                    ->relationship('compliance', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('from_date')
                    ->label('From Date')
                    ->displayFormat('d M Y')
                    ->nullable(),

                DatePicker::make('to_date')
                    ->label('To Date')
                    ->displayFormat('d M Y')
                    ->nullable()
                    ->afterOrEqual('from_date'),
            ]);
    }
}

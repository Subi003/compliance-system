<?php

namespace App\Filament\Resources\ComplianceTypes\Tables;

use App\Models\ComplianceType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ComplianceTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Type Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('terms')
                    ->label('Terms')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => ComplianceType::TERMS_OPTIONS[$state] ?? $state)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('terms')
                    ->label('Terms')
                    ->options(ComplianceType::TERMS_OPTIONS),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->placeholder('All'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

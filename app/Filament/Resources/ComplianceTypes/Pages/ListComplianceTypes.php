<?php

namespace App\Filament\Resources\ComplianceTypes\Pages;

use App\Filament\Resources\ComplianceTypes\ComplianceTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComplianceTypes extends ListRecords
{
    protected static string $resource = ComplianceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

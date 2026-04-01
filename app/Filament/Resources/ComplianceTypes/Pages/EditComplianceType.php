<?php

namespace App\Filament\Resources\ComplianceTypes\Pages;

use App\Filament\Resources\ComplianceTypes\ComplianceTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComplianceType extends EditRecord
{
    protected static string $resource = ComplianceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ComplianceRecords\Pages;

use App\Filament\Resources\ComplianceRecords\ComplianceRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComplianceRecord extends EditRecord
{
    protected static string $resource = ComplianceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

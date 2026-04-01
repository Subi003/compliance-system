<?php

namespace App\Filament\Resources\Compliances\Pages;

use App\Filament\Resources\Compliances\ComplianceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompliances extends ListRecords
{
    protected static string $resource = ComplianceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

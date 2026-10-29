<?php

namespace App\Filament\Resources\ComplianceTypes\Schemas;

use App\Models\ComplianceType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ComplianceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Type Name')
                    ->required()
                    ->maxLength(255),

                Select::make('terms')
                    ->label('Terms')
                    ->options(ComplianceType::TERMS_OPTIONS)
                    ->required()
                    ->native(false),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),

                FileUpload::make('attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->downloadable()
                    ->openable()
                    ->reorderable()
                    ->appendFiles()
                    ->preserveFilenames()
                    ->directory('compliance-type-attachments')
                    ->maxSize(10240) // 10 MB per file
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->helperText('Upload PDF, Word, Excel or image files. Max 10MB each.')
                    ->columnSpanFull(),
            ]);
    }
}

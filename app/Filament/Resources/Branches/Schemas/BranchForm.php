<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('Branch Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')
                    ->maxLength(255),

                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('responsible_id')
                    ->label('Responsible Person')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('first_approver_id')
                    ->label('First Approver')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                CheckboxList::make('compliances')
                    ->relationship('compliances', 'name')
                    ->columns(2)
                    ->label('Assigned Compliances')
                    ->columnSpanFull(),

                FileUpload::make('attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->downloadable()
                    ->openable()
                    ->reorderable()
                    ->appendFiles()
                    ->preserveFilenames()
                    ->directory('branch-attachments')
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

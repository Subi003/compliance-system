<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Account Details')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label(fn (string $operation) => $operation === 'edit'
                                ? 'New Password (leave blank to keep)'
                                : 'Password')
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Role & Branch Access')
                    ->columns(1)
                    ->components([
                        Select::make('roles')
                            ->label('Role')
                            ->options(Role::pluck('name', 'name'))
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->relationship('roles', 'name'),

                        CheckboxList::make('branches')
                            ->label('Assigned Branches')
                            ->relationship('branches', 'title')
                            ->columns(2),
                    ]),
            ]);
    }
}

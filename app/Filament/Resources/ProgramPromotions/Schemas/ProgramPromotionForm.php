<?php

namespace App\Filament\Resources\ProgramPromotions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramPromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Promo program')
                    ->columns(2)
                    ->schema([
                        Select::make('program_id')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(180),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Select::make('discount_type')
                            ->options(['percentage' => 'Percentage', 'fixed' => 'Fixed amount'])
                            ->required(),
                        TextInput::make('discount_value')
                            ->numeric()
                            ->required()
                            ->prefix('Rp / %'),
                        DateTimePicker::make('starts_at'),
                        DateTimePicker::make('ends_at'),
                        TextInput::make('badge_label')->maxLength(80),
                        Toggle::make('is_active')->default(true),
                        Textarea::make('terms')->columnSpanFull(),
                    ]),
            ]);
    }
}

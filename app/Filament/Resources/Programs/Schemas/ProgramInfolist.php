<?php

namespace App\Filament\Resources\Programs\Schemas;

use App\Models\Program;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('category')
                    ->badge(),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('target_age')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('duration_meetings')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_students')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('registration_fee')
                    ->numeric(),
                TextEntry::make('thumbnail')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Program $record): bool => $record->trashed()),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Contents\Schemas;

use App\Models\Content;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('title'),
                TextEntry::make('body')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('images')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta.category')
                    ->label('Kategori')
                    ->placeholder('-')
                    ->visible(fn (Content $record): bool => $record->type === Content::TYPE_PARTNER),
                TextEntry::make('meta.website')
                    ->label('Website')
                    ->placeholder('-')
                    ->visible(fn (Content $record): bool => $record->type === Content::TYPE_PARTNER),
                TextEntry::make('meta.since')
                    ->label('Tahun kerja sama')
                    ->placeholder('-')
                    ->visible(fn (Content $record): bool => $record->type === Content::TYPE_PARTNER),
                TextEntry::make('meta.role')
                    ->label('Role / asal')
                    ->placeholder('-')
                    ->visible(fn (Content $record): bool => $record->type === Content::TYPE_TESTIMONIAL),
                TextEntry::make('meta.rating')
                    ->label('Rating')
                    ->placeholder('-')
                    ->visible(fn (Content $record): bool => $record->type === Content::TYPE_TESTIMONIAL),
                TextEntry::make('display_order')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_published')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

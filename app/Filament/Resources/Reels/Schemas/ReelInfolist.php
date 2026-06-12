<?php

namespace App\Filament\Resources\Reels\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('video_path'),
                TextEntry::make('thumbnail_path')
                    ->placeholder('-'),
                TextEntry::make('duration_seconds')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('views_count')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('likes_count')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_published')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->dateTime()
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

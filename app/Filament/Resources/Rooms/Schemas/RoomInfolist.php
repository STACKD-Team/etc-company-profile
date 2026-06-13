<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Models\Room;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('image')
                    ->label('Gambar')
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label('Nama room'),
                TextEntry::make('description')
                    ->label('Deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('facilities')
                    ->label('Fasilitas')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('display_order')
                    ->label('Urutan tampil')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Room $record): bool => $record->trashed()),
            ]);
    }
}

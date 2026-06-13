<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Services\MediaStorageService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Room')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama room')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),
                        TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(999),
                        TextInput::make('display_order')
                            ->label('Urutan tampil')
                            ->numeric()
                            ->default(0),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        TagsInput::make('facilities')
                            ->label('Fasilitas')
                            ->placeholder('Tambah fasilitas')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar room')
                            ->image()
                            ->visibility('public')
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'rooms'))
                            ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file))),
                    ]),
            ]);
    }
}

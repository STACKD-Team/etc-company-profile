<?php

namespace App\Filament\Resources\Reels\Schemas;

use App\Services\MediaStorageService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ReelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('video_path')
                    ->label('Video')
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/webm'])
                    ->maxSize(51200)
                    ->visibility('public')
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'reels/videos'))
                    ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file)))
                    ->required(),
                FileUpload::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->image()
                    ->maxSize(4096)
                    ->visibility('public')
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'reels/thumbnails'))
                    ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file))),
                TextInput::make('duration_seconds')
                    ->numeric()
                    ->default(null),
                Select::make('category')
                    ->options([
            'promosi' => 'Promosi',
            'dokumentasi' => 'Dokumentasi',
            'edukasi' => 'Edukasi',
            'testimoni' => 'Testimoni',
            'event' => 'Event',
        ])
                    ->default('edukasi'),
                TextInput::make('views_count')
                    ->numeric()
                    ->default(0),
                TextInput::make('likes_count')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published'),
                DateTimePicker::make('published_at'),
            ]);
    }
}

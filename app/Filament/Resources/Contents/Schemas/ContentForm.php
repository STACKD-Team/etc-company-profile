<?php

namespace App\Filament\Resources\Contents\Schemas;

use App\Services\MediaStorageService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options([
            'page' => 'Page',
            'gallery' => 'Gallery',
            'partner' => 'Partner',
            'room' => 'Room',
            'team_member_extra' => 'Team member extra',
            'setting' => 'Setting',
        ])
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->default(null),
                Textarea::make('body')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Main image / partner logo')
                    ->image()
                    ->visibility('public')
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'contents/images'))
                    ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file))),
                FileUpload::make('images')
                    ->label('Gallery images')
                    ->multiple()
                    ->image()
                    ->visibility('public')
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'contents/gallery'))
                    ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file)))
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->helperText('JSON metadata: caption, alt_text, category, website, since, partner_type, sort_order.')
                    ->formatStateUsing(fn ($state): ?string => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                    ->dehydrateStateUsing(fn (?string $state): ?array => filled($state) ? json_decode($state, true) : null)
                    ->columnSpanFull(),
                TextInput::make('display_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published'),
            ]);
    }
}

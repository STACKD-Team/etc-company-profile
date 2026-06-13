<?php

namespace App\Filament\Resources\Contents\Schemas;

use App\Models\Content;
use App\Services\MediaStorageService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ContentForm
{
    public static function configure(Schema $schema, ?string $fixedType = null): Schema
    {
        return $schema
            ->components([
                Section::make('Konten')
                    ->columns(2)
                    ->schema([
                        self::typeField($fixedType),
                        TextInput::make('title')
                            ->label('Judul / Nama / Pertanyaan')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Deskripsi / Pesan / Jawaban')
                            ->default(null)
                            ->columnSpanFull(),
                        TextInput::make('display_order')
                            ->label('Urutan tampil')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                    ]),
                Section::make('Media')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar utama / logo / foto')
                            ->image()
                            ->visibility('public')
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'contents/images'))
                            ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file)))
                            ->columnSpanFull(),
                        FileUpload::make('images')
                            ->label('Galeri gambar')
                            ->multiple()
                            ->image()
                            ->visibility('public')
                            ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_GALLERY))
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'contents/gallery'))
                            ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file)))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($get): bool => ! self::isType($fixedType, $get, Content::TYPE_FAQ)),
                Section::make('Informasi tambahan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta.category')
                            ->label('Kategori')
                            ->maxLength(100)
                            ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_PARTNER)),
                        TextInput::make('meta.website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255)
                            ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_PARTNER)),
                        TextInput::make('meta.since')
                            ->label('Tahun kerja sama')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100)
                            ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_PARTNER)),
                        TextInput::make('meta.role')
                            ->label('Role / asal')
                            ->maxLength(120)
                            ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_TESTIMONIAL)),
                        TextInput::make('meta.rating')
                            ->label('Rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(5)
                            ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_TESTIMONIAL)),
                    ])
                    ->visible(fn ($get): bool => self::isType($fixedType, $get, Content::TYPE_PARTNER) || self::isType($fixedType, $get, Content::TYPE_TESTIMONIAL)),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            Content::TYPE_GALLERY => 'Gallery',
            Content::TYPE_PARTNER => 'Partner',
            Content::TYPE_PROFILE => 'Profile',
            Content::TYPE_FAQ => 'FAQ',
            Content::TYPE_TESTIMONIAL => 'Testimonial',
        ];
    }

    private static function isType(?string $fixedType, mixed $get, string $type): bool
    {
        return ($fixedType ?? $get('type')) === $type;
    }

    private static function typeField(?string $fixedType): Hidden|Select
    {
        if ($fixedType !== null) {
            return Hidden::make('type')->default($fixedType);
        }

        return Select::make('type')
            ->options(self::typeOptions())
            ->required();
    }
}

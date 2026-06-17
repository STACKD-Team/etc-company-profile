<?php

namespace App\Filament\Resources\Programs\Schemas;

use App\Services\MediaStorageService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Program')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required()->maxLength(150),
                        TextInput::make('slug')->required()->maxLength(170),
                        Select::make('category')
                            ->options([
                                'english' => 'English',
                                'mandarin' => 'Mandarin',
                                'japanese' => 'Japanese',
                                'test_prep' => 'Test prep',
                                'soft_skills' => 'Soft skills',
                                'other' => 'Other',
                            ])
                            ->default('english')
                            ->required(),
                        Select::make('type')
                            ->options(['regular' => 'Regular', 'private' => 'Private', 'one_on_one' => 'One on one'])
                            ->default('regular')
                            ->required(),
                        Select::make('target_age')
                            ->options([
                                'kids' => 'Kids',
                                'teen' => 'Teen',
                                'adult' => 'Adult',
                                'university' => 'University',
                                'all' => 'All',
                            ])
                            ->default('all'),
                        Toggle::make('is_active')->default(true),
                        Textarea::make('description')->columnSpanFull(),
                    ]),
                Section::make('Pricing and media')
                    ->columns(2)
                    ->schema([
                        TextInput::make('duration_meetings')->numeric()->default(16),
                        TextInput::make('max_students')->numeric()->default(10),
                        TextInput::make('price')->required()->numeric()->default(0)->prefix('Rp'),
                        TextInput::make('registration_fee')->required()->numeric()->default(200000)->prefix('Rp'),
                        FileUpload::make('thumbnail')
                            ->label('Cover image')
                            ->image()
                            ->maxSize(4096)
                            ->visibility('public')
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'programs/thumbnails'))
                            ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file)))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

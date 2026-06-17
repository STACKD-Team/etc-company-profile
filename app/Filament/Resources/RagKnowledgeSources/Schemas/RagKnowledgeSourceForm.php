<?php

namespace App\Filament\Resources\RagKnowledgeSources\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RagKnowledgeSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('source_type')
                    ->options([
                        'upload' => 'Upload',
                        'manual' => 'Manual',
                        'url' => 'URL',
                        'faq' => 'FAQ',
                    ])
                    ->default('upload')
                    ->required(),
                FileUpload::make('source_file')
                    ->label('Knowledge file')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain',
                        'text/markdown',
                        'text/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(10240)
                    ->storeFiles(false)
                    ->visible(fn (?string $operation): bool => $operation === 'create')
                    ->required(fn (?string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Published')
                    ->default(true),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'processing' => 'Processing',
                        'ready' => 'Ready',
                        'failed' => 'Failed',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                Textarea::make('extracted_text')
                    ->label('Extracted text preview / manual content')
                    ->rows(10)
                    ->columnSpanFull(),
                Textarea::make('error_message')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }
}

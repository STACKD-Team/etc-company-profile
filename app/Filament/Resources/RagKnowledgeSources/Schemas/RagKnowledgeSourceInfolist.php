<?php

namespace App\Filament\Resources\RagKnowledgeSources\Schemas;

use App\Models\RagKnowledgeSource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RagKnowledgeSourceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Knowledge Source')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpanFull(),
                        TextEntry::make('source_type')
                            ->badge(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'ready' => 'success',
                                'processing' => 'warning',
                                'failed' => 'danger',
                                'archived' => 'gray',
                                default => 'primary',
                            }),
                        TextEntry::make('is_active')
                            ->label('Published')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Hidden')
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                        TextEntry::make('file_name')
                            ->placeholder('-'),
                        TextEntry::make('mime_type')
                            ->placeholder('-'),
                        TextEntry::make('file_size')
                            ->formatStateUsing(fn (?int $state): string => $state ? number_format($state / 1024, 1).' KB' : '-'),
                        TextEntry::make('uploader.name')
                            ->label('Uploaded by')
                            ->placeholder('-'),
                        TextEntry::make('processed_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('chunks_count')
                            ->label('Chunks')
                            ->state(fn (RagKnowledgeSource $record): int => $record->chunks()->count()),
                    ]),
                Section::make('Extracted Text Preview')
                    ->schema([
                        TextEntry::make('extracted_text')
                            ->placeholder('Belum ada teks yang diekstrak.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Indexing Error')
                    ->visible(fn (RagKnowledgeSource $record): bool => filled($record->error_message))
                    ->schema([
                        TextEntry::make('error_message')
                            ->color('danger')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

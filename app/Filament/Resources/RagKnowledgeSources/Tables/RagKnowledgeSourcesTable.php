<?php

namespace App\Filament\Resources\RagKnowledgeSources\Tables;

use App\Models\RagKnowledgeSource;
use App\Services\KnowledgeSourceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RagKnowledgeSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('source_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ready' => 'success',
                        'processing' => 'warning',
                        'failed' => 'danger',
                        'archived' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean(),
                TextColumn::make('chunks_count')
                    ->counts('chunks')
                    ->label('Chunks')
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'processing' => 'Processing',
                        'ready' => 'Ready',
                        'failed' => 'Failed',
                        'archived' => 'Archived',
                    ]),
                SelectFilter::make('source_type')
                    ->options([
                        'upload' => 'Upload',
                        'manual' => 'Manual',
                        'url' => 'URL',
                        'faq' => 'FAQ',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('reindex')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->action(fn (RagKnowledgeSource $record) => app(KnowledgeSourceService::class)->reindex($record)),
                Action::make('publish')
                    ->icon('heroicon-m-eye')
                    ->visible(fn (RagKnowledgeSource $record): bool => ! $record->is_active)
                    ->action(fn (RagKnowledgeSource $record) => app(KnowledgeSourceService::class)->publish($record)),
                Action::make('unpublish')
                    ->icon('heroicon-m-eye-slash')
                    ->visible(fn (RagKnowledgeSource $record): bool => $record->is_active)
                    ->requiresConfirmation()
                    ->action(fn (RagKnowledgeSource $record) => app(KnowledgeSourceService::class)->unpublish($record)),
                Action::make('archive')
                    ->icon('heroicon-m-archive-box')
                    ->visible(fn (RagKnowledgeSource $record): bool => $record->status !== 'archived')
                    ->requiresConfirmation()
                    ->action(fn (RagKnowledgeSource $record) => app(KnowledgeSourceService::class)->archive($record)),
                Action::make('restore')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->visible(fn (RagKnowledgeSource $record): bool => $record->status === 'archived')
                    ->action(fn (RagKnowledgeSource $record) => app(KnowledgeSourceService::class)->restore($record)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

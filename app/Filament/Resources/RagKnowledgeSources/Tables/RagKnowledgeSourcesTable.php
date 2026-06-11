<?php

namespace App\Filament\Resources\RagKnowledgeSources\Tables;

use App\Models\RagKnowledgeSource;
use App\Services\KnowledgeSourceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

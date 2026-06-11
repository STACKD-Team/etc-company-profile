<?php

namespace App\Filament\Resources\Reels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->getStateUsing(fn ($record) => app(\App\Services\MediaStorageService::class)->url($record->thumbnail_path)),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_seconds')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge(),
                TextColumn::make('views_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('likes_count')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'promosi' => 'Promosi',
                        'dokumentasi' => 'Dokumentasi',
                        'edukasi' => 'Edukasi',
                        'testimoni' => 'Testimoni',
                        'event' => 'Event',
                    ]),
                SelectFilter::make('is_published')->options(['1' => 'Published', '0' => 'Draft']),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

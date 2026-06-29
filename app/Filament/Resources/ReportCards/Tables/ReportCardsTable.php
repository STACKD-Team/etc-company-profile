<?php

namespace App\Filament\Resources\ReportCards\Tables;

use App\Models\ReportCard;
use App\Services\ReportCardService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReportCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('enrollment.user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('enrollment.courseClass.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_grade')
                    ->badge(),
                TextColumn::make('next_class')
                    ->searchable(),
                TextColumn::make('instructor.name')
                    ->searchable(),
                TextColumn::make('issued_at')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                IconColumn::make('pdf_path')
                    ->label('PDF')
                    ->boolean()
                    ->getStateUsing(fn (ReportCard $record): bool => filled($record->pdf_path)),
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
                TernaryFilter::make('is_published')
                    ->label('Published'),
                SelectFilter::make('final_grade')
                    ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('publish')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ReportCard $record): bool => ! $record->is_published)
                    ->action(fn (ReportCard $record) => app(ReportCardService::class)->publish($record)),
                Action::make('unpublish')
                    ->icon('heroicon-m-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (ReportCard $record): bool => $record->is_published)
                    ->action(fn (ReportCard $record) => app(ReportCardService::class)->unpublish($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ProgramPromotions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProgramPromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('program.name')->searchable()->sortable(),
                TextColumn::make('discount_type')->badge()->sortable(),
                TextColumn::make('discount_value')->numeric()->sortable(),
                TextColumn::make('badge_label')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('starts_at')->dateTime()->sortable(),
                TextColumn::make('ends_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('program_id')->relationship('program', 'name')->searchable()->preload(),
                SelectFilter::make('discount_type')->options(['percentage' => 'Percentage', 'fixed' => 'Fixed amount']),
                SelectFilter::make('is_active')->options(['1' => 'Active', '0' => 'Inactive']),
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

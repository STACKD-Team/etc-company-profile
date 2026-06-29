<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('program.name')
                    ->searchable(),
                TextColumn::make('courseClass.name')
                    ->label('Class')
                    ->searchable(),
                TextColumn::make('applicant_name')
                    ->searchable(),
                TextColumn::make('applicant_email')
                    ->searchable(),
                TextColumn::make('applicant_phone')
                    ->searchable(),
                TextColumn::make('preferred_days')
                    ->badge(),
                TextColumn::make('preferred_time')
                    ->searchable(),
                TextColumn::make('placement_test_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge(),
                TextColumn::make('payment_amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('final_amount')->money('IDR')->sortable(),
                TextColumn::make('payment_status')->badge()->sortable(),
                TextColumn::make('program_promotion_title')->label('Promo')->toggleable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending_payment' => 'Pending payment',
                        'paid' => 'Paid',
                        'placement_test' => 'Placement test',
                        'enrolled' => 'Enrolled',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'waiting_payment' => 'Waiting payment',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('program_id')->relationship('program', 'name')->searchable()->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

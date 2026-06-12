<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('role')
                    ->badge(),
                TextColumn::make('avatar')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('no_induk')
                    ->searchable(),
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('place_of_birth')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('sex')
                    ->badge(),
                TextColumn::make('religion')
                    ->searchable(),
                TextColumn::make('nationality')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('occupation_school')
                    ->searchable(),
                TextColumn::make('mobile_phone')
                    ->searchable(),
                TextColumn::make('nisn')
                    ->searchable(),
                TextColumn::make('nik')
                    ->searchable(),
                IconColumn::make('kps_receiver')
                    ->boolean(),
                TextColumn::make('no_kps')
                    ->searchable(),
                IconColumn::make('worthy_of_pip')
                    ->boolean(),
                TextColumn::make('no_kip')
                    ->searchable(),
                TextColumn::make('rt_rw')
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->searchable(),
                TextColumn::make('village')
                    ->searchable(),
                TextColumn::make('sub_district')
                    ->searchable(),
                TextColumn::make('district')
                    ->searchable(),
                TextColumn::make('province')
                    ->searchable(),
                TextColumn::make('living_with')
                    ->searchable(),
                TextColumn::make('transportation')
                    ->searchable(),
                TextColumn::make('mother_name')
                    ->searchable(),
                TextColumn::make('father_name')
                    ->searchable(),
                TextColumn::make('instructor_position')
                    ->searchable(),
                TextColumn::make('instructor_specialization')
                    ->searchable(),
                IconColumn::make('show_on_team_page')
                    ->boolean(),
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

<?php

namespace App\Filament\Resources\Registrations\Schemas;

use App\Models\Registration;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('registration_code'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('program.name')
                    ->label('Program'),
                TextEntry::make('class_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('applicant_name'),
                TextEntry::make('applicant_email'),
                TextEntry::make('applicant_phone'),
                TextEntry::make('preferred_days')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('preferred_time')
                    ->placeholder('-'),
                TextEntry::make('placement_test_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('placement_test_result')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('payment_method')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('payment_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('payment_gateway_id')
                    ->placeholder('-'),
                TextEntry::make('payment_proof')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Registration $record): bool => $record->trashed()),
            ]);
    }
}

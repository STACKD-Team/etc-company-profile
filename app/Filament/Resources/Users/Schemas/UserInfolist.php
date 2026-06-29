<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('role')
                    ->badge(),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('no_induk')
                    ->placeholder('-'),
                TextEntry::make('full_name')
                    ->placeholder('-'),
                TextEntry::make('place_of_birth')
                    ->placeholder('-'),
                TextEntry::make('date_of_birth')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('sex')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('religion')
                    ->placeholder('-'),
                TextEntry::make('nationality')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->placeholder('-'),
                TextEntry::make('occupation_school')
                    ->placeholder('-'),
                TextEntry::make('mobile_phone')
                    ->placeholder('-'),
                TextEntry::make('nisn')
                    ->placeholder('-'),
                TextEntry::make('nik')
                    ->placeholder('-'),
                IconEntry::make('kps_receiver')
                    ->boolean(),
                TextEntry::make('no_kps')
                    ->placeholder('-'),
                IconEntry::make('worthy_of_pip')
                    ->boolean(),
                TextEntry::make('pip_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('no_kip')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('rt_rw')
                    ->placeholder('-'),
                TextEntry::make('postal_code')
                    ->placeholder('-'),
                TextEntry::make('village')
                    ->placeholder('-'),
                TextEntry::make('sub_district')
                    ->placeholder('-'),
                TextEntry::make('district')
                    ->placeholder('-'),
                TextEntry::make('province')
                    ->placeholder('-'),
                TextEntry::make('living_with')
                    ->placeholder('-'),
                TextEntry::make('transportation')
                    ->placeholder('-'),
                TextEntry::make('mother_name')
                    ->placeholder('-'),
                TextEntry::make('father_name')
                    ->placeholder('-'),
                TextEntry::make('instructor_position')
                    ->placeholder('-'),
                TextEntry::make('instructor_specialization')
                    ->placeholder('-'),
                TextEntry::make('instructor_bio')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('show_on_team_page')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (User $record): bool => $record->trashed()),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(fn (?string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'instructor' => 'Instructor', 'student' => 'Student'])
                    ->default('student')
                    ->required(),
                TextInput::make('avatar')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('no_induk')
                    ->default(null),
                TextInput::make('full_name')
                    ->default(null),
                TextInput::make('place_of_birth')
                    ->default(null),
                DatePicker::make('date_of_birth'),
                Select::make('sex')
                    ->options(['M' => 'M', 'F' => 'F'])
                    ->default(null),
                TextInput::make('religion')
                    ->default(null),
                TextInput::make('nationality')
                    ->default('Indonesia'),
                TextInput::make('status')
                    ->default(null),
                TextInput::make('occupation_school')
                    ->default(null),
                TextInput::make('mobile_phone')
                    ->tel()
                    ->default(null),
                TextInput::make('nisn')
                    ->default(null),
                TextInput::make('nik')
                    ->default(null),
                Toggle::make('kps_receiver')
                    ->required(),
                TextInput::make('no_kps')
                    ->default(null),
                Toggle::make('worthy_of_pip')
                    ->required(),
                Textarea::make('pip_reason')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('no_kip')
                    ->default(null),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('rt_rw')
                    ->default(null),
                TextInput::make('postal_code')
                    ->default(null),
                TextInput::make('village')
                    ->default(null),
                TextInput::make('sub_district')
                    ->default(null),
                TextInput::make('district')
                    ->default(null),
                TextInput::make('province')
                    ->default(null),
                TextInput::make('living_with')
                    ->default(null),
                TextInput::make('transportation')
                    ->default(null),
                TextInput::make('mother_name')
                    ->default(null),
                TextInput::make('father_name')
                    ->default(null),
                TextInput::make('instructor_position')
                    ->default(null),
                TextInput::make('instructor_specialization')
                    ->default(null),
                Textarea::make('instructor_bio')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('show_on_team_page')
                    ->required(),
            ]);
    }
}

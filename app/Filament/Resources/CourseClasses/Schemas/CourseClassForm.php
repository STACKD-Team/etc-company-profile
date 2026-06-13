<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('program_id')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('instructor_id')
                    ->relationship('instructor', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                TextInput::make('name')->required()->maxLength(150),
                TextInput::make('schedule_days')
                    ->default(null),
                TextInput::make('schedule_time')
                    ->default(null),
                Select::make('room_id')
                    ->label('Room')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                Select::make('status')
                    ->options([
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('upcoming'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('class_id')
                    ->relationship('courseClass', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('enrolled_at')
                    ->required(),
                DatePicker::make('completed_at'),
                Select::make('status')
                    ->options(['active' => 'Active', 'completed' => 'Completed', 'dropped' => 'Dropped'])
                    ->default('active'),
            ]);
    }
}

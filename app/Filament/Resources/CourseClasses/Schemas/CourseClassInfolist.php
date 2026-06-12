<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use App\Models\CourseClass;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseClassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('program.name')
                    ->label('Program'),
                TextEntry::make('instructor.name')
                    ->label('Instructor')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('schedule_days')
                    ->placeholder('-'),
                TextEntry::make('schedule_time')
                    ->placeholder('-'),
                TextEntry::make('room')
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (CourseClass $record): bool => $record->trashed()),
            ]);
    }
}

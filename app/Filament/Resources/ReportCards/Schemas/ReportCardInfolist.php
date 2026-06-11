<?php

namespace App\Filament\Resources\ReportCards\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReportCardInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('enrollment.id')
                    ->label('Enrollment'),
                TextEntry::make('score_listening')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('score_vocabulary')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('score_structure')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('score_reading')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('score_writing')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('grade_pronunciation')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('grade_sentence_arrangement')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('grade_class_participation')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('grade_questioning_skill')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('grade_analyzing_skill')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('total_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('final_grade')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('next_class')
                    ->placeholder('-'),
                TextEntry::make('comments')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('instructor.name')
                    ->label('Instructor')
                    ->placeholder('-'),
                TextEntry::make('academicDirector.name')
                    ->label('Academic director')
                    ->placeholder('-'),
                TextEntry::make('managingDirector.name')
                    ->label('Managing director')
                    ->placeholder('-'),
                TextEntry::make('issued_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('pdf_path')
                    ->placeholder('-'),
                IconEntry::make('is_published')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

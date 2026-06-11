<?php

namespace App\Filament\Resources\ReportCards\Schemas;

use App\Services\MediaStorageService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ReportCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student')
                    ->columns(2)
                    ->schema([
                        Select::make('enrollment_id')
                            ->label('Enrollment')
                            ->relationship(
                                'enrollment',
                                'id',
                                fn (Builder $query) => $query->with(['user', 'courseClass'])
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record): string => trim(($record->user?->name ?? 'Student').' - '.($record->courseClass?->name ?? 'Class').' #'.$record->id))
                            ->searchable(['id'])
                            ->preload()
                            ->required(),
                        DatePicker::make('issued_at'),
                        Select::make('instructor_id')
                            ->relationship('instructor', 'name', fn (Builder $query) => $query->where('role', 'instructor'))
                            ->searchable()
                            ->preload(),
                        TextInput::make('next_class')
                            ->maxLength(150),
                    ]),
                Section::make('Scores')
                    ->columns(3)
                    ->schema([
                        TextInput::make('score_listening')->numeric()->minValue(0),
                        TextInput::make('score_vocabulary')->numeric()->minValue(0),
                        TextInput::make('score_structure')->numeric()->minValue(0),
                        TextInput::make('score_reading')->numeric()->minValue(0),
                        TextInput::make('score_writing')->numeric()->minValue(0),
                        TextInput::make('total_score')->numeric()->minValue(0),
                        Select::make('final_grade')->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
                    ]),
                Section::make('Speaking and participation')
                    ->columns(3)
                    ->schema([
                        Select::make('grade_pronunciation')->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
                        Select::make('grade_sentence_arrangement')->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
                        Select::make('grade_class_participation')->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
                        Select::make('grade_questioning_skill')->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
                        Select::make('grade_analyzing_skill')->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']),
                    ]),
                Section::make('Approval and PDF')
                    ->columns(2)
                    ->schema([
                        Select::make('academic_director_id')
                            ->relationship('academicDirector', 'name', fn (Builder $query) => $query->where('role', 'admin'))
                            ->searchable()
                            ->preload(),
                        Select::make('managing_director_id')
                            ->relationship('managingDirector', 'name', fn (Builder $query) => $query->where('role', 'admin'))
                            ->searchable()
                            ->preload(),
                        FileUpload::make('pdf_path')
                            ->label('PDF file')
                            ->acceptedFileTypes(['application/pdf'])
                            ->visibility('private')
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(MediaStorageService::class)->putUploadedFile($file, 'report-cards/pdfs'))
                            ->deleteUploadedFileUsing(fn (?string $file): null => tap(null, fn () => app(MediaStorageService::class)->delete($file))),
                        Toggle::make('is_published'),
                        Textarea::make('comments')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

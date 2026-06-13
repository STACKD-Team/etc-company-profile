<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enrollment_id',
        'score_listening',
        'score_vocabulary',
        'score_structure',
        'score_reading',
        'score_writing',
        'grade_pronunciation',
        'grade_sentence_arrangement',
        'grade_class_participation',
        'grade_questioning_skill',
        'grade_analyzing_skill',
        'total_score',
        'final_grade',
        'next_class',
        'comments',
        'instructor_id',
        'academic_director_id',
        'managing_director_id',
        'issued_at',
        'pdf_path',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function academicDirector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'academic_director_id');
    }

    public function managingDirector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managing_director_id');
    }
}

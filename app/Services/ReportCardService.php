<?php

namespace App\Services;

use App\Models\ReportCard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class ReportCardService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return ReportCard::class;
    }

    protected function defaultWith(): array
    {
        return ['enrollment.user', 'enrollment.courseClass', 'instructor', 'academicDirector', 'managingDirector'];
    }

    public function attachPdf(ReportCard $reportCard, UploadedFile $pdf): ReportCard
    {
        /** @var ReportCard $reportCard */
        $reportCard = $this->update($reportCard, [
            'pdf_path' => $this->mediaStorage->replace($reportCard->pdf_path, $pdf, 'report-cards/pdfs'),
        ]);

        return $reportCard;
    }

    public function publish(ReportCard $reportCard): ReportCard
    {
        if (! $reportCard->enrollment_id || $reportCard->total_score === null) {
            throw new RuntimeException('A report card needs an enrollment and total score before it can be published.');
        }

        /** @var ReportCard $reportCard */
        $reportCard = $this->update($reportCard, ['is_published' => true]);

        return $reportCard;
    }

    public function unpublish(ReportCard $reportCard): ReportCard
    {
        /** @var ReportCard $reportCard */
        $reportCard = $this->update($reportCard, ['is_published' => false]);

        return $reportCard;
    }

    public function adminPaginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'total_score',
            'is_published',
            'issued_at',
            'created_at',
        ])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function forceDelete(Model $model): bool
    {
        /** @var ReportCard $model */
        $pdfPath = $model->pdf_path;
        $deleted = parent::forceDelete($model);

        if ($deleted) {
            $this->mediaStorage->delete($pdfPath);
        }

        return $deleted;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(function (Builder $query) use ($search): void {
                $query->whereHas('enrollment.user', fn (Builder $query) => $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('full_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhereHas('enrollment.courseClass', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));
            }))
            ->when(array_key_exists('is_published', $filters), fn (Builder $query) => $query->where('is_published', (bool) $filters['is_published']))
            ->when($filters['enrollment_id'] ?? null, fn (Builder $query, int|string $enrollmentId) => $query->where('enrollment_id', $enrollmentId))
            ->when($filters['instructor_id'] ?? null, fn (Builder $query, int|string $instructorId) => $query->where('instructor_id', $instructorId));

        return $this->whereDateRange($query, 'issued_at', $filters, 'issued_from', 'issued_to');
    }
}

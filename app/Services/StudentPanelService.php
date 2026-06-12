<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class StudentPanelService
{
    public function paginateClasses(int $studentId, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->enrollmentsQuery($studentId)
            ->with(['courseClass.program', 'courseClass.instructor', 'reportCard'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $this->applyEnrollmentSearch($query, $search))
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->whereHas('courseClass', fn (Builder $query) => $query->where('program_id', $programId)))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId))
            ->when($filters['instructor_id'] ?? null, fn (Builder $query, int|string $instructorId) => $query->whereHas('courseClass', fn (Builder $query) => $query->where('instructor_id', $instructorId)))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        return $this->applySort($query, $filters, [
            'enrolled_at' => 'enrolled_at',
            'completed_at' => 'completed_at',
            'status' => 'status',
            'created_at' => 'created_at',
        ], 'enrolled_at')->paginate($this->perPage($filters, $perPage))->withQueryString();
    }

    public function paginateLearningHistory(int $studentId, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->paginateClasses($studentId, $filters, $perPage);
    }

    public function paginatePayments(int $studentId, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->paymentsQuery($studentId)
            ->with(['program', 'courseClass', 'paymentPromotion'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('registration_code', 'like', '%'.$search.'%')
                        ->orWhere('applicant_name', 'like', '%'.$search.'%')
                        ->orWhere('payment_gateway_id', 'like', '%'.$search.'%')
                        ->orWhere('payment_promotion_title', 'like', '%'.$search.'%')
                        ->orWhereHas('program', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('courseClass', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->where('program_id', $programId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId))
            ->when($filters['payment_method'] ?? null, fn (Builder $query, string $method) => $query->where('payment_method', $method))
            ->when($filters['payment_status'] ?? null, function (Builder $query, string $status): void {
                $query->where(function (Builder $query) use ($status): void {
                    $query->where('payment_status', $status)
                        ->orWhere(function (Builder $query) use ($status): void {
                            $query->whereNull('payment_status')
                                ->whereIn('status', $this->registrationStatusesForPaymentStatus($status));
                        });
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['paid_at'] ?? null, fn (Builder $query, string $date) => $query->whereDate('paid_at', $date));

        return $this->applySort($query, $filters, [
            'created_at' => 'created_at',
            'paid_at' => 'paid_at',
            'payment_amount' => 'payment_amount',
            'payment_status' => 'payment_status',
            'status' => 'status',
        ], 'created_at')->paginate($this->perPage($filters, $perPage))->withQueryString();
    }

    public function paginateReportCards(int $studentId, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = ReportCard::query()
            ->with(['enrollment.courseClass.program', 'enrollment.courseClass.instructor', 'instructor'])
            ->where('is_published', true)
            ->whereHas('enrollment', fn (Builder $query) => $query->where('user_id', $studentId))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('next_class', 'like', '%'.$search.'%')
                        ->orWhere('comments', 'like', '%'.$search.'%')
                        ->orWhereHas('enrollment.courseClass', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('enrollment.courseClass.program', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->whereHas('enrollment.courseClass', fn (Builder $query) => $query->where('program_id', $programId)))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->whereHas('enrollment', fn (Builder $query) => $query->where('class_id', $classId)))
            ->when($filters['instructor_id'] ?? null, fn (Builder $query, int|string $instructorId) => $query->whereHas('enrollment.courseClass', fn (Builder $query) => $query->where('instructor_id', $instructorId)))
            ->when($filters['final_grade'] ?? null, fn (Builder $query, string $grade) => $query->where('final_grade', $grade))
            ->when($filters['issued_at'] ?? null, fn (Builder $query, string $date) => $query->whereDate('issued_at', $date))
            ->when($filters['report_status'] ?? null, function (Builder $query, string $status): void {
                match ($status) {
                    'with_file' => $query->whereNotNull('pdf_path'),
                    'without_file' => $query->whereNull('pdf_path'),
                    default => $query,
                };
            });

        return $this->applySort($query, $filters, [
            'issued_at' => 'issued_at',
            'total_score' => 'total_score',
            'final_grade' => 'final_grade',
        ], 'issued_at')->paginate($this->perPage($filters, $perPage))->withQueryString();
    }

    public function ownedClassEnrollment(int $studentId, CourseClass $class): Enrollment
    {
        return $this->enrollmentsQuery($studentId)
            ->with(['courseClass.program', 'courseClass.instructor', 'reportCard'])
            ->where('class_id', $class->id)
            ->firstOrFail();
    }

    public function ownedPayment(int $studentId, Registration $payment): Registration
    {
        abort_unless((int) $payment->user_id === $studentId, 403);

        return $payment->load(['program', 'courseClass', 'paymentPromotion']);
    }

    public function ownedPublishedReportCard(int $studentId, ReportCard $reportCard): ReportCard
    {
        $reportCard->load([
            'enrollment.courseClass.program',
            'enrollment.courseClass.instructor',
            'instructor',
            'academicDirector',
            'managingDirector',
        ]);

        abort_unless($reportCard->is_published && (int) $reportCard->enrollment?->user_id === $studentId, 403);

        return $reportCard;
    }

    public function programOptions(int $studentId): array
    {
        return Program::query()
            ->whereHas('classes.enrollments', fn (Builder $query) => $query->where('user_id', $studentId))
            ->orWhereHas('registrations', fn (Builder $query) => $query->where('user_id', $studentId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function classOptions(int $studentId): array
    {
        return CourseClass::query()
            ->whereHas('enrollments', fn (Builder $query) => $query->where('user_id', $studentId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function instructorOptions(int $studentId): array
    {
        return User::query()
            ->instructors()
            ->whereHas('classesTaught.enrollments', fn (Builder $query) => $query->where('user_id', $studentId))
            ->orderByRaw('coalesce(full_name, name)')
            ->get(['id', 'name', 'full_name'])
            ->mapWithKeys(fn (User $instructor) => [$instructor->id => $instructor->full_name ?: $instructor->name])
            ->all();
    }

    public function reportProgramOptions(int $studentId): array
    {
        return Program::query()
            ->whereHas('classes.enrollments.reportCard', function (Builder $query) use ($studentId): void {
                $query
                    ->where('is_published', true)
                    ->whereHas('enrollment', fn (Builder $query) => $query->where('user_id', $studentId));
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function reportClassOptions(int $studentId): array
    {
        return CourseClass::query()
            ->whereHas('enrollments.reportCard', function (Builder $query) use ($studentId): void {
                $query
                    ->where('is_published', true)
                    ->whereHas('enrollment', fn (Builder $query) => $query->where('user_id', $studentId));
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function reportInstructorOptions(int $studentId): array
    {
        return User::query()
            ->instructors()
            ->whereHas('classesTaught.enrollments.reportCard', function (Builder $query) use ($studentId): void {
                $query
                    ->where('is_published', true)
                    ->whereHas('enrollment', fn (Builder $query) => $query->where('user_id', $studentId));
            })
            ->orderByRaw('coalesce(full_name, name)')
            ->get(['id', 'name', 'full_name'])
            ->mapWithKeys(fn (User $instructor) => [$instructor->id => $instructor->full_name ?: $instructor->name])
            ->all();
    }

    public function paymentStatus(Registration $payment): string
    {
        return $payment->payment_status ?: match ($payment->status) {
            'paid', 'placement_test', 'enrolled' => 'paid',
            'cancelled' => 'cancelled',
            'rejected' => 'failed',
            default => 'waiting_payment',
        };
    }

    public function paymentSummary(Registration $payment): array
    {
        $original = (float) ($payment->payment_original_amount ?? $payment->payment_amount ?? 0);
        $discount = (float) ($payment->payment_discount_amount ?? 0);
        $final = (float) ($payment->payment_final_amount ?? max($original - $discount, 0));
        $status = $this->paymentStatus($payment);

        return [
            'status' => $status,
            'label' => $this->statusLabels()[$status] ?? str($status)->replace('_', ' ')->headline()->toString(),
            'color' => $this->statusColors()[$status] ?? 'primary',
            'method' => $this->methods()[$payment->payment_method] ?? ($payment->payment_method ?: 'Metode belum dipilih'),
            'original_amount' => $original,
            'discount_amount' => $discount,
            'final_amount' => $final,
            'promotion_title' => $payment->payment_promotion_title ?: $payment->paymentPromotion?->title,
            'can_continue' => filled($payment->payment_redirect_url)
                && in_array($status, ['waiting_payment', 'pending_payment'], true)
                && (! $payment->payment_expires_at || $payment->payment_expires_at->isFuture()),
            'expires_at' => $payment->payment_expires_at,
        ];
    }

    public function statusLabels(): array
    {
        return [
            'pending_payment' => 'Menunggu Pembayaran',
            'waiting_payment' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'placement_test' => 'Menunggu Placement Test',
            'enrolled' => 'Aktif Belajar',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
            'failed' => 'Gagal',
            'active' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'dropped' => 'Berhenti',
            'published' => 'Published',
            'with_file' => 'File tersedia',
            'without_file' => 'Belum ada file',
        ];
    }

    public function statusColors(): array
    {
        return [
            'pending_payment' => 'warning',
            'waiting_payment' => 'warning',
            'paid' => 'success',
            'placement_test' => 'warning',
            'enrolled' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'danger',
            'expired' => 'danger',
            'failed' => 'danger',
            'active' => 'success',
            'completed' => 'info',
            'dropped' => 'danger',
            'published' => 'success',
            'with_file' => 'success',
            'without_file' => 'gray',
        ];
    }

    public function methods(): array
    {
        return [
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'virtual_account' => 'Virtual Account',
            'ewallet' => 'E-Wallet',
            'manual' => 'Manual',
        ];
    }

    public function paymentStatusOptions(): array
    {
        return [
            'waiting_payment' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'expired' => 'Kedaluwarsa',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan',
        ];
    }

    private function enrollmentsQuery(int $studentId): Builder
    {
        return Enrollment::query()->where('user_id', $studentId);
    }

    private function paymentsQuery(int $studentId): Builder
    {
        return Registration::query()
            ->where('user_id', $studentId)
            ->where(function (Builder $query): void {
                $query->whereNotNull('payment_amount')
                    ->orWhereNotNull('payment_original_amount')
                    ->orWhereNotNull('payment_final_amount')
                    ->orWhereNotNull('payment_method')
                    ->orWhereNotNull('payment_gateway_id')
                    ->orWhereNotNull('payment_proof')
                    ->orWhereNotNull('paid_at')
                    ->orWhereIn('status', ['pending_payment', 'paid', 'placement_test', 'enrolled', 'rejected', 'cancelled']);
            });
    }

    private function applyEnrollmentSearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $query) use ($search): void {
            $query
                ->whereHas('courseClass', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('schedule_days', 'like', '%'.$search.'%')
                        ->orWhere('schedule_time', 'like', '%'.$search.'%')
                        ->orWhere('room', 'like', '%'.$search.'%');
                })
                ->orWhereHas('courseClass.program', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('courseClass.instructor', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('full_name', 'like', '%'.$search.'%');
                });
        });
    }

    private function applySort(Builder $query, array $filters, array $sortable, string $defaultSort): Builder
    {
        $sort = $sortable[$filters['sort'] ?? ''] ?? $sortable[$defaultSort];
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $direction);
    }

    private function perPage(array $filters, int $default): int
    {
        return in_array((int) ($filters['per_page'] ?? $default), [10, 20, 50], true)
            ? (int) ($filters['per_page'] ?? $default)
            : $default;
    }

    private function registrationStatusesForPaymentStatus(string $status): array
    {
        return match ($status) {
            'paid' => ['paid', 'placement_test', 'enrolled'],
            'failed' => ['rejected'],
            'cancelled' => ['cancelled'],
            default => ['pending_payment'],
        };
    }
}

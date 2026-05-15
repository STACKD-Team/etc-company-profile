<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class RegistrationService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return Registration::class;
    }

    protected function defaultWith(): array
    {
        return ['user', 'program', 'courseClass'];
    }

    public function createFromOnlineForm(array $applicantData): Registration
    {
        /** @var Registration $registration */
        $registration = $this->create($applicantData + [
            'status' => 'pending_payment',
        ]);

        return $registration;
    }

    public function uploadPaymentProof(Registration $registration, UploadedFile $proof): Registration
    {
        /** @var Registration $registration */
        $registration = $this->update($registration, [
            'payment_proof' => $this->mediaStorage->replace($registration->payment_proof, $proof, 'registrations/payment-proofs'),
        ]);

        return $registration;
    }

    public function markAsPaid(Registration $registration, ?float $amount = null, ?string $paymentMethod = null, bool $adminOverride = false): Registration
    {
        if (! $adminOverride && ! $registration->payment_proof && ! $registration->payment_gateway_id) {
            throw new RuntimeException('A registration needs payment proof or a gateway transaction before it can be marked as paid.');
        }

        $data = [
            'status' => 'paid',
            'paid_at' => now(),
        ];

        if ($amount !== null) {
            $data['payment_amount'] = $amount;
        }

        if ($paymentMethod !== null) {
            $data['payment_method'] = $paymentMethod;
        }

        /** @var Registration $registration */
        $registration = $this->update($registration, $data);

        return $registration;
    }

    public function createEnrollmentFromRegistration(Registration $registration, ?string $enrolledAt = null): Enrollment
    {
        if (! $registration->user_id || ! $registration->class_id) {
            throw new RuntimeException('A registration needs a user and class before enrollment can be created.');
        }

        return Enrollment::query()->firstOrCreate(
            [
                'user_id' => $registration->user_id,
                'class_id' => $registration->class_id,
            ],
            [
                'enrolled_at' => $enrolledAt ?? now()->toDateString(),
                'status' => 'active',
            ],
        );
    }

    public function forceDelete(Model $model): bool
    {
        /** @var Registration $model */
        $paymentProof = $model->payment_proof;
        $deleted = parent::forceDelete($model);

        if ($deleted) {
            $this->mediaStorage->delete($paymentProof);
        }

        return $deleted;
    }

    public function schedulePlacementTest(Registration $registration, string $scheduledAt): Registration
    {
        /** @var Registration $registration */
        $registration = $this->update($registration, [
            'placement_test_at' => $scheduledAt,
            'status' => 'placement_test',
        ]);

        return $registration;
    }

    public function assignClass(Registration $registration, CourseClass|int $courseClass): Registration
    {
        $classId = $courseClass instanceof CourseClass ? $courseClass->getKey() : $courseClass;

        /** @var Registration $registration */
        $registration = $this->update($registration, [
            'class_id' => $classId,
            'status' => 'enrolled',
        ]);

        return $registration;
    }

    public function cancel(Registration $registration, ?string $notes = null): Registration
    {
        /** @var Registration $registration */
        $registration = $this->update($registration, array_filter([
            'status' => 'cancelled',
            'notes' => $notes,
        ], fn ($value) => $value !== null));

        return $registration;
    }

    public function reject(Registration $registration, ?string $notes = null): Registration
    {
        /** @var Registration $registration */
        $registration = $this->update($registration, array_filter([
            'status' => 'rejected',
            'notes' => $notes,
        ], fn ($value) => $value !== null));

        return $registration;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = $this->whereLike($query, ['applicant_name', 'applicant_email'], $filters['search'] ?? null)
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['payment_method'] ?? null, fn (Builder $query, string $method) => $query->where('payment_method', $method))
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->where('program_id', $programId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId));

        return $this->whereDateRange($query, 'created_at', $filters, 'created_from', 'created_to');
    }
}

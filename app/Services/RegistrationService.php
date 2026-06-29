<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RegistrationService extends BaseCrudService
{
    public function __construct(
        protected MidtransPaymentService $midtransPayment,
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
        if (! isset($applicantData['full_name'])) {
            /** @var Registration $registration */
            $registration = $this->create($applicantData + [
                'registration_code' => $this->generateRegistrationCode(),
                'status' => 'pending_payment',
            ]);

            return $registration;
        }

        return DB::transaction(function () use ($applicantData): Registration {
            $program = Program::query()->with('activePromotions')->findOrFail($applicantData['program_id']);
            $user = $this->storeStudentProfile($applicantData);
            $paymentSnapshot = $this->midtransPayment->snapshotAmount($program);

            /** @var Registration $registration */
            $registration = Registration::query()->create([
                'registration_code' => $this->generateRegistrationCode(),
                'user_id' => $user->id,
                'program_id' => $program->id,
                'applicant_name' => $applicantData['full_name'],
                'applicant_email' => $applicantData['email'],
                'applicant_phone' => $applicantData['mobile_phone'],
                'preferred_days' => $applicantData['preferred_days'],
                'preferred_time' => $applicantData['preferred_time'],
                ...$paymentSnapshot,
                'payment_status' => 'waiting_payment',
                'status' => 'pending_payment',
                'notes' => json_encode([
                    'applying_for' => $applicantData['applying_for'],
                    'submitted_from' => 'online_registration',
                    'submitted_at' => now()->toIso8601String(),
                ], JSON_THROW_ON_ERROR),
            ]);

            return $this->midtransPayment
                ->createTransaction($registration)
                ->load($this->defaultWith());
        });
    }

    public function receiptData(Registration $registration): array
    {
        $registration->loadMissing(['user', 'program']);

        return [
            'code' => $registration->registration_code,
            'student' => $registration->applicant_name,
            'email' => $registration->applicant_email,
            'phone' => $registration->applicant_phone,
            'program' => $registration->program?->name ?? '-',
            'preferred_days' => $registration->preferred_days,
            'preferred_time' => $registration->preferred_time,
            'payment_method' => $registration->payment_method,
            'payment_amount' => $registration->payment_amount,
            'status' => $registration->status,
            'submitted_at' => $registration->created_at,
        ];
    }

    public function paginateAdminRegistrations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->applyFilters($this->baseQuery(), $filters), $filters, [
            'created_at',
            'registration_code',
            'applicant_name',
            'applicant_email',
            'status',
            'program_id',
            'payment_amount',
            'paid_at',
        ])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginatePaymentVerifications(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->baseQuery()
            ->where(function (Builder $query): void {
                $query->whereNotNull('payment_method')
                    ->orWhereNotNull('payment_gateway_id')
                    ->orWhereNotNull('midtrans_order_id');
            });

        return $this->applySorting($this->applyFilters($query, $filters), $filters, [
            'created_at',
            'applicant_name',
            'status',
            'payment_status',
            'program_id',
            'payment_method',
            'payment_amount',
            'final_amount',
            'paid_at',
        ])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function markAsPaid(Registration $registration, ?float $amount = null, ?string $paymentMethod = null, bool $adminOverride = false): Registration
    {
        if (! $adminOverride && ! $registration->payment_gateway_id && ! $registration->midtrans_order_id) {
            throw new RuntimeException('A registration needs a Midtrans gateway transaction before it can be marked as paid.');
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
        return parent::forceDelete($model);
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
        $query = $this->whereLike($query, ['registration_code', 'applicant_name', 'applicant_email', 'midtrans_order_id', 'payment_gateway_id', 'program_promotion_title'], $filters['search'] ?? null)
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn (Builder $query, string $status) => $query->where('payment_status', $status))
            ->when($filters['payment_method'] ?? null, fn (Builder $query, string $method) => $query->where('payment_method', $method))
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->where('program_id', $programId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId));

        return $this->whereDateRange($query, 'created_at', $filters, 'created_from', 'created_to');
    }

    protected function storeStudentProfile(array $data): User
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $data['email'])->first();

        if ($user && $user->role !== 'student') {
            throw new RuntimeException('Email ini sudah dipakai oleh akun non-siswa.');
        }

        $profileData = [
            'name' => $data['full_name'],
            'email' => $data['email'],
            'role' => 'student',
            'is_active' => true,
            'full_name' => $data['full_name'],
            'place_of_birth' => $data['place_of_birth'],
            'date_of_birth' => $data['date_of_birth'],
            'sex' => $data['sex'],
            'religion' => $data['religion'],
            'nationality' => $data['nationality'],
            'occupation_school' => $data['occupation_school'],
            'mobile_phone' => $data['mobile_phone'],
            'nisn' => $data['nisn'] ?? null,
            'nik' => $data['nik'] ?? null,
            'kps_receiver' => (bool) $data['kps_receiver'],
            'no_kps' => $data['no_kps'] ?? null,
            'worthy_of_pip' => (bool) $data['worthy_of_pip'],
            'pip_reason' => $data['pip_reason'] ?? null,
            'no_kip' => $data['no_kip'] ?? null,
            'address' => $data['address'],
            'rt_rw' => $data['rt_rw'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'village' => $data['village'] ?? null,
            'sub_district' => $data['sub_district'] ?? null,
            'district' => $data['district'] ?? null,
            'province' => $data['province'] ?? null,
            'living_with' => $data['living_with'] ?? null,
            'transportation' => $data['transportation'] ?? null,
            'mother_name' => $data['mother_name'],
            'father_name' => $data['father_name'],
        ];

        if (! $user) {
            $profileData['password'] = Str::password(24);

            return User::query()->create($profileData);
        }

        $user->update($profileData);

        return $user->refresh();
    }

    protected function generateRegistrationCode(): string
    {
        do {
            $code = 'REG-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Registration::query()->where('registration_code', $code)->exists());

        return $code;
    }

    protected function decodeNotes(?string $notes): array
    {
        if (! $notes) {
            return [];
        }

        $decoded = json_decode($notes, true);

        return is_array($decoded) ? $decoded : ['notes' => $notes];
    }
}

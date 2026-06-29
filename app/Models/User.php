<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'avatar',
        'is_active',
        'no_induk',
        'full_name',
        'place_of_birth',
        'date_of_birth',
        'sex',
        'religion',
        'nationality',
        'status',
        'occupation_school',
        'mobile_phone',
        'nisn',
        'nik',
        'kps_receiver',
        'no_kps',
        'worthy_of_pip',
        'pip_reason',
        'no_kip',
        'address',
        'rt_rw',
        'postal_code',
        'village',
        'sub_district',
        'district',
        'province',
        'living_with',
        'transportation',
        'mother_name',
        'father_name',
        'instructor_position',
        'instructor_specialization',
        'instructor_bio',
        'show_on_team_page',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'date_of_birth' => 'date',
            'kps_receiver' => 'boolean',
            'worthy_of_pip' => 'boolean',
            'show_on_team_page' => 'boolean',
        ];
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classesTaught(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'instructor_id');
    }

    public function instructorReportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class, 'instructor_id');
    }

    public function academicDirectorReportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class, 'academic_director_id');
    }

    public function managingDirectorReportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class, 'managing_director_id');
    }

    public function chatbotLogs(): HasMany
    {
        return $this->hasMany(ChatbotLog::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && $this->role === 'admin'
            && (bool) $this->is_active;
    }
}

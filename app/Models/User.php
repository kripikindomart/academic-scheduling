<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'email_verified_at',
        'program_study_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the program study that the user belongs to.
     */
    public function programStudy()
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    /**
     * Scope a query to only include users from a specific program study.
     */
    public function scopeByProgramStudy($query, $programStudyId = null)
    {
        if (auth()->user() && auth()->user()->canAccessAllProgramStudies()) {
            // Admin bisa lihat semua data
            return $query;
        }

        // User biasa hanya lihat data program studinya sendiri
        $targetProgramStudyId = $programStudyId ?? auth()->user()->program_study_id;
        return $query->where('program_study_id', $targetProgramStudyId);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole(['admin', 'Super Admin', 'super admin']);
    }

    /**
     * Check if user can access all program studies.
     */
    public function canAccessAllProgramStudies()
    {
        return $this->isAdmin();
    }

    /**
     * Get the program study ID that this user can access.
     */
    public function getAccessibleProgramStudyId()
    {
        if ($this->canAccessAllProgramStudies()) {
            return null; // Admin can access all
        }

        return $this->program_study_id;
    }
}

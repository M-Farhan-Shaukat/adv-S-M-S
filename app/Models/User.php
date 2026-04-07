<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
        'role_id',
        'is_active',
        'age',
        'phone',
        'cnic',
        'postal_code',
        'email_verification_token',
        'email_verified_at'
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
        ];
    }
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Keep old role() for backward compat (Spatie Role model)
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    // School-specific relations
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function staffMember()
    {
        return $this->hasOne(Staff::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function meetings()
    {
        return $this->hasMany(MeetingSchedule::class, 'scheduled_by');
    }

    // Helper: get primary role name (lowercase)
    public function primaryRole(): string
    {
        return strtolower($this->getRoleNames()->first() ?? '');
    }

    // Helper: check role case-insensitive
    public function hasSchoolRole(string $role): bool
    {
        return $this->getRoleNames()->map(fn($r) => strtolower($r))->contains(strtolower($role));
    }

    // Spatie-compatible permission check used in old views
    public function hasPermission($permission): bool
    {
        $roles = $this->getRoleNames()->map(fn($r) => strtolower($r));
        if ($roles->intersect(['admin', 'principal'])->isNotEmpty()) return true;
        return $this->hasPermissionTo($permission);
    }



}

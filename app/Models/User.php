<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($this->role->slug, $roles);
        }

        return $this->role->slug === $roles;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->user_type === 'super_admin';
    }

    public function isBranchManager(): bool
    {
        return $this->hasRole('branch_manager') || $this->user_type === 'branch_manager';
    }

    public function isCounsellor(): bool
    {
        return $this->hasRole('counsellor') || $this->user_type === 'counsellor';
    }

    public function isAccountant(): bool
    {
        return $this->hasRole('accountant') || $this->user_type === 'accountant';
    }

    public function isFaculty(): bool
    {
        return $this->hasRole('faculty') || $this->user_type === 'faculty';
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student') || $this->user_type === 'student';
    }

    public function isParent(): bool
    {
        return $this->hasRole('parent') || $this->user_type === 'parent';
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions->contains('slug', $permissionSlug);
    }
}

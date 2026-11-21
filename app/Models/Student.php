<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function latestAdmission()
    {
        return $this->hasOne(Admission::class)->latestOfMany();
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_students')
                    ->withPivot(['roll_no', 'enrolled_at', 'status'])
                    ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function feeDues()
    {
        return $this->hasMany(StudentFeeDue::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(StudentExamAttempt::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}


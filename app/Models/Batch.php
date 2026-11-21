<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'primary_mentor_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'batch_students')
                    ->withPivot(['roll_no', 'enrolled_at', 'status'])
                    ->withTimestamps();
    }

    public function faculty()
    {
        return $this->belongsToMany(User::class, 'batch_faculty', 'batch_id', 'faculty_id')
                    ->withPivot('subject_id')
                    ->withTimestamps();
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}


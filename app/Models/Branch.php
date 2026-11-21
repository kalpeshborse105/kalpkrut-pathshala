<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}


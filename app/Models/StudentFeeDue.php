<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeDue extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_fee_due_id');
    }
}


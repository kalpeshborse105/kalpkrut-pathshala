<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
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

    public function feeDue()
    {
        return $this->belongsTo(StudentFeeDue::class, 'student_fee_due_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}


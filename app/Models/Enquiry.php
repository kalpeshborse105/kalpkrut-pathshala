<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function source()
    {
        return $this->belongsTo(EnquirySource::class, 'enquiry_source_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'interested_course_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function followups()
    {
        return $this->hasMany(EnquiryFollowup::class)->orderBy('followup_date', 'desc');
    }

    public function counsellingSessions()
    {
        return $this->hasMany(CounsellingSession::class);
    }

    public function demoClasses()
    {
        return $this->hasMany(DemoClass::class);
    }

    public function admission()
    {
        return $this->hasOne(Admission::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounsellingSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function counsellor()
    {
        return $this->belongsTo(User::class, 'counsellor_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }
}


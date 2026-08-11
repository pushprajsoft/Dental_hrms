<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdPayment extends Model
{
    protected $fillable = [
        'opd_visit_id',
        'method',
        'amount',
        'reference_no',
    ];

    public function visit()
    {
        return $this->belongsTo(OpdVisit::class, 'opd_visit_id');
    }
}
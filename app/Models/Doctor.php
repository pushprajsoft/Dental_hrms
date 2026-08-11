<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_code',
        'full_name',
        'specialization',
        'qualification',
        'phone',
        'email',
        'experience_years',
        'joining_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    /**
     * Auto-generate a code like DOC-0001, DOC-0002...
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($doctor) {
            $lastId = static::max('id') ?? 0;
            $nextId = $lastId + 1;
            $doctor->doctor_code = 'DOC-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }
}
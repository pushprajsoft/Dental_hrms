<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_code', 'patient_id', 'doctor_id', 'test_name', 'status', 
        'result', 'unit', 'reference_range', 'notes'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($test) {
            $lastId = static::max('id') ?? 0;
            $test->test_code = 'LAB-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
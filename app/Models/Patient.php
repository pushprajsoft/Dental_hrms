<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_code',
        'full_name',
        'gender',
        'date_of_birth',
        'age',               // ADDED
        'phone',
        'email',
        'aadhar',            // ADDED
        'address',
        'blood_group',
        'chief_complaint',
        'treatment_plan',
        'doctor_name',
        'status',
        'mlc',               // ADDED
        'fh_name',           // ADDED
        'mother_name',       // ADDED
        'marital_status',    // ADDED
        'rel_name',          // ADDED
        'rel_relation',      // ADDED
        'rel_contact',       // ADDED
        'rel_address',       // ADDED
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            $lastId = static::max('id') ?? 0;
            $nextId = $lastId + 1;
            $patient->patient_code = 'PT-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }

    public function visits()
    {
        return $this->hasMany(OpdVisit::class, 'patient_id')->latest('visit_date');
    }
}
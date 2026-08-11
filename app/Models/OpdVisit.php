<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        'visit_type',
        'token_number',
        'mlc',
        'referred_by',
        'chief_complaint',
        'height_cm',
        'weight_kg',
        'blood_pressure',
        'pulse_rate',
        'temperature',
        'spo2',
        'symptoms',
        'consultation_fee',
        'other_charges',
        'discount',
        'total_amount',
        'amount_paid',
        'payment_method',
        'payment_date',
        'refund_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'visit_date'       => 'date',
        'payment_date'     => 'date',
        'consultation_fee' => 'decimal:2',
        'other_charges'    => 'decimal:2',
        'discount'         => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'amount_paid'      => 'decimal:2',
        'refund_amount'    => 'decimal:2',
        'height_cm'        => 'decimal:1',
        'weight_kg'        => 'decimal:1',
        'temperature'      => 'decimal:1',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($visit) {
            $lastId = static::max('id') ?? 0;
            $nextId = $lastId + 1;
            $visit->visit_code = 'OPD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
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

    public function payments()
    {
        return $this->hasMany(OpdPayment::class, 'opd_visit_id');
    }

    public function getNetCollectionAttribute()
    {
        return $this->amount_paid - $this->refund_amount;
    }

    public function getBalanceDueAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }
}
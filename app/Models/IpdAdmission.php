<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpdAdmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'ipd_code', 'admission_date', 'registered_type', 'patient_id', 'scheme_type', 'scheme_name', 
        'case_type', 'bill_category', 'corporate', 'esic_no', 'urn_no', 'admission_note', 'referral_doctor', 'remark',
        'p_name', 'p_gender', 'p_dob', 'p_age', 'p_mobile', 'p_aadhar', 'p_address', 'p_mlc', 'p_fh_name', 'p_mother_name', 'p_marital_status',
        'rel_name', 'rel_relation', 'rel_contact', 'rel_address',
        'doctor_id', 'attending_doctor_id', 'bed_id', 'allotment_date', 'advance_paid', 'payment_method', 'refund_amount', 'status', 'discharge_date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ipd) {
            $lastId = static::max('id') ?? 0;
            $nextId = $lastId + 1;
            $ipd->ipd_code = 'IPD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function attendingDoctor()
    {
        return $this->belongsTo(Doctor::class, 'attending_doctor_id');
    }

    // BED RELATIONSHIP ADDED
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }
}
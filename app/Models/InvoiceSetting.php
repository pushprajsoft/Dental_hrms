<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name', 
        'clinic_address', 
        'clinic_phone', 
        'clinic_email', 
        'clinic_state', // <-- ADDED THIS
        'gst_number', 
        'gst_enabled', 
        'gst_percentage', 
        'footer_notes'
    ];

    // Helper to always get the first setting record
    public static function current()
    {
        return self::firstOrCreate(['id' => 1], [
            'clinic_name' => 'DentaCare Clinic',
            'clinic_state' => 'Maharashtra', // <-- ADDED DEFAULT STATE
            'gst_percentage' => 18.00
        ]);
    }
}
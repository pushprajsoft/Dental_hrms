<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_name', 'logo_path', 'hospital_address', 'hospital_phone', 'hospital_email', 
        'gst_number', 'header_html', 'footer_html'
    ];

    public static function current()
    {
        return self::firstOrCreate(['id' => 1], [
            'hospital_name' => 'Shubh-HMS',
            'header_html' => '<h2>DentaCare Clinic</h2><p>Multi-Speciality Dental Hospital</p>',
            'footer_html' => '<p><strong>Terms:</strong> All payments are non-refundable.</p>'
        ]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'reason',
        'status',
        'reminder_sent',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'reminder_sent'    => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            $lastId = static::max('id') ?? 0;
            $appointment->appointment_code = 'APT-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
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

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                      ->whereIn('status', ['Scheduled', 'Confirmed']);
    }

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->appointment_time)->format('h:i A');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Scheduled' => 'status-scheduled',
            'Confirmed' => 'status-confirmed',
            'Completed' => 'status-completed',
            'Cancelled' => 'status-cancelled',
            'No-Show'   => 'status-noshow',
            default     => 'status-scheduled',
        };
    }

    public function getWhatsappReminderLinkAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->patient->phone ?? '');
        $doctorName = $this->doctor->full_name ?? 'our doctor';

        $message = "Hello {$this->patient->full_name}, this is a reminder from DentaCare Clinic for your appointment on "
            . $this->appointment_date->format('d M Y') . " at {$this->formatted_time} with Dr. {$doctorName}. "
            . "Please arrive 10 minutes early. Reply to confirm. Thank you!";

        return "https://wa.me/91{$phone}?text=" . urlencode($message);
    }
}
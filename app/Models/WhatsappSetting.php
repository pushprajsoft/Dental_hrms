<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'country_code',
        'support_number',
        'thank_you_template',
        'auto_schedule_enabled',
        'scheduled_time',
    ];

    protected $casts = [
        'auto_schedule_enabled' => 'boolean',
    ];

    /**
     * There's only ever one settings row — fetch it, or create a
     * sensible default one the first time it's needed.
     */
    public static function current(): self
    {
        return self::firstOrCreate([], [
            'country_code'           => '91',
            'support_number'         => '',
            'thank_you_template'     => 'Hi {name}, thank you for visiting us! We hope you have a speedy recovery.',
            'auto_schedule_enabled'  => false,
            'scheduled_time'         => '21:00:00',
        ]);
    }

    /**
     * Strip spaces/dashes/plus signs and make sure the country code
     * is on the front of the number, so wa.me links always work.
     * Accepts null safely (returns an empty string) so a settings
     * row with no support number yet doesn't crash the page.
     */
    protected function cleanNumber(?string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number ?? '');

        if ($number !== '' && !str_starts_with($number, $this->country_code)) {
            $number = $this->country_code . $number;
        }

        return $number;
    }

    /**
     * Build a wa.me link pre-filled with the thank-you message for a
     * specific patient (name swapped into the template).
     */
    public function thankYouLinkFor(string $phone, string $patientName): string
    {
        $number = $this->cleanNumber($phone);

        $message = str_replace('{name}', $patientName, $this->thank_you_template);

        return "https://wa.me/{$number}?text=" . urlencode($message);
    }

    /**
     * Build a wa.me link to the clinic's own support number (no patient name).
     */
    public function supportLink(): string
    {
        $number = $this->cleanNumber($this->support_number);

        return "https://wa.me/{$number}";
    }

    /**
     * Nicely formatted scheduled time for display, e.g. "9:00 PM".
     */
    public function scheduledTimeFormatted(): string
    {
        return \Carbon\Carbon::parse($this->scheduled_time)->format('g:i A');
    }
}
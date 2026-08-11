<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    /**
     * Show the WhatsApp settings + list of patients still waiting
     * on their scheduled thank-you message.
     */
    public function edit()
    {
        $settings = WhatsappSetting::current();

        // Patients registered in the last 3 days who haven't been
        // messaged yet. The window stops the list growing forever
        // if a message ever gets missed.
        $pendingPatients = Patient::whereNull('whatsapp_sent_at')
            ->where('created_at', '>=', now()->subDays(3))
            ->orderBy('created_at')
            ->get();

        return view('whatsapp.settings', compact('settings', 'pendingPatients'));
    }

    /**
     * Save changes to the WhatsApp settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'country_code'        => 'required|string|max:5',
            'support_number'      => 'nullable|string|max:20',
            'thank_you_template'  => 'required|string|max:1000',
            'scheduled_time'      => 'required|date_format:H:i',
        ]);

        // Checkboxes only appear in the request when checked, so
        // read it separately instead of relying on validate().
        $validated['auto_schedule_enabled'] = $request->boolean('auto_schedule_enabled');

        $settings = WhatsappSetting::current();
        $settings->update($validated);

        return back()->with('success', 'WhatsApp settings updated successfully.');
    }

    /**
     * Mark a patient's thank-you message as sent (called via fetch()
     * right after the wa.me tab opens).
     */
    public function markSent(Patient $patient)
    {
        $patient->update(['whatsapp_sent_at' => now()]);

        return response()->json(['success' => true]);
    }
}
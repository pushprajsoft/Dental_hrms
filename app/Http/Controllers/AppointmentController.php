<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $doctorId  = $request->input('doctor_id');
        $statusTab = $request->input('tab', 'all');
        $date      = $request->input('date');

        $query = Appointment::with(['patient', 'doctor']);

        if ($search) {
            $query->whereHas('patient', function ($p) use ($search) {
                $p->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('appointment_code', 'like', "%{$search}%");
        }

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        match ($statusTab) {
            'today'     => $query->whereDate('appointment_date', now()->toDateString()),
            'upcoming'  => $query->where('appointment_date', '>=', now()->toDateString())->whereIn('status', ['Scheduled', 'Confirmed']),
            'completed' => $query->where('status', 'Completed'),
            'cancelled' => $query->whereIn('status', ['Cancelled', 'No-Show']),
            default     => null,
        };

        $appointments = $query->orderBy('appointment_date', 'desc')
                               ->orderBy('appointment_time', 'asc')
                               ->paginate(10)
                               ->withQueryString();

        $stats = [
            'today'     => Appointment::whereDate('appointment_date', now()->toDateString())->count(),
            'upcoming'  => Appointment::where('appointment_date', '>=', now()->toDateString())->whereIn('status', ['Scheduled', 'Confirmed'])->count(),
            'completed' => Appointment::where('status', 'Completed')->count(),
            'cancelled' => Appointment::whereIn('status', ['Cancelled', 'No-Show'])->count(),
        ];

        $doctors = Doctor::orderBy('full_name')->get();

        return view('appointments.index', compact('appointments', 'stats', 'doctors', 'search', 'doctorId', 'statusTab', 'date'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors  = Doctor::where('status', 'Active')->orderBy('full_name')->get();
        $preselectedPatientId = $request->query('patient_id');

        return view('appointments.create', compact('patients', 'doctors', 'preselectedPatientId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Appointment::create($validated);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors  = Doctor::orderBy('full_name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $this->validateData($request);

        $appointment->update($validated);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $validated['status'] = 'Scheduled';
        $validated['reminder_sent'] = false;

        $appointment->update($validated);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment rescheduled to ' . $appointment->appointment_date->format('d M Y') . ' at ' . $appointment->formatted_time . '.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
        ]);

        $appointment->update($validated);

        return redirect()
            ->back()
            ->with('success', "Appointment marked as {$validated['status']}.");
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted.');
    }

    public function details(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);

        return response()->json([
            'appointment_code' => $appointment->appointment_code,
            'patient_id'        => $appointment->patient->id,
            'patient_name'      => $appointment->patient->full_name,
            'patient_code'      => $appointment->patient->patient_code,
            'phone'             => $appointment->patient->phone,
            'gender'            => $appointment->patient->gender,
            'address'           => $appointment->patient->address,
            'doctor_name'       => $appointment->doctor->full_name ?? 'Not assigned',
            'date'              => $appointment->appointment_date->format('d M Y'),
            'time'              => $appointment->formatted_time,
            'duration'          => $appointment->duration_minutes,
            'reason'            => $appointment->reason,
            'status'            => $appointment->status,
            'notes'             => $appointment->notes,
            'whatsapp_link'     => $appointment->whatsapp_reminder_link,
        ]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'doctor_id'         => 'nullable|exists:doctors,id',
            'appointment_date'  => 'required|date',
            'appointment_time'  => 'required',
            'duration_minutes'  => 'required|integer|min:5',
            'reason'            => 'nullable|string|max:255',
            'status'            => 'required|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
            'notes'             => 'nullable|string',
        ]);
    }
}
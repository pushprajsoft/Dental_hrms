<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Dashboard + list of all patients (with search).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::when($search, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                      ->orWhere('patient_code', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'total'     => Patient::count(),
            'male'      => Patient::where('gender', 'Male')->count(),
            'female'    => Patient::where('gender', 'Female')->count(),
            'thisMonth' => Patient::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
        ];

        $whatsapp = WhatsappSetting::current();

        return view('patients.index', compact('patients', 'stats', 'search', 'whatsapp'));
    }

    /**
     * Show the "Add New Patient" form.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Validate and store a new patient.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $patient = Patient::create($validated);

        // If the user arrived here from the OPD form, send them back there
        if ($request->filled('return_to') && $request->input('return_to') === 'opd') {
            return redirect()
                ->route('opd.create', ['patient_id' => $patient->id])
                ->with('success', 'Patient added. Continue with the OPD visit below.');
        }

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient added successfully.');
    }

    /**
     * Show full details of one patient, including OPD visit history
     * and lifetime billing/payment stats.
     */
    public function show(Patient $patient)
    {
        $visits = $patient->visits()
            ->with(['doctor', 'payments'])
            ->paginate(10);

        $lifetime = [
            'totalVisits'  => $patient->visits()->count(),
            'totalBilled'  => (float) $patient->visits()->sum('total_amount'),
            'totalPaid'    => (float) $patient->visits()->sum('amount_paid'),
            'totalRefund'  => (float) $patient->visits()->sum('refund_amount'),
        ];
        $lifetime['outstanding'] = max(0, $lifetime['totalBilled'] - $lifetime['totalPaid']);

        return view('patients.show', compact('patient', 'visits', 'lifetime'));
    }

    /**
     * Show the "Edit Patient" form, pre-filled.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Validate and update an existing patient.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $this->validateData($request);

        // FIX: Prevent null values from deleting existing data
        $updateData = [];
        foreach ($validated as $key => $value) {
            $updateData[$key] = $value ?? $patient->$key;
        }

        $patient->update($updateData);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient details updated successfully.');
    }

    /**
     * Delete a patient record.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient record deleted.');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'gender'    => 'required|in:Male,Female,Other',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string',
        ]);
        $validated['status'] = 'Active';

        $patient = Patient::create($validated);

        return response()->json([
            'success' => true,
            'patient' => [
                'id'        => $patient->id,
                'label'     => $patient->patient_code . ' — ' . $patient->full_name . ' (' . $patient->phone . ')',
                'full_name' => $patient->full_name,
                'gender'    => $patient->gender,
                'phone'     => $patient->phone,
                'address'   => $patient->address,
            ],
        ]);
    }

    /**
     * Shared validation rules used by store() and update().
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'full_name'       => 'required|string|max:255',
            'gender'          => 'required|in:Male,Female,Other',
            'date_of_birth'   => 'nullable|date',
            'age'             => 'nullable|string|max:10',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email|max:255',
            'aadhar'          => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'blood_group'     => 'nullable|string|max:5',
            'chief_complaint' => 'nullable|string',
            'treatment_plan'  => 'nullable|string',
            'doctor_name'     => 'nullable|string|max:255',
            'status'          => 'required|in:Active,Completed,Follow-up',
            
            // New Extended Details
            'mlc'             => 'nullable|in:Yes,No',
            'fh_name'         => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'marital_status'  => 'nullable|string',
            'rel_name'        => 'nullable|string|max:255',
            'rel_relation'    => 'nullable|string|max:255',
            'rel_contact'     => 'nullable|string|max:15',
            'rel_address'     => 'nullable|string',
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\IpdAdmission;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class IpdController extends Controller
{
    public function dashboard(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $doctorId = $request->input('doctor_id');
        $search = $request->input('search');
        $status = $request->input('status', 'Admitted'); // Default to Admitted

        $baseQuery = IpdAdmission::whereDate('admission_date', '>=', $fromDate)
                                 ->whereDate('admission_date', '<=', $toDate);
        
        if ($doctorId) $baseQuery->where('doctor_id', $doctorId);
        if ($status && $status != 'All') $baseQuery->where('status', $status);
        if ($search) {
            $baseQuery->where(function($q) use ($search) {
                $q->where('ipd_code', 'like', "%{$search}%")
                  ->orWhere('p_name', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($p) use ($search) {
                      $p->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $admissions = (clone $baseQuery)->with(['patient', 'doctor', 'bed'])->latest('admission_date')->get();

        // Calculate individual payment sums
        $cash = (clone $baseQuery)->where('payment_method', 'Cash')->sum('advance_paid');
        $upi = (clone $baseQuery)->where('payment_method', 'UPI')->sum('advance_paid');
        $other = (clone $baseQuery)->whereIn('payment_method', ['Card', 'Cheque', 'Other'])->sum('advance_paid');
        $pending = (clone $baseQuery)->where('payment_method', 'Pending')->sum('advance_paid');
        $refund = (clone $baseQuery)->sum('refund_amount');
        
        $gross = $cash + $upi + $other;
        $net = $gross - $refund;

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'admitted' => (clone $baseQuery)->where('status', 'Admitted')->count(),
            'discharged' => (clone $baseQuery)->where('status', 'Discharged')->count(),
            'revenue' => $net, // Net Revenue
            'cash' => $cash,
            'upi' => $upi,
            'other' => $other,
            'pending' => $pending,
            'refund' => $refund,
        ];

        $doctors = Doctor::orderBy('full_name')->get();

        return view('ipd.dashboard', compact('stats', 'admissions', 'doctors', 'fromDate', 'toDate', 'doctorId', 'search', 'status'));
    }

    public function create()
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::orderBy('full_name')->get();
        
        return view('ipd.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_date' => 'required|date',
            'registered_type' => 'required|in:Existing,New',
            'patient_id' => 'nullable|exists:patients,id',
            'scheme_type' => 'nullable|string',
            'scheme_name' => 'nullable|string',
            'case_type' => 'nullable|string',
            'bill_category' => 'nullable|string',
            'corporate' => 'nullable|boolean',
            'esic_no' => 'nullable|string|max:255',
            'urn_no' => 'nullable|string|max:255',
            'admission_note' => 'nullable|string',
            'referral_doctor' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            
            'p_name' => 'required_if:registered_type,New|nullable|string|max:255',
            'p_gender' => 'required_if:registered_type,New|nullable|string',
            'p_dob' => 'nullable|date',
            'p_age' => 'nullable|string|max:10',
            'p_mobile' => 'required_if:registered_type,New|nullable|string|max:15',
            'p_aadhar' => 'nullable|string|max:20',
            'p_address' => 'nullable|string',
            'p_mlc' => 'nullable|string|in:Yes,No',
            'p_fh_name' => 'nullable|string|max:255',
            'p_mother_name' => 'nullable|string|max:255',
            'p_marital_status' => 'nullable|string',
            
            'rel_name' => 'nullable|string|max:255',
            'rel_relation' => 'nullable|string|max:255',
            'rel_contact' => 'nullable|string|max:15',
            'rel_address' => 'nullable|string',
            
            'doctor_id' => 'nullable|exists:doctors,id',
            'attending_doctor_id' => 'nullable|exists:doctors,id',
            'advance_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:Cash,UPI,Card,Cheque,Pending',
            'refund_amount' => 'nullable|numeric|min:0', 
        ]);

        $patientId = $request->patient_id;

        if ($request->registered_type === 'New') {
            $patient = \App\Models\Patient::create([
                'full_name' => $request->p_name,
                'gender' => $request->p_gender,
                'date_of_birth' => $request->p_dob,
                'age' => $request->p_age,
                'phone' => $request->p_mobile,
                'aadhar' => $request->p_aadhar,
                'address' => $request->p_address,
                'mlc' => $request->p_mlc,
                'fh_name' => $request->p_fh_name,
                'mother_name' => $request->p_mother_name,
                'marital_status' => $request->p_marital_status,
                'status' => 'Active',
            ]);
            $patientId = $patient->id;
        } elseif ($request->registered_type === 'Existing' && $patientId) {
            $patient = \App\Models\Patient::find($patientId);
            if ($patient) {
                $patient->update([
                    'full_name' => $request->p_name ?? $patient->full_name,
                    'gender' => $request->p_gender ?? $patient->gender,
                    'date_of_birth' => $request->p_dob ?? $patient->date_of_birth,
                    'age' => $request->p_age ?? $patient->age,
                    'phone' => $request->p_mobile ?? $patient->phone,
                    'aadhar' => $request->p_aadhar ?? $patient->aadhar,
                    'address' => $request->p_address ?? $patient->address,
                    'mlc' => $request->p_mlc ?? $patient->mlc,
                    'fh_name' => $request->p_fh_name ?? $patient->fh_name,
                    'mother_name' => $request->p_mother_name ?? $patient->mother_name,
                    'marital_status' => $request->p_marital_status ?? $patient->marital_status,
                ]);
            }
        }

        $ipdData = $request->except(['p_name', 'p_gender', 'p_dob', 'p_age', 'p_mobile', 'p_aadhar', 'p_address', 'p_mlc', 'p_fh_name', 'p_mother_name', 'p_marital_status']);
        $ipdData['patient_id'] = $patientId;
        $ipdData['advance_paid'] = $ipdData['advance_paid'] ?? 0;
        $ipdData['corporate'] = $ipdData['corporate'] ?? 0;
        $ipdData['refund_amount'] = $ipdData['refund_amount'] ?? 0;

        IpdAdmission::create($ipdData);

        return redirect()->route('ipd.dashboard')->with('success', 'Patient Admitted to IPD successfully!');
    }

    public function edit(IpdAdmission $ipdAdmission)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::orderBy('full_name')->get();
        
        return view('ipd.edit', compact('ipdAdmission', 'patients', 'doctors'));
    }

    public function update(Request $request, IpdAdmission $ipdAdmission)
    {
        $validated = $request->validate([
            'admission_date' => 'required|date',
            'registered_type' => 'required|in:Existing,New',
            'scheme_type' => 'nullable|string',
            'scheme_name' => 'nullable|string',
            'case_type' => 'nullable|string',
            'bill_category' => 'nullable|string',
            'corporate' => 'nullable|boolean',
            'esic_no' => 'nullable|string|max:255',
            'urn_no' => 'nullable|string|max:255',
            'admission_note' => 'nullable|string',
            'referral_doctor' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            
            'p_name' => 'nullable|string|max:255',
            'p_gender' => 'nullable|string',
            'p_dob' => 'nullable|date',
            'p_age' => 'nullable|string|max:10',
            'p_mobile' => 'nullable|string|max:15',
            'p_aadhar' => 'nullable|string|max:20',
            'p_address' => 'nullable|string',
            'p_mlc' => 'nullable|string|in:Yes,No',
            'p_fh_name' => 'nullable|string|max:255',
            'p_mother_name' => 'nullable|string|max:255',
            'p_marital_status' => 'nullable|string',
            
            'rel_name' => 'nullable|string|max:255',
            'rel_relation' => 'nullable|string|max:255',
            'rel_contact' => 'nullable|string|max:15',
            'rel_address' => 'nullable|string',
            
            'doctor_id' => 'nullable|exists:doctors,id',
            'attending_doctor_id' => 'nullable|exists:doctors,id',
            'advance_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:Cash,UPI,Card,Cheque,Pending',
            'refund_amount' => 'nullable|numeric|min:0', 
            
            'status' => 'required|in:Admitted,Discharged',
            'discharge_date' => 'nullable|date|required_if:status,Discharged',
        ]);

        if ($ipdAdmission->patient_id) {
            $patient = \App\Models\Patient::find($ipdAdmission->patient_id);
            if ($patient) {
                $patient->update([
                    'full_name' => $request->p_name ?? $patient->full_name,
                    'gender' => $request->p_gender ?? $patient->gender,
                    'date_of_birth' => $request->p_dob ?? $patient->date_of_birth,
                    'age' => $request->p_age ?? $patient->age,
                    'phone' => $request->p_mobile ?? $patient->phone,
                    'aadhar' => $request->p_aadhar ?? $patient->aadhar,
                    'address' => $request->p_address ?? $patient->address,
                    'mlc' => $request->p_mlc ?? $patient->mlc,
                    'fh_name' => $request->p_fh_name ?? $patient->fh_name,
                    'mother_name' => $request->p_mother_name ?? $patient->mother_name,
                    'marital_status' => $request->p_marital_status ?? $patient->marital_status,
                ]);
            }
        }

        $validated['advance_paid'] = $validated['advance_paid'] ?? 0;
        $validated['corporate'] = $validated['corporate'] ?? 0;
        $validated['refund_amount'] = $validated['refund_amount'] ?? 0;
        $ipdAdmission->update($validated);

        return redirect()->route('ipd.dashboard')->with('success', 'IPD Record & Patient Details updated successfully!');
    }

    // ==========================================
    // BED ALLOCATION & TRANSFER METHODS
    // ==========================================

    public function allocate(IpdAdmission $ipdAdmission)
    {
        // Get available beds, OR the bed currently assigned to this patient
        $beds = \App\Models\Bed::where('status', 'Available')
                    ->orWhere('id', $ipdAdmission->bed_id)
                    ->orderBy('bed_number')
                    ->get();
        
        return view('ipd.allocate', compact('ipdAdmission', 'beds'));
    }

    public function allocateUpdate(Request $request, IpdAdmission $ipdAdmission)
    {
        $validated = $request->validate([
            'bed_id' => 'nullable|exists:beds,id',
            'allotment_date' => 'nullable|date',
        ]);

        // 1. Free up the OLD bed if it exists (Bed Transfer Logic)
        if ($ipdAdmission->bed_id && $ipdAdmission->bed_id != $request->bed_id) {
            $oldBed = \App\Models\Bed::find($ipdAdmission->bed_id);
            if ($oldBed) {
                $oldBed->update(['status' => 'Available']);
            }
        }

        // 2. Assign the NEW bed and mark it as Occupied
        if ($request->filled('bed_id')) {
            $newBed = \App\Models\Bed::find($request->bed_id);
            
            // Only update bed status if it's actually changing to a new bed
            if ($ipdAdmission->bed_id != $request->bed_id) {
                $newBed->update(['status' => 'Occupied']);
            }
            
            $ipdAdmission->bed_id = $newBed->id;
            // Removed room_number and bed_number assignments as they are accessed via the bed relationship
        } else {
            // If no bed selected, remove bed assignment completely
            if ($ipdAdmission->bed_id) {
                $oldBed = \App\Models\Bed::find($ipdAdmission->bed_id);
                if ($oldBed) $oldBed->update(['status' => 'Available']);
            }
            $ipdAdmission->bed_id = null;
        }

        // Update the allotment/transfer date
        $ipdAdmission->allotment_date = $request->allotment_date;
        $ipdAdmission->save();

        $message = $ipdAdmission->wasChanged('bed_id') ? 'Bed transferred successfully!' : 'Bed allocation updated successfully!';
        return redirect()->route('ipd.dashboard')->with('success', $message);
    }

    // ==========================================
    // IPD DETAILS SHOW METHOD
    // ==========================================

    public function show(IpdAdmission $ipdAdmission)
    {
        // Load relationships
        $ipdAdmission->load(['patient', 'doctor', 'attendingDoctor', 'bed']);

        // Fetch all IPD visits for this patient for the visit history
        $visits = IpdAdmission::where('patient_id', $ipdAdmission->patient_id)
            ->with(['doctor'])
            ->latest('admission_date')
            ->get();

        // Calculate Lifetime Financials
        $lifetime = [
            'totalVisits'  => $visits->count(),
            'totalBilled'  => $visits->sum('advance_paid'),
            'totalPaid'    => $visits->sum('advance_paid'),
            'refunded'     => $visits->sum('refund_amount'),
        ];
        $lifetime['outstanding'] = max(0, $lifetime['totalBilled'] - $lifetime['totalPaid'] - $lifetime['refunded']);

        return view('ipd.show', compact('ipdAdmission', 'visits', 'lifetime'));
    }

    // ==========================================
    // PATIENT DETAILS & VISIT HISTORY
    // ==========================================

    public function showPatient(Patient $patient)
    {
        // Fetch all IPD admissions for this patient, ordered by newest first
        $ipdVisits = IpdAdmission::where('patient_id', $patient->id)
            ->with(['doctor', 'bed'])
            ->latest('admission_date')
            ->get();

        // Calculate Lifetime Financials for this patient
        $lifetime = [
            'totalVisits'  => $ipdVisits->count(),
            'totalBilled'  => $ipdVisits->sum('advance_paid'),
            'totalPaid'    => $ipdVisits->sum('advance_paid'), // Assuming advance paid is the collected amount
            'totalRefund'  => $ipdVisits->sum('refund_amount'),
        ];
        $lifetime['outstanding'] = 0; // Set to 0 for now, or calculate if total_billed > advance_paid

        return view('ipd.patient_details', compact('patient', 'ipdVisits', 'lifetime'));
    }

    public function destroy(IpdAdmission $ipdAdmission)
    {
        $ipdAdmission->delete();
        return redirect()->route('ipd.dashboard')->with('success', 'IPD Record deleted successfully!');
    }
}
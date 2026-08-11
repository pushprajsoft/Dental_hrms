<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;

// ==========================================
// DASHBOARD & ANALYTICS APIs
// ==========================================

// API for Mobile App Dashboard Stats
Route::get('/dashboard-stats', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'todays_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'recent_patients' => Patient::latest()->take(5)->get(['id', 'full_name', 'phone', 'patient_code'])
        ]
    ]);
});

// API for Mobile App Revenue Dashboard & Charts
Route::get('/revenue-stats', function (Request $request) {
    $fromDate = $request->input('from_date', now()->subDays(30)->toDateString());
    $toDate = $request->input('to_date', now()->toDateString());

    $visits = \App\Models\OpdVisit::whereBetween('visit_date', [$fromDate, $toDate])
        ->orderBy('visit_date', 'desc')
        ->get();

    $grouped = $visits->groupBy(function($visit) {
        return \Carbon\Carbon::parse($visit->visit_date)->format('d M');
    });

    $chartLabels = [];
    $chartData = [];
    foreach ($grouped as $date => $dayVisits) {
        $chartLabels[] = $date;
        $chartData[] = $dayVisits->sum('amount_paid');
    }

    return response()->json([
        'status' => 'success',
        'data' => [
            'total_revenue' => $visits->sum('amount_paid'),
            'total_visits' => $visits->count(),
            'chart_labels' => $chartLabels,
            'chart_data' => $chartData,
            'transactions' => $visits->take(50)->map(function($v) { // Changed to 50 for mobile app view
                return [
                    'id' => $v->id, 
                    'code' => $v->visit_code, 
                    'amount' => $v->amount_paid, 
                    'status' => $v->status, 
                    'date' => $v->visit_date
                ];
            })->values()
        ]
    ]);
});

// API for Today's Appointments (Used in Mobile Revenue Page)
Route::get('/todays-appointments', function () {
    $appointments = Appointment::with('patient:id,full_name', 'doctor:id,full_name')
        ->whereDate('appointment_date', today())
        ->orderBy('appointment_time')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $appointments
    ]);
});

// ==========================================
// PATIENT MANAGEMENT APIs
// ==========================================

// API for Mobile App Patients List
Route::get('/patients', function () {
    return response()->json([
        'status' => 'success',
        'data' => Patient::latest()->get(['id', 'full_name', 'phone', 'patient_code', 'gender'])
    ]);
});

// API to Create a New Patient from Mobile App
Route::post('/patients', function (Request $request) {
    $validator = validator($request->all(), [
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:15',
        'gender' => 'required|in:Male,Female,Other',
        'address' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    }

    $patient = Patient::create($validator->validated());
    $patient->patient_code = 'PT-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
    $patient->save();

    return response()->json(['status' => 'success', 'data' => $patient], 201);
});

// API to fetch a single patient's details WITH their visit history
Route::get('/patients/{id}', function ($id) {
    // 'with('visits')' fetches all their OPD visits automatically!
    $patient = Patient::with('visits')->find($id);
    
    if (!$patient) {
        return response()->json(['status' => 'error', 'message' => 'Patient not found'], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $patient
    ]);
});

// API to Edit/Update a Patient from Mobile App
Route::put('/patients/{id}', function (Request $request, $id) {
    $patient = Patient::find($id);
    
    if (!$patient) {
        return response()->json(['status' => 'error', 'message' => 'Patient not found'], 404);
    }

    $validator = validator($request->all(), [
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:15',
        'gender' => 'required|in:Male,Female,Other',
        'address' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    }

    $patient->update($validator->validated());

    return response()->json([
        'status' => 'success',
        'message' => 'Patient updated successfully',
        'data' => $patient
    ]);
});
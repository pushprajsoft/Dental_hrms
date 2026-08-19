<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
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
            'transactions' => $visits->take(50)->map(function($v) {
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

// API for Today's Appointments (Used in Mobile Dashboard)
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
// DOCTOR MANAGEMENT APIs
// ==========================================

// Get Doctors List (for dropdown in appointments)
Route::get('/doctors', function () {
    return response()->json([
        'status' => 'success',
        'data' => Doctor::select('id', 'full_name', 'specialization')->get()
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

// ==========================================
// APPOINTMENT MANAGEMENT APIs
// ==========================================

// Get All Appointments (with filters)
Route::get('/appointments', function (Request $request) {
    $query = Appointment::with('patient:id,full_name,patient_code', 'doctor:id,full_name');
    
    // Filter by specific date
    if ($request->has('date')) {
        $query->whereDate('appointment_date', $request->date);
    }
    
    // Filter by status
    if ($request->has('status') && $request->status !== 'All') {
        $query->where('status', $request->status);
    }
    
    // Filter by date range
    if ($request->has('from_date') && $request->has('to_date')) {
        $query->whereBetween('appointment_date', [$request->from_date, $request->to_date]);
    }
    
    // Search by patient name
    if ($request->has('search')) {
        $query->whereHas('patient', function($q) use ($request) {
            $q->where('full_name', 'like', '%' . $request->search . '%');
        });
    }
    
    $appointments = $query->orderBy('appointment_date', 'desc')
        ->orderBy('appointment_time', 'asc')
        ->get();
    
    // Stats for the appointment screen
    $stats = [
        'total' => Appointment::count(),
        'today' => Appointment::whereDate('appointment_date', today())->count(),
        'scheduled' => Appointment::where('status', 'Scheduled')->count(),
        'confirmed' => Appointment::where('status', 'Confirmed')->count(),
        'completed' => Appointment::where('status', 'Completed')->count(),
        'cancelled' => Appointment::where('status', 'Cancelled')->count(),
    ];
    
    return response()->json([
        'status' => 'success',
        'data' => $appointments,
        'stats' => $stats
    ]);
});

// Get Single Appointment
Route::get('/appointments/{id}', function ($id) {
    $appointment = Appointment::with('patient', 'doctor')->find($id);
    
    if (!$appointment) {
        return response()->json(['status' => 'error', 'message' => 'Appointment not found'], 404);
    }
    
    return response()->json([
        'status' => 'success',
        'data' => $appointment
    ]);
});

// Create New Appointment
Route::post('/appointments', function (Request $request) {
    $validator = validator($request->all(), [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'nullable|exists:doctors,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required|date_format:H:i',
        'duration_minutes' => 'nullable|integer|min:15|max:120',
        'reason' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:1000',
    ]);
    
    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    }
    
    $data = $validator->validated();
    
    // appointment_code is auto-generated in model's boot() method
    $data['status'] = 'Scheduled';
    $data['duration_minutes'] = $data['duration_minutes'] ?? 30;
    
    $appointment = Appointment::create($data);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Appointment created successfully',
        'data' => $appointment->load('patient', 'doctor')
    ], 201);
});

// Update Appointment
Route::put('/appointments/{id}', function (Request $request, $id) {
    $appointment = Appointment::find($id);
    
    if (!$appointment) {
        return response()->json(['status' => 'error', 'message' => 'Appointment not found'], 404);
    }
    
    $validator = validator($request->all(), [
        'patient_id' => 'sometimes|required|exists:patients,id',
        'doctor_id' => 'nullable|exists:doctors,id',
        'appointment_date' => 'sometimes|required|date',
        'appointment_time' => 'sometimes|required|date_format:H:i',
        'duration_minutes' => 'nullable|integer|min:15|max:120',
        'reason' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:1000',
        'status' => 'sometimes|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
    ]);
    
    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    }
    
    $appointment->update($validator->validated());
    
    return response()->json([
        'status' => 'success',
        'message' => 'Appointment updated successfully',
        'data' => $appointment->load('patient', 'doctor')
    ]);
});

// Update Appointment Status Only (Quick Action)
Route::patch('/appointments/{id}/status', function (Request $request, $id) {
    $appointment = Appointment::find($id);
    
    if (!$appointment) {
        return response()->json(['status' => 'error', 'message' => 'Appointment not found'], 404);
    }
    
    $request->validate([
        'status' => 'required|in:Scheduled,Confirmed,Completed,Cancelled,No-Show'
    ]);
    
    $appointment->update(['status' => $request->status]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Status updated to ' . $request->status,
        'data' => $appointment
    ]);
});

// Delete Appointment
Route::delete('/appointments/{id}', function ($id) {
    $appointment = Appointment::find($id);
    
    if (!$appointment) {
        return response()->json(['status' => 'error', 'message' => 'Appointment not found'], 404);
    }
    
    $appointment->delete();
    
    return response()->json([
        'status' => 'success',
        'message' => 'Appointment deleted successfully'
    ]);
});

// ==========================================
// AUTHENTICATION APIs
// ==========================================

// Simple Login (for mobile app - returns token)
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);
    
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }
    
    $token = $user->createToken('mobile-app')->plainTextToken;
    
    return response()->json([
        'status' => 'success',
        'message' => 'Login successful',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'Admin',
            ]
        ]
    ]);
});

// Logout
Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    
    return response()->json([
        'status' => 'success',
        'message' => 'Logged out successfully'
    ]);
})->middleware('auth:sanctum');

// ==========================================
// USER PROFILE MANAGEMENT APIs
// ==========================================

// Get Current User Profile
Route::get('/profile', function (Request $request) {
    $user = $request->user();
    
    return response()->json([
        'status' => 'success',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'role' => $user->role ?? 'Admin',
            'avatar' => $user->avatar ?? null,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at->format('d M Y'),
        ]
    ]);
})->middleware('auth:sanctum');

// Update Profile (Name, Email, Phone)
Route::put('/profile', function (Request $request) {
    $user = $request->user();
    
    $validator = validator($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
    ]);
    
    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    }
    
    $user->update($validator->validated());
    
    return response()->json([
        'status' => 'success',
        'message' => 'Profile updated successfully',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]
    ]);
})->middleware('auth:sanctum');

// Change Password
Route::put('/change-password', function (Request $request) {
    $user = $request->user();
    
    $request->validate([
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Current password is incorrect'
        ], 422);
    }
    
    $user->update([
        'password' => Hash::make($request->password)
    ]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Password changed successfully'
    ]);
})->middleware('auth:sanctum');

// Upload Avatar
Route::post('/profile/avatar', function (Request $request) {
    $user = $request->user();
    
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    
    if ($user->avatar && file_exists(public_path($user->avatar))) {
        unlink(public_path($user->avatar));
    }
    
    $avatarName = time() . '.' . $request->avatar->extension();
    $request->avatar->move(public_path('avatars'), $avatarName);
    
    $user->update(['avatar' => 'avatars/' . $avatarName]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Avatar updated successfully',
        'data' => ['avatar' => 'avatars/' . $avatarName]
    ]);
})->middleware('auth:sanctum');
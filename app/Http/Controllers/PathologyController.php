<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\PrintSetting;
use Illuminate\Http\Request;

class PathologyController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending' => LabTest::where('status', 'Pending')->count(),
            'collected' => LabTest::where('status', 'Sample Collected')->count(),
            'completed' => LabTest::where('status', 'Completed')->count(),
            'total' => LabTest::count(),
        ];

        $tests = LabTest::with(['patient', 'doctor'])->latest()->take(10)->get();
        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::orderBy('full_name')->get();

        return view('pathology.dashboard', compact('stats', 'tests', 'patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'test_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        LabTest::create($validated);

        return redirect()->route('pathology.dashboard')->with('success', 'Lab Test Requested successfully!');
    }

    public function edit(LabTest $labTest)
    {
        $labTest->load(['patient', 'doctor']);
        return view('pathology.edit', compact('labTest'));
    }

    public function update(Request $request, LabTest $labTest)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Sample Collected,Completed',
            'result' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $labTest->update($validated);

        return redirect()->route('pathology.dashboard')->with('success', 'Lab Results updated successfully!');
    }

    public function report($id)
    {
        $test = LabTest::with(['patient', 'doctor'])->findOrFail($id);
        $settings = PrintSetting::current();

        return view('pathology.report', compact('test', 'settings'));
    }
}
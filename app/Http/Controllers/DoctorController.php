<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * List all doctors (with search).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $doctors = Doctor::when($search, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                      ->orWhere('doctor_code', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'total'  => Doctor::count(),
            'active' => Doctor::where('status', 'Active')->count(),
            'onLeave'=> Doctor::where('status', 'On Leave')->count(),
        ];

        return view('doctors.index', compact('doctors', 'stats', 'search'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Doctor::create($validated);

        return redirect()
            ->route('doctors.index')
            ->with('success', 'Doctor added successfully.');
    }

    public function show(Doctor $doctor)
    {
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $this->validateData($request);

        $doctor->update($validated);

        return redirect()
            ->route('doctors.index')
            ->with('success', 'Doctor details updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()
            ->route('doctors.index')
            ->with('success', 'Doctor record deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'full_name'        => 'required|string|max:255',
            'specialization'   => 'required|string|max:255',
            'qualification'    => 'nullable|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
            'experience_years' => 'nullable|integer|min:0|max:70',
            'joining_date'     => 'nullable|date',
            'status'           => 'required|in:Active,On Leave,Inactive',
        ]);
    }
}
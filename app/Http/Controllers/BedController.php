<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;

class BedController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bedType = $request->input('bed_type');
        $status = $request->input('status');

        $query = Bed::query();

        if ($search) {
            $query->where('bed_number', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%");
        }
        if ($bedType) {
            $query->where('bed_type', $bedType);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $beds = $query->orderBy('bed_number', 'asc')->get();
        
        $stats = [
            'total' => Bed::count(),
            'available' => Bed::where('status', 'Available')->count(),
            'occupied' => Bed::where('status', 'Occupied')->count(),
            'maintenance' => Bed::where('status', 'Maintenance')->count(),
        ];

        return view('ipd.beds', compact('beds', 'stats', 'search', 'bedType', 'status'));
    }

    public function create()
    {
        return view('ipd.bed_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:255|unique:beds',
            'room_number' => 'nullable|string|max:255',
            'bed_type' => 'required|in:General,Private,ICU,Ward',
            'charge_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Occupied,Maintenance',
        ]);

        Bed::create($validated);

        return redirect()->route('ipd.beds.index')->with('success', 'Bed Master added successfully!');
    }

    public function edit(Bed $bed)
    {
        return view('ipd.bed_edit', compact('bed'));
    }

    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:255|unique:beds,bed_number,' . $bed->id,
            'room_number' => 'nullable|string|max:255',
            'bed_type' => 'required|in:General,Private,ICU,Ward',
            'charge_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Occupied,Maintenance',
        ]);

        $bed->update($validated);

        return redirect()->route('ipd.beds.index')->with('success', 'Bed updated successfully!');
    }

    public function destroy(Bed $bed)
    {
        $bed->delete();

        return redirect()->route('ipd.beds.index')->with('success', 'Bed deleted successfully!');
    }
}
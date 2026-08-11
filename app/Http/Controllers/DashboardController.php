<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\OpdVisit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Main Stats
        $stats = [
            'totalPatients'       => Patient::count(),
            'totalDoctors'        => Doctor::count(),
            'todaysAppointments'  => Appointment::whereDate('appointment_date', today())->count(),
        ];

        // Fetch 5 recent patients for the dynamic list
        $recentPatients = Patient::latest()->take(5)->get();

        // --- REVENUE DATA ---
        
        // 1. Monthly Revenue (Last 6 Months) for Line Chart
        $revenueLabels = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueLabels[] = $month->format('M Y');
            $revenueData[] = OpdVisit::whereMonth('visit_date', $month->month)
                                     ->whereYear('visit_date', $month->year)
                                     ->sum('amount_paid');
        }

        // 2. Daily Revenue (Last 7 Days) for Bar Chart
        $dailyRevLabels = [];
        $dailyRevData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dailyRevLabels[] = $day->format('D'); // e.g., Mon, Tue
            $dailyRevData[] = OpdVisit::whereDate('visit_date', $day->toDateString())->sum('amount_paid');
        }

        return view('dashboard', compact('stats', 'recentPatients', 'revenueLabels', 'revenueData', 'dailyRevLabels', 'dailyRevData'));
    }
}
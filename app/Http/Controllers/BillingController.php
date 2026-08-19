<?php

namespace App\Http\Controllers;

use App\Models\OpdVisit;
use App\Models\PrintSetting;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    // 1. Show All Bills with Date Filter
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = OpdVisit::with(['patient', 'doctor'])->orderBy('visit_date', 'desc');

        if ($fromDate && $toDate) {
            $query->whereBetween('visit_date', [$fromDate, $toDate]);
        }

        $bills = $query->get();
        $totalRevenue = $bills->sum('amount_paid');

        return view('billing.index', compact('bills', 'fromDate', 'toDate', 'totalRevenue'));
    }

    // 2. Generate Dynamic Invoice (Handles different PDF types)
    public function invoice($id)
    {
        $visit = OpdVisit::with(['patient', 'doctor'])->findOrFail($id);
        $settings = PrintSetting::current();

        // Check the 'type' parameter from the URL (?type=cash or ?type=prescription)
        if (request()->query('type') === 'cash') {
            return view('billing.opd_cash_bill', compact('visit', 'settings'));
        } 
        elseif (request()->query('type') === 'prescription') {
            return view('billing.prescription_pdf', compact('visit', 'settings'));
        }

        // Default to the standard Word-like invoice
        return view('billing.invoice', compact('visit', 'settings'));
    }
}
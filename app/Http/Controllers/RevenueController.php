<?php

namespace App\Http\Controllers;

use App\Models\OpdVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        // Get Date Filters (Make them optional. If empty, fetch all records)
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = OpdVisit::with(['patient', 'doctor'])->orderBy('visit_date', 'desc');

        // Only apply date filter if BOTH dates are provided
        if ($fromDate && $toDate) {
            $query->whereBetween('visit_date', [$fromDate, $toDate]);
        }

        $visits = $query->get();

        // Calculate Summary Stats
        $totalRevenue = $visits->sum('amount_paid');
        $totalVisits = $visits->count();
        $avgRevenue = $totalVisits > 0 ? $totalRevenue / $totalVisits : 0;

        // Prepare Chart Data (Sort chronologically so the chart goes left to right)
        $chartLabels = [];
        $chartData = [];
        
        $grouped = $visits->groupBy(function($visit) {
            return Carbon::parse($visit->visit_date)->format('Y-m-d');
        })->sortKeys();

        foreach ($grouped as $date => $dayVisits) {
            $chartLabels[] = Carbon::parse($date)->format('d M Y');
            $chartData[] = $dayVisits->sum('amount_paid');
        }

        return view('revenue.index', compact(
            'visits', 'fromDate', 'toDate', 'totalRevenue', 'totalVisits', 'avgRevenue', 'chartLabels', 'chartData'
        ));
    }

    public function export(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = OpdVisit::with(['patient', 'doctor'])->orderBy('visit_date', 'desc');
        if ($fromDate && $toDate) {
            $query->whereBetween('visit_date', [$fromDate, $toDate]);
        }
        $visits = $query->get();

        // Define true Excel XML Headers
        $fileName = "Revenue_Report_" . now()->format('d_m_Y') . ".xls";
        
        $headers = [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "public",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Native PHP Excel XML Generator (No packages needed)
        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        $xml .= '<Worksheet ss:Name="Revenue Report">' . "\n";
        $xml .= '<Table>' . "\n";

        // Header Row
        $xml .= '<Row>' . "\n";
        $headers_list = ['Date', 'Visit Code', 'Patient Name', 'Doctor Name', 'Total Amount', 'Amount Paid', 'Payment Method', 'Status'];
        foreach ($headers_list as $header) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
        }
        $xml .= '</Row>' . "\n";

        // Data Rows
        foreach ($visits as $visit) {
            $xml .= '<Row>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($visit->visit_date) . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($visit->visit_code) . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($visit->patient->full_name ?? 'N/A') . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($visit->doctor->full_name ?? 'N/A') . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="Number">' . number_format($visit->total_amount, 2, '.', '') . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="Number">' . number_format($visit->amount_paid, 2, '.', '') . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($visit->payment_method) . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($visit->status) . '</Data></Cell>' . "\n";
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>' . "\n";

        return response()->make($xml, 200, $headers);
    }
}
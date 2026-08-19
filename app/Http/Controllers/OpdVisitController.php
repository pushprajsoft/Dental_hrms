<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\OpdVisit;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpdVisitController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $doctorId  = $request->input('doctor_id');
        $visitType = $request->input('visit_type');
        $fromDate  = $request->input('from_date') ?: now()->toDateString();
        $toDate    = $request->input('to_date') ?: now()->toDateString();

        $base = OpdVisit::whereDate('visit_date', '>=', $fromDate)
                         ->whereDate('visit_date', '<=', $toDate);
        if ($doctorId)  $base->where('doctor_id', $doctorId);
        if ($visitType) $base->where('visit_type', $visitType);

        $visits = (clone $base)
            ->with(['patient', 'doctor'])
            ->when($search, function ($q) use ($search) {
                $q->where('visit_code', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($p) use ($search) {
                      $p->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            })
            ->latest('visit_date')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'    => (clone $base)->count(),
            'new'      => (clone $base)->where('visit_type', 'New')->count(),
            'followup' => (clone $base)->where('visit_type', 'Follow-up')->count(),
            'revisit'  => (clone $base)->where('visit_type', 'Revisit')->count(),
        ];

        $paymentTotals = $this->paymentTotals($fromDate, $toDate, $doctorId, $visitType);

        $otherBreakdown = [
            'cheque' => (float) ($paymentTotals['Cheque'] ?? 0),
            'card'   => (float) ($paymentTotals['Card'] ?? 0),
            'other'  => (float) ($paymentTotals['Other'] ?? 0),
        ];

        $collection = [
            'cash'    => (float) ($paymentTotals['Cash'] ?? 0),
            'upi'     => (float) ($paymentTotals['UPI'] ?? 0),
            'other'   => (float) array_sum($otherBreakdown),
            'refund'  => (float) (clone $base)->sum('refund_amount'),
            'revenue' => (float) (clone $base)->sum('total_amount'),
        ];
        $collection['net'] = $collection['cash'] + $collection['upi'] + $collection['other'] - $collection['refund'];

        $doctorRevenue = (clone $base)
            ->select('doctor_id', DB::raw('COUNT(*) as total_visits'), DB::raw('SUM(amount_paid) as total_collected'))
            ->whereNotNull('doctor_id')
            ->groupBy('doctor_id')
            ->with('doctor')
            ->orderByDesc('total_collected')
            ->get();

        $doctors = Doctor::orderBy('full_name')->get();

        return view('opd.index', compact(
            'visits', 'stats', 'collection', 'otherBreakdown', 'doctorRevenue', 'doctors',
            'search', 'doctorId', 'visitType', 'fromDate', 'toDate'
        ));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors  = Doctor::where('status', 'Active')->orderBy('full_name')->get();
        $nextToken = $this->nextTokenNumber(now()->toDateString());

        $preselectedPatientId = $request->query('patient_id');

        return view('opd.create', compact('patients', 'doctors', 'nextToken', 'preselectedPatientId'));
    }

    public function store(Request $request)
    {
        $validated = $this->calculateTotals($this->validateData($request));

        if (empty($validated['token_number'])) {
            $validated['token_number'] = $this->nextTokenNumber($validated['visit_date']);
        }

        $payments = $validated['payments'];
        unset($validated['payments']);

        $validated['amount_paid']    = collect($payments)->sum('amount');
        $validated['payment_method'] = $this->derivePaymentMethod($payments);

        DB::transaction(function () use ($validated, $payments) {
            $visit = OpdVisit::create($validated);

            foreach ($payments as $payment) {
                $visit->payments()->create([
                    'method'        => $payment['method'],
                    'amount'        => $payment['amount'],
                    'reference_no'  => $payment['reference_no'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('opd.index')
            ->with('success', 'OPD visit recorded successfully.');
    }

    public function show(OpdVisit $opd)
    {
        $opd->load(['patient', 'doctor', 'payments']);

        return view('opd.show', ['visit' => $opd]);
    }

    public function edit(OpdVisit $opd)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors  = Doctor::orderBy('full_name')->get();
        $opd->load('payments');

        return view('opd.edit', ['visit' => $opd, 'patients' => $patients, 'doctors' => $doctors]);
    }

    public function update(Request $request, OpdVisit $opd)
    {
        $validated = $this->calculateTotals($this->validateData($request));

        $payments = $validated['payments'];
        unset($validated['payments']);

        $validated['amount_paid']    = collect($payments)->sum('amount');
        $validated['payment_method'] = $this->derivePaymentMethod($payments);

        DB::transaction(function () use ($validated, $payments, $opd) {
            $opd->update($validated);

            // Replace old payment rows with the new set submitted from the edit form
            $opd->payments()->delete();

            foreach ($payments as $payment) {
                $opd->payments()->create([
                    'method'        => $payment['method'],
                    'amount'        => $payment['amount'],
                    'reference_no'  => $payment['reference_no'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('opd.index')
            ->with('success', 'OPD visit updated successfully.');
    }

    public function destroy(OpdVisit $opd)
    {
        $opd->delete(); // cascades and deletes related opd_payments rows automatically

        return redirect()
            ->route('opd.index')
            ->with('success', 'OPD visit record deleted.');
    }

    private function calculateTotals(array $data): array
    {
        $consultationFee = (float) ($data['consultation_fee'] ?? 0);
        $otherCharges    = (float) ($data['other_charges'] ?? 0);
        $discount        = (float) ($data['discount'] ?? 0);

        $data['total_amount'] = max(0, $consultationFee + $otherCharges - $discount);

        return $data;
    }

    private function derivePaymentMethod(array $payments): string
    {
        $methods = collect($payments)->pluck('method')->unique();

        return $methods->count() === 1 ? $methods->first() : 'Mixed';
    }

    private function paymentTotals(string $fromDate, string $toDate, $doctorId, $visitType)
    {
        $query = DB::table('opd_payments')
            ->join('opd_visits', 'opd_visits.id', '=', 'opd_payments.opd_visit_id')
            ->whereDate('opd_visits.visit_date', '>=', $fromDate)
            ->whereDate('opd_visits.visit_date', '<=', $toDate);

        if ($doctorId)  $query->where('opd_visits.doctor_id', $doctorId);
        if ($visitType) $query->where('opd_visits.visit_type', $visitType);

        return $query->select('opd_payments.method', DB::raw('SUM(opd_payments.amount) as total'))
                     ->groupBy('opd_payments.method')
                     ->pluck('total', 'method');
    }

    private function nextTokenNumber(string $date): string
    {
        $count = OpdVisit::whereDate('visit_date', $date)->count();
        return (string) ($count + 1);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'nullable|exists:doctors,id',
            'visit_date'       => 'required|date',
            'visit_type'       => 'required|in:New,Follow-up,Revisit',
            'token_number'     => 'nullable|string|max:20',
            'mlc'              => 'required|in:Yes,No',
            'referred_by'      => 'nullable|string|max:255',
            'chief_complaint'  => 'nullable|string',
            'height_cm'        => 'nullable|numeric|min:0',
            'weight_kg'        => 'nullable|numeric|min:0',
            'blood_pressure'   => 'nullable|string|max:20',
            'pulse_rate'       => 'nullable|integer|min:0',
            'temperature'      => 'nullable|numeric|min:0',
            'spo2'             => 'nullable|integer|min:0|max:100',
            'symptoms'         => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'other_charges'    => 'nullable|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'payment_date'     => 'nullable|date',
            'refund_amount'    => 'nullable|numeric|min:0',
            'status'           => 'required|in:Paid,Partial,Pending,Refunded',
            'notes'            => 'nullable|string',

            // Split payment rows
            'payments'                   => 'required|array|min:1',
            'payments.*.method'          => 'required|in:Cash,UPI,Cheque,Card,Other',
            'payments.*.amount'          => 'required|numeric|min:0.01',
            'payments.*.reference_no'    => 'nullable|string|max:100',
        ]);
    }

    public function doctorFee(Doctor $doctor)
    {
        return response()->json(['fee' => 300]);
    }
}
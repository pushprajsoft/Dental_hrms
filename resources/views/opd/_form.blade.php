{{-- Shared by create.blade.php and edit.blade.php.
     $visit only exists when editing. --}}

@if($errors->any())
    <div class="alert-clinic" style="background: var(--clr-warn-soft); color: var(--clr-warn);">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul style="margin:6px 0 0 18px; padding:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<style>
    .opd-patient-row { display:flex; gap:10px; align-items:flex-end; }
    .opd-patient-row .patient-select-wrap { flex:1; }
    .opd-readonly-chip {
        background: var(--clr-bg, #f6f9ff);
        border: 1px dashed var(--clr-border, #e5e9f0);
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 0.82rem;
        color: var(--clr-muted, #64748b);
        min-height: 38px;
        display:flex; align-items:center;
    }
    .opd-section-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        color: var(--clr-primary, #123C3A);
        margin: 22px 0 10px;
        display:flex; align-items:center; gap:8px;
        font-size: 0.95rem;
    }
    .opd-section-title:first-child { margin-top: 0; }
</style>

<div class="opd-section-title"><i class="fa-solid fa-user"></i> Patient</div>

<div class="form-grid">

    <div class="full-span">
        <label class="form-label-clinic">Patient *</label>
        <div class="opd-patient-row">
            <div class="patient-select-wrap">
                <select name="patient_id" id="patient_id" class="form-control-clinic" required>
                    <option value="">Select patient</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}"
                            data-gender="{{ $p->gender }}"
                            data-phone="{{ $p->phone }}"
                            data-address="{{ $p->address }}"
                            @selected(old('patient_id', $preselectedPatientId ?? ($visit->patient_id ?? '')) == $p->id)>
                            {{ $p->patient_code }} — {{ $p->full_name }} ({{ $p->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('patients.create', ['return_to' => 'opd']) }}" class="btn-outline-clinic" style="white-space:nowrap;">
                <i class="fa-solid fa-user-plus"></i> New Patient
            </a>
        </div>
    </div>

    <div>
        <label class="form-label-clinic">Gender</label>
        <div class="opd-readonly-chip" id="patient_gender_display">—</div>
    </div>

    <div>
        <label class="form-label-clinic">Phone</label>
        <div class="opd-readonly-chip" id="patient_phone_display">—</div>
    </div>

    <div class="full-span">
        <label class="form-label-clinic">Address</label>
        <div class="opd-readonly-chip" id="patient_address_display">—</div>
    </div>

</div>

<div class="opd-section-title"><i class="fa-solid fa-calendar-check"></i> Visit Details</div>

<div class="form-grid">

    <div>
        <label class="form-label-clinic">Consulting Doctor</label>
        <select name="doctor_id" id="doctor_id" class="form-control-clinic">
            <option value="">Not assigned</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}" @selected(old('doctor_id', $visit->doctor_id ?? '') == $d->id)>
                    {{ $d->full_name }} ({{ $d->specialization }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label-clinic">Visit Date *</label>
        <input type="date" name="visit_date" id="visit_date" class="form-control-clinic"
               value="{{ old('visit_date', isset($visit->visit_date) ? $visit->visit_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>

    <div>
        <label class="form-label-clinic">Visit Type *</label>
        <select name="visit_type" class="form-control-clinic" required>
            @foreach(['New', 'Follow-up', 'Revisit'] as $t)
                <option value="{{ $t }}" @selected(old('visit_type', $visit->visit_type ?? 'New') == $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label-clinic">Token Number</label>
        <input type="text" name="token_number" class="form-control-clinic"
               value="{{ old('token_number', $visit->token_number ?? ($nextToken ?? '')) }}" placeholder="Auto">
    </div>

    <div>
        <label class="form-label-clinic">MLC *</label>
        <select name="mlc" class="form-control-clinic" required>
            <option value="No" @selected(old('mlc', $visit->mlc ?? 'No') == 'No')>No</option>
            <option value="Yes" @selected(old('mlc', $visit->mlc ?? 'No') == 'Yes')>Yes</option>
        </select>
    </div>

    <div>
        <label class="form-label-clinic">Referred By</label>
        <input type="text" name="referred_by" class="form-control-clinic"
               value="{{ old('referred_by', $visit->referred_by ?? '') }}" placeholder="Ref. doctor / self">
    </div>

    <div class="full-span">
        <label class="form-label-clinic">Chief Complaint / Reason for Visit</label>
        <textarea name="chief_complaint" class="form-control-clinic" rows="2">{{ old('chief_complaint', $visit->chief_complaint ?? '') }}</textarea>
    </div>

</div>

<div class="opd-section-title"><i class="fa-solid fa-heart-pulse"></i> Vitals</div>

<div class="form-grid">
    <div>
        <label class="form-label-clinic">Height (cm)</label>
        <input type="number" step="0.1" name="height_cm" class="form-control-clinic" value="{{ old('height_cm', $visit->height_cm ?? '') }}">
    </div>
    <div>
        <label class="form-label-clinic">Weight (kg)</label>
        <input type="number" step="0.1" name="weight_kg" class="form-control-clinic" value="{{ old('weight_kg', $visit->weight_kg ?? '') }}">
    </div>
    <div>
        <label class="form-label-clinic">BP (mmHg)</label>
        <input type="text" name="blood_pressure" class="form-control-clinic" placeholder="120/80" value="{{ old('blood_pressure', $visit->blood_pressure ?? '') }}">
    </div>
    <div>
        <label class="form-label-clinic">Pulse (bpm)</label>
        <input type="number" name="pulse_rate" class="form-control-clinic" value="{{ old('pulse_rate', $visit->pulse_rate ?? '') }}">
    </div>
    <div>
        <label class="form-label-clinic">Temp (°F)</label>
        <input type="number" step="0.1" name="temperature" class="form-control-clinic" value="{{ old('temperature', $visit->temperature ?? '') }}">
    </div>
    <div>
        <label class="form-label-clinic">SpO2 (%)</label>
        <input type="number" min="0" max="100" name="spo2" class="form-control-clinic" value="{{ old('spo2', $visit->spo2 ?? '') }}">
    </div>
    <div class="full-span">
        <label class="form-label-clinic">Symptoms</label>
        <input type="text" name="symptoms" class="form-control-clinic" placeholder="e.g. Fever, Headache..." value="{{ old('symptoms', $visit->symptoms ?? '') }}">
    </div>
</div>

<div class="opd-section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Charges & Payment</div>

<div class="form-grid">

    <div>
        <label class="form-label-clinic">Consultation Fee (₹)</label>
        <input type="number" step="0.01" min="0" name="consultation_fee" id="consultation_fee" class="form-control-clinic"
               value="{{ old('consultation_fee', $visit->consultation_fee ?? 0) }}">
    </div>

    <div>
        <label class="form-label-clinic">Other Charges (₹)</label>
        <input type="number" step="0.01" min="0" name="other_charges" id="other_charges" class="form-control-clinic"
               value="{{ old('other_charges', $visit->other_charges ?? 0) }}">
    </div>

    <div>
        <label class="form-label-clinic">Discount (₹)</label>
        <input type="number" step="0.01" min="0" name="discount" id="discount" class="form-control-clinic"
               value="{{ old('discount', $visit->discount ?? 0) }}">
    </div>

    <div>
        <label class="form-label-clinic">Total Payable (₹)</label>
        <div class="opd-readonly-chip" id="total_payable_display" style="font-weight:700; color: var(--clr-primary);">₹0.00</div>
    </div>

    <div>
        <label class="form-label-clinic">Balance Due (₹)</label>
        <div class="opd-readonly-chip" id="balance_due_display">₹0.00</div>
    </div>

    <div>
        <label class="form-label-clinic">Payment Date</label>
        <input type="date" name="payment_date" class="form-control-clinic"
               value="{{ old('payment_date', isset($visit->payment_date) ? $visit->payment_date->format('Y-m-d') : '') }}">
    </div>

    <div>
        <label class="form-label-clinic">Refund Amount (₹)</label>
        <input type="number" step="0.01" min="0" name="refund_amount" class="form-control-clinic"
               value="{{ old('refund_amount', $visit->refund_amount ?? 0) }}">
    </div>

    <div>
        <label class="form-label-clinic">Status *</label>
        <select name="status" class="form-control-clinic" required>
            @foreach(['Paid', 'Partial', 'Pending', 'Refunded'] as $s)
                <option value="{{ $s }}" @selected(old('status', $visit->status ?? 'Pending') == $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <div class="full-span">
        <label class="form-label-clinic">Payment Split *</label>
        <div id="paymentRows"></div>

        <button type="button" class="btn-outline-clinic" id="addPaymentRow" style="margin-top:8px;">
            <i class="fa-solid fa-plus"></i> Add Payment
        </button>

        <div style="margin-top:10px; display:flex; gap:20px; font-size:0.88rem;">
            <span>Total Entered: <strong id="totalEnteredDisplay">₹0.00</strong></span>
            <span id="paymentBalanceWarning" style="color: var(--clr-warn, #b45309); font-weight:600;"></span>
        </div>
    </div>

    <div class="full-span">
        <label class="form-label-clinic">Notes</label>
        <textarea name="notes" class="form-control-clinic" rows="3">{{ old('notes', $visit->notes ?? '') }}</textarea>
    </div>

</div>

<template id="paymentRowTemplate">
    <div class="opd-patient-row payment-row" style="margin-bottom:8px;">
        <div style="flex:1;">
            <select name="payments[__i__][method]" class="form-control-clinic payment-method" required>
                <option value="">Method</option>
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
                <option value="Cheque">Cheque</option>
                <option value="Card">Card</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div style="flex:1;">
            <input type="number" step="0.01" min="0.01" name="payments[__i__][amount]"
                   class="form-control-clinic payment-amount" placeholder="Amount (₹)" required>
        </div>
        <div style="flex:1;">
            <input type="text" name="payments[__i__][reference_no]"
                   class="form-control-clinic" placeholder="Ref / Cheque / UPI ID (optional)">
        </div>
        <button type="button" class="btn-outline-clinic removePaymentRow" title="Remove">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</template>

<script>
(function () {
    const patientSelect = document.getElementById('patient_id');
    const genderDisplay = document.getElementById('patient_gender_display');
    const phoneDisplay = document.getElementById('patient_phone_display');
    const addressDisplay = document.getElementById('patient_address_display');

    function fillPatientDisplay() {
        const opt = patientSelect.options[patientSelect.selectedIndex];
        if (!opt || !opt.value) {
            genderDisplay.textContent = '—';
            phoneDisplay.textContent = '—';
            addressDisplay.textContent = '—';
            return;
        }
        genderDisplay.textContent = opt.dataset.gender || '—';
        phoneDisplay.textContent = opt.dataset.phone || '—';
        addressDisplay.textContent = opt.dataset.address || '—';
    }
    patientSelect.addEventListener('change', fillPatientDisplay);
    fillPatientDisplay();

    // Doctor -> fee auto-fill (only if consultation fee is currently 0/empty, so we don't clobber edits)
    const doctorSelect = document.getElementById('doctor_id');
    const feeInput = document.getElementById('consultation_fee');
    doctorSelect.addEventListener('change', function () {
        if (!this.value) return;
        if (parseFloat(feeInput.value) > 0) return; // don't override an existing fee
        fetch("{{ url('/opd/doctor-fee') }}/" + this.value)
            .then(r => r.json())
            .then(data => {
                feeInput.value = data.fee;
                recalcTotals();
            })
            .catch(() => {});
    });

    // ---- Charges totals ----
    const consultationFee = document.getElementById('consultation_fee');
    const otherCharges = document.getElementById('other_charges');
    const discount = document.getElementById('discount');
    const totalDisplay = document.getElementById('total_payable_display');
    const balanceDisplay = document.getElementById('balance_due_display');
    const totalEnteredDisplay = document.getElementById('totalEnteredDisplay');
    const paymentWarning = document.getElementById('paymentBalanceWarning');

    function getTotalPayable() {
        const cf = parseFloat(consultationFee.value) || 0;
        const oc = parseFloat(otherCharges.value) || 0;
        const dc = parseFloat(discount.value) || 0;
        return Math.max(0, cf + oc - dc);
    }

    function getTotalEntered() {
        let total = 0;
        document.querySelectorAll('.payment-amount').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        return total;
    }

    function recalcTotals() {
        const total = getTotalPayable();
        const entered = getTotalEntered();
        const balance = Math.max(0, total - entered);

        totalDisplay.textContent = '₹' + total.toFixed(2);
        balanceDisplay.textContent = '₹' + balance.toFixed(2);
        totalEnteredDisplay.textContent = '₹' + entered.toFixed(2);

        if (entered > total) {
            paymentWarning.textContent = 'Entered amount exceeds Total Payable!';
        } else if (entered < total && entered > 0) {
            paymentWarning.textContent = 'Balance due: ₹' + (total - entered).toFixed(2);
        } else {
            paymentWarning.textContent = '';
        }
    }
    [consultationFee, otherCharges, discount].forEach(el => {
        el.addEventListener('input', recalcTotals);
    });

    // ---- Dynamic payment rows ----
    let rowIndex = 0;
    const rowsContainer = document.getElementById('paymentRows');
    const rowTemplate = document.getElementById('paymentRowTemplate').innerHTML;

    function addRow(method = '', amount = '', referenceNo = '') {
        const html = rowTemplate.replaceAll('__i__', rowIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const rowEl = wrapper.firstElementChild;

        if (method) rowEl.querySelector('.payment-method').value = method;
        if (amount) rowEl.querySelector('.payment-amount').value = amount;
        if (referenceNo) rowEl.querySelector('input[name$="[reference_no]"]').value = referenceNo;

        rowsContainer.appendChild(rowEl);
        rowIndex++;
        recalcTotals();
    }

    document.getElementById('addPaymentRow').addEventListener('click', () => addRow());

    rowsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('payment-amount')) recalcTotals();
    });

    rowsContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.removePaymentRow');
        if (btn) {
            btn.closest('.payment-row').remove();
            recalcTotals();
        }
    });

    // Pre-fill existing payments when editing, otherwise start with one blank row
    @if(isset($visit) && $visit->payments && $visit->payments->count())
        @foreach($visit->payments as $p)
            addRow(@json($p->method), @json($p->amount), @json($p->reference_no));
        @endforeach
    @else
        addRow();
    @endif

    recalcTotals();
})();
</script>
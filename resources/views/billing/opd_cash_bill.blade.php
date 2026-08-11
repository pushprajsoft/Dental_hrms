<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPD Cash Bill - {{ $visit->visit_code }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; background: #e6e6e6; margin: 0; padding: 20px; color: #000; font-size: 16px; }
        .bill-container { max-width: 800px; margin: auto; background: #fff; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        /* Header - Renders Word Editor Content + Dynamic Logo ONLY */
        .header { 
            display: flex; 
            align-items: center; 
            justify-content: flex-start; 
            gap: 25px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 15px; 
            margin-bottom: 20px;
            overflow: hidden;
        }
        .header img { 
            max-height: 80px; 
            width: auto; 
            flex-shrink: 0;
        }
        .header-content { 
            flex: 1; 
        }
        /* Ensure styles from TinyMCE render nicely */
        .header-content h1, .header-content h2, .header-content h3 { margin: 0 0 5px 0; }
        .header-content p { margin: 4px 0; font-size: 14px; }
        
        .bill-title { text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0; text-transform: uppercase; letter-spacing: 1px; }

        /* Patient Info Grid - 2 Equal Columns with Center Gap */
        .patient-info { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 60px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            border: 1px solid #000; 
            padding: 15px; 
        }
        .patient-col { 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
        }
        .patient-col div { display: flex; gap: 5px; }
        .patient-col strong { font-weight: bold; white-space: nowrap; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f2f2f2; font-weight: bold; text-align: center; }
        .col-sn { width: 40px; text-align: center; }
        .col-qty { width: 60px; text-align: center; }
        .col-price { width: 90px; text-align: right; }
        .col-amount { width: 100px; text-align: right; }
        .col-action { width: 40px; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Totals */
        .totals-section { display: flex; justify-content: space-between; margin-top: 20px; }
        .totals-box { width: 50%; margin-left: auto; font-size: 14px; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #ccc; }
        .totals-row.final { font-weight: bold; font-size: 16px; border-top: 2px solid #000; border-bottom: none; margin-top: 10px; padding-top: 10px; }

        /* Footer & Buttons */
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; font-size: 14px; }
        .sign-box { text-align: center; width: 200px; border-top: 1px solid #000; padding-top: 5px; }
        .footer-note { text-align: center; margin-top: 30px; font-size: 14px; font-style: italic; }
        .meta-info { display: flex; justify-content: space-between; font-size: 12px; color: #555; margin-top: 40px; border-top: 1px dashed #ccc; padding-top: 10px; }

        .btn-add { background: #3FBFAD; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-bottom: 10px; }
        .btn-remove { color: #dc2626; cursor: pointer; background: none; border: none; font-size: 16px; }
        .input-field { width: 100%; border: none; background: transparent; font-family: inherit; font-size: inherit; padding: 0; }
        .input-field:focus { outline: 1px solid #3FBFAD; background: #f8fcfa; }

        .no-print { display: block; text-align: center; margin-top: 20px; }
        .btn-print { background: #123C3A; color: #fff; border: none; padding: 12px 30px; font-size: 16px; cursor: pointer; border-radius: 6px; }

        @media print {
            body { margin: 0; padding: 0; background: #fff; }
            .bill-container { box-shadow: none; max-width: 100%; padding: 20px; }
            .no-print { display: none !important; }
            .input-field { border: none; }
        }
    </style>
</head>
<body>

<div class="bill-container">
    
    @php
        // Set dynamic CSS for Logo Alignment & Size
        $headerFlexDirection = 'row';
        $headerJustifyContent = 'flex-start';
        $headerTextAlign = 'left';
        
        if ($settings->logo_alignment == 'top') {
            $headerFlexDirection = 'column';
            $headerJustifyContent = 'center';
            $headerTextAlign = 'center';
        } elseif ($settings->logo_alignment == 'right') {
            $headerJustifyContent = 'flex-end';
            $headerFlexDirection = 'row-reverse';
            $headerTextAlign = 'right';
        } elseif ($settings->logo_alignment == 'none') {
            $headerJustifyContent = 'center';
            $headerTextAlign = 'center';
        }
        
        $logoHeight = $settings->logo_size . 'px';
    @endphp

    <!-- Header (Renders EXACTLY what you designed in the Word Editor + Dynamic Logo) -->
    <div class="header" style="flex-direction: {{ $headerFlexDirection }}; justify-content: {{ $headerJustifyContent }};">
        @if($settings->logo_path && $settings->logo_alignment != 'none' && file_exists(public_path($settings->logo_path)))
            <img src="{{ asset($settings->logo_path) }}" alt="Logo" style="max-height: {{ $logoHeight }}; width: auto;">
        @endif
        <div class="header-content" style="text-align: {{ $headerTextAlign }};">
            {!! $settings->header_html !!}
        </div>
    </div>

    <div class="bill-title">OPD Cash Bill</div>

    <!-- Patient Info (Balanced Left and Right) -->
    <div class="patient-info">
        <div class="patient-col">
            <div><strong>Patient Name:</strong> {{ $visit->patient->full_name ?? 'N/A' }}</div>
            <div><strong>Age/Sex:</strong> {{ $visit->patient->age ?? 'N/A' }} / {{ $visit->patient->gender ?? 'N/A' }}</div>
            <div><strong>Father Name:</strong> {{ $visit->patient->fh_name ?? 'N/A' }}</div>
            <div><strong>Mother Name:</strong> {{ $visit->patient->mother_name ?? 'N/A' }}</div>
            <div><strong>Address:</strong> {{ $visit->patient->address ?? 'N/A' }}</div>
        </div>
        <div class="patient-col">
            <div><strong>UHID NO:</strong> {{ $visit->patient->patient_code ?? 'N/A' }}</div>
            <div><strong>Bill No:</strong> {{ $visit->visit_code }}</div>
            <div><strong>Date/Time:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('d-M-Y / h:i A') }}</div>
            <div><strong>Mobile:</strong> {{ $visit->patient->phone ?? 'N/A' }}</div>
            <div><strong>Consultant:</strong> Dr. {{ $visit->doctor->full_name ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <table id="itemsTable">
        <thead>
            <tr>
                <th class="col-sn">SN</th>
                <th>Service Name</th>
                <th class="col-qty">Qty</th>
                <th class="col-price">Price/Qty</th>
                <th class="col-amount">Amount</th>
                <th class="col-action no-print"></th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            <!-- Initial Row populated by JS -->
        </tbody>
    </table>

    <button type="button" class="btn-add no-print" onclick="addItem()"><i class="fa-solid fa-plus"></i> Add Item</button>

    <!-- Totals & Payment -->
    <div class="totals-section">
        <div style="width: 48%;">
            <div class="totals-row"><strong>Status:</strong> <span id="statusDisplay">{{ $visit->status }}</span></div>
            <div class="totals-row"><strong>Paid Amount:</strong> ₹ <input type="number" class="input-field text-right" id="paidAmount" value="{{ $visit->amount_paid }}" oninput="calculateTotals()" style="width:100px; border:1px solid #ccc;"></div>
            <div class="totals-row"><strong>Balance Amt.:</strong> ₹ <span id="balanceAmount">0.00</span></div>
        </div>
        <div class="totals-box">
            <div class="totals-row"><span>Total Amount:</span> <span>₹ <span id="totalAmount">0.00</span></span></div>
            <div class="totals-row"><span>Discount Amt.:</span> <span>₹ <input type="number" class="input-field text-right" id="discountAmount" value="{{ $visit->discount }}" oninput="calculateTotals()" style="width:80px; border:1px solid #ccc;"></span></span></div>
            <div class="totals-row final"><span>Final Payment Amt.:</span> <span>₹ <span id="finalAmount">0.00</span></span></div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sign-box">Signature of Recipient</div>
        <div class="sign-box">Authorized Signatory</div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        {!! $settings->footer_html !!}
    </div>

    <div class="meta-info">
        <div>Created By: {{ auth()->user()->name ?? 'System' }}</div>
        <div>Printed By: {{ auth()->user()->name ?? 'System' }}</div>
        <div>Printed On: {{ now()->format('d-M-Y h:i A') }}</div>
    </div>
</div>

<!-- Print Button -->
<div class="no-print" style="text-align: center; margin-top: 20px;">
    <button onclick="window.print()" class="btn-print">
        <i class="fa-solid fa-print"></i> Print / Save as PDF
    </button>
</div>

<script>
    let itemCount = 0;

    // Add a new row to the table
    function addItem(serviceName = '', qty = 1, price = 0) {
        itemCount++;
        const table = document.getElementById('itemsBody');
        const row = table.insertRow();
        
        row.innerHTML = `
            <td class="col-sn text-center">${itemCount}</td>
            <td><input type="text" class="input-field" value="${serviceName}" placeholder="Service Name"></td>
            <td class="col-qty"><input type="number" class="input-field text-center" value="${qty}" oninput="calculateRow(this)" min="1"></td>
            <td class="col-price"><input type="number" class="input-field text-right" value="${price}" oninput="calculateRow(this)" step="0.01"></td>
            <td class="col-amount text-right"><span class="row-amount">0.00</span></td>
            <td class="col-action no-print"><button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button></td>
        `;

        calculateRow(row.querySelector('input[type="number"]'));
    }

    // Calculate amount for a specific row
    function calculateRow(element) {
        const row = element.closest('tr');
        const qty = parseFloat(row.querySelector('input[type="number"][min="1"]').value) || 0;
        const price = parseFloat(row.querySelector('input[type="number"][step="0.01"]').value) || 0;
        const amount = qty * price;
        row.querySelector('.row-amount').innerText = amount.toFixed(2);
        calculateTotals();
    }

    // Remove a row
    function removeItem(button) {
        const row = button.closest('tr');
        row.remove();
        // Re-number SN
        let rows = document.querySelectorAll('#itemsBody tr');
        itemCount = 0;
        rows.forEach(r => {
            itemCount++;
            r.querySelector('.col-sn').innerText = itemCount;
        });
        calculateTotals();
    }

    // Calculate all totals
    function calculateTotals() {
        let total = 0;
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            total += parseFloat(row.querySelector('.row-amount').innerText) || 0;
        });

        document.getElementById('totalAmount').innerText = total.toFixed(2);

        const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
        const finalAmount = total - discount;
        document.getElementById('finalAmount').innerText = finalAmount.toFixed(2);

        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        const balance = finalAmount - paid;
        document.getElementById('balanceAmount').innerText = balance.toFixed(2);
        
        // Update Status
        let status = 'Unpaid';
        if (balance <= 0 && paid > 0) status = 'Paid';
        else if (paid > 0 && balance > 0) status = 'Partial';
        document.getElementById('statusDisplay').innerText = status;
    }

    // Initialize with the existing consultation fee
    window.onload = function() {
        addItem('Consultation & Treatment Charges', 1, {{ $visit->consultation_fee ?? 0 }});
        @if($visit->other_charges > 0)
            addItem('Other Charges / Procedures', 1, {{ $visit->other_charges }});
        @endif
    };
</script>

</body>
</html>
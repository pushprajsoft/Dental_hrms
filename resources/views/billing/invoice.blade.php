<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $visit->visit_code }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; margin: 0; padding: 20px; color: #333; }
        .invoice-container { max-width: 850px; margin: auto; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        
        /* Header */
        .header { background: linear-gradient(135deg, #123C3A, #3FBFAD); color: #fff; padding: 30px; display: flex; justify-content: space-between; align-items: center; }
        .clinic-name { font-size: 28px; font-weight: 700; margin: 0; }
        .clinic-details { font-size: 13px; opacity: 0.9; margin-top: 5px; line-height: 1.5; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0; font-size: 36px; text-transform: uppercase; opacity: 0.9; }
        .invoice-meta { font-size: 13px; margin-top: 10px; }

        /* Body */
        .body { padding: 30px; }
        .billed-to { margin-bottom: 30px; display: flex; justify-content: space-between; }
        .billed-to h4 { margin: 0 0 8px 0; color: #123C3A; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        .billed-to p { margin: 2px 0; font-size: 14px; }

        /* Dynamic Table */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th { background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; padding: 12px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        .item-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; }
        .item-input { width: 100%; border: 1px solid transparent; background: transparent; padding: 8px; font-size: 14px; border-radius: 6px; font-family: inherit; }
        .item-input:focus { outline: none; border-color: #3FBFAD; background: #f8fcfa; }
        .item-input.number { text-align: right; }
        
        .action-btns { text-align: right; margin-bottom: 20px; }
        .btn-add { background: #ecfdf5; color: #15803D; border: 1px dashed #15803D; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .btn-remove { color: #EF4444; cursor: pointer; background: none; border: none; font-size: 16px; }
        .btn-remove:hover { color: #B91C1C; }

        /* Totals */
        .totals-box { margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .total-row input { width: 80px; text-align: right; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px; }
        .grand-total { background: #123C3A; color: #fff; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: 700; margin-top: 10px; }

        /* Footer Actions */
        .footer-actions { padding: 30px; background: #f8fafc; display: flex; justify-content: center; gap: 15px; border-top: 1px solid #e2e8f0; }
        .btn { padding: 12px 30px; border-radius: 8px; border: none; font-size: 15px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-2px); }
        .btn-print { background: #3FBFAD; color: #fff; }
        .btn-whatsapp { background: #25D366; color: #fff; }
        
        .invoice-footer { text-align: center; padding: 20px; font-size: 12px; color: #94a3b8; }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .invoice-container { box-shadow: none; border-radius: 0; max-width: 100%; }
            .item-input { border: none; }
            .btn-remove { display: none !important; }
        }
    </style>
</head>
<body>

<!-- Load html2pdf library for generating colorful PDFs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="invoice-container" id="invoiceContent">
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="clinic-name">{{ $settings->clinic_name }}</h1>
            <div class="clinic-details">
                {{ $settings->clinic_address }}<br>
                <i class="fa-solid fa-phone"></i> {{ $settings->clinic_phone }} | 
                <i class="fa-solid fa-envelope"></i> {{ $settings->clinic_email }}<br>
                @if($settings->gst_enabled) <strong>GSTIN:</strong> {{ $settings->gst_number }} @endif
            </div>
        </div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="invoice-meta">
                <strong>Bill No:</strong> {{ $visit->visit_code }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y') }}
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="body">
        <div class="billed-to">
            <div>
                <h4>Billed To</h4>
                <p><strong>{{ $visit->patient->full_name ?? 'Walk-in Patient' }}</strong></p>
                <p>{{ $visit->patient->phone ?? '' }}</p>
                <p>{{ $visit->patient->address ?? '' }}</p>
            </div>
            <div style="text-align: right;">
                <h4>Doctor</h4>
                <p><strong>Dr. {{ $visit->doctor->full_name ?? 'N/A' }}</strong></p>
                <p>Payment Mode: {{ $visit->payment_method }}</p>
            </div>
        </div>

        <!-- Dynamic Items Table -->
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Rate (₹)</th>
                    <th style="width: 15%;">Amount (₹)</th>
                    <th style="width: 5%;"></th>
                </tr>
            </thead>
            <tbody id="itemRows">
                <!-- Initial Row -->
                <tr>
                    <td><input type="text" class="item-input" value="OPD Consultation & Treatment ({{ $visit->visit_code }})"></td>
                    <td><input type="number" class="item-input qty" value="1" oninput="calculateTotal()"></td>
                    <td><input type="number" class="item-input rate" value="{{ $visit->total_amount }}" oninput="calculateTotal()"></td>
                    <td><input type="text" class="item-input amount" value="{{ number_format($visit->total_amount, 2) }}" readonly></td>
                    <td><button class="btn-remove no-print" onclick="removeRow(this)"><i class="fa-solid fa-times-circle"></i></button></td>
                </tr>
            </tbody>
        </table>

        <div class="action-btns no-print">
            <button class="btn-add" onclick="addRow()"><i class="fa-solid fa-plus"></i> Add Item</button>
        </div>

        <!-- Totals -->
        <div class="totals-box">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹ <span id="subTotal">{{ number_format($visit->total_amount, 2) }}</span></span>
            </div>
            
            <div class="total-row no-print">
                <span>GST (<input type="number" id="gstRate" value="{{ $settings->gst_enabled ? $settings->gst_percentage : 0 }}" oninput="calculateTotal()" style="width: 50px;">%):</span>
                <span>₹ <span id="gstAmount">0.00</span></span>
            </div>
            <!-- Hidden GST info for print -->
            <div class="total-row" style="display: none;" id="printGst">
                <span>GST ({{ $settings->gst_percentage }}%):</span>
                <span>₹ <span id="printGstAmount">0.00</span></span>
            </div>

            <div class="grand-total">
                <span>Total Paid:</span>
                <span>₹ <span id="grandTotal">{{ number_format($visit->total_amount, 2) }}</span></span>
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="footer-actions no-print">
        <button class="btn btn-print" onclick="window.print()"><i class="fa-solid fa-print"></i> Print / Save PDF</button>
        <button class="btn btn-whatsapp" onclick="sendWhatsApp()"><i class="fa-brands fa-whatsapp"></i> Generate PDF & Send WhatsApp</button>
    </div>

    <div class="invoice-footer">
        {{ $settings->footer_notes }}
    </div>
</div>

<script>
    // 1. Add new empty row
    function addRow() {
        const table = document.getElementById('itemRows');
        const row = table.insertRow();
        row.innerHTML = `
            <td><input type="text" class="item-input" placeholder="New item description"></td>
            <td><input type="number" class="item-input qty" value="1" oninput="calculateTotal()"></td>
            <td><input type="number" class="item-input rate" value="0" oninput="calculateTotal()"></td>
            <td><input type="text" class="item-input amount" value="0.00" readonly></td>
            <td><button class="btn-remove no-print" onclick="removeRow(this)"><i class="fa-solid fa-times-circle"></i></button></td>
        `;
        calculateTotal();
    }

    // 2. Remove row
    function removeRow(btn) {
        const row = btn.parentNode.parentNode;
        if(document.getElementById('itemRows').rows.length > 1) {
            row.parentNode.removeChild(row);
            calculateTotal();
        }
    }

    // 3. Live Calculation Engine
    function calculateTotal() {
        let subTotal = 0;
        const rows = document.querySelectorAll('#itemRows tr');

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            const amount = qty * rate;
            
            row.querySelector('.amount').value = amount.toFixed(2);
            subTotal += amount;
        });

        const gstRate = parseFloat(document.getElementById('gstRate').value) || 0;
        const gstAmount = (subTotal * gstRate) / 100;
        const grandTotal = subTotal + gstAmount;

        document.getElementById('subTotal').innerText = subTotal.toFixed(2);
        document.getElementById('gstAmount').innerText = gstAmount.toFixed(2);
        document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);

        document.getElementById('printGstAmount').innerText = gstAmount.toFixed(2);
    }

    // 4. Generate Colorful PDF and Send via WhatsApp
    function sendWhatsApp() {
        calculateTotal(); // ensure math is correct
        
        // Hide no-print elements so the PDF looks clean
        const noPrintElements = document.querySelectorAll('.no-print');
        noPrintElements.forEach(el => el.style.visibility = 'hidden');

        // Hide the input borders for the PDF generation
        const inputs = document.querySelectorAll('.item-input');
        inputs.forEach(el => el.style.border = 'none');

        const element = document.getElementById('invoiceContent');
        const opt = {
            margin: 0,
            filename: 'Invoice_{{ $visit->visit_code }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Generate PDF -> Download it -> Open WhatsApp
        html2pdf().set(opt).from(element).save().then(() => {
            
            // Restore the UI for editing again
            noPrintElements.forEach(el => el.style.visibility = 'visible');
            inputs.forEach(el => el.style.border = '1px solid transparent');

            // Open WhatsApp with pre-filled message
            const patientName = "{{ $visit->patient->full_name ?? 'Patient' }}";
            const billNo = "{{ $visit->visit_code }}";
            const grandTotal = document.getElementById('grandTotal').innerText;
            const clinicName = "{{ $settings->clinic_name }}";
            
            let phone = "{{ $visit->patient->phone ?? '' }}";
            phone = phone.replace(/\D/g, '');
            if(phone.length === 10) phone = "91" + phone;

            const message = `Hello *${patientName}*,\n\nYour invoice from *${clinicName}* has been generated.\n\n*Bill No:* ${billNo}\n*Total Amount:* ₹${grandTotal}\n\n*Please find the PDF invoice attached.*\n\nThank you for visiting us!`;
            
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        });
    }

    // Init print GST display logic
    window.onbeforeprint = function() {
        let gstRate = parseFloat(document.getElementById('gstRate').value) || 0;
        if(gstRate > 0) {
            document.getElementById('printGst').style.display = 'flex';
        } else {
            document.getElementById('printGst').style.display = 'none';
        }
    };
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $visit->visit_code }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; background: #e6e6e6; margin: 0; padding: 20px; color: #000; font-size: 15px; }
        .prescription-container { max-width: 800px; margin: auto; background: #fff; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); min-height: 1100px; display: flex; flex-direction: column; box-sizing: border-box; }
        
        /* Header */
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
        .header img { max-height: 80px; width: auto; flex-shrink: 0; }
        .header-content { flex: 1; }
        .header-content h1, .header-content h2, .header-content h3 { margin: 0 0 5px 0; }
        .header-content p { margin: 4px 0; font-size: 14px; }
        
        /* Patient Info Grid - Exactly matching the reference picture */
        .patient-info { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 60px; /* Clean space in the center */
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

        /* Rx Symbol */
        .rx-symbol { 
            font-size: 42px; 
            font-weight: bold; 
            font-family: 'Times New Roman', Times, serif; 
            margin: 10px 0 30px 10px; 
            text-decoration: underline;
            line-height: 1;
        }

        /* Blank Prescription Area */
        .prescription-area { 
            flex: 1; 
            min-height: 500px; 
        }

        /* Footer & Buttons */
        .signatures { display: flex; justify-content: flex-end; margin-top: 40px; }
        .sign-box { text-align: center; width: 250px; border-top: 1px solid #000; padding-top: 5px; font-size: 14px; font-weight: bold; }
        .footer-note { text-align: center; margin-top: 20px; font-size: 13px; font-style: italic; border-top: 1px dashed #ccc; padding-top: 15px; }

        .no-print { display: block; text-align: center; margin-top: 20px; }
        .btn-print { background: #123C3A; color: #fff; border: none; padding: 12px 30px; font-size: 16px; cursor: pointer; border-radius: 6px; }

        @media print {
            body { margin: 0; padding: 0; background: #fff; }
            .prescription-container { box-shadow: none; max-width: 100%; padding: 20px; min-height: auto; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="prescription-container">
    
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

    <!-- Patient Info (Matches Picture Exactly + Added Fields) -->
    <div class="patient-info">
        <div class="patient-col">
            <div><strong>Patient Name:</strong> {{ $visit->patient->full_name ?? 'N/A' }}</div>
            <div><strong>Age/Sex:</strong> {{ $visit->patient->age ?? 'N/A' }} / {{ $visit->patient->gender ?? 'N/A' }}</div>
            <div><strong>Father Name:</strong> {{ $visit->patient->fh_name ?? 'N/A' }}</div>
            <div><strong>Mother Name:</strong> {{ $visit->patient->mother_name ?? 'N/A' }}</div>
            <div><strong>Address:</strong> {{ $visit->patient->address ?? 'N/A' }}</div>
            <div><strong>Category:</strong> {{ $visit->visit_type ?? 'GENERAL' }}</div>
        </div>
        <div class="patient-col">
            <div><strong>UHID NO:</strong> {{ $visit->patient->patient_code ?? 'N/A' }}</div>
            <div><strong>Bill No:</strong> {{ $visit->visit_code }}</div>
            <div><strong>Date/Time:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('d-M-Y / h:i A') }}</div>
            <div><strong>Mobile:</strong> {{ $visit->patient->phone ?? 'N/A' }}</div>
            <div><strong>Consultant:</strong> Dr. {{ $visit->doctor->full_name ?? 'N/A' }}</div>
            <div><strong>Token No.:</strong> {{ $visit->token_number ?? '1' }}</div>
            <div><strong>Corporate:</strong> {{ $visit->corporate ? 'Yes' : 'No' }}</div>
        </div>
    </div>

    <!-- Prescription Symbol (After details) -->
    <div class="rx-symbol">Rx</div>

    <!-- BLANK PRESCRIPTION AREA (For writing medicines) -->
    <div class="prescription-area">
        <!-- Intentionally left blank for handwritten or digital prescription -->
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sign-box">Doctor's Signature</div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        {!! $settings->footer_html !!}
    </div>

</div>

<!-- Print Button -->
<div class="no-print" style="text-align: center; margin-top: 20px;">
    <button onclick="window.print()" class="btn-print">
        <i class="fa-solid fa-print"></i> Print / Save as PDF
    </button>
</div>

</body>
</html>
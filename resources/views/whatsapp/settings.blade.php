@extends('layouts.app')

@section('title', 'WhatsApp Settings')
@section('page-title', 'WhatsApp')
@section('page-subtitle', 'Manage patient messaging settings')

@section('content')

    <div class="panel" style="max-width: 640px; margin-bottom: 24px;">

        @if($errors->any())
            <div class="alert-clinic" style="background: var(--clr-warn-soft); color: var(--clr-warn);">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('whatsapp.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <label class="form-label-clinic">Country Code</label>
            <input type="text" name="country_code" class="form-control-clinic"
                   value="{{ old('country_code', $settings->country_code) }}"
                   placeholder="e.g. 91 for India" style="margin-bottom:18px; max-width:160px;">

            <label class="form-label-clinic">Clinic Support Number (for the Quick Support button)</label>
            <input type="text" name="support_number" class="form-control-clinic"
                   value="{{ old('support_number', $settings->support_number) }}"
                   placeholder="e.g. 9876543210" style="margin-bottom:18px;">

            <label class="form-label-clinic">Thank-You Message Template</label>
            <textarea name="thank_you_template" class="form-control-clinic" rows="4"
                      style="margin-bottom:8px;">{{ old('thank_you_template', $settings->thank_you_template) }}</textarea>
            <div style="color: var(--clr-muted); font-size:0.8rem; margin-bottom:22px;">
                Use <code>{name}</code> anywhere you want the patient's name inserted automatically.
            </div>

            <hr style="border:none; border-top:1px solid var(--clr-border); margin:22px 0;">

            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                <div>
                    <label class="form-label-clinic" style="margin-bottom:2px;">Auto-Schedule Thank-You Messages</label>
                    <div style="color: var(--clr-muted); font-size:0.8rem;">
                        When on, newly registered patients appear below, ready to message at the time you set.
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="auto_schedule_enabled" value="1"
                           {{ old('auto_schedule_enabled', $settings->auto_schedule_enabled) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <label class="form-label-clinic">Send Time</label>
            <input type="time" name="scheduled_time" class="form-control-clinic"
                   value="{{ old('scheduled_time', \Carbon\Carbon::parse($settings->scheduled_time)->format('H:i')) }}"
                   style="margin-bottom:22px; max-width:160px;">

            <button type="submit" class="btn-clinic">
                <i class="fa-solid fa-floppy-disk"></i> Save Settings
            </button>
        </form>
    </div>

    <div class="panel" style="max-width: 640px;">
        <h3 style="margin-top:0; margin-bottom:4px;">Scheduled Messages</h3>
        <div style="color: var(--clr-muted); font-size:0.85rem; margin-bottom:18px;">
            Patients registered in the last 3 days, waiting on their thank-you message.
        </div>

        @if(!$settings->auto_schedule_enabled)
            <div class="alert-clinic" style="background: var(--clr-warn-soft); color: var(--clr-warn);">
                <i class="fa-solid fa-power-off"></i> Auto-scheduling is currently off. Turn it on above to start tracking.
            </div>
        @elseif($pendingPatients->isEmpty())
            <div style="color: var(--clr-muted); font-size:0.9rem;">No pending messages right now — you're all caught up.</div>
        @else
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--clr-border); text-align:left;">
                        <th style="padding:8px 6px; font-size:0.8rem; color: var(--clr-muted);">Patient</th>
                        <th style="padding:8px 6px; font-size:0.8rem; color: var(--clr-muted);">Registered</th>
                        <th style="padding:8px 6px; font-size:0.8rem; color: var(--clr-muted);">Status</th>
                        <th style="padding:8px 6px; font-size:0.8rem; color: var(--clr-muted);"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPatients as $patient)
                        @php
                            $sendAt = \Carbon\Carbon::parse($patient->created_at->format('Y-m-d') . ' ' . $settings->scheduled_time);
                            $isReady = now()->greaterThanOrEqualTo($sendAt);
                        @endphp
                        <tr id="pending-row-{{ $patient->id }}" style="border-bottom: 1px solid var(--clr-border);">
                            <td style="padding:10px 6px; font-weight:500;">{{ $patient->full_name }}</td>
                            <td style="padding:10px 6px; color: var(--clr-muted); font-size:0.85rem;">{{ $patient->created_at->format('d M, g:i A') }}</td>
                            <td style="padding:10px 6px;">
                                @if($isReady)
                                    <span style="color:#2E9E6B; font-weight:600; font-size:0.82rem;"><i class="fa-solid fa-circle-check"></i> Ready</span>
                                @else
                                    <span style="color: var(--clr-muted); font-size:0.82rem;"><i class="fa-solid fa-clock"></i> Waiting until {{ $sendAt->format('g:i A') }}</span>
                                @endif
                            </td>
                            <td style="padding:10px 6px; text-align:right;">
                                @if($isReady)
                                    <a href="{{ $settings->thankYouLinkFor($patient->phone, $patient->full_name) }}"
                                       target="_blank"
                                       class="btn-clinic"
                                       style="padding:6px 14px; font-size:0.82rem;"
                                       onclick="markWhatsappSent({{ $patient->id }})">
                                        <i class="fa-brands fa-whatsapp"></i> Send Now
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection

@section('scripts')
<script>
    function markWhatsappSent(patientId) {
        fetch(`/whatsapp/mark-sent/${patientId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('pending-row-' + patientId);
                if (row) {
                    row.style.opacity = '0.4';
                    row.querySelector('td:last-child').innerHTML =
                        '<span style="color:#2E9E6B; font-size:0.82rem;"><i class="fa-solid fa-check"></i> Sent</span>';
                }
            }
        })
        .catch(err => console.error('Failed to mark as sent:', err));
    }
</script>
@endsection
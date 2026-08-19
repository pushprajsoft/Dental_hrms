@extends('layouts.app')

@section('title', 'Add OPD Visit')
@section('page-title', 'Add New OPD Visit')
@section('page-subtitle', 'Record a new out-patient visit')

@section('content')

    <div class="panel">
        <!-- Added id="opdForm" to target this specific form -->
        <form action="{{ route('opd.store') }}" method="POST" id="opdForm">
            @csrf
            @include('opd._form')

            <div style="margin-top:26px; display:flex; gap:12px;">
                <!-- Added id="saveBtn" to target this specific button -->
                <button type="submit" class="btn-clinic" id="saveBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Visit
                </button>
                <a href="{{ route('opd.index') }}" class="btn-outline-clinic">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
<script>
    // Prevent multiple form submissions (Fixes the 3 entries bug)
    document.addEventListener('DOMContentLoaded', function() {
        const opdForm = document.getElementById('opdForm');
        
        if (opdForm) {
            opdForm.addEventListener('submit', function(event) {
                const submitButton = document.getElementById('saveBtn');
                
                // If button is already disabled, stop the form from submitting again
                if (submitButton && submitButton.disabled) {
                    event.preventDefault();
                    return;
                }

                // Disable the button and change text
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
                }
            });
        }
    });
</script>
@endsection
@extends('layouts.app')
@section('title', 'File Leave Request | Natanem Engineering')

@push('head')
{{-- Flatpickr for better date picking and disabling dates --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-input[readonly] { background-color: #f1f5f9 !important; cursor: pointer; }
    .availability-badge { display: none; }
</style>
@endpush

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">File Leave Request</h1>
        <p class="text-muted mb-0">Submit a formal time-off request for administrative review.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('hr.leaves.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Portfolio
        </a>
    </div>
</div>

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-8">
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('hr.leaves.store') }}" method="POST" id="leaveForm">
                    @csrf
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-calendar2-range text-primary"></i>
                            Request Details
                        </h5>

                        {{-- Employee Selection --}}
                        <div class="mb-4">
                            <label class="form-label small fw-800 text-muted text-uppercase">Select Employee</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-soft border-0"><i class="bi bi-person-badge"></i></span>
                                <select name="employee_id" id="employeeSelect" class="form-select border-0 bg-light-soft py-3 px-4 shadow-sm rounded-end-4 @error('employee_id') is-invalid @enderror" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" required>
                                    <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>Who is requesting leave?</option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                                            {{ $e->first_name }} {{ $e->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('employee_id') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                            
                            {{-- Availability Hint --}}
                            <div id="availabilityHint" class="mt-3 p-3 rounded-4 bg-info-soft text-info small" style="display: none;">
                                <i class="bi bi-info-circle me-2"></i>
                                <span id="availabilityText">Fetching employee schedule...</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Start Date --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Start Date</label>
                                <input type="text" name="start_date" id="startDate"
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') }}" placeholder="Select date" required>
                                @error('start_date') <div class="text-danger small mt-1 d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">End Date</label>
                                <input type="text" name="end_date" id="endDate"
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date') }}" placeholder="Select date" required>
                                @error('end_date') <div class="text-danger small mt-1 d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div class="mt-4">
                            <label class="form-label small fw-800 text-muted text-uppercase">Reason for Absence</label>
                            <textarea name="reason" rows="4" 
                                      class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('reason') is-invalid @enderror" 
                                      placeholder="Provide a brief explanation for this leave request...">{{ old('reason') }}</textarea>
                            @error('reason') <div class="text-danger small mt-1 d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert bg-white shadow-sm rounded-4 border-0 d-flex align-items-center p-3 mb-4">
                        <i class="bi bi-info-circle-fill text-warning fs-4 me-3"></i>
                        <span class="small text-muted">Leave requests are subject to approval by department heads and final confirmation from the HR administrator. Overlapping dates with existing leaves are automatically restricted.</span>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('hr.leaves.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-warning rounded-pill px-5 py-3 fw-800 shadow-lg border-0 text-white" style="background-color: #f59e0b;">
                            <i class="bi bi-send-fill me-2"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employeeSelect');
        const availabilityHint = document.getElementById('availabilityHint');
        const availabilityText = document.getElementById('availabilityText');
        
        let disabledDates = [];

        const startPicker = flatpickr("#startDate", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: [],
            onChange: function(selectedDates, dateStr, instance) {
                endPicker.set('minDate', dateStr);
            }
        });

        const endPicker = flatpickr("#endDate", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: []
        });

        function fetchLeaveDates(employeeId) {
            if (!employeeId) return;

            availabilityHint.style.display = 'flex';
            availabilityText.innerText = 'Checking employee schedule...';

            fetch(`/hr/employees/${employeeId}/leave-dates`)
                .then(response => response.json())
                .then(data => {
                    disabledDates = data.map(leave => {
                        return {
                            from: leave.start,
                            to: leave.end
                        };
                    });

                    startPicker.set('disable', disabledDates);
                    endPicker.set('disable', disabledDates);

                    if (data.length > 0) {
                        availabilityText.innerHTML = `<strong>Restricted:</strong> This employee has ${data.length} active/pending leave(s) booked. Overlapping dates are disabled.`;
                        availabilityHint.classList.replace('bg-info-soft', 'bg-warning-soft');
                        availabilityHint.classList.replace('text-info', 'text-warning');
                    } else {
                        availabilityText.innerText = 'This employee is fully available for new leave requests.';
                        availabilityHint.classList.replace('bg-warning-soft', 'bg-info-soft');
                        availabilityHint.classList.replace('text-warning', 'text-info');
                    }
                })
                .catch(err => {
                    console.error('Error fetching dates:', err);
                    availabilityHint.style.display = 'none';
                });
        }

        employeeSelect.addEventListener('change', function() {
            fetchLeaveDates(this.value);
            startPicker.clear();
            endPicker.clear();
        });

        // Initialize if value exists (old input)
        if (employeeSelect.value) {
            fetchLeaveDates(employeeSelect.value);
        }
    });
</script>
@endpush

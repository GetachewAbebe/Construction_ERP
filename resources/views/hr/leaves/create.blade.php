@extends('layouts.app')
@section('title', 'New Leave Request | Natanem Engineering')

@push('head')
{{-- Flatpickr for better date picking and disabling dates --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-input[readonly] { 
        background-color: #f8fafc !important; 
        cursor: pointer; 
        font-weight: 600;
    }
    .leave-form-section {
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.03) 0%, rgba(59, 130, 246, 0.03) 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="page-header-premium mb-5">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">New Leave Request</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Leave Portfolio
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <form action="{{ route('hr.leaves.store') }}" method="POST" id="leaveForm">
            @csrf
            
            {{-- Employee Selection Section --}}
            <div class="erp-card mb-4">
                <h5 class="fw-800 text-erp-deep mb-4 pb-3 border-bottom">
                    <i class="bi bi-person-badge text-primary me-2"></i>
                    Employee Information
                </h5>
                
                <div class="row">
                    <div class="col-md-12">
                        <label class="erp-label">Employee Name</label>
                        <select name="employee_id" id="employeeSelect" class="erp-input @error('employee_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>Select employee...</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->first_name }} {{ $e->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        
                        {{-- Availability Hint --}}
                        <div id="availabilityHint" class="mt-3 p-3 rounded-4 bg-info-soft text-info small fw-600" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="availabilityText">Fetching employee schedule...</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Leave Dates Section --}}
            <div class="erp-card mb-4">
                <h5 class="fw-800 text-erp-deep mb-4 pb-3 border-bottom">
                    <i class="bi bi-calendar2-range text-primary me-2"></i>
                    Leave Period
                </h5>
                
                <div class="row g-4">
                    {{-- Start Date --}}
                    <div class="col-md-6">
                        <label class="erp-label">Start Date</label>
                        <input type="text" name="start_date" id="startDate"
                               class="erp-input @error('start_date') is-invalid @enderror" 
                               value="{{ old('start_date') }}" placeholder="Select start date" required>
                        @error('start_date') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-6">
                        <label class="erp-label">End Date</label>
                        <input type="text" name="end_date" id="endDate"
                               class="erp-input @error('end_date') is-invalid @enderror" 
                               value="{{ old('end_date') }}" placeholder="Select end date" required>
                        @error('end_date') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Reason Section --}}
            <div class="erp-card mb-4">
                <h5 class="fw-800 text-erp-deep mb-4 pb-3 border-bottom">
                    <i class="bi bi-chat-left-text text-primary me-2"></i>
                    Leave Details
                </h5>
                
                <div class="row">
                    <div class="col-12">
                        <label class="erp-label">Reason for Leave</label>
                        <textarea name="reason" rows="5" 
                                  class="erp-input @error('reason') is-invalid @enderror" 
                                  placeholder="Provide a brief explanation for this leave request..." style="resize: vertical; min-height: 120px;">{{ old('reason') }}</textarea>
                        @error('reason') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Info Alert --}}
            <div class="alert bg-gradient-to-r from-blue-50 to-emerald-50 border-0 rounded-4 p-4 mb-4 d-flex align-items-start gap-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(5, 150, 105, 0.08) 100%);">
                <i class="bi bi-info-circle-fill text-primary fs-4 mt-1"></i>
                <div class="small text-muted">
                    <strong class="text-erp-deep d-block mb-1">Important Information</strong>
                    Leave requests require approval from your department head and HR administrator. Dates that overlap with existing approved or pending leaves are automatically restricted.
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('hr.leaves.index') }}" class="btn btn-white rounded-pill px-5 py-3 fw-700 shadow-sm border-0">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-900 shadow-xl border-0">
                    <i class="bi bi-send-fill me-2 fs-5"></i>SUBMIT LEAVE REQUEST
                </button>
            </div>
        </form>
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
                        availabilityText.innerHTML = `<strong>Notice:</strong> This employee has ${data.length} active/pending leave(s). Overlapping dates are disabled.`;
                        availabilityHint.classList.remove('bg-info-soft', 'text-info');
                        availabilityHint.classList.add('bg-warning-soft', 'text-warning');
                    } else {
                        availabilityText.innerText = 'This employee is available for new leave requests.';
                        availabilityHint.classList.remove('bg-warning-soft', 'text-warning');
                        availabilityHint.classList.add('bg-success-soft', 'text-success');
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

@extends('layouts.app')

@section('title', 'Loan Details – Inventory')

@section('content')
<div class="container py-4">
    {{-- Screen Header (hidden on print) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom d-print-none">
        <div>
            <a href="{{ route('inventory.loans.index') }}" class="text-decoration-none text-muted small">
                ← Back to Item Lending
            </a>
            <h1 class="h4 mb-0 mt-2">Item Loan Request</h1>
        </div>
        <div>
            @php
                $statusConfig = match($loan->status) {
                    'approved' => ['class' => 'success', 'text' => 'APPROVED'],
                    'returned' => ['class' => 'info', 'text' => 'RETURNED'],
                    'rejected' => ['class' => 'danger', 'text' => 'REJECTED'],
                    default    => ['class' => 'warning', 'text' => 'PENDING'],
                };
            @endphp
            <span class="badge bg-{{ $statusConfig['class'] }} fs-6 px-3 py-2">{{ $statusConfig['text'] }}</span>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show d-print-none" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Print Layout --}}
    <div class="print-document">
        {{-- Company Header --}}
        <div class="text-center mb-3 pb-2 border-bottom">
            <h2 class="mb-0 fw-bold">NATANEM CONSTRUCTION PLC</h2>
            <p class="mb-0 small">ITEM LENDING REQUEST FORM</p>
        </div>

        {{-- Document Info Row --}}
        <div class="row mb-3 small">
            <div class="col-4">
                <strong>Request ID:</strong> #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}
            </div>
            <div class="col-4 text-center">
                <strong>Date:</strong> {{ optional($loan->requested_at)->format('M d, Y') ?? optional($loan->created_at)->format('M d, Y') }}
            </div>
            <div class="col-4 text-end">
                <strong>Status:</strong> <span class="badge bg-{{ $statusConfig['class'] }} badge-sm">{{ $statusConfig['text'] }}</span>
            </div>
        </div>

        {{-- Main Content - 2 Columns --}}
        <div class="row g-3">
            {{-- Left Column --}}
            <div class="col-6">
                {{-- Employee Information --}}
                <div class="section-box mb-3">
                    <h6 class="section-title">EMPLOYEE INFORMATION</h6>
                    <table class="info-table">
                        <tr>
                            <td class="label">Name:</td>
                            <td class="value fw-bold">{{ $loan->employee->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Position:</td>
                            <td class="value">{{ $loan->employee->position ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Department:</td>
                            <td class="value">{{ $loan->employee->department ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Item Information --}}
                <div class="section-box mb-3">
                    <h6 class="section-title">ITEM INFORMATION</h6>
                    <table class="info-table">
                        <tr>
                            <td class="label">Item Name:</td>
                            <td class="value fw-bold">{{ $loan->item->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Item No:</td>
                            <td class="value">{{ $loan->item->item_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Quantity:</td>
                            <td class="value"><strong>{{ $loan->quantity }}</strong> {{ $loan->item->unit_of_measurement ?? 'pcs' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Location:</td>
                            <td class="value">{{ $loan->item->store_location ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-6">
                {{-- Lending Details --}}
                <div class="section-box mb-3">
                    <h6 class="section-title">LENDING DETAILS</h6>
                    <table class="info-table">
                        <tr>
                            <td class="label">Request Date:</td>
                            <td class="value">{{ optional($loan->requested_at)->format('M d, Y') ?? optional($loan->created_at)->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Due Date:</td>
                            <td class="value">{{ optional($loan->due_date)->format('M d, Y') ?? 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Return Date:</td>
                            <td class="value">{{ optional($loan->returned_at)->format('M d, Y') ?? 'Not returned' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Approval Information --}}
                <div class="section-box mb-3">
                    <h6 class="section-title">APPROVAL</h6>
                    <table class="info-table">
                        @if($loan->status === 'approved')
                            <tr>
                                <td class="label">Approved By:</td>
                                <td class="value fw-bold">{{ $loan->approvedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Date:</td>
                                <td class="value">{{ optional($loan->approved_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        @elseif($loan->status === 'rejected')
                            <tr>
                                <td class="label">Rejected By:</td>
                                <td class="value">{{ $loan->rejectedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Reason:</td>
                                <td class="value">{{ $loan->rejection_reason ?? 'N/A' }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="2" class="value text-warning"><em>Pending approval</em></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Notes (if any) --}}
        @if($loan->notes)
            <div class="section-box mb-3">
                <h6 class="section-title">NOTES</h6>
                <p class="small mb-0">{{ $loan->notes }}</p>
            </div>
        @endif

        {{-- Signature Section --}}
        <div class="row mt-3 pt-2 border-top signature-section">
            <div class="col-4 text-center">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <p class="sig-label">Employee Signature</p>
                    <p class="sig-name">{{ $loan->employee->name ?? '' }}</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <p class="sig-label">Approved By</p>
                    <p class="sig-name">{{ $loan->approvedBy->name ?? '' }}</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <p class="sig-label">Inventory Manager</p>
                    <p class="sig-name">_________________</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-2 pt-2 border-top">
            <p class="small text-muted mb-0">Natanem Construction PLC - Inventory Management System</p>
        </div>
    </div>

    {{-- Actions (hidden when printing) --}}
    <div class="d-flex justify-content-between mt-4 pt-3 border-top d-print-none">
        <a href="{{ route('inventory.loans.index') }}" class="btn btn-outline-secondary">
            ← Back to List
        </a>

        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-primary">
                🖨 Print Document
            </button>
            
            @if($loan->status === 'pending')
                <a href="{{ route('inventory.loans.edit', $loan) }}" class="btn btn-outline-primary">Edit</a>
                <form action="{{ route('inventory.loans.destroy', $loan) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this request?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
            @elseif($loan->status === 'approved' && !$loan->returned_at)
                <form action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST"
                      onsubmit="return confirm('Mark as returned?');">
                    @csrf
                    <button type="submit" class="btn btn-success">Mark as Returned</button>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
    /* Print Styles - Optimized for A4 */
    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            background: white;
            font-size: 11pt;
            line-height: 1.3;
        }
        
        .container {
            max-width: 100%;
            padding: 0;
        }
        
        .d-print-none {
            display: none !important;
        }
        
        .print-document {
            width: 100%;
            height: 100%;
        }
        
        h2 {
            font-size: 18pt;
            margin-bottom: 2px;
        }
        
        .badge {
            border: 1.5px solid currentColor;
            background: white !important;
            color: black !important;
            padding: 2px 8px;
            font-size: 9pt;
        }
    }
    
    /* General Styles */
    .section-box {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .section-title {
        background: #f8f9fa;
        padding: 6px 10px;
        margin: 0;
        font-size: 11pt;
        font-weight: 700;
        border-bottom: 1px solid #dee2e6;
    }
    
    .info-table {
        width: 100%;
        margin: 0;
        font-size: 10pt;
    }
    
    .info-table tr {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-table tr:last-child {
        border-bottom: none;
    }
    
    .info-table td {
        padding: 5px 10px;
        vertical-align: top;
    }
    
    .info-table .label {
        width: 35%;
        color: #6c757d;
        font-weight: 600;
    }
    
    .info-table .value {
        width: 65%;
    }
    
    .signature-section {
        margin-top: 20px;
    }
    
    .sig-box {
        padding: 10px 5px;
    }
    
    .sig-line {
        border-bottom: 1.5px solid #000;
        height: 40px;
        margin-bottom: 5px;
    }
    
    .sig-label {
        font-size: 9pt;
        font-weight: 600;
        margin-bottom: 2px;
    }
    
    .sig-name {
        font-size: 9pt;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    @media print {
        .section-title {
            font-size: 10pt;
            padding: 4px 8px;
        }
        
        .info-table {
            font-size: 9pt;
        }
        
        .info-table td {
            padding: 3px 8px;
        }
        
        .sig-line {
            height: 35px;
        }
        
        .sig-label, .sig-name {
            font-size: 8pt;
        }
    }
</style>
@endsection

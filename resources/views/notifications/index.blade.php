@extends('layouts.app')

@section('title', 'Notification Center - Natanem Engineering')

@section('content')
<div class="row pt-4">
    <div class="col-12">
        <div class="hardened-glass p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">Notification History</h2>
                    <p class="text-muted small mb-0">Track all your past activity and system alerts</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
                            Mark All as Read
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-uppercase x-small text-muted letter-spacing-wider">
                        <tr>
                            <th width="50">#</th>
                            <th>Notification</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr class="{{ $notification->read_at ? 'opacity-75' : 'bg-light-subtle' }}">
                            <td>
                                <div class="notification-icon rounded-circle bg-{{ $notification->data['color'] ?? 'primary' }}-soft text-{{ $notification->data['color'] ?? 'primary' }} d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }}"></i>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-erp-deep">{{ $notification->data['title'] ?? 'System Alert' }}</div>
                                <div class="small text-muted">{{ $notification->data['message'] ?? 'Click for details' }}</div>
                            </td>
                            <td>
                                @if($notification->read_at)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 rounded-pill fw-bold">Read</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 fw-800 animate-pulse shadow-sm">Unread</span>
                                @endif
                            </td>
                            <td>
                                @if(($notification->data['priority'] ?? '') === 'high')
                                    <span class="badge bg-danger p-2 rounded-circle" title="High Priority">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                    </span>
                                    <span class="x-small fw-bold text-danger ms-1">High</span>
                                @else
                                    <span class="badge bg-primary p-2 rounded-circle" title="Medium Priority">
                                        <i class="bi bi-dash-circle"></i>
                                    </span>
                                    <span class="x-small fw-bold text-primary ms-1">Medium</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $notification->created_at->format('M d, Y') }}<br>
                                <span class="x-small">{{ $notification->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 text-nowrap">
                                    {{-- Administrator Quick Actions --}}
                                    @if(Auth::user()->hasRole('Administrator') && !$notification->read_at && isset($notification->data['type']) && str_ends_with($notification->data['type'], '_request'))
                                        @if(isset($notification->data['expense_id']))
                                            <form action="{{ route('admin.finance.expenses.approve', $notification->data['expense_id']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" onclick="markNotificationRead('{{ $notification->id }}')">Verify</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" 
                                                    onclick="openGlobalRejectModal('{{ route('admin.finance.expenses.reject', $notification->data['expense_id']) }}', 'Expense', '{{ $notification->id }}')">
                                                Void
                                            </button>
                                        @elseif(isset($notification->data['leave_id']))
                                            <form action="{{ route('admin.requests.leave.approve', $notification->data['leave_id']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" onclick="markNotificationRead('{{ $notification->id }}')">Approve</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" 
                                                    onclick="openGlobalRejectModal('{{ route('admin.requests.leave.reject', $notification->data['leave_id']) }}', 'Leave Request', '{{ $notification->id }}')">
                                                Reject
                                            </button>
                                        @elseif(isset($notification->data['loan_id']))
                                            <form action="{{ route('admin.requests.items.approve', $notification->data['loan_id']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" onclick="markNotificationRead('{{ $notification->id }}')">Issue</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" 
                                                    onclick="openGlobalRejectModal('{{ route('admin.requests.items.reject', $notification->data['loan_id']) }}', 'Item Request', '{{ $notification->id }}')">
                                                Deny
                                            </button>
                                        @endif
                                    @endif

                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold shadow-sm" onclick="markNotificationRead('{{ $notification->id }}')">
                                        View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="bi bi-mailbox fs-1"></i>
                                </div>
                                <h5 class="text-muted fw-bold">No notifications found</h5>
                                <p class="text-muted small">You're all caught up with the system updates.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background: rgba(5, 150, 105, 0.1); }
    .bg-success-soft { background: rgba(5, 150, 105, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft  { background: rgba(239, 68, 68, 0.1); }
    
    .animate-pulse {
        animation: pulse-badge 1.5s infinite;
    }
    
    @keyframes pulse-badge {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
@endsection

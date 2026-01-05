@extends('layouts.app')
@section('title','Employees')

@section('content')
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-1 text-erp-deep">Employees</h1>
                    <p class="text-muted small mb-0">
                        View and manage employee records.
                    </p>
                </div>
                @unless(Auth::user()->hasRole('Administrator'))
                <a href="{{ route('hr.employees.create') }}"
                   class="btn btn-sm btn-success">
                    New Employee
                </a>
                @endunless
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="row">
            <div class="col">
                <div class="card shadow-soft border-0">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;"></th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Position</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $e)
                                        <tr>
                                            <td>
                                                @if($e->profile_picture)
                                                    <img src="{{ asset('storage/' . $e->profile_picture) }}" 
                                                         class="rounded-circle border" 
                                                         width="32" height="32" 
                                                         style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted small" 
                                                         style="width: 32px; height: 32px;">
                                                        {{ substr($e->first_name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $e->first_name }} {{ $e->last_name }}</div>
                                                <div class="small text-muted">{{ $e->email }}</div>
                                            </td>
                                            <td>{{ $e->department }}</td>
                                            <td>{{ $e->position }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match($e->status) {
                                                        'Active' => 'bg-success',
                                                        'On Leave' => 'bg-warning text-dark',
                                                        'Terminated' => 'bg-danger',
                                                        'Resigned' => 'bg-secondary',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $e->status ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $e->phone ?? '-' }}</td>
                                            <td class="text-center">
                                                @unless(Auth::user()->hasRole('Administrator'))
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('hr.employees.edit', $e) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        Edit
                                                    </a>
                                                    <form method="POST"
                                                          action="{{ route('hr.employees.destroy', $e) }}"
                                                          onsubmit="return confirm('Delete this employee?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                                @else
                                                <span class="text-muted x-small">View Only</span>
                                                @endunless
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No employees yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="mt-3">
                            {{ $employees->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

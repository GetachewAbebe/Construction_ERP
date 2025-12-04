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
                <a href="{{ route('hr.employees.create') }}"
                   class="btn btn-sm btn-success">
                    New Employee
                </a>
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
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Hire Date</th>
                                        <th scope="col" class="text-end">Salary</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $e)
                                        <tr>
                                            <td>{{ $e->first_name }} {{ $e->last_name }}</td>
                                            <td>{{ $e->email }}</td>
                                            <td>{{ optional($e->hire_date)->format('Y-m-d') }}</td>
                                            <td class="text-end">
                                                {{ $e->salary ? number_format($e->salary, 2) : '—' }}
                                            </td>
                                            <td class="text-center">
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

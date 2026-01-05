@extends('layouts.app')

@section('title', 'Expenses - Natanem Engineering')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Financial Expenses</h1>
            <p class="text-muted mb-0">Track all costs across projects and categories.</p>
        </div>
        </div>
        @unless(Auth::user()->hasRole('Administrator'))
        <a href="{{ route('finance.expenses.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>Record Expense
        </a>
        @endunless
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('finance.expenses.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Project</label>
                    <select name="project_id" class="form-select border-light bg-light rounded-3">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Category</label>
                    <select name="category" class="form-select border-light bg-light rounded-3">
                        <option value="">All Categories</option>
                        <option value="materials" {{ request('category') == 'materials' ? 'selected' : '' }}>Materials</option>
                        <option value="labor" {{ request('category') == 'labor' ? 'selected' : '' }}>Labor</option>
                        <option value="transport" {{ request('category') == 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-dark rounded-3 px-4">Filter</button>
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $expense->expense_date->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $expense->user->name ?? 'System' }}</small>
                            </td>
                            <td>
                                <a href="{{ route('finance.projects.show', $expense->project) }}" class="text-decoration-none fw-semibold">
                                    {{ $expense->project->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-3">
                                    {{ ucfirst($expense->category) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;">
                                    {{ $expense->description ?? 'No description' }}
                                </div>
                                @if($expense->reference_no)
                                    <small class="text-muted mt-1 d-block"><i class="bi bi-receipt me-1"></i>{{ $expense->reference_no }}</small>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">
                                ETB {{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="text-end pe-4">
                                @unless(Auth::user()->hasRole('Administrator'))
                                <div class="btn-group">
                                    <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary rounded-start-3">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('finance.expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-end-3">
                                            <i class="bi bi-trash"></i>
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
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">No expenses recorded matching your criteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

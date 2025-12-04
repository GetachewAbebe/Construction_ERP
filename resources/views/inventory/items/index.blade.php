@extends('layouts.app')
@section('title', 'Inventory Items')

@section('content')
    <div class="container py-4">

        {{-- Validation summary if any --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <strong>There were problems with your submission:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-1 text-erp-deep">Inventory Items</h1>
                    <p class="text-muted small mb-0">
                        Browse and manage materials, equipment and other inventory items.
                    </p>
                </div>
                <a href="{{ route('inventory.items.create') }}" class="btn btn-sm btn-success">
                    Add Item
                </a>
            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <div class="row">
            <div class="col">
                <div class="card shadow-soft border-0">
                    <div class="card-body">

                        {{-- Optional: simple filter/search bar --}}
                        <form method="GET" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="form-control form-control-sm"
                                    placeholder="Search by name or code..."
                                >
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select form-select-sm">
                                    <option value="">Category (Any)</option>
                                    {{-- You can loop categories here if you have them --}}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Status (Any)</option>
                                    <option value="in_stock" @selected(request('status') === 'in_stock')>In stock</option>
                                    <option value="low_stock" @selected(request('status') === 'low_stock')>Low stock</option>
                                    <option value="out_of_stock" @selected(request('status') === 'out_of_stock')>Out of stock</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-md-end">
                                <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">Category</th>
                                        <th scope="col" class="text-end">Quantity</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->name }}
                                                @if(!empty($item->code))
                                                    <div class="small text-muted">
                                                        {{ $item->code }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $item->category ?? '—' }}</td>
                                            <td class="text-end">{{ $item->quantity ?? 0 }}</td>
                                            <td>{{ $item->location ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $status = $item->status ?? 'in_stock';
                                                @endphp
                                                @if ($status === 'in_stock')
                                                    <span class="badge bg-success-subtle text-erp-deep">In stock</span>
                                                @elseif ($status === 'low_stock')
                                                    <span class="badge bg-warning-subtle text-erp-deep">Low stock</span>
                                                @elseif ($status === 'out_of_stock')
                                                    <span class="badge bg-danger-subtle">Out of stock</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle">{{ ucfirst($status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('inventory.items.edit', $item) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        Edit
                                                    </a>
                                                    <form method="POST"
                                                          action="{{ route('inventory.items.destroy', $item) }}"
                                                          onsubmit="return confirm('Delete this item?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No items found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="mt-3">
                            {{ $items->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

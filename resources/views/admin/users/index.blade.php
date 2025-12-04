@extends('layouts.app')

@section('title', 'Admin – Users')

@section('content')
<div class="container py-4">

    {{-- Page header --}}
    <div class="row mb-3">
        <div class="col d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">System Users</h1>
                <p class="text-muted mb-0">
                    Manage user accounts and access roles for Natanem Engineering.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                    + Add User
                </a>
            </div>
        </div>
    </div>

    {{-- Status flash --}}
    @if(session('status'))
        <div class="alert alert-success py-2">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
                <input
                    type="text"
                    name="q"
                    value="{{ $q ?? '' }}"
                    class="form-control form-control-sm"
                    placeholder="Search by name or email..."
                >
                <button class="btn btn-outline-secondary btn-sm" type="submit">
                    Search
                </button>
            </form>
        </div>
    </div>

    {{-- Users table --}}
    <div class="card shadow-soft border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                {{-- use simple column now --}}
                                <span class="badge bg-outline-success text-dark border">
                                    {{ $user->role ?? '—' }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-success me-1">
                                    Edit
                                </a>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">This is you</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
            <div class="card-footer border-0">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

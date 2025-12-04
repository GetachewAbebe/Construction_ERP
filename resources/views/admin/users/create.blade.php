@extends('layouts.admin')
@section('title','New User')

@section('content')
    <h1 class="text-xl font-semibold">New User</h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-4 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input name="name" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('name') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('email') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('password') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Role</label>
            <select name="role" required
                    class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select role…</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected(old('role')===$role->name)>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div class="pt-2">
            <button class="px-5 py-2 rounded-2xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Create</button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 text-slate-600 hover:text-slate-800">Cancel</a>
        </div>
    </form>
@endsection

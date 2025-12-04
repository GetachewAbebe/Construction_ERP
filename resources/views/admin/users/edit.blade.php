@extends('layouts.admin')
@section('title','Edit User')

@section('content')
    <h1 class="text-xl font-semibold">Edit User</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-4 max-w-xl">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input name="name" value="{{ old('name', $user->name) }}" required
                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('name') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('email') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Password <span class="text-slate-400">(leave blank to keep)</span>
            </label>
            <input type="password" name="password"
                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('password') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Role</label>
            <select name="role" required
                    class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected($user->roles->pluck('name')->contains($role->name))>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        </div>

        <div class="pt-2">
            <button class="px-5 py-2 rounded-2xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Save</button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 text-slate-600 hover:text-slate-800">Cancel</a>
        </div>
    </form>
@endsection

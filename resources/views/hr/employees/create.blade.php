<x-layouts.app-shell title="Onboard Employee">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Onboard Employee</h2>
                <p class="text-sm text-base-content/60">Add a new member to the organization.</p>
            </div>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-identification" class="h-5 w-5 text-primary" /> Personal identity</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">First name</label>
                        <input name="first_name" value="{{ old('first_name') }}" required class="input input-bordered w-full {{ $errors->has('first_name') ? 'input-error' : '' }}" />
                        @error('first_name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Last name</label>
                        <input name="last_name" value="{{ old('last_name') }}" required class="input input-bordered w-full {{ $errors->has('last_name') ? 'input-error' : '' }}" />
                        @error('last_name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Work email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}" />
                        @error('email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" placeholder="+251 9…" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-briefcase" class="h-5 w-5 text-success" /> Assignment</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Department</label>
                        <input name="department_name" list="departmentList" value="{{ old('department_name') }}" placeholder="Select or type…" class="input input-bordered w-full" />
                        <datalist id="departmentList">@foreach ($departments as $dept)<option value="{{ $dept->name }}">@endforeach</datalist>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Position</label>
                        <input name="position_title" list="positionList" value="{{ old('position_title') }}" placeholder="Select or type…" class="input input-bordered w-full" />
                        <datalist id="positionList">@foreach ($positions as $pos)<option value="{{ $pos->title }}">@endforeach</datalist>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Hire date</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Monthly salary (ETB)</label>
                        <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" placeholder="0.00" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Status</label>
                        <select name="status" class="select select-bordered w-full {{ $errors->has('status') ? 'select-error' : '' }}">
                            @foreach (['Active', 'On Leave', 'Terminated', 'Resigned'] as $s)
                                <option value="{{ $s }}" @selected(old('status', 'Active') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Profile picture</label>
                        <input type="file" name="profile_picture" accept="image/*" class="file-input file-input-bordered w-full" />
                        @error('profile_picture') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('hr.employees.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Complete onboarding</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

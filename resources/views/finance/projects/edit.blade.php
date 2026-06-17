<x-layouts.app-shell title="Edit Project">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Project</h2>
                <p class="text-sm text-base-content/60">{{ $project->name }}</p>
            </div>
            <a href="{{ route('finance.projects.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('finance.projects.update', $project) }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-building-office-2" class="h-5 w-5 text-primary" /> Project identity</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Project name</label>
                        <input name="name" value="{{ old('name', $project->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Location</label>
                        <input name="location" value="{{ old('location', $project->location) }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Budget (ETB)</label>
                        <input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget) }}" class="input input-bordered w-full {{ $errors->has('budget') ? 'input-error' : '' }}" />
                        @error('budget') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-calendar-days" class="h-5 w-5 text-success" /> Timeline &amp; status</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Start date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">End date</label>
                        <input type="date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Status</label>
                        <select name="status" class="select select-bordered w-full">
                            @foreach (['Planned', 'In Progress', 'Completed', 'On Hold', 'Cancelled'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $project->status) === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Description / scope</label>
                <textarea name="description" rows="4" class="textarea textarea-bordered w-full">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('finance.projects.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

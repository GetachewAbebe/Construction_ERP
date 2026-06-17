<x-layouts.app-shell title="Edit Category">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Category</h2>
                <p class="text-sm text-base-content/60">Reconfigure <span class="font-medium text-base-content">{{ $classification->name }}</span>.</p>
            </div>
            <a href="{{ route('inventory.asset-classifications.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('inventory.asset-classifications.update', $classification) }}" method="POST"
              class="rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium">Category name</label>
                    <input name="name" value="{{ old('name', $classification->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                    @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Code</label>
                    <input name="code" value="{{ old('code', $classification->code) }}" required class="input input-bordered w-full uppercase {{ $errors->has('code') ? 'input-error' : '' }}" />
                    @error('code') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Parent category</label>
                    <select name="parent_id" class="select select-bordered w-full">
                        <option value="">No parent (root)</option>
                        @foreach ($parents as $p)
                            <option value="{{ $p->id }}" @selected(old('parent_id', $classification->parent_id) == $p->id)>{{ str_repeat('— ', $p->depth) }}{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Icon identifier</label>
                    <input name="icon_identifier" value="{{ old('icon_identifier', $classification->icon_identifier) }}" placeholder="e.g. bi-tag" class="input input-bordered w-full" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium">Description</label>
                    <textarea name="description" rows="3" class="textarea textarea-bordered w-full">{{ old('description', $classification->description) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('inventory.asset-classifications.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update category</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

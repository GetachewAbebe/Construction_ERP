<x-layouts.app-shell title="Edit Template">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Template</h2>
                <p class="text-sm text-base-content/60">{{ $notificationTemplate->name }}</p>
            </div>
            <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('admin.notification-templates.update', $notificationTemplate) }}" method="POST"
              class="space-y-4 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Key</label>
                    <input name="key" value="{{ old('key', $notificationTemplate->key) }}" required class="input input-bordered w-full font-mono {{ $errors->has('key') ? 'input-error' : '' }}" />
                    @error('key') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Name</label>
                    <input name="name" value="{{ old('name', $notificationTemplate->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                    @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Type</label>
                    <select name="type" required class="select select-bordered w-full">
                        @foreach (['email' => 'Email', 'notification' => 'In-app notification', 'sms' => 'SMS'] as $v => $label)
                            <option value="{{ $v }}" @selected(old('type', $notificationTemplate->type) === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Subject <span class="text-base-content/40">(email)</span></label>
                    <input name="subject" value="{{ old('subject', $notificationTemplate->subject) }}" class="input input-bordered w-full" />
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Variables <span class="text-base-content/40">(comma-separated)</span></label>
                <input name="variables" value="{{ old('variables', implode(', ', $notificationTemplate->variables ?? [])) }}" placeholder="name, amount, date" class="input input-bordered w-full" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Body</label>
                <textarea name="body" rows="8" required class="textarea textarea-bordered w-full font-mono text-sm {{ $errors->has('body') ? 'textarea-error' : '' }}">{{ old('body', $notificationTemplate->body) }}</textarea>
                @error('body') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $notificationTemplate->is_active)) class="checkbox checkbox-sm checkbox-primary"> Active
            </label>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update template</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

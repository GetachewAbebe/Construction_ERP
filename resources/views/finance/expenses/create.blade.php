<x-layouts.app-shell title="New Expense">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">New Financial Requisition</h2>
                <p class="text-sm text-base-content/60">Log a field expenditure for a project.</p>
            </div>
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('finance.expenses.store') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium">Project</label>
                <select name="project_id" required class="select select-bordered w-full {{ $errors->has('project_id') ? 'select-error' : '' }}">
                    <option value="" disabled @selected(!old('project_id'))>Select project…</option>
                    @foreach ($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('project_id') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Category</label>
                    <select name="category" required class="select select-bordered w-full {{ $errors->has('category') ? 'select-error' : '' }}">
                        @foreach (['materials' => 'Materials & Supplies', 'labor' => 'Labor & Wages', 'transport' => 'Logistics & Transport', 'equipment' => 'Tools & Equipment', 'utility' => 'Utilities', 'other' => 'Other'] as $v => $label)
                            <option value="{{ $v }}" @selected(old('category') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Amount (ETB)</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required placeholder="0.00" class="input input-bordered w-full {{ $errors->has('amount') ? 'input-error' : '' }}" />
                    @error('amount') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Transaction date</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required class="input input-bordered w-full {{ $errors->has('expense_date') ? 'input-error' : '' }}" />
                    @error('expense_date') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Reference no.</label>
                    <input name="reference_no" value="{{ old('reference_no') }}" placeholder="e.g. VOUCH-A093" class="input input-bordered w-full" />
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Narration / purpose</label>
                <textarea name="description" rows="3" placeholder="Purpose of this transaction…" class="textarea textarea-bordered w-full">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Log requisition</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

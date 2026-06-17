<x-layouts.app-shell title="Edit Expense">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Requisition</h2>
                <p class="text-sm text-base-content/60">Adjust expenditure details.</p>
            </div>
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('finance.expenses.update', $expense) }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')

            <div>
                <label class="mb-1.5 block text-sm font-medium">Project</label>
                <select name="project_id" required class="select select-bordered w-full {{ $errors->has('project_id') ? 'select-error' : '' }}">
                    @foreach ($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id', $expense->project_id) == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('project_id') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Category</label>
                    <select name="category" required class="select select-bordered w-full">
                        @foreach (['materials' => 'Materials & Supplies', 'labor' => 'Labor & Wages', 'transport' => 'Logistics & Transport', 'equipment' => 'Tools & Equipment', 'utility' => 'Utilities', 'other' => 'Other'] as $v => $label)
                            <option value="{{ $v }}" @selected(old('category', $expense->category) === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Amount (ETB)</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" required class="input input-bordered w-full {{ $errors->has('amount') ? 'input-error' : '' }}" />
                    @error('amount') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Transaction date</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d')) }}" required class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Reference no.</label>
                    <input name="reference_no" value="{{ old('reference_no', $expense->reference_no) }}" class="input input-bordered w-full" />
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Narration / purpose</label>
                <textarea name="description" rows="3" class="textarea textarea-bordered w-full">{{ old('description', $expense->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

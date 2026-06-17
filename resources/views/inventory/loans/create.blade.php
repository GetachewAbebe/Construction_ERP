<x-layouts.app-shell title="New Loan Request">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Request Asset Loan</h2>
                <p class="text-sm text-base-content/60">Record a tool/material loan for an employee.</p>
            </div>
            <a href="{{ route('inventory.loans.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('inventory.loans.store') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-cube" class="h-5 w-5 text-primary" /> Assignment</h3>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Item</label>
                        <select name="inventory_item_id" required class="select select-bordered w-full {{ $errors->has('inventory_item_id') ? 'select-error' : '' }}">
                            <option value="" disabled @selected(!old('inventory_item_id'))>Choose an item…</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" @selected(old('inventory_item_id') == $item->id)>{{ $item->item_no }} – {{ $item->name }} (stock: {{ $item->quantity }})</option>
                            @endforeach
                        </select>
                        @error('inventory_item_id') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Assign to employee</label>
                        <select name="employee_id" required class="select select-bordered w-full {{ $errors->has('employee_id') ? 'select-error' : '' }}">
                            <option value="" disabled @selected(!old('employee_id'))>Select employee…</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-calendar-days" class="h-5 w-5 text-success" /> Terms</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Quantity</label>
                        <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required class="input input-bordered w-full {{ $errors->has('quantity') ? 'input-error' : '' }}" />
                        @error('quantity') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Expected return</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="input input-bordered w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Site location or project usage…" class="textarea textarea-bordered w-full">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('inventory.loans.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit request</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

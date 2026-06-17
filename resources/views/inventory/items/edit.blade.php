<x-layouts.app-shell title="Edit Item">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Item</h2>
                <p class="text-sm text-base-content/60">Item <span class="font-medium text-base-content">{{ $item->item_no }}</span>.</p>
            </div>
            <a href="{{ route('inventory.items.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('inventory.items.update', $item) }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-pencil-square" class="h-5 w-5 text-primary" /> Item details</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">SKU / Item no</label>
                        <input name="item_no" value="{{ old('item_no', $item->item_no) }}" required class="input input-bordered w-full {{ $errors->has('item_no') ? 'input-error' : '' }}" />
                        @error('item_no') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Item name</label>
                        <input name="name" value="{{ old('name', $item->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1.5 block text-sm font-medium">Description</label>
                        <textarea name="description" rows="3" class="textarea textarea-bordered w-full">{{ old('description', $item->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-chart-bar" class="h-5 w-5 text-success" /> Stock &amp; value</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Classification</label>
                        <select name="classification_id" class="select select-bordered w-full">
                            <option value="">Select…</option>
                            @foreach ($classifications as $cl)
                                <option value="{{ $cl->id }}" @selected(old('classification_id', $item->classification_id) == $cl->id)>{{ $cl->name }} ({{ $cl->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Vendor</label>
                        <select name="vendor_id" class="select select-bordered w-full">
                            <option value="">No specific vendor</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}" @selected(old('vendor_id', $item->vendor_id) == $v->id)>{{ $v->name }} ({{ $v->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Quantity</label>
                        <input type="number" name="quantity" min="0" value="{{ old('quantity', $item->quantity) }}" required class="input input-bordered w-full {{ $errors->has('quantity') ? 'input-error' : '' }}" />
                        @error('quantity') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Unit of measure</label>
                        <input name="unit_of_measurement" value="{{ old('unit_of_measurement', $item->unit_of_measurement) }}" required placeholder="PCS, KG, LTR" class="input input-bordered w-full {{ $errors->has('unit_of_measurement') ? 'input-error' : '' }}" />
                        @error('unit_of_measurement') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('inventory.items.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

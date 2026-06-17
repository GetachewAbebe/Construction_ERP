<x-layouts.app-shell title="New Item">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Register New Item</h2>
                <p class="text-sm text-base-content/60">Add an asset to the stock registry.</p>
            </div>
            <a href="{{ route('inventory.items.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('inventory.items.store') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-cube" class="h-5 w-5 text-primary" /> Specifications</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">SKU / Item no</label>
                        <input name="item_no" value="{{ old('item_no') }}" required placeholder="CAT-202X-001" class="input input-bordered w-full {{ $errors->has('item_no') ? 'input-error' : '' }}" />
                        @error('item_no') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Item name</label>
                        <input name="name" value="{{ old('name') }}" required placeholder="e.g. Industrial Mixer" class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1.5 block text-sm font-medium">Description</label>
                        <textarea name="description" rows="3" placeholder="Specifications, dimensions, notes…" class="textarea textarea-bordered w-full">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-rectangle-stack" class="h-5 w-5 text-success" /> Logistics</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Classification</label>
                        <select name="classification_id" class="select select-bordered w-full">
                            <option value="">Select…</option>
                            @foreach ($classifications as $cl)
                                <option value="{{ $cl->id }}" @selected(old('classification_id') == $cl->id)>{{ $cl->name }} ({{ $cl->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Vendor</label>
                        <select name="vendor_id" class="select select-bordered w-full">
                            <option value="">Select…</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(old('vendor_id') == $vendor->id)>{{ $vendor->name }} ({{ $vendor->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Initial quantity</label>
                        <input type="number" name="quantity" min="0" value="{{ old('quantity', 0) }}" required class="input input-bordered w-full {{ $errors->has('quantity') ? 'input-error' : '' }}" />
                        @error('quantity') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Unit of measure</label>
                        <input name="unit_of_measurement" value="{{ old('unit_of_measurement') }}" required placeholder="PCS, KG, LTR" class="input input-bordered w-full {{ $errors->has('unit_of_measurement') ? 'input-error' : '' }}" />
                        @error('unit_of_measurement') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Store location</label>
                        <input name="store_location" value="{{ old('store_location') }}" required placeholder="e.g. Main Warehouse" class="input input-bordered w-full {{ $errors->has('store_location') ? 'input-error' : '' }}" />
                        @error('store_location') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">In date</label>
                        <input type="date" name="in_date" value="{{ old('in_date', date('Y-m-d')) }}" required class="input input-bordered w-full {{ $errors->has('in_date') ? 'input-error' : '' }}" />
                        @error('in_date') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('inventory.items.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Register item</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

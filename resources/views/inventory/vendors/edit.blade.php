<x-layouts.app-shell title="Edit Vendor">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Vendor</h2>
                <p class="text-sm text-base-content/60">{{ $vendor->name }}</p>
            </div>
            <a href="{{ route('inventory.vendors.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('inventory.vendors.update', $vendor) }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-information-circle" class="h-5 w-5 text-primary" /> Basic information</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Legal name</label>
                        <input name="name" value="{{ old('name', $vendor->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Status</label>
                        <label class="flex h-12 cursor-pointer items-center justify-between rounded-lg border border-base-300 px-4">
                            <span class="text-sm font-medium">Partner active</span>
                            <input type="checkbox" name="is_active" value="1" @checked($vendor->is_active) class="toggle toggle-primary toggle-sm">
                        </label>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Category</label>
                        <select name="category" class="select select-bordered w-full">
                            <option value="" @selected(!$vendor->category)>General supplier</option>
                            @foreach (['Raw Materials', 'Machinery & Tools', 'Safety Gear', 'Services', 'Logistics'] as $c)
                                <option value="{{ $c }}" @selected($vendor->category === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Tax ID (TIN)</label>
                        <input name="tax_id" value="{{ old('tax_id', $vendor->tax_id) }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Payment terms</label>
                        <select name="payment_terms" class="select select-bordered w-full">
                            @foreach (['Immediate', 'Net 15', 'Net 30', 'Net 60', 'Milestone'] as $t)
                                <option value="{{ $t }}" @selected($vendor->payment_terms === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-user-circle" class="h-5 w-5 text-success" /> Communication</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Contact person</label>
                        <input name="contact_person" value="{{ old('contact_person', $vendor->contact_person) }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $vendor->email) }}" class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}" />
                        @error('email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Phone</label>
                        <input name="phone" value="{{ old('phone', $vendor->phone) }}" class="input input-bordered w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Address</label>
                        <textarea name="address" rows="3" class="textarea textarea-bordered w-full">{{ old('address', $vendor->address) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('inventory.vendors.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

<x-layouts.app-shell title="New Vendor">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Onboard Vendor</h2>
                <p class="text-sm text-base-content/60">Register a supplier or service provider.</p>
            </div>
            <a href="{{ route('inventory.vendors.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('inventory.vendors.store') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-information-circle" class="h-5 w-5 text-primary" /> Basic information</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Legal name</label>
                        <input name="name" value="{{ old('name') }}" required placeholder="e.g. Acme Supplies Plc" class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Category</label>
                        <select name="category" class="select select-bordered w-full">
                            <option value="">General supplier</option>
                            @foreach (['Raw Materials', 'Machinery & Tools', 'Safety Gear', 'Services', 'Logistics'] as $c)
                                <option value="{{ $c }}" @selected(old('category') === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Payment terms</label>
                        <select name="payment_terms" class="select select-bordered w-full">
                            @foreach (['Immediate', 'Net 15', 'Net 30', 'Net 60', 'Milestone'] as $t)
                                <option value="{{ $t }}" @selected(old('payment_terms') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Tax ID (TIN)</label>
                        <input name="tax_id" value="{{ old('tax_id') }}" placeholder="0012345678" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-user-circle" class="h-5 w-5 text-success" /> Communication</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Contact person</label>
                        <input name="contact_person" value="{{ old('contact_person') }}" placeholder="e.g. John Doe" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="sales@vendor.com" class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}" />
                        @error('email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" placeholder="+251 911 …" class="input input-bordered w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Address</label>
                        <textarea name="address" rows="3" placeholder="Full physical location…" class="textarea textarea-bordered w-full">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('inventory.vendors.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Register vendor</button>
            </div>
        </form>
    </div>
</x-layouts.app-shell>

<x-layouts.app-shell title="System Settings">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">System Settings</h2>
                <p class="text-sm text-base-content/60">Core application configuration.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Dashboard
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <form action="{{ route('admin.system-settings.update') }}" method="POST"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-building-office-2" class="h-5 w-5 text-primary" /> Organization</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Company name</label>
                        <input name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required class="input input-bordered w-full {{ $errors->has('company_name') ? 'input-error' : '' }}" />
                        @error('company_name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Company email</label>
                        <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}" required class="input input-bordered w-full {{ $errors->has('company_email') ? 'input-error' : '' }}" />
                        @error('company_email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Phone number</label>
                        <input name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Address</label>
                        <input name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '') }}" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-cog-6-tooth" class="h-5 w-5 text-success" /> System defaults</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Timezone</label>
                        <select name="timezone" required class="select select-bordered w-full">
                            @foreach (['Africa/Addis_Ababa' => 'Africa/Addis Ababa (EAT)', 'UTC' => 'UTC', 'Africa/Nairobi' => 'Africa/Nairobi (EAT)'] as $v => $label)
                                <option value="{{ $v }}" @selected(old('timezone', $settings['timezone'] ?? '') == $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Currency symbol</label>
                        <input name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'ETB') }}" required class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Date format</label>
                        <select name="date_format" class="select select-bordered w-full">
                            @foreach (['Y-m-d' => 'YYYY-MM-DD', 'd/m/Y' => 'DD/MM/YYYY', 'M j, Y' => 'MMM D, YYYY'] as $v => $label)
                                <option value="{{ $v }}" @selected(old('date_format', $settings['date_format'] ?? '') == $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Items per page</label>
                        <input type="number" name="items_per_page" min="5" max="100" value="{{ old('items_per_page', $settings['items_per_page'] ?? 15) }}" required class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Discard</a>
                <button type="submit" class="btn btn-primary">Apply configuration</button>
            </div>
        </form>

        <div class="flex items-start gap-3 rounded-xl border border-warning/30 bg-warning/5 p-4 text-sm">
            <x-mary-icon name="o-exclamation-triangle" class="h-5 w-5 shrink-0 text-warning" />
            <p class="text-base-content/70">Changes propagate across all modules immediately. Timezone/date changes affect historical display; currency changes don't convert existing records.</p>
        </div>
    </div>
</x-layouts.app-shell>

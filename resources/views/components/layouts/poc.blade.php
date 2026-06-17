<!DOCTYPE html>
<html lang="en" data-theme="natanem">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Natanem ERP' }}</title>

    {{-- Isolated Tailwind/daisyUI/Mary stylesheet only — no Bootstrap here. --}}
    @vite(['resources/css/mary.css'])
    @livewireStyles
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased">
    {{-- Corporate top bar --}}
    <header class="bg-base-100 border-b border-base-300 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="grid h-9 w-9 place-items-center rounded-md bg-primary text-primary-content font-bold text-sm">NE</div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold">Natanem Engineering ERP</div>
                    <div class="text-[11px] text-base-content/50">Inventory Management</div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="badge badge-outline badge-sm text-base-content/60">POC</span>
                <div class="flex items-center gap-2">
                    <div class="grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-primary text-xs font-semibold">SA</div>
                    <div class="hidden sm:block leading-tight">
                        <div class="text-xs font-medium">System Admin</div>
                        <div class="text-[11px] text-base-content/50">Administrator</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-6">
        {{ $slot }}
    </main>

    {{-- Mary UI toast host (used by the component's success/warning/error calls). --}}
    <x-mary-toast />

    @livewireScripts
</body>
</html>

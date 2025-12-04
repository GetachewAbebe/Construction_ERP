@php
    $is = fn($p) => request()->routeIs($p);
    $link  = 'flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100';
    $active= ' bg-slate-200';
@endphp

<aside class="sticky top-[76px]">
    <div class="rounded-2xl border border-white/70 bg-white/90 backdrop-blur shadow-sm p-3">
        <div class="px-1 pb-1">
            <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Requests</div>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('admin.requests.leave') }}"
               class="{{ $link }}{{ $is('admin.requests.leave') ? $active : '' }}">
                <i data-lucide="calendar-clock" class="w-4 h-4"></i>
                <span>Leave</span>
            </a>
            <a href="{{ route('admin.requests.purchases') }}"
               class="{{ $link }}{{ $is('admin.requests.purchases') ? $active : '' }}">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Purchases</span>
            </a>
            <a href="{{ route('admin.requests.items') }}"
               class="{{ $link }}{{ $is('admin.requests.items') ? $active : '' }}">
                <i data-lucide="boxes" class="w-4 h-4"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('admin.requests.finance') }}"
               class="{{ $link }}{{ $is('admin.requests.finance') ? $active : '' }}">
                <i data-lucide="banknote" class="w-4 h-4"></i>
                <span>Finance</span>
            </a>
        </nav>
    </div>
</aside>

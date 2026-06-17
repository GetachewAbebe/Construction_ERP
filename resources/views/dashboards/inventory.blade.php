<x-layouts.app-shell title="Inventory Dashboard">
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">Inventory Control</h2>
            <p class="text-sm text-base-content/60">Stock levels, equipment loans and alerts.</p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Total Items', 'value' => $totalItems ?? 0, 'icon' => 'o-cube', 'accent' => 'border-l-primary', 'text' => 'text-primary'],
                ['label' => 'Low Stock', 'value' => $lowStockCount ?? 0, 'icon' => 'o-exclamation-triangle', 'accent' => 'border-l-warning', 'text' => 'text-warning'],
                ['label' => 'Out of Stock', 'value' => $zeroStockCount ?? 0, 'icon' => 'o-x-circle', 'accent' => 'border-l-error', 'text' => 'text-error'],
                ['label' => 'Open Loans', 'value' => $openLoanCount ?? 0, 'icon' => 'o-arrow-path', 'accent' => 'border-l-info', 'text' => 'text-info'],
            ] as $kpi)
                <div class="flex items-center justify-between rounded-lg border border-base-300 border-l-4 {{ $kpi['accent'] }} bg-base-100 px-4 py-3 shadow-sm">
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">{{ $kpi['label'] }}</div>
                        <div class="mt-1 text-2xl font-bold tabular-nums {{ $kpi['text'] }}">{{ $kpi['value'] }}</div>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-base-200 {{ $kpi['text'] }}">
                        <x-mary-icon name="{{ $kpi['icon'] }}" class="h-5 w-5" />
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Top items chart --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <h3 class="font-semibold">Top Items by Quantity</h3>
                <p class="text-xs text-base-content/50">Highest stock on hand · catalog health {{ $healthPercentage ?? 0 }}%</p>
                <div id="topItemsChart" class="mt-3"></div>
            </div>

            {{-- Low-stock alerts --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 font-semibold">Low-stock Alerts</h3>
                <div class="space-y-3">
                    @forelse ($recentAlerts as $item)
                        <div class="flex items-center justify-between text-sm">
                            <span class="truncate">{{ $item->name }}</span>
                            <span class="badge badge-warning badge-sm">{{ $item->quantity }} left</span>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/40">No low-stock items. 🎉</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector('#topItemsChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [{ name: 'Quantity', data: @json($chartData) }],
                colors: ['#1a3a5c'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($chartCategories) },
                grid: { borderColor: '#e3e8ef' },
            }).render();
        });
    </script>
    @endpush
</x-layouts.app-shell>

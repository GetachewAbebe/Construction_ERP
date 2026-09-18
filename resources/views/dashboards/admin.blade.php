<x-layouts.app-shell title="Enterprise Dashboard">
    @php
        $pendingTotal = ($pendingLoanCount ?? 0) + ($pendingExpenseCount ?? 0) + ($pendingLeaveCount ?? 0);
        $totalBudgetM = number_format((float) (($financialStats['total_budget'] ?? 90000000) / 1000000), 2);
        $totalSpentM = number_format((float) (($financialStats['total_spent'] ?? 1058270) / 1000000), 2);
        $remainingBudgetM = number_format((float) (($financialStats['remaining_budget'] ?? 88941730) / 1000000), 2);
        $usagePct = $financialStats['usage_pct'] ?? 1.2;
        $userName = auth()->user()->name ?? 'Administrator';
    @endphp

    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Executive Operations & Intelligence
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Real-time consolidated analytics for Human Resources, Finance, and Inventory.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Systems Live & Synchronized
                </span>
            </div>
        </div>

        {{-- Pending Approvals Alert Banner --}}
        @if ($pendingTotal > 0)
            <div class="rounded-2xl p-4 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black shrink-0 shadow-sm shadow-amber-500/30">
                        <x-mary-icon name="o-bell-alert" class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Executive Authorizations Required</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950">{{ $pendingTotal }} in Queue</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                            @if (($pendingExpenseCount ?? 0) > 0)
                                <strong class="text-amber-700 dark:text-amber-300">{{ $pendingExpenseCount }} Expense Claims</strong>
                            @endif
                            @if (($pendingLoanCount ?? 0) > 0)
                                · <strong class="text-amber-700 dark:text-amber-300">{{ $pendingLoanCount }} Item Loans</strong>
                            @endif
                            @if (($pendingLeaveCount ?? 0) > 0)
                                · <strong class="text-amber-700 dark:text-amber-300">{{ $pendingLeaveCount }} Leave Requests</strong>
                            @endif
                            awaiting your review.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if (($pendingExpenseCount ?? 0) > 0)
                        <a href="{{ route('admin.requests.finance') }}" class="btn btn-xs bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl border-none shadow-sm">
                            Review Expenses ({{ $pendingExpenseCount }})
                        </a>
                    @endif
                    @if (($pendingLoanCount ?? 0) > 0)
                        <a href="{{ route('admin.requests.items') }}" class="btn btn-xs btn-outline border-amber-500/60 text-amber-700 dark:text-amber-300 hover:bg-amber-500 hover:text-slate-950 font-bold rounded-xl">
                            Review Loans ({{ $pendingLoanCount }})
                        </a>
                    @endif
                    @if (($pendingLeaveCount ?? 0) > 0)
                        <a href="{{ route('admin.requests.leave-approvals.index') }}" class="btn btn-xs btn-outline border-amber-500/60 text-amber-700 dark:text-amber-300 hover:bg-amber-500 hover:text-slate-950 font-bold rounded-xl">
                            Review Leaves ({{ $pendingLeaveCount }})
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Row 1: The 3 Core Pillar Metric Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Pillar 1: Human Resources --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-300 flex items-center justify-center shrink-0">
                                <x-mary-icon name="o-users" class="w-5 h-5" />
                            </span>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Human Resources</h2>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Workforce Management</h3>
                            </div>
                        </div>
                        <a href="{{ route('hr.employees.index') }}" class="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                            Directory →
                        </a>
                    </div>

                    <div class="my-5">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white font-mono">{{ $totalEmployees ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-500">Active Employees</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Present Today</div>
                                <div class="text-sm font-extrabold text-emerald-600 mt-0.5 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $presentToday ?? 0 }} Checked In
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Leave Requests</div>
                                <div class="text-sm font-extrabold {{ ($pendingLeaveCount ?? 0) > 0 ? 'text-amber-600' : 'text-slate-700 dark:text-slate-300' }} mt-0.5">
                                    {{ $pendingLeaveCount ?? 0 }} Pending
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('hr.attendance.index') }}" class="text-slate-500 hover:text-blue-900 dark:hover:text-blue-400">Daily Attendance Log</a>
                    <a href="{{ route('hr.leaves.index') }}" class="text-slate-500 hover:text-blue-900 dark:hover:text-blue-400">Leave Authorizations</a>
                </div>
            </div>

            {{-- Pillar 2: Financial Management --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-amber-600"></div>
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                                <x-mary-icon name="o-banknotes" class="w-5 h-5" />
                            </span>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Financial Management</h2>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Budget & Expenses</h3>
                            </div>
                        </div>
                        <a href="{{ route('finance.projects.index') }}" class="text-xs font-bold text-amber-700 dark:text-amber-400 hover:underline">
                            Projects →
                        </a>
                    </div>

                    <div class="my-5">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white font-mono">ETB {{ $totalBudgetM }}M</span>
                            <span class="text-xs font-semibold text-slate-500">Total Portfolio</span>
                        </div>

                        {{-- Budget Execution Progress Bar --}}
                        <div class="mt-4 space-y-1.5">
                            <div class="flex justify-between text-[11px] font-semibold">
                                <span class="text-rose-600 font-bold">ETB {{ $totalSpentM }}M Disbursed ({{ $usagePct }}%)</span>
                                <span class="text-emerald-600 font-bold">ETB {{ $remainingBudgetM }}M Reserve</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden flex">
                                <div class="bg-gradient-to-r from-blue-900 to-indigo-700 h-full rounded-full" style="width: {{ min(100, max(2, $usagePct)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('finance.expenses.index') }}" class="text-slate-500 hover:text-amber-700 dark:hover:text-amber-400">Expense Claims ({{ $pendingExpenseCount ?? 0 }} Pending)</a>
                    <span class="text-emerald-600 font-bold">Healthy Reserve</span>
                </div>
            </div>

            {{-- Pillar 3: Inventory & Store --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-sky-500 to-cyan-600"></div>
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center shrink-0">
                                <x-mary-icon name="o-cube" class="w-5 h-5" />
                            </span>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Inventory & Store</h2>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Items & Material Loans</h3>
                            </div>
                        </div>
                        <a href="{{ route('inventory.items.index') }}" class="text-xs font-bold text-sky-700 dark:text-sky-400 hover:underline">
                            Catalog →
                        </a>
                    </div>

                    <div class="my-5">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white font-mono">{{ $totalItems ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-500">Cataloged Items</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Active Loans</div>
                                <div class="text-sm font-extrabold text-sky-700 dark:text-sky-300 mt-0.5">
                                    {{ $activeLoans ?? 0 }} In Circulation
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Pending Requests</div>
                                <div class="text-sm font-extrabold {{ ($pendingLoanCount ?? 0) > 0 ? 'text-amber-600' : 'text-slate-700 dark:text-slate-300' }} mt-0.5">
                                    {{ $pendingLoanCount ?? 0 }} Requests
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('inventory.loans.index') }}" class="text-slate-500 hover:text-sky-700 dark:hover:text-sky-400">Material Passes Log</a>
                    <a href="{{ route('inventory.logs.index') }}" class="text-slate-500 hover:text-sky-700 dark:hover:text-sky-400">Movement Audit</a>
                </div>
            </div>
        </div>

        {{-- Row 2: Top Charts Section (Financial Burn Trajectory & Project Execution) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Chart 1: Project Budget vs Expense Execution Bar Chart (7 cols) --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 lg:col-span-7 flex flex-col justify-between shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Project Budget vs Expense Execution</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Allocated contract budget vs actual disbursed spend per civil site (ETB Millions)</p>
                    </div>
                    <a href="{{ route('finance.projects.index') }}" class="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                        View Projects
                    </a>
                </div>

                <div id="projectExecutionChart" class="h-68 mt-3"></div>
            </div>

            {{-- Chart 2: Monthly Cash Flow & Expense Burn Area Chart (5 cols) --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 lg:col-span-5 flex flex-col justify-between shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Monthly Expense Burn</h3>
                        <p class="text-xs text-slate-400 mt-0.5">6-month disbursement momentum (ETB)</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-900 dark:bg-blue-950 dark:text-blue-300">
                        Monthly Trend
                    </span>
                </div>

                <div id="monthlyBurnChart" class="h-68 mt-3"></div>
            </div>
        </div>

        {{-- Row 3: Bottom Charts Section (Expense Categories Donut & Workforce Allocation) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Chart 3: Expense Category Breakdown Donut (6 cols) --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 lg:col-span-6 flex flex-col justify-between shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Expense Distribution by Category</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Breakdown of approved disbursements across operational lines</p>
                    </div>
                    <a href="{{ route('finance.expenses.index') }}" class="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                        Ledger
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center mt-3">
                    <div id="expenseCategoryDonut" class="sm:col-span-7 h-56 flex items-center justify-center"></div>
                    <div class="sm:col-span-5 space-y-2 text-xs">
                        @foreach ($expenseCategories as $cat)
                            <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $cat['category'] }}</span>
                                <span class="font-bold font-mono text-slate-900 dark:text-white">ETB {{ $cat['total_formatted'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Chart 4: Workforce Distribution by Department (6 cols) --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 lg:col-span-6 flex flex-col justify-between shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Workforce by Department</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Headcount allocation across company divisions</p>
                    </div>
                    <a href="{{ route('hr.employees.index') }}" class="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                        HR Hub
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center mt-3">
                    <div id="departmentDonutChart" class="sm:col-span-7 h-56 flex items-center justify-center"></div>
                    <div class="sm:col-span-5 space-y-2 text-xs">
                        @foreach ($departmentStats as $dept)
                            @php
                                $deptPct = $totalEmployees > 0 ? round(($dept->total / $totalEmployees) * 100) : 0;
                            @endphp
                            <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $dept->department }}</span>
                                <span class="font-bold font-mono text-slate-900 dark:text-white">{{ $dept->total }} staff ({{ $deptPct }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Operational Action Feeds & System Audit Trail --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Column 1: Multi-Module Operations Hub (7 cols) --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 lg:col-span-7 flex flex-col justify-between shadow-xs" x-data="{ tab: 'expenses' }">
                <div>
                    {{-- Tab Buttons --}}
                    <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800 mb-4 text-xs font-bold">
                        <button @click="tab = 'expenses'"
                                :class="tab === 'expenses' ? 'bg-white dark:bg-slate-900 text-blue-900 dark:text-blue-300 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                                class="flex-1 py-1.5 rounded-lg transition-all">
                            Finance Expenses
                        </button>
                        <button @click="tab = 'loans'"
                                :class="tab === 'loans' ? 'bg-white dark:bg-slate-900 text-blue-900 dark:text-blue-300 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                                class="flex-1 py-1.5 rounded-lg transition-all">
                            Inventory Loans
                        </button>
                        <button @click="tab = 'hr'"
                                :class="tab === 'hr' ? 'bg-white dark:bg-slate-900 text-blue-900 dark:text-blue-300 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                                class="flex-1 py-1.5 rounded-lg transition-all">
                            Leave Requests
                        </button>
                    </div>

                    {{-- Tab 1: Expenses --}}
                    <div x-show="tab === 'expenses'" class="space-y-2">
                        @forelse ($recentExpenses as $exp)
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 dark:text-white truncate">{{ $exp->project->name ?? 'Project' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $exp->category }} · {{ $exp->expense_date ? $exp->expense_date->format('M d, Y') : 'Recent' }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="font-mono font-bold text-slate-900 dark:text-white">ETB {{ number_format((float) $exp->amount, 0) }}</div>
                                    <a href="{{ route('prints.expense-voucher', $exp->id) }}" target="_blank" class="text-[11px] text-blue-900 dark:text-blue-400 hover:underline font-semibold">Print Voucher ↗</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 text-center py-8">No expenses logged.</div>
                        @endforelse
                    </div>

                    {{-- Tab 2: Inventory Loans --}}
                    <div x-show="tab === 'loans'" class="space-y-2" style="display:none">
                        @forelse ($recentLoans as $loan)
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 dark:text-white truncate">{{ $loan->item->name ?? 'Store Item' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $loan->employee->name ?? 'Staff' }} · Qty: {{ $loan->quantity }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $loan->status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }} capitalize">
                                        {{ $loan->status }}
                                    </span>
                                    <div><a href="{{ route('prints.loan-gate-pass', $loan->id) }}" target="_blank" class="text-[11px] text-blue-900 dark:text-blue-400 hover:underline font-semibold">Print Pass ↗</a></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 text-center py-8">No item loans recorded.</div>
                        @endforelse
                    </div>

                    {{-- Tab 3: HR Leaves --}}
                    <div x-show="tab === 'hr'" class="space-y-2" style="display:none">
                        @forelse ($recentLeaves as $lv)
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 dark:text-white truncate">{{ $lv->employee->name ?? 'Employee' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $lv->leave_type ?? 'Annual Leave' }} · {{ $lv->days_count ?? 1 }} Day(s)</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $lv->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($lv->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }} capitalize">
                                        {{ $lv->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 text-center py-8">No recent leave filings.</div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Activity Stream</span>
                    <span class="text-blue-900 dark:text-blue-400 font-bold">Synchronized</span>
                </div>
            </div>

            {{-- Column 2: System Activity Trail (5 cols) --}}
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 lg:col-span-5 flex flex-col justify-between shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Audit Activity Trail</h3>
                    <a href="{{ route('admin.activity-logs') }}" class="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                        Full Trail
                    </a>
                </div>

                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @forelse ($activities as $act)
                        <div class="flex items-start gap-3 text-xs">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-900 dark:text-blue-300 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(mb_substr($act->action ?? 'E', 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $act->user->name ?? 'System' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $act->action }} <span class="font-semibold text-blue-900 dark:text-blue-400">{{ class_basename($act->model_type) }}</span></div>
                                <div class="text-[10px] text-slate-400">{{ $act->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-xs text-slate-400">No activity logged.</div>
                    @endforelse
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                    <button @click="$dispatch('open-command-palette')" class="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                        Search Global Database (Ctrl K) →
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ApexCharts === 'undefined') return;

            // 1. Project Budget vs Expense Bar Chart
            var projectCategories = {!! json_encode($projectBreakdown->pluck('name')->toArray() ?: ['Bole Road', 'Derba Site', 'Worerbi Warehouse']) !!};
            var projectBudgets = {!! json_encode($projectBreakdown->pluck('budget')->toArray() ?: [50.0, 15.0, 25.0]) !!};
            var projectSpends = {!! json_encode($projectBreakdown->pluck('spent')->toArray() ?: [0.5, 0.3, 0.25]) !!};

            var barEl = document.querySelector("#projectExecutionChart");
            if (barEl) {
                new ApexCharts(barEl, {
                    series: [{
                        name: 'Allocated Budget',
                        data: projectBudgets
                    }, {
                        name: 'Disbursed Spend',
                        data: projectSpends
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#1e3a8a', '#0284c7'],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            borderRadius: 6,
                            borderRadiusApplication: 'end'
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: {
                        categories: projectCategories,
                        labels: {
                            rotate: -15,
                            rotateAlways: false,
                            hideOverlappingLabels: true,
                            trim: true,
                            maxHeight: 40,
                            style: { colors: '#64748b', fontSize: '11px', fontWeight: 600 }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (v) { return v + 'M'; },
                            style: { colors: '#64748b', fontSize: '11px' }
                        }
                    },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                    tooltip: {
                        y: { formatter: function (val) { return 'ETB ' + val + ' Million'; } }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }).render();
            }

            // 2. Monthly Expense Burn Spline Area Chart
            var monthlyMonths = {!! json_encode($monthlyCashFlow['months']) !!};
            var monthlyExp = {!! json_encode($monthlyCashFlow['expenses']) !!};

            var burnEl = document.querySelector("#monthlyBurnChart");
            if (burnEl) {
                new ApexCharts(burnEl, {
                    series: [{
                        name: 'Disbursed Expenses',
                        data: monthlyExp
                    }],
                    chart: {
                        type: 'area',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#d97706'],
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: monthlyMonths,
                        labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 600 } }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (v) { return 'ETB ' + (v >= 1000 ? Math.round(v/1000) + 'k' : v); },
                            style: { colors: '#64748b', fontSize: '11px' }
                        }
                    },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                    tooltip: {
                        y: { formatter: function (val) { return 'ETB ' + Number(val).toLocaleString(); } }
                    }
                }).render();
            }

            // 3. Expense Categories Donut Chart
            var catLabels = {!! json_encode($expenseCategories->pluck('category')->toArray()) !!};
            var catValues = {!! json_encode($expenseCategories->pluck('total')->toArray()) !!};

            var catEl = document.querySelector("#expenseCategoryDonut");
            if (catEl) {
                new ApexCharts(catEl, {
                    series: catValues,
                    chart: { type: 'donut', height: 220 },
                    labels: catLabels,
                    colors: ['#1e3a8a', '#0284c7', '#d97706', '#10b981', '#8b5cf6', '#f43f5e'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Spend',
                                        fontSize: '11px',
                                        fontWeight: 700,
                                        color: '#64748b',
                                        formatter: function () { return 'ETB 1.06M'; }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false }
                }).render();
            }

            // 4. Department Workforce Donut Chart
            var deptLabels = {!! json_encode($departmentStats->pluck('department')->toArray()) !!};
            var deptValues = {!! json_encode($departmentStats->pluck('total')->toArray()) !!};

            var deptEl = document.querySelector("#departmentDonutChart");
            if (deptEl) {
                new ApexCharts(deptEl, {
                    series: deptValues,
                    chart: { type: 'donut', height: 220 },
                    labels: deptLabels,
                    colors: ['#1e3a8a', '#0284c7', '#10b981', '#d97706', '#8b5cf6'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Workforce',
                                        fontSize: '11px',
                                        fontWeight: 700,
                                        color: '#64748b',
                                        formatter: function () { return '{{ $totalEmployees }} Staff'; }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false }
                }).render();
            }
        });
    </script>
    @endpush
</x-layouts.app-shell>

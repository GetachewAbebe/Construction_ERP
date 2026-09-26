<?php

declare(strict_types=1);

namespace App\Http\Controllers\Labor;

use App\Http\Controllers\Controller;
use App\Models\CasualLaborer;
use App\Models\MusterRoll;
use App\Models\MusterRollItem;
use App\Models\Project;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class MusterRollController extends Controller
{
    public function index(Request $request): Response
    {
        $query = MusterRoll::query()
            ->with([
                'project:id,name',
                'supervisor:id,name',
                'approvedBy:id,name',
                'paidBy:id,name',
                'expense:id,reference_no,amount',
            ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('muster_roll_no', 'ILIKE', "%{$search}%")
                  ->orWhere('title', 'ILIKE', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'ILIKE', "%{$search}%");
                  });
            });
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if ($toDate = $request->input('to_date')) {
            $query->whereDate('date', '<=', $toDate);
        }

        $musterRolls = $query->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_disbursed' => (float) MusterRoll::where('status', MusterRoll::STATUS_PAID)->sum('total_net_amount'),
            'pending_approval' => MusterRoll::whereIn('status', [MusterRoll::STATUS_DRAFT, MusterRoll::STATUS_SUBMITTED])->count(),
            'paid_rolls' => MusterRoll::where('status', MusterRoll::STATUS_PAID)->count(),
            'total_workers_active' => CasualLaborer::where('is_active', true)->count(),
        ];

        return Inertia::render('Labor/MusterRolls/Index', [
            'musterRolls' => $musterRolls,
            'projects' => Project::select('id', 'name')->orderBy('name')->get(),
            'stats' => $stats,
            'filters' => $request->only(['search', 'project_id', 'status', 'from_date', 'to_date']),
        ]);
    }

    public function create(Request $request): Response
    {
        $selectedProjectId = $request->input('project_id') ? (int) $request->input('project_id') : null;

        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();
        $supervisors = User::select('id', 'name')->orderBy('name')->get();

        $activeWorkers = CasualLaborer::query()
            ->where('is_active', true)
            ->when($selectedProjectId, function ($q) use ($selectedProjectId) {
                $q->where(function ($sub) use ($selectedProjectId) {
                    $sub->where('project_id', $selectedProjectId)
                        ->orWhereNull('project_id');
                });
            })
            ->orderBy('first_name')
            ->get()
            ->map(function (CasualLaborer $w) {
                return [
                    'id' => $w->id,
                    'worker_code' => $w->worker_code,
                    'full_name' => $w->full_name,
                    'trade' => $w->trade,
                    'skill_level' => $w->skill_level,
                    'daily_wage_rate' => (float) $w->daily_wage_rate,
                    'overtime_hourly_rate' => (float) ($w->overtime_hourly_rate ?: round($w->daily_wage_rate / 8 * 1.25, 2)),
                    'project_id' => $w->project_id,
                ];
            });

        return Inertia::render('Labor/MusterRolls/Create', [
            'projects' => $projects,
            'supervisors' => $supervisors,
            'activeWorkers' => $activeWorkers,
            'selectedProjectId' => $selectedProjectId,
            'today' => now()->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,submitted'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.casual_laborer_id' => ['required', 'exists:casual_laborers,id'],
            'items.*.trade' => ['required', 'string', 'max:100'],
            'items.*.attendance_status' => ['required', 'string', 'in:full_day,half_day,absent,overtime_only'],
            'items.*.days_worked' => ['required', 'numeric', 'min:0', 'max:1'],
            'items.*.daily_rate' => ['required', 'numeric', 'min:0'],
            'items.*.overtime_hours' => ['nullable', 'numeric', 'min:0'],
            'items.*.overtime_rate' => ['nullable', 'numeric', 'min:0'],
            'items.*.deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.task_assigned' => ['nullable', 'string', 'max:255'],
            'items.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $musterRoll = DB::transaction(function () use ($validated) {
            $musterRollNo = MusterRoll::generateMusterRollNo();

            $roll = MusterRoll::create([
                'muster_roll_no' => $musterRollNo,
                'project_id' => $validated['project_id'],
                'date' => $validated['date'],
                'title' => $validated['title'] ?? 'Daily Site Labor Muster Roll',
                'supervisor_id' => $validated['supervisor_id'] ?? Auth::id(),
                'status' => $validated['status'] ?? MusterRoll::STATUS_SUBMITTED,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = new MusterRollItem([
                    'muster_roll_id' => $roll->id,
                    'casual_laborer_id' => $itemData['casual_laborer_id'],
                    'trade' => $itemData['trade'],
                    'attendance_status' => $itemData['attendance_status'],
                    'days_worked' => $itemData['days_worked'],
                    'daily_rate' => $itemData['daily_rate'],
                    'overtime_hours' => $itemData['overtime_hours'] ?? 0.00,
                    'overtime_rate' => $itemData['overtime_rate'] ?? 0.00,
                    'deduction_amount' => $itemData['deduction_amount'] ?? 0.00,
                    'task_assigned' => $itemData['task_assigned'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);

                $item->computeAmounts();
                $item->save();
            }

            $roll->recalculateTotals();

            return $roll;
        });

        return redirect()->route('labor.muster-rolls.show', $musterRoll->id)
            ->with('success', "Daily Muster Roll {$musterRoll->muster_roll_no} registered successfully.");
    }

    public function show(MusterRoll $musterRoll): Response
    {
        $musterRoll->load([
            'project:id,name,location,budget',
            'supervisor:id,name',
            'approvedBy:id,name',
            'paidBy:id,name',
            'expense:id,reference_no,amount,category,status',
            'items.casualLaborer',
        ]);

        return Inertia::render('Labor/MusterRolls/Show', [
            'musterRoll' => $musterRoll,
        ]);
    }

    public function approve(Request $request, MusterRoll $musterRoll): RedirectResponse
    {
        if ($musterRoll->status === MusterRoll::STATUS_PAID) {
            return redirect()->back()->with('error', 'Cannot alter an already paid muster roll.');
        }

        $musterRoll->approve(Auth::user());

        return redirect()->back()->with('success', "Muster Roll {$musterRoll->muster_roll_no} approved for cash disbursement.");
    }

    public function recordPayout(Request $request, MusterRoll $musterRoll): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:cash,telebirr,cbe_birr,bank_transfer'],
            'payout_reference' => ['nullable', 'string', 'max:100'],
        ]);

        $expense = DB::transaction(function () use ($musterRoll, $validated) {
            return $musterRoll->recordPayout($validated, Auth::user());
        });

        return redirect()->back()->with('success', "Wage disbursement recorded! Project Expense #{$expense->reference_no} ({$musterRoll->total_net_amount} ETB) auto-posted.");
    }

    public function destroy(MusterRoll $musterRoll): RedirectResponse
    {
        if ($musterRoll->status === MusterRoll::STATUS_PAID) {
            return redirect()->back()->with('error', 'Cannot delete an already paid muster roll with financial records.');
        }

        $musterRoll->delete();

        return redirect()->route('labor.muster-rolls.index')
            ->with('success', "Muster Roll {$musterRoll->muster_roll_no} deleted.");
    }

    public function print(MusterRoll $musterRoll, QrCodeService $qr): View
    {
        $musterRoll->load([
            'project',
            'supervisor',
            'approvedBy',
            'paidBy',
            'items.casualLaborer',
        ]);

        $verificationUrl = route('verify.muster-roll', $musterRoll->muster_roll_no);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#0f172a');

        return view('prints.muster-roll', [
            'musterRoll' => $musterRoll,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }
}

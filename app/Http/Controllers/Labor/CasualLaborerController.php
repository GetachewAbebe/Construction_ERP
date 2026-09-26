<?php

declare(strict_types=1);

namespace App\Http\Controllers\Labor;

use App\Http\Controllers\Controller;
use App\Models\CasualLaborer;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CasualLaborerController extends Controller
{
    public function index(Request $request): Response
    {
        $query = CasualLaborer::query()->with('project:id,name');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('worker_code', 'ILIKE', "%{$search}%")
                  ->orWhere('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('middle_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('phone', 'ILIKE', "%{$search}%")
                  ->orWhere('id_card_number', 'ILIKE', "%{$search}%");
            });
        }

        if ($trade = $request->input('trade')) {
            $query->where('trade', $trade);
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $workers = $query->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_workers' => CasualLaborer::count(),
            'active_workers' => CasualLaborer::where('is_active', true)->count(),
            'total_trades' => CasualLaborer::distinct('trade')->count('trade'),
            'avg_daily_rate' => round((float) CasualLaborer::where('is_active', true)->avg('daily_wage_rate'), 2),
        ];

        return Inertia::render('Labor/Workers/Index', [
            'workers' => $workers,
            'projects' => Project::select('id', 'name')->orderBy('name')->get(),
            'trades' => CasualLaborer::TRADES,
            'skillLevels' => CasualLaborer::SKILL_LEVELS,
            'stats' => $stats,
            'filters' => $request->only(['search', 'trade', 'project_id', 'is_active']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'id_card_number' => ['nullable', 'string', 'max:50'],
            'trade' => ['required', 'string', 'max:100'],
            'skill_level' => ['required', 'string', 'in:unskilled,semi_skilled,skilled,master'],
            'daily_wage_rate' => ['required', 'numeric', 'min:0'],
            'overtime_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['worker_code'] = CasualLaborer::generateWorkerCode();
        $validated['overtime_hourly_rate'] = $validated['overtime_hourly_rate'] ?? round($validated['daily_wage_rate'] / 8 * 1.25, 2);
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        CasualLaborer::create($validated);

        return redirect()->back()->with('success', "Worker {$validated['first_name']} {$validated['last_name']} registered successfully ({$validated['worker_code']}).");
    }

    public function update(Request $request, CasualLaborer $worker): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'id_card_number' => ['nullable', 'string', 'max:50'],
            'trade' => ['required', 'string', 'max:100'],
            'skill_level' => ['required', 'string', 'in:unskilled,semi_skilled,skilled,master'],
            'daily_wage_rate' => ['required', 'numeric', 'min:0'],
            'overtime_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['updated_by'] = Auth::id();
        $worker->update($validated);

        return redirect()->back()->with('success', "Worker {$worker->worker_code} updated successfully.");
    }

    public function destroy(CasualLaborer $worker): RedirectResponse
    {
        $worker->delete();

        return redirect()->back()->with('success', "Worker {$worker->worker_code} removed.");
    }

    /**
     * Return active workers as JSON for rapid muster roll dynamic entry.
     */
    public function quickList(Request $request): JsonResponse
    {
        $query = CasualLaborer::query()
            ->where('is_active', true)
            ->select('id', 'worker_code', 'first_name', 'middle_name', 'last_name', 'trade', 'skill_level', 'daily_wage_rate', 'overtime_hourly_rate', 'project_id');

        if ($projectId = $request->input('project_id')) {
            $query->where(function ($q) use ($projectId) {
                $q->where('project_id', $projectId)
                  ->orWhereNull('project_id');
            });
        }

        $workers = $query->orderBy('first_name')->get()->map(function ($w) {
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

        return response()->json($workers);
    }
}

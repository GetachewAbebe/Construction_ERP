<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\SubcontractorCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SubcontractorController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Subcontractor::with(['project', 'certificates']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('trade', 'like', "%{$q}%")
                    ->orWhere('contract_number', 'like', "%{$q}%");
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subcontractors = $query->latest()->paginate(12)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name']);

        $totals = [
            'total_subcontractors' => Subcontractor::count(),
            'total_contract_sum' => (float) Subcontractor::sum('contract_sum'),
            'total_certified_gross' => (float) SubcontractorCertificate::whereIn('status', ['approved', 'paid'])->sum('gross_amount'),
            'total_retention_held' => (float) SubcontractorCertificate::whereIn('status', ['approved', 'paid'])->sum('retention_amount'),
            'total_net_disbursed' => (float) SubcontractorCertificate::whereIn('status', ['approved', 'paid'])->sum('net_payable'),
        ];

        return Inertia::render('Contracts/Subcontractors/Index', [
            'subcontractors' => $subcontractors,
            'projects' => $projects,
            'totals' => $totals,
            'filters' => $request->only(['q', 'project_id', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'trade' => ['required', 'string', 'max:100'],
            'contract_number' => ['nullable', 'string', 'max:100', 'unique:subcontractors,contract_number'],
            'contract_sum' => ['required', 'numeric', 'min:0'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['contract_number'])) {
            $validated['contract_number'] = 'SUB-' . date('Y') . '-' . str_pad((string) (Subcontractor::count() + 1), 4, '0', STR_PAD_LEFT);
        }

        Subcontractor::create($validated);

        return redirect()->route('contracts.subcontractors.index')
            ->with('success', 'Subcontractor trade contract registered successfully.');
    }

    public function storeCertificate(Request $request, Subcontractor $subcontractor)
    {
        $validated = $request->validate([
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'work_description' => ['required', 'string', 'max:2000'],
            'gross_amount' => ['required', 'numeric', 'min:1'],
            'retention_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $gross = (float) $validated['gross_amount'];
        $retentionPercent = isset($validated['retention_percent']) && $validated['retention_percent'] !== ''
            ? (float) $validated['retention_percent']
            : 5.00;

        $retentionAmount = round($gross * ($retentionPercent / 100), 2);
        $netPayable = max(0.0, $gross - $retentionAmount);

        $certCount = $subcontractor->certificates()->count() + 1;
        $certNumber = 'IPC-' . $subcontractor->id . '-' . str_pad((string) $certCount, 3, '0', STR_PAD_LEFT);

        $certificate = SubcontractorCertificate::create([
            'subcontractor_id' => $subcontractor->id,
            'project_id' => $subcontractor->project_id,
            'approved_by' => Auth::id(),
            'certificate_number' => $certNumber,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'work_description' => $validated['work_description'],
            'gross_amount' => $gross,
            'retention_percent' => $retentionPercent,
            'retention_amount' => $retentionAmount,
            'net_payable' => $netPayable,
            'status' => 'approved',
        ]);

        return redirect()->route('contracts.subcontractors.index')
            ->with('success', "Interim Payment Certificate #{$certNumber} issued for ETB " . number_format($netPayable, 2) . ' (Retention: ETB ' . number_format($retentionAmount, 2) . ').');
    }
}

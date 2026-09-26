<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ClientPaymentCertificate;
use App\Models\ClientPaymentReceipt;
use App\Models\Project;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClientPaymentCertificateController extends Controller
{
    /**
     * Display a listing of client payment certificates.
     */
    public function index(Request $request): Response
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');
        $projectId = $request->input('project_id');

        $query = ClientPaymentCertificate::with(['project', 'preparer', 'approver', 'receipts'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $lower = mb_strtolower($q);
                    $sub->where(DB::raw('LOWER(certificate_no)'), 'like', "%{$lower}%")
                        ->orWhere(DB::raw('LOWER(consultant_name)'), 'like', "%{$lower}%")
                        ->orWhere(DB::raw('LOWER(work_description)'), 'like', "%{$lower}%")
                        ->orWhereHas('project', fn ($p) => $p->where(DB::raw('LOWER(name)'), 'like', "%{$lower}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->orderByDesc('submission_date')
            ->orderByDesc('id');

        $certificates = $query->paginate(15)->withQueryString();

        $stats = [
            'total_certificates' => ClientPaymentCertificate::count(),
            'total_certified' => (float) ClientPaymentCertificate::whereIn('status', ['certified', 'partially_paid', 'paid'])->sum('total_certified_amount'),
            'total_collected' => (float) ClientPaymentCertificate::sum('amount_paid'),
            'outstanding_receivable' => (float) ClientPaymentCertificate::whereIn('status', ['submitted', 'certified', 'partially_paid'])->sum('balance_due'),
            'total_retention_held' => (float) ClientPaymentCertificate::whereIn('status', ['certified', 'partially_paid', 'paid'])->sum('retention_deduction'),
        ];

        $projects = Project::select('id', 'name', 'location', 'budget')->orderBy('name')->get();

        return Inertia::render('Finance/ClientCertificates/Index', [
            'certificates' => $certificates,
            'stats' => $stats,
            'projects' => $projects,
            'filters' => [
                'q' => $q,
                'status' => $status,
                'project_id' => $projectId,
            ],
        ]);
    }

    /**
     * Show form for creating a new client payment certificate (IPC).
     */
    public function create(Request $request)
    {
        $projectId = $request->input('project_id');
        $selectedProject = null;
        $previousIpc = null;
        $suggestedCertificateNo = '';
        $suggestedSequence = 1;
        $previousCumulativeGross = 0.0;

        if ($projectId) {
            $selectedProject = Project::find($projectId);
            if ($selectedProject) {
                $previousIpc = ClientPaymentCertificate::where('project_id', $projectId)
                    ->orderByDesc('ipc_sequence')
                    ->first();

                if ($previousIpc) {
                    $suggestedSequence = $previousIpc->ipc_sequence + 1;
                    $previousCumulativeGross = (float) $previousIpc->cumulative_gross_amount;
                }

                $suggestedCertificateNo = ClientPaymentCertificate::generateCertificateNo($selectedProject);
            }
        }

        $projects = Project::select('id', 'name', 'location', 'budget')->orderBy('name')->get();

        return Inertia::render('Finance/ClientCertificates/Create', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'previousIpc' => $previousIpc,
            'suggestedCertificateNo' => $suggestedCertificateNo,
            'suggestedSequence' => $suggestedSequence,
            'previousCumulativeGross' => $previousCumulativeGross,
        ]);
    }

    /**
     * Store a newly created client payment certificate.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'submission_date' => 'required|date',
            'client_name' => 'nullable|string|max:255',
            'consultant_name' => 'nullable|string|max:255',
            'work_description' => 'required|string|max:1000',
            'cumulative_gross_amount' => 'required|numeric|min:0',
            'previous_gross_amount' => 'required|numeric|min:0',
            'materials_on_site' => 'nullable|numeric|min:0',
            'retention_rate' => 'nullable|numeric|min:0|max:100',
            'advance_recoupment_rate' => 'nullable|numeric|min:0|max:100',
            'other_deductions' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        $cumulative = (float) $validated['cumulative_gross_amount'];
        $previous = (float) $validated['previous_gross_amount'];
        $currentGross = max(0, round($cumulative - $previous, 2));

        $mos = isset($validated['materials_on_site']) ? (float) $validated['materials_on_site'] : 0.0;
        $retRate = isset($validated['retention_rate']) ? (float) $validated['retention_rate'] : 5.0;
        $advRate = isset($validated['advance_recoupment_rate']) ? (float) $validated['advance_recoupment_rate'] : 0.0;
        $otherDed = isset($validated['other_deductions']) ? (float) $validated['other_deductions'] : 0.0;
        $taxRate = isset($validated['tax_rate']) ? (float) $validated['tax_rate'] : 15.0;

        $retentionDeduction = round($currentGross * ($retRate / 100), 2);
        $advanceDeduction = round($currentGross * ($advRate / 100), 2);

        $subtotalNet = max(0, round($currentGross + $mos - $retentionDeduction - $advanceDeduction - $otherDed, 2));
        $taxAmount = round($subtotalNet * ($taxRate / 100), 2);
        $totalCertified = round($subtotalNet + $taxAmount, 2);

        $nextSeq = ClientPaymentCertificate::where('project_id', $project->id)->count() + 1;
        $certificateNo = ClientPaymentCertificate::generateCertificateNo($project);

        $certificate = ClientPaymentCertificate::create([
            'certificate_no' => $certificateNo,
            'project_id' => $project->id,
            'ipc_sequence' => $nextSeq,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'submission_date' => $validated['submission_date'],
            'payment_due_date' => now()->addDays(30)->toDateString(),
            'client_name' => $validated['client_name'] ?? ($project->name . ' Employer/Client'),
            'consultant_name' => $validated['consultant_name'] ?? 'Supervising Consultant Engineer',
            'work_description' => $validated['work_description'],
            'cumulative_gross_amount' => $cumulative,
            'previous_gross_amount' => $previous,
            'current_gross_amount' => $currentGross,
            'materials_on_site' => $mos,
            'retention_rate' => $retRate,
            'retention_deduction' => $retentionDeduction,
            'advance_recoupment_rate' => $advRate,
            'advance_deduction' => $advanceDeduction,
            'other_deductions' => $otherDed,
            'subtotal_net_amount' => $subtotalNet,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_certified_amount' => $totalCertified,
            'amount_paid' => 0,
            'balance_due' => $totalCertified,
            'status' => 'submitted',
            'prepared_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('finance.client-certificates.show', $certificate->id)
            ->with('success', "Client Payment Certificate {$certificate->certificate_no} submitted successfully.");
    }

    /**
     * Display the specified certificate.
     */
    public function show(ClientPaymentCertificate $clientCertificate): Response
    {
        $clientCertificate->load([
            'project',
            'preparer',
            'approver',
            'receipts.receiver',
        ]);

        return Inertia::render('Finance/ClientCertificates/Show', [
            'certificate' => $clientCertificate,
        ]);
    }

    /**
     * Certify / approve the certificate by supervising consultant or executive.
     */
    public function certify(Request $request, ClientPaymentCertificate $clientCertificate)
    {
        $clientCertificate->update([
            'status' => 'certified',
            'certification_date' => now()->toDateString(),
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', "Certificate {$clientCertificate->certificate_no} has been marked as officially Certified.");
    }

    /**
     * Record a client cash collection / payment deposit against the certificate.
     */
    public function recordPayment(Request $request, ClientPaymentCertificate $clientCertificate)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($clientCertificate->balance_due > 0 ? $clientCertificate->balance_due : 999999999),
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($clientCertificate, $validated) {
            ClientPaymentReceipt::create([
                'client_payment_certificate_id' => $clientCertificate->id,
                'receipt_no' => ClientPaymentReceipt::generateReceiptNo(),
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'bank_name' => $validated['bank_name'] ?? null,
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'received_by' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $clientCertificate->refreshPaymentStatus();
        });

        return back()->with('success', 'Client payment receipt recorded successfully! Revenue balance updated.');
    }

    /**
     * Render printable corporate Interim Payment Certificate with QR code.
     */
    public function print(ClientPaymentCertificate $clientCertificate, QrCodeService $qr)
    {
        $clientCertificate->load(['project', 'preparer', 'approver', 'receipts']);
        $verificationUrl = route('verify.client-ipc', $clientCertificate->id);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#0f172a');

        return view('prints.client-ipc', [
            'certificate' => $clientCertificate,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }
}

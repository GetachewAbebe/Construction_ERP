<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Services\EthiopianCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectDocumentController extends Controller
{
    /**
     * Display a listing of engineering blueprints and project documents.
     */
    public function index(Request $request): Response
    {
        $projectId = $request->input('project_id');
        $documentType = $request->input('document_type');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = ProjectDocument::query()
            ->with([
                'project:id,name',
                'milestone:id,title,wbs_code',
                'uploader:id,first_name,middle_name,last_name',
                'approver:id,first_name,middle_name,last_name',
            ])
            ->latest();

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($documentType) {
            $query->where('document_type', $documentType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('document_code', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(12)->withQueryString();

        $projects = Project::select('id', 'name')->orderBy('name')->get();

        $stats = [
            'total' => ProjectDocument::count(),
            'approved' => ProjectDocument::where('status', 'approved')->count(),
            'under_review' => ProjectDocument::where('status', 'under_review')->count(),
            'superseded' => ProjectDocument::where('status', 'superseded')->count(),
        ];

        return Inertia::render('Operations/Documents/Index', [
            'documents' => $documents,
            'projects' => $projects,
            'filters' => [
                'project_id' => $projectId ?? '',
                'document_type' => $documentType ?? '',
                'status' => $status ?? '',
                'search' => $search ?? '',
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly uploaded engineering document or blueprint.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document_code' => 'nullable|string|max:100',
            'document_type' => 'required|string|in:architectural,structural,mep,soil_geotechnical,boq_spec,contract_agreement,as_built,shop_drawing,site_submittal,other',
            'revision_number' => 'nullable|string|max:50',
            'milestone_id' => 'nullable|exists:project_milestones,id',
            'file' => 'required|file|max:51200', // max 50MB
            'description' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
        $fileSize = $file->getSize();

        // Generate clean storage path
        $extension = $file->getClientOriginalExtension();
        $safeName = Str::slug(pathinfo($fileName, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $extension;
        $path = $file->storeAs("projects/{$project->id}/documents", $safeName, 'public');

        // Document Code Generation if not provided
        $code = $request->input('document_code');
        if (empty($code)) {
            $prefix = match ($request->input('document_type')) {
                'architectural' => 'ARCH',
                'structural' => 'STR',
                'mep' => 'MEP',
                'soil_geotechnical' => 'GEO',
                'boq_spec' => 'BOQ',
                'contract_agreement' => 'CNT',
                'as_built' => 'ASB',
                'shop_drawing' => 'SHP',
                'site_submittal' => 'SUB',
                default => 'DOC',
            };
            $code = sprintf('DWG-%s-%03d', $prefix, $project->documents()->count() + 1);
        }

        $document = $project->documents()->create([
            'milestone_id' => $request->input('milestone_id'),
            'title' => $request->input('title'),
            'document_code' => $code,
            'document_type' => $request->input('document_type'),
            'revision_number' => $request->input('revision_number', 'Rev 0') ?: 'Rev 0',
            'file_path' => $path,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'uploaded_by' => auth()->id(),
            'status' => 'under_review',
            'description' => $request->input('description'),
            'ethiopian_date_text' => EthiopianCalendarService::formatDual(now()),
        ]);

        return back()->with('success', "Engineering blueprint '{$document->title}' ({$document->document_code}) uploaded successfully.");
    }

    /**
     * Download the specified engineering document safely.
     */
    public function download(ProjectDocument $document): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found in storage repository.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Approve, reject, or mark document as superseded.
     */
    public function updateStatus(Request $request, ProjectDocument $document)
    {
        $validated = $request->validate([
            'status' => 'required|in:under_review,approved,superseded,rejected',
        ]);

        $newStatus = $validated['status'];

        if ($newStatus === 'approved') {
            $document->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $msg = "Document '{$document->title}' has been officially APPROVED for construction execution.";
        } elseif ($newStatus === 'superseded') {
            $document->update([
                'status' => 'superseded',
            ]);
            $msg = "Document '{$document->title}' has been marked as SUPERSEDED by newer revision.";
        } elseif ($newStatus === 'rejected') {
            $document->update([
                'status' => 'rejected',
                'approved_by' => null,
                'approved_at' => null,
            ]);
            $msg = "Document '{$document->title}' has been REJECTED.";
        } else {
            $document->update([
                'status' => 'under_review',
                'approved_by' => null,
                'approved_at' => null,
            ]);
            $msg = "Document '{$document->title}' moved back to UNDER REVIEW.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Remove the specified document from the system.
     */
    public function destroy(ProjectDocument $document)
    {
        // Delete physical file if exists
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $title = $document->title;
        $document->delete();

        return back()->with('success', "Engineering drawing '{$title}' removed successfully.");
    }
}

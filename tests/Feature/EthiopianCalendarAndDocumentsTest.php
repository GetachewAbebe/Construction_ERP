<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectMilestone;
use App\Models\User;
use App\Services\EthiopianCalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EthiopianCalendarAndDocumentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;
    protected ProjectMilestone $milestone;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'admin']);

        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->user->assignRole('admin');

        $this->project = Project::create([
            'name' => 'Bole Commercial Tower Phase 1',
            'location' => 'Bole Subcity, Addis Ababa',
            'budget' => 45000000.00,
            'status' => 'In Progress',
            'start_date' => '2026-01-01',
            'end_date' => '2027-12-31',
        ]);

        $this->milestone = ProjectMilestone::create([
            'project_id' => $this->project->id,
            'title' => 'Deep Foundation Piling & Substructure',
            'wbs_code' => '1.1',
            'progress' => 40,
            'weight_pct' => 15.00,
            'status' => 'in_progress',
            'order' => 1,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_ethiopian_calendar_service_converts_known_dates_accurately(): void
    {
        // 1. Enkutatash 2017 (Sept 11, 2024 GC)
        $ec1 = EthiopianCalendarService::toEthiopian('2024-09-11');
        $this->assertSame(2017, $ec1['year']);
        $this->assertSame(1, $ec1['month']);
        $this->assertSame(1, $ec1['day']);
        $this->assertSame('መስከረም', $ec1['month_name_am']);
        $this->assertSame('Meskerem', $ec1['month_name_en']);
        $this->assertSame('መስከረም 1, 2017 ዓ.ም', $ec1['formatted_am']);

        // 2. Pagume 6 in Ethiopian Leap Year 2015 (Sept 11, 2023 GC)
        $ecLeap = EthiopianCalendarService::toEthiopian('2023-09-11');
        $this->assertSame(2015, $ecLeap['year']);
        $this->assertSame(13, $ecLeap['month']);
        $this->assertSame(6, $ecLeap['day']);
        $this->assertTrue($ecLeap['is_leap_year']);

        // 3. Enkutatash 2016 (Sept 12, 2023 GC - day after Pagume 6)
        $ecNewYear = EthiopianCalendarService::toEthiopian('2023-09-12');
        $this->assertSame(2016, $ecNewYear['year']);
        $this->assertSame(1, $ecNewYear['month']);
        $this->assertSame(1, $ecNewYear['day']);

        // 4. Reverse conversion: Ethiopian to Gregorian
        $gcBack = EthiopianCalendarService::toGregorian(2017, 1, 1);
        $this->assertSame('2024-09-11', $gcBack->toDateString());

        // 5. Dual format string
        $dual = EthiopianCalendarService::formatDual('2024-09-11');
        $this->assertStringContainsString('11 Sep 2024', $dual);
        $this->assertStringContainsString('መስከረም 1, 2017 ዓ.ም', $dual);

        // 6. Days in Month logic
        $this->assertSame(30, EthiopianCalendarService::daysInMonth(2016, 1));
        $this->assertSame(5, EthiopianCalendarService::daysInMonth(2016, 13)); // non-leap Pagume
        $this->assertSame(6, EthiopianCalendarService::daysInMonth(2015, 13)); // leap Pagume
    }

    public function test_can_upload_engineering_document_with_dual_calendar_stamp(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('foundation_reinforcement.pdf', 2048, 'application/pdf');

        $response = $this->actingAs($this->user)->post("/projects/{$this->project->id}/documents", [
            'title' => 'Basement 2 Mat Foundation Structural Details',
            'document_type' => 'structural',
            'revision_number' => 'Rev 1.0',
            'milestone_id' => $this->milestone->id,
            'file' => $file,
            'description' => 'Structural engineering drawings approved by consulting engineer.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('project_documents', [
            'project_id' => $this->project->id,
            'milestone_id' => $this->milestone->id,
            'title' => 'Basement 2 Mat Foundation Structural Details',
            'document_type' => 'structural',
            'revision_number' => 'Rev 1.0',
            'status' => 'under_review',
            'uploaded_by' => $this->user->id,
        ]);

        $document = ProjectDocument::first();
        $this->assertNotNull($document);
        $this->assertStringStartsWith('DWG-STR-', $document->document_code);
        $this->assertNotNull($document->ethiopian_date_text);
        $this->assertStringContainsString('ዓ.ም', $document->ethiopian_date_text);

        // Assert file was physically stored
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_can_download_stored_engineering_document(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('hvac_layout.dwg', 512, 'application/octet-stream');

        $this->actingAs($this->user)->post("/projects/{$this->project->id}/documents", [
            'title' => 'HVAC Ductwork Schematics',
            'document_type' => 'mep',
            'revision_number' => 'Rev 0',
            'file' => $file,
        ]);

        $document = ProjectDocument::first();
        $this->assertNotNull($document);

        $downloadResponse = $this->actingAs($this->user)->get("/documents/{$document->id}/download");
        $downloadResponse->assertOk();
    }

    public function test_can_approve_and_supersede_document_revision(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('architectural_section.pdf', 1024, 'application/pdf');

        $this->actingAs($this->user)->post("/projects/{$this->project->id}/documents", [
            'title' => 'Building Cross-Section A-A',
            'document_type' => 'architectural',
            'revision_number' => 'Rev A',
            'file' => $file,
        ]);

        $document = ProjectDocument::first();
        $this->assertSame('under_review', $document->status);
        $this->assertNull($document->approved_by);

        // 1. Approve document
        $approveResponse = $this->actingAs($this->user)->patch("/documents/{$document->id}/status", [
            'status' => 'approved',
        ]);
        $approveResponse->assertSessionHasNoErrors();

        $document->refresh();
        $this->assertSame('approved', $document->status);
        $this->assertSame($this->user->id, $document->approved_by);
        $this->assertNotNull($document->approved_at);

        // 2. Mark as Superseded when newer revision arrives
        $supersedeResponse = $this->actingAs($this->user)->patch("/documents/{$document->id}/status", [
            'status' => 'superseded',
        ]);
        $supersedeResponse->assertSessionHasNoErrors();

        $document->refresh();
        $this->assertSame('superseded', $document->status);
    }

    public function test_can_delete_document_and_remove_file_from_storage(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test_spec.pdf', 100, 'application/pdf');

        $this->actingAs($this->user)->post("/projects/{$this->project->id}/documents", [
            'title' => 'Temporary Soil Investigation Spec',
            'document_type' => 'soil_geotechnical',
            'file' => $file,
        ]);

        $document = ProjectDocument::first();
        $filePath = $document->file_path;
        Storage::disk('public')->assertExists($filePath);

        $deleteResponse = $this->actingAs($this->user)->delete("/documents/{$document->id}");
        $deleteResponse->assertSessionHasNoErrors();

        // Soft deleted in DB
        $this->assertSoftDeleted('project_documents', ['id' => $document->id]);

        // File deleted from storage
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_edms_index_page_renders_with_filters(): void
    {
        Storage::fake('public');

        $doc = ProjectDocument::create([
            'project_id' => $this->project->id,
            'milestone_id' => $this->milestone->id,
            'title' => 'Structural Framing Plan',
            'document_code' => 'DWG-STR-001',
            'document_type' => 'structural',
            'revision_number' => 'Rev 0',
            'file_path' => 'projects/1/documents/mock.pdf',
            'file_name' => 'mock.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $this->user->id,
            'status' => 'approved',
            'ethiopian_date_text' => 'መስከረም 1, 2017 ዓ.ም',
        ]);

        $response = $this->actingAs($this->user)->get('/operations/documents?document_type=structural');
        $response->assertOk();
    }
}

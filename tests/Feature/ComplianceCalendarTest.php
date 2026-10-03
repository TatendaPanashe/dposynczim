<?php

namespace Tests\Feature;

use App\Models\ComplianceCategory;
use App\Models\ComplianceObligationTemplate;
use App\Models\OrganisationComplianceObligation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComplianceCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_organisation_can_subscribe_to_a_catalogue_obligation(): void
    {
        [$user, $organization] = $this->userWithOrganisation();
        $template = $this->template();
        $template->checklistItems()->create(['title' => 'Upload proof', 'sort_order' => 0]);

        $response = $this->actingAs($user)->post(route('compliance.calendar.store'), [
            'template_id' => $template->id,
            'due_at' => '2026-11-30',
            'assigned_user_id' => $user->id,
        ]);

        $obligation = OrganisationComplianceObligation::firstOrFail();
        $response->assertRedirect(route('compliance.calendar.show', $obligation));
        $this->assertSame($organization->id, $obligation->organization_id);
        $this->assertSame('POTRAZ/data-protection licence renewal', $obligation->title);
        $this->assertDatabaseHas('compliance_obligation_checklist_items', [
            'organisation_compliance_obligation_id' => $obligation->id,
            'title' => 'Upload proof',
        ]);
    }

    public function test_evidence_is_required_before_completion_when_configured(): void
    {
        [$user, $organization] = $this->userWithOrganisation();
        $obligation = OrganisationComplianceObligation::create([
            'organization_id' => $organization->id,
            'title' => 'Tax payment',
            'category' => 'Tax',
            'frequency' => 'monthly',
            'risk_level' => 'high',
            'status' => 'not_started',
            'due_at' => '2026-10-31',
            'evidence_required' => true,
        ]);

        $this->actingAs($user)->post(route('compliance.calendar.complete', $obligation))
            ->assertSessionHasErrors('evidence');
        $this->assertDatabaseMissing('organisation_compliance_obligations', [
            'id' => $obligation->id,
            'status' => 'completed',
        ]);
    }

    public function test_completion_uploads_evidence_and_generates_next_occurrence(): void
    {
        Storage::fake('private');
        [$user, $organization] = $this->userWithOrganisation();
        $obligation = OrganisationComplianceObligation::create([
            'organization_id' => $organization->id,
            'title' => 'NSSA contribution reminder',
            'category' => 'Tax and payroll',
            'frequency' => 'monthly',
            'risk_level' => 'high',
            'status' => 'not_started',
            'due_at' => '2026-10-31',
            'evidence_required' => true,
        ]);

        $this->actingAs($user)->post(route('compliance.calendar.complete', $obligation), [
            'evidence' => UploadedFile::fake()->create('receipt.pdf', 10, 'application/pdf'),
        ])->assertRedirect();

        $this->assertDatabaseHas('organisation_compliance_obligations', [
            'id' => $obligation->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('organisation_compliance_obligations', [
            'organization_id' => $organization->id,
            'title' => 'NSSA contribution reminder',
            'due_at' => '2026-11-30 00:00:00',
            'status' => 'not_started',
        ]);
        $this->assertDatabaseHas('compliance_obligation_evidence', [
            'organisation_compliance_obligation_id' => $obligation->id,
            'label' => 'receipt.pdf',
        ]);
    }

    public function test_other_organisation_cannot_view_obligation_detail(): void
    {
        [$user] = $this->userWithOrganisation();
        $other = Organization::create(['name' => 'Other Client', 'slug' => 'other-client']);
        $obligation = OrganisationComplianceObligation::withoutGlobalScopes()->create([
            'organization_id' => $other->id,
            'title' => 'Private task',
            'category' => 'Internal',
            'frequency' => 'once-off',
            'risk_level' => 'medium',
            'status' => 'not_started',
            'due_at' => '2026-10-31',
        ]);

        $this->actingAs($user)->get(route('compliance.calendar.show', $obligation))
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: Organization}
     */
    private function userWithOrganisation(): array
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'dpo_address' => '10 Nelson Mandela Avenue, Harare',
            'dpo_qualifications' => ['LLB'],
            'dpo_certification_status' => 'Certified',
            'dpo_reporting_line' => 'Board',
            'dpo_official_phone' => '0242000000',
            'dpo_mobile' => '0770000000',
        ]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);

        return [$user, $organization];
    }

    private function template(): ComplianceObligationTemplate
    {
        $category = ComplianceCategory::create(['name' => 'Data protection']);

        return ComplianceObligationTemplate::create([
            'compliance_category_id' => $category->id,
            'title' => 'POTRAZ/data-protection licence renewal',
            'short_code' => 'DP-LIC-TEST',
            'frequency' => 'annual',
            'risk_level' => 'high',
            'reminder_days' => [30, 14, 7, 1],
            'evidence_required' => true,
            'is_active' => true,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\FormDp1;
use App\Models\MonthlyPayment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DocumentDownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dp1_download_accepts_legacy_plain_json_encrypted_fields(): void
    {
        [$user, $organization] = $this->userWithPaidMonthlyAccess();
        $formId = $this->insertDp1Form($organization, [
            'entity_profile' => [
                'entity_name' => 'Mosi Legal',
                'registration_number' => 'REG-001',
                'legal_structure' => 'Private company',
                'business_sector' => 'Legal services',
                'physical_address' => '1 Samora Machel Avenue, Harare',
                'phone_number' => '0242000000',
                'email_address' => 'privacy@example.test',
                'dpo_name' => 'Tendai Moyo',
                'dpo_phone' => '0770000000',
                'dpo_email' => 'dpo@example.test',
                'declarant_name' => 'Nyasha Dube',
                'declarant_position' => 'Managing Partner',
            ],
            'processing_details' => [
                'data_subject_categories' => 'Clients',
                'personal_data_types' => 'Names and contact details',
                'processing_purpose' => 'Provide legal services',
                'data_recipients' => 'Courts and regulators',
                'legal_grounds' => 'Contract',
            ],
            'sensitive_data_details' => ['details' => 'Matter files'],
            'processors' => ['details' => 'Accounting SaaS'],
            'cross_border_transfers' => ['details' => 'Cloud backups'],
            'security_measures' => [
                'risks' => 'Unauthorized access',
                'details' => 'Role-based access',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('compliance.dp1.download', $formId));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="DP1-'.$formId.'.pdf"');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_invalid_encrypted_payloads_do_not_crash_dp1_downloads(): void
    {
        [$user, $organization] = $this->userWithPaidMonthlyAccess();
        $formId = $this->insertDp1Form($organization, [
            'entity_profile' => 'eyJpdiI6ImludmFsaWQiLCJ2YWx1ZSI6ImludmFsaWQiLCJtYWMiOiJpbnZhbGlkIn0=',
        ]);

        $response = $this->actingAs($user)->get(route('compliance.dp1.download', $formId));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertSame([], FormDp1::findOrFail($formId)->entity_profile);
    }

    /**
     * @return array{0: User, 1: Organization}
     */
    private function userWithPaidMonthlyAccess(): array
    {
        $organization = Organization::create([
            'name' => 'Mosi Legal',
            'slug' => 'mosi-legal',
        ]);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'paid',
            'merchant_reference' => 'TEST-DP1-PAID-001',
            'paid_at' => now(),
        ]);

        return [$user, $organization];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function insertDp1Form(Organization $organization, array $overrides = []): int
    {
        $defaults = [
            'organization_id' => $organization->id,
            'status' => 'draft',
            'data_subject_count' => 50,
            'tier' => 'Tier 1',
            'registration_fee' => 50,
            'application_fee' => 30,
            'total_fee' => 80,
            'entity_profile' => [],
            'processing_details' => [],
            'sensitive_data_details' => [],
            'processors' => [],
            'cross_border_transfers' => [],
            'security_measures' => [],
            'attachments' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $attributes = array_merge($defaults, $overrides);

        foreach ([
            'entity_profile',
            'processing_details',
            'sensitive_data_details',
            'processors',
            'cross_border_transfers',
            'security_measures',
        ] as $key) {
            if (is_array($attributes[$key])) {
                $attributes[$key] = json_encode($attributes[$key], JSON_THROW_ON_ERROR);
            }
        }

        return DB::table('form_dp1s')->insertGetId($attributes);
    }
}

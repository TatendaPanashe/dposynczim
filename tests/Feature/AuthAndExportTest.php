<?php

namespace Tests\Feature;

use App\Mail\PotrazApplicationSubmitted;
use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\MonthlyPayment;
use App\Models\Organization;
use App\Models\RopaRecord;
use App\Models\User;
use App\Services\PesepayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;
use ZipArchive;

class AuthAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_create_a_dpo_account(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Tendai Moyo',
            'email' => 'tendai@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('compliance.dpo-profile.edit'));
        $this->assertAuthenticatedAs(User::where('email', 'tendai@example.test')->firstOrFail());
        $this->assertDatabaseMissing('organizations', ['name' => 'Mosi Legal']);
    }

    public function test_a_visitor_can_create_an_account_with_google(): void
    {
        $provider = Mockery::mock();
        $googleUser = Mockery::mock();
        $googleUser->shouldReceive('getId')->andReturn('google-123');
        $googleUser->shouldReceive('getEmail')->andReturn('tendai@example.test');
        $googleUser->shouldReceive('getName')->andReturn('Tendai Moyo');
        $googleUser->shouldReceive('getNickname')->andReturn(null);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $user = User::where('email', 'tendai@example.test')->firstOrFail();
        $response->assertRedirect(route('compliance.dpo-profile.edit'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-123', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_google_login_links_an_existing_account_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'tendai@example.test',
            'google_id' => null,
        ]);
        $provider = Mockery::mock();
        $googleUser = Mockery::mock();
        $googleUser->shouldReceive('getId')->andReturn('google-456');
        $googleUser->shouldReceive('getEmail')->andReturn('tendai@example.test');
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('compliance.dpo-profile.edit'));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertSame('google-456', $user->fresh()->google_id);
        $this->assertSame(1, User::where('email', 'tendai@example.test')->count());
    }

    public function test_a_ropa_download_requires_monthly_access(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $response = $this->actingAs($user)->get(route('compliance.ropa.download'));

        $response->assertRedirect(route('compliance.payments.ropa'));
    }

    public function test_a_ropa_form_captures_template_fields(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $response = $this->actingAs($user)->post(route('compliance.ropa.store'), [
            'business_function' => 'Finance',
            'storage_location' => 'M365 and accounting system',
            'processing_activity' => 'Supplier payments',
            'purpose' => 'Pay approved supplier invoices',
            'data_subject_categories' => "Suppliers\nDirectors",
            'personal_data_categories' => "Contact data\nBanking details",
            'legal_basis' => 'Contract',
            'controller_name' => 'Mosi Legal',
            'retention_period' => 'Seven years',
            'retention_basis' => 'Tax retention policy',
            'data_classification' => 'Sensitive',
            'recipients' => 'Finance team',
            'processor_name' => 'Accounting SaaS',
            'third_party_agreement' => 'DPA in place',
            'security_measures' => 'Role-based access',
            'transfer_security_measures' => 'Encrypted transfer',
            'data_collection_method' => 'Supplier onboarding form',
            'data_volume' => '250 records',
            'dpia_record' => 'Not required',
            'data_risks' => 'Payment fraud',
            'risk_impact' => 'Medium',
            'risk_actions' => 'Quarterly access review',
            'action_owner' => 'Finance Manager',
            'action_due_date' => '2026-10-31',
            'owner' => 'Finance Manager',
            'reviewed_at' => '2026-09-14',
        ]);

        $response->assertRedirect(route('compliance.ropa.index'));
        $this->assertDatabaseHas('ropa_records', [
            'organization_id' => $organization->id,
            'business_function' => 'Finance',
            'processing_activity' => 'Supplier payments',
            'controller_name' => 'Mosi Legal',
            'data_classification' => 'Sensitive',
            'processor_name' => 'Accounting SaaS',
            'risk_impact' => 'Medium',
            'action_owner' => 'Finance Manager',
        ]);
    }

    public function test_a_ropa_record_downloads_with_the_xlsx_template_after_monthly_access_is_paid(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        RopaRecord::create([
            'organization_id' => $organization->id,
            'processing_activity' => 'Customer onboarding',
            'purpose' => 'Open and manage customer accounts',
            'data_subject_categories' => ['Customers'],
            'personal_data_categories' => ['Identity data'],
            'legal_basis' => 'Contract',
            'recipients' => ['Finance team'],
            'retention_period' => 'Seven years',
            'security_measures' => ['Role-based access'],
            'cross_border_transfer' => [],
            'owner' => 'Operations',
        ]);
        RopaRecord::create([
            'organization_id' => $organization->id,
            'processing_activity' => 'Supplier due diligence',
            'purpose' => 'Assess service providers',
            'data_subject_categories' => ['Suppliers'],
            'personal_data_categories' => ['Contact data'],
            'legal_basis' => 'Legitimate interest',
            'recipients' => [],
            'retention_period' => 'Three years',
            'security_measures' => ['Access review'],
            'cross_border_transfer' => [],
            'owner' => 'Procurement',
        ]);
        MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'paid',
            'merchant_reference' => 'TEST-ROPA-PAID-001',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('compliance.ropa.download'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="ROPA-register.xlsx"');
        $this->assertStringStartsWith('PK', $response->getContent());
        $this->assertXlsxWorksheetContains($response->getContent(), 'Customer onboarding');
        $this->assertXlsxWorksheetContains($response->getContent(), 'Supplier due diligence');
    }

    public function test_a_dp1_form_captures_the_fields_used_by_the_official_pdf(): void
    {
        Storage::fake('private');
        $organization = Organization::create([
            'name' => 'Mosi Legal',
            'slug' => 'mosi-legal',
            'registration_number' => 'REG-001',
            'business_sector' => 'Legal services',
            'legal_structure' => 'Private company',
            'physical_address' => '1 Samora Machel Avenue, Harare',
            'telephone' => '0242000000',
            'email' => 'privacy@example.test',
        ]);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);

        $response = $this->actingAs($user)->post(route('compliance.dp1.store'), [
            'organization_id' => $organization->id,
            'entity_name' => 'Mosi Legal',
            'registration_number' => 'REG-001',
            'business_sector' => 'Legal services',
            'legal_structure' => 'Private company',
            'physical_address' => '1 Samora Machel Avenue, Harare',
            'phone_number' => '0242000000',
            'email_address' => 'privacy@example.test',
            'website' => 'https://example.test',
            'dpo_name' => 'Tendai Moyo',
            'dpo_phone' => '0770000000',
            'dpo_email' => 'dpo@example.test',
            'representative_name' => 'Harare Agent',
            'representative_phone' => '0712000000',
            'representative_address' => '2 Julius Nyerere Way, Harare',
            'representative_email' => 'agent@example.test',
            'representative_website' => 'https://agent.example.test',
            'data_subject_count' => 2500,
            'data_subject_categories' => 'Clients and suppliers',
            'personal_data_types' => 'Names, contact details, identity numbers',
            'processing_purpose' => 'Provide legal services and manage supplier payments',
            'data_recipients' => 'Courts, regulators, and processors',
            'legal_grounds' => 'Contractual necessity and legal obligation',
            'sensitive_data_details' => 'Legal matter records may include health details',
            'processor_details' => 'Accounting SaaS and secure document storage provider',
            'cross_border_transfers' => 'Cloud backups stored outside Zimbabwe',
            'data_risks' => 'Unauthorized access or disclosure',
            'security_measures' => 'Role-based access, encryption, and audit logs',
            'declarant_name' => 'Nyasha Dube',
            'declarant_position' => 'Managing Partner',
            'certificate_of_incorporation' => UploadedFile::fake()->create('certificate.pdf', 20, 'application/pdf'),
            'signature_file' => UploadedFile::fake()->image('signature.png'),
        ]);

        $form = FormDp1::firstOrFail();
        $response->assertRedirect(route('compliance.dp1.show', $form));

        $this->assertSame('0242000000', $form->entity_profile['phone_number']);
        $this->assertSame('dpo@example.test', $form->entity_profile['dpo_email']);
        $this->assertSame('Harare Agent', $form->entity_profile['representative_name']);
        $this->assertSame('Provide legal services and manage supplier payments', $form->processing_details['processing_purpose']);
        $this->assertSame('Unauthorized access or disclosure', $form->security_measures['risks']);
        $this->assertSame('Managing Partner', $form->entity_profile['declarant_position']);
        Storage::disk('private')->assertExists($form->attachments['certificate_of_incorporation']);
        Storage::disk('private')->assertExists($form->attachments['signature_file']);
    }

    public function test_a_dp1_application_can_be_previewed_and_edited_before_download(): void
    {
        Storage::fake('private');
        $organization = Organization::create([
            'name' => 'Mosi Legal',
            'slug' => 'mosi-legal',
            'registration_number' => 'REG-001',
            'business_sector' => 'Legal services',
            'legal_structure' => 'Private company',
            'physical_address' => '1 Samora Machel Avenue, Harare',
            'telephone' => '0242000000',
            'email' => 'privacy@example.test',
        ]);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        $form = FormDp1::create([
            'organization_id' => $organization->id,
            'status' => 'draft',
            'data_subject_count' => 50,
            'tier' => 'Tier 1',
            'registration_fee' => 50,
            'application_fee' => 30,
            'total_fee' => 80,
            'entity_profile' => [
                'entity_name' => 'Mosi Legal',
                'registration_number' => 'REG-001',
                'physical_address' => '1 Samora Machel Avenue, Harare',
                'business_sector' => 'Legal services',
                'legal_structure' => 'Private company',
            ],
            'processing_details' => [
                'data_subject_categories' => 'Clients',
                'personal_data_types' => 'Names',
                'processing_purpose' => 'Open matters',
                'legal_grounds' => 'Contract',
                'data_recipients' => null,
            ],
            'sensitive_data_details' => ['details' => null],
            'processors' => ['details' => null],
            'cross_border_transfers' => ['details' => null],
            'security_measures' => ['risks' => null, 'details' => 'Access controls'],
            'attachments' => ['certificate_of_incorporation' => 'dp1-attachments/certificate.pdf'],
        ]);

        $preview = $this->actingAs($user)->get(route('compliance.dp1.show', $form));

        $preview->assertOk();
        $preview->assertSee('Open matters');
        $preview->assertSee('Edit application');

        $response = $this->actingAs($user)->patch(route('compliance.dp1.update', $form), [
            'organization_id' => $organization->id,
            'entity_name' => 'Mosi Legal',
            'registration_number' => 'REG-001',
            'business_sector' => 'Legal services',
            'legal_structure' => 'Private company',
            'physical_address' => '1 Samora Machel Avenue, Harare',
            'phone_number' => '0242000000',
            'email_address' => 'privacy@example.test',
            'data_subject_count' => 1200,
            'data_subject_categories' => 'Clients and suppliers',
            'personal_data_types' => 'Names and contact details',
            'processing_purpose' => 'Open matters and pay suppliers',
            'data_recipients' => 'Regulators',
            'legal_grounds' => 'Contract and legal obligation',
            'security_measures' => 'Encryption and access controls',
            'signature_file' => UploadedFile::fake()->image('signature.png'),
        ]);

        $response->assertRedirect(route('compliance.dp1.show', $form));
        $form->refresh();
        $this->assertSame(1200, $form->data_subject_count);
        $this->assertSame('Tier 2', $form->tier);
        $this->assertSame('Open matters and pay suppliers', $form->processing_details['processing_purpose']);
        $this->assertArrayHasKey('certificate_of_incorporation', $form->attachments);
        Storage::disk('private')->assertExists($form->attachments['signature_file']);
    }

    public function test_an_admin_can_download_dp1_with_the_official_pdf_template(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $admin = User::factory()->create(['is_admin' => true, 'organization_id' => null]);
        $form = FormDp1::create([
            'organization_id' => $organization->id,
            'status' => 'draft',
            'data_subject_count' => 2500,
            'tier' => 'Tier 2',
            'registration_fee' => 300,
            'application_fee' => 30,
            'total_fee' => 330,
            'entity_profile' => [
                'entity_name' => 'Mosi Legal',
                'registration_number' => 'REG-001',
                'physical_address' => '1 Samora Machel Avenue, Harare',
                'phone_number' => '0242000000',
                'email_address' => 'privacy@example.test',
                'website' => 'https://example.test',
                'business_sector' => 'Legal services',
                'legal_structure' => 'Private company',
                'dpo_name' => 'Tendai Moyo',
                'dpo_phone' => '0770000000',
                'dpo_email' => 'dpo@example.test',
                'representative_name' => 'Harare Agent',
                'representative_phone' => '0712000000',
                'representative_address' => '2 Julius Nyerere Way, Harare',
                'representative_email' => 'agent@example.test',
                'representative_website' => 'https://agent.example.test',
                'declarant_name' => 'Nyasha Dube',
                'declarant_position' => 'Managing Partner',
            ],
            'processing_details' => [
                'data_subject_categories' => 'Clients and suppliers',
                'personal_data_types' => 'Names and identity numbers',
                'processing_purpose' => 'Provide legal services',
                'data_recipients' => 'Courts and regulators',
                'legal_grounds' => 'Contractual necessity',
            ],
            'sensitive_data_details' => ['details' => 'Legal records may include health details'],
            'processors' => ['details' => 'Accounting SaaS'],
            'cross_border_transfers' => ['details' => 'Cloud backups stored outside Zimbabwe'],
            'security_measures' => [
                'risks' => 'Unauthorized access',
                'details' => 'Role-based access and encryption',
            ],
            'attachments' => ['certificate_of_incorporation' => 'dp1-attachments/certificate.pdf'],
        ]);

        $response = $this->actingAs($admin)->get(route('compliance.dp1.download', $form));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_a_dp2_download_requires_monthly_access(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_license_number' => 'LIC-001',
            'controller_physical_address' => 'Harare',
            'controller_postal_address' => 'P.O. Box 1, Harare',
            'controller_telephone' => '0242000000',
            'controller_email' => 'legal@example.test',
            'business_scope' => 'Legal services',
            'dpo_address' => 'Harare',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('compliance.dp2.download', $form));

        $response->assertRedirect(route('compliance.payments.dp2', $form));
    }

    public function test_a_dp2_download_uses_the_official_pdf_template_after_monthly_access_is_paid(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_license_number' => 'LIC-001',
            'controller_physical_address' => 'Harare',
            'controller_postal_address' => 'P.O. Box 1, Harare',
            'controller_telephone' => '0242000000',
            'controller_email' => 'legal@example.test',
            'business_scope' => 'Legal services',
            'dpo_address' => 'Harare',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);
        MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'paid',
            'merchant_reference' => 'TEST-PAID-001',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('compliance.dp2.download', $form));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_an_admin_can_download_dp2_without_monthly_payment(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $admin = User::factory()->create(['is_admin' => true, 'organization_id' => null]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_license_number' => 'LIC-001',
            'controller_physical_address' => 'Harare',
            'controller_postal_address' => 'P.O. Box 1, Harare',
            'controller_telephone' => '0242000000',
            'controller_email' => 'legal@example.test',
            'business_scope' => 'Legal services',
            'dpo_address' => 'Harare',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('compliance.dp2.download', $form));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_a_dpo_can_email_a_dp2_application_to_potraz(): void
    {
        Mail::fake();
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'paid',
            'merchant_reference' => 'TEST-DP2-SEND-PAID',
            'paid_at' => now(),
        ]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_license_number' => 'LIC-001',
            'controller_physical_address' => 'Harare',
            'controller_postal_address' => 'P.O. Box 1, Harare',
            'controller_telephone' => '0242000000',
            'controller_email' => 'legal@example.test',
            'business_scope' => 'Legal services',
            'dpo_address' => 'Harare',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('compliance.dp2.send-potraz', $form))
            ->assertRedirect();

        Mail::assertSent(PotrazApplicationSubmitted::class, function (PotrazApplicationSubmitted $mail): bool {
            return $mail->hasTo(config('mail.potraz_applications_to'))
                && $mail->documentType === 'DP2'
                && $mail->filename === 'DP2-1.pdf'
                && str_starts_with($mail->pdfContent, '%PDF-');
        });
    }

    public function test_a_dpo_must_pay_before_emailing_a_potraz_application(): void
    {
        Mail::fake();
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_license_number' => 'LIC-001',
            'controller_physical_address' => 'Harare',
            'controller_postal_address' => 'P.O. Box 1, Harare',
            'controller_telephone' => '0242000000',
            'controller_email' => 'legal@example.test',
            'business_scope' => 'Legal services',
            'dpo_address' => 'Harare',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('compliance.dp2.send-potraz', $form))
            ->assertRedirect(route('compliance.payments.dp2', $form));

        Mail::assertNothingSent();
    }

    public function test_a_dpo_can_email_a_dp3_notification_to_potraz(): void
    {
        Mail::fake();
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'paid',
            'merchant_reference' => 'TEST-DP3-SEND-PAID',
            'paid_at' => now(),
        ]);
        $incident = BreachIncident::create([
            'organization_id' => $organization->id,
            'reference' => 'INC-260923-ABCDE',
            'title' => 'Lost laptop',
            'description' => 'A laptop containing client records was lost.',
            'occurred_at' => now()->subHours(4),
            'detected_at' => now()->subHours(2),
            'sla_due_at' => now()->addHours(22),
            'severity' => 'high',
            'status' => 'open',
            'affected_data_subjects' => ['Clients'],
        ]);

        $this->actingAs($user)
            ->post(route('compliance.incidents.send-potraz', $incident))
            ->assertRedirect();

        Mail::assertSent(PotrazApplicationSubmitted::class, function (PotrazApplicationSubmitted $mail): bool {
            return $mail->hasTo(config('mail.potraz_applications_to'))
                && $mail->documentType === 'DP3'
                && $mail->filename === 'INC-260923-ABCDE.pdf'
                && str_starts_with($mail->pdfContent, '%PDF-');
        });

        $this->assertNotNull($incident->refresh()->dp3_submitted_at);
    }

    public function test_the_monthly_payment_page_explains_the_price(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('compliance.payments.dp2', $form));

        $response->assertOk();
        $response->assertSee('$1.99');
        $response->assertSee('Paid once per month');
    }

    public function test_pesepay_checkout_redirects_to_the_returned_checkout_url(): void
    {
        config([
            'services.pesepay.integration_key' => 'test-integration-key',
            'services.pesepay.encryption_key' => '12345678901234567890123456789012',
            'services.pesepay.make_payment_url' => 'https://pesepay.example.test/make-payment',
            'services.pesepay.use_sdk' => false,
        ]);

        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);
        $pesepay = app(PesepayService::class);
        Http::fake([
            'pesepay.example.test/*' => Http::response([
                'payload' => $pesepay->encrypt([
                    'redirectUrl' => 'https://checkout.pesepay.example.test/pay/abc123',
                    'referenceNumber' => 'PZW-REF-001',
                    'transactionStatus' => 'INITIATED',
                ], config('services.pesepay.encryption_key')),
            ]),
        ]);

        $response = $this->actingAs($user)->post(route('compliance.payments.initiate'), [
            'document_type' => 'dp2',
            'document_id' => $form->id,
            'payment_method_code' => 'PZW212',
        ]);

        $response->assertRedirect('https://checkout.pesepay.example.test/pay/abc123');
        $this->assertDatabaseHas('monthly_payments', [
            'organization_id' => $organization->id,
            'status' => 'pending',
            'pesepay_reference' => 'PZW-REF-001',
        ]);
    }

    public function test_pesepay_processing_response_is_recorded_without_checkout_url(): void
    {
        config([
            'services.pesepay.integration_key' => 'test-integration-key',
            'services.pesepay.encryption_key' => '12345678901234567890123456789012',
            'services.pesepay.make_payment_url' => 'https://pesepay.example.test/make-payment',
            'services.pesepay.use_sdk' => false,
        ]);

        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);
        $pesepay = app(PesepayService::class);
        Http::fake([
            'pesepay.example.test/*' => Http::response([
                'payload' => $pesepay->encrypt([
                    'referenceNumber' => 'PZW-REF-002',
                    'transactionStatus' => 'PENDING',
                    'transactionStatusDescription' => 'Transaction is being processed',
                ], config('services.pesepay.encryption_key')),
            ]),
        ]);

        $response = $this->actingAs($user)->post(route('compliance.payments.initiate'), [
            'document_type' => 'dp2',
            'document_id' => $form->id,
            'payment_method_code' => 'PZW212',
        ]);

        $response->assertRedirect(route('compliance.payments.dp2', $form));
        $response->assertSessionHas('success', 'Your payment request was sent to Pesepay. Please complete any prompt from your payment provider, then try the download again.');
        $this->assertDatabaseHas('monthly_payments', [
            'organization_id' => $organization->id,
            'status' => 'pending',
            'pesepay_reference' => 'PZW-REF-002',
            'redirect_url' => null,
        ]);
    }

    public function test_pesepay_rejection_shows_the_gateway_message(): void
    {
        config([
            'services.pesepay.integration_key' => 'test-integration-key',
            'services.pesepay.encryption_key' => '12345678901234567890123456789012',
            'services.pesepay.make_payment_url' => 'https://pesepay.example.test/make-payment',
            'services.pesepay.use_sdk' => false,
        ]);

        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);
        Http::fake([
            'pesepay.example.test/*' => Http::response([
                'message' => 'Invalid payment method code',
            ], 422),
        ]);

        $response = $this->actingAs($user)->from(route('compliance.payments.dp2', $form))->post(route('compliance.payments.initiate'), [
            'document_type' => 'dp2',
            'document_id' => $form->id,
            'payment_method_code' => 'PZW212',
        ]);

        $response->assertRedirect(route('compliance.payments.dp2', $form));
        $response->assertSessionHasErrors([
            'payment' => 'Pesepay rejected the payment request. Invalid payment method code',
        ]);
    }

    public function test_a_successful_pesepay_return_unlocks_monthly_access(): void
    {
        config([
            'services.pesepay.integration_key' => 'test-integration-key',
            'services.pesepay.encryption_key' => '12345678901234567890123456789012',
            'services.pesepay.check_payment_url' => 'https://pesepay.example.test/check-payment',
            'services.pesepay.use_sdk' => false,
        ]);

        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);
        $payment = MonthlyPayment::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'month' => now()->format('Y-m'),
            'amount_cents' => 199,
            'currency' => 'USD',
            'status' => 'pending',
            'merchant_reference' => 'TEST-PENDING-001',
            'pesepay_reference' => 'PZW-REF-001',
            'metadata' => ['document_type' => 'dp2', 'document_id' => $form->id],
        ]);
        $pesepay = app(PesepayService::class);
        Http::fake([
            'pesepay.example.test/*' => Http::response([
                'payload' => $pesepay->encrypt([
                    'referenceNumber' => 'PZW-REF-001',
                    'transactionStatus' => 'SUCCESS',
                ], config('services.pesepay.encryption_key')),
            ]),
        ]);

        $response = $this->actingAs($user)->get(route('compliance.payments.return', $payment));

        $response->assertRedirect(route('compliance.dp2.download', $form));
        $this->assertDatabaseHas('monthly_payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
    }

    private function assertXlsxWorksheetContains(string $content, string $expected): void
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx-test-');
        file_put_contents($path, $content);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true);
        $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($path);

        $this->assertIsString($worksheet);
        $this->assertStringContainsString($expected, $worksheet);
    }
}

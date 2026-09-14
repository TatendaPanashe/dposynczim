<?php

namespace Tests\Feature;

use App\Models\FormDp2;
use App\Models\MonthlyPayment;
use App\Models\Organization;
use App\Models\RopaRecord;
use App\Models\User;
use App\Services\PesepayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use ZipArchive;

class AuthAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_create_an_organisation_workspace(): void
    {
        $response = $this->post(route('register.store'), [
            'organization_name' => 'Mosi Legal',
            'name' => 'Tendai Moyo',
            'email' => 'tendai@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('compliance.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'tendai@example.test')->firstOrFail());
        $this->assertDatabaseHas('organizations', ['name' => 'Mosi Legal']);
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

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path) === true);
        $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($path);

        $this->assertIsString($worksheet);
        $this->assertStringContainsString($expected, $worksheet);
    }
}

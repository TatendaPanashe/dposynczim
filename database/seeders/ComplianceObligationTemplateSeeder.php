<?php

namespace Database\Seeders;

use App\Models\ComplianceCategory;
use App\Models\ComplianceObligationTemplate;
use Illuminate\Database\Seeder;

class ComplianceObligationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            'Data protection',
            'Corporate',
            'Tax and payroll',
            'Contracts',
            'Internal governance',
            'Financial crime',
        ])->mapWithKeys(fn (string $name): array => [
            $name => ComplianceCategory::updateOrCreate(['name' => $name], ['is_active' => true]),
        ]);

        $templates = [
            ['Data protection', 'POTRAZ/data-protection licence renewal', 'DP-LIC-RENEW', 'Annual review of data-controller registration or licence renewal dates.', 'POTRAZ/Data Protection Authority', 'annual', 'high', ['Confirm current registration status', 'Upload renewal proof or regulator correspondence']],
            ['Data protection', 'DPO appointment/review', 'DPO-REVIEW', 'Confirm the DPO appointment remains current and contact details are accurate.', 'POTRAZ/Data Protection Authority', 'annual', 'high', ['Review DP2 appointment', 'Confirm DPO contact details']],
            ['Data protection', 'Data-protection impact assessment review', 'DPIA-REVIEW', 'Review higher-risk processing activities for DPIA updates.', 'Internal', 'annual', 'high', ['Identify high-risk processing', 'Record review decision']],
            ['Data protection', 'Data-breach response exercise', 'BREACH-EXERCISE', 'Run a tabletop breach response exercise and retain evidence.', 'Internal', 'semi-annual', 'critical', ['Run exercise', 'Capture lessons learned']],
            ['Data protection', 'Privacy-policy review', 'PRIVACY-REVIEW', 'Review privacy notices and policy alignment.', 'Internal', 'annual', 'medium', ['Review policy text', 'Approve updated version']],
            ['Data protection', 'Employee data-protection training', 'DP-TRAINING', 'Track annual employee privacy and security awareness training.', 'Internal', 'annual', 'medium', ['Confirm attendee list', 'Upload training proof']],
            ['Corporate', 'Annual company return', 'CO-RETURN', 'Configurable annual corporate return reminder.', 'Companies Registry', 'annual', 'high', ['Confirm filing deadline', 'Upload filing receipt']],
            ['Corporate', 'Beneficial-ownership register review', 'BO-REVIEW', 'Review beneficial ownership information for accuracy.', 'Companies Registry', 'annual', 'medium', ['Review register', 'Record changes or no-change confirmation']],
            ['Internal governance', 'Board resolution review', 'BOARD-RES', 'Review board approvals and standing authorities.', 'Internal', 'annual', 'medium', ['Review resolutions', 'Record gaps']],
            ['Tax and payroll', 'Tax return/payment reminder', 'TAX-RETURN', 'Configurable tax filing or payment reminder.', 'ZIMRA', 'monthly', 'high', ['Confirm amount due', 'Upload payment proof']],
            ['Tax and payroll', 'NSSA contribution reminder', 'NSSA-CONTRIB', 'Track NSSA contribution preparation and payment.', 'NSSA', 'monthly', 'high', ['Prepare schedule', 'Upload payment proof']],
            ['Contracts', 'Insurance renewal', 'INS-RENEW', 'Track insurance policy renewal and proof of cover.', 'Insurer', 'annual', 'medium', ['Review cover', 'Upload policy document']],
            ['Corporate', 'Business licence renewal', 'BIZ-LICENCE', 'Track local authority or sector licence renewal.', 'Local authority', 'annual', 'high', ['Confirm renewal deadline', 'Upload licence']],
            ['Contracts', 'Contract renewal review', 'CONTRACT-REVIEW', 'Review key contract renewals before expiry.', 'Internal', 'custom', 'medium', ['Review renewal terms', 'Record decision']],
            ['Internal governance', 'Internal audit', 'INTERNAL-AUDIT', 'Schedule internal compliance audit activity.', 'Internal', 'quarterly', 'medium', ['Define scope', 'Upload audit report']],
            ['Financial crime', 'AML/KYC file review', 'AML-KYC', 'Review AML/KYC files where applicable.', 'FIU/RBZ/Internal', 'annual', 'critical', ['Sample files', 'Record remediation']],
        ];

        foreach ($templates as [$category, $title, $code, $description, $regulator, $frequency, $risk, $checklist]) {
            $template = ComplianceObligationTemplate::updateOrCreate(['short_code' => $code], [
                'compliance_category_id' => $categories[$category]->getKey(),
                'title' => $title,
                'description' => $description,
                'regulator' => $regulator,
                'jurisdiction' => 'Zimbabwe/SADC',
                'business_type' => 'Configurable',
                'frequency' => $frequency,
                'reminder_days' => [30, 14, 7, 1],
                'evidence_required' => true,
                'is_active' => true,
                'risk_level' => $risk,
                'legal_reference' => 'Configurable template - verify applicable legal position',
                'is_configurable_template' => true,
            ]);

            foreach ($checklist as $index => $item) {
                $template->checklistItems()->updateOrCreate(['title' => $item], ['sort_order' => $index]);
            }
        }
    }
}

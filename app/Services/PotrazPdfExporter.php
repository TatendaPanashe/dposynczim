<?php

namespace App\Services;

use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use setasign\Fpdi\Tcpdf\Fpdi;

final class PotrazPdfExporter
{
    public function dp1(FormDp1 $form): string
    {
        $entity = $form->entity_profile;
        $processing = $form->processing_details;

        return $this->overlay(
            'dp1-template.pdf',
            function (Fpdi $pdf, int $page) use ($form, $entity, $processing): void {
                if ($page === 1) {
                    $this->text($pdf, $entity['entity_name'] ?? '', 32, 105, 8);
                    $this->text($pdf, $entity['registration_number'] ?? '', 12, 124, 8);
                    $this->text($pdf, $form->tier, 151, 124, 8);
                    $this->text($pdf, $entity['legal_structure'] ?? '', 42, 154, 7, 150);
                    $this->text($pdf, $entity['business_sector'] ?? '', 45, 170, 8);
                    $this->text($pdf, $entity['physical_address'] ?? '', 45, 177, 8, 145);
                    $this->text($pdf, $entity['phone_number'] ?? '', 45, 191, 8);
                    $this->text($pdf, $entity['email_address'] ?? '', 45, 198, 8);
                    $this->text($pdf, $entity['website'] ?? '', 45, 205, 8);
                    $this->text($pdf, $entity['dpo_name'] ?? '', 32, 222, 8);
                    $this->text($pdf, $entity['dpo_phone'] ?? '', 45, 231, 8);
                    $this->text($pdf, $entity['dpo_email'] ?? '', 45, 239, 8);
                    $this->text($pdf, $entity['representative_name'] ?? '', 32, 260, 8);
                    $this->text($pdf, $entity['representative_phone'] ?? '', 45, 269, 8);

                    return;
                }

                if ($page === 2) {
                    $this->text($pdf, $entity['representative_address'] ?? '', 28, 22, 8, 155);
                    $this->text($pdf, $entity['representative_email'] ?? '', 28, 38, 8);
                    $this->text($pdf, $entity['representative_website'] ?? '', 28, 48, 8);
                    $this->text($pdf, $processing['data_subject_categories'] ?? '', 15, 112, 7, 34);
                    $this->text($pdf, $processing['personal_data_types'] ?? '', 52, 112, 7, 34);
                    $this->text($pdf, $processing['processing_purpose'] ?? '', 91, 112, 7, 34);
                    $this->text($pdf, $processing['data_recipients'] ?? '', 128, 112, 7, 34);
                    $this->text($pdf, $processing['legal_grounds'] ?? '', 165, 112, 7, 34);

                    return;
                }

                if ($page === 3) {
                    $this->text($pdf, $form->sensitive_data_details['details'] ?? '', 16, 85, 7, 174);
                    $this->text($pdf, $form->processors['details'] ?? '', 16, 219, 7, 174);

                    return;
                }

                if ($page === 4) {
                    $this->text($pdf, $form->security_measures['risks'] ?? '', 16, 42, 7, 80);
                    $this->text($pdf, $form->security_measures['details'] ?? '', 104, 42, 7, 88);
                    $this->text($pdf, $form->cross_border_transfers['details'] ?? '', 16, 133, 7, 174);
                    $this->text($pdf, $this->attachmentChecklist($form), 155, 174, 8, 35);
                    $this->text($pdf, $entity['declarant_name'] ?? '', 35, 235, 8, 70);
                    $this->text($pdf, $entity['declarant_position'] ?? '', 122, 235, 8, 70);
                    $this->signature($pdf, $form, 35, 247, 38, 14);
                }
            },
        );
    }

    public function dp2(FormDp2 $form): string
    {
        return $this->overlay(
            'dp2-template.pdf',
            function (Fpdi $pdf, int $page) use ($form): void {
                if ($page === 1) {
                    $this->text($pdf, $form->controller_name ?? $form->organization?->name ?? '', 112, 120, 7);
                    $this->text($pdf, $form->controller_license_number ?? '', 112, 131, 7);
                    $this->text($pdf, $form->controller_physical_address ?? '', 112, 136, 7, 88);
                    $this->text($pdf, $form->controller_postal_address ?? '', 112, 146, 7, 88);
                    $this->text($pdf, $form->controller_telephone ?? '', 82, 156, 7);
                    $this->text($pdf, $form->controller_fax ?? '', 165, 156, 7);
                    $this->text($pdf, $form->controller_email ?? '', 112, 165, 7, 88);
                    $this->text($pdf, $form->business_scope ?? '', 112, 179, 7, 88);
                    $this->text($pdf, $form->full_name, 113, 193, 7);
                    $this->text($pdf, $form->dpo_registration_number ?? '', 105, 204, 7);
                    $this->text($pdf, $form->official_email, 151, 213, 7);
                    $this->text($pdf, $form->official_phone, 60, 217, 7);
                    $this->text($pdf, $form->dpo_mobile, 60, 226, 7);

                    return;
                }

                $this->text($pdf, $form->dpo_address ?? '', 32, 44, 7, 155);
                $this->text($pdf, implode('; ', $form->qualifications ?? []), 32, 86, 7, 155);
            },
        );
    }

    public function dp3(BreachIncident $incident): string
    {
        return $this->overlay(
            'dp3-template.pdf',
            function (Fpdi $pdf, int $page) use ($incident): void {
                if ($page === 1) {
                    $this->text($pdf, $incident->organization?->name ?? '', 110, 135, 8);

                    return;
                }

                $this->text($pdf, $incident->occurred_at?->format('d M Y, H:i') ?? '', 82, 28, 8);
                $this->text($pdf, $incident->detected_at->format('d M Y, H:i'), 82, 38, 8);
                $this->text($pdf, $incident->title, 45, 58, 8, 150);
                $this->text($pdf, implode('; ', $incident->affected_data_subjects ?? []), 45, 99, 8, 150);
                $this->text($pdf, $incident->description, 45, 143, 8, 150);
                $this->text($pdf, 'Severity: '.$incident->severity, 45, 193, 8, 150);
            },
        );
    }

    /** @param callable(Fpdi, int): void $fill */
    private function overlay(string $template, callable $fill): string
    {
        $path = base_path('dpa/'.$template);

        if (! File::exists($path)) {
            throw new RuntimeException('POTRAZ template not found: '.$template);
        }

        $pdf = new Fpdi;
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetMargins(0, 0, 0);
        $pageCount = $pdf->setSourceFile($path);

        for ($page = 1; $page <= $pageCount; $page++) {
            $templateId = $pdf->importPage($page);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
            $fill($pdf, $page);
        }

        return $pdf->Output('', 'S');
    }

    private function text(Fpdi $pdf, string $value, float $x, float $y, int $size, float $width = 110): void
    {
        $pdf->SetFont('helvetica', '', $size);
        $pdf->SetTextColor(20, 30, 35);
        $pdf->SetXY($x, $y - 2);
        $pdf->MultiCell(
            $width,
            5,
            strtoupper(trim($value)),
            0,
            'L',
            false,
            1,
            '',
            '',
            true,
            0,
            false,
            true,
            5,
            'T',
        );
    }

    private function attachmentChecklist(FormDp1 $form): string
    {
        $attachments = $form->attachments ?? [];

        return collect([
            'certificate_of_incorporation',
            'cr6_cr14',
            'tax_clearance',
            'data_protection_policy',
            'signature_file',
        ])->map(fn (string $key): string => array_key_exists($key, $attachments) ? 'X' : '')
            ->filter()
            ->implode("\n");
    }

    private function signature(Fpdi $pdf, FormDp1 $form, float $x, float $y, float $width, float $height): void
    {
        $path = $form->attachments['signature_file'] ?? null;

        if ($path === null || ! Storage::disk('private')->exists($path)) {
            return;
        }

        $absolutePath = Storage::disk('private')->path($path);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return;
        }

        $pdf->Image($absolutePath, $x, $y, $width, $height, strtoupper($extension === 'jpg' ? 'jpeg' : $extension));
    }
}

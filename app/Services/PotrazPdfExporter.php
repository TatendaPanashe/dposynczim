<?php

namespace App\Services;

use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use Illuminate\Support\Facades\File;
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
            function (Fpdi $pdf, int $page) use ($form, $entity): void {
                if ($page !== 1) {
                    return;
                }

                $this->text($pdf, $entity['entity_name'] ?? '', 32, 105, 8);
                $this->text($pdf, $entity['registration_number'] ?? '', 12, 124, 8);
                $this->text($pdf, $form->tier, 151, 124, 8);
                $this->text($pdf, $entity['business_sector'] ?? '', 45, 170, 8);
                $this->text($pdf, $entity['physical_address'] ?? '', 45, 177, 8);
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
                    $this->text($pdf, $form->controller_physical_address ?? '', 112, 141, 7, 88);
                    $this->text($pdf, $form->controller_postal_address ?? '', 112, 151, 7, 88);
                    $this->text($pdf, $form->controller_telephone ?? '', 82, 161, 7);
                    $this->text($pdf, $form->controller_fax ?? '', 165, 161, 7);
                    $this->text($pdf, $form->controller_email ?? '', 112, 170, 7, 88);
                    $this->text($pdf, $form->business_scope ?? '', 112, 179, 7, 88);
                    $this->text($pdf, $form->full_name, 113, 193, 7);
                    $this->text($pdf, $form->dpo_registration_number ?? '', 105, 204, 7);
                    $this->text($pdf, $form->official_email, 151, 213, 7);
                    $this->text($pdf, $form->official_phone, 60, 222, 7);
                    $this->text($pdf, $form->dpo_mobile, 60, 231, 7);

                    return;
                }

                $this->text($pdf, $form->dpo_address ?? '', 32, 44, 7, 155);
                $this->text($pdf, implode('; ', $form->qualifications ?? []), 32, 88, 7, 155);
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
}

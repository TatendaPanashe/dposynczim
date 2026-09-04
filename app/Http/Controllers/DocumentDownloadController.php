<?php

namespace App\Http\Controllers;

use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\RopaRecord;
use App\Services\PotrazPdfExporter;
use Symfony\Component\HttpFoundation\Response;

class DocumentDownloadController extends Controller
{
    public function dp1(FormDp1 $formDp1, PotrazPdfExporter $exporter): Response
    {
        return $this->pdfDownload($exporter->dp1($formDp1), 'DP1-'.$formDp1->id.'.pdf');
    }

    public function dp2(FormDp2 $formDp2, PotrazPdfExporter $exporter): Response
    {
        return $this->pdfDownload($exporter->dp2($formDp2), 'DP2-'.$formDp2->id.'.pdf');
    }

    public function ropa(): Response
    {
        return response()->streamDownload(function (): void {
            echo view('exports.ropa-xls', ['records' => RopaRecord::latest()->get()])->render();
        }, 'ROPA-register.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function incident(BreachIncident $breachIncident, PotrazPdfExporter $exporter): Response
    {
        return $this->pdfDownload($exporter->dp3($breachIncident), $breachIncident->reference.'.pdf');
    }

    private function pdfDownload(string $content, string $filename): Response
    {
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}

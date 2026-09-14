<?php

namespace App\Http\Controllers;

use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\MonthlyPayment;
use App\Models\RopaRecord;
use App\Services\PotrazPdfExporter;
use App\Services\RopaExcelExporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentDownloadController extends Controller
{
    public function dp1(Request $request, FormDp1 $formDp1, PotrazPdfExporter $exporter): Response
    {
        if (! $this->hasMonthlyAccess($request)) {
            return to_route('compliance.payments.dp1', $formDp1);
        }

        return $this->pdfDownload($exporter->dp1($formDp1), 'DP1-'.$formDp1->id.'.pdf');
    }

    public function dp2(Request $request, FormDp2 $formDp2, PotrazPdfExporter $exporter): Response
    {
        if (! $this->hasMonthlyAccess($request)) {
            return to_route('compliance.payments.dp2', $formDp2);
        }

        return $this->pdfDownload($exporter->dp2($formDp2), 'DP2-'.$formDp2->id.'.pdf');
    }

    public function ropa(Request $request, RopaExcelExporter $exporter): Response
    {
        if (! $this->hasMonthlyAccess($request)) {
            return to_route('compliance.payments.ropa');
        }

        $records = RopaRecord::with('organization')->latest()->get();

        return response($exporter->export($records), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="ROPA-register.xlsx"',
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

    private function hasMonthlyAccess(Request $request): bool
    {
        $organization = $request->user()->activeOrganization();

        if ($organization === null) {
            return false;
        }

        return MonthlyPayment::where('organization_id', $organization->id)
            ->where('month', now()->format('Y-m'))
            ->where('status', 'paid')
            ->exists();
    }
}

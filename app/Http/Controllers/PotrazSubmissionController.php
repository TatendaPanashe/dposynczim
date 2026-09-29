<?php

namespace App\Http\Controllers;

use App\Mail\PotrazApplicationSubmitted;
use App\Models\BreachIncident;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\MonthlyPayment;
use App\Services\PotrazPdfExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PotrazSubmissionController extends Controller
{
    public function dp1(Request $request, FormDp1 $formDp1, PotrazPdfExporter $exporter): RedirectResponse
    {
        if (! $this->hasMonthlyAccess($request)) {
            return to_route('compliance.payments.dp1', $formDp1);
        }

        $entity = $formDp1->entity_profile ?? [];

        $this->send(
            documentType: 'DP1',
            subjectName: $entity['entity_name'] ?? $formDp1->organization?->name ?? 'Data controller',
            filename: 'DP1-'.$formDp1->id.'.pdf',
            pdfContent: $exporter->dp1($formDp1),
        );

        return back()->with('success', 'DP1 application sent to POTRAZ/DPA.');
    }

    public function dp2(Request $request, FormDp2 $formDp2, PotrazPdfExporter $exporter): RedirectResponse
    {
        if (! $this->hasMonthlyAccess($request)) {
            return to_route('compliance.payments.dp2', $formDp2);
        }

        $this->send(
            documentType: 'DP2',
            subjectName: $formDp2->controller_name ?? $formDp2->organization?->name ?? $formDp2->full_name,
            filename: 'DP2-'.$formDp2->id.'.pdf',
            pdfContent: $exporter->dp2($formDp2),
        );

        return back()->with('success', 'DP2 application sent to POTRAZ/DPA.');
    }

    public function dp3(Request $request, BreachIncident $breachIncident, PotrazPdfExporter $exporter): RedirectResponse
    {
        if (! $this->hasMonthlyAccess($request)) {
            return to_route('compliance.payments.dp3', $breachIncident);
        }

        $this->send(
            documentType: 'DP3',
            subjectName: $breachIncident->organization?->name ?? $breachIncident->reference,
            filename: $breachIncident->reference.'.pdf',
            pdfContent: $exporter->dp3($breachIncident),
        );

        $breachIncident->update(['dp3_submitted_at' => now()]);

        return back()->with('success', 'DP3 notification sent to POTRAZ/DPA.');
    }

    private function send(string $documentType, string $subjectName, string $filename, string $pdfContent): void
    {
        $user = auth()->user();

        Mail::to(config('mail.potraz_applications_to'))->send(
            new PotrazApplicationSubmitted(
                documentType: $documentType,
                subjectName: $subjectName,
                filename: $filename,
                pdfContent: $pdfContent,
                senderName: $user?->name ?? config('app.name'),
                senderEmail: $user?->email ?? config('mail.from.address'),
            ),
        );
    }

    private function hasMonthlyAccess(Request $request): bool
    {
        if ($request->user()->isAdmin()) {
            return true;
        }

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

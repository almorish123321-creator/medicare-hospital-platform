<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateInvoicePdf(Invoice $invoice): string
    {
        // Note: requires barryvdh/laravel-dompdf package
        $pdf = Pdf::loadView('pdfs.invoice', ['invoice' => $invoice]);
        $path = "invoices/invoice_{$invoice->id}.pdf";
        Storage::put($path, $pdf->output());
        return $path;
    }

    public function generatePrescriptionPdf($prescription): string
    {
        $pdf = Pdf::loadView('pdfs.prescription', ['prescription' => $prescription]);
        $path = "prescriptions/prescription_{$prescription->id}.pdf";
        Storage::put($path, $pdf->output());
        return $path;
    }

    public function generateReportPdf(string $view, array $data, string $filename): string
    {
        $pdf = Pdf::loadView($view, $data);
        $path = "reports/{$filename}";
        Storage::put($path, $pdf->output());
        return $path;
    }
}

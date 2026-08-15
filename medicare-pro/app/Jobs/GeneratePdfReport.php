<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Services\PdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * GeneratePdfReport Job
 *
 * Generates PDF documents asynchronously. Supports multiple report
 * types such as invoices and medical records. The generated file is
 * stored on the configured disk and its path is returned for
 * downstream use (e.g. email attachments, downloads).
 *
 * @property string               $type     The report type ('invoice' or 'medical_record').
 * @property int                  $model_id The ID of the model to generate the report for.
 * @property array<string, mixed> $data     Optional extra data passed to the PDF view.
 */
class GeneratePdfReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * The supported report types.
     */
    private const SUPPORTED_TYPES = ['invoice', 'medical_record'];

    /**
     * Create a new job instance.
     *
     * @param  string               $type     One of: 'invoice', 'medical_record'.
     * @param  int                  $model_id The primary key of the target model.
     * @param  array<string, mixed> $data     Extra context for the PDF view template.
     */
    public function __construct(
        public string $type,
        public int $model_id,
        public array $data = [],
    ) {
        $this->onQueue('reports');
    }

    /**
     * Execute the job.
     *
     * Resolves the model, delegates to the appropriate PdfService method,
     * and persists the output. If the type is unsupported or the model is
     * missing the job fails gracefully.
     */
    public function handle(PdfService $pdfService): ?string
    {
        if (! in_array($this->type, self::SUPPORTED_TYPES, true)) {
            Log::warning("GeneratePdfReport: Unsupported report type '{$this->type}'.");
            return null;
        }

        try {
            return match ($this->type) {
                'invoice' => $this->generateInvoice($pdfService),
                'medical_record' => $this->generateMedicalRecord($pdfService),
            };
        } catch (\Throwable $e) {
            Log::error("GeneratePdfReport: Failed to generate {$this->type} PDF for model #{$this->model_id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
 * Generate an invoice PDF for the given model ID.
 */
    private function generateInvoice(PdfService $pdfService): string
    {
        $invoice = Invoice::with([
            'patient.user',
            'appointment.doctor.user',
            'hospital',
            'payments',
        ])->findOrFail($this->model_id);

        $path = $pdfService->generateInvoicePdf($invoice);

        Log::info("GeneratePdfReport: Invoice PDF generated.", [
            'invoice_id' => $this->model_id,
            'path' => $path,
        ]);

        return $path;
    }

    /**
     * Generate a medical record PDF for the given model ID.
     */
    private function generateMedicalRecord(PdfService $pdfService): string
    {
        $record = MedicalRecord::with([
            'patient.user',
            'doctor.user',
            'appointment',
            'prescription.items',
        ])->findOrFail($this->model_id);

        $data = array_merge($this->data, [
            'record' => $record,
            'generated_at' => now()->toDateTimeString(),
        ]);

        $filename = "medical_record_{$record->id}_" . now()->format('Ymd_His') . '.pdf';
        $path = $pdfService->generateReportPdf('pdfs.medical-record', $data, $filename);

        Log::info('GeneratePdfReport: Medical record PDF generated.', [
            'medical_record_id' => $this->model_id,
            'path' => $path,
        ]);

        return $path;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GeneratePdfReport: Job failed permanently.', [
            'type' => $this->type,
            'model_id' => $this->model_id,
            'error' => $exception->getMessage(),
        ]);
    }
}

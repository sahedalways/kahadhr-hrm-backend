<?php

namespace App\Traits;

use App\Exports\GenericExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Excel as ExcelType;

trait Exportable
{
    public function export($data, string $type, string $filename = 'export', string $pdfView = null, array $pdfData = []): Response
    {
        if ($data instanceof Builder) {
            $data = $data->get();
        }

        if (!($data instanceof Collection)) {
            throw new \Exception("Data must be a Collection or Eloquent Builder");
        }

        // PDF export
        if ($type === 'pdf') {
            if (!$pdfView) {
                throw new \Exception("PDF view is required for PDF export");
            }

            $pdfData = array_merge($pdfData, ['data' => $data]);
            $pdf = Pdf::loadView($pdfView, $pdfData);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename . '.pdf');
        }

        // Excel / CSV export
        // Excel / CSV export
        if (in_array($type, ['excel', 'csv'])) {

            $extraHeadings = [
                [siteSetting()->site_title ?? 'My Site'],
                ['Email: ' . (siteSetting()->site_email ?? '') . ' | Phone: ' . (siteSetting()->site_phone_number ?? '')],
                ['Print Date: ' . now()->format('d M Y, H:i')],
                [$filename],
                []
            ];

            // Pass $columns as third argument
            return Excel::download(
                new GenericExport($data, $extraHeadings, $pdfData['columns'] ?? []),
                $filename . '.' . ($type === 'excel' ? 'xlsx' : 'csv'),
                $type === 'excel' ? ExcelType::XLSX : ExcelType::CSV
            );
        }


        throw new \Exception("Invalid export type: {$type}");
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class GenericExport implements FromCollection, WithHeadings, WithEvents
{
    public $data;
    public $columns;
    public $extraHeadings;

    public function __construct(Collection $data, array $extraHeadings = [], array $columns = [])
    {
        $this->data          = $data;
        $this->extraHeadings = $extraHeadings;
        $this->columns       = $columns; // Table column titles
    }

    public function collection()
    {
        return $this->data;
    }

    /**
     * Combine:
     * - Top headings (site info)
     * - Blank row
     * - Table column headings
     */
    public function headings(): array
    {
        $headings = [];


        if (!empty($this->extraHeadings)) {
            foreach ($this->extraHeadings as $extra) {
                $headings[] = $extra;
            }
        }

        $headings[] = [];
        $headings[] = [];

        // Add actual table headings
        if (!empty($this->columns)) {
            $headings[] = $this->columns;
        }

        return $headings;
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Make extra headings bold (top rows)
                if (!empty($this->extraHeadings)) {
                    $event->sheet->getStyle('A1:Z' . count($this->extraHeadings))->getFont()->setBold(true);
                }

                // Table heading row index
                $headingRow = 2 + count($this->extraHeadings) + 1; // 2 blank rows + extra headings

                // Bold & center table headings
                $event->sheet->getStyle('A' . $headingRow . ':Z' . $headingRow)->getFont()->setBold(true);
                $event->sheet->getStyle('A' . $headingRow . ':Z' . $headingRow)->getAlignment()->setHorizontal('center');

                // Set row height for top rows
                for ($i = 1; $i <= $headingRow; $i++) {
                    $event->sheet->getRowDimension($i)->setRowHeight(22);
                }

                // Table heading background color
                $event->sheet->getStyle('A' . $headingRow . ':Z' . $headingRow)->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'E0EBF7']
                    ]
                ]);
            }
        ];
    }
}

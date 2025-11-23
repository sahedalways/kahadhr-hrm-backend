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
        return array_merge(
            $this->extraHeadings,
            [['']],        // blank row 1
            [['']],        // blank row 2
            [$this->columns] // actual table headings
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Make all heading rows bold
                $event->sheet->getStyle('A1:Z10')->getFont()->setBold(true);

                // Center alignment for header and table headings
                $event->sheet->getStyle('A1:Z10')->getAlignment()->setHorizontal('center');

                // Increase row height for beauty
                foreach ([1, 2, 3, 4, 5, 6, 7] as $row) {
                    $event->sheet->getRowDimension($row)->setRowHeight(22);
                }

                // Table heading background color
                $event->sheet->getStyle('A8:Z8')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'E0EBF7']
                    ]
                ]);
            }
        ];
    }
}

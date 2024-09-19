<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use DB;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use \Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LeadsTemplateExport implements FromCollection,WithCustomStartCell, WithEvents,WithColumnFormatting 
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function columnFormats(): array
    {
        return [
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function registerEvents(): array {
        
        return [
            AfterSheet::class => function(AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                $sheet->setCellValue('A1', "No.");
                $sheet->setCellValue('B1', "Tanggal");
                $sheet->setCellValue('C1', "Nama");
                $sheet->setCellValue('D1', "Nama Perusahaan");
                $sheet->setCellValue('E1', "Jabatan");
                $sheet->setCellValue('F1', "Nomor Telepon");
                $sheet->setCellValue('G1', "Email");
                $sheet->setCellValue('H1', "Kebutuhan");
                $sheet->setCellValue('I1', "Wilayah");
                $sheet->setCellValue('J1', "Sumber Leads");
                $sheet->setCellValue('K1', "Keterangan");
                $sheet->setCellValue('L1', "Keterangan Lanjutan");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ];
                
                $cellRange = 'A1:L1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);

                $event->sheet->getStyle('A2:L800')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }

    public function collection()
    {
        $data = [];
        
        return collect($data);
    }
}

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
                $sheet->setCellValue('B1', "Tanggal ( Contoh : 22-01-2024 )");
                $sheet->setCellValue('C1', "Nama Perusahaan");
                $sheet->setCellValue('D1', "Jenis Perusahaan ( Sesuai Master Jenis Perusahaan )");
                $sheet->setCellValue('E1', "No. Telp. Perusahaan");
                $sheet->setCellValue('F1', "PIC");
                $sheet->setCellValue('G1', "Jabatan PIC");
                $sheet->setCellValue('H1', "No Telp. PIC");
                $sheet->setCellValue('I1', "Email PIC");
                $sheet->setCellValue('J1', "Kebutuhan ( Security , Direct Labour , Cleaning Service , Logistik )");
                $sheet->setCellValue('K1', "Wilayah ( Sesuai Master )");
                $sheet->setCellValue('L1', "Sumber Leads ( Sesuai Master )");
                $sheet->setCellValue('M1', "Alamat");
                $sheet->setCellValue('N1', "Keterangan");
                $sheet->setCellValue('O1', "Username Sales");

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
                
                $cellRange = 'A1:O1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);

                $event->sheet->getStyle('A2:O800')->applyFromArray([
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

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

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class BarangTemplateExport implements FromCollection, WithCustomStartCell, WithEvents, WithColumnFormatting, WithMultipleSheets, WithTitle
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
        return 'A2';
    }

    public function registerEvents(): array {

        return [
            AfterSheet::class => function(AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                $sheet->setCellValue('A1', "ID");
                $sheet->setCellValue('B1', "Nama");
                $sheet->setCellValue('C1', "ID Jenis Barang");
                $sheet->setCellValue('D1', "Jenis Barang ( Biarkan Kosong )");
                $sheet->setCellValue('E1', "Harga");
                $sheet->setCellValue('F1', "Satuan");
                $sheet->setCellValue('G1', "Masa Pakai");
                $sheet->setCellValue('H1', "Merk");
                $sheet->setCellValue('I1', "Jumlah Default");
                $sheet->setCellValue('J1', "Urutan");
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

                $cellRange = 'A1:J1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);

                $event->sheet->getStyle('A2:J1000')->applyFromArray([
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

    public function sheets(): array
    {
        return [
            'Data Barang' => new BarangTemplateExport(),
            'Ref Jenis Barang' => new RefJenisBarang(),
        ];
    }

    public function title(): string
    {
        return 'Data Barang';
    }

    public function collection()
    {
        $data = DB::table("m_barang")
            ->select('id','nama','jenis_barang_id','jenis_barang','harga','satuan','masa_pakai','merk','jumlah_default','urutan')
            ->whereNull('deleted_at')
            ->orderBy('jenis_barang_id','asc')
            ->orderBy('urutan','asc')
            ->orderBy('nama','asc')
            ->get();
        return collect($data);
    }
}
class RefJenisBarang implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_jenis_barang")->select('id','nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Jenis Barang';
    }
}

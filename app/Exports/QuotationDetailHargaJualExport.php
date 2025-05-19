<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Maatwebsite\Excel\Events\AfterSheet;
use DB;

class QuotationDetailHargaJualExport implements WithMultipleSheets
{
    protected $quotationId;
    protected $jenis;

    public function __construct($quotationId, $jenis)
    {
        $this->quotationId = $quotationId;
        $this->jenis = $jenis;
    }

    public function sheets(): array
    {
        switch ($this->jenis) {
            case "All":
                return [
                    ...$this->getKaporlapSheets(),
                    new QuotationDevicesExport($this->quotationId),
                    new QuotationOHCExport($this->quotationId),
                    new QuotationChemicalExport($this->quotationId),
                ];
            case "Kaporlap":
                return $this->getKaporlapSheets();
            case "Devices":
                return [new QuotationDevicesExport($this->quotationId)];
            case "Chemical":
                return [new QuotationChemicalExport($this->quotationId)];
            case "OHC":
                return [new QuotationOHCExport($this->quotationId)];
            default:
                return [];
        }
    }

    private function getKaporlapSheets(): array
    {
        $details = DB::table('sl_quotation_detail')
            ->where('quotation_id', $this->quotationId)
            ->whereNull('deleted_at')
            ->get();

        $sheets = [];
        foreach ($details as $detail) {
            $sheets[] = new QuotationKaporlapDetailSheetExport($this->quotationId, $detail);
        }

        return $sheets;
    }
}

class QuotationKaporlapDetailSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $quotationId;
    protected $quotationDetail;

    public function __construct($quotationId, $quotationDetail)
    {
        $this->quotationId = $quotationId;
        $this->quotationDetail = $quotationDetail;
    }

    public function collection()
    {
        $data = DB::table('sl_quotation_kaporlap')
        ->join('m_barang', 'sl_quotation_kaporlap.barang_id', '=', 'm_barang.id')
        ->where('sl_quotation_kaporlap.quotation_id', $this->quotationId)
        ->where('sl_quotation_kaporlap.quotation_detail_id', $this->quotationDetail->id)
        ->whereNull('sl_quotation_kaporlap.deleted_at')
        ->select([
            'sl_quotation_kaporlap.jenis_barang',
            'sl_quotation_kaporlap.nama',
            'sl_quotation_kaporlap.jumlah',
            'sl_quotation_kaporlap.harga',
            DB::raw('sl_quotation_kaporlap.jumlah * sl_quotation_kaporlap.harga as total'),
        ])
        ->orderBy('sl_quotation_kaporlap.jenis_barang', 'asc')
        ->orderBy('m_barang.urutan', 'asc')
        ->get();

        foreach ($data as $index => $item) {
            $item->no = $index + 1;
        }
        $data = $data->map(function ($item) {
            return [
                'No' => $item->no,
                'Jenis Barang' => $item->jenis_barang,
                'Nama' => $item->nama,
                'Jumlah' => $item->jumlah,
                'Harga' => $item->harga,
                'Total' => $item->total,
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            "No.",
            "Jenis Barang",
            "Nama",
            "Jumlah",
            "Harga",
            "Total"
        ];
    }

    public function title(): string
    {
        return 'Kaporlap ' . $this->quotationDetail->jabatan_kebutuhan;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $styleArray = [
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    'font' => ['bold' => true],
                ];

                $event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray($styleArray);
                $event->sheet->autoSize(true);

                $event->sheet->getStyle('A2:F1000')->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
            }
        ];
    }
}



// Sheet untuk sl_quotation_devices
class QuotationDevicesExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $quotationId;

    public function __construct($quotationId)
    {
        $this->quotationId = $quotationId;
    }

    public function collection()
    {
        $data = DB::table('sl_quotation_devices')
        ->where('quotation_id', $this->quotationId)
        ->whereNull('deleted_at')
        ->select([
            'id as no',
            'jenis_barang',
            'nama',
            'jumlah',
            'harga',
            DB::raw('jumlah * harga as total'),
        ])
        ->orderBy('sl_quotation_devices.jenis_barang', 'asc')
        ->get()
        ->map(function ($item, $index) {
            $item->no = $index + 1;
            return $item;
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            "No.",
            "Jenis Barang",
            "Nama",
            "Jumlah",
            "Harga",
            "Total"
        ];
    }

    public function title(): string
    {
        return 'Devices';
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet;
                $sheet->setCellValue('A1', "No.");
                $sheet->setCellValue('B1', "Jenis Barang");
                $sheet->setCellValue('C1', "Nama");
                $sheet->setCellValue('D1', "Jumlah");
                $sheet->setCellValue('E1', "Harga");
                $sheet->setCellValue('F1', "Total");
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
                $cellRange = 'A1:F1';
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);
                $event->sheet->getStyle('A2:F1000')->applyFromArray([
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
}

// Sheet untuk sl_quotation_ohc
class QuotationOHCExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $quotationId;

    public function __construct($quotationId)
    {
        $this->quotationId = $quotationId;
    }

    public function collection()
    {
        $data = DB::table('sl_quotation_ohc')
        ->where('quotation_id', $this->quotationId)
        ->whereNull('deleted_at')
        ->select([
            'id as no',
            'jenis_barang',
            'nama',
            'jumlah',
            'harga',
            DB::raw('jumlah * harga as total'),
        ])
        ->orderBy('sl_quotation_ohc.jenis_barang', 'asc')
        ->get()
        ->map(function ($item, $index) {
            $item->no = $index + 1;
            return $item;
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            "No.",
            "Jenis Barang",
            "Nama",
            "Jumlah",
            "Harga",
            "Total"
        ];
    }

    public function title(): string
    {
        return 'OHC';
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet;
                $sheet->setCellValue('A1', "No.");
                $sheet->setCellValue('B1', "Jenis Barang");
                $sheet->setCellValue('C1', "Nama");
                $sheet->setCellValue('D1', "Jumlah");
                $sheet->setCellValue('E1', "Harga");
                $sheet->setCellValue('F1', "Total");
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
                $cellRange = 'A1:F1';
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);
                $event->sheet->getStyle('A2:F1000')->applyFromArray([
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
}

// Sheet untuk sl_quotation_chemical
class QuotationChemicalExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $quotationId;

    public function __construct($quotationId)
    {
        $this->quotationId = $quotationId;
    }

    public function collection()
    {
        $data = DB::table('sl_quotation_chemical')
        ->where('quotation_id', $this->quotationId)
        ->whereNull('deleted_at')
        ->select([
            'id as no',
            'jenis_barang',
            'nama',
            'jumlah',
            'harga',
            DB::raw('jumlah * harga as total'),
        ])
        ->orderBy('sl_quotation_chemical.jenis_barang', 'asc')
        ->get()
        ->map(function ($item, $index) {
            $item->no = $index + 1;
            return $item;
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            "No.",
            "Jenis Barang",
            "Nama",
            "Jumlah",
            "Harga",
            "Total"
        ];
    }

    public function title(): string
    {
        return 'Chemical';
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet;
                $sheet->setCellValue('A1', "No.");
                $sheet->setCellValue('B1', "Jenis Barang");
                $sheet->setCellValue('C1', "Nama");
                $sheet->setCellValue('D1', "Jumlah");
                $sheet->setCellValue('E1', "Harga");
                $sheet->setCellValue('F1', "Total");
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
                $cellRange = 'A1:F1';
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);
                $event->sheet->getStyle('A2:F1000')->applyFromArray([
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
}

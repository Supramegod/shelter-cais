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
use Carbon\Carbon;

class LeadsExport implements FromCollection,WithCustomStartCell, WithEvents,WithColumnFormatting 
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

                $sheet->setCellValue('A1', "NO.");
                $sheet->setCellValue('B1', "BRANCH");
                $sheet->setCellValue('C1', "TANGGAL");
                $sheet->setCellValue('D1', "PERUSAHAAN");
                $sheet->setCellValue('E1', "TELP. PERUSAHAAN");
                $sheet->setCellValue('F1', "PIC");
                $sheet->setCellValue('G1', "TELP. PIC");
                $sheet->setCellValue('H1', "EMAIL PIC");
                $sheet->setCellValue('I1', "STATUS");
                $sheet->setCellValue('J1', "SUMBER");
                $sheet->setCellValue('K1', "Keterangan");

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
                
                $cellRange = 'A1:K1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);

                $event->sheet->getStyle('A2:K800')->applyFromArray([
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
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $data = DB::table('sl_leads')
                        ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
                        ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
                        ->leftJoin('m_platform','sl_leads.platform_id','=','m_platform.id')
                        ->select('sl_leads.nomor',$db2.'.m_branch.name as branch','sl_leads.tgl_leads','sl_leads.nama_perusahaan','sl_leads.telp_perusahaan','sl_leads.pic','sl_leads.no_telp','sl_leads.email', 'm_status_leads.nama as status',  'm_platform.nama as platform',  'sl_leads.notes')
                        ->whereNull('sl_leads.deleted_at')
                        ->get();
        foreach ($data as $key => $value) {
            $value->tgl_leads = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_leads)->isoFormat('D MMMM Y');
        }       
        return collect($data);
    }
}

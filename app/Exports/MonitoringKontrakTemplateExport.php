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

class MonitoringKontrakTemplateExport implements FromCollection, WithCustomStartCell, WithEvents, WithColumnFormatting, WithMultipleSheets, WithTitle
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

                $sheet->setCellValue('A1', "No Kontrak");
                $sheet->setCellValue('B1', "CRM 1");
                $sheet->setCellValue('C1', "CRM 2");
                $sheet->setCellValue('D1', "CRM 3");
                $sheet->setCellValue('E1', "SPV RO");
                $sheet->setCellValue('F1', "RO 1");
                $sheet->setCellValue('G1', "RO 2");
                $sheet->setCellValue('H1', "RO 3");
                $sheet->setCellValue('I1', "Kode Site");
                $sheet->setCellValue('J1', "Nama Site");
                $sheet->setCellValue('K1', "Alamat Site");
                $sheet->setCellValue('L1', "Kode Perusahaan");
                $sheet->setCellValue('M1', "Nama Perusahaan");
                $sheet->setCellValue('N1', "Alamat Perusahaan");
                $sheet->setCellValue('O1', "Entitas");
                $sheet->setCellValue('P1', "Nama Proyek");
                $sheet->setCellValue('Q1', "Status PKS");
                $sheet->setCellValue('R1', "Layanan");
                $sheet->setCellValue('S1', "Cabang");
                $sheet->setCellValue('T1', "Bidang Usaha");
                $sheet->setCellValue('U1', "Jenis Perusahaan");
                $sheet->setCellValue('V1', "Provinsi");
                $sheet->setCellValue('W1', "Kota");
                $sheet->setCellValue('X1', "PMA/PMDN");
                $sheet->setCellValue('Y1', "Loyalty");
                $sheet->setCellValue('Z1', "Kontrak Awal");
                $sheet->setCellValue('AA1', "Kontrak Akhir");
                $sheet->setCellValue('AB1', "Jumlah HC");
                $sheet->setCellValue('AC1', "Total Sebelum Pajak");
                $sheet->setCellValue('AD1', "Dasar Pengenaan Pajak");
                $sheet->setCellValue('AE1', "PPN");
                $sheet->setCellValue('AF1', "PPh");
                $sheet->setCellValue('AG1', "Total Invoice");
                $sheet->setCellValue('AH1', "Persen MF");
                $sheet->setCellValue('AI1', "Nominal MF");
                $sheet->setCellValue('AJ1', "Persen BPJS TK");
                $sheet->setCellValue('AK1', "Nominal BPJS TK");
                $sheet->setCellValue('AL1', "Persen BPJS KES");
                $sheet->setCellValue('AM1', "Nominal BPJS KES");
                $sheet->setCellValue('AN1', "Asuransi TK");
                $sheet->setCellValue('AO1', "Asuransi Kes");
                $sheet->setCellValue('AP1', "OHC");
                $sheet->setCellValue('AQ1', "THR Provisi");
                $sheet->setCellValue('AR1', "THR Ditagihkan");
                $sheet->setCellValue('AS1', "Penagihan Selisih THR");
                $sheet->setCellValue('AT1', "Kaporlap");
                $sheet->setCellValue('AU1', "Device");
                $sheet->setCellValue('AV1', "Chemical");
                $sheet->setCellValue('AW1', "Training Dalam 1 Tahun");
                $sheet->setCellValue('AX1', "Biaya Training");
                $sheet->setCellValue('AY1', "Tanggal Kirim Invoice");
                $sheet->setCellValue('AZ1', "Jumlah Hari TOP");
                $sheet->setCellValue('BA1', "Tipe Hari TOP");
                $sheet->setCellValue('BB1', "Tanggal Gaji");
                $sheet->setCellValue('BC1', "PIC 1");
                $sheet->setCellValue('BD1', "Jabatan 1");
                $sheet->setCellValue('BE1', "Email 1");
                $sheet->setCellValue('BF1', "No. Telepon 1");
                $sheet->setCellValue('BG1', "PIC 2");
                $sheet->setCellValue('BH1', "Jabatan 2");
                $sheet->setCellValue('BI1', "Email 2");
                $sheet->setCellValue('BJ1', "No. Telepon 2");
                $sheet->setCellValue('BK1', "PIC 3");
                $sheet->setCellValue('BL1', "Jabatan 3");
                $sheet->setCellValue('BM1', "Email 3");
                $sheet->setCellValue('BN1', "No. Telepon 3");
                $sheet->setCellValue('BO1', "Kategori Sesuai HC");
                $sheet->setCellValue('BP1', "Sales");
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

                $cellRange = 'A1:BP1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->autoSize(true);

                $event->sheet->getStyle('A2:BP1000')->applyFromArray([
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
            'Data Kontrak' => new MonitoringKontrakTemplateExport(),
            'Ref Provinsi' => new RefProvinsiSheet(),
            'Ref Kota' => new RefKotaSheet(),
            'Ref Status PKS' => new RefStatusPksSheet(),
            'Ref Jenis Perusahaan' => new RefJenisPerusahaanSheet(),
            'Ref Bidang Perusahaan' => new RefBidangPerusahaanSheet(),
            'Ref Layanan' => new RefLayananSheet(),
            'Ref Branch' => new RefBranchSheet(),
            'Ref Entitas' => new RefEntitasSheet(),
            'Ref Loyalty' => new RefLoyaltySheet(),
            'Ref Tipe Hari TOP' => new RefTipeHariTopSheet(),
            'Ref Kategori Sesuai HC' => new RefKategoriSesuaiHcSheet(),
            'Ref RO' => new RefROSheet(),
            'Ref CRM' => new RefCRMSheet(),
            'Ref Sales' => new RefSalesSheet(),
        ];
    }

    public function title(): string
    {
        return 'Data Kontrak';
    }

    public function collection()
    {
        $data = [];

        return collect($data);
    }
}
class RefProvinsiSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_province")->select('name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Provinsi';
    }
}
class RefKotaSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_city")->select('name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Kota';
    }
}

class RefStatusPksSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_status_pks")->select('nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Status PKS';
    }
}

class RefJenisPerusahaanSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_jenis_perusahaan")->select('nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Jenis Perusahaan';
    }
}

class RefBidangPerusahaanSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_bidang_perusahaan")->select('nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Bidang Perusahaan';
    }
}

class RefLayananSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_kebutuhan")->select('nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Layanan';
    }
}

class RefBranchSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_branch")->select('name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Branch';
    }
}

class RefEntitasSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_company")->select('name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Entitas';
    }
}

class RefLoyaltySheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_loyalty")->select('nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Loyalty';
    }
}

class RefTipeHariTopSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = [
            ["Kalender"],
            ["Kerja"]
        ];
        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Tipe Hari TOP';
    }
}

class RefKategoriSesuaiHcSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $data = DB::table("m_kategori_sesuai_hc")->select('nama')->whereNull('deleted_at')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Kategori Sesuai HC';
    }
}

class RefROSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_user")->whereIn('role_id',[4,5,6,8])->select('full_name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref RO';
    }
}

class RefCRMSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_user")->whereIn('role_id',[54,55,56])->select('full_name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref CRM';
    }
}
class RefSalesSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table("$db2.m_user")->whereIn('role_id',[29,30,31,32,33,34,35])->select('full_name')->get();

        return collect($data);
    }

    public function title(): string
    {
        return 'Ref Sales';
    }
}

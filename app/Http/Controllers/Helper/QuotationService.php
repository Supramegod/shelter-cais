<?php

namespace App\Http\Controllers\Helper;

use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function calculateQuotation($quotation)
    {
        // PERHITUNGAN HPP DAN COSS
        $quotation->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
        $qmanajemenFee = DB::table('m_management_fee')->where('id', $quotation->management_fee_id)->first();
        $quotation->management_fee = $qmanajemenFee->nama;

        $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM sl_quotation_detail_tunjangan WHERE deleted_at IS NULL AND quotation_id = ?", [$quotation->id]);

        $jumlahHc = $quotation->quotation_detail->sum('jumlah_hc');
        $quotation->jumlah_hc = $jumlahHc;
        $provisi = 12;
        if (!strpos($quotation->durasi_kerjasama, 'tahun')) {
            $provisi = (int)str_replace(" bulan", "", $quotation->durasi_kerjasama);
        }
        $quotation->provisi = $provisi;

        foreach ($quotation->quotation_detail as $kbd) {
            $quotationSite = DB::table('sl_quotation_site')->whereNull('deleted_at')->where('id', $kbd->quotation_site_id)->first();
            $kbd->nominal_upah = $quotationSite->nominal_upah;
            $kbd->umk = $quotationSite->umk;
            $kbd->ump = $quotationSite->ump;
            $this->processQuotationDetail($kbd, $daftarTunjangan, $quotation, $jumlahHc, $provisi);
        }

        return $quotation;
    }

    private function processQuotationDetail(&$kbd, $daftarTunjangan, $quotation, $jumlahHc, $provisi)
    {
        // Pindahkan detail proses di sini
        $totalTunjangan = 0;
        foreach ($daftarTunjangan as $tunjangan) {
            $kbd->{$tunjangan->nama} = 0;
            $dtTunjangan = DB::table('sl_quotation_detail_tunjangan')
                ->whereNull('deleted_at')
                ->where('quotation_detail_id', $kbd->id)
                ->where('nama_tunjangan', $tunjangan->nama)
                ->first();

            if ($dtTunjangan) {
                $kbd->{$tunjangan->nama} = $dtTunjangan->nominal;
                $totalTunjangan += $dtTunjangan->nominal;
            }
        }

        $kbd->total_tunjangan = $totalTunjangan;

        $umk = $kbd->umk;

        $this->calculateBpjs($kbd, $quotation, $umk);
        $this->calculateExtras($kbd, $quotation, $provisi, $jumlahHc,$umk);
        // Perhitungan Kaporlap
        $this->calculateKaporlap($kbd, $quotation, $provisi);

        // Perhitungan Devices
        $this->calculateDevices($kbd, $quotation, $provisi, $jumlahHc);

        // Perhitungan OHC
        $this->calculateOhc($kbd, $quotation, $jumlahHc,$provisi);

        // Perhitungan Chemical
        $this->calculateChemical($kbd, $quotation, $provisi);

        $kbd->total_personil = $this->calculateTotalPersonnel($kbd, $quotation, $totalTunjangan);
        $kbd->sub_total_personil = $kbd->total_personil * $kbd->jumlah_hc;

        $kbd->management_fee = 0;
        if($quotation->management_fee_id==1){
            $kbd->management_fee = $kbd->sub_total_personil*$quotation->persentase/100; 
        }else if($quotation->management_fee_id==4){
            $kbd->management_fee = $kbd->sub_total_personil*$quotation->persentase/100; 
        }else if($quotation->management_fee_id==5){
            $kbd->management_fee = $kbd->nominal_upah*$quotation->persentase/100;
        }

        // bunga bank dan insentif
        $pengaliTop = 0;
        if ($quotation->top == "Kurang Dari 7 Hari") {
            $pengaliTop = 7;
        }else if($quotation->top == "Lebih Dari 7 Hari"){
            $pengaliTop = $quotation->jumlah_hari_invoice;
        };

        
        $kbd->bunga_bank = round($kbd->sub_total_personil*$pengaliTop*$quotation->persen_bunga_bank/100,2);
        $kbd->insentif = round($kbd->management_fee*$quotation->persen_insentif/100,2);

        $kbd->grand_total = $kbd->sub_total_personil + $kbd->management_fee + $kbd->bunga_bank + $kbd->insentif;
        $this->calculateTaxes($kbd, $quotation);
        $kbd->total_invoice = $kbd->grand_total + $kbd->ppn + $kbd->pph;
        $kbd->pembulatan = ceil($kbd->total_invoice / 1000) * 1000;

        $isPembulatan = 0;
        if($quotation->penagihan=="Dengan Pembulatan"){
            $isPembulatan = 1;
        }
        $kbd->is_pembulatan = $isPembulatan;

        $this->calculateCoss($kbd, $quotation, $jumlahHc, $provisi,$quotation);

    }

    private function calculateBpjs(&$kbd, $quotation, $umk)
    {
        // Inisialisasi default
        $kbd->nominal_takaful = 0;
        $kbd->bpjs_jkm = 0;
        $kbd->bpjs_jkk = 0;
        $kbd->bpjs_jht = 0;
        $kbd->bpjs_jp = 0;
        $kbd->bpjs_kes = 0;
        $kbd->persen_bpjs_jkm = 0;
        $kbd->persen_bpjs_jkk = 0;
        $kbd->persen_bpjs_jht = 0;
        $kbd->persen_bpjs_jp = 0;
        $kbd->persen_bpjs_kes = 0;

        if ($quotation->penjamin == "Takaful") {
            $kbd->nominal_takaful = $quotation->nominal_takaful;
        } else {
            $upahBpjs = $kbd->nominal_upah < $umk ? $umk : $kbd->nominal_upah;
            // if($umk==null || $umk==0){
            //     $umk = $kbd->nominal_upah;
            // }

            // Hitung JKK berdasarkan resiko
            switch ($quotation->resiko) {
                case "Sangat Rendah":
                    $kbd->bpjs_jkk = $upahBpjs * 0.24 / 100;
                    $kbd->persen_bpjs_jkk = 0.24;
                    break;
                case "Rendah":
                    $kbd->bpjs_jkk = $upahBpjs * 0.54 / 100;
                    $kbd->persen_bpjs_jkk = 0.54;
                    break;
                case "Sedang":
                    $kbd->bpjs_jkk = $upahBpjs * 0.89 / 100;
                    $kbd->persen_bpjs_jkk = 0.89;
                    break;
                case "Tinggi":
                    $kbd->bpjs_jkk = $upahBpjs * 1.27 / 100;
                    $kbd->persen_bpjs_jkk = 1.27;
                    break;
                case "Sangat Tinggi":
                    $kbd->bpjs_jkk = $upahBpjs * 1.74 / 100;
                    $kbd->persen_bpjs_jkk = 1.74;
                    break;
            }

            // Hitung JKM
            $kbd->bpjs_jkm = $upahBpjs * 0.3 / 100;
            $kbd->persen_bpjs_jkm = 0.3;

            // Hitung JHT (jika program BPJS mencakup JHT)
            $kbd->bpjs_jht = in_array($quotation->program_bpjs, ["3 BPJS", "4 BPJS"]) ? $upahBpjs * 3.7 / 100 : 0;
            $kbd->persen_bpjs_jht = in_array($quotation->program_bpjs, ["3 BPJS", "4 BPJS"]) ? 3.7 : 0;

            // Hitung JP (jika program BPJS mencakup JP)
            $kbd->bpjs_jp = $quotation->program_bpjs == "4 BPJS" ? $upahBpjs * 2 / 100 : 0;
            $kbd->persen_bpjs_jp = $quotation->program_bpjs == "4 BPJS" ? 2 : 0;

            // Hitung BPJS Kesehatan berdasarkan UMK
            $kbd->bpjs_kes = $umk * 4 / 100;
            $kbd->persen_bpjs_kes = 4;
        }
    }

    private function calculateExtras(&$kbd, $quotation, $provisi, $jumlahHc,$umk)
    {
        // Tambahkan perhitungan kompensasi, tunjangan, dll.
        // THR
        $kbd->tunjangan_hari_raya = 0;
        if ($quotation->thr == "Diprovisikan") {
            $kbd->tunjangan_hari_raya = $kbd->nominal_upah / $provisi;
        }

        // Kompensasi
        $kbd->kompensasi = 0;
        if ($quotation->kompensasi == "Diprovisikan") {
            $kbd->kompensasi = $kbd->nominal_upah / $provisi;
        }

        // Tunjangan Holiday
        $kbd->tunjangan_holiday = 0;
        $quotation->tunjangan_holiday_display = 0;
        if ($quotation->tunjangan_holiday == "Flat") {
            $kbd->tunjangan_holiday = $quotation->nominal_tunjangan_holiday;
        } else {
            $quotation->tunjangan_holiday_display = ($umk / 173 * 14) * 15;
        }

        // Lembur
        $kbd->lembur = 0;
        $quotation->lembur_per_jam = 0;
        if ($quotation->lembur == "Flat") {
            $kbd->lembur = $quotation->nominal_lembur;
            $quotation->lembur_per_jam = null;
        } else {
            $quotation->lembur_per_jam = ($umk / 173 * 1.5) * 1;
        }
    }

    private function calculateTotalPersonnel($kbd, $quotation, $totalTunjangan)
    {
        // Hitung total personal seperti pada kode
        return $kbd->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_chemical+$kbd->personil_ohc;
    }

    private function calculateTaxes(&$kbd, $quotation)
    {
        // PPN dan PPh default
        $kbd->ppn = 0;
        $kbd->pph = 0;

        if ($quotation->ppn_pph_dipotong == "Management Fee") {
            $kbd->ppn = $kbd->management_fee * 11 / 100;
            $kbd->pph = $kbd->management_fee * -2 / 100;
        } elseif ($quotation->ppn_pph_dipotong == "Total Invoice") {
            $kbd->ppn = $kbd->grand_total * 11 / 100;
            $kbd->pph = $kbd->grand_total * -2 / 100;
        }
    }

    private function calculateKaporlap($kbd, $value, $provisi)
{
    $personilKaporlap = 0;
    $kaporlapItems = DB::table('sl_quotation_kaporlap')
        ->whereNull('deleted_at')
        ->where('quotation_id', $value->id)
        ->where('quotation_detail_id', $kbd->id)
        ->get();

    foreach ($kaporlapItems as $item) {
        $personilKaporlap += ($item->harga * $item->jumlah) / $provisi / $kbd->jumlah_hc;
    }

    $kbd->personil_kaporlap = $personilKaporlap;
}

private function calculateDevices($kbd, $value, $provisi, $jumlahHc)
{
    $personilDevices = 0;
    $deviceItems = DB::table('sl_quotation_devices')
        ->whereNull('deleted_at')
        ->where('quotation_id', $value->id)
        ->get();

    foreach ($deviceItems as $item) {
        $personilDevices += ($item->harga * $item->jumlah / $jumlahHc) / $provisi;
    }

    $kbd->personil_devices = $personilDevices;
}

private function calculateOhc($kbd, $value, $jumlahHc,$provisi)
{
    $totalOhc = 0;
    $personilOhc = 0;
    $ohcItems = DB::table('sl_quotation_ohc')
        ->whereNull('deleted_at')
        ->where('quotation_id', $value->id)
        ->get();

    foreach ($ohcItems as $item) {
        $totalOhc += ($item->harga * $item->jumlah / $jumlahHc * $kbd->jumlah_hc);
        $personilOhc = $totalOhc / $provisi;
    }

    $kbd->list_ohc = $ohcItems;
    $kbd->total_ohc = $totalOhc;
    $kbd->personil_ohc = $personilOhc;
}

private function calculateChemical($kbd, $value, $provisi)
{
    $personilChemical = 0;
    $chemicalItems = DB::table('sl_quotation_chemical')
        ->whereNull('deleted_at')
        ->where('quotation_id', $value->id)
        ->get();

    foreach ($chemicalItems as $item) {
        $personilChemical += ($item->harga * $item->jumlah) / $provisi;
    }

    $kbd->personil_chemical = $personilChemical;
}

private function calculateCoss($kbd, $value, $jumlahHc, $provisi,$quotation)
{
    // Total Base Manpower
    $kbd->total_base_manpower = round($kbd->nominal_upah + $kbd->total_tunjangan, 2);

    // Total Exclude Base Manpower
    $kbd->total_exclude_base_manpower = round(
        $kbd->tunjangan_hari_raya +
        $kbd->kompensasi +
        $kbd->tunjangan_holiday +
        $kbd->lembur +
        $kbd->nominal_takaful +
        $kbd->bpjs_jkk +
        $kbd->bpjs_jkm +
        $kbd->bpjs_jht +
        $kbd->bpjs_jp +
        $kbd->bpjs_kes +
        (ceil($kbd->personil_kaporlap / 1000) * 1000) +
        (ceil($kbd->personil_devices / 1000) * 1000) +
        (ceil($kbd->personil_chemical / 1000) * 1000),
        2
    );

    // Total Personil COSS
    // dd($kbd->total_base_manpower,$kbd->total_exclude_base_manpower,$kbd->personil_ohc,$kbd->biaya_monitoring_kontrol);
    $kbd->total_personil_coss = round($kbd->total_base_manpower + $kbd->total_exclude_base_manpower +$kbd->personil_ohc+$kbd->biaya_monitoring_kontrol, 2);

    // Subtotal Personil COSS
    $kbd->sub_total_personil_coss = round($kbd->total_personil_coss * $kbd->jumlah_hc, 2);

    // Management Fee COSS
    $kbd->management_fee_coss = 0;
    if ($value->management_fee_id == 1 || $value->management_fee_id == 4) {
        $kbd->management_fee_coss = round($kbd->sub_total_personil_coss * $value->persentase / 100, 2);
    } elseif ($value->management_fee_id == 5) {
        $kbd->management_fee_coss = round($kbd->nominal_upah * $value->persentase / 100, 2);
    }

    // // bunga bank dan insentif
    // $pengaliTop = 0;
    // if ($quotation->top == "Kurang Dari 7 Hari") {
    //     $pengaliTop = 7;
    // }else if($quotation->top == "Lebih Dari 7 Hari"){
    //     $pengaliTop = $quotation->jumlah_hari_invoice;
    // };

    // Grand Total COSS
    // $kbd->grand_total_coss = round(
    //     $kbd->sub_total_personil_coss + $kbd->total_ohc + $kbd->management_fee_coss + $kbd->bunga_bank + $kbd->insentif,
    //     2
    // );
    $kbd->grand_total_coss = round(
        // $kbd->sub_total_personil_coss + $kbd->total_ohc + $kbd->management_fee_coss,2);
        $kbd->sub_total_personil_coss + $kbd->management_fee_coss,2);

    // PPN dan PPh COSS
    $kbd->ppn_coss = 0;
    $kbd->pph_coss = 0;
    if ($value->ppn_pph_dipotong == "Management Fee") {
        $kbd->ppn_coss = round($kbd->management_fee_coss * 11 / 100, 2);
        $kbd->pph_coss = round($kbd->management_fee_coss * -2 / 100, 2);
    } elseif ($value->ppn_pph_dipotong == "Total Invoice") {
        $kbd->ppn_coss = round($kbd->grand_total_coss * 11 / 100, 2);
        $kbd->pph_coss = round($kbd->grand_total_coss * -2 / 100, 2);
    }

    // Total Invoice COSS
    $kbd->total_invoice_coss = round($kbd->grand_total_coss + $kbd->ppn_coss + $kbd->pph_coss, 2);

    // Pembulatan COSS
    $kbd->pembulatan_coss = ceil($kbd->total_invoice_coss / 1000) * 1000;
}


}
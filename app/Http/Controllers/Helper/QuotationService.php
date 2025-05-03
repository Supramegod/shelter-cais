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
        if($quotation->durasi_kerjasama == null){
            $provisi = 12;
        }else if (!strpos($quotation->durasi_kerjasama, 'tahun')) {
            $provisi = (int)str_replace(" bulan", "", $quotation->durasi_kerjasama);
        }
        $quotation->provisi = $provisi;

        foreach ($quotation->quotation_detail as $kbd) {
            $hpp = DB::table('sl_quotation_detail_hpp')->whereNull('deleted_at')->where('quotation_detail_id', $kbd->id)->first();
            $coss = DB::table('sl_quotation_detail_coss')->whereNull('deleted_at')->where('quotation_detail_id', $kbd->id)->first();
            $quotationSite = DB::table('sl_quotation_site')->whereNull('deleted_at')->where('id', $kbd->quotation_site_id)->first();

            $nominalUpah = $quotationSite->nominal_upah;
            if($hpp->gaji_pokok !=null){
                $nominalUpah = $hpp->gaji_pokok;
            }
            $kbd->nominal_upah = $nominalUpah;

            $kbd->umk = $quotationSite->umk;
            $kbd->ump = $quotationSite->ump;

            // inisial insentif dan bunga bank
            $kbd->bunga_bank = $hpp->bunga_bank;
            $kbd->insentif = $hpp->insentif;
            if ($kbd->bunga_bank === null) {
                $kbd->bunga_bank = 0;
            }
            if ($kbd->insentif === null) {
                $kbd->insentif = 0;
            }

            $this->processQuotationDetail($kbd, $daftarTunjangan, $quotation, $jumlahHc, $provisi,$hpp,$coss);
        }

        $this->calculateHpp($quotation, $jumlahHc, $provisi);
        $this->calculateCoss($quotation, $jumlahHc, $provisi);


        // recalculating bunga bank dan insentif karena gross up
        $persenBungaBank = $quotation->persen_bunga_bank;
        $persenInsentif = $quotation->persen_insentif;
        $bungaBank = 0;
        $insentif = 0;
        if($persenBungaBank != 0 && $persenBungaBank != null){
            $bungaBank = $quotation->total_sebelum_management_fee * ($quotation->persen_bunga_bank / 100) / $jumlahHc;
        }
        if($persenInsentif != 0 && $persenInsentif != null){
            $insentif = $quotation->nominal_management_fee_coss * ($quotation->persen_insentif / 100) / $jumlahHc;
        }
        // Hitung ulang sub total personil
        foreach ($quotation->quotation_detail as $kbd) {
            $hpp = DB::table('sl_quotation_detail_hpp')->whereNull('deleted_at')->where('quotation_detail_id', $kbd->id)->first();
            $coss = DB::table('sl_quotation_detail_coss')->whereNull('deleted_at')->where('quotation_detail_id', $kbd->id)->first();
            $quotationSite = DB::table('sl_quotation_site')->whereNull('deleted_at')->where('id', $kbd->quotation_site_id)->first();

            $nominalUpah = $quotationSite->nominal_upah;
            if($hpp->gaji_pokok !=null){
                $nominalUpah = $hpp->gaji_pokok;
            }
            $kbd->nominal_upah = $nominalUpah;

            $kbd->umk = $quotationSite->umk;
            $kbd->ump = $quotationSite->ump;

            if ($hpp->bunga_bank === null) {
                $kbd->bunga_bank = $bungaBank;
            }
            if ($hpp->insentif === null) {
                $kbd->insentif = $insentif;
            }
            $this->processQuotationDetail($kbd, $daftarTunjangan, $quotation, $jumlahHc, $provisi,$hpp,$coss);
            $this->calculateHpp($quotation, $jumlahHc, $provisi);
            $this->calculateCoss($quotation, $jumlahHc, $provisi);
        }
        return $quotation;
    }

    private function processQuotationDetail(&$kbd, $daftarTunjangan, $quotation, $jumlahHc, $provisi,$hpp,$coss)
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
        $ump = $kbd->ump;

        $this->calculateBpjs($kbd, $quotation, $umk,$ump,$hpp);
        $this->calculateExtras($kbd, $quotation, $provisi, $jumlahHc,$umk,$hpp);
        // Perhitungan Kaporlap
        $this->calculateKaporlap($kbd, $quotation, $provisi,$hpp,$coss);

        // Perhitungan Devices
        $this->calculateDevices($kbd, $quotation, $provisi, $jumlahHc,$hpp,$coss);

        // Perhitungan OHC
        $this->calculateOhc($kbd, $quotation, $jumlahHc,$provisi,$hpp,$coss);

        // Perhitungan Chemical
        $this->calculateChemical($kbd, $quotation, $provisi,$hpp,$coss,$jumlahHc);

        // hpp
        $kbd->total_personil = $this->calculateTotalPersonnel($kbd, $quotation, $totalTunjangan);
        $kbd->sub_total_personil = $kbd->total_personil * $kbd->jumlah_hc;

        // coss
        // Total Base Manpower
        $kbd->total_base_manpower = round($kbd->nominal_upah + $kbd->total_tunjangan, 2);

        // Total Exclude Base Manpower
        $kbd->total_exclude_base_manpower = round(
            $kbd->tunjangan_hari_raya +
            $kbd->kompensasi +
            $kbd->tunjangan_holiday +
            $kbd->lembur +
            $kbd->nominal_takaful +
            $kbd->bpjs_kesehatan +
            $kbd->bpjs_ketenagakerjaan +
            $kbd->personil_kaporlap_coss+
            $kbd->personil_devices_coss +
            $kbd->personil_chemical_coss,
            2
        );
        // Total Personil COSS
        // dd($kbd->total_base_manpower,$kbd->total_exclude_base_manpower,$kbd->personil_ohc,$kbd->biaya_monitoring_kontrol);
        $kbd->total_personil_coss = round($kbd->total_base_manpower + $kbd->total_exclude_base_manpower +$kbd->personil_ohc_coss, 2);
        // Subtotal Personil COSS
        $kbd->sub_total_personil_coss = round($kbd->total_personil_coss * $kbd->jumlah_hc, 2);

    }

    private function calculateBpjs(&$kbd, $quotation, $umk,$ump,$hpp)
    {        // Inisialisasi default
        // $kbd->nominal_takaful = $hpp->takaful;
        $kbd->bpjs_jkm = $hpp->bpjs_jkm;
        $kbd->bpjs_jkk = $hpp->bpjs_jkk;
        $kbd->bpjs_jht = $hpp->bpjs_jht;
        $kbd->bpjs_jp = $hpp->bpjs_jp;
        $kbd->bpjs_kes = $hpp->bpjs_ks;
        $kbd->persen_bpjs_jkm = null;
        $kbd->persen_bpjs_jkk = null;
        $kbd->persen_bpjs_jht = null;
        $kbd->persen_bpjs_jp = null;
        $kbd->persen_bpjs_kes = null;

        // if($kbd->nominal_takaful=== null){
        //     $kbd->nominal_takaful = $quotation->nominal_takaful;
        // }
        $upahBpjs = 0;
        // Case 1 Gaji diatas UMK
        if($kbd->nominal_upah > $umk){
            $upahBpjs = $kbd->nominal_upah;
        }
        // Case 2 Gaji = umk
        else if ($kbd->nominal_upah == $umk) {
            $upahBpjs = $umk;
        }
        // Case 3 Gaji dibawah UMK tapi lebih dari atau sama dengan ump
        else if ($kbd->nominal_upah < $umk && $kbd->nominal_upah >= $ump) {
            $upahBpjs = $kbd->nominal_upah;
        }
        // Case 4 Gaji dibawah umk dan ump
        else if ($kbd->nominal_upah < $ump) {
            $upahBpjs = $ump;
        }

        $upahBpjsKes = $umk;
        // $upahBpjs = $kbd->nominal_upah < $umk ? $umk : $kbd->nominal_upah;
        // $upahBpjsKes = $kbd->nominal_upah;
        // if($kbd->nominal_upah < $umk){
        //     $upahBpjsKes = $umk;
        // }
        // if($umk==null || $umk==0){
        //     $umk = $kbd->nominal_upah;
        // }

        // Hitung JKK berdasarkan resiko
        if ($kbd->bpjs_jkk === null) {
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
        }

        // Hitung JKM
        if ($kbd->bpjs_jkm === null) {
            $kbd->bpjs_jkm = $upahBpjs * 0.3 / 100;
            $kbd->persen_bpjs_jkm = 0.3;
        }

        // Hitung JHT (jika program BPJS mencakup JHT)
        if($kbd->bpjs_jht=== null){
            $kbd->bpjs_jht = $upahBpjs * 3.7 / 100;
            $kbd->persen_bpjs_jht = 3.7;
        }

        // Hitung JP (jika program BPJS mencakup JP)
        if ($kbd->bpjs_jp === null) {
            $kbd->bpjs_jp = $upahBpjs * 2 / 100;
            $kbd->persen_bpjs_jp = 2;
        }

        // Hitung BPJS Kesehatan berdasarkan UMK
        if ($kbd->bpjs_kes === null) {
            $kbd->bpjs_kes = $upahBpjsKes * 4 / 100;
            $kbd->persen_bpjs_kes = 4;
        }

        if($kbd->is_bpjs_jkk=="0"){
            $kbd->bpjs_jkk = 0;
            $kbd->persen_bpjs_jkk = 0;
        }
        if ($kbd->is_bpjs_jkm == "0") {
            $kbd->bpjs_jkm = 0;
            $kbd->persen_bpjs_jkm = 0;
        }
        if ($kbd->is_bpjs_jht == "0") {
            $kbd->bpjs_jht = 0;
            $kbd->persen_bpjs_jht = 0;
        }
        if ($kbd->is_bpjs_jp == "0") {
            $kbd->bpjs_jp = 0;
            $kbd->persen_bpjs_jp = 0;
        }
        // if ($kbd->is_bpjs_kes == "0") {
        //     $kbd->bpjs_kes = 0;
        //     $kbd->persen_bpjs_kes = 0;
        // }

        $kbd->persen_bpjs_ketenagakerjaan = $kbd->persen_bpjs_jkk+$kbd->persen_bpjs_jkm+$kbd->persen_bpjs_jht+$kbd->persen_bpjs_jp;
        $kbd->bpjs_ketenagakerjaan = $kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp;
        $quotation->persen_bpjs_ketenagakerjaan = $kbd->persen_bpjs_ketenagakerjaan;
        $quotation->penjamin = $kbd->penjamin_kesehatan;

        if($kbd->penjamin_kesehatan=="BPJS"){
            $kbd->bpjs_kesehatan = $kbd->bpjs_kes;
            $kbd->persen_bpjs_kesehatan = $kbd->persen_bpjs_kes;
            $quotation->persen_bpjs_kesehatan = $kbd->persen_bpjs_kesehatan;
        }else{
            $kbd->bpjs_kesehatan = 0;
            $kbd->persen_bpjs_kesehatan = 0;
            $quotation->persen_bpjs_kesehatan = 0;
            // dd($kbd->bpjs_jkk,$kbd->bpjs_jkm,$kbd->bpjs_jht,$kbd->bpjs_jp,$kbd->bpjs_kes);
        };
    }

    private function calculateExtras(&$kbd, $quotation, $provisi, $jumlahHc,$umk,$hpp)
    {
        // Tambahkan perhitungan kompensasi, tunjangan, dll.
        // THR
        $kbd->tunjangan_hari_raya = $hpp->tunjangan_hari_raya;
        if ($kbd->tunjangan_hari_raya === null) {
            if ($quotation->thr == "Diprovisikan") {
                $kbd->tunjangan_hari_raya = $kbd->nominal_upah / $provisi;
            }
        }

        // Kompensasi
        $kbd->kompensasi = $hpp->kompensasi;
        if ($quotation->kompensasi == "Diprovisikan") {
            if ($kbd->kompensasi === null) {
                $kbd->kompensasi = $kbd->nominal_upah / $provisi;
            }
        }

        // Tunjangan Holiday
        $kbd->tunjangan_holiday = 0;
        $quotation->tunjangan_holiday_display = 0;
        if ($quotation->tunjangan_holiday == "Flat") {
            if ($hpp->tunjangan_hari_libur_nasional !== null) {
                $kbd->tunjangan_holiday = $hpp->tunjangan_hari_libur_nasional;
            }else{
                $kbd->tunjangan_holiday = $quotation->nominal_tunjangan_holiday;
            }
        } else {
            $quotation->tunjangan_holiday_display = ($umk / 173 * 14) * 1.5;
        }

        // Lembur
        $kbd->lembur = 0;
        $quotation->lembur_per_jam = 0;
        if ($quotation->lembur == "Flat") {
            if ($hpp->lembur !== null) {
                $kbd->lembur = $hpp->lembur;
            }else{
                $kbd->lembur = $quotation->nominal_lembur;
            }
            $quotation->lembur_per_jam = null;
            $quotation->nominal_lembur = $kbd->lembur;
        } else {
            $quotation->lembur_per_jam = ($umk / 173 * 1.5) * 1;
        }

        if($quotation->lembur_ditagihkan == "Ditagihkan Terpisah"){
            $kbd->lembur = 0;
        }

        $quotation->nominal_lembur = $kbd->lembur;
    }

    private function calculateTotalPersonnel($kbd, $quotation, $totalTunjangan)
    {
        // Hitung total personal seperti pada kode
        // return $kbd->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_chemical+$kbd->personil_ohc+$kbd->bunga_bank+$kbd->insentif;
                return $kbd->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_ketenagakerjaan+$kbd->bpjs_kesehatan+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_chemical+$kbd->personil_ohc+$kbd->bunga_bank+$kbd->insentif;
    }

    private function calculateTaxes(&$kbd, $quotation)
    {
        // PPN dan PPh default
        $kbd->ppn = 0;
        $kbd->pph = 0;

        if ($quotation->ppn_pph_dipotong == "Management Fee") {
            $kbd->ppn = $kbd->management_fee * 11/12*12/100;
            $kbd->pph = $kbd->management_fee * -2 / 100;
        } elseif ($quotation->ppn_pph_dipotong == "Total Invoice") {
            $kbd->ppn = $kbd->grand_total * 11/12*12/100;
            $kbd->pph = $kbd->grand_total * -2 / 100;
        }
    }

    private function calculateKaporlap($kbd, $value, $provisi,$hpp,$coss)
    {
        $personilKaporlap = $hpp->provisi_seragam;
        $personilKaporlapCoss = $coss->provisi_seragam;

        if ($personilKaporlap === null) {
            $kaporlapItems = DB::table('sl_quotation_kaporlap')
            ->whereNull('deleted_at')
            ->where('quotation_id', $value->id)
            ->where('quotation_detail_id', $kbd->id)
            ->get();
            foreach ($kaporlapItems as $item) {
                $personilKaporlap += ($item->harga * $item->jumlah) / $provisi;
            }
        }
        $kbd->personil_kaporlap = $personilKaporlap;

        if ($personilKaporlapCoss === null) {
            $personilKaporlapCoss = $personilKaporlap;
        }

        $kbd->personil_kaporlap_coss = $personilKaporlapCoss;
    }

    private function calculateDevices($kbd, $value, $provisi, $jumlahHc,$hpp,$coss)
    {
        $personilDevices = $hpp->provisi_peralatan;
        $personilDevicesCoss = $coss->provisi_peralatan;

        if($personilDevices === null){
            $deviceItems = DB::table('sl_quotation_devices')
            ->whereNull('deleted_at')
            ->where('quotation_id', $value->id)
            ->get();

            foreach ($deviceItems as $item) {
                $personilDevices += ($item->harga * $item->jumlah / $jumlahHc) / $provisi;
            }
        }
        $kbd->personil_devices = $personilDevices;

        if ($personilDevicesCoss === null) {
            $personilDevicesCoss = $personilDevices;
        }

        $kbd->personil_devices_coss = $personilDevicesCoss;

    }

    private function calculateOhc($kbd, $value, $jumlahHc,$provisi,$hpp,$coss)
    {
        $personilOhc = $hpp->provisi_ohc;
        $personilOhcCoss = $coss->provisi_ohc;

        if ($personilOhc === null) {
            $ohcItems = DB::table('sl_quotation_ohc')
            ->whereNull('deleted_at')
            ->where('quotation_id', $value->id)
            ->get();

            foreach ($ohcItems as $item) {
                $personilOhc += (($item->harga * $item->jumlah) / $provisi) / $jumlahHc;
            }
        }
        $kbd->personil_ohc = $personilOhc;

        if ($personilOhcCoss === null) {
            $personilOhcCoss = $personilOhc;
        }
        $kbd->personil_ohc_coss = $personilOhcCoss;
    }

    private function calculateChemical($kbd, $value, $provisi,$hpp,$coss,$jumlahHc)
    {
        $personilChemical = $hpp->provisi_chemical;
        $personilChemicalCoss = $coss->provisi_chemical;

        if ($personilChemical === null) {
            $chemicalItems = DB::table('sl_quotation_chemical')
            ->whereNull('deleted_at')
            ->where('quotation_id', $value->id)
            ->get();

            foreach ($chemicalItems as $item) {
                // $personilChemical += ($item->harga * $item->jumlah) / $provisi;+
                $personilChemical += ((($item->jumlah * $item->harga) / $item->masa_pakai)) / $jumlahHc;
            }
        }
        $kbd->personil_chemical = $personilChemical;

        if ($personilChemicalCoss === null) {
            $personilChemicalCoss = $personilChemical;
        }

        $kbd->personil_chemical_coss = $personilChemicalCoss;
    }

    private function calculateCoss(&$quotation, $jumlahHc, $provisi)
    {
        $quotation->total_sebelum_management_fee_coss = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_sebelum_management_fee_coss += $kbd->sub_total_personil_coss;
        }
        $quotation->total_base_manpower_coss = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_base_manpower_coss += ($kbd->total_base_manpower*$kbd->jumlah_hc);
        }

        $quotation->upah_pokok_coss = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->upah_pokok_coss += ($kbd->nominal_upah*$kbd->jumlah_hc);
        }

        $quotation->total_bpjs_coss = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_bpjs_coss += ($kbd->bpjs_ketenagakerjaan*$kbd->jumlah_hc);
        }
        $quotation->total_bpjs_kesehatan_coss = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_bpjs_kesehatan_coss += ($kbd->bpjs_kesehatan*$kbd->jumlah_hc);
            $quotation->total_bpjs_kesehatan_coss += ($kbd->nominal_takaful*$kbd->jumlah_hc);
        }


        $quotation->nominal_management_fee_coss = 0;
        if($quotation->management_fee_id==1){
            $quotation->nominal_management_fee_coss = $quotation->total_base_manpower_coss * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==4){
            $quotation->nominal_management_fee_coss = $quotation->total_sebelum_management_fee_coss * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==5){
            // dd($quotation->upah_pokok_coss,$quotation->persentase,$quotation->upah_pokok_coss * $quotation->persentase / 100);
            $quotation->nominal_management_fee_coss = $quotation->upah_pokok_coss * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==6){
            $quotation->nominal_management_fee_coss = ($quotation->upah_pokok_coss+$quotation->total_bpjs_coss) * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==7){
            $quotation->nominal_management_fee_coss = ($quotation->upah_pokok_coss+$quotation->total_bpjs_coss+$quotation->total_bpjs_kesehatan_coss) * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==8){
            $quotation->nominal_management_fee_coss = ($quotation->upah_pokok_coss+$quotation->total_bpjs_kesehatan_coss) * $quotation->persentase / 100;
        }

        $quotation->grand_total_sebelum_pajak_coss = $quotation->total_sebelum_management_fee_coss + $quotation->nominal_management_fee_coss;
        $quotation->dpp_coss = 11/12 * $quotation->nominal_management_fee_coss;
        $quotation->ppn_coss = $quotation->dpp_coss*12/100;
        $quotation->pph_coss = $quotation->nominal_management_fee_coss * -2 / 100;
        if($quotation->is_ppn == 1){
            $quotation->total_invoice_coss = $quotation->grand_total_sebelum_pajak_coss + $quotation->ppn_coss + $quotation->pph_coss;
        }else{
            $quotation->total_invoice_coss = $quotation->grand_total_sebelum_pajak_coss;
        }
        $quotation->pembulatan_coss = ceil($quotation->total_invoice_coss / 1000) * 1000;

        $quotation->margin_coss = $quotation->grand_total_sebelum_pajak_coss-$quotation->total_sebelum_management_fee;
        $quotation->gpm_coss = $quotation->margin_coss/$quotation->grand_total_sebelum_pajak_coss*100;
        // bunga bank dan insentif
        // $pengaliTop = 0;
        // if ($quotation->top == "Kurang Dari 7 Hari") {
        //     $pengaliTop = 7;
        // }else if($quotation->top == "Lebih Dari 7 Hari"){
        //     $pengaliTop = $quotation->jumlah_hari_invoice;
        // };
        // return $quotation;
    }
    private function calculateHpp(&$quotation, $jumlahHc, $provisi){
        $quotation->total_sebelum_management_fee = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_sebelum_management_fee += ($kbd->sub_total_personil);
        }
        $quotation->total_base_manpower = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_base_manpower += ($kbd->total_base_manpower*$kbd->jumlah_hc);
        }

        $quotation->upah_pokok = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->upah_pokok += ($kbd->nominal_upah*$kbd->jumlah_hc);
        }
        $quotation->total_bpjs = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_bpjs += ($kbd->bpjs_ketenagakerjaan*$kbd->jumlah_hc);
        }
        $quotation->total_bpjs_kesehatan = 0;
        foreach ($quotation->quotation_detail as $key => $kbd) {
            $quotation->total_bpjs_kesehatan += ($kbd->bpjs_kesehatan*$kbd->jumlah_hc);
            $quotation->total_bpjs_kesehatan += ($kbd->nominal_takaful*$kbd->jumlah_hc);
        }

        $quotation->nominal_management_fee = 0;
        if($quotation->management_fee_id==1){
            $quotation->nominal_management_fee = $quotation->total_base_manpower * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==4){
            $quotation->nominal_management_fee = $quotation->total_sebelum_management_fee * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==5){
            $quotation->nominal_management_fee = $quotation->upah_pokok * $quotation->persentase / 100;
            // $kbd->management_fee = $quotation->nominal_upah*$quotation->persentase/100;
        }else if($quotation->management_fee_id==6){
            $quotation->nominal_management_fee = ($quotation->upah_pokok+$quotation->total_bpjs) * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==7){
            $quotation->nominal_management_fee = ($quotation->upah_pokok+$quotation->total_bpjs+$quotation->total_bpjs_kesehatan) * $quotation->persentase / 100;
        }else if($quotation->management_fee_id==8){
            $quotation->nominal_management_fee = ($quotation->upah_pokok+$quotation->total_bpjs_kesehatan) * $quotation->persentase / 100;
        }

        $quotation->grand_total_sebelum_pajak = $quotation->total_sebelum_management_fee + $quotation->nominal_management_fee;
        $quotation->dpp = 11/12 * $quotation->nominal_management_fee;
        $quotation->ppn = $quotation->dpp *12/100;
        $quotation->pph = $quotation->nominal_management_fee * -2 / 100;
        if($quotation->is_ppn == 1){
            $quotation->total_invoice = $quotation->grand_total_sebelum_pajak + $quotation->ppn + $quotation->pph;
        }else{
            $quotation->total_invoice = $quotation->grand_total_sebelum_pajak;
        }
        $quotation->total_invoice = $quotation->grand_total_sebelum_pajak + $quotation->ppn + $quotation->pph;
        $quotation->pembulatan = ceil($quotation->total_invoice / 1000) * 1000;

        $quotation->margin = $quotation->grand_total_sebelum_pajak-$quotation->total_sebelum_management_fee;
        $quotation->gpm = $quotation->margin/$quotation->grand_total_sebelum_pajak*100;

        // bunga bank dan insentif
        // $pengaliTop = 0;
        // if ($quotation->top == "Kurang Dari 7 Hari") {
        //     $pengaliTop = 7;
        // }else if($quotation->top == "Lebih Dari 7 Hari"){
        //     $pengaliTop = $quotation->jumlah_hari_invoice;
        // };
        // return $quotation;
    }
}

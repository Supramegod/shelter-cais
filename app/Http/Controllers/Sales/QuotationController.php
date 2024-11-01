<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use \stdClass;
use App\Exports\LeadsTemplateExport;
use App\Exports\LeadsExport;
use Illuminate\Support\Facades\Storage;


class QuotationController extends Controller
{
    public function index (Request $request){
        $tglDari = $request->tgl_dari;
        $tglSampai = $request->tgl_sampai;

        if($tglDari==null){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
        }
        if($tglSampai==null){
            $tglSampai = carbon::now()->toDateString();
        }

        $ctglDari = Carbon::createFromFormat('Y-m-d',  $tglDari);
        $ctglSampai = Carbon::createFromFormat('Y-m-d',  $tglSampai);
        

        $branch = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();
        $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
        $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();

        $error = null;
        $success = null;
        if($ctglDari->gt($ctglSampai)){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        };
        if($ctglSampai->lt($ctglDari)){
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }
        return view('sales.quotation.list',compact('branch','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            return view('sales.quotation.add',compact('now'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function cetakChecklist (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
            $quotationKebutuhan = 
            DB::table("sl_quotation_kebutuhan") 
            ->join('m_kebutuhan','m_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id')
            ->whereNull('sl_quotation_kebutuhan.deleted_at')
            ->where('sl_quotation_kebutuhan.id',$id)
            ->orderBy('sl_quotation_kebutuhan.kebutuhan_id','ASC')
            ->select('sl_quotation_kebutuhan.nominal_takaful','sl_quotation_kebutuhan.penjamin','sl_quotation_kebutuhan.quotation_id','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.nomor','sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.nominal_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            $quotation = DB::table("sl_quotation")->where('id',$quotationKebutuhan[0]->quotation_id)->first();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$value->kebutuhan_id)->whereNull('deleted_at')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            $quotation->mulai_kontrak = Carbon::parse($quotation->mulai_kontrak)->format('d F Y');
            $quotation->kontrak_selesai = Carbon::parse($quotation->kontrak_selesai)->format('d F Y');
            $quotation->tgl_quotation = Carbon::parse($quotation->tgl_quotation)->format('d F Y');
            $quotation->tgl_penempatan = Carbon::parse($quotation->tgl_penempatan)->format('d F Y');

            $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();
            $sPersonil = "";
            foreach ($quotationKebutuhan as $iqk => $qk) {
                $jPersonil = DB::select("SELECT sum(jumlah_hc) as jumlah_hc FROM sl_quotation_kebutuhan_detail WHERE quotation_kebutuhan_id = $qk->id and deleted_at is null;");
                if($jPersonil!=null){
                    if ($jPersonil[0]->jumlah_hc!=null && $jPersonil[0]->jumlah_hc!=0) {
                        $sPersonil .= $jPersonil[0]->jumlah_hc." Manpower (";
                        $detailPersonil = DB::table('sl_quotation_kebutuhan_detail')
                        ->join('m_kebutuhan_detail','m_kebutuhan_detail.id','sl_quotation_kebutuhan_detail.kebutuhan_detail_id')
                        ->whereNull('sl_quotation_kebutuhan_detail.deleted_at')->where('sl_quotation_kebutuhan_detail.quotation_kebutuhan_id',$qk->id)
                        ->orderBy('m_kebutuhan_detail.urutan','ASC')
                        ->get();
                        foreach ($detailPersonil as $idp => $vdp) {
                            if($idp !=0){
                                $sPersonil .= ", ";
                            }
                            $sPersonil .= $vdp->jumlah_hc." ".$vdp->jabatan_kebutuhan;
                        }

                        $sPersonil .= " )";
                    }else{
                        $sPersonil = "-";
                    }
                }else{
                    $sPersonil = "-";
                }

                $qk->jumlah_personel = $sPersonil;

            }
            $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();

            $listPic = DB::table('sl_quotation_pic')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
            return view('sales.quotation.cetakan.checklist',compact('listPic','listTraining','listTrainingQ','salaryRuleQ','salaryRule','leads','quotation','quotationKebutuhan','now'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function step(Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            $quotationKebutuhan = 
            DB::table("sl_quotation_kebutuhan")
            ->join('m_kebutuhan','m_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id')
            ->whereNull('sl_quotation_kebutuhan.deleted_at')
            ->where('sl_quotation_kebutuhan.quotation_id',$request->id)
            ->orderBy('sl_quotation_kebutuhan.kebutuhan_id','ASC')
            ->select('sl_quotation_kebutuhan.is_aktif','sl_quotation_kebutuhan.nominal_takaful','sl_quotation_kebutuhan.penjamin','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.nomor','sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.nominal_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$value->kebutuhan_id)->orderBy('name','asc')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            $province = DB::connection('mysqlhris')->table('m_province')->get();
            foreach ($province as $key => $value) {
                $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('province_id',$value->id)->first();
                $value->ump = "Rp. 0";
                if($dataUmp !=null){
                    $value->ump = "Rp. ".number_format($dataUmp->ump,0,",",".");
                }
            }
            $kota = DB::connection('mysqlhris')->table('m_city')->get();
            $manfee = DB::table('m_management_fee')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
           
            //step 6 - aplikasi pendukung
            $aplikasiPendukung = null;
            $arrAplikasiSel = [];
            if($request->step==6){
                $aplikasiPendukung = DB::table('m_aplikasi_pendukung')->whereNull('deleted_at')->get();
                $listApp = DB::table('sl_quotation_kebutuhan_aplikasi')->where('quotation_id',$id)->whereNull('deleted_at')->get();

                foreach ($listApp as $key => $value) {
                    array_push($arrAplikasiSel,$value->aplikasi_pendukung_id);
                }
            }

            //step 7 - kaporlap
            $listKaporlap = null;
            $listJenis = [];
            if($request->step==7){
                $arrKaporlap = [1,2,3,4,5];
                if($quotationKebutuhan[0]->kebutuhan_id != 1){
                    $arrKaporlap = [5];
                }

                $listJenis = DB::table('m_jenis_barang')->whereIn('id',$arrKaporlap)->get();
                $listKaporlap = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',$arrKaporlap)
                                    ->orderBy("nama","asc")
                                    ->get();

                foreach ($listKaporlap as $key => $kaporlap) {
                    foreach ($quotationKebutuhan[0]->kebutuhan_detail as $kKd => $vKd) {
                        $kaporlap->{'jumlah_'.$vKd->id} = 0;
                        $kebkap = DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('barang_id',$kaporlap->id)->where('quotation_kebutuhan_detail_id',$vKd->id)->first();
                        if($kebkap !=null){
                            $kaporlap->{'jumlah_'.$vKd->id} = $kebkap->jumlah;
                        }
                    }
                }
            }

            //step 8 ohc
            $listOhc = null;
            if($request->step==8){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[6,7,8])->get();
                $listOhc = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[6,7,8])
                                    ->get();
                foreach ($listOhc as $key => $value) {
                    $value->harga = number_format($value->harga,0,",",".");
                }
            }

            //step 9 - devices
            $listDevices = null;
            if($request->step==9){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[9,10,11,12,17])->get();
                $listDevices = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[8,9,10,11,12,17])
                                    ->get();

                foreach ($listDevices as $key => $devices) {
                    if($devices->jenis_barang_id==17){
                        $cekExist = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('aplikasi_pendukung',$devices->nama)->where('quotation_id',$id)->first();
                        if($cekExist != null){
                            $devices->{'jumlah'} = 1;
                        }else{
                            $devices->{'jumlah'} = 0;
                        }
                    }else{
                        foreach ($quotationKebutuhan[0]->kebutuhan_detail as $kKd => $vKd) {
                            $devices->{'jumlah_'.$vKd->id} = 0;
                            $kebkap = DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('barang_id',$devices->id)->where('quotation_kebutuhan_detail_id',$vKd->id)->first();
                            if($kebkap !=null){
                                $devices->{'jumlah_'.$vKd->id} = $kebkap->jumlah;
                            }
                        }
                    }
                    
                }
            }

            //step 10 - chemical
            $listChemical = null;
            if($request->step==10){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[13,14,15,16])->get();
                $listChemical = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[13,14,15,16])
                                    ->get();
                foreach ($listChemical as $key => $value) {
                    $value->harga = number_format($value->harga,0,",",".");
                }
            }

            // step 11 cost structure
            $leads = null;
            $data = null;
            $daftarTunjangan = null;
            $listTraining = [];
            $listTrainingQ = [];

            if($request->step==11){
                $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama,nominal FROM `sl_quotation_kebutuhan_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");

                $jumlahHc = 0;
                foreach ($quotationKebutuhan[0]->kebutuhan_detail as $jhc) {
                    $jumlahHc += $jhc->jumlah_hc;
                }

                $provisi = 12;
                if(!strpos($quotation->durasi_kerjasama, 'tahun')){
                    $provisi = (int)str_replace(" bulan", "", $quotation->durasi_kerjasama);
                }
                $quotation->provisi = $provisi;

                foreach ($quotationKebutuhan[0]->kebutuhan_detail as $ikbd => $kbd) {
                    // $kbd->daftar_tunjangan = [];
                    $totalTunjangan = 0;
                    foreach ($daftarTunjangan as $idt => $tunjangan) {
                        $kbd->{$tunjangan->nama} = 0;
                        //cari data tunjangan
                        $dtTunjangan = DB::table('sl_quotation_kebutuhan_detail_tunjangan')->whereNull('deleted_at')->where('quotation_kebutuhan_detail_id',$kbd->id)->where('nama_tunjangan',$tunjangan->nama)->first();
                        if($dtTunjangan != null){
                            $kbd->{$tunjangan->nama} = $dtTunjangan->nominal;

                            $totalTunjangan += $dtTunjangan->nominal;
                        }
                    }

                    $kbd->nominal_takaful = 0;
                    $kbd->bpjs_jkm = 0;
                    $kbd->bpjs_jkk = 0;
                    $kbd->bpjs_jht = 0;
                    $kbd->bpjs_jp = 0;
                    $kbd->bpjs_kes = 0;

                    if($quotationKebutuhan[0]->penjamin=="Takaful"){
                        $kbd->nominal_takaful = $quotationKebutuhan[0]->nominal_takaful;
                    }else{
                        // hitung JKK
                        if($quotationKebutuhan[0]->resiko=="Sangat Rendah"){
                            $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.24/100;
                        }else if($quotationKebutuhan[0]->resiko=="Rendah"){
                            $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.54/100;
                        }else if($quotationKebutuhan[0]->resiko=="Sedang"){
                            $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.89/100;
                        }else if($quotationKebutuhan[0]->resiko=="Tinggi"){
                            $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.27/100;
                        }else if($quotationKebutuhan[0]->resiko=="Sangat Tinggi"){
                            $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.74/100;
                        };

                        //hitung JKM
                        $kbd->bpjs_jkm = $quotationKebutuhan[0]->nominal_upah*0.3/100;

                        //Hitung JHT
                        if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                            $kbd->bpjs_jht = $quotationKebutuhan[0]->nominal_upah*3.7/100;
                        }else{
                            $kbd->bpjs_jht = 0;
                        }

                        //Hitung JP
                        if($quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                            $kbd->bpjs_jp = $quotationKebutuhan[0]->nominal_upah*2/100;
                        }else {
                            $kbd->bpjs_jp = 0;
                        }

                        $kbd->bpjs_kes = $quotationKebutuhan[0]->nominal_upah*4/100;

                    }

                    $kbd->tunjangan_hari_raya = 0;
                    if($quotation->thr=="Diprovisikan"){
                        $kbd->tunjangan_hari_raya = $quotationKebutuhan[0]->nominal_upah/$provisi;
                    }

                    $kbd->kompensasi = 0;
                    if($quotation->kompensasi=="Diprovisikan"){
                        $kbd->kompensasi = $quotationKebutuhan[0]->nominal_upah/$provisi;
                    }
                    
                    $kbd->tunjangan_holiday = 0;
                    if($quotation->tunjangan_holiday=="Flat"){
                        $kbd->tunjangan_holiday = $quotation->nominal_tunjangan_holiday;
                    }else{
                        $kbd->tunjangan_holiday = ($quotationKebutuhan[0]->nominal_upah/173*(14))*15/$provisi;
                    }

                    $kbd->lembur = 0;
                    if($quotation->lembur=="Flat"){
                        $kbd->lembur = $quotation->nominal_lembur;
                    }else{
                        $kbd->lembur = 0;
                    }

                    $personilKaporlap = 0;
                    $kbdkaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                    foreach ($kbdkaporlap as $ikdbkap => $kdbkap) {
                        $personilKaporlap += ($kdbkap->harga*$kdbkap->jumlah)/$provisi;
                    };

                    $kbd->personil_kaporlap = $personilKaporlap;

                    $personilDevices = 0;
                    $kbddevices = DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                    foreach ($kbddevices as $ikdbdev => $kdbdev) {
                        $personilDevices += ($kdbdev->harga*$kdbdev->jumlah)/$provisi;
                    };
                    $appPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                    foreach ($appPendukung as $kapp => $app) {
                        $personilDevices += ($app->harga*1)/$provisi;
                    }

                    $kbd->personil_devices = $personilDevices;

                    $personilOhc = 0;
                    $kbdOhc = DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                    foreach ($kbdOhc as $ikdbohc => $kdbohc) {
                        $personilOhc += ($kdbohc->harga*$kdbohc->jumlah/$jumlahHc)/$provisi;
                    };

                    $kbd->personil_ohc = $personilOhc;

                    $personilChemical = 0;
                    $kbdChemical = DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                    foreach ($kbdChemical as $ikdbchemical => $kdbchemical) {
                        $personilChemical += ($kdbchemical->harga*$kdbchemical->jumlah)/$provisi;
                    };

                    $kbd->personil_chemical = $personilChemical;

                    // dd($kbd->personil_kaporlap);
                    $kbd->total_personil = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;

                    $kbd->sub_total_personil = $kbd->total_personil*$kbd->jumlah_hc;
                    
                    $kbd->management_fee = $kbd->sub_total_personil*$quotationKebutuhan[0]->persentase/100;

                    $kbd->grand_total = $kbd->sub_total_personil+$kbd->management_fee;

                    $kbd->ppn = 0;
                    $kbd->pph = 0;
                    if ($quotation->ppn_pph_dipotong=="Management Fee") {
                        $kbd->ppn = $kbd->management_fee*11/100;
                        $kbd->pph = $kbd->management_fee*(-2/100);
                    }else if ($quotation->ppn_pph_dipotong=="Total Invoice") {
                        $kbd->ppn = $kbd->grand_total*11/100;
                        $kbd->pph = $kbd->grand_total*(-2/100);
                    }
                    
                    $kbd->total_invoice = $kbd->grand_total + $kbd->ppn + $kbd->pph;

                    

                    $kbd->pembulatan = ceil($kbd->total_invoice / 1000) * 1000;

                    // COST STRUCTURE
                    $kbd->total_base_manpower = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan;
                    $kbd->total_exclude_base_manpower = $kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;;

                    $kbd->total_personil_coss = $kbd->total_base_manpower + $kbd->total_exclude_base_manpower + $kbd->biaya_monitoring_kontrol;

                    $kbd->sub_total_personil_coss = $kbd->total_personil_coss*$kbd->jumlah_hc;
                    
                    $kbd->management_fee_coss = $kbd->sub_total_personil_coss*$quotationKebutuhan[0]->persentase/100;

                    $kbd->grand_total_coss = $kbd->sub_total_personil_coss+$kbd->management_fee_coss;
                    
                    $kbd->ppn_coss = 0;
                    $kbd->pph_coss = 0;
                    if($quotation->ppn_pph_dipotong =="Management Fee"){
                        $kbd->ppn_coss = $kbd->management_fee_coss*11/100;
                        $kbd->pph_coss = $kbd->management_fee_coss*(-2/100);
                    }else  if($quotation->ppn_pph_dipotong =="Total Invoice"){
                        $kbd->ppn_coss = $kbd->grand_total_coss*11/100;
                        $kbd->pph_coss = $kbd->grand_total_coss*(-2/100);
                    }

                    $kbd->total_invoice_coss = $kbd->grand_total_coss + $kbd->ppn_coss + $kbd->pph_coss;

                    $kbd->pembulatan_coss = ceil($kbd->total_invoice_coss / 1000) * 1000;

                };

                $data = DB::table('sl_quotation_kebutuhan')->where('id',$quotationKebutuhan[0]->id)->first();
                $data->detail = DB::table('sl_quotation_kebutuhan_detail')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                $data->totalHc = 0;

                foreach ($data->detail as $key => $value) {
                    $data->totalHc += $value->jumlah_hc;
                }
                $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();         
            }

            $salaryRuleQ = null;
            $listJabatanPic = null;

            if($request->step==12){
                $listJabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
                $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$id)->whereNull('deleted_at')->get();
                $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
                $quotation->mulai_kontrak = Carbon::parse($quotation->mulai_kontrak)->format('d F Y');
                $quotation->kontrak_selesai = Carbon::parse($quotation->kontrak_selesai)->format('d F Y');
                $quotation->tgl_quotation = Carbon::parse($quotation->tgl_quotation)->format('d F Y');
                $quotation->tgl_penempatan = Carbon::parse($quotation->tgl_penempatan)->format('d F Y');

                $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
                $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();
                $sPersonil = "";
                foreach ($quotationKebutuhan as $iqk => $qk) {
                    $jPersonil = DB::select("SELECT sum(jumlah_hc) as jumlah_hc FROM sl_quotation_kebutuhan_detail WHERE quotation_kebutuhan_id = $qk->id and deleted_at is null;");
                    if($jPersonil!=null){
                        if ($jPersonil[0]->jumlah_hc!=null && $jPersonil[0]->jumlah_hc!=0) {
                            $sPersonil .= $jPersonil[0]->jumlah_hc." Manpower (";
                            $detailPersonil = DB::table('sl_quotation_kebutuhan_detail')
                            ->whereNull('sl_quotation_kebutuhan_detail.deleted_at')
                            ->where('sl_quotation_kebutuhan_detail.quotation_kebutuhan_id',$qk->id)
                            ->get();
                            foreach ($detailPersonil as $idp => $vdp) {
                                if($idp !=0){
                                    $sPersonil .= ", ";
                                }
                                $sPersonil .= $vdp->jumlah_hc." ".$vdp->jabatan_kebutuhan;
                            }

                            $sPersonil .= " )";
                        }else{
                            $sPersonil = "-";
                        }
                    }else{
                        $sPersonil = "-";
                    }
                    $qk->jumlah_personel = $sPersonil;
                }
            }

            $isEdit = false;

            if(isset($request->edit)){
                $isEdit = true;
            }
            return view('sales.quotation.edit-'.$request->step,compact('listJabatanPic','listTrainingQ','listTraining','daftarTunjangan','salaryRuleQ','data','leads','isEdit','listChemical','listDevices','listOhc','listJenis','listKaporlap','jenisPerusahaan','aplikasiPendukung','arrAplikasiSel','manfee','kota','province','quotation','request','company','salaryRule','quotationKebutuhan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view (Request $request,$id){
        try {
            $data = DB::table('sl_quotation_kebutuhan')->where('id',$id)->first();
            $data->detail = DB::table('sl_quotation_kebutuhan_detail')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$id)->get();
            $data->totalHc = 0;
            $data->umk = 0;
            $data->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$data->quotation_id)->first();
            $data->pks = DB::table('sl_pks')->whereNull('deleted_at')->where('quotation_id',$data->quotation_id)->first();

            foreach ($data->detail as $key => $value) {
                $data->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($data->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->whereNull('deleted_at')->where('city_id',$data->kota_id)->first();

                if($dataUmk!=null){
                    $data->umk = $dataUmk->umk;
                }
            }
            
            $master = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
            $quotation = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');

            //format
            $master->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$master->mulai_kontrak)->isoFormat('D MMMM Y');
            $master->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$master->kontrak_selesai)->isoFormat('D MMMM Y');
            $master->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$master->tgl_penempatan)->isoFormat('D MMMM Y');
            $master->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$master->created_at)->isoFormat('D MMMM Y');
            $master->stgl_quotation = Carbon::createFromFormat('Y-m-d',$master->tgl_quotation)->isoFormat('D MMMM Y');

            $master->salary_rule = "";
            $salaryRuleList = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();
            if($salaryRuleList != null){
                $master->salary_rule = $salaryRuleList->nama_salary_rule;
            }

            $data->manajemen_fee = "";
            $manajemenFeeList = DB::table('m_management_fee')->where('id',$data->management_fee_id)->first();
            if($manajemenFeeList != null){
                $data->manajemen_fee = $manajemenFeeList->nama;
            }
            $statusQuotation = DB::table('m_status_quotation')->where('id',$master->status_quotation_id)->first();
            $master->status = $statusQuotation->nama;



            $aplikasiPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$id)->get();
            foreach ($aplikasiPendukung as $key => $value) {
                $app = DB::table('m_aplikasi_pendukung')->where('id',$value->aplikasi_pendukung_id)->first();
                $value->link_icon = $app->link_icon;
            }

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_kaporlap where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_ohc where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_devices where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_chemical where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->where('jumlah',1)->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_kebutuhan_ohc')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_kebutuhan_chemical')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();

            // tambahan hitungan
            $quotationKebutuhan = 
            DB::table("sl_quotation_kebutuhan")
            ->join('m_kebutuhan','m_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id')
            ->whereNull('sl_quotation_kebutuhan.deleted_at')
            ->where('sl_quotation_kebutuhan.id',$id)
            ->orderBy('sl_quotation_kebutuhan.kebutuhan_id','ASC')
            ->select('sl_quotation_kebutuhan.is_aktif','sl_quotation_kebutuhan.nominal_takaful','sl_quotation_kebutuhan.penjamin','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.nomor','sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.nominal_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$value->kebutuhan_id)->whereNull('deleted_at')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama,nominal FROM `sl_quotation_kebutuhan_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");

            $jumlahHc = 0;
            foreach ($quotationKebutuhan[0]->kebutuhan_detail as $jhc) {
                $jumlahHc += $jhc->jumlah_hc;
            }

            $provisi = 12;
            if(!strpos($quotation->durasi_kerjasama, 'tahun')){
                $provisi = (int)str_replace(" bulan", "", $quotation->durasi_kerjasama);
            }
            $quotation->provisi = $provisi;

            foreach ($quotationKebutuhan[0]->kebutuhan_detail as $ikbd => $kbd) {
                // $kbd->daftar_tunjangan = [];
                $totalTunjangan = 0;
                foreach ($daftarTunjangan as $idt => $tunjangan) {
                    $kbd->{$tunjangan->nama} = 0;
                    //cari data tunjangan
                    $dtTunjangan = DB::table('sl_quotation_kebutuhan_detail_tunjangan')->whereNull('deleted_at')->where('quotation_kebutuhan_detail_id',$kbd->id)->where('nama_tunjangan',$tunjangan->nama)->first();
                    if($dtTunjangan != null){
                        $kbd->{$tunjangan->nama} = $dtTunjangan->nominal;

                        $totalTunjangan += $dtTunjangan->nominal;
                    }
                }

                $kbd->nominal_takaful = 0;
                $kbd->bpjs_jkm = 0;
                $kbd->bpjs_jkk = 0;
                $kbd->bpjs_jht = 0;
                $kbd->bpjs_jp = 0;
                $kbd->bpjs_kes = 0;

                if($quotationKebutuhan[0]->penjamin=="Takaful"){
                    $kbd->nominal_takaful = $quotationKebutuhan[0]->nominal_takaful;
                }else{
                    // hitung JKK
                    if($quotationKebutuhan[0]->resiko=="Sangat Rendah"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.24/100;
                    }else if($quotationKebutuhan[0]->resiko=="Rendah"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.54/100;
                    }else if($quotationKebutuhan[0]->resiko=="Sedang"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.89/100;
                    }else if($quotationKebutuhan[0]->resiko=="Tinggi"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.27/100;
                    }else if($quotationKebutuhan[0]->resiko=="Sangat Tinggi"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.74/100;
                    };

                    //hitung JKM
                    $kbd->bpjs_jkm = $quotationKebutuhan[0]->nominal_upah*0.3/100;

                    //Hitung JHT
                    if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                        $kbd->bpjs_jht = $quotationKebutuhan[0]->nominal_upah*3.7/100;
                    }else{
                        $kbd->bpjs_jht = 0;
                    }

                    //Hitung JP
                    if($quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                        $kbd->bpjs_jp = $quotationKebutuhan[0]->nominal_upah*2/100;
                    }else {
                        $kbd->bpjs_jp = 0;
                    }

                    $kbd->bpjs_kes = $quotationKebutuhan[0]->nominal_upah*4/100;

                }

                $kbd->tunjangan_hari_raya = 0;
                if($quotation->thr=="Diprovisikan"){
                    $kbd->tunjangan_hari_raya = $quotationKebutuhan[0]->nominal_upah/$provisi;
                }

                $kbd->kompensasi = 0;
                if($quotation->kompensasi=="Diprovisikan"){
                    $kbd->kompensasi = $quotationKebutuhan[0]->nominal_upah/$provisi;
                }
                
                $kbd->tunjangan_holiday = 0;
                if($quotation->tunjangan_holiday=="Flat"){
                    $kbd->tunjangan_holiday = $quotation->nominal_tunjangan_holiday;
                }else{
                    $kbd->tunjangan_holiday = ($quotationKebutuhan[0]->nominal_upah/173*(14))*15/$provisi;
                }

                $kbd->lembur = 0;
                if($quotation->lembur=="Flat"){
                    $kbd->lembur = $quotation->nominal_lembur;
                }else{
                    $kbd->lembur = 0;
                }

                $personilKaporlap = 0;
                $kbdkaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdkaporlap as $ikdbkap => $kdbkap) {
                    $personilKaporlap += ($kdbkap->harga*$kdbkap->jumlah)/$provisi;
                };

                $kbd->personil_kaporlap = $personilKaporlap;

                $personilDevices = 0;
                $kbddevices = DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbddevices as $ikdbdev => $kdbdev) {
                    $personilDevices += ($kdbdev->harga*$kdbdev->jumlah)/$provisi;
                };
                $appPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                foreach ($appPendukung as $kapp => $app) {
                    $personilDevices += ($app->harga*1)/$provisi;
                }

                $kbd->personil_devices = $personilDevices;

                $personilOhc = 0;
                $kbdOhc = DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                foreach ($kbdOhc as $ikdbohc => $kdbohc) {
                    $personilOhc += ($kdbohc->harga*$kdbohc->jumlah/$jumlahHc)/$provisi;
                };

                $kbd->personil_ohc = $personilOhc;

                $personilChemical = 0;
                $kbdChemical = DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdChemical as $ikdbchemical => $kdbchemical) {
                    $personilChemical += ($kdbchemical->harga*$kdbchemical->jumlah)/$provisi;
                };

                $kbd->personil_chemical = $personilChemical;

                // dd($kbd->personil_kaporlap);
                $kbd->total_personil = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;

                $kbd->sub_total_personil = $kbd->total_personil*$kbd->jumlah_hc;
                
                $kbd->management_fee = $kbd->sub_total_personil*$quotationKebutuhan[0]->persentase/100;

                $kbd->grand_total = $kbd->sub_total_personil+$kbd->management_fee;

                $kbd->ppn = 0;
                $kbd->pph = 0;
                if ($quotation->ppn_pph_dipotong=="Management Fee") {
                    $kbd->ppn = $kbd->management_fee*11/100;
                    $kbd->pph = $kbd->management_fee*(-2/100);
                }else if ($quotation->ppn_pph_dipotong=="Total Invoice") {
                    $kbd->ppn = $kbd->grand_total*11/100;
                    $kbd->pph = $kbd->grand_total*(-2/100);
                }
                
                $kbd->total_invoice = $kbd->grand_total + $kbd->ppn + $kbd->pph;

                

                $kbd->pembulatan = ceil($kbd->total_invoice / 1000) * 1000;

                // COST STRUCTURE
                $kbd->total_base_manpower = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan;
                $kbd->total_exclude_base_manpower = $kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;;

                $kbd->total_personil_coss = $kbd->total_base_manpower + $kbd->total_exclude_base_manpower + $kbd->biaya_monitoring_kontrol;

                $kbd->sub_total_personil_coss = $kbd->total_personil_coss*$kbd->jumlah_hc;
                
                $kbd->management_fee_coss = $kbd->sub_total_personil_coss*$quotationKebutuhan[0]->persentase/100;

                $kbd->grand_total_coss = $kbd->sub_total_personil_coss+$kbd->management_fee_coss;
                
                $kbd->ppn_coss = 0;
                $kbd->pph_coss = 0;
                if($quotation->ppn_pph_dipotong =="Management Fee"){
                    $kbd->ppn_coss = $kbd->management_fee_coss*11/100;
                    $kbd->pph_coss = $kbd->management_fee_coss*(-2/100);
                }else  if($quotation->ppn_pph_dipotong =="Total Invoice"){
                    $kbd->ppn_coss = $kbd->grand_total_coss*11/100;
                    $kbd->pph_coss = $kbd->grand_total_coss*(-2/100);
                }

                $kbd->total_invoice_coss = $kbd->grand_total_coss + $kbd->ppn_coss + $kbd->pph_coss;

                $kbd->pembulatan_coss = ceil($kbd->total_invoice_coss / 1000) * 1000;

            };
            
            $listPic = DB::table('sl_quotation_pic')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            $kebutuhanDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$data->id)->whereNull('deleted_at')->get();
            foreach ($kebutuhanDetail as $kkd => $vkd) {
                $vkd->requirement = DB::table('sl_quotation_kebutuhan_detail_requirement')->where('quotation_kebutuhan_detail_id',$vkd->id)->whereNull('deleted_at')->get();
            }
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();

            return view('sales.quotation.view',compact('quotation','salaryRuleQ','kebutuhanDetail','listPic','daftarTunjangan','quotationKebutuhan','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','data','master','leads','aplikasiPendukung'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save (Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'leads' => 'required',
                'jumlah_site' => 'required'
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
                $current_date = Carbon::now()->toDateString();
                $newId = DB::table('sl_quotation')->insertGetId([
                    'tgl_quotation' => $current_date,
                    'leads_id' => $request->leads_id,
                    'jumlah_site' =>  $request->jumlah_site,
                    'nama_perusahaan' => $request->leads,
                    'step' => 1,
                    'status_quotation_id' =>1,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                $leads = DB::table('sl_leads')->where('id',$request->leads_id)->first();

                DB::table('sl_quotation_pic')->insert([
                    'quotation_id' => $newId,
                    'leads_id' => $request->leads_id,
                    'nama' => $leads->pic,
                    'jabatan_id' => $leads->jabatan_id,
                    'jabatan' => $leads->jabatan,
                    'no_telp' => $leads->no_telp,
                    'email' => $leads->email,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                DB::commit();
                return redirect()->route('quotation.step',['id'=>$newId,'step'=>'1']);
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit1 (Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'nama_site' => 'required',
                'jenis_kontrak' => 'required'
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
                $current_date = Carbon::now()->toDateString();

                $newStep = 2;
                $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                if($dataQuotation->step>$newStep){
                    $newStep = $dataQuotation->step;
                }
                if($request->edit==1){
                    $newStep = $dataQuotation->step;
                }
                
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'nama_site' =>  $request->nama_site,
                    'jenis_kontrak' => $request->jenis_kontrak,
                    'step' => $newStep,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                DB::commit();
                $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

                if($request->edit==0){
                    return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'2']);
                }else{
                    return redirect()->route('quotation.view',$data->id);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit2 (Request $request){
        if($request->ada_cuti=="Tidak Ada"){
            $request->macam_cuti = "Tidak Ada";
            $request->gaji_saat_cuti =null;
            $request->prorate =null;
        }else{
            $request->macam_cuti = implode(",",$request->cuti);
            if(in_array("Cuti Melahirkan",$request->cuti)){
                if($request->gaji_saat_cuti!="Prorate"){
                    $request->prorate =null;
                }
            }else{
                $request->gaji_saat_cuti =null;
                $request->prorate =null;
            }
            if(!in_array("Cuti Kematian",$request->cuti)){
                $request->hari_cuti_kematian =null;
            }
            if(!in_array("Istri Melahirkan",$request->cuti)){
                $request->hari_istri_melahirkan =null;
            }
            if(!in_array("Cuti Menikah",$request->cuti)){
                $request->hari_cuti_menikah =null;
            }
        }
        
        try {
            $validator = Validator::make($request->all(), [
                'kebutuhan' => 'required',
                'entitas' => 'required',
                'mulai_kontrak' => 'required',
                'kontrak_selesai' => 'required',
                'tgl_penempatan' => 'required',
                'top' => 'required',
                'salary_rule' => 'required'
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                if($request->mulai_kontrak>$request->kontrak_selesai){
                    return back()->withErrors(['mulai_kontrak' => 'Mulai Kontrak tidak boleh lebih dari Kontrak Selesai']);
                };
                if($request->kontrak_selesai<$request->mulai_kontrak){
                    return back()->withErrors(['kontrak_selesai' => 'Kontrak Selesai tidak boleh kurang dari mulai kontrak']);
                };
                if($request->tgl_penempatan<$request->mulai_kontrak){
                    return back()->withErrors(['tgl_penempatan_kurang' => 'Tanggal Penempatan tidak boleh kurang dari Kontrak Awal']);
                };
                if($request->tgl_penempatan>$request->kontrak_selesai){
                    return back()->withErrors(['tgl_penempatan_kurang' => 'Tanggal Penempatan tidak boleh lebih dari Kontrak Selesai']);
                };
                $current_date_time = Carbon::now()->toDateTimeString();
                $current_date = Carbon::now()->toDateString();
                $quotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                $salaryRule = DB::table('m_salary_rule')->where('id',$request->salary_rule)->first();
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'kebutuhan_id' =>  implode(",",$request->kebutuhan),
                    'company_id' => $request->entitas,
                    'mulai_kontrak' => $request->mulai_kontrak,
                    'kontrak_selesai' => $request->kontrak_selesai,
                    'tgl_penempatan' => $request->tgl_penempatan,
                    'salary_rule_id' => $request->salary_rule,
                    'top' => $request->top,
                    'jumlah_hari_invoice' => $request->jumlah_hari_invoice,
                    'tipe_hari_invoice' => $request->tipe_hari_invoice,
                    'evaluasi_kontrak' => $request->evaluasi_kontrak,
                    'durasi_kerjasama' => $request->durasi_kerjasama,
                    'durasi_karyawan' => $request->durasi_karyawan,
                    'evaluasi_karyawan' => $request->evaluasi_karyawan,
                    'step' => 3,
                    'cuti' => $request->macam_cuti,
                    'hari_cuti_kematian' => $request->hari_cuti_kematian,
                    'hari_istri_melahirkan' => $request->hari_istri_melahirkan,
                    'hari_cuti_menikah' => $request->hari_cuti_menikah,
                    'gaji_saat_cuti' => $request->gaji_saat_cuti,
                    'prorate' => $request->prorate,
                    'shift_kerja' => $request->shift_kerja ,
                    'jam_kerja' => $request->jam_kerja ,
                    'mulai_kerja' => $request->mulai_kerja ,
                    'selesai_kerja' => $request->selesai_kerja ,
                    // 'sistem_kerja' => $request->sistem_kerja ,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                $kebutuhanPerjanjian ="";
                foreach ($request->kebutuhan as $key => $value) {
                    $company = DB::connection('mysqlhris')->table('m_company')->where('id',$request->entitas)->first();
                    $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')
                                            ->whereNull('deleted_at')
                                            ->where('quotation_id',$request->id)
                                            ->where('kebutuhan_id',$value)->first();
                    $kebutuhan = DB::table('m_kebutuhan')->where('id',$value)->first();

                    $kebutuhanPerjanjian = "<b>".$kebutuhan->nama."</b>";
                    
                    if($quotationKebutuhan == null){
                        DB::table('sl_quotation_kebutuhan')->insert([
                            'nomor' => $this->generateNomor($quotation->leads_id,$request->entitas),
                            'quotation_id' => $request->id,
                            'leads_id' => $quotation->leads_id,
                            'kebutuhan_id' => $value,
                            'kebutuhan' => $kebutuhan->nama,
                            'company_id' => $request->entitas,
                            'company' => $company->name,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->where('kebutuhan_id',$value)->update([
                            'quotation_id' => $request->id,
                            'leads_id' => $quotation->leads_id,
                            'kebutuhan_id' => $value,
                            'kebutuhan' => $kebutuhan->nama,
                            'company_id' => $request->entitas,
                            'company' => $company->name,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);
                    }
                };

                DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNotIn('kebutuhan_id', $request->kebutuhan)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                $newStep = 3;
                $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                if($dataQuotation->step>$newStep){
                    $newStep = $dataQuotation->step;
                }
                if($request->edit==1){
                    $newStep = $dataQuotation->step;
                }
                
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'step' => $newStep,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

                if($request->edit==0){
                    return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'3']);
                }else{
                    return redirect()->route('quotation.view',$data->id);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit3 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $newStep = 4;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'4']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit4 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                $provinsiId = $request['provinsi-'.$value->id];
                $kotaId = $request['kota-'.$value->id];
                $upah = $request['upah-'.$value->id];
                $manfee = $request['manajemen_fee_'.$value->id];
                $presentase = $request['persentase_'.$value->id];

                $provinsi = null;
                if($provinsiId != null){
                    $provinceList = DB::connection('mysqlhris')->table('m_province')->where('id',$provinsiId)->first();
                    if($provinceList != null){
                        $provinsi = $provinceList->name;
                    }
                }

                $kota = null;
                if($kotaId != null){
                    $kotaList = DB::connection('mysqlhris')->table('m_city')->where('id',$kotaId)->first();
                    if($kotaList != null){
                        $kota = $kotaList->name;
                    }
                }

                $customUpah = 0;
                if($upah == "Custom"){
                    $customUpah = str_replace(".","",$request['custom-upah-'.$value->id]);
                }else{
                    //cari ump / umk
                    if($upah =="UMP"){
                        $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('province_id',$provinsiId)->first();
                        if($dataUmp !=null){
                            $customUpah = $dataUmp->ump;
                        }
                    }else if ($upah =="UMK") {
                        $dataUmk = DB::table("m_umk")->whereNull('deleted_at')->where('city_id',$kotaId)->first();
                        if($dataUmk !=null){
                            $customUpah = $dataUmk->umk;
                        }
                    }
                }
                
                DB::table('sl_quotation_kebutuhan')->where('id',$value->id)->update([
                    'provinsi_id' => $provinsiId,
                    'provinsi' => $provinsi,
                    'kota_id' => $kotaId,
                    'kota' => $kota,
                    'upah' => $upah,
                    'nominal_upah' => $customUpah,
                    'management_fee_id' => $manfee,
                    'is_aktif' => 0,
                    'persentase' => $presentase,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            $newStep = 5;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            if($request->lembur!="Flat"){
                $request->nominal_lembur = null;
            }else{
                $request->nominal_lembur = str_replace(".","",$request->nominal_lembur);
            }

            if($request->tunjangan_holiday!="Flat"){
                $request->nominal_tunjangan_holiday = null;
            }else{
                $request->nominal_tunjangan_holiday = str_replace(".","",$request->nominal_tunjangan_holiday);
            }

            if($request->ada_lembur=="Tidak Ada"){
                $request->lembur ="Tidak Ada";
            }
            if($request->ada_kompensasi=="Tidak Ada"){
                $request->kompensasi ="Tidak Ada";
            }
            if($request->ada_thr=="Tidak Ada"){
                $request->thr ="Tidak Ada";
            }
            if($request->ada_tunjangan_holiday=="Tidak Ada"){
                $request->tunjangan_holiday ="Tidak Ada";
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'thr' => $request->thr,
                'kompensasi' => $request->kompensasi,
                'lembur' => $request->lembur,
                'ppn_pph_dipotong' => $request->ppn_pph_dipotong,
                'tunjangan_holiday' => $request->tunjangan_holiday,
                'nominal_lembur' => $request->nominal_lembur,
                'nominal_tunjangan_holiday' => $request->nominal_tunjangan_holiday,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'5']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit5 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                $jenisPerusahaanId = $request['jenis-perusahaan-'.$value->id];
                $resiko = $request['resiko-'.$value->id];
                $penjamin = $request['penjamin-'.$value->id];
                $programBpjs = $request['program-bpjs-'.$value->id];
                $nominalTakaful = $request['nominal-takaful-'.$value->id];

                // jika penjamin takaful maka program bpjs == null
                if($penjamin=="Takaful"){
                    $programBpjs = null; 
                    $nominalTakaful = str_replace(".","",$nominalTakaful);
                }else{
                    $nominalTakaful =null;
                }

                $jenisPerusahaan = null;
                if($jenisPerusahaanId != null){
                    $jenisPerusahaanList = DB::table('m_jenis_perusahaan')->where('id',$jenisPerusahaanId)->first();
                    if($jenisPerusahaanList != null){
                        $jenisPerusahaan = $jenisPerusahaanList->nama;
                    }
                }

                $isAktif = $value->is_aktif;
                if($isAktif==2){
                    if($programBpjs != "4 BPJS"){
                        $isAktif = 0;
                    }
                }

                if($isAktif ==2){
                    $isAktif = 1;
                };

                DB::table('sl_quotation_kebutuhan')->where('id',$value->id)->update([
                    'jenis_perusahaan_id' => $jenisPerusahaanId,
                    'jenis_perusahaan' => $jenisPerusahaan,
                    'resiko' => $resiko,
                    'is_aktif' => $isAktif,
                    'penjamin' => $penjamin,
                    'program_bpjs' => $programBpjs,
                    'nominal_takaful' => $nominalTakaful,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            $newStep = 6;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'6']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit6 (Request $request){
        DB::beginTransaction();

        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            if($request->aplikasi_pendukung !=null){
                $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
                foreach ($quotationKebutuhan as $key => $value) {
                    $aplikasiPendukung = $request->aplikasi_pendukung;
                    $arrAplikasiId = [];
                    foreach ($aplikasiPendukung as $keyd => $valued) {
                        $appdukung = DB::table('m_aplikasi_pendukung')->where('id',$valued)->first();
    
                        $dataAplikasi = DB::table('sl_quotation_kebutuhan_aplikasi')->where('aplikasi_pendukung_id',$valued)->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->first();
                        $quotationKebutuhanDetail = DB::table("sl_quotation_kebutuhan_detail")->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->first();

                        if($dataAplikasi==null){
                            $appId = DB::table('sl_quotation_kebutuhan_aplikasi')->insertGetId([
                                'quotation_id' => $request->id,
                                'quotation_kebutuhan_id' => $value->id,
                                'aplikasi_pendukung_id' => $valued,
                                'aplikasi_pendukung' => $appdukung->nama,
                                'harga' => $appdukung->harga,
                                'created_at' => $current_date_time,
                                'created_by' => Auth::user()->full_name
                            ]);

                            DB::table('sl_quotation_kebutuhan_devices')->insert([
                                'quotation_id' => $request->id,
                                'quotation_kebutuhan_id' => $value->id,
                                'quotation_kebutuhan_detail_id' => $quotationKebutuhanDetail->id,
                                'quotation_kebutuhan_aplikasi_id' => $appId,
                                'barang_id' => $appdukung->barang_id,
                                'jumlah' => 1,
                                'harga' => $appdukung->harga,
                                'nama' => $appdukung->nama,
                                'jenis_barang' => 'Aplikasi Pendukung',
                                'created_at' => $current_date_time,
                                'created_by' => Auth::user()->full_name
                            ]);
                            array_push($arrAplikasiId,$appId);
                        }else{
                            DB::table('sl_quotation_kebutuhan_aplikasi')->where('id',$dataAplikasi->id)->update([
                                'quotation_id' => $request->id,
                                'quotation_kebutuhan_id' => $value->id,
                                'aplikasi_pendukung_id' => $valued,
                                'aplikasi_pendukung' => $appdukung->nama,
                                'harga' => $appdukung->harga,
                                'updated_at' => $current_date_time,
                                'updated_by' => Auth::user()->full_name
                            ]);

                            DB::table("sl_quotation_kebutuhan_devices")
                            ->whereNull('deleted_at')
                            ->where('quotation_kebutuhan_id',$value->id)
                            ->where('quotation_kebutuhan_detail_id',$quotationKebutuhanDetail->id)
                            ->where('quotation_kebutuhan_aplikasi_id',$dataAplikasi->id)
                            ->where('barang_id',$request->barang)->update([
                                    'jumlah' => 1,
                                    'harga' => $appdukung->harga,
                                    'nama' => $appdukung->nama,
                                    'jenis_barang' => 'Aplikasi Pendukung',
                                    'updated_at' => $current_date_time,
                                    'updated_by' => Auth::user()->full_name
                            ]);

                            array_push($arrAplikasiId,$dataAplikasi->id);
                        }
                    }
    
                    DB::table('sl_quotation_kebutuhan_aplikasi')->where('quotation_kebutuhan_id',$value->id)->whereNotIn('aplikasi_pendukung_id', $aplikasiPendukung)->update([
                        'deleted_at' => $current_date_time,
                        'deleted_by' => Auth::user()->full_name
                    ]);
                    DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$value->id)->whereNotIn('quotation_kebutuhan_aplikasi_id', $arrAplikasiId)->update([
                        'deleted_at' => $current_date_time,
                        'deleted_by' => Auth::user()->full_name
                    ]);
                }
            }

            $newStep = 7;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::commit();
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'7']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit7 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();
            $kebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->first();
            $detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$kebutuhan->id)->whereNull('deleted_at')->get();

            $listKaporlap = DB::table('m_barang')
                                ->whereNull('deleted_at')
                                ->orderBy("nama","asc")
                                ->get();
            
            //hapus dulu data existing 
            DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$kebutuhan->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            foreach ($listKaporlap as $key => $value) {
                foreach ($detail as $kd => $vd) {
                    //cek apakah 0 jika 0 skip
                    if($request->{'jumlah_'.$value->id.'_'.$vd->id} == "0" ||$request->{'jumlah_'.$value->id.'_'.$vd->id} == null){
                        continue;   
                    }else{
                        //cari harga
                        $barang = DB::table('m_barang')->where('id',$value->id)->first();
                        DB::table('sl_quotation_kebutuhan_kaporlap')->insert([
                            'quotation_kebutuhan_detail_id' => $vd->id,
                            'quotation_kebutuhan_id' => $kebutuhan->id,
                            'quotation_id' => $quotation->id,
                            'barang_id' => $barang->id,
                            'jumlah' => $request->{'jumlah_'.$value->id.'_'.$vd->id},
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            $newStep = 8;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPPSecurity($data->id);
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'8']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit8 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $newStep = 9;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPPSecurity($data->id);
            
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'9']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit9 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();
            $kebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->first();
            $detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$kebutuhan->id)->whereNull('deleted_at')->get();

            $listDevices = DB::table('m_barang')
                                ->whereNull('deleted_at')
                                ->orderBy("nama","asc")
                                ->get();
            
            //hapus dulu data existing 
            DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$kebutuhan->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            foreach ($listDevices as $key => $value) {
                foreach ($detail as $kd => $vd) {
                    //cek apakah 0 jika 0 skip
                    if($request->{'jumlah_'.$value->id.'_'.$vd->id} == "0" ||$request->{'jumlah_'.$value->id.'_'.$vd->id} == null){
                        continue;   
                    }else{
                        //cari harga
                        $barang = DB::table('m_barang')->where('id',$value->id)->first();
                        DB::table('sl_quotation_kebutuhan_devices')->insert([
                            'quotation_kebutuhan_detail_id' => $vd->id,
                            'quotation_kebutuhan_id' => $kebutuhan->id,
                            'quotation_id' => $quotation->id,
                            'barang_id' => $barang->id,
                            'jumlah' => $request->{'jumlah_'.$value->id.'_'.$vd->id},
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            $newStep = 10;
            $dataStep = 10;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($dataQuotation->step>$newStep){
                $dataStep = $dataQuotation->step;
            }
            if($data->kebutuhan_id==2||$data->kebutuhan_id==1||$data->kebutuhan_id==4){
                $newStep=11;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $dataStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            // $this->perhitunganHPPSecurity($data->id);
            
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>$newStep]);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit10 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $newStep = 11;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPP($data->id);
            
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'11']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit11 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $newStep = 12;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'penagihan' => $request->penagihan,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPPSecurity($data->id);

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'12']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit12 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $newStep = 13;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            if($request->ada_training=="Tidak Ada"){
                $request->training ="0";
            }
            if($request->ada_serikat=="Tidak Ada"){
                $request->status_serikat ="Tidak Ada";
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'npwp' => $request->npwp ,
                'alamat_npwp' => $request->alamat_npwp,
                'pic_invoice' => $request->pic_invoice ,
                'telp_pic_invoice' => $request->telp_pic_invoice ,
                'email_pic_invoice' => $request->email_pic_invoice ,
                'materai' => $request->materai ,
                'kunjungan_operasional' => $request->jumlah_kunjungan_operasional." ".$request->bulan_tahun_kunjungan_operasional ,
                'kunjungan_tim_crm' => $request->jumlah_kunjungan_tim_crm." ".$request->bulan_tahun_kunjungan_tim_crm ,
                'keterangan_kunjungan_operasional' => $request->keterangan_kunjungan_operasional ,
                'keterangan_kunjungan_tim_crm' => $request->keterangan_kunjungan_tim_crm ,
                'training' => $request->training ,
                'joker_reliever' => $request->joker_reliever ,
                'syarat_invoice' => $request->syarat_invoice ,
                'alamat_penagihan_invoice' => $request->alamat_penagihan_invoice ,
                'catatan_site' => $request->catatan_site ,
                'status_serikat' => $request->status_serikat ,
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            //tamba perjanjian
            //hapus dulu perjanjian yg lama atau kalau ada
            DB::table('sl_quotation_kerjasama')->where('quotation_id',$request->id)->whereNull('deleted_at')->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            $kebutuhanPerjanjian = "<b>".$data->kebutuhan."</b>";
            //buat perjanjian
            // $top = "";
            // if($dataQuotation->top=="Kurang Dari 7 Hari"){
            //     $top = "Maksimal 7 Hari Kerja"; 
            // }else{
            //     $top = "Maksimal ".$dataQuotation->jumlah_hari_invoice." hari ".$dataQuotation->tipe_hari_invoice;
            // }

            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$dataQuotation->salary_rule_id)->first();

            $tableSalary = '<table class="table table-bordered" style="width:100%">
                              <thead>
                                <tr>
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-center"><b>Schedule Plan</b></th>
                                  <th class="text-center"><b>Periode</b></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center">1</td>
                                  <td>Cut Off</td>
                                  <td>'.$salaryRuleQ->cutoff.'</td>
                                </tr>
                                <tr>
                                  <td class="text-center">2</td>
                                  <td>Pengiriman <i>Invoice</i></td>
                                  <td>'.$salaryRuleQ->pengiriman_invoice.'</td>
                                </tr>
                                <tr>
                                  <td class="text-center">3</td>
                                  <td>Pembayaran <i>Invoice</i></td>
                                  <td>'.$salaryRuleQ->pembayaran_invoice.'</td>
                                </tr>
                                <tr>
                                  <td class="text-center">6</td>
                                  <td>Rilis <i>Payroll</i> / Gaji</td>
                                  <td>'.$salaryRuleQ->rilis_payroll.'</td>
                                </tr>
                              </tbody>
                            </table>';
            $kunjunganOperasional = "";
            if ($dataQuotation->kunjungan_operasional !=null) {
                $kunjunganOperasional = explode(" ",$dataQuotation->kunjungan_operasional)[0]." kali dalam 1 ".explode(" ",$dataQuotation->kunjungan_operasional)[1];
            }
            $appPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$data->id)->get();
            $sAppPendukung = "<b>";
            foreach ($appPendukung as $kduk => $dukung) {
                if($kduk != 0){
                    $sAppPendukung .= ", ";
                }
                $sAppPendukung .= $dukung->aplikasi_pendukung;
            }
            $sAppPendukung .= "</b>";

            $arrPerjanjian = [
                "Penawaran harga ini berlaku 30 hari sejak tanggal diterbitkan.",
                "Akan dilakukan <i>survey</i> area untuk kebutuhan ".$kebutuhanPerjanjian." sebagai tahapan <i>assesment</i> area untuk memastikan efektifitas pekerjaan.",
                "Komponen dan nilai dalam penawaran harga ini berdasarkan kesepakatan para pihak dalam pengajuan harga awal, apabila ada perubahan, pengurangan maupun penambahan pada komponen dan nilai pada penawaran, maka <b>para pihak</b> sepakat akan melanjutkan ke tahap negosiasi selanjutnya.",
                "Skema cut-off, pengiriman <i>invoice</i>, pembayaran <i>invoice</i> dan penggajian adalah <b>TOP/talangan</b> maksimal 30 hari kalender dengan skema sebagai berikut: <br>".$tableSalary."<i><br>*Rilis gaji adalah talangan.<br>*Maksimal pembayaran invoice 30 hari kalender setelah invoice</i>",
                "Kunjungan tim operasional ".$kunjunganOperasional.", untuk monitoring dan supervisi dengan karyawan dan wajib bertemu dengan pic <b>Pihak Pertama</b> untuk koordinasi.",
                "Tim operasional bersifat <i>on call</i> apabila terjadi <i>case</i> atau insiden yang terjadi yang mengharuskan untuk datang ke lokasi kerja Pihak Pertama.",
                "Pemenuhan kandidat dilakukan dengan 2 tahap <i>screening</i> :<br>
                    a. Tahap ke -1 : dilakukan oleh tim rekrutmen <b>Pihak Kedua</b> untuk memastikan bahwa kandidat sudah sesuai dengan kualifikasi <b>dari Pihak Pertama</b>.<br>
                    b. Tahap ke -2 : dilakukan oleh user <b>Pihak Pertama</b>, dan dijadwalkan setelah adanya <i>report</i> hasil <i>screening</i> dari <b>Pihak Kedua</b>.",
                "<i>Support</i> aplikasi digital :".$sAppPendukung."."
            ];

            foreach ($arrPerjanjian as $key => $value) {
                DB::table('sl_quotation_kerjasama')->insert([
                    'quotation_id' => $request->id,
                    'perjanjian' => $value,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'13']);
            }else{
                return redirect()->route('quotation.view',$data->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit13 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$request->id)->first();
            $umk = DB::table('m_umk')->where('city_id',$data->kota_id)->whereNull('deleted_at')->first();
            $isAktif = 1;
            $statusQuotation = 3;

            //jika top lebih dari 7 hari
            if($quotation->top=="Lebih Dari 7 Hari"){
                $isAktif = 0;
                $statusQuotation = 2;
            }
            // jika nominal kurang dari umk
            if ($data->nominal_upah<$umk->umk) {
                $isAktif = 0;
                $statusQuotation = 2;
            }

            // jika persentasi mf kurang dari 7
            if ($data->persentase < 7) {
                $isAktif = 0;
                $statusQuotation = 2;
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 100,
                'status_quotation_id' =>$statusQuotation,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('sl_quotation_kebutuhan')->where('id',$data->id)->update([
                'is_aktif' => $isAktif,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //Masukkan Requirement
            $detail = DB::table('sl_quotation_kebutuhan_detail')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$data->id)->get();
            foreach ($detail as $key => $value) {
                //cari apakah ada requirement
                $existData= DB::table('sl_quotation_kebutuhan_detail_requirement')->where('quotation_kebutuhan_detail_id',$value->id)->whereNull('deleted_at')->get();

                if(count($existData)==0){
                    $requirement = DB::table('m_kebutuhan_detail_requirement')->whereNull('deleted_at')->where('position_id',$value->kebutuhan_detail_id)->get();
                    foreach ($requirement as $kreq => $req) {
                        DB::table('sl_quotation_kebutuhan_detail_requirement')->insert([
                            'quotation_id' => $data->quotation_id,
                            'quotation_kebutuhan_id' => $data->id,
                            'quotation_kebutuhan_detail_id' => $value->id,
                            'requirement' => $req->requirement,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            return redirect()->route('quotation.view',$data->id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete (Request $request){
        return null;
    }

    public function list (Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table('sl_quotation')
                    ->leftJoin('sl_quotation_kebutuhan','sl_quotation.id','sl_quotation_kebutuhan.quotation_id')
                    ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
                    ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
                    ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                    ->select('m_status_quotation.nama as status','sl_quotation_kebutuhan.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
                    ->whereNull('sl_quotation.deleted_at')->whereNull('sl_quotation_kebutuhan.deleted_at');

            if(!empty($request->tgl_dari)){
                $data = $data->where('sl_quotation.tgl_quotation','>=',$request->tgl_dari);
            }else{
                $data = $data->where('sl_quotation.tgl_quotation','==',carbon::now()->toDateString());
            }
            if(!empty($request->tgl_sampai)){
                $data = $data->where('sl_quotation.tgl_quotation','<=',$request->tgl_sampai);
            }else{
                $data = $data->where('sl_quotation.tgl_quotation','==',carbon::now()->toDateString());
            }
            if(!empty($request->branch)){
                $data = $data->where('sl_leads.branch_id',$request->branch);
            }
            if(!empty($request->company)){
                $data = $data->where('sl_quotation.company_id',$request->company);
            }
            if(isset($request->is_aktif)){
                $data = $data->where('sl_quotation_kebutuhan.is_aktif',$request->is_aktif);
            }
            //divisi sales
            if(in_array(Auth::user()->role_id,[29,30,31,32,33])){
                // sales
                if(Auth::user()->role_id==29){
                    $data = $data->where('m_tim_sales_d.user_id',Auth::user()->id);
                }else if(Auth::user()->role_id==30){
                }
                // spv sales
                else if(Auth::user()->role_id==31){
                    $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();
                    $memberSales = [];
                    $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$tim->tim_sales_id)->get();
                    foreach ($sales as $key => $value) {
                        array_push($memberSales,$value->user_id);
                    }
                    $data = $data->whereIn('m_tim_sales_d.user_id',$memberSales);
                }
                // Asisten Manager Sales , Manager Sales
                else if(Auth::user()->role_id==32 || Auth::user()->role_id==33){
                }
            }
            //divisi RO
            else if(in_array(Auth::user()->role_id,[4,5,6,8])){
                if(in_array(Auth::user()->role_id,[4,5])){
                    $data = $data->where('sl_leads.ro_id',Auth::user()->id);
                }else if(in_array(Auth::user()->role_id,[6,8])){

                }
            }
            //divisi crm
            else if(in_array(Auth::user()->role_id,[54,55,56])){
                if(in_array(Auth::user()->role_id,[54])){
                    $data = $data->where('sl_leads.crm_id',Auth::user()->id);
                }else if(in_array(Auth::user()->role_id,[55,56])){

                }
            };

            if(!empty($request->kebutuhan)){
                $data = $data->where('sl_quotation.kebutuhan','like','%'.$request->kebutuhan.'%');
            }
            $data = $data->get();
                        
            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_quotation)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                $id = null;
                if($data->id == null){
                    $id = $data->quotation_id;
                }
                if($data->step != 100){
                    return '<div class="justify-content-center d-flex">
                                <a href="'.route('quotation.step',['id'=>$data->quotation_id,'step'=>$data->step]).'" class="btn btn-primary waves-effect btn-xs">Lanjutkan Pengisian</a> &nbsp;
                    </div>';
                }else{
                    return '<div class="justify-content-center d-flex">
                                <a href="'.route('quotation.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                    </div>';
                }
                
            })
            ->editColumn('nomor', function ($data) {
                if($data->id != null){
                    return '<a href="'.route('quotation.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
                }
                return "";
            })
            ->editColumn('nama_perusahaan', function ($data) {
                return '<a href="'.route('leads.view',$data->leads_id).'" style="font-weight:bold;color:#000056">'.$data->nama_perusahaan.'</a>';
            })
            ->rawColumns(['aksi','nomor','nama_perusahaan'])
            ->make(true);
    }

    
    public function addDetailHC(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('id',$request->quotation_kebutuhan_id)->first();
            $kebutuhan = DB::table('m_kebutuhan')->where('id',$quotationKebutuhan->kebutuhan_id)->first();
            $kebutuhanD = DB::connection('mysqlhris')->table('m_position')->where('id',$request->jabatan_detail_id)->first();
            $quotation = DB::table('sl_quotation')->where('id',$quotationKebutuhan->quotation_id)->first();

            // cek apakah data sudah ada
            $checkExist = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$quotationKebutuhan->id)->where('kebutuhan_detail_id',$request->jabatan_detail_id)->whereNull('deleted_at')->get();
            if(count($checkExist)>0){
                DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$quotationKebutuhan->id)->where('kebutuhan_detail_id',$request->jabatan_detail_id)->whereNull('deleted_at')->update([
                    'jumlah_hc' => $checkExist[0]->jumlah_hc+$request->jumlah_hc,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                return "Data Berhasil Ditambahkan";
            }else{
                $detailIdBaru = DB::table('sl_quotation_kebutuhan_detail')->insertGetId([
                    'quotation_id' => $quotationKebutuhan->quotation_id,
                    'quotation_kebutuhan_id' => $quotationKebutuhan->id,
                    'kebutuhan_detail_id' => $request->jabatan_detail_id,
                    'kebutuhan' => $kebutuhan->nama,
                    'jabatan_kebutuhan' => $kebutuhanD->name,
                    'jumlah_hc' => $request->jumlah_hc,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                //masukkan tunjangan
                $listTunjangan = DB::table('m_kebutuhan_detail_tunjangan')->whereNull('deleted_at')->where('kebutuhan_detail_id',$request->jabatan_detail_id)->get();
                //apabila ada THR masukkan THR

                if (count($listTunjangan)>0) {
                    foreach ($listTunjangan as $key => $tunjangan) {
                        DB::table('sl_quotation_kebutuhan_detail_tunjangan')->insert([
                            'quotation_id' => $quotationKebutuhan->quotation_id,
                            'quotation_kebutuhan_id' => $quotationKebutuhan->id,
                            'quotation_kebutuhan_detail_id' => $detailIdBaru,
                            'tunjangan_id' =>$tunjangan->tunjangan_id,
                            'nama_tunjangan' => $tunjangan->nama,
                            'nominal' => $tunjangan->nominal,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }            
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addQuotationTraining(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation_training')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
            
            $arrTrainingId = explode(",",$request->training_id);
            foreach ($arrTrainingId as $key => $value) {
                $dTraining = DB::table('m_training')->where('id',$value)->first();
                DB::table('sl_quotation_training')->insert([
                    'training_id' => $value,
                    'quotation_id' =>$request->quotation_id,
                    'nama' => $dTraining->nama,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            $listTrain = "";
            $dataTraining = DB::table('sl_quotation_training')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->get();
            foreach ($dataTraining as $key => $value) {
                if($key != 0){
                    $listTrain .= ", ";
                };
                
                $listTrain .= $value->nama;
            }

            return $listTrain;
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addBarang(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $barang = $request->barang;
            $jenis = $request->jenis;
            $jenisBarang = DB::table('m_jenis_barang')->where('id',$jenis)->first();

            DB::table('m_barang')->insert([
                'nama' => $barang,
                'jenis_barang_id' => $jenisBarang->id,
                'jenis_barang' => $jenisBarang->nama,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $namaTunjangan = $request->namaTunjangan;
            $nominalTunjangan = $request->nominalTunjangan;
            $kebutuhanDetailId = $request->kebutuhanDetailId;

            $nominalTunjangan = str_replace(".","",$nominalTunjangan);

            $kebutuhanDetail = DB::table('sl_quotation_kebutuhan_detail')->where('id',$kebutuhanDetailId)->first();

            DB::table('sl_quotation_kebutuhan_detail_tunjangan')->insert([
                'quotation_id' => $kebutuhanDetail->quotation_id,
                'quotation_kebutuhan_id' => $kebutuhanDetail->quotation_kebutuhan_id,
                'quotation_kebutuhan_detail_id' => $kebutuhanDetail->id,
                'tunjangan_id' => null,
                'nama_tunjangan' => $namaTunjangan,
                'nominal' => $nominalTunjangan,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addDetailPic(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $nama = $request->nama;
            $jabatan = $request->jabatan;
            $no_telp = $request->no_telp;
            $email = $request->email;
            $quotation_id = $request->quotation_id;

            $quotation = DB::table('sl_quotation')->where('id',$quotation_id)->first();
            $jabatan = DB::table('m_jabatan_pic')->where('id',$jabatan)->first();

            DB::table('sl_quotation_pic')->insert([
                'quotation_id' => $quotation->id,
                'leads_id' => $quotation->leads_id,
                'nama' => $nama,
                'jabatan_id' => $jabatan->id,
                'jabatan' => $jabatan->nama,
                'no_telp' => $no_telp,
                'email' => $email,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailHC(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_detail')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_kebutuhan_detail_tunjangan')->where('quotation_kebutuhan_detail_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function deleteTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation_kebutuhan_detail_tunjangan')->where('quotation_id',$request->quotation_id)->where('nama_tunjangan',$request->nama)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function listDetailHC (Request $request){
        $data = DB::table('sl_quotation_kebutuhan_detail')
        ->where('sl_quotation_kebutuhan_detail.quotation_kebutuhan_id',$request->quotation_kebutuhan_id)
        ->whereNull('sl_quotation_kebutuhan_detail.deleted_at')->get();
        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'" data-kebutuhan="'.$data->quotation_kebutuhan_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }
    
    public function listDetailPic (Request $request){
        $quotation = DB::table('sl_quotation')->where('id',$request->quotation_id)->first();
        
        $data = DB::table('sl_quotation_pic')
        ->where('quotation_id',$request->quotation_id)
        ->whereNull('deleted_at')
        ->select('is_kuasa','id','nama','jabatan','no_telp','email')
        ->get();

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->addColumn('kuasa', function ($data) {
            if($data->id==0){
                return "";
            }

            $checked = "";

            if ($data->is_kuasa==1) {
                $checked = "checked";
            }
            return '<input name="is_kuasa[]" class="form-check-input set-is-kuasa" type="radio" value="" data-id="'.$data->id.'" '.$checked.' >';
        })
        ->rawColumns(['aksi','kuasa'])
        ->make(true);
    }

    public function changeKuasaPic(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_pic')->where('quotation_id',$request->quotation_id)->update([
                'is_kuasa' => 0,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('sl_quotation_pic')->where('id',$request->id)->update([
                'is_kuasa' => 1,
                'updated_by' => Auth::user()->full_name
            ]);
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailPic(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_pic')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function changeKota (Request $request){
        $data = DB::connection('mysqlhris')->table('m_city')->where('province_id',$request->province_id)->get();
        foreach ($data as $key => $value) {
            $dataUmk = DB::table("m_umk")->whereNull('deleted_at')->where('city_id',$value->id)->first();
            $value->umk = "Rp. 0";
            if($dataUmk !=null){
                $value->umk = "Rp. ".number_format($dataUmk->umk,0,",",".");
            }
        }
        return $data;
    }

    public function listQuotationKerjasama (Request $request){
        $data = DB::table('sl_quotation_kerjasama')->where('quotation_id',$request->quotation_id)->whereNull('deleted_at')->get();
        
        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        };
        
        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if($data->is_delete==1){
                return '<div class="justify-content-center d-flex">
                    <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                </div>';
            }else{
                return '';
            }
        })
        ->rawColumns(['aksi','perjanjian'])
        ->make(true);
    }

    public function addQuotationKerjasama(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kerjasama')->insert([
                'quotation_id' => $request->quotation_id,
                'perjanjian' => $request->perjanjian,
                'is_delete' => 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function deleteQuotation(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function approveQuotation(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            if(in_array(Auth::user()->role_id,[96])){
                DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                    'ot1' => Auth::user()->full_name,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                //ambil quotation
                $data = DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->first();
                $master = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
                if($master->top=="Kurang Dari 7 Hari"){
                    DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                        'is_aktif' => 1,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);

                    DB::table('sl_quotation')->where('id',$data->quotation_id)->update([
                        'status_quotation_id' => 3,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }
            }else if(in_array(Auth::user()->role_id,[97])){
                DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                    'ot2' => Auth::user()->full_name,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else if(in_array(Auth::user()->role_id,[99])){
                DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                    'ot3' => Auth::user()->full_name,
                    'is_aktif' => 1,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                DB::table('sl_quotation')->where('id',$data->quotation_id)->update([
                    'status_quotation_id' => 3,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function deleteQuotationKerjasama(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kerjasama')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addDetailKaporlap (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->get();

            foreach ($data as $key => $value) {
                if($request['jumlah'.$value->id] !=null && $request['jumlah'.$value->id] !=""){
                    $dataExist = DB::table("sl_quotation_kebutuhan_kaporlap")
                    ->whereNull('deleted_at')
                    ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                    ->where('quotation_kebutuhan_detail_id',$value->id)
                    ->where('barang_id',$request->barang)
                    ->first();

                    $barang = DB::table('m_barang')->where('id',$request->barang)->first();
                    if($dataExist!=null){
                        DB::table("sl_quotation_kebutuhan_kaporlap")
                            ->whereNull('deleted_at')
                            ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                            ->where('quotation_kebutuhan_detail_id',$value->id)
                            ->where('barang_id',$request->barang)->update([
                                    'jumlah' => $dataExist->jumlah+(int)$request['jumlah'.$value->id],
                                    'harga' => $barang->harga,
                                    'nama' => $barang->nama,
                                    'jenis_barang' => $barang->jenis_barang,
                                    'updated_at' => $current_date_time,
                                    'updated_by' => Auth::user()->full_name
                            ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_kaporlap')->insert([
                            'quotation_kebutuhan_detail_id' => $value->id,
                            'quotation_kebutuhan_id' => $value->quotation_kebutuhan_id,
                            'quotation_id' => $value->quotation_id,
                            'barang_id' => $request->barang,
                            'jumlah' => $request['jumlah'.$value->id],
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return "Data Gagal Ditambahkan";

            abort(500);
        }
    }

    public function listKaporlap (Request $request){
        $raw = ['aksi'];
        $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_kebutuhan_kaporlap.quotation_kebutuhan_id,sl_quotation_kebutuhan_kaporlap.barang_id,sl_quotation_kebutuhan_kaporlap.jenis_barang,sl_quotation_kebutuhan_kaporlap.nama,sl_quotation_kebutuhan_kaporlap.harga 
from sl_quotation_kebutuhan_kaporlap 
INNER JOIN m_barang ON sl_quotation_kebutuhan_kaporlap.barang_id = m_barang.id
WHERE sl_quotation_kebutuhan_kaporlap.deleted_at is null 
and quotation_kebutuhan_id = $request->quotation_kebutuhan_id
ORDER BY m_barang.jenis_barang_id asc,sl_quotation_kebutuhan_kaporlap.nama ASC;");

$total =DB::select("select sum(harga*jumlah) as total from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id = $request->quotation_kebutuhan_id")[0]->total;
$objectTotal = (object) ['jenis_barang_id' => 100,
'quotation_kebutuhan_id' => 0,
'barang_id' => 0,
'jenis_barang' => 'TOTAL',
'nama' => '',
'harga' => $total];

        array_push($data,$objectTotal);
        $dt = DataTables::of($data)
        ->addColumn('aksi', function ($data){
            if($data->barang_id==0){
                return null;
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-kebutuhan="'.$data->quotation_kebutuhan_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        });
        $dt = $dt->editColumn('harga', function ($data){
            return "Rp ".number_format($data->harga,0,",",".");
        });

        $dataDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->whereNull('deleted_at')->get();

        foreach ($dataDetail as $key => $value) {
            $dt = $dt->addColumn("data-$value->id", function ($data) use ($value) {
                $dataD = DB::select("select jumlah from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id = $data->quotation_kebutuhan_id and quotation_kebutuhan_detail_id = $value->id and barang_id = $data->barang_id");
                if(count($dataD)>0){
                    return $dataD[0]->jumlah;
                }else{
                    return "";
                };
            });
        };

        $dt = $dt->rawColumns($raw);
        $dt = $dt->make(true);

        return $dt;
    }

    public function deleteDetailKaporlap(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_kaporlap')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // AJAX OHC
    public function addDetailOhc (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->get();

            foreach ($data as $key => $value) {
                if($request->jumlah !=null && $request->jumlah !=""){
                    $dataExist = DB::table("sl_quotation_kebutuhan_ohc")
                    ->whereNull('deleted_at')
                    ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                    ->where('barang_id',$request->barang)
                    ->first();

                    $barang = DB::table('m_barang')->where('id',$request->barang)->first();
                    $harga = str_replace(".","",$request->harga);
                    if($dataExist!=null){
                        DB::table("sl_quotation_kebutuhan_ohc")
                            ->whereNull('deleted_at')
                            ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                            ->where('quotation_kebutuhan_detail_id',$value->id)
                            ->where('barang_id',$request->barang)->update([
                                    'jumlah' => $dataExist->jumlah+(int)$request->jumlah,
                                    'harga' => $harga,
                                    'nama' => $barang->nama,
                                    'jenis_barang' => $barang->jenis_barang,
                                    'updated_at' => $current_date_time,
                                    'updated_by' => Auth::user()->full_name
                            ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_ohc')->insert([
                            'quotation_kebutuhan_detail_id' => $value->id,
                            'quotation_kebutuhan_id' => $value->quotation_kebutuhan_id,
                            'quotation_id' => $value->quotation_id,
                            'barang_id' => $request->barang,
                            'jumlah' => $request->jumlah,
                            'harga' => $harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return "Data Gagal Ditambahkan";

            abort(500);
        }
    }

    public function listOhc (Request $request){
        $raw = ['aksi'];
        $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_kebutuhan_ohc.jumlah,sl_quotation_kebutuhan_ohc.quotation_kebutuhan_id,sl_quotation_kebutuhan_ohc.barang_id,sl_quotation_kebutuhan_ohc.jenis_barang,sl_quotation_kebutuhan_ohc.nama,sl_quotation_kebutuhan_ohc.harga 
from sl_quotation_kebutuhan_ohc 
INNER JOIN m_barang ON sl_quotation_kebutuhan_ohc.barang_id = m_barang.id
WHERE sl_quotation_kebutuhan_ohc.deleted_at is null 
and quotation_kebutuhan_id = $request->quotation_kebutuhan_id
ORDER BY m_barang.jenis_barang_id asc,sl_quotation_kebutuhan_ohc.nama ASC;");

$total =DB::select("select sum(harga*jumlah) as total from sl_quotation_kebutuhan_ohc WHERE deleted_at is null and quotation_kebutuhan_id = $request->quotation_kebutuhan_id")[0]->total;
$objectTotal = (object) ['jenis_barang_id' => 100,
'quotation_kebutuhan_id' => 0,
'barang_id' => 0,
'jenis_barang' => 'TOTAL',
'nama' => '',
'jumlah' => '',
'harga' => $total];

        array_push($data,$objectTotal);
        $dt = DataTables::of($data)
        ->addColumn('aksi', function ($data){
            if($data->barang_id==0){
                return null;
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-kebutuhan="'.$data->quotation_kebutuhan_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        });
        $dt = $dt->editColumn('harga', function ($data){
            return "Rp ".number_format($data->harga,0,",",".");
        });

        $dt = $dt->rawColumns($raw);
        $dt = $dt->make(true);

        return $dt;
    }

    public function deleteDetailOhc(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_ohc')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    // END OF AJAX OHC

    // AJAX DEVICES
    public function addDetailDevices (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->get();

            foreach ($data as $key => $value) {
                if($request['jumlah'.$value->id] !=null && $request['jumlah'.$value->id] !=""){
                    $dataExist = DB::table("sl_quotation_kebutuhan_devices")
                    ->whereNull('deleted_at')
                    ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                    ->where('quotation_kebutuhan_detail_id',$value->id)
                    ->where('barang_id',$request->barang)
                    ->first();

                    $barang = DB::table('m_barang')->where('id',$request->barang)->first();
                    if($dataExist!=null){
                        DB::table("sl_quotation_kebutuhan_devices")
                            ->whereNull('deleted_at')
                            ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                            ->where('quotation_kebutuhan_detail_id',$value->id)
                            ->where('barang_id',$request->barang)->update([
                                    'jumlah' => $dataExist->jumlah+(int)$request['jumlah'.$value->id],
                                    'harga' => $barang->harga,
                                    'nama' => $barang->nama,
                                    'jenis_barang' => $barang->jenis_barang,
                                    'updated_at' => $current_date_time,
                                    'updated_by' => Auth::user()->full_name
                            ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_devices')->insert([
                            'quotation_kebutuhan_detail_id' => $value->id,
                            'quotation_kebutuhan_id' => $value->quotation_kebutuhan_id,
                            'quotation_id' => $value->quotation_id,
                            'barang_id' => $request->barang,
                            'jumlah' => $request['jumlah'.$value->id],
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return "Data Gagal Ditambahkan";

            abort(500);
        }
    }

    public function listDevices (Request $request){
        $raw = ['aksi'];
        $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_kebutuhan_devices.quotation_kebutuhan_id,sl_quotation_kebutuhan_devices.barang_id,sl_quotation_kebutuhan_devices.jenis_barang,sl_quotation_kebutuhan_devices.nama,sl_quotation_kebutuhan_devices.harga 
from sl_quotation_kebutuhan_devices 
LEFT JOIN m_barang ON sl_quotation_kebutuhan_devices.barang_id = m_barang.id
WHERE sl_quotation_kebutuhan_devices.deleted_at is null 
and quotation_kebutuhan_id = $request->quotation_kebutuhan_id
ORDER BY m_barang.jenis_barang_id asc,sl_quotation_kebutuhan_devices.nama ASC;");

$total =DB::select("select sum(harga*jumlah) as total from sl_quotation_kebutuhan_devices WHERE deleted_at is null and quotation_kebutuhan_id = $request->quotation_kebutuhan_id")[0]->total;
$objectTotal = (object) ['jenis_barang_id' => 100,
'quotation_kebutuhan_id' => 0,
'barang_id' => 0,
'jenis_barang' => 'TOTAL',
'nama' => '',
'harga' => $total];

        array_push($data,$objectTotal);
        $dt = DataTables::of($data)
        ->addColumn('aksi', function ($data){
            if($data->barang_id==0){
                return null;
            }
            if(in_array($data->barang_id,[193,194,195,196])){
                return null;
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-kebutuhan="'.$data->quotation_kebutuhan_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        });
        $dt = $dt->editColumn('harga', function ($data){
            return "Rp ".number_format($data->harga,0,",",".");
        });

        $dataDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->whereNull('deleted_at')->get();

        foreach ($dataDetail as $key => $value) {
            $dt = $dt->addColumn("data-$value->id", function ($data) use ($value) {
                $dataD = DB::select("select jumlah from sl_quotation_kebutuhan_devices WHERE deleted_at is null and quotation_kebutuhan_id = $data->quotation_kebutuhan_id and quotation_kebutuhan_detail_id = $value->id and barang_id = $data->barang_id");
                if(count($dataD)>0){
                    return $dataD[0]->jumlah;
                }else{
                    return "";
                };
            });
        };

        $dt = $dt->rawColumns($raw);
        $dt = $dt->make(true);

        return $dt;
    }

    public function deleteDetailDevices(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    // END OF AJAX DEVICES

    // AJAX CHEMICAL

    public function addDetailChemical (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->get();

            foreach ($data as $key => $value) {
                if($request['jumlah'.$value->id] !=null && $request['jumlah'.$value->id] !=""){
                    $dataExist = DB::table("sl_quotation_kebutuhan_chemical")
                    ->whereNull('deleted_at')
                    ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                    ->where('quotation_kebutuhan_detail_id',$value->id)
                    ->where('barang_id',$request->barang)
                    ->first();

                    $barang = DB::table('m_barang')->where('id',$request->barang)->first();
                    if($dataExist!=null){
                        DB::table("sl_quotation_kebutuhan_chemical")
                            ->whereNull('deleted_at')
                            ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                            ->where('quotation_kebutuhan_detail_id',$value->id)
                            ->where('barang_id',$request->barang)->update([
                                    'jumlah' => $dataExist->jumlah+(int)$request['jumlah'.$value->id],
                                    'harga' => $barang->harga,
                                    'nama' => $barang->nama,
                                    'jenis_barang' => $barang->jenis_barang,
                                    'updated_at' => $current_date_time,
                                    'updated_by' => Auth::user()->full_name
                            ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_chemical')->insert([
                            'quotation_kebutuhan_detail_id' => $value->id,
                            'quotation_kebutuhan_id' => $value->quotation_kebutuhan_id,
                            'quotation_id' => $value->quotation_id,
                            'barang_id' => $request->barang,
                            'jumlah' => $request['jumlah'.$value->id],
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return "Data Gagal Ditambahkan";

            abort(500);
        }
    }

    public function listChemical (Request $request){
        $raw = ['aksi'];
        $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_kebutuhan_chemical.quotation_kebutuhan_id,sl_quotation_kebutuhan_chemical.barang_id,sl_quotation_kebutuhan_chemical.jenis_barang,sl_quotation_kebutuhan_chemical.nama,sl_quotation_kebutuhan_chemical.harga 
from sl_quotation_kebutuhan_chemical 
INNER JOIN m_barang ON sl_quotation_kebutuhan_chemical.barang_id = m_barang.id
WHERE sl_quotation_kebutuhan_chemical.deleted_at is null 
and quotation_kebutuhan_id = $request->quotation_kebutuhan_id
ORDER BY m_barang.jenis_barang_id asc,sl_quotation_kebutuhan_chemical.nama ASC;");

$total =DB::select("select sum(harga*jumlah) as total from sl_quotation_kebutuhan_chemical WHERE deleted_at is null and quotation_kebutuhan_id = $request->quotation_kebutuhan_id")[0]->total;
$objectTotal = (object) ['jenis_barang_id' => 100,
'quotation_kebutuhan_id' => 0,
'barang_id' => 0,
'jenis_barang' => 'TOTAL',
'nama' => '',
'harga' => $total];

        array_push($data,$objectTotal);
        $dt = DataTables::of($data)
        ->addColumn('aksi', function ($data){
            if($data->barang_id==0){
                return null;
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-kebutuhan="'.$data->quotation_kebutuhan_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        });
        $dt = $dt->editColumn('harga', function ($data){
            return "Rp ".number_format($data->harga,0,",",".");
        });

        $dataDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->whereNull('deleted_at')->get();

        foreach ($dataDetail as $key => $value) {
            $dt = $dt->addColumn("data-$value->id", function ($data) use ($value) {
                $dataD = DB::select("select jumlah from sl_quotation_kebutuhan_chemical WHERE deleted_at is null and quotation_kebutuhan_id = $data->quotation_kebutuhan_id and quotation_kebutuhan_detail_id = $value->id and barang_id = $data->barang_id");
                if(count($dataD)>0){
                    return $dataD[0]->jumlah;
                }else{
                    return "";
                };
            });
        };

        $dt = $dt->rawColumns($raw);
        $dt = $dt->make(true);

        return $dt;
    }

    public function deleteDetailChemical(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_chemical')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addBiayaMonitoring(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_detail')->where('id',$request->detailId)->update([
                'biaya_monitoring_kontrol' => $request-> nominal,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // END OF AJAX CHEMICAL
    
    public function generateNomor ($leadsId,$companyId){
        // generate nomor QUOT/SIG/AAABB-092024-00001
        $now = Carbon::now();

        $nomor = "QUOT/";
        $dataLeads = DB::table('sl_leads')->where('id',$leadsId)->first();
        $company = DB::connection('mysqlhris')->table('m_company')->where('id',$companyId)->first();
        if($company != null){
            $nomor = $nomor.$company->code."/";
            $nomor = $nomor.$dataLeads->nomor."-";
        }else{
            $nomor = $nomor."NN/NNNNN-";
        }

        $month = $now->month;
        if($month<10){
            $month = "0".$month;
        }

        $urutan = "00001";

        $jumlahData = DB::select("select * from sl_quotation_kebutuhan where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

    public function listDetailRequirement (Request $request){        
        $data = DB::table('sl_quotation_kebutuhan_detail_requirement')
        ->where('quotation_kebutuhan_detail_id',$request->quotation_kebutuhan_detail_id)
        ->whereNull('deleted_at')
        ->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-detail="'.$data->quotation_kebutuhan_detail_id.'" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function deleteDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_detail_requirement')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $requirement = $request->requirement;
            $quotationKebutuhanDetailId = $request->quotation_kebutuhan_detail_id;

            $data = DB::table('sl_quotation_kebutuhan_detail')->where('id',$quotationKebutuhanDetailId)->first();
            DB::table('sl_quotation_kebutuhan_detail_requirement')->insert([
                'quotation_id' => $data->quotation_id,
                'quotation_kebutuhan_id' => $data->quotation_kebutuhan_id,
                'quotation_kebutuhan_detail_id' => $data->id,
                'requirement' => $requirement,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }
    public function cetakCoss (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table('sl_quotation_kebutuhan')->where('id',$id)->first();
            $data->detail = DB::table('sl_quotation_kebutuhan_detail')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$id)->get();
            $data->totalHc = 0;
            $data->umk = 0;
            $data->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$data->quotation_id)->first();

            foreach ($data->detail as $key => $value) {
                $data->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($data->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->whereNull('deleted_at')->where('city_id',$data->kota_id)->first();

                if($dataUmk!=null){
                    $data->umk = $dataUmk->umk;
                }
            }
            
            $master = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
            $quotation = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();

            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');

            //format
            $master->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$master->mulai_kontrak)->isoFormat('D MMMM Y');
            $master->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$master->kontrak_selesai)->isoFormat('D MMMM Y');
            $master->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$master->tgl_penempatan)->isoFormat('D MMMM Y');
            $master->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$master->created_at)->isoFormat('D MMMM Y');
            $master->stgl_quotation = Carbon::createFromFormat('Y-m-d',$master->tgl_quotation)->isoFormat('D MMMM Y');

            $master->salary_rule = "";
            $salaryRuleList = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();
            if($salaryRuleList != null){
                $master->salary_rule = $salaryRuleList->nama_salary_rule;
            }

            $data->manajemen_fee = "";
            $manajemenFeeList = DB::table('m_management_fee')->where('id',$data->management_fee_id)->first();
            if($manajemenFeeList != null){
                $data->manajemen_fee = $manajemenFeeList->nama;
            }

            $aplikasiPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$id)->get();
            foreach ($aplikasiPendukung as $key => $value) {
                $app = DB::table('m_aplikasi_pendukung')->where('id',$value->aplikasi_pendukung_id)->first();
                $value->link_icon = $app->link_icon;
            }

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_kaporlap where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_ohc where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_devices where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_chemical where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->where('jumlah',1)->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_kebutuhan_ohc')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_kebutuhan_chemical')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();

            // tambahan hitungan
            $quotationKebutuhan = 
            DB::table("sl_quotation_kebutuhan")
            ->join('m_kebutuhan','m_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id')
            ->whereNull('sl_quotation_kebutuhan.deleted_at')
            ->where('sl_quotation_kebutuhan.id',$id)
            ->orderBy('sl_quotation_kebutuhan.kebutuhan_id','ASC')
            ->select('sl_quotation_kebutuhan.is_aktif','sl_quotation_kebutuhan.nominal_takaful','sl_quotation_kebutuhan.penjamin','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.nomor','sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.nominal_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$value->kebutuhan_id)->whereNull('deleted_at')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama,nominal FROM `sl_quotation_kebutuhan_detail_tunjangan` WHERE deleted_at is null and quotation_id = $master->id");

            $jumlahHc = 0;
            foreach ($quotationKebutuhan[0]->kebutuhan_detail as $jhc) {
                $jumlahHc += $jhc->jumlah_hc;
            }

            $provisi = 12;
            if(!strpos($master->durasi_kerjasama, 'tahun')){
                $provisi = (int)str_replace(" bulan", "", $master->durasi_kerjasama);
            }
            $master->provisi = $provisi;

            foreach ($quotationKebutuhan[0]->kebutuhan_detail as $ikbd => $kbd) {
                // $kbd->daftar_tunjangan = [];
                $totalTunjangan = 0;
                foreach ($daftarTunjangan as $idt => $tunjangan) {
                    $kbd->{$tunjangan->nama} = 0;
                    //cari data tunjangan
                    $dtTunjangan = DB::table('sl_quotation_kebutuhan_detail_tunjangan')->whereNull('deleted_at')->where('quotation_kebutuhan_detail_id',$kbd->id)->where('nama_tunjangan',$tunjangan->nama)->first();
                    if($dtTunjangan != null){
                        $kbd->{$tunjangan->nama} = $dtTunjangan->nominal;

                        $totalTunjangan += $dtTunjangan->nominal;
                    }
                }

                $kbd->nominal_takaful = 0;
                $kbd->bpjs_jkm = 0;
                $kbd->bpjs_jkk = 0;
                $kbd->bpjs_jht = 0;
                $kbd->bpjs_jp = 0;
                $kbd->bpjs_kes = 0;

                if($quotationKebutuhan[0]->penjamin=="Takaful"){
                    $kbd->nominal_takaful = $quotationKebutuhan[0]->nominal_takaful;
                }else{
                    // hitung JKK
                    if($quotationKebutuhan[0]->resiko=="Sangat Rendah"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.24/100;
                    }else if($quotationKebutuhan[0]->resiko=="Rendah"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.54/100;
                    }else if($quotationKebutuhan[0]->resiko=="Sedang"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.89/100;
                    }else if($quotationKebutuhan[0]->resiko=="Tinggi"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.27/100;
                    }else if($quotationKebutuhan[0]->resiko=="Sangat Tinggi"){
                        $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.74/100;
                    };

                    //hitung JKM
                    $kbd->bpjs_jkm = $quotationKebutuhan[0]->nominal_upah*0.3/100;

                    //Hitung JHT
                    if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                        $kbd->bpjs_jht = $quotationKebutuhan[0]->nominal_upah*3.7/100;
                    }else{
                        $kbd->bpjs_jht = 0;
                    }

                    //Hitung JP
                    if($quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                        $kbd->bpjs_jp = $quotationKebutuhan[0]->nominal_upah*2/100;
                    }else {
                        $kbd->bpjs_jp = 0;
                    }

                    $kbd->bpjs_kes = $quotationKebutuhan[0]->nominal_upah*4/100;

                }

                $kbd->tunjangan_hari_raya = 0;
                if($master->thr=="Diprovisikan"){
                    $kbd->tunjangan_hari_raya = $quotationKebutuhan[0]->nominal_upah/$provisi;
                }

                $kbd->kompensasi = 0;
                if($master->kompensasi=="Diprovisikan"){
                    $kbd->kompensasi = $quotationKebutuhan[0]->nominal_upah/$provisi;
                }
                
                $kbd->tunjangan_holiday = 0;
                if($master->tunjangan_holiday=="Flat"){
                    $kbd->tunjangan_holiday = $master->nominal_tunjangan_holiday;
                }else{
                    $kbd->tunjangan_holiday = ($quotationKebutuhan[0]->nominal_upah/173*(14))*15/$provisi;
                }

                $kbd->lembur = 0;
                if($master->lembur=="Flat"){
                    $kbd->lembur = $master->nominal_lembur;
                }else{
                    $kbd->lembur = 0;
                }

                $personilKaporlap = 0;
                $kbdkaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdkaporlap as $ikdbkap => $kdbkap) {
                    $personilKaporlap += ($kdbkap->harga*$kdbkap->jumlah)/$provisi;
                };

                $kbd->personil_kaporlap = $personilKaporlap;

                $personilDevices = 0;
                $kbddevices = DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbddevices as $ikdbdev => $kdbdev) {
                    $personilDevices += ($kdbdev->harga*$kdbdev->jumlah)/$provisi;
                };
                $appPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                foreach ($appPendukung as $kapp => $app) {
                    $personilDevices += ($app->harga*1)/$provisi;
                }

                $kbd->personil_devices = $personilDevices;

                $personilOhc = 0;
                $kbdOhc = DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                foreach ($kbdOhc as $ikdbohc => $kdbohc) {
                    $personilOhc += ($kdbohc->harga*$kdbohc->jumlah/$jumlahHc)/$provisi;
                };

                $kbd->personil_ohc = $personilOhc;

                $personilChemical = 0;
                $kbdChemical = DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdChemical as $ikdbchemical => $kdbchemical) {
                    $personilChemical += ($kdbchemical->harga*$kdbchemical->jumlah)/$provisi;
                };

                $kbd->personil_chemical = $personilChemical;

                // dd($kbd->personil_kaporlap);
                $kbd->total_personil = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;

                $kbd->sub_total_personil = $kbd->total_personil*$kbd->jumlah_hc;
                
                $kbd->management_fee = $kbd->sub_total_personil*$quotationKebutuhan[0]->persentase/100;

                $kbd->grand_total = $kbd->sub_total_personil+$kbd->management_fee;

                $kbd->ppn = 0;
                $kbd->pph = 0;
                if ($master->ppn_pph_dipotong=="Management Fee") {
                    $kbd->ppn = $kbd->management_fee*11/100;
                    $kbd->pph = $kbd->management_fee*(-2/100);
                }else if ($master->ppn_pph_dipotong=="Total Invoice") {
                    $kbd->ppn = $kbd->grand_total*11/100;
                    $kbd->pph = $kbd->grand_total*(-2/100);
                }
                
                $kbd->total_invoice = $kbd->grand_total + $kbd->ppn + $kbd->pph;

                

                $kbd->pembulatan = ceil($kbd->total_invoice / 1000) * 1000;

                // COST STRUCTURE
                $kbd->total_base_manpower = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan;
                $kbd->total_exclude_base_manpower = $kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;;

                $kbd->total_personil_coss = $kbd->total_base_manpower + $kbd->total_exclude_base_manpower + $kbd->biaya_monitoring_kontrol;

                $kbd->sub_total_personil_coss = $kbd->total_personil_coss*$kbd->jumlah_hc;
                
                $kbd->management_fee_coss = $kbd->sub_total_personil_coss*$quotationKebutuhan[0]->persentase/100;

                $kbd->grand_total_coss = $kbd->sub_total_personil_coss+$kbd->management_fee_coss;
                
                $kbd->ppn_coss = 0;
                $kbd->pph_coss = 0;
                if($master->ppn_pph_dipotong =="Management Fee"){
                    $kbd->ppn_coss = $kbd->management_fee_coss*11/100;
                    $kbd->pph_coss = $kbd->management_fee_coss*(-2/100);
                }else  if($master->ppn_pph_dipotong =="Total Invoice"){
                    $kbd->ppn_coss = $kbd->grand_total_coss*11/100;
                    $kbd->pph_coss = $kbd->grand_total_coss*(-2/100);
                }

                $kbd->total_invoice_coss = $kbd->grand_total_coss + $kbd->ppn_coss + $kbd->pph_coss;

                $kbd->pembulatan_coss = ceil($kbd->total_invoice_coss / 1000) * 1000;

            };
            
            $listPic = DB::table('sl_quotation_pic')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            $kebutuhanDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$data->id)->whereNull('deleted_at')->get();
            foreach ($kebutuhanDetail as $kkd => $vkd) {
                $vkd->requirement = DB::table('sl_quotation_kebutuhan_detail_requirement')->where('quotation_kebutuhan_detail_id',$vkd->id)->whereNull('deleted_at')->get();
            }
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();

            return view('sales.quotation.cetakan.coss',compact('quotation','salaryRuleQ','kebutuhanDetail','listPic','daftarTunjangan','quotationKebutuhan','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','data','master','leads','aplikasiPendukung'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function cetakQuotation (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table('sl_quotation_kebutuhan')->where('id',$id)->first();
            $data->detail = DB::table('sl_quotation_kebutuhan_detail')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$id)->get();
            $data->totalHc = 0;
            $data->umk = 0;
            $data->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$data->quotation_id)->first();

            foreach ($data->detail as $key => $value) {
                $data->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($data->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->whereNull('deleted_at')->where('city_id',$data->kota_id)->first();

                if($dataUmk!=null){
                    $data->umk = $dataUmk->umk;
                }
            }
            
            $master = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');

            //format
            $master->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$master->mulai_kontrak)->isoFormat('D MMMM Y');
            $master->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$master->kontrak_selesai)->isoFormat('D MMMM Y');
            $master->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$master->tgl_penempatan)->isoFormat('D MMMM Y');
            $master->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$master->created_at)->isoFormat('D MMMM Y');
            $master->stgl_quotation = Carbon::createFromFormat('Y-m-d',$master->tgl_quotation)->isoFormat('D MMMM Y');

            $master->salary_rule = "";
            $salaryRuleList = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();
            if($salaryRuleList != null){
                $master->salary_rule = $salaryRuleList->nama_salary_rule;
            }

            $data->manajemen_fee = "";
            $manajemenFeeList = DB::table('m_management_fee')->where('id',$data->management_fee_id)->first();
            if($manajemenFeeList != null){
                $data->manajemen_fee = $manajemenFeeList->nama;
            }

            $aplikasiPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$id)->get();
            foreach ($aplikasiPendukung as $key => $value) {
                $app = DB::table('m_aplikasi_pendukung')->where('id',$value->aplikasi_pendukung_id)->first();
                $value->link_icon = $app->link_icon;
            }

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_kaporlap where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_ohc where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_devices where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_chemical where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->where('jumlah',1)->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_kebutuhan_ohc')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_kebutuhan_chemical')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();

            // tambahan hitungan
            $quotationKebutuhan = 
            DB::table("sl_quotation_kebutuhan")
            ->join('m_kebutuhan','m_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id')
            ->whereNull('sl_quotation_kebutuhan.deleted_at')
            ->where('sl_quotation_kebutuhan.id',$id)
            ->orderBy('sl_quotation_kebutuhan.kebutuhan_id','ASC')
            ->select('sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.nomor','sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.nominal_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$value->kebutuhan_id)->whereNull('deleted_at')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            $daftarTunjangan = DB::select("SELECT DISTINCT tunjangan_id,nama FROM `m_kebutuhan_detail_tunjangan` WHERE deleted_at is null and kebutuhan_id =".$quotationKebutuhan[0]->kebutuhan_id);

            foreach ($quotationKebutuhan[0]->kebutuhan_detail as $ikbd => $kbd) {
                // $kbd->daftar_tunjangan = [];
                $totalTunjangan = 0;
                foreach ($daftarTunjangan as $idt => $tunjangan) {
                    $kbd->{$tunjangan->nama} = 0;
                    //cari data tunjangan
                    $dtTunjangan = DB::table('sl_quotation_kebutuhan_detail_tunjangan')->whereNull('deleted_at')->where('quotation_kebutuhan_detail_id',$kbd->id)->where('tunjangan_id',$tunjangan->tunjangan_id)->first();
                    if($dtTunjangan != null){
                        $kbd->{$tunjangan->nama} = $dtTunjangan->nominal;

                        $totalTunjangan += $dtTunjangan->nominal;
                    }
                }
                $kbd->tunjangan_hari_raya = 0;
                if($master->thr=="Diprovisikan"){
                    $kbd->tunjangan_hari_raya = $quotationKebutuhan[0]->nominal_upah/12;
                }

                // hitung JKK
                if($quotationKebutuhan[0]->resiko=="Sangat Rendah"){
                    $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.24/100;
                }else if($quotationKebutuhan[0]->resiko=="Rendah"){
                    $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.54/100;
                }else if($quotationKebutuhan[0]->resiko=="Sedang"){
                    $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*0.89/100;
                }else if($quotationKebutuhan[0]->resiko=="Tinggi"){
                    $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.27/100;
                }else if($quotationKebutuhan[0]->resiko=="Sangat Tinggi"){
                    $kbd->bpjs_jkk = $quotationKebutuhan[0]->nominal_upah*1.74/100;
                };

                //hitung JKM
                $kbd->bpjs_jkm = $quotationKebutuhan[0]->nominal_upah*0.3/100;

                //Hitung JHT
                if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                    $kbd->bpjs_jht = $quotationKebutuhan[0]->nominal_upah*3.7/100;
                }else{
                    $kbd->bpjs_jht = 0;
                }
                
                //Hitung JP
                if($quotationKebutuhan[0]->program_bpjs=="4 BPJS" ){
                    $kbd->bpjs_jp = $quotationKebutuhan[0]->nominal_upah*2/100;
                }else {
                    $kbd->bpjs_jp = 0;
                }

                $kbd->bpjs_kes = $quotationKebutuhan[0]->nominal_upah*4/100;

                $personilKaporlap = 0;
                $kbdkaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdkaporlap as $ikdbkap => $kdbkap) {
                    $personilKaporlap += ($kdbkap->harga*$kdbkap->jumlah);
                };

                $kbd->personil_kaporlap = $personilKaporlap;

                $personilDevices = 0;
                $kbddevices = DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbddevices as $ikdbdev => $kdbdev) {
                    $personilDevices += ($kdbdev->harga*$kdbdev->jumlah);
                };

                $kbd->personil_devices = $personilDevices;

                $personilOhc = 0;
                $kbdOhc = DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdOhc as $ikdbohc => $kdbohc) {
                    $personilOhc += ($kdbohc->harga*$kdbohc->jumlah);
                };

                $kbd->personil_ohc = $personilOhc;

                $personilChemical = 0;
                $kbdChemical = DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->where('quotation_kebutuhan_detail_id',$kbd->id)->get();
                foreach ($kbdChemical as $ikdbchemical => $kdbchemical) {
                    $personilChemical += ($kdbchemical->harga*$kdbchemical->jumlah);
                };

                $kbd->personil_chemical = $personilChemical;

                $kbd->total_personil = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;

                $kbd->sub_total_personil = $kbd->total_personil*$kbd->jumlah_hc;
                
                $kbd->management_fee = $kbd->sub_total_personil*$quotationKebutuhan[0]->persentase/100;

                $kbd->grand_total = $kbd->sub_total_personil+$kbd->management_fee;

                $kbd->ppn = $kbd->management_fee*11/100;

                $kbd->pph = $kbd->management_fee*(-2/100);

                $kbd->total_invoice = $kbd->grand_total + $kbd->ppn + $kbd->pph;

                $kbd->pembulatan = ceil($kbd->total_invoice / 1000) * 1000;

                // COST STRUCTURE
                $kbd->total_base_manpower = $quotationKebutuhan[0]->nominal_upah+$totalTunjangan;
                $kbd->total_exclude_base_manpower = $kbd->tunjangan_hari_raya+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_ohc+$kbd->personil_chemical;;

                $kbd->total_personil_coss = $kbd->total_base_manpower + $kbd->total_exclude_base_manpower + $kbd->biaya_monitoring_kontrol;

                $kbd->sub_total_personil_coss = $kbd->total_personil_coss*$kbd->jumlah_hc;
                
                $kbd->management_fee_coss = $kbd->sub_total_personil_coss*$quotationKebutuhan[0]->persentase/100;

                $kbd->grand_total_coss = $kbd->sub_total_personil_coss+$kbd->management_fee_coss;

                $kbd->ppn_coss = $kbd->management_fee_coss*11/100;

                $kbd->pph_coss = $kbd->management_fee_coss*(-2/100);

                $kbd->total_invoice_coss = $kbd->grand_total_coss + $kbd->ppn_coss + $kbd->pph_coss;

                $kbd->pembulatan_coss = ceil($kbd->total_invoice_coss / 1000) * 1000;

            };
            
            $listPic = DB::table('sl_quotation_pic')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            $kebutuhanDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$data->id)->whereNull('deleted_at')->get();
            foreach ($kebutuhanDetail as $kkd => $vkd) {
                $vkd->requirement = DB::table('sl_quotation_kebutuhan_detail_requirement')->where('quotation_kebutuhan_detail_id',$vkd->id)->whereNull('deleted_at')->get();
            }
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();

            $listKerjasama = DB::table('sl_quotation_kerjasama')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            return view('sales.quotation.cetakan.quotation',compact('listKerjasama','salaryRuleQ','kebutuhanDetail','listPic','daftarTunjangan','quotationKebutuhan','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','data','master','leads','aplikasiPendukung'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}
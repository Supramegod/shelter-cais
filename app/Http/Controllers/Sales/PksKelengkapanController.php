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
use App\Http\Controllers\Helper\QuotationService;
use App\Http\Controllers\Sales\CustomerActivityController;
use App\Http\Controllers\Sales\QuotationController;

class PksKelengkapanController extends Controller
{
    public function add ($pksId){
        try {
            $pks = DB::table('sl_pks')->where('id',$pksId)->first();
            if($pks== null){
                return view('errors.404');
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $province = DB::connection('mysqlhris')->table('m_province')->get();
            $kota = [];
            if($pks->kota_id != null){
                $kota = DB::connection('mysqlhris')->table('m_city')->where('province_id',$pks->provinsi_id)->get();
            }

            $leads = null;
            if($pks->leads_id != null) {
                $leads = DB::table('sl_leads')->where('id',$pks->leads_id)->first();
            }

            return view('sales.lengkapi-quotation.add',compact('kota','pks','now','company','province','leads'));
        } catch (\Exception $e) {
            dd($e);
            abort(500);
        }
    }

    public function step(Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            $quotation->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$quotation->kebutuhan_id)->orderBy('name','asc')->get();
            $quotation->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            $quotation->quotation_site = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();

            $province = DB::connection('mysqlhris')->table('m_province')->get();
            // $dataProvinsi = DB::connection('mysqlhris')->table('m_province')->where('id',$quotation->provinsi_id)->first();
            // $dataKota = DB::connection('mysqlhris')->table('m_city')->where('id',$quotation->kota_id)->first();

            // $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('province_id',$dataProvinsi->id)->first();
            // $dataProvinsi->ump = "Rp. 0";
            // if($dataUmp !=null){
            //     $dataProvinsi->ump = "Rp. ".number_format($dataUmp->ump,0,",",".");
            // }
            // $dataUmk = DB::table("m_umk")->whereNull('deleted_at')->where('city_id',$dataKota->id)->first();
            // $dataKota->umk = "Rp. 0";
            // if($dataUmk !=null){
            //     $dataKota->umk = "Rp. ".number_format($dataUmk->umk,0,",",".");
            // }

            // foreach ($province as $key => $value) {
            //     $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('province_id',$value->id)->first();
            //     $value->ump = "Rp. 0";
            //     if($dataUmp !=null){
            //         $value->ump = "Rp. ".number_format($dataUmp->ump,0,",",".");
            //     }
            // }
            $kota = DB::connection('mysqlhris')->table('m_city')->get();
            $manfee = DB::table('m_management_fee')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();

            //step 6 - aplikasi pendukung
            $aplikasiPendukung = null;
            $arrAplikasiSel = [];
            if($request->step==6){
                $aplikasiPendukung = DB::table('m_aplikasi_pendukung')->whereNull('deleted_at')->get();
                $listApp = DB::table('sl_quotation_aplikasi')->where('quotation_id',$id)->whereNull('deleted_at')->get();

                foreach ($listApp as $key => $value) {
                    array_push($arrAplikasiSel,$value->aplikasi_pendukung_id);
                }
            }

            //step 7 - kaporlap
            $listKaporlap = null;
            $listJenis = [];
            if($request->step==7){
                $arrKaporlap = [1,2,3,4,5];
                if($quotation->kebutuhan_id != 1){
                    $arrKaporlap = [5];
                }

                $listJenis = DB::table('m_jenis_barang')->whereIn('id',$arrKaporlap)->get();
                $listKaporlap = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',$arrKaporlap)
                                    ->orderBy("urutan","asc")
                                    ->orderBy("nama","asc")
                                    ->get();

                foreach ($listKaporlap as $key => $kaporlap) {
                    foreach ($quotation->quotation_detail as $kKd => $vKd) {
                        $kaporlap->{'jumlah_'.$vKd->id} = 0;
                        $kebkap = DB::table('sl_quotation_kaporlap')->whereNull('deleted_at')->where('barang_id',$kaporlap->id)->where('quotation_detail_id',$vKd->id)->first();
                        if($kebkap !=null){
                            $kaporlap->{'jumlah_'.$vKd->id} = $kebkap->jumlah;
                        }
                    }
                }
            }
            //step 8 - devices
            $listDevices = null;
            if($request->step==8){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[9,10,11,12,17])->get();
                $listDevices = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[8,9,10,11,12,17])
                                    ->orderBy("urutan","asc")
                                    ->orderBy("nama","asc")
                                    ->get();

                foreach ($listDevices as $key => $devices) {
                    $devices->jumlah = 0;
                    $kebkap = DB::table('sl_quotation_devices')->whereNull('deleted_at')->where('barang_id',$devices->id)->where('quotation_id',$id)->first();
                    if($kebkap !=null){
                        $devices->jumlah = $kebkap->jumlah;
                    }
                }
            }

            //step 9 - chemical
            $listChemical = null;
            if($request->step==9){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[13,14,15,16,18,19])->get();
                $listChemical = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[13,14,15,16,18,19])
                                    ->orderBy("urutan","asc")
                                    ->orderBy("nama","asc")
                                    ->get();
                foreach ($listChemical as $key => $value) {
                    $value->harga = number_format($value->harga,0,",",".");
                }
            }


            //step 10 ohc
            $listOhc = null;
            if($request->step==10){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[6,7,8])->get();
                $listOhc = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[6,7,8])
                                    ->orderBy("urutan","asc")
                                    ->orderBy("nama","asc")
                                    ->get();
                foreach ($listOhc as $key => $value) {
                    $value->harga = number_format($value->harga,0,",",".");
                }
            }

            // step 11 Harga Jual
            $leads = null;
            $data = null;
            $daftarTunjangan = null;
            $listTraining = [];
            $listTrainingQ = [];
            $calcQuotation = null;

            if($request->step==11){

                $quotationService = new QuotationService();
                $calcQuotation = $quotationService->calculateQuotation($quotation);
                $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");
                $data = DB::table('sl_quotation')->where('id',$quotation->id)->first();
                $data->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
                $data->totalHc = 0;
                foreach ($quotation->quotation_site as $key => $site) {
                    $site->jumlah_detail = 0;
                    foreach ($quotation->quotation_detail as $kd => $vd) {
                        if($vd->quotation_site_id == $site->id){
                            $site->jumlah_detail += 1;
                        }
                    }
                }

                foreach ($data->detail as $key => $value) {
                    $data->totalHc += $value->jumlah_hc;
                }
                $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
            }
            $isEdit = false;

            if(isset($request->edit)){
                $isEdit = true;
            }

            $listJabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
            $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
            $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();

            return view('sales.lengkapi-quotation.edit-'.$request->step,compact('calcQuotation','listJabatanPic','listTrainingQ','listTraining','daftarTunjangan','salaryRuleQ','data','leads','isEdit','listChemical','listDevices','listOhc','listJenis','listKaporlap','jenisPerusahaan','aplikasiPendukung','arrAplikasiSel','manfee','kota','province','quotation','request','company','salaryRule'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save (Request $request){
        try {
            DB::beginTransaction();
            $newId = null;
            $pks = DB::table('sl_pks')->where('id',$request->pks_id)->first();
            $current_date_time = Carbon::now()->toDateTimeString();
            $current_date = Carbon::now()->toDateString();
            $kebutuhan = DB::table('m_kebutuhan')->where('id',$request->layanan)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('id',$request->entitas)->first();
            $leads = DB::table('sl_leads')->where('id',$pks->leads_id)->first();

            $quotationController = new QuotationController();
            $quotationNomor = $quotationController->generateNomor($pks->leads_id,$request->entitas);
            $newId = DB::table('sl_quotation')->insertGetId([
                'nomor' => $quotationNomor,
                'tgl_quotation' => $current_date,
                'leads_id' => $pks->leads_id,
                'jumlah_site' =>  $request->jumlah_site,
                'nama_perusahaan' => $leads->nama_perusahaan,
                'kebutuhan_id' => $request->layanan,
                'kebutuhan' => $kebutuhan->nama,
                'company_id' => $request->entitas,
                'company' => $company->name,
                'step' => 1,
                'status_quotation_id' =>1,
                'pks_id' => $pks->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_pic')->insert([
                'quotation_id' => $newId,
                'leads_id' => $pks->leads_id,
                'nama' => $leads->pic,
                'jabatan_id' => $leads->jabatan_id,
                'jabatan' => $leads->jabatan,
                'no_telp' => $leads->no_telp,
                'email' => $leads->email,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            //SITE
            $province = DB::connection('mysqlhris')->table('m_province')->where('id',$request->provinsi)->first();
            $city = DB::connection('mysqlhris')->table('m_city')->where('id',$request->kota)->first();

            $ump = 0;
            $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('is_aktif',1)->where('province_id',$province->id)->first();
            if($dataUmp !=null){
                $ump = $dataUmp->ump;
            }

            $umk = 0;
            $dataUmk = DB::table("m_umk")->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$city->id)->first();
            if($dataUmk !=null){
                $umk = $dataUmk->umk;
            }

            DB::table('sl_quotation_site')->insert([
                'quotation_id' => $newId,
                'leads_id' => $pks->leads_id,
                'nama_site' => $request->nama_site,
                'provinsi_id' => $province->id,
                'provinsi' => $province->name,
                'kota_id' => $city->id,
                'kota' => $city->name,
                'ump' => $ump,
                'umk' => $umk,
                'penempatan' => $request->penempatan,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($pks->leads_id);

            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $request->perusahaan_id,
                'quotation_id' => $newId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$quotationNomor.' terbentuk dari PKS Nomor :'.$pks->nomor,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            //update PKS nya
            DB::table('sl_pks')->where('id',$pks->id)->update([
                'quotation_id' => $newId,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::commit();

            return redirect()->route('lengkapi-quotation.step',['id'=>$newId,'step'=>'1']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit1 (Request $request){
        try {
            DB::beginTransaction();

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
                'jenis_kontrak' => $request->jenis_kontrak,
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::commit();
            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'2']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
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
                $newStep = 3;
                if($quotation->step>$newStep){
                    $newStep = $quotation->step;
                }
                if($request->edit==1){
                    $newStep = $quotation->step;
                }

                DB::table('sl_quotation')->where('id',$request->id)->update([
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
                    'cuti' => $request->macam_cuti,
                    'hari_cuti_kematian' => $request->hari_cuti_kematian,
                    'hari_istri_melahirkan' => $request->hari_istri_melahirkan,
                    'hari_cuti_menikah' => $request->hari_cuti_menikah,
                    'gaji_saat_cuti' => $request->gaji_saat_cuti,
                    'prorate' => $request->prorate,
                    'shift_kerja' => $request->shift_kerja ,
                    'jam_kerja' => $request->jam_kerja ,
                    'step' => $newStep,
                    // 'mulai_kerja' => $request->mulai_kerja ,
                    // 'selesai_kerja' => $request->selesai_kerja ,
                    // 'sistem_kerja' => $request->sistem_kerja ,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                DB::commit();

                if($request->edit==0){
                    return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'3']);
                }else{
                    return redirect()->route('lengkapi-quotation.view',$request->id);
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
            // $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'4']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$dataQuotation->id);
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
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            $upah = $request['upah'];
            $manfee = $request['manajemen_fee'];
            $presentase = $request['persentase'];
            $hitunganUpah = "Per Bulan";

            //dijadikan acuan
            $nilaiUpah = 0;

            $customUpah = 0;
            if($upah == "Custom"){
                $hitunganUpah = $request['hitungan_upah'];
                $customUpah = str_replace(".","",$request['custom-upah']);

                if($hitunganUpah=="Per Hari"){
                    $customUpah = $customUpah*21;
                }else if ($hitunganUpah=="Per Jam") {
                    $customUpah = $customUpah*21*8;
                }
            }

            $newStep = 5;
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }

            if($request->lembur!="Flat"){
                $request->nominal_lembur = null;
                $request->jenis_bayar_lembur = null;
            }else{
                if ($request->nominal_lembur != null && $request->nominal_lembur != "") {
                    $request->nominal_lembur = str_replace(".", "", $request->nominal_lembur);
                }
            }

            if($request->tunjangan_holiday!="Flat"){
                $request->nominal_tunjangan_holiday = null;
                $request->jenis_bayar_tunjangan_holiday = null;
            }else{
                if ($request->nominal_tunjangan_holiday != null && $request->nominal_tunjangan_holiday != "") {
                    $request->nominal_tunjangan_holiday = str_replace(".", "", $request->nominal_tunjangan_holiday);
                }
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

            //rubah custom upah untuk quotation site
            $quotationSite = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationSite as $key => $site) {
                $nominalUpah = 0;
                if($upah == "Custom"){
                    $nominalUpah = $customUpah;
                }else{
                    //cari ump / umk
                    if($upah =="UMP"){
                        $dataUmp = DB::table("m_ump")->where('is_aktif',1)->whereNull('deleted_at')->where('province_id',$site->provinsi_id)->first();
                        if($dataUmp !=null){
                            $nominalUpah = $dataUmp->ump;
                        }
                    }else if ($upah =="UMK") {
                        $dataUmk = DB::table("m_umk")->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$site->kota_id)->first();
                        if($dataUmk !=null){
                            $nominalUpah = $dataUmk->umk;
                        }
                    }
                }
                DB::table('sl_quotation_site')->where('id',$site->id)->update([
                    'nominal_upah' => $nominalUpah,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                $nilaiUpah = $nominalUpah;
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'upah' => $upah,
                'nominal_upah' => $nilaiUpah,
                'hitungan_upah' => $hitunganUpah,
                'management_fee_id' => $manfee,
                'is_aktif' => 0,
                'persentase' => $presentase,
                'step' => $newStep,
                'thr' => $request->thr,
                'kompensasi' => $request->kompensasi,
                'lembur' => $request->lembur,
                'is_ppn' => $request->is_ppn,
                'ppn_pph_dipotong' => $request->ppn_pph_dipotong,
                'tunjangan_holiday' => $request->tunjangan_holiday,
                'nominal_lembur' => $request->nominal_lembur,
                'nominal_tunjangan_holiday' => $request->nominal_tunjangan_holiday,
                'jenis_bayar_tunjangan_holiday' => $request->jenis_bayar_tunjangan_holiday,
                'jenis_bayar_lembur' => $request->jenis_bayar_lembur,
                'lembur_ditagihkan' => $request->lembur_ditagihkan,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //update semua nominal menjadi upah
            DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->update([
                'nominal_upah' => $nominalUpah,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'5']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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
            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();
            $quotationDetail = DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            $jenisPerusahaanId = $request['jenis-perusahaan'];
            $resiko = $request['resiko'];
            $programBpjs = $request['program-bpjs'];
            $isAktif = 1;

            foreach ($quotationDetail as $key => $value) {
                $penjamin = $request->penjamin[$value->id] ?? null;
                $jkk = $request->jkk[$value->id] ?? null;
                $jkm = $request->jkm[$value->id] ?? null;
                $jht = $request->jht[$value->id] ?? null;
                $jp = $request->jp[$value->id] ?? null;
                $nominalTakaful = $request->nominal_takaful[$value->id] ?? null;

                DB::table('sl_quotation_detail')->where('id',$value->id)->update([
                    'penjamin_kesehatan' => $penjamin,
                    'is_bpjs_jkk' => $jkk=="on" ? 1 : 0,
                    'is_bpjs_jkm' => $jkm=="on" ? 1 : 0,
                    'is_bpjs_jht' => $jht=="on" ? 1 : 0,
                    'is_bpjs_jp' => $jp=="on" ? 1 : 0,
                    'nominal_takaful' => $nominalTakaful,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            $jenisPerusahaan = null;
            if($jenisPerusahaanId != null){
                $jenisPerusahaanList = DB::table('m_jenis_perusahaan')->where('id',$jenisPerusahaanId)->first();
                if($jenisPerusahaanList != null){
                    $jenisPerusahaan = $jenisPerusahaanList->nama;
                }
            }

            $isAktif = $quotation->is_aktif;
            // if($isAktif==2){
            //     if($programBpjs != "4 BPJS"){
            //         $isAktif = 0;
            //     }
            // }

            if($isAktif == 2){
                $isAktif = 1;
            };

            $newStep = 6;
            if($quotation->step>$newStep){
                $newStep = $quotation->step;
            }
            if($request->edit==1){
                $newStep = $quotation->step;
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'jenis_perusahaan_id' => $jenisPerusahaanId,
                'jenis_perusahaan' => $jenisPerusahaan,
                'resiko' => $resiko,
                'is_aktif' => $isAktif,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            // note harga jual
            $note = '
                  <b>Upah pokok base on Umk '.Carbon::now()->year.' </b> <br>
Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP).
<span class="text-danger">Pengalian base on upah</span>		<br>
BPJS Kesehatan. <span class="text-danger">*base on Umk '.Carbon::now()->year.'</span> <br>
<br>
<span class="text-danger">*prosentase Bpjs Tk J. Kecelakaan Kerja disesuaikan dengan tingkat resiko sesuai ketentuan.</span>';
if($quotation->note_harga_jual == null){
    DB::table('sl_quotation')->where('id',$request->id)->update([
        'note_harga_jual' => $note,
    ]);
}

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'6']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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
                $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->get();
                $aplikasiPendukung = $request->aplikasi_pendukung;
                $arrAplikasiId = [];
                foreach ($aplikasiPendukung as $keyd => $valued) {
                    $appdukung = DB::table('m_aplikasi_pendukung')->where('id',$valued)->first();

                    $dataAplikasi = DB::table('sl_quotation_aplikasi')->where('aplikasi_pendukung_id',$valued)->where('quotation_id',$request->id)->whereNull('deleted_at')->first();
                    $quotationDetail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where('quotation_id',$request->id)->get();

                    if($dataAplikasi==null){
                        $appId = DB::table('sl_quotation_aplikasi')->insertGetId([
                            'quotation_id' => $request->id,
                            'aplikasi_pendukung_id' => $valued,
                            'aplikasi_pendukung' => $appdukung->nama,
                            'harga' => $appdukung->harga,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);

                        array_push($arrAplikasiId,$appId);
                    }else{
                        DB::table('sl_quotation_aplikasi')->where('id',$dataAplikasi->id)->update([
                            'quotation_id' => $request->id,
                            'aplikasi_pendukung_id' => $valued,
                            'aplikasi_pendukung' => $appdukung->nama,
                            'harga' => $appdukung->harga,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);

                        array_push($arrAplikasiId,$dataAplikasi->id);
                    }
                }

                DB::table('sl_quotation_aplikasi')->where('quotation_id',$request->id)->whereNotIn('aplikasi_pendukung_id', $aplikasiPendukung)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                DB::table('sl_quotation_devices')->where('quotation_id',$request->id)->whereNotNull('quotation_aplikasi_id')->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                // insert device lagi saja
                $jumlahHc=0;
                foreach ($quotationDetail as $key => $detail) {
                    $jumlahHc+= $detail->jumlah_hc;
                }

                foreach ($arrAplikasiId as $key => $appId) {
                    $quotAplikasi = DB::table('sl_quotation_aplikasi')->where('id',$appId)->first();
                    $appdukung = DB::table('m_aplikasi_pendukung')->where('id',$quotAplikasi->aplikasi_pendukung_id)->first();

                    DB::table('sl_quotation_devices')->insert([
                        'quotation_id' => $request->id,
                        'quotation_aplikasi_id' => $appId,
                        'barang_id' => $appdukung->barang_id,
                        'jumlah' => $jumlahHc,
                        'harga' => $appdukung->harga,
                        'nama' => $appdukung->nama,
                        'jenis_barang' => 'Aplikasi Pendukung',
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }

            }else{
                DB::table('sl_quotation_aplikasi')->where('quotation_id',$request->id)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                DB::table('sl_quotation_devices')->where('quotation_id',$request->id)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);
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

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'7']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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
            $detail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();

            $listKaporlap = DB::table('m_barang')
                                ->whereNull('deleted_at')
                                ->orderBy("nama","asc")
                                ->get();

            //hapus dulu data existing
            DB::table('sl_quotation_kaporlap')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            foreach ($listKaporlap as $key => $value) {
                foreach ($detail as $kd => $vd) {
                    //cek apakah 0 jika 0 skip
                    if($request->{'jumlah'.'_'.$value->id.'_'.$vd->id} == "0" ||$request->{'jumlah'.'_'.$value->id.'_'.$vd->id} == null){
                        continue;
                    }else{
                        //cari harga
                        $barang = DB::table('m_barang')->where('id',$value->id)->first();
                        $jenisBarang = DB::table('m_jenis_barang')->where('id',$barang->jenis_barang_id)->first();
                        DB::table('sl_quotation_kaporlap')->insert([
                            'quotation_detail_id' => $vd->id,
                            'quotation_id' => $quotation->id,
                            'barang_id' => $barang->id,
                            'jumlah' => $request->{'jumlah'.'_'.$value->id.'_'.$vd->id},
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang_id' => $jenisBarang->id,
                            'jenis_barang' => $jenisBarang->nama,
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

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'8']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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

            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();

            $listDevices = DB::table('m_barang')
                                ->whereNull('deleted_at')
                                ->orderBy("nama","asc")
                                ->get();

            //hapus dulu data existing
            DB::table('sl_quotation_devices')->whereNotIn('barang_id',[192,194,195,196])->whereNull('deleted_at')->where('quotation_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            foreach ($listDevices as $key => $value) {
                //cek apakah 0 jika 0 skip
                if($request->{'jumlah'.'_'.$value->id} == "0" ||$request->{'jumlah'.'_'.$value->id} == null){
                    continue;
                }else{
                    //cari harga
                    $barang = DB::table('m_barang')->where('id',$value->id)->first();
                    $jenisBarang = DB::table('m_jenis_barang')->where('id',$barang->jenis_barang_id)->first();
                    DB::table('sl_quotation_devices')->insert([
                        'quotation_id' => $quotation->id,
                        'barang_id' => $barang->id,
                        'jumlah' => $request->{'jumlah'.'_'.$value->id},
                        'harga' => $barang->harga,
                        'nama' => $barang->nama,
                        'jenis_barang_id' => $jenisBarang->id,
                        'jenis_barang' => $jenisBarang->nama,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
            }

            $newStep = 9;
            $dataStep = 9;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();

            if($dataQuotation->step>$newStep){
                $dataStep = $dataQuotation->step;
            }
            if($quotation->kebutuhan_id==2||$quotation->kebutuhan_id==1||$quotation->kebutuhan_id==4){
                $newStep=10;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $dataStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>$newStep]);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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

            $newStep = 10;
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

            // $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPP($data->id);

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'10']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$dataQuotation->id);
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

            if($request->ada_training=="Tidak Ada"){
                $request->training ="0";
            }

            $persenBungaBank = $dataQuotation->persen_bunga_bank;
            if($dataQuotation->persen_bunga_bank != 0 && $dataQuotation->persen_bunga_bank != null){
                $persenBungaBank = 1.3;
            };

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'kunjungan_operasional' => $request->jumlah_kunjungan_operasional." ".$request->bulan_tahun_kunjungan_operasional ,
                'kunjungan_tim_crm' => $request->jumlah_kunjungan_tim_crm." ".$request->bulan_tahun_kunjungan_tim_crm ,
                'keterangan_kunjungan_operasional' => $request->keterangan_kunjungan_operasional ,
                'keterangan_kunjungan_tim_crm' => $request->keterangan_kunjungan_tim_crm ,
                'training' => $request->training ,
                'persen_bunga_bank' => $persenBungaBank,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            if($request->edit==0){
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'11']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
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


            // tambah approval dir sales
            // $dataQuotationD =  DB::table('sl_quotation_d')->where('quotation_id',$request->id)->get();

            $isAktif = $dataQuotation->is_aktif;
            // foreach ($dataQuotationD as $key => $dataD) {
            //     if($dataD->biaya_monitoring > 0 ){
            //         $isAktif=0;
            //     }
            // }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'is_aktif' => $isAktif,
                'step' => $newStep,
                'penagihan' => $request->penagihan,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);


            //tambah perjanjian
            //hapus dulu perjanjian yg lama atau kalau ada
            DB::table('sl_quotation_kerjasama')->where('quotation_id',$request->id)->whereNull('deleted_at')->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            $kebutuhanPerjanjian = "<b>".$dataQuotation->kebutuhan."</b>";
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
            $appPendukung = DB::table('sl_quotation_aplikasi')->whereNull('deleted_at')->where('quotation_id',$request->id)->get();
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
                return redirect()->route('lengkapi-quotation.step',['id'=>$request->id,'step'=>'12']);
            }else{
                return redirect()->route('lengkapi-quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit12 (Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$request->id)->first();
            $quotationSite = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            $isAktif = 1;
            $statusQuotation = 3;

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'is_aktif' => $isAktif,
                'step' => 100,
                'status_quotation_id' =>$statusQuotation,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //Masukkan Requirement
            $detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
            foreach ($detail as $key => $value) {
                //cari apakah ada requirement
                $existData= DB::table('sl_quotation_detail_requirement')->where('quotation_detail_id',$value->id)->whereNull('deleted_at')->get();

                if(count($existData)==0){
                    $requirement = DB::table('m_kebutuhan_detail_requirement')->whereNull('deleted_at')->where('position_id',$value->position_id)->get();
                    foreach ($requirement as $kreq => $req) {
                        DB::table('sl_quotation_detail_requirement')->insert([
                            'quotation_id' => $quotation->id,
                            'quotation_detail_id' => $value->id,
                            'requirement' => $req->requirement,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('monitoring-kontrak.view',$quotation->pks_id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    public function editNoteHargaJual($id){
        $data = DB::table('sl_quotation')->where('id',$id)->first();
        return view('sales.lengkapi-quotation.edit-note-harga-jual',compact('data'));
    }

    public function saveEditNoteHargaJual(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation')->where('id',$request->id)->first();
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'note_harga_jual' => $request->raw_text,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            return redirect()->route('lengkapi-quotation.step',['id'=>$data->id,'step'=>'11']);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);}
    }
    public function addQuotationKerjasama($id){
        $quotation = DB::table('sl_quotation')->where('id',$id)->first();
        return view('sales.lengkapi-quotation.add-quotation-kerjasama',compact('quotation'));
    }

    public function saveAddQuotationKerjasama(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kerjasama')->insert([
                'quotation_id' => $request->quotation_id,
                'perjanjian' => $request->raw_text,
                'is_delete' => 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);
            return redirect()->route('lengkapi-quotation.step',['id'=>$request->quotation_id,'step'=>'12']);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    public function editQuotationKerjasama($id){
        $data = DB::table('sl_quotation_kerjasama')->where('id',$id)->first();
        $quotation = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
        return view('sales.lengkapi-quotation.edit-quotation-kerjasama',compact('data','quotation'));
    }

    public function saveEditQuotationKerjasama(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_quotation_kerjasama')->where('id',$request->id)->first();
            DB::table('sl_quotation_kerjasama')->where('id',$request->id)->update([
                'perjanjian' => $request->raw_text,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            return redirect()->route('lengkapi-quotation.step',['id'=>$data->quotation_id,'step'=>'12']);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);}
    }
    public function listQuotationKerjasama (Request $request){
        $data = DB::table('sl_quotation_kerjasama')->where('quotation_id',$request->quotation_id)->whereNull('deleted_at')->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        };

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                    <a href="'.route('lengkapi-quotation.edit-quotation-kerjasama',$data->id).'" class="btn-edit btn btn-warning waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-pencil"></i></a> &nbsp;
                    <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                </div>';
        })
        ->rawColumns(['aksi','perjanjian'])
        ->make(true);
    }
}

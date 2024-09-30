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
            ->select('sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.custom_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$value->kebutuhan_id)->whereNull('deleted_at')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            $province = DB::connection('mysqlhris')->table('m_province')->get();
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
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[1,2,3,4])->get();
                $listKaporlap = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[1,2,3,4])
                                    ->get();
                foreach ($listKaporlap as $key => $value) {
                    $kaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->where('barang_id',$value->id)->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->first();
                    if($kaporlap != null){
                        $value->jumlah_sc = $kaporlap->jumlah_sc;
                        $value->jumlah_sg = $kaporlap->jumlah_sg;
                    }else{
                        $value->jumlah_sc = 0;
                        $value->jumlah_sg = 0;
                    }
                }
            }

            //step 8 ohc
            $listOhc = null;
            if($request->step==8){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[5,6,7])->get();
                $listOhc = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[5,6,7])
                                    ->get();
                foreach ($listOhc as $key => $value) {
                    $ohc = DB::table('sl_quotation_kebutuhan_ohc')->where('barang_id',$value->id)->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->first();
                    if($ohc != null){
                        $value->jumlah = $ohc->jumlah;
                    }else{
                        $value->jumlah = 0;
                    }
                }
            }

            //step 9 - devices
            $listDevices = null;
            if($request->step==9){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[8,9,10,11])->get();
                $listDevices = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[8,9,10,11])
                                    ->get();
                foreach ($listDevices as $key => $value) {
                    $devices = DB::table('sl_quotation_kebutuhan_devices')->where('barang_id',$value->id)->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->first();
                    if($devices != null){
                        $value->jumlah = $devices->jumlah;
                    }else{
                        $value->jumlah = 0;
                    }
                }
            }

            //step 10 - chemical
            $listChemical = null;
            if($request->step==10){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[12,13,14,15])->get();
                $listChemical = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[12,13,14,15])
                                    ->get();
                foreach ($listChemical as $key => $value) {
                    $chemical = DB::table('sl_quotation_kebutuhan_chemical')->where('barang_id',$value->id)->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->first();
                    if($chemical != null){
                        $value->jumlah = $chemical->jumlah;
                    }else{
                        $value->jumlah = 0;
                    }
                }
            }

            return view('sales.quotation.edit-'.$request->step,compact('listChemical','listDevices','listOhc','listJenis','listKaporlap','jenisPerusahaan','aplikasiPendukung','arrAplikasiSel','manfee','kota','province','quotation','request','company','salaryRule','quotationKebutuhan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // public function edit7 (Request $request,$id){
    //     try {
    //         $quotation = DB::table("sl_quotation")->where('id',$id)->first();
    //         return view('sales.quotation.edit-7',compact('quotation','request'));
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    // public function edit8 (Request $request,$id){
    //     try {
    //         $quotation = DB::table("sl_quotation")->where('id',$id)->first();
    //         return view('sales.quotation.edit-8',compact('quotation','request'));
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }


    public function view (Request $request,$id){
        try {
            $data = DB::table('sl_quotation_kebutuhan')->where('id',$id)->first();
            $data->detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$id)->get();
            $data->totalHc = 0;

            foreach ($data->detail as $key => $value) {
                $data->totalHc += $value->jumlah_hc;
            }
            
            $master = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            //format
            $master->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$master->mulai_kontrak)->isoFormat('D MMMM Y');
            $master->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$master->kontrak_selesai)->isoFormat('D MMMM Y');
            $master->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$master->tgl_penempatan)->isoFormat('D MMMM Y');
            $master->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$master->created_at)->isoFormat('D MMMM Y');

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
            return view('sales.quotation.view',compact('now','data','master','leads','aplikasiPendukung'));
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
                'leads' => 'required'
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
                    'nama_perusahaan' => $request->leads,
                    'step' => 1,
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
                'jumlah_site' => 'required',
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
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'jumlah_site' =>  $request->jumlah_site,
                    'jenis_kontrak' => $request->jenis_kontrak,
                    'step' => 2,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                DB::commit();
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'2']);
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit2 (Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'kebutuhan' => 'required',
                'entitas' => 'required',
                'mulai_kontrak' => 'required',
                'kontrak_selesai' => 'required',
                'tgl_penempatan' => 'required',
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

                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'kebutuhan_id' =>  implode(",",$request->kebutuhan),
                    'company_id' => $request->entitas,
                    'mulai_kontrak' => $request->mulai_kontrak,
                    'kontrak_selesai' => $request->kontrak_selesai,
                    'tgl_penempatan' => $request->tgl_penempatan,
                    'salary_rule_id' => $request->salary_rule,
                    'step' => 3,
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

                    if($key==(count($request->kebutuhan)-1)){
                        $kebutuhanPerjanjian = $kebutuhanPerjanjian." dan <b>".$kebutuhan->nama."</b>";
                    }else if($key==0){
                        $kebutuhanPerjanjian = "<b>".$kebutuhan->nama."</b>";
                    }else{
                        $kebutuhanPerjanjian = $kebutuhanPerjanjian." , <b>".$kebutuhan->nama."</b>";
                    };
                    
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

                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'step' => 3,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                //hapus dulu perjanjian yg lama atau kalau ada
                DB::table('sl_quotation_kerjasama')->where('quotation_id',$request->id)->whereNull('deleted_at')->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                //buat perjanjian
                $arrPerjanjian = [
                    "Penawaran harga ini berlaku 30 hari sejak tanggal diterbitkan.",
                    "Akan dilakukan <i>survey</i> area untuk kebutuhan ".$kebutuhanPerjanjian." sebagai tahapan <i>assesment</i> area untuk memastikan efektifitas pekerjaan.",
                    "Komponen dan nilai dalam penawaran harga ini berdasarkan kesepakatan para pihak dalam pengajuan harga awal, apabila ada perubahan, pengurangan maupun penambahan pada komponen dan nilai pada penawaran, maka <b>para pihak</b> sepakat akan melanjutkan ke tahap negosiasi selanjutnya.",
                ];

                foreach ($arrPerjanjian as $key => $value) {
                    DB::table('sl_quotation_kerjasama')->insert([
                        'quotation_id' => $request->id,
                        'perjanjian' => $value,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }

                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'3']);
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
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 4,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'4']);
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
                    $customUpah = $request['custom-upah-'.$value->id];
                }

                $quotationStatus = "";
                $successStatus = "";
                $isAktif = $value->is_aktif;
                if($value->is_aktif==2){
                    if($upah == "Custom"){
                        if($customUpah < 1800000){
                            $isAktif = 0;
                        }
                    }

                    if($value->kebutuhan =="Security"){
                        if($manfee <7){
                            $isAktif = 0;
                        }
                    }else{
                        if($manfee <6){
                            $isAktif = 0;
                        }
                    }
                }

                if($isAktif == 1){
                    $successStatus = "Quotation telah Aktif";
                }else if($isAktif == 0) {
                    $quotationStatus = "Memerlukan Approval Manajemen";
                }


                DB::table('sl_quotation_kebutuhan')->where('id',$value->id)->update([
                    'provinsi_id' => $provinsiId,
                    'provinsi' => $provinsi,
                    'kota_id' => $kotaId,
                    'kota' => $kota,
                    'upah' => $upah,
                    'custom_upah' => $customUpah,
                    'management_fee_id' => $manfee,
                    'is_aktif' =>$isAktif,
                    'quotation_status' => $quotationStatus,
                    'success_status' => $successStatus,
                    'persentase' => $presentase,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 5,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'5']);
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
                $programBpjs = $request['program-bpjs-'.$value->id];

                $jenisPerusahaan = null;
                if($jenisPerusahaanId != null){
                    $jenisPerusahaanList = DB::table('m_jenis_perusahaan')->where('id',$jenisPerusahaanId)->first();
                    if($jenisPerusahaanList != null){
                        $jenisPerusahaan = $jenisPerusahaanList->nama;
                    }
                }

                $quotationStatus = "";
                $successStatus = "";
                $isAktif = $value->is_aktif;
                if($isAktif==2){
                    if($programBpjs != "4 BPJS"){
                        $isAktif = 0;
                    }
                }

                if($isAktif ==2){
                    $isAktif = 1;
                };

                if($isAktif == 1){
                    $successStatus = "Quotation telah Aktif";
                }else if($isAktif == 0) {
                    $quotationStatus = "Memerlukan Approval Manajemen";
                }

                DB::table('sl_quotation_kebutuhan')->where('id',$value->id)->update([
                    'jenis_perusahaan_id' => $jenisPerusahaanId,
                    'jenis_perusahaan' => $jenisPerusahaan,
                    'resiko' => $resiko,
                    'is_aktif' => $isAktif,
                    'quotation_status' => $quotationStatus,
                    'success_status' => $successStatus,
                    'program_bpjs' => $programBpjs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 6,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'6']);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit6 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                $aplikasiPendukung = $request->aplikasi_pendukung;
                foreach ($aplikasiPendukung as $keyd => $valued) {
                    $appdukung = DB::table('m_aplikasi_pendukung')->where('id',$valued)->first();

                    $dataAplikasi = DB::table('sl_quotation_kebutuhan_aplikasi')->where('aplikasi_pendukung_id',$valued)->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->first();
                    if($dataAplikasi==null){
                        DB::table('sl_quotation_kebutuhan_aplikasi')->insert([
                            'quotation_id' => $request->id,
                            'quotation_kebutuhan_id' => $value->id,
                            'aplikasi_pendukung_id' => $valued,
                            'aplikasi_pendukung' => $appdukung->nama,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_aplikasi')->where('id',$dataAplikasi->id)->update([
                            'quotation_id' => $request->id,
                            'quotation_kebutuhan_id' => $value->id,
                            'aplikasi_pendukung_id' => $valued,
                            'aplikasi_pendukung' => $appdukung->nama,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);
                    }
                }

                DB::table('sl_quotation_kebutuhan_aplikasi')->where('quotation_kebutuhan_id',$value->id)->whereNotIn('aplikasi_pendukung_id', $aplikasiPendukung)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 7,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'7']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit7 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                foreach ($request->barang as $keyD => $valueD) {
                    //cari dulu apakah ada data
                    $data = DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->first();
                    $kaporlap = DB::table('m_barang')->where('id',$valueD)->first();
                    if($data == null){
                        DB::table('sl_quotation_kebutuhan_kaporlap')->insert([
                            'quotation_kebutuhan_id' => $value->id,
                            'quotation_id' => $request->id,
                            'barang_id' => $valueD,
                            'jumlah_sc' => $request['sc_'.$valueD],
                            'jumlah_sg' => $request['sg_'.$valueD],
                            'harga' => $kaporlap->harga,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah_sc' => $request['sc_'.$valueD],
                            'jumlah_sg' => $request['sg_'.$valueD],
                            'harga' => $kaporlap->harga,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);
                    }
                };
            };

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 8,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'8']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit8 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                foreach ($request->barang as $keyD => $valueD) {
                    //cari dulu apakah ada data
                    $data = DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->first();
                    $ohc = DB::table('m_barang')->where('id',$valueD)->first();
                    if($data == null){
                        DB::table('sl_quotation_kebutuhan_ohc')->insert([
                            'quotation_kebutuhan_id' => $value->id,
                            'quotation_id' => $request->id,
                            'barang_id' => $valueD,
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $ohc->harga,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $ohc->harga,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);
                    }
                };
            };

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 9,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'9']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit9 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                foreach ($request->barang as $keyD => $valueD) {
                    //cari dulu apakah ada data
                    $data = DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->first();
                    $devices = DB::table('m_barang')->where('id',$valueD)->first();
                    if($data == null){
                        DB::table('sl_quotation_kebutuhan_devices')->insert([
                            'quotation_kebutuhan_id' => $value->id,
                            'quotation_id' => $request->id,
                            'barang_id' => $valueD,
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $devices->harga,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $devices->harga,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);
                    }
                };
            };

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 10,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'10']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit10 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationKebutuhan as $key => $value) {
                foreach ($request->barang as $keyD => $valueD) {
                    //cari dulu apakah ada data
                    $data = DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->first();
                    $chemical = DB::table('m_barang')->where('id',$valueD)->first();
                    if($data == null){
                        DB::table('sl_quotation_kebutuhan_chemical')->insert([
                            'quotation_kebutuhan_id' => $value->id,
                            'quotation_id' => $request->id,
                            'barang_id' => $valueD,
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $chemical->harga,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $chemical->harga,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);
                    }
                };
            };

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 11,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->fulsl_name
            ]);
            
            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'11']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit11 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => 100,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();
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
        $data = DB::table('sl_quotation_kebutuhan')
                    ->join('sl_quotation','sl_quotation.id','sl_quotation_kebutuhan.quotation_id')
                    ->join('sl_leads','sl_leads.id','sl_quotation.leads_id')
                    ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                    ->select('sl_quotation.jenis_kontrak','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
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
                return '<div class="justify-content-center d-flex">
                                    <a href="'.route('quotation.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                        </div>';
            })
            ->editColumn('nomor', function ($data) {
                return '<a href="'.route('quotation.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
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
            $kebutuhanD = DB::table('m_kebutuhan_detail')->where('id',$request->jabatan_detail_id)->first();
            DB::table('sl_quotation_kebutuhan_detail')->insert([
                'quotation_id' => $quotationKebutuhan->quotation_id,
                'quotation_kebutuhan_id' => $quotationKebutuhan->id,
                'kebutuhan_detail_id' => $request->jabatan_detail_id,
                'kebutuhan' => $kebutuhan->nama,
                'jabatan_kebutuhan' => $kebutuhanD->nama,
                'jumlah_hc' => $request->jumlah_hc,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);
            return "sukses";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "gagal";
        }
    }

    public function deleteDetailHC(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kebutuhan_detail')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function listDetailHC (Request $request){
        $data = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$request->quotation_kebutuhan_id)->whereNull('deleted_at')->get();
        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'" data-kebutuhan="'.$data->quotation_kebutuhan_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }
    
    public function changeKota (Request $request){
        $data = DB::connection('mysqlhris')->table('m_city')->where('province_id',$request->province_id)->get();
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
            if(in_array(Auth::user()->role_id,[31,32,33,50,51,52])){
                DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                    'ot1' => Auth::user()->full_name,
                    'info_status' => "Quotation Menunggu di approve oleh Direktur",
                    'quotation_status' => null,
                    'success_status' => null,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan')->where('id',$request->id)->update([
                    'ot2' => Auth::user()->full_name,
                    'is_aktif' => 1,
                    'success_status' => "Quotation Telah Aktif",
                    'quotation_status' => null,
                    'info_status' => null,
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

}
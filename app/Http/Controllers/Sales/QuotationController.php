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
            ->select('sl_quotation_kebutuhan.jenis_perusahaan_id','sl_quotation_kebutuhan.resiko','sl_quotation_kebutuhan.program_bpjs','sl_quotation_kebutuhan.nominal_upah','sl_quotation_kebutuhan.persentase','sl_quotation_kebutuhan.management_fee_id','sl_quotation_kebutuhan.upah','sl_quotation_kebutuhan.kota_id','sl_quotation_kebutuhan.provinsi_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
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

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_kaporlap where deleted_at is null and quotation_kebutuhan_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_ohc where deleted_at is null and quotation_kebutuhan_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_devices where deleted_at is null and quotation_kebutuhan_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_chemical where deleted_at is null and quotation_kebutuhan_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_kebutuhan_ohc')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_kebutuhan_chemical')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();

            $listHPP = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listCS = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listGpm = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();

            return view('sales.quotation.view',compact('listGpm','listCS','listHPP','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','data','master','leads','aplikasiPendukung'));
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
                $salaryRule = DB::table('m_salary_rule')->where('id',$request->salary_rule)->first();
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'kebutuhan_id' =>  implode(",",$request->kebutuhan),
                    'company_id' => $request->entitas,
                    'mulai_kontrak' => $request->mulai_kontrak,
                    'kontrak_selesai' => $request->kontrak_selesai,
                    'tgl_penempatan' => $request->tgl_penempatan,
                    'salary_rule_id' => $request->salary_rule,
                    'top' => $salaryRule->top,
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
                }else{
                    $customUpah = 5200000;
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
                    'nominal_upah' => $customUpah,
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
                            'nama' => $kaporlap->nama,
                            'jenis_barang' => $kaporlap->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_kaporlap')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah_sc' => $request['sc_'.$valueD],
                            'jumlah_sg' => $request['sg_'.$valueD],
                            'harga' => $kaporlap->harga,
                            'nama' => $kaporlap->nama,
                            'jenis_barang' => $kaporlap->jenis_barang,
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
                            'nama' => $ohc->nama,
                            'jenis_barang' => $ohc->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_ohc')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $ohc->harga,
                            'nama' => $ohc->nama,
                            'jenis_barang' => $ohc->jenis_barang,
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
                            'nama' => $devices->nama,
                            'jenis_barang' => $devices->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_devices')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $devices->harga,
                            'nama' => $devices->nama,
                            'jenis_barang' => $devices->jenis_barang,
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
                            'nama' => $chemical->nama,
                            'jenis_barang' => $chemical->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }else{
                        DB::table('sl_quotation_kebutuhan_chemical')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$value->id)->where('barang_id',$valueD)->update([
                            'jumlah' => $request['jumlah_'.$valueD],
                            'harga' => $chemical->harga,
                            'nama' => $chemical->nama,
                            'jenis_barang' => $chemical->jenis_barang,
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

            $this->perhitunganHPPSecurity($data->id);

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

            // cek apakah data sudah ada
            $checkExist = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$quotationKebutuhan->id)->where('kebutuhan_detail_id',$request->jabatan_detail_id)->whereNull('deleted_at')->get();
            if(count($checkExist)>0){
                return "Jabatan ini sudah ada";
            };

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
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
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

    public function perhitunganHPPSecurity ($quotationKebutuhanId){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            //Gaji Pokok
            $gajiPokok = 0;

            $data = DB::table('sl_quotation_kebutuhan')->where('id',$quotationKebutuhanId)->first();
            if($data != null ){
                if($data->nominal_upah !=null){
                    $gajiPokok = $data->nominal_upah;
                }
            }

            //cari data
            $existGajiPokok = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','gaji_pokok')->first();
            if($existGajiPokok !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','gaji_pokok')->update([
                    'percentage' => null,
                    'nominal' => $gajiPokok,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'gaji_pokok',
                    'structure' => 'Gaji Pokok',
                    'percentage' => null,
                    'nominal' => $gajiPokok,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //tunjangan overtime 72 jam
            $tunjanganOverTime = 400000;
            
            //cari data
            $existTunjanganOvertime = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjanan_overtime')->first();
            if($existTunjanganOvertime !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjanan_overtime')->update([
                    'percentage' => null,
                    'nominal' => $tunjanganOverTime,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'tunjanan_overtime',
                    'structure' => 'Tunjangan Overtime Flat 72 Jam',
                    'percentage' => null,
                    'nominal' => $tunjanganOverTime,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            //tunjangan Hari Raya
            $tunjanganHariRaya = $gajiPokok/12;
            
            //cari data
            $existTunjanganHariRaya = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjangan_hari_raya')->first();
            if($existTunjanganHariRaya !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjangan_hari_raya')->update([
                    'percentage' => null,
                    'nominal' => $tunjanganHariRaya,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'tunjangan_hari_raya',
                    'structure' => 'Tunjangan Hari Raya',
                    'percentage' => null,
                    'nominal' => $tunjanganHariRaya,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS JKK
            $bpjsJKK = $gajiPokok*24/100;
            
            //cari data
            $existBpjsJKK = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkk')->first();
            if($existBpjsJKK !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkk')->update([
                    'percentage' => 0.24,
                    'nominal' => $bpjsJKK,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_jkk',
                    'structure' => 'BPJS Ketenagakerjaan J. Kecelakaan Kerja',
                    'percentage' => 0.24,
                    'nominal' => $bpjsJKK,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS Jkm
            $bpjsJKM = $gajiPokok*30/100;
            
            //cari data
            $existBpjsJKM = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkm')->first();
            if($existBpjsJKM !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkm')->update([
                    'percentage' => 0.30,
                    'nominal' => $bpjsJKM,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_jkm',
                    'structure' => 'BPJS Ketenagakerjaan J. Kematian',
                    'percentage' => 0.30,
                    'nominal' => $bpjsJKK,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS JHT
            $bpjsJHT = $gajiPokok*37/100;
            
            //cari data
            $existBpjsJHT = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jht')->first();
            if($existBpjsJHT !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jht')->update([
                    'percentage' => 3.7,
                    'nominal' => $bpjsJHT,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_jht',
                    'structure' => 'BPJS Ketenagakerjaan J. Hari Tua',
                    'percentage' => 3.7,
                    'nominal' => $bpjsJKK,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS KESEHATAN
            $bpjsKes = $gajiPokok*40/100;
            
            //cari data
            $existBpjsKes = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_kes')->first();
            if($existBpjsKes !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_kes')->update([
                    'percentage' => 4,
                    'nominal' => $bpjsKes,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_kes',
                    'structure' => 'BPJS Kesehatan',
                    'percentage' => 4,
                    'nominal' => $bpjsKes,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Seragam
            $provisiSeragam = 0;
            
            //collect data
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah_sg)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qProvisiSeragam)>0){
                if($qProvisiSeragam[0]->kaporlap !=null){
                    $provisiSeragam = $qProvisiSeragam[0]->kaporlap;
                }
            }

            //cari data
            $existBpjsKes = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->first();
            if($existBpjsKes !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->update([
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'provisi_seragam',
                    'structure' => 'Provisi 1 Seragam (Pdl)',
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Seragam
            $provisiSeragam = 0;
            
            //collect data
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah_sg)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qProvisiSeragam)>0){
                if($qProvisiSeragam[0]->kaporlap !=null){
                    $provisiSeragam = $qProvisiSeragam[0]->kaporlap;
                }
            }

            //cari data
            $existProvisiSeragam = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->first();
            if($existProvisiSeragam !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->update([
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'provisi_seragam',
                    'structure' => 'Provisi 1 Seragam (Pdl)',
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Devices
            $provisiDevices = 0;
            
            //collect data
            $qProvisiDevices = DB::select("SELECT sum(harga*jumlah)/12 as devices from sl_quotation_kebutuhan_devices WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qProvisiDevices)>0){
                if($qProvisiDevices[0]->devices !=null){
                    $provisiDevices = $qProvisiDevices[0]->devices;
                }
            }

            //cari data
            $existProvisiDevices = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_devices')->first();
            if($existProvisiDevices !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_devices')->update([
                    'percentage' => null,
                    'nominal' => $provisiDevices,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'provisi_devices',
                    'structure' => 'Provisi Peralatan Pos Security',
                    'percentage' => null,
                    'nominal' => $provisiDevices,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //OHC
            $ohc = 0;
            
            //collect data
            $qOhc = DB::select("SELECT sum(harga*jumlah)/12 as ohc from sl_quotation_kebutuhan_ohc WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qOhc)>0){
                if($qOhc[0]->ohc !=null){
                    $ohc = $qOhc[0]->ohc;
                }
            }

            //cari data
            $existOhc = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ohc')->first();
            if($existOhc !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ohc')->update([
                    'percentage' => null,
                    'nominal' => $ohc,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'ohc',
                    'structure' => 'Over Head Cost',
                    'percentage' => null,
                    'nominal' => $ohc,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Total Biaya Personil
            $totalBiayaPersonil = $gajiPokok+$tunjanganOverTime+$tunjanganHariRaya+$bpjsJKK+$bpjsJKM+$bpjsJHT+$bpjsKes+$provisiSeragam+$provisiDevices+$ohc;

            //cari data
            $existBiayaPersonil = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','biaya_personil')->first();
            if($existBiayaPersonil !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','biaya_personil')->update([
                    'percentage' => null,
                    'nominal' => $totalBiayaPersonil,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'biaya_personil',
                    'structure' => 'Total Biaya per Personil',
                    'percentage' => null,
                    'nominal' => $totalBiayaPersonil,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Sub Total Biaya Personil
            $personil = 0;

            $qPersonil = DB::select('SELECT sum(jumlah_hc) as  jumlah_hc from sl_quotation_kebutuhan_detail WHERE deleted_at is null and quotation_kebutuhan_id ='.$quotationKebutuhanId);
            if(count($qPersonil)>0){
                if($qPersonil[0]->jumlah_hc !=null){
                    $personil = $qPersonil[0]->jumlah_hc;
                }
            }
            $subTotalBiayaPersonilHpp = $totalBiayaPersonil*$personil;

            //cari data
            $existSubBiayaPersonil = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','sub_biaya_personil')->first();
            if($existSubBiayaPersonil !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','sub_biaya_personil')->update([
                    'percentage' => null,
                    'nominal' => $subTotalBiayaPersonilHpp,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'sub_biaya_personil',
                    'structure' => 'Sub Total Biaya All Personil',
                    'percentage' => null,
                    'nominal' => $subTotalBiayaPersonilHpp,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Management Fee
            $managementFee = 0;
            if($data->persentase !=null){
                $managementFee = $subTotalBiayaPersonilHpp*$data->persentase/100;
            }

            //cari data
            $existSubManagementFee = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','management_fee')->first();
            if($existSubBiayaPersonil !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','management_fee')->update([
                    'percentage' => null,
                    'nominal' => $managementFee,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'management_fee',
                    'structure' => 'Management Fee (MF)',
                    'percentage' => null,
                    'nominal' => $managementFee,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            //Grant Total Sebelum Pajak
            $grandTotal = $managementFee+$subTotalBiayaPersonilHpp;

            //cari data
            $existGrandTotal = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','grand_total')->first();
            if($existGrandTotal !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','grand_total')->update([
                    'percentage' => null,
                    'nominal' => $grandTotal,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'grand_total',
                    'structure' => 'Grand Total Sebelum Pajak',
                    'percentage' => null,
                    'nominal' => $grandTotal,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            //PPN Management Fee
            $ppnManagementFeeHpp = $managementFee*11/100;

            //cari data
            $existPpnManagementFee = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ppn_management_fee')->first();
            if($existPpnManagementFee !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ppn_management_fee')->update([
                    'percentage' => null,
                    'nominal' => $ppnManagementFeeHpp,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'ppn_management_fee',
                    'structure' => 'PPn *dari management fee',
                    'percentage' => null,
                    'nominal' => $ppnManagementFeeHpp,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //PPh Management Fee
            $pphManagementFee = $managementFee*(-2)/100;

            //cari data
            $existPphManagementFee = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pph_management_fee')->first();
            if($existPphManagementFee !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pph_management_fee')->update([
                    'percentage' => null,
                    'nominal' => $pphManagementFee,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'pph_management_fee',
                    'structure' => 'PPh *dari management fee',
                    'percentage' => null,
                    'nominal' => $pphManagementFee,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //TotalInvoice
            $totalInvoiceHpp = $grandTotal+$ppnManagementFeeHpp+$pphManagementFee;

            //cari data
            $existPphTotalInvoice = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','total_invoice')->first();
            if($existPphTotalInvoice !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','total_invoice')->update([
                    'percentage' => null,
                    'nominal' => $totalInvoiceHpp,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'total_invoice',
                    'structure' => 'TOTAL INVOICE',
                    'percentage' => null,
                    'nominal' => $totalInvoiceHpp,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Pembulatan
            $pembulatan = ceil($totalInvoiceHpp / 100) * 100;

            //cari data
            $existPembulatan = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pembulatan')->first();
            if($existPembulatan !=null){
                DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pembulatan')->update([
                    'percentage' => null,
                    'nominal' => $pembulatan,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'pembulatan',
                    'structure' => 'PEMBULATAN',
                    'percentage' => null,
                    'nominal' => $pembulatan,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            // COST STRUCTURE ----------------------------------------------------------------//
            //Gaji Pokok
            $gajiPokok = 0;

            $data = DB::table('sl_quotation_kebutuhan')->where('id',$quotationKebutuhanId)->first();
            if($data != null ){
                if($data->nominal_upah !=null){
                    $gajiPokok = $data->nominal_upah;
                }
            }

            //cari data
            $existGajiPokok = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','gaji_pokok')->first();
            if($existGajiPokok !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','gaji_pokok')->update([
                    'percentage' => null,
                    'nominal' => $gajiPokok,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'gaji_pokok',
                    'structure' => 'Gaji Pokok',
                    'percentage' => null,
                    'nominal' => $gajiPokok,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //tunjangan overtime 72 jam
            $tunjanganOverTime = 400000;
            
            //cari data
            $existTunjanganOvertime = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjanan_overtime')->first();
            if($existTunjanganOvertime !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjanan_overtime')->update([
                    'percentage' => null,
                    'nominal' => $tunjanganOverTime,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'tunjanan_overtime',
                    'structure' => 'Tunjangan Overtime Flat 72 Jam',
                    'percentage' => null,
                    'nominal' => $tunjanganOverTime,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            //tunjangan Hari Raya
            $tunjanganHariRaya = $gajiPokok/12;
            
            //cari data
            $existTunjanganHariRaya = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjangan_hari_raya')->first();
            if($existTunjanganHariRaya !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','tunjangan_hari_raya')->update([
                    'percentage' => null,
                    'nominal' => $tunjanganHariRaya,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'tunjangan_hari_raya',
                    'structure' => 'Tunjangan Hari Raya',
                    'percentage' => null,
                    'nominal' => $tunjanganHariRaya,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS JKK
            $bpjsJKK = $gajiPokok*24/100;
            
            //cari data
            $existBpjsJKK = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkk')->first();
            if($existBpjsJKK !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkk')->update([
                    'percentage' => 0.24,
                    'nominal' => $bpjsJKK,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_jkk',
                    'structure' => 'BPJS Ketenagakerjaan J. Kecelakaan Kerja',
                    'percentage' => 0.24,
                    'nominal' => $bpjsJKK,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS Jkm
            $bpjsJKM = $gajiPokok*30/100;
            
            //cari data
            $existBpjsJKM = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkm')->first();
            if($existBpjsJKM !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jkm')->update([
                    'percentage' => 0.30,
                    'nominal' => $bpjsJKM,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_jkm',
                    'structure' => 'BPJS Ketenagakerjaan J. Kematian',
                    'percentage' => 0.30,
                    'nominal' => $bpjsJKK,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS JHT
            $bpjsJHT = $gajiPokok*37/100;
            
            //cari data
            $existBpjsJHT = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jht')->first();
            if($existBpjsJHT !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_jht')->update([
                    'percentage' => 3.7,
                    'nominal' => $bpjsJHT,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_jht',
                    'structure' => 'BPJS Ketenagakerjaan J. Hari Tua',
                    'percentage' => 3.7,
                    'nominal' => $bpjsJKK,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //BPJS KESEHATAN
            $bpjsKes = $gajiPokok*40/100;
            
            //cari data
            $existBpjsKes = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_kes')->first();
            if($existBpjsKes !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','bpjs_kes')->update([
                    'percentage' => 4,
                    'nominal' => $bpjsKes,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'bpjs_kes',
                    'structure' => 'BPJS Kesehatan',
                    'percentage' => 4,
                    'nominal' => $bpjsKes,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Seragam
            $provisiSeragam = 0;
            
            //collect data
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah_sg)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qProvisiSeragam)>0){
                if($qProvisiSeragam[0]->kaporlap !=null){
                    $provisiSeragam = $qProvisiSeragam[0]->kaporlap;
                }
            }

            //cari data
            $existBpjsKes = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->first();
            if($existBpjsKes !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->update([
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'provisi_seragam',
                    'structure' => 'Provisi 1 Seragam (Pdl)',
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Seragam
            $provisiSeragam = 0;
            
            //collect data
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah_sg)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qProvisiSeragam)>0){
                if($qProvisiSeragam[0]->kaporlap !=null){
                    $provisiSeragam = $qProvisiSeragam[0]->kaporlap;
                }
            }

            //cari data
            $existProvisiSeragam = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->first();
            if($existProvisiSeragam !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_seragam')->update([
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'provisi_seragam',
                    'structure' => 'Provisi 1 Seragam (Pdl)',
                    'percentage' => null,
                    'nominal' => $provisiSeragam,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Devices
            $provisiDevices = 0;
            
            //collect data
            $qProvisiDevices = DB::select("SELECT sum(harga*jumlah)/12 as devices from sl_quotation_kebutuhan_devices WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
            if(count($qProvisiDevices)>0){
                if($qProvisiDevices[0]->devices !=null){
                    $provisiDevices = $qProvisiDevices[0]->devices;
                }
            }

            //cari data
            $existProvisiDevices = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_devices')->first();
            if($existProvisiDevices !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','provisi_devices')->update([
                    'percentage' => null,
                    'nominal' => $provisiDevices,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'provisi_devices',
                    'structure' => 'Provisi Peralatan Pos Security',
                    'percentage' => null,
                    'nominal' => $provisiDevices,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Provisi Devices
            $biayaMonitoringDanKontrol = 50000;
            
            //cari data
            $existBiayaMonitoringDanKontrol = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','biaya_monitoring_kontrol')->first();
            if($existBiayaMonitoringDanKontrol !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','biaya_monitoring_kontrol')->update([
                    'percentage' => null,
                    'nominal' => $biayaMonitoringDanKontrol,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'biaya_monitoring_kontrol',
                    'structure' => 'BIAYA MONITORING & KONTROL',
                    'percentage' => null,
                    'nominal' => $biayaMonitoringDanKontrol,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Total Biaya Personil
            $totalBiayaPersonil = $gajiPokok+$tunjanganOverTime+$tunjanganHariRaya+$bpjsJKK+$bpjsJKM+$bpjsJHT+$bpjsKes+$provisiSeragam+$provisiDevices+$biayaMonitoringDanKontrol;

            //cari data
            $existBiayaPersonil = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','biaya_personil')->first();
            if($existBiayaPersonil !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','biaya_personil')->update([
                    'percentage' => null,
                    'nominal' => $totalBiayaPersonil,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'biaya_personil',
                    'structure' => 'Total Biaya per Personil',
                    'percentage' => null,
                    'nominal' => $totalBiayaPersonil,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Sub Total Biaya Personil
            $personil = 0;

            $qPersonil = DB::select('SELECT sum(jumlah_hc) as  jumlah_hc from sl_quotation_kebutuhan_detail WHERE deleted_at is null and quotation_kebutuhan_id ='.$quotationKebutuhanId);
            if(count($qPersonil)>0){
                if($qPersonil[0]->jumlah_hc !=null){
                    $personil = $qPersonil[0]->jumlah_hc;
                }
            }
            $subTotalBiayaPersonilCs = $totalBiayaPersonil*$personil;

            //cari data
            $existSubBiayaPersonil = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','sub_biaya_personil')->first();
            if($existSubBiayaPersonil !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','sub_biaya_personil')->update([
                    'percentage' => null,
                    'nominal' => $subTotalBiayaPersonilCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'sub_biaya_personil',
                    'structure' => 'Sub Total Biaya All Personil',
                    'percentage' => null,
                    'nominal' => $subTotalBiayaPersonilCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Management Fee
            $managementFee = 0;
            if($data->persentase !=null){
                $managementFee = $subTotalBiayaPersonilCs*$data->persentase/100;
            }

            //cari data
            $existSubManagementFee = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','management_fee')->first();
            if($existSubBiayaPersonil !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','management_fee')->update([
                    'percentage' => null,
                    'nominal' => $managementFee,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'management_fee',
                    'structure' => 'Management Fee (MF)',
                    'percentage' => null,
                    'nominal' => $managementFee,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            //Grant Total Sebelum Pajak
            $grandTotal = $managementFee+$subTotalBiayaPersonilCs;

            //cari data
            $existGrandTotal = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','grand_total')->first();
            if($existGrandTotal !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','grand_total')->update([
                    'percentage' => null,
                    'nominal' => $grandTotal,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'grand_total',
                    'structure' => 'Grand Total Sebelum Pajak',
                    'percentage' => null,
                    'nominal' => $grandTotal,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            //PPN Management Fee
            $ppnManagementFeeCs = $managementFee*11/100;

            //cari data
            $existPpnManagementFee = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ppn_management_fee')->first();
            if($existPpnManagementFee !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ppn_management_fee')->update([
                    'percentage' => null,
                    'nominal' => $ppnManagementFeeCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'ppn_management_fee',
                    'structure' => 'PPn *dari management fee',
                    'percentage' => null,
                    'nominal' => $ppnManagementFeeCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //PPh Management Fee
            $pphManagementFee = $managementFee*(-2)/100;

            //cari data
            $existPphManagementFee = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pph_management_fee')->first();
            if($existPphManagementFee !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pph_management_fee')->update([
                    'percentage' => null,
                    'nominal' => $pphManagementFee,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'pph_management_fee',
                    'structure' => 'PPh *dari management fee',
                    'percentage' => null,
                    'nominal' => $pphManagementFee,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //TotalInvoice
            $totalInvoiceCs = $grandTotal+$ppnManagementFeeCs+$pphManagementFee;

            //cari data
            $existPphTotalInvoice = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','total_invoice')->first();
            if($existPphTotalInvoice !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','total_invoice')->update([
                    'percentage' => null,
                    'nominal' => $totalInvoiceCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'total_invoice',
                    'structure' => 'TOTAL INVOICE',
                    'percentage' => null,
                    'nominal' => $totalInvoiceCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //Pembulatan
            $pembulatan = ceil($totalInvoiceCs / 100) * 100;

            //cari data
            $existPembulatan = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pembulatan')->first();
            if($existPembulatan !=null){
                DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','pembulatan')->update([
                    'percentage' => null,
                    'nominal' => $pembulatan,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_cost_structure')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'pembulatan',
                    'structure' => 'PEMBULATAN',
                    'percentage' => null,
                    'nominal' => $pembulatan,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }


            // ANALISA GPM ------------------------------------------------------------------------------------------------//
            // Nominal
            $existNominal = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','nominal')->first();
            if($existNominal !=null){
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','nominal')->update([
                    'hpp' => $totalInvoiceHpp,
                    'harga_jual' => $totalInvoiceCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                
            }else{
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'nominal',
                    'keterangan' => 'Nominal',
                    'hpp' => $totalInvoiceHpp,
                    'harga_jual' => $totalInvoiceCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            // PPN
            $existPPn = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ppn')->first();
            if($existPPn !=null){
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','ppn')->update([
                    'hpp' => $ppnManagementFeeHpp,
                    'harga_jual' => $ppnManagementFeeCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                
            }else{
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'ppn',
                    'keterangan' => 'PPN',
                    'hpp' => $ppnManagementFeeHpp,
                    'harga_jual' => $ppnManagementFeeCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            // Total Biaya
            $existTotalBiaya = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','total_biaya')->first();
            if($existTotalBiaya !=null){
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','total_biaya')->update([
                    'hpp' => $subTotalBiayaPersonilHpp,
                    'harga_jual' => $subTotalBiayaPersonilCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                
            }else{
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'total_biaya',
                    'keterangan' => 'Total Biaya',
                    'hpp' => $subTotalBiayaPersonilHpp,
                    'harga_jual' => $subTotalBiayaPersonilCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            // Margin
            $marginHpp = $totalInvoiceHpp-$ppnManagementFeeHpp-$subTotalBiayaPersonilHpp;
            $marginCs = $totalInvoiceCs-$ppnManagementFeeCs-$subTotalBiayaPersonilCs;
            $existMargin = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','margin')->first();
            if($existMargin !=null){
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','margin')->update([
                    'hpp' => $marginHpp,
                    'harga_jual' => $marginCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                
            }else{
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'margin',
                    'keterangan' => 'Margin',
                    'hpp' => $marginHpp,
                    'harga_jual' => $marginCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            // GPM
            $gpmHpp = $marginHpp/$subTotalBiayaPersonilHpp*100;
            $gpmCs = $marginCs/$subTotalBiayaPersonilCs*100;
            $existGpm = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','gpm')->first();
            if($existGpm !=null){
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhanId)->where('kunci','gpm')->update([
                    'hpp' => $gpmHpp,
                    'harga_jual' => $gpmCs,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                
            }else{
                DB::table('sl_quotation_kebutuhan_analisa_gpm')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'gpm',
                    'keterangan' => 'GPM',
                    'hpp' => $gpmHpp,
                    'harga_jual' => $gpmCs,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}
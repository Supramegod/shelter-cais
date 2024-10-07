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
                if($quotationKebutuhan[0]->kebutuhan_id != 2){
                    $arrKaporlap = [5];
                }

                $listJenis = DB::table('m_jenis_barang')->whereIn('id',$arrKaporlap)->get();
                $listKaporlap = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',$arrKaporlap)
                                    ->orderBy("nama","asc")
                                    ->get();
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
            $listAplikasiPendukung = null;
            if($request->step==9){
                $listAplikasiPendukung = DB::table('sl_quotation_kebutuhan_aplikasi')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[9,10,11,12])->get();
                $listDevices = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[8,9,10,11,12])
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
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[13,14,15,16])->get();
                $listChemical = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[13,14,15,16])
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

            // step 11 cost structure
            $listHPP = null;
            $listCS = null;
            $listGpm = null;
            $leads = null;
            $data = null;

            if($request->step==11){
                $data = DB::table('sl_quotation_kebutuhan')->where('id',$quotationKebutuhan[0]->id)->first();
                $data->detail = DB::table('sl_quotation_kebutuhan_detail')->whereNull('deleted_at')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->get();
                $data->totalHc = 0;

                foreach ($data->detail as $key => $value) {
                    $data->totalHc += $value->jumlah_hc;
                }
                $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
                $listHPP = DB::table('sl_quotation_kebutuhan_hpp')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->whereNull('deleted_at')->get();
                $listCS = DB::table('sl_quotation_kebutuhan_cost_structure')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->whereNull('deleted_at')->get();
                $listGpm = DB::table('sl_quotation_kebutuhan_analisa_gpm')->where('quotation_kebutuhan_id',$quotationKebutuhan[0]->id)->whereNull('deleted_at')->get();            
            }
            $isEdit = false;

            if(isset($request->edit)){
                $isEdit = true;
            }
            return view('sales.quotation.edit-'.$request->step,compact('listAplikasiPendukung','data','leads','listGpm','listCS','listHPP','isEdit','listChemical','listDevices','listOhc','listJenis','listKaporlap','jenisPerusahaan','aplikasiPendukung','arrAplikasiSel','manfee','kota','province','quotation','request','company','salaryRule','quotationKebutuhan'));
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

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_kaporlap where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_ohc where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_devices where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_kebutuhan_chemical where deleted_at is null and jumlah=1 and quotation_kebutuhan_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kebutuhan_kaporlap')->where('jumlah',1)->where('quotation_kebutuhan_id',$id)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_kebutuhan_ohc')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_kebutuhan_devices')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_kebutuhan_chemical')->where('quotation_kebutuhan_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();

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

                $newStep = 2;
                $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                if($dataQuotation->step>$newStep){
                    $newStep = $dataQuotation->step;
                }
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'jumlah_site' =>  $request->jumlah_site,
                    'jenis_kontrak' => $request->jenis_kontrak,
                    'step' => $newStep,
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

                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'step' => $newStep,
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
            $newStep = 4;
                $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                if($dataQuotation->step>$newStep){
                    $newStep = $dataQuotation->step;
                }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
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

                $quotationStatus = "";
                $successStatus = "";
                
                DB::table('sl_quotation_kebutuhan')->where('id',$value->id)->update([
                    'provinsi_id' => $provinsiId,
                    'provinsi' => $provinsi,
                    'kota_id' => $kotaId,
                    'kota' => $kota,
                    'upah' => $upah,
                    'nominal_upah' => $customUpah,
                    'management_fee_id' => $manfee,
                    'is_aktif' => 0,
                    'quotation_status' => $quotationStatus,
                    'success_status' => $successStatus,
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

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
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

            $newStep = 6;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
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
            if($request->aplikasi_pendukung !=null){
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
                                'harga' => $appdukung->harga,
                                'created_at' => $current_date_time,
                                'created_by' => Auth::user()->full_name
                            ]);
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
                        }
                    }
    
                    DB::table('sl_quotation_kebutuhan_aplikasi')->where('quotation_kebutuhan_id',$value->id)->whereNotIn('aplikasi_pendukung_id', $aplikasiPendukung)->update([
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
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
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

            $newStep = 8;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPPSecurity($data->id);
            
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

            $newStep = 9;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPPSecurity($data->id);
            
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

            $newStep = 10;
                $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                if($dataQuotation->step>$newStep){
                    $newStep = $dataQuotation->step;
                }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            $this->perhitunganHPPSecurity($data->id);

            // jika security maka skip chemical
            if($quotationKebutuhan[0]->kebutuhan_id==2){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'11']);
            }else{
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'10']);
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

            $newStep = 11;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->fulsl_name
            ]);
            
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            $this->perhitunganHPPSecurity($data->id);

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

            $newStep = 12;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            $this->perhitunganHPPSecurity($data->id);

            return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'12']);

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit12 (Request $request){
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
        $data = DB::table('sl_quotation')
                    ->leftJoin('sl_quotation_kebutuhan','sl_quotation.id','sl_quotation_kebutuhan.quotation_id')
                    ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
                    ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                    ->select('sl_quotation_kebutuhan.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
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
                if($data->id != null){
                    return '<a href="'.route('leads.view',$data->leads_id).'" style="font-weight:bold;color:#000056">'.$data->nama_perusahaan.'</a>';
                }
                return "";
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
                DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$quotationKebutuhan->id)->where('kebutuhan_detail_id',$request->jabatan_detail_id)->whereNull('deleted_at')->update([
                    'jumlah_hc' => $checkExist[0]->jumlah_hc+$request->jumlah_hc,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
                return "Data Berhasil Ditambahkan";
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
                if($request['jumlah'.$value->id] !=null && $request['jumlah'.$value->id] !=""){
                    $dataExist = DB::table("sl_quotation_kebutuhan_ohc")
                    ->whereNull('deleted_at')
                    ->where('quotation_kebutuhan_id',$value->quotation_kebutuhan_id)
                    ->where('quotation_kebutuhan_detail_id',$value->id)
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
                                    'jumlah' => $dataExist->jumlah+(int)$request['jumlah'.$value->id],
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
                            'jumlah' => $request['jumlah'.$value->id],
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
        $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_kebutuhan_ohc.quotation_kebutuhan_id,sl_quotation_kebutuhan_ohc.barang_id,sl_quotation_kebutuhan_ohc.jenis_barang,sl_quotation_kebutuhan_ohc.nama,sl_quotation_kebutuhan_ohc.harga 
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
                $dataD = DB::select("select jumlah from sl_quotation_kebutuhan_ohc WHERE deleted_at is null and quotation_kebutuhan_id = $data->quotation_kebutuhan_id and quotation_kebutuhan_detail_id = $value->id and barang_id = $data->barang_id");
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
            $bpjsJKK = $gajiPokok*0.24/100;
            
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
            $bpjsJKM = $gajiPokok*0.30/100;
            
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
            $bpjsJHT = $gajiPokok*3.7/100;
            
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
            $bpjsKes = $gajiPokok*4/100;
            
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
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
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
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
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
                    'percentage' => $data->persentase,
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
                    'percentage' => $data->persentase,
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
                    'percentage' => 11,
                    'nominal' => $ppnManagementFeeHpp,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'ppn_management_fee',
                    'structure' => 'PPn <span class="text-danger"><i>*dari management fee</i></span>',
                    'percentage' => 11,
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
                    'percentage' => -2,
                    'nominal' => $pphManagementFee,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                DB::table('sl_quotation_kebutuhan_hpp')->insert([
                    'quotation_id' => $data->quotation_id,
                    'quotation_kebutuhan_id' => $data->id,
                    'kunci' => 'pph_management_fee',
                    'structure' => 'PPh <span class="text-danger"><i>*dari management fee</i></span>',
                    'percentage' => -2,
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
            $bpjsJKK = $gajiPokok*0.24/100;
            
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
            $bpjsJKM = $gajiPokok*0.30/100;
            
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
            $bpjsJHT = $gajiPokok*3.7/100;
            
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
            $bpjsKes = $gajiPokok*4/100;
            
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
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
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
            $qProvisiSeragam = DB::select("SELECT sum(harga*jumlah)/12 as kaporlap from sl_quotation_kebutuhan_kaporlap WHERE deleted_at is null and quotation_kebutuhan_id =".$quotationKebutuhanId);
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
                    'structure' => 'Total Biaya per Personil <span class="text-danger"><i>(1+2+3)</i></span>',
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
                    'structure' => 'Management Fee (MF) <span class="text-danger"><i>*dari sub total biaya</i></span>',
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
                    'structure' => 'PPn <span class="text-danger"><i>*dari management fee</i></span>',
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
                    'structure' => 'PPh <span class="text-danger"><i>*dari management fee</i></span>',
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
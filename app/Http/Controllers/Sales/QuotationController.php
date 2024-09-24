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
    
    public function edit1 (Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            return view('sales.quotation.edit-1',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit2 (Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            return view('sales.quotation.edit-2',compact('quotation','company','salaryRule'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit3 (Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            $quotationKebutuhan = 
            DB::table("sl_quotation_kebutuhan")
            ->join('m_kebutuhan','m_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id')
            ->whereNull('sl_quotation_kebutuhan.deleted_at')
            ->where('sl_quotation_kebutuhan.quotation_id',$request->id)
            ->orderBy('sl_quotation_kebutuhan.kebutuhan_id','ASC')
            ->select('sl_quotation_kebutuhan.id','sl_quotation_kebutuhan.kebutuhan_id','m_kebutuhan.icon','sl_quotation_kebutuhan.kebutuhan')
            ->get();

            foreach ($quotationKebutuhan as $key => $value) {
                $value->detail = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$value->kebutuhan_id)->whereNull('deleted_at')->get();
                $value->kebutuhan_detail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_kebutuhan_id',$value->id)->whereNull('deleted_at')->get();
            }

            return view('sales.quotation.edit-3',compact('quotation','quotationKebutuhan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit4 (Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            return view('sales.quotation.edit-4',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function edit5 (Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            return view('sales.quotation.edit-5',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit6 (Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            return view('sales.quotation.edit-6',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            return view('sales.quotation.view',compact('now'));
        } catch (\Exception $e) {
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
                return redirect()->route('quotation.edit-1',$newId);
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
                return redirect()->route('quotation.edit-2',$request->id);
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

                foreach ($request->kebutuhan as $key => $value) {
                    $company = DB::connection('mysqlhris')->table('m_company')->where('id',$request->entitas)->first();
                    $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')
                                            ->whereNull('deleted_at')
                                            ->where('quotation_id',$request->id)
                                            ->where('kebutuhan_id',$value)->first();
                    $kebutuhan = DB::table('m_kebutuhan')->where('id',$value)->first();
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
                return redirect()->route('quotation.edit-3',$request->id);
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit3 (Request $request){
        try {
            return redirect()->route('quotation.edit-4',$request->id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit4 (Request $request){
        try {
            return redirect()->route('quotation.edit-5',$request->id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit5 (Request $request){
        try {
            return redirect()->route('quotation.edit-6',$request->id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit6 (Request $request){
        try {
            return redirect()->route('quotation');
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
                    ->select('sl_quotation.jenis_kontrak','sl_quotation_kebutuhan.company','sl_quotation_kebutuhan.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation_kebutuhan.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
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
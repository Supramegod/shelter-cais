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
    
    public function edit1 (Request $request){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$request->id)->first();
            return view('sales.quotation.edit-1',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit2 (Request $request){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$request->id)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            return view('sales.quotation.edit-2',compact('quotation','company','salaryRule'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit3 (Request $request){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$request->id)->first();
            return view('sales.quotation.edit-3',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit4 (Request $request){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$request->id)->first();
            return view('sales.quotation.edit-4',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function edit5 (Request $request){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$request->id)->first();
            return view('sales.quotation.edit-5',compact('quotation'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function edit6 (Request $request){
        try {
            return view('sales.quotation.edit-6');
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
                    'nomor' =>  null,
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
            DB::beginTransaction();

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
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'kebutuhan_id' =>  $request->kebutuhan,
                    'company_id' => $request->entitas,
                    'mulai_kontrak' => $request->mulai_kontrak,
                    'kontrak_selesai' => $request->kontrak_selesai,
                    'tgl_penempatan' => $request->tgl_penempatan,
                    'salary_rule_id' => $request->salary_rule,
                    'step' => 3,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                DB::commit();
                return redirect()->route('quotation.edit-3',$request->id);
            }
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
            $data = DB::table('sl_customer_activity')
                        ->join('sl_leads','sl_leads.id','sl_customer_activity.leads_id')
                        ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
                        ->leftJoin('m_kebutuhan','m_kebutuhan.id','=','sl_leads.kebutuhan_id')
                        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                        ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
                        ->select('sl_customer_activity.email','sl_customer_activity.notulen','sl_customer_activity.jenis_visit','sl_customer_activity.link_bukti_foto','sl_customer_activity.penerima','sl_customer_activity.jam_realisasi','sl_customer_activity.tgl_realisasi','sl_customer_activity.notes_tipe','sl_customer_activity.start','sl_customer_activity.end','sl_customer_activity.durasi','m_status_leads.nama as status_leads','sl_customer_activity.leads_id','sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_customer_activity.tipe','sl_leads.nama_perusahaan as nama', $db2.'.m_branch.name as branch', 'm_kebutuhan.nama as kebutuhan','m_tim_sales_d.nama as sales','sl_customer_activity.notes as keterangan')
                        ->whereNull('sl_customer_activity.deleted_at');
            
            if(!empty($request->tgl_dari)){
                $data = $data->where('sl_customer_activity.tgl_activity','>=',$request->tgl_dari);
            }else{
                $data = $data->where('sl_customer_activity.tgl_activity','==',carbon::now()->toDateString());
            }
            if(!empty($request->tgl_sampai)){
                $data = $data->where('sl_customer_activity.tgl_activity','<=',$request->tgl_sampai);
            }else{
                $data = $data->where('sl_customer_activity.tgl_activity','==',carbon::now()->toDateString());
            }
            if(!empty($request->branch)){
                $data = $data->where('sl_leads.branch_id',$request->branch);
            }
            // if(!empty($request->company)){
            //     $data = $data->where('sl_leads.company_id',$request->company);
            // }

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
                $data = $data->where('m_kebutuhan.id',$request->kebutuhan);
            }

            $data->where('sl_customer_activity.id',"0");
            $data = $data->get();
                        

            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
                $value->tgl_r = null;
                if($value->tgl_realisasi != null){
                    $value->tgl_r = Carbon::createFromFormat('Y-m-d',$value->tgl_realisasi)->isoFormat('D MMMM Y');
                }
            }

            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                                    <a href="'.route('customer-activity.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                        </div>';
            })
            ->editColumn('nomor', function ($data) {
                return '<a href="'.route('customer-activity.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
            })
            ->editColumn('nama', function ($data) {
                return '<a href="'.route('leads.view',$data->leads_id).'" style="font-weight:bold;color:#000056">'.$data->nama.'</a>';
            })
            ->rawColumns(['aksi','nomor','nama'])
            ->make(true);
    }

    public function generateNomor ($leadsId){
        // generate nomor QUOT/SIG/AAABB-092024-00001
        //generate nomor CAT/SG/ABCD1-072024-00001;
        $now = Carbon::now();

        $nomor = "QUOT/";

        $dataLeads = DB::table('sl_leads')->where('id',$leadsId)->first();
        if($dataLeads != null){
            if($dataLeads->kebutuhan_id==1){
                $nomor = $nomor."LS/";
            } else if($dataLeads->kebutuhan_id==2){
                $nomor = $nomor."SG/";
            } else if($dataLeads->kebutuhan_id==3){
                $nomor = $nomor."CS/";
            } else if($dataLeads->kebutuhan_id==4){
                $nomor = $nomor."LL/";
            }

            $nomor = $nomor.$dataLeads->nomor."-";
        }else{
            $nomor = $nomor."NN/NNNNN-";
        }

        $month = $now->month;
        if($month<10){
            $month = "0".$month;
        }

        $urutan = "00001";

        $jumlahData = DB::select("select * from sl_quotation where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

}
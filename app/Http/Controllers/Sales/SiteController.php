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

class SiteController extends Controller
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


        $branch = DB::connection('mysqlhris')->table('m_branch')->where('id','!=',1)->where('is_active',1)->get();
        $status = DB::table('m_status_leads')->whereNull('deleted_at')->get();
        $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

        $error =null;
        $success = null;
        if($ctglDari->gt($ctglSampai)){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        };
        if($ctglSampai->lt($ctglDari)){
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }
        return view('sales.site.list',compact('branch','platform','status','tglDari','tglSampai','request','error','success'));
    }

    public function view (Request $request,$id){
        try {
            $data = DB::table('sl_leads')->whereNotNull('customer_id')->where('id',$id)->first();

            $data->stgl_leads = Carbon::createFromFormat('Y-m-d',$data->tgl_leads)->isoFormat('D MMMM Y');
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$data->created_at)->isoFormat('D MMMM Y');

            $branch = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();
            $jabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
            $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
            $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

            $activity = DB::table('sl_customer_activity')->whereNull('deleted_at')->where('leads_id',$id)->orderBy('created_at','desc')->limit(5)->get();
            foreach ($activity as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y HH:mm');
                $value->stgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
            }

            return view('sales.site.view',compact('activity','data','branch','jabatanPic','jenisPerusahaan','kebutuhan','platform'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }

    }

    public function list (Request $request){
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();

            $data = DB::table("sl_quotation_site")
            ->Join('sl_leads','sl_quotation_site.leads_id','=','sl_leads.id')
            ->leftJoin('sl_spk_site','sl_quotation_site.id','=','sl_spk_site.quotation_site_id')
            ->leftJoin('sl_site','sl_quotation_site.id','=','sl_site.quotation_site_id')
            ->whereNull('sl_quotation_site.deleted_at')
            ->whereNull('sl_leads.deleted_at')
            ->select('sl_quotation_site.id','sl_leads.nama_perusahaan','sl_quotation_site.nama_site','sl_quotation_site.provinsi','sl_quotation_site.kota','sl_quotation_site.penempatan','sl_quotation_site.created_at','sl_quotation_site.created_by','sl_spk_site.id as spk_site_id','sl_site.id as site_id')
            ->orderBy('sl_quotation_site.id', 'desc');


            // $data = DB::table('sl_site')
            //             ->join('sl_leads','sl_leads.id','=','sl_site.leads_id')
            //             ->leftJoin('m_tim_sales_d','sl_site.tim_sales_d_id','=','m_tim_sales_d.id')
            //             ->select('sl_site.*','sl_leads.nama_perusahaan','m_tim_sales_d.nama as sales')
            //             ->whereNull('sl_leads.deleted_at');

            // if(!empty($request->tgl_dari)){
            //     $data = $data->where('sl_leads.tgl_leads','>=',$request->tgl_dari);
            // }else{
            //     $data = $data->where('sl_leads.tgl_leads','==',carbon::now()->toDateString());
            // }
            // if(!empty($request->tgl_sampai)){
            //     $data = $data->where('sl_leads.tgl_leads','<=',$request->tgl_sampai);
            // }else{
            //     $data = $data->where('sl_leads.tgl_leads','==',carbon::now()->toDateString());
            // }
            // if(!empty($request->branch)){
            //     $data = $data->where('sl_leads.branch_id',$request->branch);
            // }
            // if(!empty($request->platform)){
            //     $data = $data->where('sl_leads.platform_id',$request->platform);
            // }
            // if(!empty($request->status)){
            //     $data = $data->where('sl_leads.status_leads_id',$request->status);
            // }

            //divisi sales
            // if(in_array(Auth::user()->role_id,[29,30,31,32,33])){
            //     // sales
            //     if(Auth::user()->role_id==29){
            //         $data = $data->where('m_tim_sales_d.user_id',Auth::user()->id);
            //     }else if(Auth::user()->role_id==30){
            //     }
            //     // spv sales
            //     else if(Auth::user()->role_id==31){
            //         $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();
            //         $memberSales = [];
            //         $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$tim->tim_sales_id)->get();
            //         foreach ($sales as $key => $value) {
            //             array_push($memberSales,$value->user_id);
            //         }
            //         $data = $data->whereIn('m_tim_sales_d.user_id',$memberSales);
            //     }
            //     // Asisten Manager Sales , Manager Sales
            //     else if(Auth::user()->role_id==32 || Auth::user()->role_id==33){

            //     }
            // }
            //divisi RO
            // else if(in_array(Auth::user()->role_id,[4,5,6,8])){
            //     if(in_array(Auth::user()->role_id,[4,5])){
            //         $data = $data->where('sl_leads.ro_id',Auth::user()->id);
            //     }else if(in_array(Auth::user()->role_id,[6,8])){

            //     }
            // }
            // //divisi crm
            // else if(in_array(Auth::user()->role_id,[54,55,56])){
            //     if(in_array(Auth::user()->role_id,[54])){
            //         $data = $data->where('sl_leads.crm_id',Auth::user()->id);
            //     }else if(in_array(Auth::user()->role_id,[55,56])){

            //     }
            // };

            $data = $data->get();


            foreach ($data as $key => $value) {
                // $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '';
                // return '<div class="justify-content-center d-flex">
                //                     <a href="'.route('site.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                //         </div>';
            })
            // ->editColumn('nomor', function ($data) use ($tim) {
            //     $canView = false;
            //     if(Auth::user()->role_id==29){
            //         if($data->tim_sales_d_id==$tim->id){
            //             $canView = true;
            //         }
            //     }else{
            //         $canView = true;
            //     }

            //     $route = route('site.view',$data->id);
            //     if(!$canView){
            //         $route = "#";
            //     }

            //     return '<a href="'.$route.'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nomor.'</a>';
            // })
            // ->editColumn('nama_perusahaan', function ($data) {
            //     return '<a href="'.route('leads.view',$data->id).'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nama_perusahaan.'</a>';
            // })
            ->addColumn('spk', function ($data) {
                if ($data->spk_site_id !== null) {
                    return '<i class="mdi mdi-check-circle text-success"></i>';
                }
                return '';
            })
            ->addColumn('kontrak', function ($data) {
                if ($data->site_id !== null) {
                    return '<i class="mdi mdi-check-circle text-success"></i>';
                }
                return '';
            })
            ->rawColumns(['aksi','spk','kontrak'])
            ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }


    public function availableCustomer (Request $request){
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();

            $data = DB::table('sl_leads')
                        ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
                        ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
                        ->leftJoin('m_platform','sl_leads.platform_id','=','m_platform.id')
                        ->leftJoin('m_kebutuhan','sl_leads.kebutuhan_id','=','m_kebutuhan.id')
                        ->leftJoin('m_tim_sales','sl_leads.tim_sales_id','=','m_tim_sales.id')
                        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                        ->select('sl_leads.ro','sl_leads.crm','m_tim_sales.nama as tim_sales','m_tim_sales_d.nama as sales','sl_leads.tim_sales_id','sl_leads.tim_sales_d_id','sl_leads.status_leads_id','sl_leads.id','sl_leads.tgl_leads','sl_leads.nama_perusahaan','m_kebutuhan.nama as kebutuhan','sl_leads.pic','sl_leads.no_telp','sl_leads.email', 'm_status_leads.nama as status', $db2.'.m_branch.name as branch', 'm_platform.nama as platform','m_status_leads.warna_background','m_status_leads.warna_font')
                        ->whereNull('sl_leads.deleted_at')
                        ->whereNotNull('sl_leads.customer_id');

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

            $data = $data->get();


            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
            }
            return DataTables::of($data)
            ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

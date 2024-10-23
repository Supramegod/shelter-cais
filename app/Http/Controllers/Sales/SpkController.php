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

class SpkController extends Controller
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
        return view('sales.spk.list',compact('branch','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            $data=null;
            $quotation =null;
            if($request->id!=null){
                $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('id',$request->id)->first();
                $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$data->id)->first();
            }
            return view('sales.spk.add',compact('now','data','quotation'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $data = DB::table('sl_spk')
                ->leftJoin('sl_quotation','sl_quotation.id','sl_spk.quotation_id')
                ->leftJoin('sl_quotation_kebutuhan','sl_quotation.id','sl_quotation_kebutuhan.quotation_id')
                ->whereNull('sl_spk.deleted_at')
                ->select('sl_spk.created_by','sl_spk.created_at','sl_spk.id','sl_spk.nomor','sl_quotation_kebutuhan.nomor as nomor_quotation','sl_spk.tgl_spk','sl_quotation.nama_perusahaan','sl_quotation_kebutuhan.kebutuhan','sl_spk.status_spk_id')
                ->get();

        foreach ($data as $key => $value) {
            $value->tgl_spk = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_spk)->isoFormat('D MMMM Y');
            $value->created_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_spk')->where('id',$value->status_spk_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                                <a href="'.route('spk.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                    </div>';
        })
        ->editColumn('nomor', function ($data) {
            return '<a href="'.route('spk.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        })
        ->rawColumns(['aksi','nomor'])
        ->make(true);
    }

    public function availableQuotation (Request $request){
        try {
            $data = DB::table('sl_quotation')
                ->join('sl_quotation_kebutuhan','sl_quotation.id','sl_quotation_kebutuhan.quotation_id')
                ->whereNull('sl_quotation_kebutuhan.deleted_at')
                ->whereNull('sl_quotation.deleted_at')
                ->where('sl_quotation_kebutuhan.is_aktif',1)
                ->get();
            foreach ($data as $key => $value) {
                $spk = DB::table('sl_spk')->where('quotation_id',$value->quotation_id)->first();
                if($spk!=null){
                    unset($data[$key]);
                }
            }
            
            return DataTables::of($data)
            ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('id',$request->quotation_id)->first();
            $quotation = DB::table('sl_quotation')->where('id',$quotationKebutuhan->quotation_id)->first();

            DB::table('sl_spk')->insert([
                'quotation_id' => $quotation->id,
                'quotation_kebutuhan_id' => $quotationKebutuhan->id,
                'leads_id' => $quotation->leads_id,
                'nomor' => $this->generateNomor($quotation->leads_id,$quotation->company_id),
                'tgl_spk' => $current_date_time,
                'nama_perusahaan' => $quotation->nama_perusahaan,
                'link_spk_disetujui' => null,
                'status_spk_id' => 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('id',$quotation->id)->update([
                'status_quotation_id' => 4,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('spk');
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function generateNomor ($leadsId,$companyId){
        // generate nomor QUOT/SIG/AAABB-092024-00001
        $now = Carbon::now();

        $nomor = "SPK/";
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

        $jumlahData = DB::select("select * from sl_spk where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

    public function view (Request $request,$id){
        try {
            $data = DB::table('sl_spk')->where('id',$id)->first();

            $data->stgl_spk = Carbon::createFromFormat('Y-m-d H:i:s',$data->tgl_spk)->isoFormat('D MMMM Y');
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$data->created_at)->isoFormat('D MMMM Y');
            $quotation = DB::table('sl_quotation')->where('id',$data->quotation_id)->first();
            $quotationKebutuhan = DB::table('sl_quotation_kebutuhan')->where('id',$data->quotation_kebutuhan_id)->first();
            $data->status = DB::table('m_status_spk')->where('id',$data->status_spk_id)->first()->nama;

            return view('sales.spk.view',compact('data','quotation','quotationKebutuhan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
        
    }

    public function uploadSPk (Request $request) {
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_spk')->where('id',$request->id)->update([
                'status_spk_id' => 2,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            return "success";
        } catch (\Throwable $th) {
            //throw $th;
        };
    }
}

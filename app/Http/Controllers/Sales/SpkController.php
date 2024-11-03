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
                $data = DB::table('sl_quotation_client')->whereNull('deleted_at')->where('id',$request->id)->first();
                $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$data->id)->get();

                $data->nomor = "";

                foreach ($quotation as $key => $value) {
                    if ($key!=0) {
                        $data->nomor .= ", ";
                    }
                    $data->nomor .= $value->nomor;
                }
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
                ->leftJoin('sl_spk_detail','sl_spk_detail.spk_id','sl_spk.id')
                ->leftJoin('sl_quotation','sl_quotation.id','sl_spk_detail.quotation_id')
                ->whereNull('sl_spk.deleted_at')
                ->whereNull('sl_spk_detail.deleted_at')
                ->select('sl_spk.created_by','sl_spk.created_at','sl_spk.id','sl_spk.nomor','sl_quotation.nomor as nomor_quotation','sl_spk.tgl_spk','sl_quotation.nama_perusahaan','sl_quotation.nama_site','sl_quotation.kebutuhan','sl_spk.status_spk_id')
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
            $data = DB::table('sl_quotation_client')
                ->leftJoin('sl_leads','sl_leads.id','sl_quotation_client.leads_id')
                ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                ->whereNull('sl_quotation_client.deleted_at')
                ->where('m_tim_sales_d.user_id',Auth::user()->id)
                ->select("sl_quotation_client.id","sl_quotation_client.tgl","sl_quotation_client.nama_perusahaan","sl_quotation_client.jumlah_site","sl_quotation_client.layanan")
                ->distinct()
                ->get();
            foreach ($data as $key => $value) {
                $spk = DB::table('sl_spk_detail')->where('quotation_id',$value->id)->first();
                if($spk!=null){
                    unset($data[$key]);
                }else{
                    $quotationList = DB::table("sl_quotation")->whereNull("deleted_at")->where("is_aktif",0)->where("quotation_client_id",$value->id)->get();
                    if(count($quotationList)>0){
                        unset($data[$key]);
                    }else{
                        $sQuotation = "";
                        $quotationList = DB::table("sl_quotation")->whereNull("deleted_at")->where("is_aktif",1)->where("quotation_client_id",$value->id)->get();
                        foreach ($quotationList as $keyd => $valued) {
                            if($keyd>0){
                                $sQuotation .= ", ";
                            }
                            $sQuotation .= $valued->nomor;
                        }
                        $value->quotation = $sQuotation;
                        $value->tgl_quotation = Carbon::createFromFormat('Y-m-d',$value->tgl)->isoFormat('D MMMM Y');
                    }
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
            $quotationClient = DB::table('sl_quotation_client')->where('id',$request->quotation_client_id)->first();
            $leads = DB::table('sl_leads')->where('id',$quotationClient->leads_id)->first();
            $quotation = DB::table('sl_quotation')->where('quotation_client_id',$quotationClient->id)->get();

            $newId = DB::table('sl_spk')->insertGetId([
                'quotation_client_id' => $quotationClient->id,
                'leads_id' => $quotationClient->leads_id,
                'nomor' => $this->generateNomor($quotationClient->leads_id,$quotation[0]->company_id),
                'tgl_spk' => $current_date_time,
                'nama_perusahaan' => $quotationClient->nama_perusahaan,
                'kebutuhan_id' => $quotationClient->layanan_id,
                'kebutuhan' => $quotationClient->layanan,
                'jenis_site' => $quotationClient->jumlah_site,
                'tim_sales_id' => $leads->tim_sales_id,
                'tim_sales_d_id' => $leads->tim_sales_d_id,
                'link_spk_disetujui' => null,
                'status_spk_id' => 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            foreach ($quotation as $key => $value) {
                DB::table('sl_spk_detail')->insertGetId([
                    'spk_id' => $newId,
                    'quotation_client_id' => $quotationClient->id,
                    'quotation_id' => $value->id,
                    'nomor_quotation' => $value->nomor,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            DB::table('sl_quotation')->where('quotation_client_id',$quotationClient->id)->update([
                'status_quotation_id' => 4,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('spk.view',$newId);
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
            $quotationClient = DB::table('sl_quotation_client')->where('id',$data->quotation_client_id)->first();
            $quotation = DB::table('sl_quotation')->where('quotation_client_id',$data->quotation_client_id)->get();
            $data->status = DB::table('m_status_spk')->where('id',$data->status_spk_id)->first()->nama;

            return view('sales.spk.view',compact('data','quotation','quotationClient'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
        
    }

    public function cetakSpk (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table("sl_spk")->where("id",$id)->first();
            $quotationClient = DB::table("sl_quotation_client")->where("id",$data->quotation_client_id)->first();
            $quotation = DB::table("sl_quotation")->where("quotation_client_id",$quotationClient->id)->get();
            $leads = DB::table("sl_leads")->where("id",$data->leads_id)->first();
            foreach ($quotation as $key => $value) {
                $value->tgl_penempatan = Carbon::createFromFormat('Y-m-d',$value->tgl_penempatan)->isoFormat('D MMMM Y');
                $value->detail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where("quotation_id",$value->id)->get();

                $totalhc = 0;
                foreach ($value->detail as $keyd => $valued) {
                    $totalhc+=$valued->jumlah_hc;
                }
                $value->total_hc = $totalhc;
                $value->pic = DB::table("sl_quotation_pic")->whereNull('deleted_at')->where('quotation_id',$value->id)->where('is_kuasa',1)->first();
            }

            $company = DB::connection('mysqlhris')->table('m_company')->where('id',$quotation[0]->company_id)->first();

            return view('sales.spk.cetakan.spk',compact('now','data','quotation','quotationClient','leads','company'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function uploadSPk (Request $request) {
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $originalFileName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = $originalFileName.date("YmdHis").rand(10000,99999).".".$fileExtension;

            Storage::disk('spk')->put($originalName, file_get_contents($request->file('file')));
            
            DB::table('sl_spk')->where('id',$request->id)->update([
                'status_spk_id' => 2,
                'link_spk_disetujui' =>env('APP_URL')."/public/spk/".$originalName,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return "success";
        } catch (\Throwable $th) {
            //throw $th;
        };
    }
}

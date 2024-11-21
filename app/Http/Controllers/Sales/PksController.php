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

class PksController extends Controller
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
        return view('sales.pks.list',compact('branch','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            $data=null;
            $quotation =null;
            if($request->id!=null){
                $spk = DB::table('sl_spk')->whereNull('deleted_at')->where('id',$request->id)->first();
            }
            return view('sales.pks.add',compact('now','spk'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $data = DB::table('sl_pks')
                ->leftJoin('sl_quotation_client','sl_quotation_client.id','sl_pks.quotation_client_id')
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->whereNull('sl_pks.deleted_at')
                ->whereNull('sl_spk.deleted_at')
                ->whereNull('sl_quotation_client.deleted_at')
                ->select('sl_pks.created_by','sl_pks.created_at','sl_pks.id','sl_pks.nomor','sl_spk.nomor as nomor_spk','sl_pks.tgl_pks','sl_quotation_client.nama_perusahaan','sl_quotation_client.layanan as kebutuhan','sl_pks.status_pks_id')
                ->get();

        foreach ($data as $key => $value) {
            $value->tgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_pks)->isoFormat('D MMMM Y');
            $value->created_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_pks')->where('id',$value->status_pks_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                                <a href="'.route('pks.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                    </div>';
        })
        ->editColumn('nomor', function ($data) {
            return '<a href="'.route('pks.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        })
        ->rawColumns(['aksi','nomor'])
        ->make(true);
    }

    public function availableSpk (Request $request){
        try {
            $data = DB::table('sl_spk')
                ->leftJoin('sl_leads','sl_leads.id','sl_spk.leads_id')
                ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                ->where('m_tim_sales_d.user_id',Auth::user()->id)
                ->whereNull('sl_spk.deleted_at')
                ->where('sl_spk.status_spk_id',2)
                ->select("sl_spk.id","sl_spk.nomor","sl_spk.nama_perusahaan","sl_spk.tgl_spk","sl_spk.kebutuhan")
                ->get();
            
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
            $dataSpk = DB::table('sl_spk')->where('id',$request->spk_id)->first();
            $quotationClient = DB::table('sl_quotation_client')->where('id',$dataSpk->quotation_client_id)->first();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('quotation_client_id',$dataSpk->quotation_client_id)->get();

            $newId = DB::table('sl_pks')->insertGetId([
                'quotation_client_id' => $dataSpk->quotation_client_id,
                'spk_id' => $dataSpk->id,
                'leads_id' => $dataSpk->leads_id,
                'nomor' => $this->generateNomor($quotationClient->leads_id,$quotation[0]->company_id),
                'tgl_pks' => $current_date_time,
                'nama_perusahaan' => $dataSpk->nama_perusahaan,
                'link_pks_disetujui' => null,
                'status_pks_id' => 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('quotation_client_id',$quotationClient->id)->update([
                'status_quotation_id' => 5,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            DB::table('sl_spk')->where('id',$dataSpk->id)->update([
                'status_spk_id' => 3,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('pks.view',$newId);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function generateNomor ($leadsId,$companyId){
        // generate nomor QUOT/SIG/AAABB-092024-00001
        $now = Carbon::now();

        $nomor = "PKS/";
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

        $jumlahData = DB::select("select * from sl_pks where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

    public function view (Request $request,$id){
        try {
            $data = DB::table('sl_pks')->where('id',$id)->first();
            $spk = DB::table('sl_spk')->where('id',$data->spk_id)->first();
            $spkD = DB::table('sl_spk_detail')->where('spk_id',$data->spk_id)->first();
            $dataQuotation = DB::table('sl_quotation')->where('id',$spkD->quotation_id)->whereNull('deleted_at')->first();

            $data->stgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$data->tgl_pks)->isoFormat('D MMMM Y');
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$data->created_at)->isoFormat('D MMMM Y');
            $quotationClient = DB::table('sl_quotation_client')->where('id',$data->quotation_client_id)->first();
            $quotation = DB::table('sl_quotation')->where('quotation_client_id',$data->quotation_client_id)->get();
            $data->status = DB::table('m_status_pks')->where('id',$data->status_pks_id)->first()->nama;

            return view('sales.pks.view',compact('dataQuotation','spk','data','quotation','quotationClient'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
        
    }

    public function uploadPks (Request $request) {
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $originalFileName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = $originalFileName.date("YmdHis").rand(10000,99999).".".$fileExtension;

            Storage::disk('pks')->put($originalName, file_get_contents($request->file('file')));
            
            DB::table('sl_pks')->where('id',$request->id)->update([
                'status_pks_id' => 6,
                'link_pks_disetujui' =>env('APP_URL')."/public/pks/".$originalName,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        };
    }

    public function approve(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $ot = $request->ot;
            $id = $request->id;
            $status = 1;
            if($ot==1){
                $status = 2;
            }else if($ot==2){
                $status = 3;
            }else if($ot==3){
                $status = 4;
            }else if($ot==4){
                $status = 5;
            }

            $approve ="ot".$ot;
            DB::table('sl_pks')->where('id',$id)->update([
                $approve => Auth::user()->full_name,
                'status_pks_id' => $status,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);



        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function aktifkanSite(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $pks = DB::table('sl_pks')->where('id',$request->id)->first();
            DB::table('sl_pks')->where('id',$request->id)->update([
                'ot5' => Auth::user()->full_name,
                'status_pks_id' => 7,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('quotation_client_id',$pks->quotation_client_id)->update([
                'status_quotation_id' => 6,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('sl_spk')->where('id',$pks->spk_id)->update([
                'status_spk_id' => 4,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function isiChecklist(Request $request,$id){
        $pks = DB::table('sl_pks')->where('id',$id)->first();
        $spkD = DB::table('sl_spk_detail')->where('spk_id',$pks->spk_id)->whereNull('deleted_at')->first();
        $quotation = DB::table('sl_quotation')->where('id',$spkD->quotation_id)->whereNull('deleted_at')->first();

        $listJabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
        $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
        $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
        $quotation->mulai_kontrak = Carbon::parse($quotation->mulai_kontrak)->format('d F Y');
        $quotation->kontrak_selesai = Carbon::parse($quotation->kontrak_selesai)->format('d F Y');
        $quotation->tgl_quotation = Carbon::parse($quotation->tgl_quotation)->format('d F Y');
        $quotation->tgl_penempatan = Carbon::parse($quotation->tgl_penempatan)->format('d F Y');

        $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
        $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();
        $sPersonil = "";
        $jPersonil = DB::select("SELECT sum(jumlah_hc) as jumlah_hc FROM sl_quotation_detail WHERE quotation_id = $quotation->id and deleted_at is null;");
        if($jPersonil!=null){
            if ($jPersonil[0]->jumlah_hc!=null && $jPersonil[0]->jumlah_hc!=0) {
                $sPersonil .= $jPersonil[0]->jumlah_hc." Manpower (";
                $detailPersonil = DB::table('sl_quotation_detail')
                ->whereNull('sl_quotation_detail.deleted_at')
                ->where('sl_quotation_detail.quotation_id',$quotation->id)
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
        $quotation->jumlah_personel = $sPersonil;

        return view('sales.pks.checklist-form',compact('pks','quotation','listJabatanPic','listTrainingQ','listTraining','salaryRuleQ','leads'));
    }

    public function saveChecklist(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            
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
                'joker_reliever' => $request->joker_reliever ,
                'syarat_invoice' => $request->syarat_invoice ,
                'alamat_penagihan_invoice' => $request->alamat_penagihan_invoice ,
                'catatan_site' => $request->catatan_site ,
                'status_serikat' => $request->status_serikat ,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('pks.view',$request->pksid);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function cetakPks (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table("sl_pks")->where("id",$id)->first();
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

            return view('sales.pks.cetakan.pks',compact('now','data','quotation','quotationClient','leads','company'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

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
use App\Http\Controllers\Sales\CustomerActivityController;

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
        return view('sales.spk.list',compact('branch','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function indexTerhapus (Request $request){

        return view('sales.spk.list-terhapus');
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            $quotation =null;
            $siteList = [];
            if($request->id!=null){
                $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$request->id)->first();
                if($quotation==null){
                    return redirect()->route('spk.add');
                }
                $siteList = DB::table('sl_quotation_site')
                            ->where('quotation_id',$quotation->id)
                            ->whereNull('deleted_at')
                            ->whereNotIn('id', function($query) {
                                $query->select('quotation_site_id')
                                    ->from('sl_spk_site')
                                    ->whereNull('deleted_at');
                            })
                            ->get();
            }
            $view = 'sales.spk.add';
            if($quotation==null){
                $view = 'sales.spk.add-2';
            }
            return view($view,compact('now','quotation','siteList'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $data = DB::table('sl_spk')
                ->leftJoin('sl_quotation','sl_quotation.id','sl_spk.quotation_id')
                ->whereNull('sl_spk.deleted_at')
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
    public function listTerhapus (Request $request){
        $data = DB::table('sl_spk')
                ->leftJoin('sl_quotation','sl_quotation.id','sl_spk.quotation_id')
                ->whereNotNull('sl_spk.deleted_at')
                ->select('sl_spk.deleted_at','sl_spk.deleted_by','sl_spk.id','sl_spk.nomor','sl_quotation.nomor as nomor_quotation','sl_spk.tgl_spk','sl_quotation.nama_perusahaan','sl_quotation.nama_site','sl_quotation.kebutuhan','sl_spk.status_spk_id')
                ->get();

        foreach ($data as $key => $value) {
            $value->tgl_spk = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_spk)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_spk')->where('id',$value->status_spk_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function availableQuotation (Request $request){
        try {
            // $data = DB::table('sl_quotation')
            //     ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
            //     ->leftJoin('sl_spk','sl_spk.quotation_id','=','sl_quotation.id')
            //     ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
            //     ->whereNull('sl_quotation.deleted_at')
            //     ->whereNull('sl_spk.id')
            //     ->whereNull('sl_spk.deleted_at')
            //     ->where('m_tim_sales_d.user_id',Auth::user()->id)
            //     ->select("sl_quotation.nomor","sl_quotation.id","sl_quotation.tgl_quotation","sl_quotation.nama_perusahaan","sl_quotation.jumlah_site","sl_quotation.kebutuhan","sl_quotation.kebutuhan as layanan")
            //     ->distinct()
            //     ->get();
                $data = DB::table('sl_quotation')
                    ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
                    ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                    ->whereNull('sl_quotation.deleted_at')
                    ->where('sl_quotation.is_aktif',1)
                    ->where('m_tim_sales_d.user_id',Auth::user()->id)
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                            ->from('sl_quotation_site')
                            ->whereRaw('sl_quotation_site.quotation_id = sl_quotation.id')
                            ->whereNull('sl_quotation_site.deleted_at')
                            ->whereNotExists(function($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('sl_spk_site')
                                    ->whereRaw('sl_spk_site.quotation_site_id = sl_quotation_site.id')
                                    ->whereNull('sl_spk_site.deleted_at');
                            });
                    })
                    ->select("sl_quotation.nomor","sl_quotation.id","sl_quotation.tgl_quotation","sl_quotation.nama_perusahaan","sl_quotation.jumlah_site","sl_quotation.kebutuhan","sl_quotation.kebutuhan as layanan")
                    ->distinct()
                    ->get();
            foreach ($data as $key => $value) {
                $value->quotation = $value->nomor;
                $value->tgl_quotation = Carbon::createFromFormat('Y-m-d',$value->tgl_quotation)->isoFormat('D MMMM Y');
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
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$request->quotation_id)->first();
            $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();

            $spkNomor = $this->generateNomor($quotation->leads_id,$quotation->company_id);
            $newId = DB::table('sl_spk')->insertGetId([
                'quotation_id' => $quotation->id,
                'leads_id' => $quotation->leads_id,
                'nomor' => $spkNomor,
                'nomor_quotation' => $quotation->nomor,
                'tgl_spk' => $request->tanggal_spk,
                'nama_perusahaan' => $quotation->nama_perusahaan,
                'kebutuhan_id' => $quotation->kebutuhan_id,
                'kebutuhan' => $quotation->kebutuhan,
                'jenis_site' => $quotation->jumlah_site,
                'tim_sales_id' => $leads->tim_sales_id,
                'tim_sales_d_id' => $leads->tim_sales_d_id,
                'link_spk_disetujui' => null,
                'status_spk_id' => 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            //simpan SPK Site
            $siteIds = $request->input('site_ids', []);
            foreach ($siteIds as $siteId) {
                $quotationSite = DB::table('sl_quotation_site')->where('id', $siteId)->first();
                DB::table('sl_spk_site')->insert([
                    'spk_id' => $newId,
                    'quotation_id' => $quotationSite->quotation_id,
                    'quotation_site_id' => $quotationSite->id,
                    'leads_id' => $quotationSite->leads_id,
                    'nama_site' => $quotationSite->nama_site,
                    'provinsi_id' => $quotationSite->provinsi_id,
                    'provinsi' => $quotationSite->provinsi,
                    'kota_id' => $quotationSite->kota_id,
                    'kota' => $quotationSite->kota,
                    'ump' => $quotationSite->ump,
                    'umk' => $quotationSite->umk,
                    'nominal_upah' => $quotationSite->nominal_upah,
                    'penempatan' => $quotationSite->penempatan,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'status_quotation_id' => 3,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($quotation->leads_id);

            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $quotation->leads_id,
                'quotation_id' => $quotation->id,
                'spk_id' => $newId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'SPK',
                'notes' => 'SPK dengan nomor :'.$spkNomor.' terbentuk dari Quotation dengan nomor :'.$quotation->nomor,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::commit();
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
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$data->quotation_id)->first();
            $data->status = DB::table('m_status_spk')->where('id',$data->status_spk_id)->first()->nama;
            $data->site = DB::table('sl_spk_site')->where('spk_id',$data->id)->whereNull('deleted_at')->get();

            return view('sales.spk.view',compact('data','quotation'));
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
            $quotation = DB::table("sl_quotation")->where("id",$data->quotation_id)->get();
            $leads = DB::table("sl_leads")->where("id",$data->leads_id)->first();
            $jabatanPic = DB::table("m_jabatan_pic")->where("id",$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama;
            }

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
            $quotation[0]->site = DB::table('sl_quotation_site')->where('quotation_id',$quotation[0]->id)->get();
            return view('sales.spk.cetakan.spk',compact('now','data','quotation','leads','company'));
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

    public function ajukanUlangQuotation (Request $request,$spk){
        $current_date_time = Carbon::now()->toDateTimeString();
        $current_date = Carbon::now()->toDateString();
        try {
            $spk = DB::table('sl_spk')->where('id',$spk)->first();

            DB::beginTransaction();

            $qasalId = $spk->quotation_id;
            $qtujuan = DB::table("sl_quotation")->where('id',$qasalId)->first();

            $dataToInsertQuotation = (array) $qtujuan;
            unset($dataToInsertQuotation['id']);
            unset($dataToInsertQuotation['nomor']);

            $nomorQuotationBaru = $this->generateNomorQuotation($qtujuan->leads_id,$qtujuan->company_id);
            $dataToInsertQuotation['nomor'] = $nomorQuotationBaru;
            $dataToInsertQuotation['revisi'] = $qtujuan->revisi+1;
            $dataToInsertQuotation['alasan_revisi'] = $request->alasan;
            $dataToInsertQuotation['quotation_asal_id'] = $qtujuan->id;
            $dataToInsertQuotation['created_at'] = $current_date_time;
            $dataToInsertQuotation['created_by'] = Auth::user()->full_name;
            $dataToInsertQuotation['updated_at'] = null;
            $dataToInsertQuotation['updated_by'] = null;

            $dataToInsertQuotation['ot1'] = null;
            $dataToInsertQuotation['ot2'] = null;
            $dataToInsertQuotation['ot3'] = null;
            $isAktif = 1;
            $statusQuotation = 1;
            //jika top lebih dari 7 hari
            if($qtujuan->top=="Lebih Dari 7 Hari"){
                $isAktif = 0;
                $statusQuotation = 2;
            }

            // jika persentasi mf kurang dari 7
            if ($qtujuan->persentase < 7) {
                $isAktif = 0;
                $statusQuotation = 2;
            }

            $dataToInsertQuotation['status_quotation_id'] = $statusQuotation;
            $dataToInsertQuotation['is_aktif'] = $isAktif;
            $dataToInsertQuotation['step'] = 1;
            $qtujuanId = DB::table('sl_quotation')->insertGetId($dataToInsertQuotation);

            //Site
            $site = DB::table("sl_quotation_site")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_site")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);

            foreach ($site as $ks => $site) {
                $dataToInsert = (array) $site;
                unset($dataToInsert['id']);
                $dataToInsert['quotation_id'] = $qtujuanId;
                $dataToInsert['created_at'] = $current_date_time;
                $dataToInsert['created_by'] = Auth::user()->full_name;

                $newSiteId = DB::table("sl_quotation_site")->insertGetId($dataToInsert);

                $detail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where('quotation_site_id',$site->id)->where('quotation_id',$qasalId)->get();
                DB::table("sl_quotation_detail")->where('quotation_id',$qasalId)->update([
                    "deleted_at" => $current_date_time ,
                    "deleted_by" => Auth::user()->full_name,
                ]);

                foreach ($detail as $key => $value) {
                    $dataToInsert = (array) $value;
                    unset($dataToInsert['id']);
                    $dataToInsert['quotation_id'] = $qtujuanId;
                    $dataToInsert['quotation_site_id'] = $newSiteId;
                    $dataToInsert['created_at'] = $current_date_time;
                    $dataToInsert['created_by'] = Auth::user()->full_name;

                    $newId = DB::table("sl_quotation_detail")->insertGetId($dataToInsert);

                    // Quotation Detail Requirement
                    $requirement = DB::table("sl_quotation_detail_requirement")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_detail_requirement")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($requirement as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;

                        DB::table("sl_quotation_detail_requirement")->insert($dataToInsertD);
                    }

                    // Quotation Detail hpp
                    $detailhpp = DB::table("sl_quotation_detail_hpp")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_detail_hpp")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($detailhpp as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;

                        DB::table("sl_quotation_detail_hpp")->insert($dataToInsertD);
                    }

                    // Quotation Detail harga jual
                    $detailhargajual = DB::table("sl_quotation_detail_coss")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_detail_coss")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($detailhargajual as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;

                        DB::table("sl_quotation_detail_coss")->insert($dataToInsertD);
                    }

                    // Quotation Detail Tunjangan
                    $tunjangan = DB::table("sl_quotation_detail_tunjangan")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_detail_tunjangan")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($tunjangan as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;

                        DB::table("sl_quotation_detail_tunjangan")->insert($dataToInsertD);
                    }

                    // Quotation Kaporlap
                    $kaporlap = DB::table("sl_quotation_kaporlap")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_kaporlap")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($kaporlap as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;

                        DB::table("sl_quotation_kaporlap")->insert($dataToInsertD);
                    }
                }
            }

            // Quotation Devices
            $devices = DB::table("sl_quotation_devices")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_devices")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($devices as $keyd => $valued) {
                $dataToInsertD = (array) $valued;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_devices")->insert($dataToInsertD);
            }

            // Quotation Chemical
            $chemical = DB::table("sl_quotation_chemical")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_chemical")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($chemical as $keyd => $valued) {
                $dataToInsertD = (array) $valued;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_chemical")->insert($dataToInsertD);
            }

            // Quotation Ohc
            $ohc = DB::table("sl_quotation_ohc")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_ohc")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($ohc as $keyd => $valued) {
                $dataToInsertD = (array) $valued;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_ohc")->insert($dataToInsertD);
            }

            // Quotation Aplikasi
            $aplikasi = DB::table("sl_quotation_aplikasi")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_aplikasi")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($aplikasi as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_aplikasi")->insert($dataToInsertD);
            }

            // Quotation Kerjasama
            $kerjasama = DB::table("sl_quotation_kerjasama")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_kerjasama")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($kerjasama as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_kerjasama")->insert($dataToInsertD);
            }

            // Quotation PIC
            $pic = DB::table("sl_quotation_pic")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_pic")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($pic as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_pic")->insert($dataToInsertD);
            }

            // Quotation Training
            $training = DB::table("sl_quotation_training")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_training")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($training as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;

                DB::table("sl_quotation_training")->insert($dataToInsertD);
            }

            // hapus data yang sudah di ajukan ulang
            DB::table("sl_quotation")->where('id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            DB::commit();
            // hapus spk yang diajukan ulang
            DB::table('sl_spk')->where('id',$spk->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            //insert ke activity sebagai activity pertama
            $qasal = DB::table('sl_quotation')->where('id',$qasalId)->first();
            $customerActivityController = new CustomerActivityController();

            // buat activity baru dari quotation yang diajukan ulang
            $nomorActivity = $customerActivityController->generateNomor($qtujuan->leads_id);
            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $qtujuan->leads_id,
                'quotation_id' => $qasalId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$qasal->nomor.' di ajukan ulang',
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            // buat activity baru dari quotation baru
            $nomorActivity = $customerActivityController->generateNomor($qtujuan->leads_id);
            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $qtujuan->leads_id,
                'quotation_id' => $qtujuanId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$nomorQuotationBaru.' terbentuk dari ajukan ulang quotation dengan nomor :'.$qasal->nomor,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return redirect()->route('quotation');

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function generateNomorQuotation ($leadsId,$companyId){
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

        $jumlahData = DB::select("select * from sl_quotation where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }
}

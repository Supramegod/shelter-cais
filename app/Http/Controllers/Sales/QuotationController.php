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
use App\Http\Controllers\Helper\QuotationService;
use App\Http\Controllers\Sales\CustomerActivityController;

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

        $listStatus = DB::table('m_status_quotation')->whereNull('deleted_at')->get();
        $quotationAsal = DB::table('sl_quotation')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.nomor','sl_quotation.id')
        ->whereNull('sl_quotation.deleted_at')->whereNull('sl_leads.deleted_at')
        ->where('m_tim_sales_d.user_id',Auth::user()->id)
        ->where("sl_quotation.is_aktif",1)->get();
        return view('sales.quotation.list',compact('listStatus','quotationAsal','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $province = DB::connection('mysqlhris')->table('m_province')->get();
            foreach ($province as $key => $value) {
                $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('is_aktif',1)->where('province_id',$value->id)->first();
                $value->ump = "UMP : Rp. 0";
                if($dataUmp !=null){
                    $value->ump = "UMP : Rp. ".number_format($dataUmp->ump,2,",",".");
                }
            }

            $tipe = $request->tipe;

            $leads = null;
            if($request->leads_id != null) {
                $leads = DB::table('sl_leads')->where('id',$request->leads_id)->first();
            };
            
            $view = "";
            if($tipe=="Quotation Baru"){
                $view = 'sales.quotation.add';
            }else if($tipe=="Adendum"){
                $view = 'sales.quotation.add-adendum';
            }else if($tipe=="Quotation Lanjutan"){
                $view = 'sales.quotation.add-quotation-lanjutan';
            }

            return view($view,compact('now','company','province','tipe','leads'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function copyQuotation(Request $request,$qasalId,$qtujuanId){
        $dataQuotationAsal = DB::table("sl_quotation")->where('id',$qasalId)->first();
        $dataQuotationTujuan = DB::table("sl_quotation")->where('id',$qtujuanId)->first();

        $current_date_time = Carbon::now()->toDateTimeString();
        $current_date = Carbon::now()->toDateString();
        try {
            DB::beginTransaction();

            $sourceQuotation = DB::table('sl_quotation')->where('id', $qasalId)->first();
            $dataToUpdateQuotation = (array) $sourceQuotation;
            unset($dataToUpdateQuotation['id']);
            unset($dataToUpdateQuotation['nomor']);
            unset($dataToUpdateQuotation['nama_site']);
            unset($dataToUpdateQuotation['penempatan']);
            unset($dataToUpdateQuotation['provinsi_id']);
            unset($dataToUpdateQuotation['provinsi']);
            unset($dataToUpdateQuotation['kota_id']);
            unset($dataToUpdateQuotation['kota']);
            unset($dataToUpdateQuotation['upah']);
            unset($dataToUpdateQuotation['nominal_upah']);
            unset($dataToUpdateQuotation['hitungan_upah']);

            $dataToUpdateQuotation['updated_at'] = $current_date_time;
            $dataToUpdateQuotation['updated_by'] = Auth::user()->full_name;
            DB::table('sl_quotation')->where('id', $qtujuanId)->update($dataToUpdateQuotation);

            // QUotation Detail
            $detail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_detail")->where('quotation_id',$qtujuanId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);

            foreach ($detail as $key => $value) {
                $dataToInsert = (array) $value;
                unset($dataToInsert['id']);
                $dataToInsert['quotation_id'] = $qtujuanId;
                $dataToInsert['created_at'] = $current_date_time;
                $dataToInsert['created_by'] = Auth::user()->full_name;
                
                $newId = DB::table("sl_quotation_detail")->insertGetId($dataToInsert);

                // Quotation Chemical
                $chemical = DB::table("sl_quotation_chemical")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                DB::table("sl_quotation_chemical")->where("quotation_detail_id",$value->id)->where('quotation_id',$qtujuanId)->update([
                    "deleted_at" => $current_date_time ,
                    "deleted_by" => Auth::user()->full_name,
                ]);
                foreach ($chemical as $keyd => $valued) {
                    $dataToInsertD = (array) $valued;
                    unset($dataToInsertD['id']);
                    $dataToInsertD['quotation_id'] = $qtujuanId;
                    $dataToInsertD['quotation_detail_id'] = $newId;
                    $dataToInsertD['created_at'] = $current_date_time;
                    $dataToInsertD['created_by'] = Auth::user()->full_name;
                    
                    DB::table("sl_quotation_chemical")->insert($dataToInsertD);
                }

                // Quotation Detail Requirement
                $requirement = DB::table("sl_quotation_detail_requirement")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                DB::table("sl_quotation_detail_requirement")->where("quotation_detail_id",$value->id)->where('quotation_id',$qtujuanId)->update([
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
                
                // Quotation Detail Tunjangan
                $tunjangan = DB::table("sl_quotation_detail_tunjangan")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                DB::table("sl_quotation_detail_tunjangan")->where("quotation_detail_id",$value->id)->where('quotation_id',$qtujuanId)->update([
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
                DB::table("sl_quotation_kaporlap")->where("quotation_detail_id",$value->id)->where('quotation_id',$qtujuanId)->update([
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

            // Quotation Devices
            $devices = DB::table("sl_quotation_devices")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_devices")->where('quotation_id',$qtujuanId)->update([
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

            // Quotation Ohc
            $ohc = DB::table("sl_quotation_ohc")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_ohc")->where('quotation_id',$qtujuanId)->update([
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
            DB::table("sl_quotation_aplikasi")->where('quotation_id',$qtujuanId)->update([
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
            DB::table("sl_quotation_kerjasama")->where('quotation_id',$qtujuanId)->update([
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
            DB::table("sl_quotation_pic")->where('quotation_id',$qtujuanId)->update([
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
            DB::table("sl_quotation_training")->where('quotation_id',$qtujuanId)->update([
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

            DB::commit();

            return redirect()->route('quotation.step',['id'=>$qtujuanId,'step'=>'1']);

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    public function ajukanUlangQuotation (Request $request,$qasalId){
        $current_date_time = Carbon::now()->toDateTimeString();
        $current_date = Carbon::now()->toDateString();
        try {
            DB::beginTransaction();
            $qtujuan = DB::table("sl_quotation")->where('id',$qasalId)->first();
            $leads = DB::table("sl_leads")->where('id',$qtujuan->leads_id)->first();

            $dataToInsertQuotation = (array) $qtujuan;
            unset($dataToInsertQuotation['id']);
            unset($dataToInsertQuotation['nomor']);

            $nomorQuotationBaru = $this->generateNomor($qtujuan->leads_id,$qtujuan->company_id);
            $dataToInsertQuotation['nomor'] = $nomorQuotationBaru;
            $dataToInsertQuotation['revisi'] = $qtujuan->revisi+1;
            $dataToInsertQuotation['alasan_revisi'] = $request->alasan;            
            $dataToInsertQuotation['quotation_asal_id'] = $qtujuan->id;
            $dataToInsertQuotation['created_at'] = $current_date_time;
            $dataToInsertQuotation['created_by'] = Auth::user()->full_name;
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

                    // Quotation Chemical
                    $chemical = DB::table("sl_quotation_chemical")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_chemical")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($chemical as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;
                        
                        DB::table("sl_quotation_chemical")->insert($dataToInsertD);
                    }

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


            DB::commit();

            return redirect()->route('quotation.step',['id'=>$qtujuanId,'step'=>'1']);

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function cetakChecklist (Request $request,$id){
        try {
            $pks = DB::table('sl_pks')->where('id',$id)->first();
            $spk = DB::table('sl_spk')->where('id',$pks->spk_id)->whereNull('deleted_at')->first();
            $quotation = DB::table('sl_quotation')->where('id',$spk->quotation_id)->whereNull('deleted_at')->first();
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
            $quotation = DB::table("sl_quotation")->where('id',$spk->quotation_id)->first();
            $quotation->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$quotation->kebutuhan_id)->orderBy('name','asc')->get();
            $quotation->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$spk->quotation_id)->whereNull('deleted_at')->get();

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

            $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();

            $listPic = DB::table('sl_quotation_pic')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
            return view('sales.quotation.cetakan.checklist',compact('listPic','listTraining','listTrainingQ','salaryRuleQ','salaryRule','leads','quotation','now'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function step(Request $request,$id){
        try {
            $quotation = DB::table("sl_quotation")->where('id',$id)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            $quotation->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$quotation->kebutuhan_id)->orderBy('name','asc')->get();
            $quotation->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            $quotation->quotation_site = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();

            $province = DB::connection('mysqlhris')->table('m_province')->get();
            // $dataProvinsi = DB::connection('mysqlhris')->table('m_province')->where('id',$quotation->provinsi_id)->first();
            // $dataKota = DB::connection('mysqlhris')->table('m_city')->where('id',$quotation->kota_id)->first();
            
            // $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('province_id',$dataProvinsi->id)->first();
            // $dataProvinsi->ump = "Rp. 0";
            // if($dataUmp !=null){
            //     $dataProvinsi->ump = "Rp. ".number_format($dataUmp->ump,0,",",".");
            // }
            // $dataUmk = DB::table("m_umk")->whereNull('deleted_at')->where('city_id',$dataKota->id)->first();
            // $dataKota->umk = "Rp. 0";
            // if($dataUmk !=null){
            //     $dataKota->umk = "Rp. ".number_format($dataUmk->umk,0,",",".");
            // }

            // foreach ($province as $key => $value) {
            //     $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('province_id',$value->id)->first();
            //     $value->ump = "Rp. 0";
            //     if($dataUmp !=null){
            //         $value->ump = "Rp. ".number_format($dataUmp->ump,0,",",".");
            //     }
            // }
            $kota = DB::connection('mysqlhris')->table('m_city')->get();
            $manfee = DB::table('m_management_fee')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
           
            //step 6 - aplikasi pendukung
            $aplikasiPendukung = null;
            $arrAplikasiSel = [];
            if($request->step==6){
                $aplikasiPendukung = DB::table('m_aplikasi_pendukung')->whereNull('deleted_at')->get();
                $listApp = DB::table('sl_quotation_aplikasi')->where('quotation_id',$id)->whereNull('deleted_at')->get();

                foreach ($listApp as $key => $value) {
                    array_push($arrAplikasiSel,$value->aplikasi_pendukung_id);
                }
            }
            
            //step 7 - kaporlap
            $listKaporlap = null;
            $listJenis = [];
            if($request->step==7){
                $arrKaporlap = [1,2,3,4,5];
                if($quotation->kebutuhan_id != 1){
                    $arrKaporlap = [5];
                }

                $listJenis = DB::table('m_jenis_barang')->whereIn('id',$arrKaporlap)->get();
                $listKaporlap = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',$arrKaporlap)
                                    ->orderBy("nama","asc")
                                    ->get();

                foreach ($listKaporlap as $key => $kaporlap) {
                    foreach ($quotation->quotation_detail as $kKd => $vKd) {
                        $kaporlap->{'jumlah_'.$vKd->id} = 0;
                        $kebkap = DB::table('sl_quotation_kaporlap')->whereNull('deleted_at')->where('barang_id',$kaporlap->id)->where('quotation_detail_id',$vKd->id)->first();
                        if($kebkap !=null){
                            $kaporlap->{'jumlah_'.$vKd->id} = $kebkap->jumlah;
                        }
                    }
                }
            }
            //step 8 - devices
            $listDevices = null;
            if($request->step==8){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[9,10,11,12,17])->get();
                $listDevices = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[8,9,10,11,12,17])
                                    ->get();

                foreach ($listDevices as $key => $devices) {
                    $devices->jumlah = 0;
                    $kebkap = DB::table('sl_quotation_devices')->whereNull('deleted_at')->where('barang_id',$devices->id)->where('quotation_id',$id)->first();
                    if($kebkap !=null){
                        $devices->jumlah = $kebkap->jumlah;
                    }
                }
            }

            //step 9 - chemical
            $listChemical = null;
            if($request->step==9){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[13,14,15,16])->get();
                $listChemical = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[13,14,15,16])
                                    ->get();
                foreach ($listChemical as $key => $value) {
                    $value->harga = number_format($value->harga,0,",",".");
                }
            }


            //step 10 ohc
            $listOhc = null;
            if($request->step==10){
                $listJenis = DB::table('m_jenis_barang')->whereIn('id',[6,7,8])->get();
                $listOhc = DB::table('m_barang')
                                    ->whereNull('deleted_at')
                                    ->whereIn('jenis_barang_id',[6,7,8])
                                    ->get();
                foreach ($listOhc as $key => $value) {
                    $value->harga = number_format($value->harga,0,",",".");
                }
            }
            
            // step 11 Harga Jual
            $leads = null;
            $data = null;
            $daftarTunjangan = null;
            $listTraining = [];
            $listTrainingQ = [];
            $calcQuotation = null;

            if($request->step==11){

                $quotationService = new QuotationService();
                $calcQuotation = $quotationService->calculateQuotation($quotation);
                $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");
                $data = DB::table('sl_quotation')->where('id',$quotation->id)->first();
                $data->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
                $data->totalHc = 0;
                foreach ($quotation->quotation_site as $key => $site) {
                    $site->jumlah_detail = 0;
                    foreach ($quotation->quotation_detail as $kd => $vd) {
                        if($vd->quotation_site_id == $site->id){
                            $site->jumlah_detail += 1;
                        }
                    }
                }

                foreach ($data->detail as $key => $value) {
                    $data->totalHc += $value->jumlah_hc;
                }
                $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();         
            }
            $isEdit = false;

            if(isset($request->edit)){
                $isEdit = true;
            }

            $listJabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
            $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
            $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();

            return view('sales.quotation.edit-'.$request->step,compact('calcQuotation','listJabatanPic','listTrainingQ','listTraining','daftarTunjangan','salaryRuleQ','data','leads','isEdit','listChemical','listDevices','listOhc','listJenis','listKaporlap','jenisPerusahaan','aplikasiPendukung','arrAplikasiSel','manfee','kota','province','quotation','request','company','salaryRule'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view (Request $request,$id){
        try {
            $canCreateSpk = 1;
            $quotation = DB::table('sl_quotation')->where('id',$id)->first();

            if ($quotation->is_aktif != 1 ) {
                $canCreateSpk = 0;
            }
            $quotation->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            $quotation->totalHc = 0;
            $quotation->umk = 0;
            $quotation->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->first();
            $quotation->pks = DB::table('sl_pks')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->first();
            foreach ($quotation->detail as $key => $value) {
                $quotation->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($quotation->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$quotation->kota_id)->first();

                if($dataUmk!=null){
                    $quotation->umk = $dataUmk->umk;
                }
            }
            
            $quotation->quotation_site = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotation->quotation_site as $key => $site) {
                $site->jumlah_detail = 0;
                foreach ($quotation->detail as $kd => $vd) {
                    if($vd->quotation_site_id == $site->id){
                        $site->jumlah_detail += 1;
                    }
                }
            }

            $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');

            //format
            $quotation->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$quotation->mulai_kontrak)->isoFormat('D MMMM Y');
            $quotation->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$quotation->kontrak_selesai)->isoFormat('D MMMM Y');
            $quotation->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$quotation->tgl_penempatan)->isoFormat('D MMMM Y');
            $quotation->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$quotation->created_at)->isoFormat('D MMMM Y');
            $quotation->stgl_quotation = Carbon::createFromFormat('Y-m-d',$quotation->tgl_quotation)->isoFormat('D MMMM Y');

            $quotation->salary_rule = "";
            $salaryRuleList = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();
            if($salaryRuleList != null){
                $quotation->salary_rule = $salaryRuleList->nama_salary_rule;
            }

            $quotation->manajemen_fee = "";
            $manajemenFeeList = DB::table('m_management_fee')->where('id',$quotation->management_fee_id)->first();
            if($manajemenFeeList != null){
                $quotation->manajemen_fee = $manajemenFeeList->nama;
            }
            $statusQuotation = DB::table('m_status_quotation')->where('id',$quotation->status_quotation_id)->first();
            $quotation->status = $statusQuotation->nama;

            $aplikasiPendukung = DB::table('sl_quotation_aplikasi')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            foreach ($aplikasiPendukung as $key => $value) {
                $app = DB::table('m_aplikasi_pendukung')->where('id',$value->aplikasi_pendukung_id)->first();
                $value->link_icon = $app->link_icon;
            }

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kaporlap where deleted_at is null and jumlah>0 and quotation_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_ohc where deleted_at is null and jumlah>0 and quotation_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_devices where deleted_at is null and jumlah>0 and quotation_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_chemical where deleted_at is null and jumlah>0 and quotation_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kaporlap')->where('jumlah','>',0)->where('quotation_id',$id)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_ohc')->where('quotation_id',$id)->where('jumlah','>',0)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_devices')->where('quotation_id',$id)->where('jumlah','>',0)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_chemical')->where('quotation_id',$id)->where('jumlah','>',0)->whereNull('deleted_at')->get();

            $quotation->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$quotation->kebutuhan_id)->orderBy('name','asc')->get();
            $quotation->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();

            $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");

            $jumlahHc = 0;
            foreach ($quotation->quotation_detail as $jhc) {
                $jumlahHc += $jhc->jumlah_hc;
            }

            $quotationService = new QuotationService();
            $calcQuotation = $quotationService->calculateQuotation($quotation);
            $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");
            $quotation->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
            $quotation->totalHc = 0;
            foreach ($quotation->quotation_site as $key => $site) {
                $site->jumlah_detail = 0;
                foreach ($quotation->quotation_detail as $kd => $vd) {
                    if($vd->quotation_site_id == $site->id){
                        $site->jumlah_detail += 1;
                    }
                }
            }

            foreach ($quotation->detail as $key => $value) {
                $quotation->totalHc += $value->jumlah_hc;
            }
            
            $listPic = DB::table('sl_quotation_pic')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
            $kebutuhanDetail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
            foreach ($kebutuhanDetail as $kkd => $vkd) {
                $vkd->requirement = DB::table('sl_quotation_detail_requirement')->where('quotation_detail_id',$vkd->id)->whereNull('deleted_at')->get();
            }
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();

            $quotationTujuan = DB::table('sl_quotation')
            ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
            ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
            ->select('sl_quotation.nomor','sl_quotation.id')
            ->whereNull('sl_quotation.deleted_at')
            ->whereNull('sl_leads.deleted_at')
            ->where("sl_quotation.step","!=",100)
            ->where("sl_quotation.id",$quotation->id)
            ->where('m_tim_sales_d.user_id',Auth::user()->id)
            ->get();
            
            return view('sales.quotation.view',compact('canCreateSpk','quotationTujuan','quotation','salaryRuleQ','listPic','daftarTunjangan','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','leads','aplikasiPendukung'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save (Request $request){
        try {
            DB::beginTransaction();
            $newId = null;
            $current_date_time = Carbon::now()->toDateTimeString();
            $current_date = Carbon::now()->toDateString();
            $kebutuhan = DB::table('m_kebutuhan')->where('id',$request->layanan)->first();
            $company = DB::connection('mysqlhris')->table('m_company')->where('id',$request->entitas)->first();
            $leads = DB::table('sl_leads')->where('id',$request->perusahaan_id)->first();

            $quotationNomor = $this->generateNomor($request->perusahaan_id,$request->entitas);
            $newId = DB::table('sl_quotation')->insertGetId([
                'nomor' => $quotationNomor,
                'tgl_quotation' => $current_date,
                'leads_id' => $request->perusahaan_id,
                'jumlah_site' =>  $request->jumlah_site,
                'nama_perusahaan' => $leads->nama_perusahaan,
                'kebutuhan_id' => $request->layanan,
                'kebutuhan' => $kebutuhan->nama,
                'company_id' => $request->entitas,
                'company' => $company->name,
                'step' => 1,
                'status_quotation_id' =>1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_pic')->insert([
                'quotation_id' => $newId,
                'leads_id' => $request->perusahaan_id,
                'nama' => $leads->pic,
                'jabatan_id' => $leads->jabatan_id,
                'jabatan' => $leads->jabatan,
                'no_telp' => $leads->no_telp,
                'email' => $leads->email,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            if ($request->jumlah_site=="Multi Site") {
                foreach ($request->multisite as $key => $value) {
                    $province = DB::connection('mysqlhris')->table('m_province')->where('id',$request->provinsi_multi[$key])->first();
                    $city = DB::connection('mysqlhris')->table('m_city')->where('id',$request->kota_multi[$key])->first();

                    $ump = 0;
                    $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('is_aktif',1)->where('province_id',$province->id)->first();
                    if($dataUmp !=null){
                        $ump = $dataUmp->ump;
                    }

                    $umk = 0;
                    $dataUmk = DB::table("m_umk")->whereNull('deleted_at')->where('is_aktif',1)->where('city_id',$city->id)->first();
                    if($dataUmk !=null){
                        $umk = $dataUmk->umk;
                    }

                    DB::table('sl_quotation_site')->insert([
                        'quotation_id' => $newId,
                        'leads_id' => $request->perusahaan_id,
                        'nama_site' => $value,
                        'provinsi_id' => $province->id,
                        'provinsi' => $province->name,
                        'kota_id' => $city->id,
                        'kota' => $city->name,
                        'ump' => $ump,
                        'umk' => $umk,
                        'penempatan' => $request->penempatan_multi[$key],
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
            }else{
                $province = DB::connection('mysqlhris')->table('m_province')->where('id',$request->provinsi)->first();
                $city = DB::connection('mysqlhris')->table('m_city')->where('id',$request->kota)->first();

                $ump = 0;
                $dataUmp = DB::table("m_ump")->whereNull('deleted_at')->where('is_aktif',1)->where('province_id',$province->id)->first();
                if($dataUmp !=null){
                    $ump = $dataUmp->ump;
                }

                $umk = 0;
                $dataUmk = DB::table("m_umk")->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$city->id)->first();
                if($dataUmk !=null){
                    $umk = $dataUmk->umk;
                }
                
                DB::table('sl_quotation_site')->insert([
                    'quotation_id' => $newId,
                    'leads_id' => $request->perusahaan_id,
                    'nama_site' => $request->nama_site,
                    'provinsi_id' => $province->id,
                    'provinsi' => $province->name,
                    'kota_id' => $city->id,
                    'kota' => $city->name,
                    'ump' => $ump,
                    'umk' => $umk,
                    'penempatan' => $request->penempatan,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($request->perusahaan_id);

            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $request->perusahaan_id,
                'quotation_id' => $newId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$quotationNomor.' terbentuk',
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::commit();
            
            return redirect()->route('quotation.step',['id'=>$newId,'step'=>'1']);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit1 (Request $request){
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            $current_date = Carbon::now()->toDateString();

            $newStep = 2;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'jenis_kontrak' => $request->jenis_kontrak,
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::commit();
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'2']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit2 (Request $request){
        if($request->ada_cuti=="Tidak Ada"){
            $request->macam_cuti = "Tidak Ada";
            $request->gaji_saat_cuti =null;
            $request->prorate =null;
        }else{
            $request->macam_cuti = implode(",",$request->cuti);
            if(in_array("Cuti Melahirkan",$request->cuti)){
                if($request->gaji_saat_cuti!="Prorate"){
                    $request->prorate =null;
                }
            }else{
                $request->gaji_saat_cuti =null;
                $request->prorate =null;
            }
            if(!in_array("Cuti Kematian",$request->cuti)){
                $request->hari_cuti_kematian =null;
            }
            if(!in_array("Istri Melahirkan",$request->cuti)){
                $request->hari_istri_melahirkan =null;
            }
            if(!in_array("Cuti Menikah",$request->cuti)){
                $request->hari_cuti_menikah =null;
            }
        }
        
        try {
            $validator = Validator::make($request->all(), [
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
                    'mulai_kontrak' => $request->mulai_kontrak,
                    'kontrak_selesai' => $request->kontrak_selesai,
                    'tgl_penempatan' => $request->tgl_penempatan,
                    'salary_rule_id' => $request->salary_rule,
                    'top' => $request->top,
                    'jumlah_hari_invoice' => $request->jumlah_hari_invoice,
                    'tipe_hari_invoice' => $request->tipe_hari_invoice,
                    'evaluasi_kontrak' => $request->evaluasi_kontrak,
                    'durasi_kerjasama' => $request->durasi_kerjasama,
                    'durasi_karyawan' => $request->durasi_karyawan,
                    'evaluasi_karyawan' => $request->evaluasi_karyawan,
                    'step' => 3,
                    'cuti' => $request->macam_cuti,
                    'hari_cuti_kematian' => $request->hari_cuti_kematian,
                    'hari_istri_melahirkan' => $request->hari_istri_melahirkan,
                    'hari_cuti_menikah' => $request->hari_cuti_menikah,
                    'gaji_saat_cuti' => $request->gaji_saat_cuti,
                    'prorate' => $request->prorate,
                    'shift_kerja' => $request->shift_kerja ,
                    'jam_kerja' => $request->jam_kerja ,
                    // 'mulai_kerja' => $request->mulai_kerja ,
                    // 'selesai_kerja' => $request->selesai_kerja ,
                    // 'sistem_kerja' => $request->sistem_kerja ,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                $newStep = 3;
                $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
                if($dataQuotation->step>$newStep){
                    $newStep = $dataQuotation->step;
                }
                if($request->edit==1){
                    $newStep = $dataQuotation->step;
                }
                
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'step' => $newStep,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                if($request->edit==0){
                    return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'3']);
                }else{
                    return redirect()->route('quotation.view',$request->id);
                }
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
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            // $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'4']);
            }else{
                return redirect()->route('quotation.view',$dataQuotation->id);
            }
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit4 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            $upah = $request['upah'];
            $manfee = $request['manajemen_fee'];
            $presentase = $request['persentase'];
            $hitunganUpah = "Per Bulan";

            $customUpah = 0;
            if($upah == "Custom"){
                $hitunganUpah = $request['hitungan_upah'];
                $customUpah = str_replace(".","",$request['custom-upah']);

                if($hitunganUpah=="Per Hari"){
                    $customUpah = $customUpah*21;
                }else if ($hitunganUpah=="Per Jam") {
                    $customUpah = $customUpah*21*8;
                }
            }
            
            $newStep = 5;
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            if($request->lembur!="Flat"){
                $request->nominal_lembur = null;
                $request->jenis_bayar_lembur = null;
            }else{
                $request->nominal_lembur = str_replace(".","",$request->nominal_lembur);
            }

            if($request->tunjangan_holiday!="Flat"){
                $request->nominal_tunjangan_holiday = null;
                $request->jenis_bayar_tunjangan_holiday = null;
            }else{
                $request->nominal_tunjangan_holiday = str_replace(".","",$request->nominal_tunjangan_holiday);
            }

            if($request->ada_lembur=="Tidak Ada"){
                $request->lembur ="Tidak Ada";
            }
            if($request->ada_kompensasi=="Tidak Ada"){
                $request->kompensasi ="Tidak Ada";
            }
            if($request->ada_thr=="Tidak Ada"){
                $request->thr ="Tidak Ada";
            }
            if($request->ada_tunjangan_holiday=="Tidak Ada"){
                $request->tunjangan_holiday ="Tidak Ada";
            }

            //rubah custom upah untuk quotation site
            $quotationSite = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            foreach ($quotationSite as $key => $site) {
                $nominalUpah = 0;
                if($upah == "Custom"){
                    $nominalUpah = $customUpah;
                }else{
                    //cari ump / umk
                    if($upah =="UMP"){
                        $dataUmp = DB::table("m_ump")->where('is_aktif',1)->whereNull('deleted_at')->where('province_id',$site->provinsi_id)->first();
                        if($dataUmp !=null){
                            $nominalUpah = $dataUmp->ump;
                        }
                    }else if ($upah =="UMK") {
                        $dataUmk = DB::table("m_umk")->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$site->kota_id)->first();
                        if($dataUmk !=null){
                            $nominalUpah = $dataUmk->umk;
                        }
                    }
                }
                DB::table('sl_quotation_site')->where('id',$site->id)->update([
                    'nominal_upah' => $nominalUpah,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'upah' => $upah,
                'nominal_upah' => 0,
                'hitungan_upah' => $hitunganUpah,
                'management_fee_id' => $manfee,
                'is_aktif' => 0,
                'persentase' => $presentase,
                'step' => $newStep,
                'thr' => $request->thr,
                'kompensasi' => $request->kompensasi,
                'lembur' => $request->lembur,
                'ppn_pph_dipotong' => $request->ppn_pph_dipotong,
                'tunjangan_holiday' => $request->tunjangan_holiday,
                'nominal_lembur' => $request->nominal_lembur,
                'nominal_tunjangan_holiday' => $request->nominal_tunjangan_holiday,
                'jenis_bayar_tunjangan_holiday' => $request->jenis_bayar_tunjangan_holiday,
                'jenis_bayar_lembur' => $request->jenis_bayar_lembur,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //update semua nominal menjadi upah
            DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->update([
                'nominal_upah' => $nominalUpah,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'5']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit5 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();
            $quotationDetail = DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            $jenisPerusahaanId = $request['jenis-perusahaan'];
            $resiko = $request['resiko'];
            $programBpjs = $request['program-bpjs'];
            $isAktif = 1;

            foreach ($quotationDetail as $key => $value) {
                $penjamin = $request->penjamin[$value->id] ?? null;
                $jkk = $request->jkk[$value->id] ?? null;
                $jkm = $request->jkm[$value->id] ?? null;
                $jht = $request->jht[$value->id] ?? null;
                $jp = $request->jp[$value->id] ?? null;
                $nominalTakaful = $request->nominal_takaful[$value->id] ?? null;

                DB::table('sl_quotation_detail')->where('id',$value->id)->update([
                    'penjamin_kesehatan' => $penjamin,
                    'is_bpjs_jkk' => $jkk=="on" ? 1 : 0,
                    'is_bpjs_jkm' => $jkm=="on" ? 1 : 0,
                    'is_bpjs_jht' => $jht=="on" ? 1 : 0,
                    'is_bpjs_jp' => $jp=="on" ? 1 : 0,
                    'nominal_takaful' => $nominalTakaful,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            $jenisPerusahaan = null;
            if($jenisPerusahaanId != null){
                $jenisPerusahaanList = DB::table('m_jenis_perusahaan')->where('id',$jenisPerusahaanId)->first();
                if($jenisPerusahaanList != null){
                    $jenisPerusahaan = $jenisPerusahaanList->nama;
                }
            }

            $isAktif = $quotation->is_aktif;
            // if($isAktif==2){
            //     if($programBpjs != "4 BPJS"){
            //         $isAktif = 0;
            //     }
            // }

            if($isAktif == 2){
                $isAktif = 1;
            };

            $newStep = 6;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'jenis_perusahaan_id' => $jenisPerusahaanId,
                'jenis_perusahaan' => $jenisPerusahaan,
                'resiko' => $resiko,
                'is_aktif' => $isAktif,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'6']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit6 (Request $request){
        DB::beginTransaction();

        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            if($request->aplikasi_pendukung !=null){
                $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->get();
                $aplikasiPendukung = $request->aplikasi_pendukung;
                $arrAplikasiId = [];
                foreach ($aplikasiPendukung as $keyd => $valued) {
                    $appdukung = DB::table('m_aplikasi_pendukung')->where('id',$valued)->first();

                    $dataAplikasi = DB::table('sl_quotation_aplikasi')->where('aplikasi_pendukung_id',$valued)->where('quotation_id',$request->id)->whereNull('deleted_at')->first();
                    $quotationDetail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where('quotation_id',$request->id)->get();

                    if($dataAplikasi==null){
                        $appId = DB::table('sl_quotation_aplikasi')->insertGetId([
                            'quotation_id' => $request->id,
                            'aplikasi_pendukung_id' => $valued,
                            'aplikasi_pendukung' => $appdukung->nama,
                            'harga' => $appdukung->harga,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);

                        array_push($arrAplikasiId,$appId);
                    }else{
                        DB::table('sl_quotation_aplikasi')->where('id',$dataAplikasi->id)->update([
                            'quotation_id' => $request->id,
                            'aplikasi_pendukung_id' => $valued,
                            'aplikasi_pendukung' => $appdukung->nama,
                            'harga' => $appdukung->harga,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                        ]);

                        array_push($arrAplikasiId,$dataAplikasi->id);
                    }
                }

                DB::table('sl_quotation_aplikasi')->where('quotation_id',$request->id)->whereNotIn('aplikasi_pendukung_id', $aplikasiPendukung)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                DB::table('sl_quotation_devices')->where('quotation_id',$request->id)->whereNotNull('quotation_aplikasi_id')->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);

                // insert device lagi saja
                $jumlahHc=0;
                foreach ($quotationDetail as $key => $detail) {
                    $jumlahHc+= $detail->jumlah_hc;
                }

                foreach ($arrAplikasiId as $key => $appId) {
                    $quotAplikasi = DB::table('sl_quotation_aplikasi')->where('id',$appId)->first();
                    $appdukung = DB::table('m_aplikasi_pendukung')->where('id',$quotAplikasi->aplikasi_pendukung_id)->first();

                    DB::table('sl_quotation_devices')->insert([
                        'quotation_id' => $request->id,
                        'quotation_aplikasi_id' => $appId,
                        'barang_id' => $appdukung->barang_id,
                        'jumlah' => $jumlahHc,
                        'harga' => $appdukung->harga,
                        'nama' => $appdukung->nama,
                        'jenis_barang' => 'Aplikasi Pendukung',
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }

            }

            $newStep = 7;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::commit();

            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'7']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit7 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();
            $detail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();

            $listKaporlap = DB::table('m_barang')
                                ->whereNull('deleted_at')
                                ->orderBy("nama","asc")
                                ->get();
            
            //hapus dulu data existing 
            DB::table('sl_quotation_kaporlap')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            foreach ($listKaporlap as $key => $value) {
                foreach ($detail as $kd => $vd) {
                    //cek apakah 0 jika 0 skip
                    if($request->{'jumlah'.'_'.$value->id.'_'.$vd->id} == "0" ||$request->{'jumlah'.'_'.$value->id.'_'.$vd->id} == null){
                        continue;   
                    }else{
                        //cari harga
                        $barang = DB::table('m_barang')->where('id',$value->id)->first();
                        DB::table('sl_quotation_kaporlap')->insert([
                            'quotation_detail_id' => $vd->id,
                            'quotation_id' => $quotation->id,
                            'barang_id' => $barang->id,
                            'jumlah' => $request->{'jumlah'.'_'.$value->id.'_'.$vd->id},
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            $newStep = 8;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'8']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit8 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $quotation = DB::table('sl_quotation')->where('id',$request->id)->whereNull('deleted_at')->first();

            $listDevices = DB::table('m_barang')
                                ->whereNull('deleted_at')
                                ->orderBy("nama","asc")
                                ->get();
            
            //hapus dulu data existing 
            DB::table('sl_quotation_devices')->whereNotIn('barang_id',[192,194,195,196])->whereNull('deleted_at')->where('quotation_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            foreach ($listDevices as $key => $value) {
                //cek apakah 0 jika 0 skip
                if($request->{'jumlah'.'_'.$value->id} == "0" ||$request->{'jumlah'.'_'.$value->id} == null){
                    continue;   
                }else{
                    //cari harga
                    $barang = DB::table('m_barang')->where('id',$value->id)->first();
                    DB::table('sl_quotation_devices')->insert([
                        'quotation_id' => $quotation->id,
                        'barang_id' => $barang->id,
                        'jumlah' => $request->{'jumlah'.'_'.$value->id},
                        'harga' => $barang->harga,
                        'nama' => $barang->nama,
                        'jenis_barang' => $barang->jenis_barang,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
            }

            $newStep = 9;
            $dataStep = 9;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();

            if($dataQuotation->step>$newStep){
                $dataStep = $dataQuotation->step;
            }
            if($quotation->kebutuhan_id==2||$quotation->kebutuhan_id==1||$quotation->kebutuhan_id==4){
                $newStep=10;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $dataStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
                       
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>$newStep]);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit9 (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $newStep = 10;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
           
            // $data = DB::table('sl_quotation_kebutuhan')->whereNull('deleted_at')->where('quotation_id',$request->id)->first();

            // $this->perhitunganHPP($data->id);
            
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'10']);
            }else{
                return redirect()->route('quotation.view',$dataQuotation->id);
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

            $newStep = 11;
            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            if($dataQuotation->step>$newStep){
                $newStep = $dataQuotation->step;
            }
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            
            if($request->ada_training=="Tidak Ada"){
                $request->training ="0";
            }

            $persenBungaBank = $dataQuotation->persen_bunga_bank; 
            if($dataQuotation->persen_bunga_bank != 0 && $dataQuotation->persen_bunga_bank != null){
                $persenBungaBank = 1.3;
            };

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'step' => $newStep,
                'kunjungan_operasional' => $request->jumlah_kunjungan_operasional." ".$request->bulan_tahun_kunjungan_operasional ,
                'kunjungan_tim_crm' => $request->jumlah_kunjungan_tim_crm." ".$request->bulan_tahun_kunjungan_tim_crm ,
                'keterangan_kunjungan_operasional' => $request->keterangan_kunjungan_operasional ,
                'keterangan_kunjungan_tim_crm' => $request->keterangan_kunjungan_tim_crm ,
                'training' => $request->training ,
                'persen_bunga_bank' => $persenBungaBank,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
                       
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'11']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

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
            if($request->edit==1){
                $newStep = $dataQuotation->step;
            }
            

            // tambah approval dir sales
            // $dataQuotationD =  DB::table('sl_quotation_d')->where('quotation_id',$request->id)->get();

            $isAktif = $dataQuotation->is_aktif;
            // foreach ($dataQuotationD as $key => $dataD) {
            //     if($dataD->biaya_monitoring > 0 ){
            //         $isAktif=0;
            //     }
            // }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'is_aktif' => $isAktif,
                'step' => $newStep,
                'penagihan' => $request->penagihan,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            

            //tambah perjanjian
            //hapus dulu perjanjian yg lama atau kalau ada
            DB::table('sl_quotation_kerjasama')->where('quotation_id',$request->id)->whereNull('deleted_at')->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            $kebutuhanPerjanjian = "<b>".$dataQuotation->kebutuhan."</b>";
            //buat perjanjian
            // $top = "";
            // if($dataQuotation->top=="Kurang Dari 7 Hari"){
            //     $top = "Maksimal 7 Hari Kerja"; 
            // }else{
            //     $top = "Maksimal ".$dataQuotation->jumlah_hari_invoice." hari ".$dataQuotation->tipe_hari_invoice;
            // }

            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$dataQuotation->salary_rule_id)->first();

            $tableSalary = '<table class="table table-bordered" style="width:100%">
                              <thead>
                                <tr>
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-center"><b>Schedule Plan</b></th>
                                  <th class="text-center"><b>Periode</b></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center">1</td>
                                  <td>Cut Off</td>
                                  <td>'.$salaryRuleQ->cutoff.'</td>
                                </tr>
                                <tr>
                                  <td class="text-center">2</td>
                                  <td>Pengiriman <i>Invoice</i></td>
                                  <td>'.$salaryRuleQ->pengiriman_invoice.'</td>
                                </tr>
                                <tr>
                                  <td class="text-center">6</td>
                                  <td>Rilis <i>Payroll</i> / Gaji</td>
                                  <td>'.$salaryRuleQ->rilis_payroll.'</td>
                                </tr>
                              </tbody>
                            </table>';
            $kunjunganOperasional = "";
            if ($dataQuotation->kunjungan_operasional !=null) {
                $kunjunganOperasional = explode(" ",$dataQuotation->kunjungan_operasional)[0]." kali dalam 1 ".explode(" ",$dataQuotation->kunjungan_operasional)[1];
            }
            $appPendukung = DB::table('sl_quotation_aplikasi')->whereNull('deleted_at')->where('quotation_id',$request->id)->get();
            $sAppPendukung = "<b>";
            foreach ($appPendukung as $kduk => $dukung) {
                if($kduk != 0){
                    $sAppPendukung .= ", ";
                }
                $sAppPendukung .= $dukung->aplikasi_pendukung;
            }
            $sAppPendukung .= "</b>";

            $arrPerjanjian = [
                "Penawaran harga ini berlaku 30 hari sejak tanggal diterbitkan.",
                "Akan dilakukan <i>survey</i> area untuk kebutuhan ".$kebutuhanPerjanjian." sebagai tahapan <i>assesment</i> area untuk memastikan efektifitas pekerjaan.",
                "Komponen dan nilai dalam penawaran harga ini berdasarkan kesepakatan para pihak dalam pengajuan harga awal, apabila ada perubahan, pengurangan maupun penambahan pada komponen dan nilai pada penawaran, maka <b>para pihak</b> sepakat akan melanjutkan ke tahap negosiasi selanjutnya.",
                "Skema cut-off, pengiriman <i>invoice</i>, pembayaran <i>invoice</i> dan penggajian adalah <b>TOP/talangan</b> maksimal 30 hari kalender dengan skema sebagai berikut: <br>".$tableSalary."<i><br>*Rilis gaji adalah talangan.<br>*Maksimal pembayaran invoice 30 hari kalender setelah invoice</i>",
                "Kunjungan tim operasional ".$kunjunganOperasional.", untuk monitoring dan supervisi dengan karyawan dan wajib bertemu dengan pic <b>Pihak Pertama</b> untuk koordinasi.",
                "Tim operasional bersifat <i>on call</i> apabila terjadi <i>case</i> atau insiden yang terjadi yang mengharuskan untuk datang ke lokasi kerja Pihak Pertama.",
                "Pemenuhan kandidat dilakukan dengan 2 tahap <i>screening</i> :<br>
                    a. Tahap ke -1 : dilakukan oleh tim rekrutmen <b>Pihak Kedua</b> untuk memastikan bahwa kandidat sudah sesuai dengan kualifikasi <b>dari Pihak Pertama</b>.<br>
                    b. Tahap ke -2 : dilakukan oleh user <b>Pihak Pertama</b>, dan dijadwalkan setelah adanya <i>report</i> hasil <i>screening</i> dari <b>Pihak Kedua</b>.",
                "<i>Support</i> aplikasi digital :".$sAppPendukung."."
            ];

            foreach ($arrPerjanjian as $key => $value) {
                DB::table('sl_quotation_kerjasama')->insert([
                    'quotation_id' => $request->id,
                    'perjanjian' => $value,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            if($request->edit==0){
                return redirect()->route('quotation.step',['id'=>$request->id,'step'=>'12']);
            }else{
                return redirect()->route('quotation.view',$request->id);
            }

        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEdit12 (Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$request->id)->first();
            $quotationSite = DB::table('sl_quotation_site')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();
            $isAktif = 1;
            $statusQuotation = 3;

            //jika top lebih dari 7 hari
            if($quotation->top=="Lebih Dari 7 Hari"){
                $isAktif = 0;
                $statusQuotation = 2;
            }
            // jika nominal kurang dari umk
            foreach ($quotationSite as $key => $site) {
                if ($site->nominal_upah<$site->umk) {
                    $isAktif = 0;
                    $statusQuotation = 2;
                }
            }

            // jika persentasi mf kurang dari 7
            if ($quotation->persentase < 7) {
                $isAktif = 0;
                $statusQuotation = 2;
            }

            DB::table('sl_quotation')->where('id',$request->id)->update([
                'is_aktif' => $isAktif,
                'step' => 100,
                'status_quotation_id' =>$statusQuotation,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //Masukkan Requirement
            $detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
            foreach ($detail as $key => $value) {
                //cari apakah ada requirement
                $existData= DB::table('sl_quotation_detail_requirement')->where('quotation_detail_id',$value->id)->whereNull('deleted_at')->get();

                if(count($existData)==0){
                    $requirement = DB::table('m_kebutuhan_detail_requirement')->whereNull('deleted_at')->where('position_id',$value->position_id)->get();
                    foreach ($requirement as $kreq => $req) {
                        DB::table('sl_quotation_detail_requirement')->insert([
                            'quotation_id' => $quotation->quotation_id,
                            'quotation_detail_id' => $value->id,
                            'requirement' => $req->requirement,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('quotation.view',$quotation->id);
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
                    ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
                    ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
                    ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                    ->select('sl_quotation.jumlah_site','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation',
                    DB::raw('(SELECT GROUP_CONCAT(nama_site SEPARATOR "<br /> ") 
                    FROM sl_quotation_site 
                    WHERE sl_quotation_site.quotation_id = sl_quotation.id) as nama_site'))
                    ->whereNull('sl_quotation.deleted_at')->whereNull('sl_quotation.deleted_at');

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
            if(!empty($request->company)){
                $data = $data->where('sl_quotation.company_id',$request->company);
            }
            if(!empty($request->kebutuhan_id)){
                $data = $data->where('sl_quotation.kebutuhan_id',$request->kebutuhan);
            }
            
            if(!empty($request->status)){
                $data = $data->where('sl_quotation.status_quotation_id',$request->status);
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
                if($data->step != 100){
                    if($data->jumlah_site=="Multi Site"){
                        return '<div class="justify-content-center d-flex">
                        <a href="'.route('quotation.step',['id'=>$data->quotation_id,'step'=>$data->step]).'" class="btn btn-primary waves-effect btn-xs">Lanjutkan Pengisian</a> &nbsp;
            <a href="javascript:void(0)" class="btn btn-warning waves-effect btn-xs copy-quotation" data-id="'.$data->id.'" data-nomor="'.$data->nomor.'">Copy Dari Existing</a>
                        </div>';
                    }else{
                        return '<div class="justify-content-center d-flex"><a href="'.route('quotation.step',['id'=>$data->quotation_id,'step'=>$data->step]).'" class="btn btn-primary waves-effect btn-xs">Lanjutkan Pengisian</a></div>';
                    }
                }else{
                    return '<div class="justify-content-center d-flex">
                    <a href="'.route('quotation.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
        </div>';
                }
            })
            ->editColumn('nomor', function ($data) {
                $ref = "";

                if($data->step != 100){
                    $ref = "#";
                }else{
                    $ref = route('quotation.view',$data->id);
                }
                return '<a href="'.$ref.'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';

            })
            ->editColumn('nama_perusahaan', function ($data) {
                return '<a href="'.route('leads.view',$data->leads_id).'" style="font-weight:bold;color:#000056">'.$data->nama_perusahaan.'</a>';
            })
            ->rawColumns(['aksi','nomor','nama_perusahaan','nama_site'])
            ->make(true);
    }

    
    public function addDetailHC(Request $request){
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            $position = DB::connection('mysqlhris')->table('m_position')->where('id',$request->position_id)->first();
            $quotation = DB::table('sl_quotation')->where('id',$request->quotation_id)->first();
            $quotationSite = DB::table('sl_quotation_site')->where('id',$request->site_id)->first();

            // cek apakah data sudah ada
            $checkExist = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->where('position_id',$request->position_id)->where('quotation_site_id',$request->site_id)->whereNull('deleted_at')->first();
            if($checkExist !=null){
                DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->where('position_id',$request->position_id)->where('quotation_site_id',$request->site_id)->whereNull('deleted_at')->update([
                    'jumlah_hc' => $checkExist->jumlah_hc+$request->jumlah_hc,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else{
                $detailIdBaru = DB::table('sl_quotation_detail')->insertGetId([
                    'quotation_id' => $quotation->id,
                    'quotation_site_id' => $request->site_id,
                    'nama_site' => $quotationSite->nama_site,
                    'position_id' => $request->position_id,
                    'kebutuhan' => $quotation->kebutuhan,
                    'jabatan_kebutuhan' => $position->name,
                    'jumlah_hc' => $request->jumlah_hc,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
                
                $hpp = DB::table('sl_quotation_detail_hpp')->insert([
                    'quotation_id' => $quotation->id,
                    'quotation_detail_id' => $detailIdBaru,
                    'leads_id' => $quotation->leads_id,
                    'position_id' => $request->position_id,
                    'jumlah_hc' => $request->jumlah_hc,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                $coss = DB::table('sl_quotation_detail_coss')->insert([
                    'quotation_id' => $quotation->id,
                    'quotation_detail_id' => $detailIdBaru,
                    'leads_id' => $quotation->leads_id,
                    'position_id' => $request->position_id,
                    'jumlah_hc' => $request->jumlah_hc,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                //masukkan tunjangan
                $listTunjangan = DB::table('m_tunjangan_posisi')->whereNull('deleted_at')->where('position_id',$request->position_id)->get();
                if (count($listTunjangan)>0) {
                    foreach ($listTunjangan as $key => $tunjangan) {
                        DB::table('sl_quotation_detail_tunjangan')->insert([
                            'quotation_id' => $quotation->id,
                            'quotation_detail_id' => $detailIdBaru,
                            'tunjangan_id' =>$tunjangan->id,
                            'nama_tunjangan' => $tunjangan->nama,
                            'nominal' => $tunjangan->nominal,
                            'created_at' => $current_date_time,
                            'created_by' => Auth::user()->full_name
                        ]);
                    }
                }
            }
            DB::commit(); 
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addQuotationTraining(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation_training')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
            
            $arrTrainingId = explode(",",$request->training_id);
            $total = 0;
            foreach ($arrTrainingId as $key => $value) {
                $dTraining = DB::table('m_training')->where('id',$value)->first();
                $total += $dTraining->harga;
                DB::table('sl_quotation_training')->insert([
                    'training_id' => $value,
                    'quotation_id' =>$request->quotation_id,
                    'nama' => $dTraining->nama,
                    'harga' => $dTraining->harga,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            // cari training dari ohc kalau ada update kalau tidak ada update
            $detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->get();
            $checkExist = DB::table('sl_quotation_ohc')->whereNull('deleted_at')->where('barang_id',999)->where('quotation_id',$request->quotation_id)->first();
                if($checkExist == null){
                    DB::table('sl_quotation_ohc')->insert([
                        'quotation_id' => $request->quotation_id,
                        'barang_id' => 999,
                        'jumlah' => 1,
                        'nama' => 'Training',
                        'jenis_barang' => 'Direct Site Operational',
                        'harga' => $total,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('sl_quotation_ohc')->whereNull('deleted_at')->where('barang_id',999)->where('quotation_id',$request->quotation_id)->update([
                        'quotation_id' => $request->quotation_id,
                        'barang_id' => 999,
                        'jumlah' => 1,
                        'nama' => 'Training',
                        'jenis_barang' => 'Direct Site Operational',
                        'harga' => $total,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }

            $listTraning = "";
            $dataTraining = DB::table('sl_quotation_training')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->get();
            foreach ($dataTraining as $key => $value) {
                if($key != 0){
                    $listTraning .= ", ";
                };
                
                $listTraning .= $value->nama;
            }

            return $listTraning;
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addBarang(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $barang = $request->barang;
            $jenis = $request->jenis;
            $jenisBarang = DB::table('m_jenis_barang')->where('id',$jenis)->first();

            DB::table('m_barang')->insert([
                'nama' => $barang,
                'jenis_barang_id' => $jenisBarang->id,
                'jenis_barang' => $jenisBarang->nama,
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

    public function addTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $namaTunjangan = $request->namaTunjangan;
            $nominalTunjangan = $request->nominalTunjangan;
            $quotationDetailId = $request->quotationDetailId;

            $nominalTunjangan = str_replace(".","",$nominalTunjangan);

            $quotationDetail = DB::table('sl_quotation_detail')->where('quotation_id',$request->id)->whereNull('deleted_at')->get();

            $checkExist = DB::table('sl_quotation_detail_tunjangan')->where("quotation_detail_id",$quotationDetailId)->whereNull('deleted_at')->where('nama_tunjangan',$namaTunjangan)->first();

            if($checkExist==null){
                foreach ($quotationDetail as $key => $value) {
                    $nominal = 0;
                    if($value->id == $quotationDetailId){
                        $nominal = $nominalTunjangan;
                    }
                    DB::table('sl_quotation_detail_tunjangan')->insert([
                        'quotation_id' => $value->quotation_id,
                        'quotation_detail_id' => $value->id,
                        'tunjangan_id' => null,
                        'nama_tunjangan' => $namaTunjangan,
                        'nominal' => $nominal,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
            }else{
                DB::table('sl_quotation_detail_tunjangan')->whereNull('deleted_at')->where("id",$checkExist->id)->update([
                    'nominal' => $nominalTunjangan+$checkExist->nominal,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function addDetailPic(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $nama = $request->nama;
            $jabatan = $request->jabatan;
            $no_telp = $request->no_telp;
            $email = $request->email;
            $quotation_id = $request->quotation_id;

            $quotation = DB::table('sl_quotation')->where('id',$quotation_id)->first();
            $jabatan = DB::table('m_jabatan_pic')->where('id',$jabatan)->first();

            DB::table('sl_quotation_pic')->insert([
                'quotation_id' => $quotation->id,
                'leads_id' => $quotation->leads_id,
                'nama' => $nama,
                'jabatan_id' => $jabatan->id,
                'jabatan' => $jabatan->nama,
                'no_telp' => $no_telp,
                'email' => $email,
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
            DB::table('sl_quotation_detail')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_detail_tunjangan')->where('quotation_detail_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_detail_hpp')->where('quotation_detail_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_detail_coss')->where('quotation_detail_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function deleteTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation_detail_tunjangan')->where('quotation_id',$request->quotation_id)->where('nama_tunjangan',$request->nama)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function editTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation_detail_tunjangan')->where('quotation_detail_id',$request->id)->where('nama_tunjangan',$request->nama)->update([
                'nominal' => $request->nominal,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function editNominal(Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $hpp = DB::table('sl_quotation_detail_hpp')->whereNull('deleted_at')->where('quotation_detail_id',$request->id)->first();
            $coss = DB::table('sl_quotation_detail_coss')->whereNull('deleted_at')->where('quotation_detail_id',$request->id)->first();

            $hppByQuotation = DB::table('sl_quotation_detail_hpp')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->get();
            $cossByQuotation = DB::table('sl_quotation_detail_coss')->whereNull('deleted_at')->where('quotation_id',$request->quotation_id)->get();

            $isUpdateAll = false;
            
            if ($request->tabel=="hpp") {
                switch ($request->tipe) {
                    case 'Gaji Pokok':
                        $hpp->gaji_pokok = $request->nominal;
                        $coss->gaji_pokok = $request->nominal;
                        break;
                    case 'THR':
                        $hpp->tunjangan_hari_raya = $request->nominal;
                        $coss->tunjangan_hari_raya = $request->nominal;
                        break;
                    case 'Kompensasi':
                        $hpp->kompensasi = $request->nominal;
                        $coss->kompensasi = $request->nominal;
                        break;
                    case 'Tunjangan Holiday':
                        $hpp->tunjangan_hari_libur_nasional = $request->nominal;
                        $coss->tunjangan_hari_libur_nasional = $request->nominal;
                        break;
                    case 'Lembur':
                        $hpp->lembur = $request->nominal;
                        $coss->lembur = $request->nominal;
                        break;
                    case 'BPJS JKK':
                        $hpp->bpjs_jkk = $request->nominal;
                        $coss->bpjs_jkk = $request->nominal;
                        break;
                    case 'BPJS JKM':
                        $hpp->bpjs_jkm = $request->nominal;
                        $coss->bpjs_jkm = $request->nominal;
                        break;
                    case 'BPJS JHT':
                        $hpp->bpjs_jht = $request->nominal;
                        $coss->bpjs_jht = $request->nominal;
                        break;
                    case 'BPJS JP':
                        $hpp->bpjs_jp = $request->nominal;
                        $coss->bpjs_jp = $request->nominal;
                        break;
                    case 'BPJS KES':
                        $hpp->bpjs_ks = $request->nominal;
                        $coss->bpjs_ks = $request->nominal;
                        break;
                    case 'Takaful':
                        $hpp->takaful = $request->nominal;
                        $coss->takaful = $request->nominal;
                        break;
                    case 'Kaporlap':
                        $hpp->provisi_seragam = $request->nominal;
                        break;
                    case 'Peralatan':
                        foreach ($hppByQuotation as $key => $value) {
                            $value->provisi_peralatan = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    case 'Chemical':
                        foreach ($hppByQuotation as $key => $value) {
                            $value->provisi_chemical = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    case 'OHC':
                        foreach ($hppByQuotation as $key => $value) {
                            $value->provisi_ohc = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    case 'Bunga Bank':
                        foreach ($hppByQuotation as $key => $value) {
                            $value->bunga_bank = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    case 'Insentif':
                        foreach ($hppByQuotation as $key => $value) {
                            $value->insentif = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    default:
                        break;
                }
            }else if($request->tabel=="coss"){
                switch ($request->tipe) {
                    case 'Kaporlap':
                        $coss->provisi_seragam = $request->nominal;
                        break;
                    case 'Peralatan':
                        foreach ($cossByQuotation as $key => $value) {
                            $value->provisi_peralatan = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    case 'Chemical':
                        foreach ($cossByQuotation as $key => $value) {
                            $value->provisi_chemical = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    case 'OHC':
                        foreach ($cossByQuotation as $key => $value) {
                            $value->provisi_ohc = $request->nominal;
                        }
                        $isUpdateAll = true;
                        break;
                    default:
                        break;
                }
            }

            
    
            if ($isUpdateAll) {
                foreach ($hppByQuotation as $key => $value) {
                    $value->updated_at = $current_date_time;
                    $value->updated_by = Auth::user()->full_name;
                    DB::table('sl_quotation_detail_hpp')->where('id', $value->id)->update((array) $value);
                }

                foreach ($cossByQuotation as $key => $value) {
                    DB::table('sl_quotation_detail_coss')->where('id', $value->id)->update((array) $value);
                }
            }else{
                $hpp->updated_at = $current_date_time;
                $hpp->updated_by = Auth::user()->full_name;
                $coss->updated_at = $current_date_time;
                $coss->updated_by = Auth::user()->full_name;
                DB::table('sl_quotation_detail_hpp')->where('quotation_detail_id', $request->id)->update((array) $hpp);
                DB::table('sl_quotation_detail_coss')->where('quotation_detail_id', $request->id)->update((array) $coss);         
            }

               

            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function editPersenInsentif(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation')->where('id',$request->quotation_id)->update([
                'persen_insentif' => $request->persen,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function editPersenBungaBank(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('sl_quotation')->where('id',$request->quotation_id)->update([
                'persen_bunga_bank' => $request->persen,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function listDetailHC (Request $request){
        $data = DB::table('sl_quotation_detail')
        ->where('sl_quotation_detail.quotation_id',$request->quotation_id)
        ->whereNull('sl_quotation_detail.deleted_at')->get();
        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'" data-kebutuhan="'.$data->quotation_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }
    
    public function listDetailPic (Request $request){
        $quotation = DB::table('sl_quotation')->where('id',$request->quotation_id)->first();
        
        $data = DB::table('sl_quotation_pic')
        ->where('quotation_id',$request->quotation_id)
        ->whereNull('deleted_at')
        ->select('is_kuasa','id','nama','jabatan','no_telp','email')
        ->get();

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->addColumn('kuasa', function ($data) {
            if($data->id==0){
                return "";
            }

            $checked = "";

            if ($data->is_kuasa==1) {
                $checked = "checked";
            }
            return '<input name="is_kuasa[]" class="form-check-input set-is-kuasa" type="radio" value="" data-id="'.$data->id.'" '.$checked.' >';
        })
        ->rawColumns(['aksi','kuasa'])
        ->make(true);
    }

    public function changeKuasaPic(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_pic')->where('quotation_id',$request->quotation_id)->update([
                'is_kuasa' => 0,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('sl_quotation_pic')->where('id',$request->id)->update([
                'is_kuasa' => 1,
                'updated_by' => Auth::user()->full_name
            ]);
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailPic(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_pic')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function changeKota (Request $request){
        $data = DB::connection('mysqlhris')->table('m_city')->where('province_id',$request->province_id)->get();
        foreach ($data as $key => $value) {
            $dataUmk = DB::table("m_umk")->whereNull('deleted_at')->where('is_aktif',1)->where('city_id',$value->id)->first();
            $value->umk = "UMK : Rp. 0";
            if($dataUmk !=null){
                $value->umk = "UMK : Rp. ".number_format($dataUmk->umk,2,",",".");
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
            DB::table('sl_quotation')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation_detail')->where('quotation_detail',$request->id)->update([
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
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            $master = DB::table('sl_quotation')->where('id',$request->id)->first();
            
            if(in_array(Auth::user()->role_id,[96])){
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'ot1' => Auth::user()->full_name,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                //ambil quotation
                if($master->top=="Kurang Dari 7 Hari" || $master->top=="Non TOP"){
                    DB::table('sl_quotation')->where('id',$request->id)->update([
                        'is_aktif' => 1,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);

                    DB::table('sl_quotation')->where('id',$request->id)->update([
                        'status_quotation_id' => 3,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }
            }else if(in_array(Auth::user()->role_id,[97])){
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'ot2' => Auth::user()->full_name,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }else if(in_array(Auth::user()->role_id,[99])){
                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'ot3' => Auth::user()->full_name,
                    'is_aktif' => 1,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);

                DB::table('sl_quotation')->where('id',$request->id)->update([
                    'status_quotation_id' => 3,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($master->leads_id);
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $master->leads_id,
                'quotation_id' => $request->id,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$master->nomor.' di approve oleh '.Auth::user()->full_name,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::commit();
        } catch (\Exception $e) {
            dd($e);
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
        // try {
        //     $current_date_time = Carbon::now()->toDateTimeString();
        //     $data = DB::table('sl_quotation_detail')->where('id',$request->id)->get();

        //     foreach ($data as $key => $value) {
        //         if($request['jumlah'.$value->id] !=null && $request['jumlah'.$value->id] !=""){
        //             $dataExist = DB::table("sl_quotation_kaporlap")
        //             ->whereNull('deleted_at')
        //             ->where('quotation_detail',$value->quotation_detail)
        //             ->where('quotation_kebutuhan_detail_id',$value->id)
        //             ->where('barang_id',$request->barang)
        //             ->first();

        //             $barang = DB::table('m_barang')->where('id',$request->barang)->first();
        //             if($dataExist!=null){
        //                 DB::table("sl_quotation_kaporlap")
        //                     ->whereNull('deleted_at')
        //                     ->where('quotation_detail',$value->quotation_detail)
        //                     ->where('quotation_kebutuhan_detail_id',$value->id)
        //                     ->where('barang_id',$request->barang)->update([
        //                             'jumlah' => $dataExist->jumlah+(int)$request['jumlah'.$value->id],
        //                             'harga' => $barang->harga,
        //                             'nama' => $barang->nama,
        //                             'jenis_barang' => $barang->jenis_barang,
        //                             'updated_at' => $current_date_time,
        //                             'updated_by' => Auth::user()->full_name
        //                     ]);
        //             }else{
        //                 DB::table('sl_quotation_kaporlap')->insert([
        //                     'quotation_kebutuhan_detail_id' => $value->id,
        //                     'quotation_detail' => $value->quotation_detail,
        //                     'quotation_id' => $value->quotation_id,
        //                     'barang_id' => $request->barang,
        //                     'jumlah' => $request['jumlah'.$value->id],
        //                     'harga' => $barang->harga,
        //                     'nama' => $barang->nama,
        //                     'jenis_barang' => $barang->jenis_barang,
        //                     'created_at' => $current_date_time,
        //                     'created_by' => Auth::user()->full_name
        //                 ]);
        //             }
        //         }
        //     }

        //     return "Data Berhasil Ditambahkan";
        // } catch (\Exception $e) {
        //     SystemController::saveError($e,Auth::user(),$request);
        //     return "Data Gagal Ditambahkan";

        //     abort(500);
        // }
    }

    public function listKaporlap (Request $request){
//         $raw = ['aksi'];
//         $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_kaporlap.quotation_detail,sl_quotation_kaporlap.barang_id,sl_quotation_kaporlap.jenis_barang,sl_quotation_kaporlap.nama,sl_quotation_kaporlap.harga 
// from sl_quotation_kaporlap 
// INNER JOIN m_barang ON sl_quotation_kaporlap.barang_id = m_barang.id
// WHERE sl_quotation_kaporlap.deleted_at is null 
// and quotation_detail = $request->quotation_detail
// ORDER BY m_barang.jenis_barang_id asc,sl_quotation_kaporlap.nama ASC;");

// $total =DB::select("select sum(harga*jumlah) as total from sl_quotation_kaporlap WHERE deleted_at is null and quotation_detail = $request->quotation_detail")[0]->total;
// $objectTotal = (object) ['jenis_barang_id' => 100,
// 'quotation_detail' => 0,
// 'barang_id' => 0,
// 'jenis_barang' => 'TOTAL',
// 'nama' => '',
// 'harga' => $total];

//         array_push($data,$objectTotal);
//         $dt = DataTables::of($data)
//         ->addColumn('aksi', function ($data){
//             if($data->barang_id==0){
//                 return null;
//             }
//             return '<div class="justify-content-center d-flex">
//                         <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-kebutuhan="'.$data->quotation_detail.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
//                     </div>';
//         });
//         $dt = $dt->editColumn('harga', function ($data){
//             return "Rp ".number_format($data->harga,0,",",".");
//         });

//         $dataDetail = DB::table('sl_quotation_detail')->where('quotation_detail',$request->quotation_detail)->whereNull('deleted_at')->get();

//         foreach ($dataDetail as $key => $value) {
//             $dt = $dt->addColumn("data-$value->id", function ($data) use ($value) {
//                 $dataD = DB::select("select jumlah from sl_quotation_kaporlap WHERE deleted_at is null and quotation_detail = $data->quotation_detail and quotation_kebutuhan_detail_id = $value->id and barang_id = $data->barang_id");
//                 if(count($dataD)>0){
//                     return $dataD[0]->jumlah;
//                 }else{
//                     return "";
//                 };
//             });
//         };

//         $dt = $dt->rawColumns($raw);
//         $dt = $dt->make(true);

//         return $dt;
    }

    public function deleteDetailKaporlap(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_kaporlap')->where('quotation_detail',$request->quotation_detail)->where('barang_id',$request->barang_id)->update([
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
            $data = DB::table('sl_quotation_detail')->where('quotation_id',$request->quotation_id)->get();

            if($request->jumlah !=null && $request->jumlah !=""){
                $dataExist = DB::table("sl_quotation_ohc")
                ->whereNull('deleted_at')
                ->where('quotation_id',$request->quotation_id)
                ->where('barang_id',$request->barang)
                ->first();

                $barang = DB::table('m_barang')->where('id',$request->barang)->first();
                $harga = str_replace(".","",$request->harga);
                if($dataExist!=null){
                    DB::table("sl_quotation_ohc")
                        ->whereNull('deleted_at')
                        ->where('quotation_id',$request->quotation_id)
                        // ->where('quotation_detail_id',$value->id)
                        ->where('barang_id',$request->barang)->update([
                                'jumlah' => $dataExist->jumlah+(int)$request->jumlah,
                                'harga' => $harga,
                                'nama' => $barang->nama,
                                'jenis_barang' => $barang->jenis_barang,
                                'updated_at' => $current_date_time,
                                'updated_by' => Auth::user()->full_name
                        ]);
                }else{
                    DB::table('sl_quotation_ohc')->insert([
                        'quotation_id' => $request->quotation_id,
                        // 'quotation_id' => $value->quotation_id,
                        'barang_id' => $request->barang,
                        'jumlah' => $request->jumlah,
                        'harga' => $harga,
                        'nama' => $barang->nama,
                        'jenis_barang' => $barang->jenis_barang,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
            }

            // foreach ($data as $key => $value) {
                
            // }

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return "Data Gagal Ditambahkan";

            abort(500);
        }
    }

    public function listOhc (Request $request){
        $raw = ['aksi'];
        $data = DB::select("SELECT m_barang.jenis_barang_id,sl_quotation_ohc.jumlah,sl_quotation_ohc.quotation_id,sl_quotation_ohc.barang_id,sl_quotation_ohc.jenis_barang,sl_quotation_ohc.nama,sl_quotation_ohc.harga 
from sl_quotation_ohc 
INNER JOIN m_barang ON sl_quotation_ohc.barang_id = m_barang.id
WHERE sl_quotation_ohc.deleted_at is null 
and quotation_id = $request->quotation_id
ORDER BY m_barang.jenis_barang_id asc,sl_quotation_ohc.nama ASC;");

$total =DB::select("select sum(harga*jumlah) as total from sl_quotation_ohc WHERE deleted_at is null and quotation_id = $request->quotation_id")[0]->total;
$objectTotal = (object) ['jenis_barang_id' => 100,
'quotation_id' => 0,
'barang_id' => 0,
'jenis_barang' => 'TOTAL',
'nama' => '',
'jumlah' => '',
'harga' => $total];

        array_push($data,$objectTotal);
        $dt = DataTables::of($data)
        ->addColumn('aksi', function ($data){
            if($data->barang_id==0){
                return null;
            }
            if ($data->barang_id==999) {
                return null;
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-quotation="'.$data->quotation_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        });
        $dt = $dt->editColumn('harga', function ($data){
            return "Rp ".number_format($data->harga,0,",",".");
        });

        $dt = $dt->rawColumns($raw);
        $dt = $dt->make(true);

        return $dt;
    }

    public function deleteDetailOhc(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_ohc')->where('quotation_id',$request->quotation_id)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    // END OF AJAX OHC

    // AJAX DEVICES
    public function addDetailDevices (Request $request){
        // try {
        //     $current_date_time = Carbon::now()->toDateTimeString();
        //     $data = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_detail',$request->quotation_detail)->get();

        //     foreach ($data as $key => $value) {
        //         if($request['jumlah'.$value->id] !=null && $request['jumlah'.$value->id] !=""){
        //             $dataExist = DB::table("sl_quotation_devices")
        //             ->whereNull('deleted_at')
        //             ->where('quotation_detail',$value->quotation_detail)
        //             ->where('quotation_kebutuhan_detail_id',$value->id)
        //             ->where('barang_id',$request->barang)
        //             ->first();

        //             $barang = DB::table('m_barang')->where('id',$request->barang)->first();
        //             if($dataExist!=null){
        //                 DB::table("sl_quotation_devices")
        //                     ->whereNull('deleted_at')
        //                     ->where('quotation_detail',$value->quotation_detail)
        //                     ->where('quotation_kebutuhan_detail_id',$value->id)
        //                     ->where('barang_id',$request->barang)->update([
        //                             'jumlah' => $dataExist->jumlah+(int)$request['jumlah'.$value->id],
        //                             'harga' => $barang->harga,
        //                             'nama' => $barang->nama,
        //                             'jenis_barang' => $barang->jenis_barang,
        //                             'updated_at' => $current_date_time,
        //                             'updated_by' => Auth::user()->full_name
        //                     ]);
        //             }else{
        //                 DB::table('sl_quotation_devices')->insert([
        //                     'quotation_kebutuhan_detail_id' => $value->id,
        //                     'quotation_detail' => $value->quotation_detail,
        //                     'quotation_id' => $value->quotation_id,
        //                     'barang_id' => $request->barang,
        //                     'jumlah' => $request['jumlah'.$value->id],
        //                     'harga' => $barang->harga,
        //                     'nama' => $barang->nama,
        //                     'jenis_barang' => $barang->jenis_barang,
        //                     'created_at' => $current_date_time,
        //                     'created_by' => Auth::user()->full_name
        //                 ]);
        //             }
        //         }
        //     }

        //     return "Data Berhasil Ditambahkan";
        // } catch (\Exception $e) {
        //     SystemController::saveError($e,Auth::user(),$request);
        //     return "Data Gagal Ditambahkan";

        //     abort(500);
        // }
    }

    public function listDevices (Request $request){
//         $raw = ['aksi'];
//         $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_devices.quotation_detail,sl_quotation_devices.barang_id,sl_quotation_devices.jenis_barang,sl_quotation_devices.nama,sl_quotation_devices.harga 
// from sl_quotation_devices 
// LEFT JOIN m_barang ON sl_quotation_devices.barang_id = m_barang.id
// WHERE sl_quotation_devices.deleted_at is null 
// and quotation_detail = $request->quotation_detail
// ORDER BY m_barang.jenis_barang_id asc,sl_quotation_devices.nama ASC;");

// $total =DB::select("select sum(harga*jumlah) as total from sl_quotation_devices WHERE deleted_at is null and quotation_detail = $request->quotation_detail")[0]->total;
// $objectTotal = (object) ['jenis_barang_id' => 100,
// 'quotation_detail' => 0,
// 'barang_id' => 0,
// 'jenis_barang' => 'TOTAL',
// 'nama' => '',
// 'harga' => $total];

//         array_push($data,$objectTotal);
//         $dt = DataTables::of($data)
//         ->addColumn('aksi', function ($data){
//             if($data->barang_id==0){
//                 return null;
//             }
//             if(in_array($data->barang_id,[193,194,195,196])){
//                 return null;
//             }
//             return '<div class="justify-content-center d-flex">
//                         <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'" data-kebutuhan="'.$data->quotation_detail.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
//                     </div>';
//         });
//         $dt = $dt->editColumn('harga', function ($data){
//             return "Rp ".number_format($data->harga,0,",",".");
//         });

//         $dataDetail = DB::table('sl_quotation_kebutuhan_detail')->where('quotation_detail',$request->quotation_detail)->whereNull('deleted_at')->get();

//         foreach ($dataDetail as $key => $value) {
//             $dt = $dt->addColumn("data-$value->id", function ($data) use ($value) {
//                 $dataD = DB::select("select jumlah from sl_quotation_devices WHERE deleted_at is null and quotation_detail = $data->quotation_detail and quotation_kebutuhan_detail_id = $value->id and barang_id = $data->barang_id");
//                 if(count($dataD)>0){
//                     return $dataD[0]->jumlah;
//                 }else{
//                     return "";
//                 };
//             });
//         };

//         $dt = $dt->rawColumns($raw);
//         $dt = $dt->make(true);

//         return $dt;
    }

    public function deleteDetailDevices(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_devices')->where('quotation_detail',$request->quotation_detail)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    // END OF AJAX DEVICES

    // AJAX CHEMICAL

    public function addDetailChemical (Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $dataExist = DB::table("sl_quotation_chemical")
                ->whereNull('deleted_at')
                ->where('quotation_id',$request->quotation_id)
                ->where('barang_id',$request->barang)
                ->first();

            $barang = DB::table('m_barang')->where('id',$request->barang)->first();
            if($dataExist!=null){
                DB::table("sl_quotation_chemical")
                    ->whereNull('deleted_at')
                    ->where('quotation_id',$request->quotation_id)
                    ->where('barang_id',$request->barang)->update([
                            'jumlah' => $dataExist->jumlah+(int)$request['jumlah'],
                            'harga' => $barang->harga,
                            'nama' => $barang->nama,
                            'jenis_barang' => $barang->jenis_barang,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->full_name
                    ]);
            }else{
                DB::table('sl_quotation_chemical')->insert([
                    'quotation_id' => $request->quotation_id,
                    'barang_id' => $request->barang,
                    'jumlah' => $request['jumlah'],
                    'harga' => $barang->harga,
                    'nama' => $barang->nama,
                    'jenis_barang' => $barang->jenis_barang,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }
            
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return "Data Gagal Ditambahkan";
            abort(500);
        }
    }

    public function listChemical (Request $request){
        $raw = ['aksi'];
        $data = DB::select("SELECT DISTINCT m_barang.jenis_barang_id,sl_quotation_chemical.quotation_id,sl_quotation_chemical.barang_id,sl_quotation_chemical.jenis_barang,sl_quotation_chemical.nama,sl_quotation_chemical.harga,sl_quotation_chemical.jumlah
from sl_quotation_chemical 
INNER JOIN m_barang ON sl_quotation_chemical.barang_id = m_barang.id
WHERE sl_quotation_chemical.deleted_at is null 
and quotation_id = $request->quotation_id
ORDER BY m_barang.jenis_barang_id asc,sl_quotation_chemical.nama ASC;");

$total =DB::select("select sum(harga*jumlah) as total from sl_quotation_chemical WHERE deleted_at is null and quotation_id = $request->quotation_id")[0]->total;
$objectTotal = (object) ['jenis_barang_id' => 100,
'quotation_id' => 0,
'barang_id' => 0,
'jumlah' => '',
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
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-barang="'.$data->barang_id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        });
        $dt = $dt->editColumn('harga', function ($data){
            return "Rp ".number_format($data->harga,0,",",".");
        });

        $dt = $dt->rawColumns($raw);
        $dt = $dt->make(true);

        return $dt;
    }

    public function deleteDetailChemical(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_chemical')->where('quotation_detail',$request->quotation_detail)->where('barang_id',$request->barang_id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addBiayaMonitoring(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_detail')->where('id',$request->id)->update([
                'biaya_monitoring_kontrol' => $request->nominal - $request->ohc,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // END OF AJAX CHEMICAL
    
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

        $jumlahData = DB::select("select * from sl_quotation where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

    public function listDetailRequirement (Request $request){        
        $data = DB::table('sl_quotation_detail_requirement')
        ->where('quotation_detail_id',$request->quotation_detail_id)
        ->whereNull('deleted_at')
        ->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-detail="'.$data->quotation_detail_id.'" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function deleteDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_quotation_detail_requirement')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $requirement = $request->requirement;
            $quotationDetailId = $request->quotation_detail_id;

            $data = DB::table('sl_quotation_detail')->where('id',$quotationDetailId)->first();
            DB::table('sl_quotation_detail_requirement')->insert([
                'quotation_id' => $data->quotation_id,
                'quotation_detail_id' => $data->id,
                'requirement' => $requirement,
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
    public function cetakCoss (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $master = DB::table('sl_quotation')->where('id',$id)->first();
            $master->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            $master->totalHc = 0;
            $master->umk = 0;
            $master->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$master->id)->first();

            foreach ($master->detail as $key => $value) {
                $master->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($master->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$master->kota_id)->first();

                if($dataUmk!=null){
                    $master->umk = $dataUmk->umk;
                }
            }
            
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');

            //format
            $master->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$master->mulai_kontrak)->isoFormat('D MMMM Y');
            $master->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$master->kontrak_selesai)->isoFormat('D MMMM Y');
            $master->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$master->tgl_penempatan)->isoFormat('D MMMM Y');
            $master->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$master->created_at)->isoFormat('D MMMM Y');
            $master->stgl_quotation = Carbon::createFromFormat('Y-m-d',$master->tgl_quotation)->isoFormat('D MMMM Y');

            $master->salary_rule = "";
            $salaryRuleList = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();
            if($salaryRuleList != null){
                $master->salary_rule = $salaryRuleList->nama_salary_rule;
            }

            $master->manajemen_fee = "";
            $manajemenFeeList = DB::table('m_management_fee')->where('id',$master->management_fee_id)->first();
            if($manajemenFeeList != null){
                $master->manajemen_fee = $manajemenFeeList->nama;
            }

            $aplikasiPendukung = DB::table('sl_quotation_aplikasi')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            foreach ($aplikasiPendukung as $key => $value) {
                $app = DB::table('m_aplikasi_pendukung')->where('id',$value->aplikasi_pendukung_id)->first();
                $value->link_icon = $app->link_icon;
            }

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kaporlap where deleted_at is null and jumlah=1 and quotation_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_ohc where deleted_at is null and jumlah=1 and quotation_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_devices where deleted_at is null and jumlah=1 and quotation_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_chemical where deleted_at is null and jumlah=1 and quotation_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kaporlap')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_ohc')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_devices')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_chemical')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();

            $master->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$master->kebutuhan_id)->orderBy('name','asc')->get();
            $master->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$id)->whereNull('deleted_at')->get();

            $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama,nominal FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $master->id");

            $jumlahHc = 0;
            foreach ($master->quotation_detail as $jhc) {
                $jumlahHc += $jhc->jumlah_hc;
            }

            $provisi = 12;
            if(!strpos($master->durasi_kerjasama, 'tahun')){
                $provisi = (int)str_replace(" bulan", "", $master->durasi_kerjasama);
            }
            $master->provisi = $provisi;

            foreach ($master->quotation_detail as $ikbd => $kbd) {
                // $kbd->daftar_tunjangan = [];
                $totalTunjangan = 0;
                foreach ($daftarTunjangan as $idt => $tunjangan) {
                    $kbd->{$tunjangan->nama} = 0;
                    //cari data tunjangan
                    $dtTunjangan = DB::table('sl_quotation_detail_tunjangan')->whereNull('deleted_at')->where('quotation_detail_id',$kbd->id)->where('nama_tunjangan',$tunjangan->nama)->first();
                    if($dtTunjangan != null){
                        $kbd->{$tunjangan->nama} = $dtTunjangan->nominal;

                        $totalTunjangan += $dtTunjangan->nominal;
                    }
                }

                $kbd->nominal_takaful = 0;
                $kbd->bpjs_jkm = 0;
                $kbd->bpjs_jkk = 0;
                $kbd->bpjs_jht = 0;
                $kbd->bpjs_jp = 0;
                $kbd->bpjs_kes = 0;

                if($master->penjamin=="Takaful"){
                    $kbd->nominal_takaful = $master->nominal_takaful;
                }else{
                    // hitung JKK
                    if($master->resiko=="Sangat Rendah"){
                        $kbd->bpjs_jkk = $master->nominal_upah*0.24/100;
                    }else if($master->resiko=="Rendah"){
                        $kbd->bpjs_jkk = $master->nominal_upah*0.54/100;
                    }else if($master->resiko=="Sedang"){
                        $kbd->bpjs_jkk = $master->nominal_upah*0.89/100;
                    }else if($master->resiko=="Tinggi"){
                        $kbd->bpjs_jkk = $master->nominal_upah*1.27/100;
                    }else if($master->resiko=="Sangat Tinggi"){
                        $kbd->bpjs_jkk = $master->nominal_upah*1.74/100;
                    };

                    //hitung JKM
                    $kbd->bpjs_jkm = $master->nominal_upah*0.3/100;

                    //Hitung JHT
                    if($master->program_bpjs=="3 BPJS" || $master->program_bpjs=="4 BPJS" ){
                        $kbd->bpjs_jht = $master->nominal_upah*3.7/100;
                    }else{
                        $kbd->bpjs_jht = 0;
                    }

                    //Hitung JP
                    if($master->program_bpjs=="4 BPJS" ){
                        $kbd->bpjs_jp = $master->nominal_upah*2/100;
                    }else {
                        $kbd->bpjs_jp = 0;
                    }

                    $kbd->bpjs_kes = $master->nominal_upah*4/100;

                }

                $kbd->tunjangan_hari_raya = 0;
                if($master->thr=="Diprovisikan"){
                    $kbd->tunjangan_hari_raya = $master->nominal_upah/$provisi;
                }

                $kbd->kompensasi = 0;
                if($master->kompensasi=="Diprovisikan"){
                    $kbd->kompensasi = $master->nominal_upah/$provisi;
                }
                
                $kbd->tunjangan_holiday = 0;
                if($master->tunjangan_holiday=="Flat"){
                    $kbd->tunjangan_holiday = $master->nominal_tunjangan_holiday;
                }else{
                    $kbd->tunjangan_holiday = ($master->nominal_upah/173*(14))*15/$provisi;
                }

                $kbd->lembur = 0;
                if($master->lembur=="Flat"){
                    $kbd->lembur = $master->nominal_lembur;
                }else{
                    $kbd->lembur = 0;
                }

                $personilKaporlap = 0;
                $kbdkaporlap = DB::table('sl_quotation_kaporlap')->whereNull('deleted_at')->where('quotation_id',$master->id)->where('quotation_detail_id',$kbd->id)->get();
                foreach ($kbdkaporlap as $ikdbkap => $kdbkap) {
                    $personilKaporlap += ($kdbkap->harga*$kdbkap->jumlah)/$provisi;
                };

                $kbd->personil_kaporlap = $personilKaporlap;

                $personilDevices = 0;
                $kbddevices = DB::table('sl_quotation_devices')->whereNull('deleted_at')->where('quotation_id',$master->id)->where('quotation_detail_id',$kbd->id)->get();
                foreach ($kbddevices as $ikdbdev => $kdbdev) {
                    $personilDevices += ($kdbdev->harga*$kdbdev->jumlah)/$provisi;
                };
                $appPendukung = DB::table('sl_quotation_aplikasi')->whereNull('deleted_at')->where('quotation_id',$master->id)->get();
                foreach ($appPendukung as $kapp => $app) {
                    $personilDevices += ($app->harga*1)/$provisi;
                }

                $kbd->personil_devices = $personilDevices;

                $personilOhc = 0;
                $kbdOhc = DB::table('sl_quotation_ohc')->whereNull('deleted_at')->where('quotation_id',$master->id)->get();
                foreach ($kbdOhc as $ikdbohc => $kdbohc) {
                    $personilOhc += ($kdbohc->harga*$kdbohc->jumlah/$jumlahHc)/$provisi;
                };

                $kbd->personil_ohc = $personilOhc;

                $personilChemical = 0;
                $kbdChemical = DB::table('sl_quotation_chemical')->whereNull('deleted_at')->where('quotation_id',$master->id)->get();
                foreach ($kbdChemical as $ikdbchemical => $kdbchemical) {
                    $personilChemical += ($kdbchemical->harga*$kdbchemical->jumlah)/$provisi;
                };

                $kbd->personil_chemical = $personilChemical;

                // dd($kbd->personil_kaporlap);
                $kbd->total_personil = $master->nominal_upah+$totalTunjangan+$kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_chemical+$kbd->personil_ohc;

                $kbd->sub_total_personil = $kbd->total_personil*$kbd->jumlah_hc;
                
                $kbd->management_fee = $kbd->sub_total_personil*$master->persentase/100;

                $kbd->grand_total = $kbd->sub_total_personil+$kbd->management_fee;

                $kbd->ppn = 0;
                $kbd->pph = 0;
                if ($master->ppn_pph_dipotong=="Management Fee") {
                    $kbd->ppn = $kbd->management_fee*11/100;
                    $kbd->pph = $kbd->management_fee*(-2/100);
                }else if ($master->ppn_pph_dipotong=="Total Invoice") {
                    $kbd->ppn = $kbd->grand_total*11/100;
                    $kbd->pph = $kbd->grand_total*(-2/100);
                }
                
                $kbd->total_invoice = $kbd->grand_total + $kbd->ppn + $kbd->pph;

                

                $kbd->pembulatan = ceil($kbd->total_invoice / 1000) * 1000;

                // Harga Jual
                $kbd->total_base_manpower = $master->nominal_upah+$totalTunjangan;
                $kbd->total_exclude_base_manpower = $kbd->tunjangan_hari_raya+$kbd->kompensasi+$kbd->tunjangan_holiday+$kbd->lembur+$kbd->nominal_takaful+$kbd->bpjs_jkk+$kbd->bpjs_jkm+$kbd->bpjs_jht+$kbd->bpjs_jp+$kbd->bpjs_kes+$kbd->personil_kaporlap+$kbd->personil_devices+$kbd->personil_chemical;;

                $kbd->total_personil_coss = $kbd->total_base_manpower + $kbd->total_exclude_base_manpower + $kbd->personil_ohc + $kbd->biaya_monitoring_kontrol;

                $kbd->sub_total_personil_coss = $kbd->total_personil_coss*$kbd->jumlah_hc;
                
                $kbd->management_fee_coss = $kbd->sub_total_personil_coss*$master->persentase/100;

                $kbd->grand_total_coss = $kbd->sub_total_personil_coss+$kbd->management_fee_coss;
                
                $kbd->ppn_coss = 0;
                $kbd->pph_coss = 0;
                if($master->ppn_pph_dipotong =="Management Fee"){
                    $kbd->ppn_coss = $kbd->management_fee_coss*11/100;
                    $kbd->pph_coss = $kbd->management_fee_coss*(-2/100);
                }else  if($master->ppn_pph_dipotong =="Total Invoice"){
                    $kbd->ppn_coss = $kbd->grand_total_coss*11/100;
                    $kbd->pph_coss = $kbd->grand_total_coss*(-2/100);
                }

                $kbd->total_invoice_coss = $kbd->grand_total_coss + $kbd->ppn_coss + $kbd->pph_coss;

                $kbd->pembulatan_coss = ceil($kbd->total_invoice_coss / 1000) * 1000;

            };
            
            $listPic = DB::table('sl_quotation_pic')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            $quotationDetail = DB::table('sl_quotation_detail')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            foreach ($quotationDetail as $kkd => $vkd) {
                $vkd->requirement = DB::table('sl_quotation_detail_requirement')->where('quotation_detail_id',$vkd->id)->whereNull('deleted_at')->get();
            }
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();

            return view('sales.quotation.cetakan.coss',compact('salaryRuleQ','quotationDetail','listPic','daftarTunjangan','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','master','leads','aplikasiPendukung'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function getQuotationTujuan(Request $request){
        $quotation = DB::table("sl_quotation")->where("id",$request->quotationAsal)->first();

        $quotationTujuan = DB::table('sl_quotation')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.nomor','sl_quotation.id')
        ->whereNull('sl_quotation.deleted_at')
        ->whereNull('sl_leads.deleted_at')
        ->where("sl_quotation.step","!=",100)
        ->where('m_tim_sales_d.user_id',Auth::user()->id)
        ->where("sl_quotation.kebutuhan_id",$quotation->kebutuhan_id)
        ->get();

        return $quotationTujuan;
    }
    
    public function getSiteList(Request $request){
        $site = DB::table("sl_quotation_site")
        ->where("quotation_id",$request->quotation_id)
        ->whereNull('deleted_at')
        ->get();
        
        foreach ($site as $key => $value) {
            $value->no = $key+1;
        }

        return $site;
    }


    public function getQuotationAsal(Request $request){
        $quotation = DB::table("sl_quotation")->where("id",$request->quotationTujuan)->first();

        $quotationAsal = DB::table('sl_quotation')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.nomor','sl_quotation.id')
        ->whereNull('sl_quotation.deleted_at')
        ->whereNull('sl_leads.deleted_at')
        ->where("sl_quotation.step","=",100)
        ->where('m_tim_sales_d.user_id',Auth::user()->id)
        ->where("sl_quotation.quotation_id",$quotation->id)
        ->get();

        return $quotationAsal;
    }

    public function cetakQuotation (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table('sl_quotation')->where('id',$id)->first();
            $data->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            $data->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$id)->whereNull('deleted_at')->get();
            $data->totalHc = 0;
            $data->umk = 0;
            $data->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$data->id)->first();
            foreach ($data->detail as $key => $value) {
                $data->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($data->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$data->kota_id)->first();

                if($dataUmk!=null){
                    $data->umk = $dataUmk->umk;
                }
            }
            
            $master = DB::table('sl_quotation')->where('id',$data->id)->first();
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $master = DB::table('sl_quotation')->where('id',$id)->first();
            $master->detail = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            $master->totalHc = 0;
            $master->umk = 0;
            $master->spk = DB::table('sl_spk')->whereNull('deleted_at')->where('quotation_id',$master->id)->first();

            foreach ($master->detail as $key => $value) {
                $master->totalHc += $value->jumlah_hc;
            }

            //isi umk
            if ($master->kota_id !=null) {
                $dataUmk = DB::table('m_umk')->where('is_aktif',1)->whereNull('deleted_at')->where('city_id',$master->kota_id)->first();

                if($dataUmk!=null){
                    $master->umk = $dataUmk->umk;
                }
            }
            
            $leads = DB::table('sl_leads')->where('id',$master->leads_id)->first();
            $jabatanPic = DB::table('m_jabatan_pic')->where('id',$leads->jabatan)->first();
            if($jabatanPic!=null){
                $leads->jabatan = $jabatanPic->nama; 
            }

            //format
            $master->smulai_kontrak = Carbon::createFromFormat('Y-m-d',$master->mulai_kontrak)->isoFormat('D MMMM Y');
            $master->skontrak_selesai = Carbon::createFromFormat('Y-m-d',$master->kontrak_selesai)->isoFormat('D MMMM Y');
            $master->stgl_penempatan = Carbon::createFromFormat('Y-m-d',$master->tgl_penempatan)->isoFormat('D MMMM Y');
            $master->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$master->created_at)->isoFormat('D MMMM Y');
            $master->stgl_quotation = Carbon::createFromFormat('Y-m-d',$master->tgl_quotation)->isoFormat('D MMMM Y');

            $master->salary_rule = "";
            $salaryRuleList = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();
            if($salaryRuleList != null){
                $master->salary_rule = $salaryRuleList->nama_salary_rule;
            }

            $master->manajemen_fee = "";
            $manajemenFeeList = DB::table('m_management_fee')->where('id',$master->management_fee_id)->first();
            if($manajemenFeeList != null){
                $master->manajemen_fee = $manajemenFeeList->nama;
            }

            $aplikasiPendukung = DB::table('sl_quotation_aplikasi')->whereNull('deleted_at')->where('quotation_id',$id)->get();
            foreach ($aplikasiPendukung as $key => $value) {
                $app = DB::table('m_aplikasi_pendukung')->where('id',$value->aplikasi_pendukung_id)->first();
                $value->link_icon = $app->link_icon;
            }

            $listJenisKaporlap = DB::select("select distinct jenis_barang from sl_quotation_kaporlap where deleted_at is null and jumlah=1 and quotation_id = ".$id);
            $listJenisOhc = DB::select("select distinct jenis_barang from sl_quotation_ohc where deleted_at is null and jumlah=1 and quotation_id = ".$id);
            $listJenisDevices = DB::select("select distinct jenis_barang from sl_quotation_devices where deleted_at is null and jumlah=1 and quotation_id = ".$id);
            $listJenisChemical = DB::select("select distinct jenis_barang from sl_quotation_chemical where deleted_at is null and jumlah=1 and quotation_id = ".$id);

            $listKaporlap = DB::table('sl_quotation_kaporlap')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listOhc = DB::table('sl_quotation_ohc')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listDevices = DB::table('sl_quotation_devices')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();
            $listChemical = DB::table('sl_quotation_chemical')->where('quotation_id',$id)->where('jumlah',1)->whereNull('deleted_at')->get();

            $master->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$master->kebutuhan_id)->orderBy('name','asc')->get();
            $master->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$id)->whereNull('deleted_at')->get();

            $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $data->id");

            $jumlahHc = 0;
            foreach ($data->quotation_detail as $jhc) {
                $jumlahHc += $jhc->jumlah_hc;
            }

            $quotationService = new QuotationService();
            $data = $quotationService->calculateQuotation($data);
            
            $listPic = DB::table('sl_quotation_pic')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            $quotationDetail = DB::table('sl_quotation_detail')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            foreach ($quotationDetail as $kkd => $vkd) {
                $vkd->requirement = DB::table('sl_quotation_detail_requirement')->where('quotation_detail_id',$vkd->id)->whereNull('deleted_at')->get();
            }
            $salaryRuleQ = DB::table('m_salary_rule')->where('id',$master->salary_rule_id)->first();
            $master->tahun_quotation = Carbon::createFromFormat('Y-m-d',$master->tgl_quotation)->isoFormat('Y');
            $data->site = DB::table('sl_quotation_site')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            foreach ($data->site as $key => $value) {
                $value->jumlah_detail = 0;
                foreach ($quotationDetail as $kd => $vd) {
                    if($vd->quotation_site_id == $value->id){
                        $value->jumlah_detail += 1;
                    }
                }
            }
            $listKerjasama = DB::table('sl_quotation_kerjasama')->where('quotation_id',$master->id)->whereNull('deleted_at')->get();
            return view('sales.quotation.cetakan.quotation',compact('listKerjasama','salaryRuleQ','quotationDetail','listPic','daftarTunjangan','listChemical','listDevices','listOhc','listKaporlap','listJenisChemical','listJenisDevices','listJenisOhc','listJenisKaporlap','now','data','master','leads','aplikasiPendukung'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function viewChecklist ($id,$key){
        try {
            $strKey = md5($id."-SALESSHELTER");
            if($strKey != $key){
                return "Forbidden";
            }

            $pks = DB::table('sl_pks')->where('id',$id)->first();
            $spk = DB::table('sl_spk')->where('spk_id',$pks->spk_id)->whereNull('deleted_at')->first();
            $quotation = DB::table('sl_quotation')->where('id',$spk->quotation_id)->whereNull('deleted_at')->first();
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
            $salaryRule = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
            $quotation = DB::table("sl_quotation")->where('id',$spk->quotation_id)->first();
            $quotation->detail = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->where('layanan_id',$quotation->kebutuhan_id)->orderBy('name','asc')->get();
            $quotation->quotation_detail = DB::table('sl_quotation_detail')->where('quotation_id',$spk->quotation_id)->whereNull('deleted_at')->get();

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

            $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();

            $listPic = DB::table('sl_quotation_pic')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();
            return view('sales.quotation.cetakan.checklist',compact('listPic','listTraining','listTrainingQ','salaryRuleQ','salaryRule','leads','quotation','now'));
        } catch (\Exception $e) {
            return "Error";
        }
    }


}
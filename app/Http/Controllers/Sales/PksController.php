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
            $spk =null;
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
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->leftJoin('sl_quotation','sl_pks.quotation_id','sl_quotation.id')
                ->whereNull('sl_pks.deleted_at')
                ->whereNull('sl_spk.deleted_at')
                ->select('sl_pks.created_by','sl_pks.created_at','sl_pks.id','sl_pks.nomor','sl_spk.nomor as nomor_spk','sl_pks.tgl_pks','sl_quotation.nama_perusahaan','sl_quotation.kebutuhan','sl_pks.status_pks_id')
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
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $dataSpk = DB::table('sl_spk')->whereNull('deleted_at')->where('id',$request->spk_id)->first();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('quotation_id',$dataSpk->quotation_id)->get();

            $newId = DB::table('sl_pks')->insertGetId([
                'quotation_id' => $dataSpk->quotation_id,
                'spk_id' => $dataSpk->id,
                'leads_id' => $dataSpk->leads_id,
                'nomor' => $this->generateNomor($quotation->leads_id,$quotation[0]->company_id),
                'tgl_pks' => $current_date_time,
                'nama_perusahaan' => $dataSpk->nama_perusahaan,
                'link_pks_disetujui' => null,
                'status_pks_id' => 5,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('id',$quotation->id)->update([
                'status_quotation_id' => 5,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            DB::table('sl_spk')->where('id',$dataSpk->id)->update([
                'status_spk_id' => 3,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //insert perjanjian
            $awalan = "";
            $pasal1 = "";
            $pasal2 = "";
            $pasal3 = "";
            $pasal4 = "";
            $pasal5 = "";
            $pasal6 = "";
            $pasal7 = "";
            $pasal8 = "";
            $pasal9 = "";
            $pasal10 = "";
            $lampiran = "";

            DB::commit();
            return redirect()->route('pks.view',$newId);
        } catch (\Exception $e) {
            DB::rollback();
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
            $data = DB::table('sl_pks')->whereNull('deleted_at')->where('id',$id)->first();
            $spk = DB::table('sl_spk')->whereNull('deleted_at')->where('id',$data->spk_id)->first();
            $dataQuotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$spk->quotation_id)->whereNull('deleted_at')->first();

            $data->stgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$data->tgl_pks)->isoFormat('D MMMM Y');
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$data->created_at)->isoFormat('D MMMM Y');
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('quotation_id',$data->quotation_id)->get();
            $data->status = DB::table('m_status_pks')->whereNull('deleted_at')->where('id',$data->status_pks_id)->first()->nama;
            $perjanjian = DB::table('sl_pks_perjanjian')->whereNull('deleted_at')->where('pks_id',$id)->whereNull('deleted_at')->get();

            return view('sales.pks.view',compact('perjanjian','dataQuotation','spk','data','quotation'));
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
            DB::beginTransaction();
            DB::connection('mysqlhris')->beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            $pks = DB::table('sl_pks')->where('id',$request->id)->first();
            DB::table('sl_pks')->where('id',$request->id)->update([
                'ot5' => Auth::user()->full_name,
                'status_pks_id' => 7,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('id',$pks->quotation_id)->update([
                'status_quotation_id' => 6,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('sl_spk')->where('id',$pks->spk_id)->update([
                'status_spk_id' => 4,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);


            // dimasukkan ke customer dan site
            $quotation = DB::table('sl_quotation')->where('quotation_id',$pks->quotation_id)->whereNull('deleted_at')->get();
            $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();

            $custId = DB::table('sl_customer')->insertGetId([
                'leads_id' => $leads->id,
                'nomor' =>  $leads->nomor,
                'tgl_customer' => $current_date_time,
                'nama_perusahaan' => $leads->nama_perusahaan,
                'tim_sales_id' => $leads->tim_sales_id,
                'tim_sales_d_id' => $leads->tim_sales_d_id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_leads')->where('id',$leads->id)->update([
                'customer_id' => $custId,
                'status_leads_id' => 102,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            // SINGKRON KE CLIENT HRIS
            $clientId = DB::connection('mysqlhris')->table('m_client')->insertGetId([
                'customer_id' => $custId,
                'name' => $leads->nama_perusahaan,
                'address' => $leads->alamat,
                'is_active' => 1,
                'created_at' => $current_date_time, 
                'created_by' => Auth::user()->id,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->id
            ]);

            foreach ($quotation as $key => $value) {
                $siteId = DB::table('sl_site')->insertGetId([
                    'quotation_id' => $value->id,
                    'leads_id' =>  $leads->id,
                    'customer_id' => $custId,
                    'nama_site' => $value->nama_site,
                    'provinsi_id' => $value->provinsi_id,
                    'provinsi' => $value->provinsi,
                    'kota_id' => $value->kota_id,
                    'kota' => $value->kota,
                    'penempatan' => $value->penempatan,
                    'tim_sales_id' => $leads->tim_sales_id,
                    'tim_sales_d_id' => $leads->tim_sales_d_id,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name,
                ]);

                // SINGKRON KE SITE HRIS
                $siteHrisId = DB::connection('mysqlhris')->table('m_site')->insertGetId([
                    'site_id' => $siteId,
                    'code' => $leads->nomor,
                    'proyek_id' => 0, // ACCURATE
                    'contract_number' => $pks->nomor,
                    'name' => $value->nama_site,
                    'address' => $value->penempatan,
                    'layanan_id' => $value->kebutuhan_id,
                    'client_id' => $clientId,
                    'city_id' => $value->kota_id,
                    'branch_id' => $leads->branch_id,
                    'company_id' => $value->company_id,
                    'pic_id_1' => $leads->ro_id_1,
                    'pic_id_2' => $leads->ro_id_2,
                    'pic_id_3' => $leads->ro_id_3,
                    'supervisor_id' => $leads->ro_id,
                    'reliever' => $value->joker_reliever,
                    'contract_value' => 0,
                    'contract_start' => $value->mulai_kontrak,
                    'contract_end' => $value->kontrak_selesai,
                    'contract_terminated' => null,
                    'note_terminated' => '',
                    'contract_status' => 'Aktif',
                    'health_insurance_status' => 'Terdaftar',
                    'labor_insurance_status' => 'Terdaftar',
                    'vacation' => 0,
                    'attendance_machine' => '',
                    'is_active' => 1,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->id,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->id
                ]);

                // BUAT VACANCY
                $detailQuotation = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$value->id)->get();

                foreach ($detailQuotation as $kd => $d) {
                    $icon = 1;
                    if ($value->kebutuhan_id == 1) {
                        $icon = 6;
                    } else if ($value->kebutuhan_id == 2) {
                        $icon = 4;
                    } else if ($value->kebutuhan_id == 3) {
                        $icon = 2;
                    } else if ($value->kebutuhan_id == 4) {
                        $icon = 3;
                    };

                    DB::connection('mysqlhris')->table('m_vacancy')->insert([
                        'icon_id' => $icon,
                        'start_date' => $current_date_time,
                        'end_date' => Carbon::now()->addDays(7)->toDateTimeString(),
                        'company_id' => $value->company_id,
                        'site_id' => $siteHrisId,
                        'position_id' => $d->position_id,
                        'province_id' => $value->provinsi_id,
                        'city_id' => $value->kota_id,
                        'title' => $d->jabatan_kebutuhan,
                        'type' => '',
                        'content' => '',
                        'needs' => $d->jumlah_hc,
                        'phone_number1' => '',
                        'phone_number2' => '',
                        'flyer' => '',
                        'is_active' => 1,
                        'durasi_ketelitian' => 0,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->id,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->id
                    ]);
                }

                // masukkan COSS ke tabel 
                $totalNominal = 0;
                $totalNominalCoss = 0;
                $ppn = 0;
                $ppnCoss = 0;
                $totalBiaya = 0;
                $totalBiayaCoss = 0;
                $margin = 0;
                $marginCoss = 0;
                $gpm = 0;
                $gpmCoss = 0;
                $quotationService = new QuotationService();
                $calcQuotation = $quotationService->calculateQuotation($value);
                foreach ($calcQuotation->quotation_detail as $kd => $kbd) {
                    DB::table("sl_quotation_hpp")->insert([
                        'quotation_id' => $value->id,
                        'quotation_detail_id' => $kbd->id,
                        'position_id' => $kbd->position_id,
                        'leads_id' =>  $leads->id,
                        'jumlah_hc' => $calcQuotation->jumlah_hc,
                        'gaji_pokok' => $calcQuotation->nominal_upah,
                        'total_tunjangan' => $kbd->total_tunjangan,
                        'tunjangan_hari_raya' => $kbd->tunjangan_hari_raya,
                        'kompensasi' => $kbd->kompensasi,
                        'tunjangan_hari_libur_nasional' => $kbd->tunjangan_holiday,
                        'lembur' => $kbd->lembur,
                        'bpjs_jkk' => $kbd->bpjs_jkk,
                        'bpjs_jkm' => $kbd->bpjs_jkm,
                        'bpjs_jht' => $kbd->bpjs_jht,
                        'bpjs_jp' => $kbd->bpjs_jp,
                        'bpjs_ks' => $kbd->bpjs_kes,
                        'persen_bpjs_jkk' =>  $kbd->persen_bpjs_jkk,
                        'persen_bpjs_jkm' =>  $kbd->persen_bpjs_jkm,
                        'persen_bpjs_jht' =>  $kbd->persen_bpjs_jht,
                        'persen_bpjs_jp' =>  $kbd->persen_bpjs_jp,
                        'persen_bpjs_ks' =>  $kbd->persen_bpjs_kes,
                        'provisi_seragam' =>  $kbd->personil_kaporlap,
                        'provisi_peralatan' => $kbd->personil_devices ,
                        'chemical' => $kbd->personil_chemical,
                        'total_biaya_per_personil' => $kbd->total_personil,
                        'total_biaya_all_personil' => $kbd->sub_total_personil,
                        'management_fee' => $kbd->management_fee,
                        'persen_management_fee' => $value->persentase,
                        'ohc' => $kbd->total_ohc,
                        'grand_total' => $kbd->grand_total,
                        'ppn' => $kbd->ppn,
                        'pph' => $kbd->pph,
                        'total_invoice' => $kbd->total_invoice,
                        'pembulatan' => $kbd->pembulatan,
                        'is_pembulatan' => $kbd->is_pembulatan,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);

                    DB::table("sl_quotation_coss")->insert([
                        'quotation_id' => $value->id,
                        'quotation_detail_id' => $kbd->id,
                        'position_id' => $kbd->position_id,
                        'leads_id' =>  $leads->id,
                        'jumlah_hc' => $calcQuotation->jumlah_hc,
                        'gaji_pokok' => $calcQuotation->nominal_upah,
                        'total_tunjangan' => $kbd->total_tunjangan,
                        'total_base_manpower' => $kbd->total_base_manpower,
                        'tunjangan_hari_raya' => $kbd->tunjangan_hari_raya,
                        'kompensasi' => $kbd->kompensasi,
                        'tunjangan_hari_libur_nasional' => $kbd->tunjangan_holiday,
                        'lembur' => $kbd->lembur,
                        'bpjs_jkk' => $kbd->bpjs_jkk,
                        'bpjs_jkm' => $kbd->bpjs_jkm,
                        'bpjs_jht' => $kbd->bpjs_jht,
                        'bpjs_jp' => $kbd->bpjs_jp,
                        'bpjs_ks' => $kbd->bpjs_kes,
                        'persen_bpjs_jkk' =>  $kbd->persen_bpjs_jkk,
                        'persen_bpjs_jkm' =>  $kbd->persen_bpjs_jkm,
                        'persen_bpjs_jht' =>  $kbd->persen_bpjs_jht,
                        'persen_bpjs_jp' =>  $kbd->persen_bpjs_jp,
                        'persen_bpjs_ks' =>  $kbd->persen_bpjs_kes,
                        'provisi_seragam' =>  $kbd->personil_kaporlap,
                        'provisi_peralatan' => $kbd->personil_devices ,
                        'chemical' => $kbd->personil_chemical,
                        'total_exclude_base_manpower' => $kbd->total_exclude_base_manpower,
                        'bunga_bank' => $kbd->bunga_bank,
                        'insentif' => $kbd->insentif,
                        'management_fee' => $kbd->management_fee_coss,
                        'persen_bunga_bank' => $value->persen_bunga_bank,
                        'persen_insentif' => $value->persen_insentif,
                        'persen_management_fee' => $value->persentase,
                        'grand_total' => $kbd->grand_total_coss,
                        'ppn' => $kbd->ppn_coss,
                        'pph' => $kbd->pph_coss,
                        'total_invoice' => $kbd->total_invoice_coss,
                        'pembulatan' => $kbd->pembulatan_coss,
                        'is_pembulatan' => $kbd->is_pembulatan,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);

                    $totalNominal += $kbd->total_invoice;
                    $totalNominalCoss += $kbd->total_invoice_coss;
                    $ppn += $kbd->ppn;
                    $ppnCoss += $kbd->ppn_coss;
                    $totalBiaya += $kbd->sub_total_personil;
                    $totalBiayaCoss += $kbd->sub_total_personil;
                    $margin = $totalNominal-$ppn-$totalBiaya;
                    $marginCoss = $totalNominalCoss-$ppnCoss-$totalBiayaCoss;
                    $gpm = ($margin/$totalBiaya)*100;
                    $gpmCoss = ($marginCoss/$totalBiayaCoss)*100;
                }
                DB::table("sl_quotation_margin")->insert([
                    'quotation_id' => $value->id,
                    'leads_id' =>  $leads->id,
                    'nominal_hpp' => $totalNominal,
                    'nominal_harga_pokok' => $totalNominalCoss,
                    'ppn_hpp' => $ppn,
                    'ppn_harga_pokok' => $ppnCoss,
                    'total_biaya_hpp' => $totalBiaya,
                    'total_biaya_harga_pokok' => $totalBiayaCoss,
                    'margin_hpp' => $margin,
                    'margin_harga_pokok' => $marginCoss,
                    'gpm_hpp' => $gpm,
                    'gpm_harga_pokok' => $gpmCoss,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            // SINGKRON KE ACCURATE

            DB::commit();
            DB::connection('mysqlhris')->commit();
        } catch (\Exception $e) {
            DB::rollback();
            DB::connection('mysqlhris')->rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function isiChecklist(Request $request,$id){
        $pks = DB::table('sl_pks')->where('id',$id)->first();
        $spk = DB::table('sl_spk')->where('spk_id',$pks->spk_id)->whereNull('deleted_at')->first();
        $quotation = DB::table('sl_quotation')->where('id',$spk->quotation_id)->whereNull('deleted_at')->first();

        $listRo = DB::connection('mysqlhris')->table('m_user')->whereIn('role_id',[4,5,6,8])->orderBy('full_name','asc')->get();
        $listCrm = DB::connection('mysqlhris')->table('m_user')->whereIn('role_id',[54,55,56])->orderBy('full_name','asc')->get();

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

        return view('sales.pks.checklist-form',compact('listCrm','listRo','pks','quotation','listJabatanPic','listTrainingQ','listTraining','salaryRuleQ','leads'));
    }

    public function saveChecklist(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            
            if($request->ada_serikat=="Tidak Ada"){
                $request->status_serikat ="Tidak Ada";
            }

            // $ro = DB::connection('mysqlhris')->table('m_user')->where('id',$request->ro)->first();
            // $crm = DB::connection('mysqlhris')->table('m_user')->where('id',$request->crm)->first();
        
            // DB::table('sl_pks')->where('id',$request->pks_id)->update([
            //     'ro_id' => $ro->id,
            //     'ro' => $ro->full_name,
            //     'crm_id' => $crm->id,
            //     'crm' => $crm->full_name,
            //     'updated_at' => $current_date_time,
            //     'updated_by' => Auth::user()->full_name
            // ]);

            DB::table('sl_quotation')->where('quotation_id',$request->quotation_id)->update([
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

            return redirect()->route('pks.view',$request->pks_id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function cetakPks (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table('sl_pks_perjanjian')->where('pks_id',$id)->get();
            return view('sales.pks.cetakan.pks',compact('now','data'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function editPerjanjian ($id){
        try {
            $data = DB::table('sl_pks_perjanjian')->where('id',$id)->first();
            $pks = DB::table('sl_pks')->where('id',$data->pks_id)->first();
            
            return view('sales.pks.edit-perjanjian',compact('data','pks'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEditPerjanjian(Request $request,$id){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_pks_perjanjian')->where('id',$id)->first();

            DB::table('sl_pks_perjanjian')->where('id',$id)->update([
                'pasal' => $request->pasal,
                'judul' => $request->judul,
                'raw_text' => $request->raw_text,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name 
            ]);
            return redirect()->route('pks.view',$data->pks_id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function ajukanUlangQuotation (Request $request,$pks){
        $current_date_time = Carbon::now()->toDateTimeString();
        $current_date = Carbon::now()->toDateString();
        try {
            $pks = DB::table('sl_pks')->where('id',$pks)->first();
            $spk = DB::table('sl_spk')->where('id',$pks->spk_id)->first();

            DB::beginTransaction();

            $qasalId = $spk->quotation_id;
            $qtujuan = DB::table("sl_quotation")->where('id',$qasalId)->first();

            $dataToInsertQuotation = (array) $qtujuan;
            unset($dataToInsertQuotation['id']);
            unset($dataToInsertQuotation['nomor']);

            $dataToInsertQuotation['nomor'] = $this->generateNomorQuotation($qtujuan->leads_id,$qtujuan->company_id);
            $dataToInsertQuotation['revisi'] = $qtujuan->revisi+1;
            $dataToInsertQuotation['alasan_revisi'] = $request->alasan;            
            $dataToInsertQuotation['quotation_asal_id'] = $qtujuan->id;
            $dataToInsertQuotation['step'] = 1;
            $dataToInsertQuotation['created_at'] = $current_date_time;
            $dataToInsertQuotation['created_by'] = Auth::user()->full_name;
            $qtujuanId = DB::table('sl_quotation')->insertGetId($dataToInsertQuotation);

            // QUotation Detail
            $detail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_detail")->where('quotation_id',$qasalId)->update([
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
            DB::commit();

            // hapus spk yang diajukan ulang
            DB::table('sl_spk')->where('id',$spk->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
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

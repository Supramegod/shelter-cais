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


class CustomerActivityController extends Controller
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
        return view('sales.customer-activity.list',compact('branch','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function add (Request $request){
        try {
            $leads = null;

            if(!empty($request->leads_id)){
                $leads = DB::table('sl_leads')->where('id',$request->leads_id)->first();
                $branchLeads = DB::connection('mysqlhris')->table('m_branch')->where('id',$leads->branch_id)->first();
                if($branchLeads !=null){
                    $leads->branch = $branchLeads->name;
                }
                $kebutuhanLeads = DB::table('m_kebutuhan')->where('id',$leads->kebutuhan_id)->first();
                if($kebutuhanLeads !=null){
                    $leads->kebutuhan = $kebutuhanLeads->nama;
                }
                
                $timSalesId = DB::table('m_tim_sales')->where('id',$leads->tim_sales_id)->first();
                $timSalesDId = DB::table('m_tim_sales_d')->where('id',$leads->tim_sales_d_id)->first();
                if($timSalesId !=null){
                    $leads->timSalesName = $timSalesId->nama;
                }
                if($timSalesDId !=null){
                    $leads->salesName = $timSalesDId->nama;
                }
            }
            
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $nowd = Carbon::now()->toDateString();
            $statusLeads = DB::table('m_status_leads')->whereNull('deleted_at')->where('id','!=','1')->where('id','!=','2')->get();
            $timSales = DB::table('m_tim_sales')->whereNull('deleted_at')->get();
            $tim_sales_id = null;
            $tim_sales_d_id = null;

            $roList = DB::connection('mysqlhris')->select("SELECT id,full_name from m_user WHERE role_id IN ( 4,5,6,7,8 ) and is_active = 1 ORDER BY role_id ASC , full_name ASC");
            $crmList = DB::connection('mysqlhris')->select("SELECT id,full_name from m_user WHERE role_id IN ( 54,55,56 ) and is_active = 1 ORDER BY role_id ASC , full_name ASC");

            if(Auth::user()->role_id == 29){
                $dataSalesD = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->whereNull('deleted_at')->first();
                $tim_sales_id = $dataSalesD->tim_sales_id;
                $tim_sales_d_id = $dataSalesD->id;
            }
            
            return view('sales.customer-activity.add',compact('roList','crmList','leads','now','nowd','statusLeads','timSales','tim_sales_id','tim_sales_d_id'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function view (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $nowd = Carbon::now()->toDateString();
            $statusLeads = DB::table('m_status_leads')->whereNull('deleted_at')->where('id','!=','1')->get();
            $timSales = DB::table('m_tim_sales')->whereNull('deleted_at')->get();

            $data = DB::table('sl_customer_activity')->where('id',$id)->first();
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$data->created_at)->isoFormat('D MMMM Y HH:mm');
            $data->stgl_activity = Carbon::createFromFormat('Y-m-d',$data->tgl_activity)->isoFormat('D MMMM Y');
            
            $leads = DB::table('sl_leads')->where('id',$data->leads_id)->first();
            $branchLeads = DB::connection('mysqlhris')->table('m_branch')->where('id',$leads->branch_id)->first();
            $kebutuhanLeads = DB::table('m_kebutuhan')->where('id',$leads->kebutuhan_id)->first();
            $timSalesD = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$data->tim_sales_id)->get();

            $leads->branch = $branchLeads->name;
            $leads->kebutuhan = $kebutuhanLeads->nama;
            $tim_sales_id = $leads->tim_sales_id;
            $tim_sales_d_id = $leads->tim_sales_d_id;

            $activity = DB::table('sl_customer_activity')->whereNull('deleted_at')->where('leads_id',$data->leads_id)->orderBy('created_at','desc')->limit(5)->get();
            foreach ($activity as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y HH:mm');
                $value->stgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
            }
            
            // inisialisasi 
            $data->status_leads = null;
            $data->tim_sales = null;
            $data->nama_sales = null;

            // isi keterangan 
            $statusAct = DB::table('m_status_leads')->where('id',$data->status_leads_id)->first();
            if($statusAct !=null){
                $data->status_leads = $statusAct->nama;
            }

            $timSales = DB::table('m_tim_sales')->where('id',$data->tim_sales_id)->first();
            if($timSales !=null){
                $data->tim_sales = $timSales->nama;
            }
            
            $timSalesD = DB::table('m_tim_sales_d')->where('id',$data->tim_sales_d_id)->first();
            if($timSalesD !=null){
                $namaSales = DB::connection('mysqlhris')->table('m_user')->where('id',$timSalesD->user_id)->first();
                if($namaSales !=null){
                    $data->nama_sales = $namaSales->full_name;
                }
            }

            $dataFile = DB::table('sl_customer_activity_file')->whereNull('deleted_at')->where('customer_activity_id',$id)->get();

            return view('sales.customer-activity.view',compact('dataFile','activity','leads','data','now','nowd','statusLeads','timSales','timSalesD','tim_sales_id','tim_sales_d_id'));
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
                'leads' => 'required',
                'tgl_activity' => 'required',
                'tipe' => 'required'
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                // dd($request);
                // validator tipe 
                if($request->tipe=="Telepon" || $request->tipe=="Online Meeting"){
                    $validator = Validator::make($request->all(), [
                        'tgl_realisasi_telepon' => 'required',
                        'start' => 'required',
                        'end' => 'required'
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }else if($request->tipe=="Visit"){
                    $validator = Validator::make($request->all(), [
                        'tgl_realisasi' => 'required',
                        'jam_realisasi' => 'required',
                        'jenis_visit' => 'required',
                        'notulen' => 'required'
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }else if($request->tipe=="Kirim Berkas"){
                    if($request->id == null){
                        $validator = Validator::make($request->all(), [
                            'tgl_realisasi' => 'required',
                            'jam_realisasi' => 'required',
                            'penerima' => 'required',
                            'files' => 'required'
                        ], [
                            'min' => 'Masukkan :attribute minimal :min',
                            'max' => 'Masukkan :attribute maksimal :max',
                            'required' => ':attribute harus di isi',
                            'mimes' =>'Extensi tidak valid'
                        ]);
                    }else{
                        $validator = Validator::make($request->all(), [
                            'tgl_realisasi' => 'required',
                            'jam_realisasi' => 'required',
                            'penerima' => 'required'
                        ], [
                            'min' => 'Masukkan :attribute minimal :min',
                            'max' => 'Masukkan :attribute maksimal :max',
                            'required' => ':attribute harus di isi',
                            'mimes' =>'Extensi tidak valid'
                        ]);
                    }
                }else if($request->tipe=="Email"){
                    $validator = Validator::make($request->all(), [
                        'tgl_realisasi' => 'required',
                        'email' => 'required',
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }
                // else if($request->tipe=="Ubah Status"){
                //     $validator = Validator::make($request->all(), [
                //         'status_leads_id' => 'required',
                //     ], [
                //         'min' => 'Masukkan :attribute minimal :min',
                //         'max' => 'Masukkan :attribute maksimal :max',
                //         'required' => ':attribute harus di isi',
                //     ]);
                // }
                else if($request->tipe=="Pilih Sales"){
                    $validator = Validator::make($request->all(), [
                        'tim_sales_id' => 'required',
                        'tim_sales_d_id' => 'required',
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }else if($request->tipe=="Pilih RO"){
                    $validator = Validator::make($request->all(), [
                        'ro' => 'required',
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }else if($request->tipe=="Pilih CRM"){
                    $validator = Validator::make($request->all(), [
                        'crm' => 'required',
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }

                //validator role
                if(!in_array(Auth::user()->role_id,[4,5])){
                    $validator = Validator::make($request->all(), [
                        'status_leads_id' => 'required',
                    ], [
                        'min' => 'Masukkan :attribute minimal :min',
                        'max' => 'Masukkan :attribute maksimal :max',
                        'required' => ':attribute harus di isi',
                    ]);
                }

                if ($validator->fails()) {
                    return back()->withErrors($validator->errors())->withInput();
                }

                $current_date_time = Carbon::now()->toDateTimeString();
                $nomor = $this->generateNomor($request->leads_id);

                //optional
                $start = null;
                $end = null;
                $durasi = null;
                $tglRealisasi = null;
                $jamRealisasi = null;
                $notesTipe = null;
                $linkBuktiFoto = null;
                $penerima = null;
                $jenisVisit = null;
                $notulen = null;
                $email = null;
                $timSalesId = null;
                $timSalesDId = null;
                $branchSales = null;
                $roId = null;
                $crmId = null;
                $roName = null;
                $crmName = null;
                $statusLeads = null;

                $msgSave = '';

                if($request->start !=null){
                    $start = $request->start;
                }

                if($request->end !=null){
                    $end = $request->end;
                }

                if($request->durasi !=null){
                    $durasi = $request->durasi;
                }

                if($request->tgl_realisasi_telepon !=null){
                    $tglRealisasi = $request->tgl_realisasi_telepon;
                }

                if($request->tgl_realisasi !=null){
                    $tglRealisasi = $request->tgl_realisasi;
                }

                if($request->jam_realisasi !=null){
                    $jamRealisasi = $request->jam_realisasi;
                }

                if($request->notes_tipe !=null){
                    $notesTipe = $request->notes_tipe;
                }

                if($request->penerima !=null){
                    $penerima = $request->penerima;
                }

                if($request->jenis_visit !=null){
                    $jenisVisit = $request->jenis_visit;
                }

                if($request->notulen !=null){
                    $notulen = $request->notulen;
                }

                if($request->email !=null){
                    $email = $request->email;
                }
                
                if($request->status_leads_id !=null) {
                    $statusLeads = $request->status_leads_id;
                }

                if($request->tim_sales_id !=null){
                    $timSalesId = $request->tim_sales_id;
                    $timSales = DB::table('m_tim_sales')->where('id',$request->tim_sales_id)->first();
                    $branchSales = $timSales->branch_id;
                }

                if($request->tim_sales_d_id !=null){
                    $timSalesDId = $request->tim_sales_d_id;
                }
                
                if($request->ro !=null){
                    $roId = $request->ro;
                    $roName = DB::connection('mysqlhris')->table('m_user')->where('id',$roId)->first();
                    $roName = $roName->full_name;
                }

                if($request->crm !=null){
                    $crmId = $request->crm;
                    $crmName = DB::connection('mysqlhris')->table('m_user')->where('id',$crmId)->first();
                    $crmName = $crmName->full_name;
                }

                if(!empty($request->id)){
                    $dataActivity = DB::table('sl_customer_activity')->where('id',$request->id)->first();
                    if($linkBuktiFoto ==null){
                        $linkBuktiFoto = $dataActivity->link_bukti_foto;
                    }

                    DB::table('sl_customer_activity')->where('id',$request->id)->update([
                        'tgl_activity' => $request->tgl_activity,
                        'tim_sales_id' => $timSalesId,
                        'tim_sales_d_id' => $timSalesDId,
                        'notes' => $request->notes,
                        'tipe' => $request->tipe,
                        'start' => $start,
                        'end' => $end,
                        'durasi' => $durasi,
                        'tgl_realisasi' => $tglRealisasi,
                        'jam_realisasi' => $jamRealisasi,
                        'penerima' => $penerima,
                        'notes_tipe' => $notesTipe,
                        'notulen' => $notulen,
                        'jenis_visit' => $jenisVisit,
                        'link_bukti_foto' => $linkBuktiFoto,
                        'email' => $email,
                        'ro_id' => $roId,
                        'ro' => $roName,
                        'crm_id' => $crmId,
                        'crm' => $crmName,
                        'status_leads_id' => $statusLeads,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->name
                    ]);
                    $msgSave = 'Customer Activity berhasil disimpan.';

                }else{
                    $id = DB::table('sl_customer_activity')->insertGetId([
                        'tgl_activity' => $request->tgl_activity,
                        'branch_id' => $branchSales,
                        'nomor' => $nomor,
                        'leads_id' => $request->leads_id,
                        'tim_sales_id' => $timSalesId,
                        'tim_sales_d_id' => $timSalesDId,
                        'notes' => $request->notes,
                        'tipe' => $request->tipe,
                        'start' => $start,
                        'end' => $end,
                        'durasi' => $durasi,
                        'tgl_realisasi' => $tglRealisasi,
                        'jam_realisasi' => $jamRealisasi,
                        'penerima' => $penerima,
                        'notes_tipe' => $notesTipe,
                        'link_bukti_foto' => $linkBuktiFoto,
                        'notulen' => $notulen,
                        'email' => $email,
                        'ro_id' => $roId,
                        'ro' => $roName,
                        'crm_id' => $crmId,
                        'crm' => $crmName,
                        'status_leads_id' => $statusLeads,
                        'jenis_visit' => $jenisVisit,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                    
                    // save file
                    if($request->file('files') != null){
                        foreach ($request->file('files') as $key => $value) {
                            $file = $request->file('files')[$key];
                            $extension = $file->getClientOriginalExtension();

                            $filename = $file->getClientOriginalName();
                            $filename = str_replace(".".$extension,"",$filename);
                            $originalName = $filename.date("YmdHis").rand(10000,99999).".".$extension."";
                    
                            Storage::disk('bukti-activity')->put($originalName, file_get_contents($file));

                            $link = env('APP_URL').'/public/uploads/customer-activity/'.$originalName;

                            DB::table('sl_customer_activity_file')->insert([
                                'customer_activity_id' => $id,
                                'nama_file' => $request->namafiles[$key],
                                'url_file' => $link,
                                'created_at' => $current_date_time,
                                'created_by' => Auth::user()->full_name
                            ]);
                        }
                    }
                    
                    $msgSave = 'Customer Activity berhasil disimpan dengan nomor : '.$nomor.' !';
                }

                //update status leads
                if($request->status_leads_id !=null){
                    $dataLeads = DB::table('sl_leads')->where('id',$request->leads_id)->first();
                    if($dataLeads->status_leads_id != $request->status_leads_id){
                        DB::table('sl_leads')->where('id',$request->leads_id)->update([
                            'status_leads_id' => $request->status_leads_id,
                            'updated_at' => $current_date_time,
                            'updated_by' => Auth::user()->name
                        ]);
                    };
                }
                

                if($request->tipe=="Pilih Sales"){
                    DB::table('sl_leads')->where('id',$request->leads_id)->update([
                        'tim_sales_id' => $timSalesId,
                        'tim_sales_d_id' => $timSalesDId,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->name
                    ]);
                }else if($request->tipe=="Pilih RO"){
                    DB::table('sl_leads')->where('id',$request->leads_id)->update([
                        'ro_id' => $roId,
                        'ro' => $roName,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->name
                    ]);
                }else if($request->tipe=="Pilih CRM"){
                    DB::table('sl_leads')->where('id',$request->leads_id)->update([
                        'crm_id' => $crmId,
                        'crm' => $crmName,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->name
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete (Request $request){
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_customer_activity')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->name
            ]);

            $msgSave = 'Customer activity berhasil dihapus.';
            
            DB::commit();
            return redirect()->route('customer-activity')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function memberTimSales (Request $request){
        $data = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$request->tim_sales_id)->get();
        return $data;
    }

    public function list (Request $request){
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();

            $data = DB::table('sl_customer_activity')
                        ->join('sl_leads','sl_leads.id','sl_customer_activity.leads_id')
                        ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
                        ->leftJoin('m_kebutuhan','m_kebutuhan.id','=','sl_leads.kebutuhan_id')
                        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                        ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
                        ->select('sl_customer_activity.created_by','sl_customer_activity.email','sl_customer_activity.notulen','sl_customer_activity.jenis_visit','sl_customer_activity.link_bukti_foto','sl_customer_activity.penerima','sl_customer_activity.jam_realisasi','sl_customer_activity.tgl_realisasi','sl_customer_activity.notes_tipe','sl_customer_activity.start','sl_customer_activity.end','sl_customer_activity.durasi','m_status_leads.nama as status_leads','sl_customer_activity.leads_id','sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_customer_activity.tipe','sl_leads.nama_perusahaan as nama', $db2.'.m_branch.name as branch', 'm_kebutuhan.nama as kebutuhan','m_tim_sales_d.nama as sales','sl_customer_activity.notes as keterangan')
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
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function generateNomor ($leadsId){
        //generate nomor CAT/SG/ABCD1-072024-00001;
        $now = Carbon::now();

        $nomor = "CAT/";

        $dataLeads = DB::table('sl_leads')->where('id',$leadsId)->first();
        if($dataLeads != null){
            if($dataLeads->kebutuhan_id==2){
                $nomor = $nomor."LS/";
            } else if($dataLeads->kebutuhan_id==1){
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

        $jumlahData = DB::select("select * from sl_customer_activity where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

}

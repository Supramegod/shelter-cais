<?php

namespace App\Http\Controllers\Auth;

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

class DashboardController extends Controller
{
    public function dashboardApproval(Request $request){
        $jumlahMenungguApproval = 0;
        $jumlahMenungguDirSales = 0;
        $jumlahMenungguDirkeu = 0;
        $jumlahMenungguDirut = 0;
        $quotationBelumLengkap = 0;
        $jumlahMenungguManagerCrm = 0;
        $error = 0;

        $data = DB::table('sl_quotation')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
        ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.step','sl_quotation.top','sl_quotation.ot3','sl_quotation.ot2','sl_quotation.ot1','sl_quotation.nama_site','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
        ->whereNull('sl_leads.deleted_at')
        ->whereNull('sl_quotation.deleted_at')
        ->where('sl_quotation.is_aktif',0)->get();

        $quotationExisting = DB::table('sl_quotation')->whereNull('deleted_at')->where('is_aktif',0)->get();
        

        $dataMenungguAnda = [];
        $dataMenungguApproval = [];
        $dataBelumLengkap = [];

        foreach ($quotationExisting as $key => $quotation) {
            array_push($dataMenungguApproval,$quotation);

            if ($quotation->step == 100 && $quotation->is_aktif==0){
                if ($quotation->ot1 == null) {
                    $jumlahMenungguDirSales++;
                    if(Auth::user()->role_id==96){
                        array_push($dataMenungguAnda,$quotation);
                    }
                }
                if($quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari"){
                    $jumlahMenungguDirkeu++;
                    if(Auth::user()->role_id==97 || Auth::user()->role_id==40 ){
                        array_push($dataMenungguAnda,$quotation);
                    }
                }
                if ( $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari" ){
                    $jumlahMenungguDirut++;
                }
            } else if ( $quotation->step != 100){
                $quotationBelumLengkap++;
                $jumlahMenungguApproval++;
                if(Auth::user()->role_id==99){
                    array_push($dataMenungguAnda,$quotation);
                }
            }else{
                array_push($dataBelumLengkap,$quotation);
                $error++;
            }
        }

        // jika manager crm maka cari sl_pks yang siap untuk diaktifkan dengan status_pks_id = 6
        if(Auth::user()->role_id==56){
            $dataPks = DB::table('sl_pks')
            ->leftJoin('m_status_pks','sl_pks.status_pks_id','m_status_pks.id')
            ->select('sl_pks.id','sl_pks.nomor','sl_pks.tgl_pks','sl_pks.leads_id','sl_pks.status_pks_id','m_status_pks.nama as status_pks')
            ->whereNull('sl_pks.deleted_at')
            ->where('sl_pks.status_pks_id',6)->get();

            foreach ($dataPks as $key => $pks) {
                $jumlahMenungguManagerCrm++;
            }
        }

        return view('home.dashboard-approval',compact('jumlahMenungguManagerCrm','dataBelumLengkap','dataMenungguApproval','dataMenungguAnda','jumlahMenungguApproval','jumlahMenungguDirSales','jumlahMenungguDirkeu','jumlahMenungguDirut','quotationBelumLengkap'));
    }

    public function dashboardAktifitasSales(Request $request) {
        $aktifitasSalesHariIni = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->where('is_activity',1)
            ->whereDate('created_at', Carbon::today()->toDateString())

            ->count();

            // dd(Carbon::now()->endOfWeek()->format('Y-m-d'));
        $aktifitasSalesMingguIni = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->where('is_activity',1)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $aktifitasSalesBulanIni = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->where('is_activity',1)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $aktifitasSalesTahunIni = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->where('is_activity',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $aktifitasSalesUserIds = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('user_id')
            ->where('is_activity', 1)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();
        
        $sales = [];
        $jumlahAktifitas = [];
        foreach ($aktifitasSalesUserIds as $key => $value) {
            $user = DB::connection('mysqlhris')->table('m_user')->where('id',$value->user_id)->first();
            if($user==null){
                continue;
            }
            array_push($sales,$user->full_name." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitas,$value->jumlah_aktifitas);
        }

        $aktifitasByTipe = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->select('tipe', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('tipe')
            ->where('is_activity', 1)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();
        
        $tipe = [];
        $jumlahAktifitasTipe = [];
        foreach ($aktifitasByTipe as $key => $value) {
            array_push($tipe,$value->tipe." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasTipe,$value->jumlah_aktifitas);
        };

        $aktifitasByStatusLeads = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->select(DB::raw('(select nama from m_status_leads where id=sl_customer_activity.status_leads_id) as status_leads'), DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('status_leads')
            ->where('is_activity', 1)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();
        
        $statusLeads = [];
        $jumlahAktifitasStatusLeads = [];
        foreach ($aktifitasByStatusLeads as $key => $value) {
            array_push($statusLeads,$value->status_leads." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasStatusLeads,$value->jumlah_aktifitas);
        };

        $aktifitasSalesPerTanggal = [];

        for ($i = 1; $i <= 31; $i++) {
            $aktifitasSalesPerTanggal[$i] = 0;
        }

        $aktifitasSales = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->where('is_activity', 1)
            ->whereNotNull('user_id')
            ->whereMonth('created_at', Carbon::now()->month)
            ->select(DB::raw('DAY(created_at) as tanggal'), 'user_id', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('tanggal', 'user_id')
            ->get();

        $aktifitasSalesUser = [];

        foreach ($aktifitasSales as $aktifitas) {
            if (!isset($aktifitasSalesUser[$aktifitas->user_id])) {
                $aktifitasSalesUser[$aktifitas->user_id] = [
                    'user' => DB::connection('mysqlhris')->table('m_user')->where('id', $aktifitas->user_id)->value('full_name'),
                    'jumlah_aktifitas' => []
                ];
            }

            $aktifitasSalesUser[$aktifitas->user_id]['jumlah_aktifitas'][$aktifitas->tanggal] = $aktifitas->jumlah_aktifitas;
        }

        foreach ($aktifitasSalesUser as &$userAktifitas) {
            for ($i = 1; $i <= 31; $i++) {
                if (!isset($userAktifitas['jumlah_aktifitas'][$i])) {
                    $userAktifitas['jumlah_aktifitas'][$i] = 0;
                }
            }
            ksort($userAktifitas['jumlah_aktifitas']);
        }

        $aktifitasSalesPerTanggal = array_values($aktifitasSalesUser);

        $aktifitasSalesByTipePerTanggal = [];

        $aktifitasSalesByTipe = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->where('is_activity', 1)
            ->whereMonth('created_at', Carbon::now()->month)
            ->select('tipe', DB::raw('DAY(created_at) as tanggal'), DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('tipe', 'tanggal')
            ->get();

        foreach ($aktifitasSalesByTipe as $aktifitas) {
            if (!isset($aktifitasSalesByTipePerTanggal[$aktifitas->tipe])) {
                $aktifitasSalesByTipePerTanggal[$aktifitas->tipe] = [
                    'tipe' => $aktifitas->tipe,
                    'jumlah_aktifitas' => []
                ];
            }

            $aktifitasSalesByTipePerTanggal[$aktifitas->tipe]['jumlah_aktifitas'][$aktifitas->tanggal] = $aktifitas->jumlah_aktifitas;
        }

        foreach ($aktifitasSalesByTipePerTanggal as &$tipeAktifitas) {
            for ($i = 1; $i <= 31; $i++) {
                if (!isset($tipeAktifitas['jumlah_aktifitas'][$i])) {
                    $tipeAktifitas['jumlah_aktifitas'][$i] = 0;
                }
            }
            ksort($tipeAktifitas['jumlah_aktifitas']);
            $tipeAktifitas['jumlah_aktifitas'] = array_map(function($tanggal, $aktifitas) {
                return ['tanggal' => $tanggal, 'aktifitas' => $aktifitas];
            }, array_keys($tipeAktifitas['jumlah_aktifitas']), $tipeAktifitas['jumlah_aktifitas']);
        }

        $aktifitasSalesByTipePerTanggal = array_values($aktifitasSalesByTipePerTanggal);
        
        $aktifitasByVisit = DB::table('sl_customer_activity')
            ->whereNull('deleted_at')
            ->whereNotNull('jenis_visit')
            ->select('jenis_visit', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('jenis_visit')
            ->where('is_activity', 1)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();
        
        $jenisVisit = [];
        $jumlahAktifitasVisit = [];
        foreach ($aktifitasByVisit as $key => $value) {
            array_push($jenisVisit,$value->jenis_visit." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasVisit,$value->jumlah_aktifitas);
        };

        $warna = ['#836AF9','#ffe800','#28dac6','#FF8132','#ffcf5c','#299AFF','#4F5D70','#EDF1F4','#2B9AFF','#84D0FF','#FF6384','#4BC0C0','#FF9F40','#B9FF00','#00FFB9','#FF00B9','#B900FF','#4B00FF','#FFC107','#FF5722'];

        return view('home.dashboard-aktifitas-sales', compact('jenisVisit','jumlahAktifitasVisit','statusLeads','jumlahAktifitasStatusLeads','aktifitasSalesByTipePerTanggal','aktifitasSalesPerTanggal','warna','tipe','jumlahAktifitasTipe','jumlahAktifitas','sales','aktifitasSalesHariIni','aktifitasSalesMingguIni','aktifitasSalesBulanIni','aktifitasSalesTahunIni'));
    }

    public function dashboardLeads (Request $request){
        $leadsBaruHariIni = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->count();

        $leadsBaruMingguIni = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $leadsBaruBulanIni = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $leadsBaruTahunIni = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $leadsBelumAdaAktifitas = DB::table('sl_leads')
            ->leftJoin('sl_customer_activity', 'sl_leads.id', '=', 'sl_customer_activity.leads_id')
            ->whereNull('sl_leads.deleted_at')
            ->whereNull('sl_customer_activity.id')
            ->count();

        $leadsBelumAdaCustomer = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereNull('customer_id')
            ->count();

        $leadsBelumAdaSales = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereNull('tim_sales_d_id')
            ->count();
        
        $leadsBelumAdaQuotation = DB::table('sl_leads')
            ->leftJoin('sl_quotation', 'sl_leads.id', '=', 'sl_quotation.leads_id')
            ->whereNull('sl_leads.deleted_at')
            ->whereNull('sl_quotation.id')
            ->count();

        $leadsByKebutuhan = DB::table('sl_leads')
            ->leftJoin('m_kebutuhan', 'sl_leads.kebutuhan_id', '=', 'm_kebutuhan.id')
            ->whereNull('sl_leads.deleted_at')
            // ->whereNotNull('sl_leads.kebutuhan_id')
            // ->where('sl_leads.kebutuhan_id', '!=', 99)
            ->whereYear('sl_leads.created_at', Carbon::now()->year)
            ->select('m_kebutuhan.nama as kebutuhan','sl_leads.kebutuhan_id', DB::raw('MONTH(sl_leads.created_at) as bulan'), DB::raw('count(*) as jumlah_leads'))
            ->groupBy('sl_leads.kebutuhan_id','kebutuhan', 'bulan')
            ->get();

        $leadsGroupedByKebutuhan = [];

        foreach ($leadsByKebutuhan as $lead) {
            if (!isset($leadsGroupedByKebutuhan[$lead->kebutuhan_id])) {
                $leadsGroupedByKebutuhan[$lead->kebutuhan_id] = [
                    'kebutuhan' => $lead->kebutuhan,
                    'kebutuhan_id' => $lead->kebutuhan_id,
                    'jumlah_leads' => array_fill(1, 12, ['bulan' => 0, 'leads' => 0])
                ];
            }
            $leadsGroupedByKebutuhan[$lead->kebutuhan_id]['jumlah_leads'][$lead->bulan] = [
                'bulan' => $lead->bulan,
                'leads' => $lead->jumlah_leads
            ];
        }

        $leadsGroupedByKebutuhan = array_values($leadsGroupedByKebutuhan);

        $leadsBySumber = DB::table('sl_leads')
            ->leftJoin('m_platform', 'sl_leads.platform_id', '=', 'm_platform.id')
            ->whereNull('sl_leads.deleted_at')
            ->whereNotNull('m_platform.id')
            ->select('m_platform.nama as platform',DB::raw('count(*) as jumlah_leads'))
            ->groupBy('platform')
            ->get();

        $leadsWithCustomer = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereNotNull('customer_id')
            ->count();

        $leadsWithoutCustomer = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereNull('customer_id')
            ->count();

        $leadsGroupKebutuhan = DB::table('sl_leads')
            ->leftJoin('m_kebutuhan', 'sl_leads.kebutuhan_id', '=', 'm_kebutuhan.id')
            ->whereNull('sl_leads.deleted_at')
            ->whereNotNull('m_kebutuhan.id')
            ->select('m_kebutuhan.nama as kebutuhan',DB::raw('count(*) as jumlah_leads'))
            ->groupBy('kebutuhan')
            ->get();
        $totalLeadsKebutuhan = DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->whereNotNull('kebutuhan_id')
            ->count();

        $warna = ['#836AF9','#ffe800','#28dac6','#FF8132','#ffcf5c','#299AFF','#4F5D70','#EDF1F4','#2B9AFF','#84D0FF','#FF6384','#4BC0C0','#FF9F40','#B9FF00','#00FFB9','#FF00B9','#B900FF','#4B00FF','#FFC107','#FF5722'];
        return view('home.dashboard-leads',compact('totalLeadsKebutuhan','leadsGroupKebutuhan','leadsWithoutCustomer','leadsWithCustomer','leadsBySumber','leadsGroupedByKebutuhan','leadsBelumAdaQuotation','leadsBelumAdaSales','leadsBelumAdaCustomer','leadsBelumAdaAktifitas','warna','leadsBaruHariIni','leadsBaruMingguIni','leadsBaruBulanIni','leadsBaruTahunIni'));
    }

    public function getListDashboardApprovalData(Request $request) {
        $arrData = [];

        $data = DB::table('sl_quotation')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
        ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.step','sl_quotation.top','sl_quotation.ot3','sl_quotation.ot2','sl_quotation.ot1','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation',
        DB::raw('(SELECT GROUP_CONCAT(nama_site SEPARATOR "<br /> ") 
                    FROM sl_quotation_site 
                    WHERE sl_quotation_site.quotation_id = sl_quotation.id) as nama_site')
        )
        ->whereNull('sl_leads.deleted_at')
        ->whereNull('sl_quotation.deleted_at')
        ->where('sl_quotation.is_aktif',0)->get();

        foreach ($data as $key => $value) {
            $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_quotation)->isoFormat('D MMMM Y');
        }
        
        if($request->tipe =="menunggu-anda"){
            foreach ($data as $key => $quotation) {    
                if ($quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot1 == null) {
                    if(Auth::user()->role_id==96){
                        array_push($arrData,$quotation);
                    }
                }else if($quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari"){
                    if(Auth::user()->role_id==97 || Auth::user()->role_id==40 ){
                        array_push($arrData,$quotation);
                    }
                }else if ( $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari" ){
                    if(Auth::user()->role_id==99){
                        array_push($arrData,$quotation);
                    }
                }
            }
        }else if($request->tipe =="menunggu-approval"){
            foreach ($data as $key => $quotation) {
                if ($quotation->step == 100 && $quotation->is_aktif==0){
                    array_push($arrData,$quotation);
                }
            }
        }else if($request->tipe =="quotation-belum-lengkap"){
            foreach ($data as $key => $quotation) {    
                if ($quotation->step != 100 && $quotation->is_aktif==0){
                    array_push($arrData,$quotation);
                }
            }
        };

        return DataTables::of($arrData)
            ->addColumn('aksi', function ($data) {
               return "";
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

    public function getListDashboardAktifkanData(Request $request) {
        $arrData = [];

        $data = DB::table('sl_pks')
        ->leftJoin('sl_quotation','sl_quotation.id','sl_pks.quotation_id')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
        ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_pks.nomor','sl_quotation.step','sl_quotation.top','sl_quotation.ot3','sl_quotation.ot2','sl_quotation.ot1','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_pks.id','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation',
        DB::raw('(SELECT GROUP_CONCAT(nama_site SEPARATOR "<br /> ") 
                    FROM sl_quotation_site 
                    WHERE sl_quotation_site.quotation_id = sl_quotation.id) as nama_site')
        )
        ->whereNull('sl_leads.deleted_at')
        ->whereNull('sl_quotation.deleted_at')
        ->whereNull('sl_pks.deleted_at')
        ->where('sl_pks.status_pks_id',6)->get();

        foreach ($data as $key => $value) {
            $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_quotation)->isoFormat('D MMMM Y');
        }


        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->editColumn('nomor', function ($data) {
                $ref = route('pks.view',$data->id);
                return '<a href="'.$ref.'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
            })
            ->editColumn('nama_perusahaan', function ($data) {
                return '<a href="'.route('leads.view',$data->leads_id).'" style="font-weight:bold;color:#000056">'.$data->nama_perusahaan.'</a>';
            })
            ->rawColumns(['aksi','nomor','nama_perusahaan','nama_site'])
            ->make(true);
    }

    public function dashboardGeneral (Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $branches = DB::connection('mysqlhris')->table('m_branch')->pluck('name');

        $leadsByBranch = $branches->map(function ($branch) use ($db2) {
            $jumlahLeads = DB::table('sl_leads')
                ->leftJoin($db2.'.m_branch', 'sl_leads.branch_id', '=', $db2.'.m_branch.id')
                ->whereNull('sl_leads.deleted_at')
                ->whereNull('sl_leads.customer_id')
                ->whereMonth('sl_leads.created_at', Carbon::now()->month)
                ->where($db2.'.m_branch.name', $branch)
                ->count();

            return (object) [
                'branch' => $branch,
                'jumlah_leads' => $jumlahLeads
            ];
        });

        $kebutuhanList = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
        $leadsByBranchAndKebutuhan = $branches->map(function ($branch) use ($db2) {
            $kebutuhanIds = DB::table('m_kebutuhan')->pluck('id');
            $data = $kebutuhanIds->map(function ($kebutuhanId) use ($branch, $db2) {
                $jumlahLeads = DB::table('sl_leads')
                    ->leftJoin($db2.'.m_branch', 'sl_leads.branch_id', '=', $db2.'.m_branch.id')
                    ->whereNull('sl_leads.deleted_at')
                    ->where('sl_leads.kebutuhan_id', $kebutuhanId)
                    ->where($db2.'.m_branch.name', $branch)
                    ->whereMonth('sl_leads.created_at', Carbon::now()->month)
                    ->count();

                $kebutuhan = DB::table('m_kebutuhan')->where('id', $kebutuhanId)->value('nama');

                return (object) [
                    'kebutuhan' => $kebutuhan,
                    'jumlah_leads' => $jumlahLeads
                ];
            });

            return (object) [
                'branch' => $branch,
                'data' => $data
            ];
        });
        
        $branchesWithCustomerData = $branches->map(function ($branch) use ($db2) {
            $branchId = DB::connection('mysqlhris')->table('m_branch')->where('name', $branch)->value('id');
            $target = DB::connection('mysqlhris')->table('m_branch')->where('id', $branchId)->value('sales_target');
            $actual = DB::table('sl_customer')
                ->leftJoin('sl_leads', 'sl_customer.leads_id', '=', 'sl_leads.id')
                ->whereNull('sl_customer.deleted_at')
                ->where('sl_leads.branch_id', $branchId)
                ->count();

            return (object) [
                'branch' => $branch,
                'data' => (object) [
                    'target' => $target,
                    'actual' => $actual
                ]
            ];
        });

        $warna = ['#836AF9','#ffe800','#28dac6','#FF8132','#ffcf5c','#299AFF','#4F5D70','#EDF1F4','#2B9AFF','#84D0FF','#FF6384','#4BC0C0','#FF9F40','#B9FF00','#00FFB9','#FF00B9','#B900FF','#4B00FF','#FFC107','#FF5722'];
        return view('home.dashboard-general',compact('branchesWithCustomerData','kebutuhanList','leadsByBranchAndKebutuhan','leadsByBranch','warna'));
    }

}

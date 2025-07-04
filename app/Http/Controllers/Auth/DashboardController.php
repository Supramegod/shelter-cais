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
                if($quotation->ot1 != null && $quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari"){
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
                // if(Auth::user()->role_id==99){
                //     array_push($dataMenungguAnda,$quotation);
                // }
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

    public function dashboardSdtTraining(Request $request){
        $jumlahTraining = DB::table('sdt_training')
            ->where('is_aktif', 1)
            ->count();

        $jumlahClient = DB::table('m_training_client')
            ->where('is_aktif', 1)
            ->count();

        $jumlahTrainer = DB::table('m_training_trainer')
            ->where('is_aktif', 1)
            ->count();

        $jumlahMateri = DB::table('m_training_materi')
            ->where('is_aktif', 1)
            ->count();


        return view('home.dashboard-sdt-training',compact('jumlahTraining','jumlahClient','jumlahTrainer','jumlahMateri'));
    }

    public function dashboardAktifitasSales(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $cabangList = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();


        $aktifitasSalesHariIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->whereDate('sl_customer_activity.created_at', Carbon::today()->toDateString())

            ->count();

            // dd(Carbon::now()->endOfWeek()->format('Y-m-d'));
        $aktifitasSalesMingguIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->whereBetween('sl_customer_activity.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $aktifitasSalesBulanIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->count();

        $aktifitasSalesTahunIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereYear('sl_customer_activity.created_at', Carbon::now()->year)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->count();

        $aktifitasSalesUserIds = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->whereNotNull('sl_customer_activity.user_id')
            ->select('sl_customer_activity.user_id', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('sl_customer_activity.user_id')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
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
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->select('sl_customer_activity.tipe', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('sl_customer_activity.tipe')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->get();

        $tipe = [];
        $jumlahAktifitasTipe = [];
        foreach ($aktifitasByTipe as $key => $value) {
            array_push($tipe,$value->tipe." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasTipe,$value->jumlah_aktifitas);
        };

        $aktifitasByStatusLeads = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->select(DB::raw('(select nama from m_status_leads where id=sl_customer_activity.status_leads_id) as status_leads'), DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('status_leads')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
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
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereNotNull('sl_customer_activity.user_id')
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->select(DB::raw('DAY(sl_customer_activity.created_at) as tanggal'), 'sl_customer_activity.user_id', DB::raw('count(*) as jumlah_aktifitas'))
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
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->select('sl_customer_activity.tipe', DB::raw('DAY(sl_customer_activity.created_at) as tanggal'), DB::raw('count(*) as jumlah_aktifitas'))
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
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->whereNotNull('sl_customer_activity.jenis_visit')
            ->select('sl_customer_activity.jenis_visit', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('sl_customer_activity.jenis_visit')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->get();

        $jenisVisit = [];
        $jumlahAktifitasVisit = [];
        foreach ($aktifitasByVisit as $key => $value) {
            array_push($jenisVisit,$value->jenis_visit." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasVisit,$value->jumlah_aktifitas);
        };

        $warna = ['#836AF9','#ffe800','#28dac6','#FF8132','#ffcf5c','#299AFF','#4F5D70','#EDF1F4','#2B9AFF','#84D0FF','#FF6384','#4BC0C0','#FF9F40','#B9FF00','#00FFB9','#FF00B9','#B900FF','#4B00FF','#FFC107','#FF5722'];

        return view('home.dashboard-aktifitas-sales', compact('cabangList','jenisVisit','jumlahAktifitasVisit','statusLeads','jumlahAktifitasStatusLeads','aktifitasSalesByTipePerTanggal','aktifitasSalesPerTanggal','warna','tipe','jumlahAktifitasTipe','jumlahAktifitas','sales','aktifitasSalesHariIni','aktifitasSalesMingguIni','aktifitasSalesBulanIni','aktifitasSalesTahunIni'));
    }

    public function dashboardAktifitasTelesales(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $cabangList = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();

        $aktifitasTelesalesHariIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->whereDate('sl_customer_activity.created_at', Carbon::today()->toDateString())

            ->count();

            // dd(Carbon::now()->endOfWeek()->format('Y-m-d'));
        $aktifitasTelesalesMingguIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->whereBetween('sl_customer_activity.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $aktifitasTelesalesBulanIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->count();

        $aktifitasTelesalesTahunIni = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereYear('sl_customer_activity.created_at', Carbon::now()->year)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->count();

        $aktifitasTelesalesUserIds = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->whereNotNull('sl_customer_activity.user_id')
            ->select('sl_customer_activity.user_id', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('sl_customer_activity.user_id')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->get();

        $telesales = [];
        $jumlahAktifitas = [];
        foreach ($aktifitasTelesalesUserIds as $key => $value) {
            $user = DB::connection('mysqlhris')->table('m_user')->where('id',$value->user_id)->first();
            if($user==null){
                continue;
            }
            array_push($telesales,$user->full_name." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitas,$value->jumlah_aktifitas);
        }

        $aktifitasByTipe = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->select('sl_customer_activity.tipe', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('sl_customer_activity.tipe')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->get();

        $tipe = [];
        $jumlahAktifitasTipe = [];
        foreach ($aktifitasByTipe as $key => $value) {
            array_push($tipe,$value->tipe." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasTipe,$value->jumlah_aktifitas);
        };

        $aktifitasByStatusLeads = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->select(DB::raw('(select nama from m_status_leads where id=sl_customer_activity.status_leads_id) as status_leads'), DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('status_leads')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->get();

        $statusLeads = [];
        $jumlahAktifitasStatusLeads = [];
        foreach ($aktifitasByStatusLeads as $key => $value) {
            array_push($statusLeads,$value->status_leads." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasStatusLeads,$value->jumlah_aktifitas);
        };

        $aktifitasTelesalesPerTanggal = [];

        for ($i = 1; $i <= 31; $i++) {
            $aktifitasTelesalesPerTanggal[$i] = 0;
        }

        $aktifitasTelesales = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereNotNull('sl_customer_activity.user_id')
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->select(DB::raw('DAY(sl_customer_activity.created_at) as tanggal'), 'sl_customer_activity.user_id', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('tanggal', 'user_id')
            ->get();

        $aktifitasTelesalesUser = [];

        foreach ($aktifitasTelesales as $aktifitas) {
            if (!isset($aktifitasTelesalesUser[$aktifitas->user_id])) {
                $aktifitasTelesalesUser[$aktifitas->user_id] = [
                    'user' => DB::connection('mysqlhris')->table('m_user')->where('id', $aktifitas->user_id)->value('full_name'),
                    'jumlah_aktifitas' => []
                ];
            }

            $aktifitasTelesalesUser[$aktifitas->user_id]['jumlah_aktifitas'][$aktifitas->tanggal] = $aktifitas->jumlah_aktifitas;
        }

        foreach ($aktifitasTelesalesUser as &$userAktifitas) {
            for ($i = 1; $i <= 31; $i++) {
                if (!isset($userAktifitas['jumlah_aktifitas'][$i])) {
                    $userAktifitas['jumlah_aktifitas'][$i] = 0;
                }
            }
            ksort($userAktifitas['jumlah_aktifitas']);
        }

        $aktifitasTelesalesPerTanggal = array_values($aktifitasTelesalesUser);

        $aktifitasTelesalesByTipePerTanggal = [];

        $aktifitasTelesalesByTipe = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->select('sl_customer_activity.tipe', DB::raw('DAY(sl_customer_activity.created_at) as tanggal'), DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('tipe', 'tanggal')
            ->get();

        foreach ($aktifitasTelesalesByTipe as $aktifitas) {
            if (!isset($aktifitasTelesalesByTipePerTanggal[$aktifitas->tipe])) {
                $aktifitasTelesalesByTipePerTanggal[$aktifitas->tipe] = [
                    'tipe' => $aktifitas->tipe,
                    'jumlah_aktifitas' => []
                ];
            }

            $aktifitasTelesalesByTipePerTanggal[$aktifitas->tipe]['jumlah_aktifitas'][$aktifitas->tanggal] = $aktifitas->jumlah_aktifitas;
        }

        foreach ($aktifitasTelesalesByTipePerTanggal as &$tipeAktifitas) {
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

        $aktifitasTelesalesByTipePerTanggal = array_values($aktifitasTelesalesByTipePerTanggal);

        $aktifitasByVisit = DB::table('sl_customer_activity')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->whereNotNull('sl_customer_activity.jenis_visit')
            ->select('sl_customer_activity.jenis_visit', DB::raw('count(*) as jumlah_aktifitas'))
            ->groupBy('sl_customer_activity.jenis_visit')
            ->where('sl_customer_activity.is_activity', 1)
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->get();

        $jenisVisit = [];
        $jumlahAktifitasVisit = [];
        foreach ($aktifitasByVisit as $key => $value) {
            array_push($jenisVisit,$value->jenis_visit." ( ".$value->jumlah_aktifitas." )");
            array_push($jumlahAktifitasVisit,$value->jumlah_aktifitas);
        };

        $warna = ['#836AF9','#ffe800','#28dac6','#FF8132','#ffcf5c','#299AFF','#4F5D70','#EDF1F4','#2B9AFF','#84D0FF','#FF6384','#4BC0C0','#FF9F40','#B9FF00','#00FFB9','#FF00B9','#B900FF','#4B00FF','#FFC107','#FF5722'];

        return view('home.dashboard-aktifitas-telesales', compact('cabangList','jenisVisit','jumlahAktifitasVisit','statusLeads','jumlahAktifitasStatusLeads','aktifitasTelesalesByTipePerTanggal','aktifitasTelesalesPerTanggal','warna','tipe','jumlahAktifitasTipe','jumlahAktifitas','telesales','aktifitasTelesalesHariIni','aktifitasTelesalesMingguIni','aktifitasTelesalesBulanIni','aktifitasTelesalesTahunIni'));
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
                }
                // else if ( $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari" ){
                //     if(Auth::user()->role_id==99){
                //         array_push($arrData,$quotation);
                //     }
                // }
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


    public function listAktifitasSalesHariIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->whereDate('sl_customer_activity.created_at', Carbon::today()->toDateString())
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasSalesMingguIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->whereBetween('sl_customer_activity.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasSalesBulanIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasSalesTahunIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->whereYear('sl_customer_activity.created_at', Carbon::now()->year)
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasTelesalesHariIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->whereDate('sl_customer_activity.created_at', Carbon::today()->toDateString())
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasTelesalesMingguIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->whereBetween('sl_customer_activity.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasTelesalesBulanIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->whereMonth('sl_customer_activity.created_at', Carbon::now()->month)
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listAktifitasTelesalesTahunIni(Request $request) {
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];
        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('is_activity',1)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->whereYear('sl_customer_activity.created_at', Carbon::now()->year)
            ->select('sl_customer_activity.id','sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function pivotAktifitasSales (Request $request){
        $tanggalDari = $request->tanggalDari;
        $tanggalSampai = $request->tanggalSampai;

        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table('sl_customer_activity')
        ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
        ->join($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
        ->leftJoin('sl_quotation', 'sl_quotation.id', '=', 'sl_customer_activity.quotation_id')
        ->leftJoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
        ->leftJoin('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
        ->whereNull('sl_customer_activity.deleted_at')
        ->where('is_activity',1)
        ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
        ->whereBetween('sl_customer_activity.tgl_activity', [$tanggalDari, $tanggalSampai])
        ->select('sl_customer_activity.tgl_activity as Tanggal','sl_customer_activity.tipe as Tipe',$db2.'.m_user.full_name as User',$db2.'.m_branch.name as Branch','m_status_leads.nama as Status_Leads','sl_quotation.nomor as Nomor_Quotation','sl_leads.nama_perusahaan as Nama_Perusahaan')
        ->get();

        foreach ($data as $key => $value) {
            $value->Bulan = Carbon::createFromFormat('Y-m-d', $value->Tanggal)->isoFormat('MMMM');
            $value->Tahun = Carbon::createFromFormat('Y-m-d', $value->Tanggal)->year;
            $value->Tanggal = Carbon::createFromFormat('Y-m-d',$value->Tanggal)->isoFormat('D MMMM Y');
        }

        return $data;
    }
    public function pivotAktifitasTelesales (Request $request){
        $tanggalDari = $request->tanggalDari;
        $tanggalSampai = $request->tanggalSampai;

        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $data = DB::table('sl_customer_activity')
        ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
        ->join($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
        ->leftJoin('sl_quotation', 'sl_quotation.id', '=', 'sl_customer_activity.quotation_id')
        ->leftJoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
        ->leftJoin('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
        ->whereNull('sl_customer_activity.deleted_at')
        ->where('is_activity',1)
        ->whereIn($db2.'.m_user.role_id', [30])
        ->whereBetween('sl_customer_activity.tgl_activity', [$tanggalDari, $tanggalSampai])
        ->select('sl_customer_activity.tgl_activity as Tanggal','sl_customer_activity.tipe as Tipe',$db2.'.m_user.full_name as User',$db2.'.m_branch.name as Branch','m_status_leads.nama as Status_Leads','sl_quotation.nomor as Nomor_Quotation','sl_leads.nama_perusahaan as Nama_Perusahaan')
        ->get();

        foreach ($data as $key => $value) {
            $value->Bulan = Carbon::createFromFormat('Y-m-d', $value->Tanggal)->isoFormat('MMMM');
            $value->Tahun = Carbon::createFromFormat('Y-m-d', $value->Tanggal)->year;
            $value->Tanggal = Carbon::createFromFormat('Y-m-d',$value->Tanggal)->isoFormat('D MMMM Y');
        }

        return $data;
    }

    public function laporanMingguanSales(Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $branch_id = $request->branch_id;

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->leftjoin($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
            ->leftjoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
            ->select(
                $db2.'.m_user.id as user_id',
                $db2.'.m_user.full_name as nama_sales',
                $db2.'.m_branch.name as cabang',
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 1 AND sl_customer_activity.tipe != "Visit" and sl_customer_activity.notes like "%visit%" THEN 1 ELSE 0 END) as w1_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 1 AND sl_customer_activity.tipe = "visit" THEN 1 ELSE 0 END) as w1_visit'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 1 AND sl_customer_activity.notes like "%Quotation%terbentuk%" THEN 1 ELSE 0 END) as w1_quot'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 1 AND sl_customer_activity.notes like "%spk%terbentuk%" THEN 1 ELSE 0 END) as w1_spk'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 2 AND sl_customer_activity.tipe != "Visit" and sl_customer_activity.notes like "%visit%" THEN 1 ELSE 0 END) as w2_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 2 AND sl_customer_activity.tipe = "visit" THEN 1 ELSE 0 END) as w2_visit'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 2 AND sl_customer_activity.notes like "%Quotation%terbentuk%" THEN 1 ELSE 0 END) as w2_quot'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 2 AND sl_customer_activity.notes like "%spk%terbentuk%" THEN 1 ELSE 0 END) as w2_spk'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 3 AND sl_customer_activity.tipe != "Visit" and sl_customer_activity.notes like "%visit%" THEN 1 ELSE 0 END) as w3_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 3 AND sl_customer_activity.tipe = "visit" THEN 1 ELSE 0 END) as w3_visit'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 3 AND sl_customer_activity.notes like "%Quotation%terbentuk%" THEN 1 ELSE 0 END) as w3_quot'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 3 AND sl_customer_activity.notes like "%spk%terbentuk%" THEN 1 ELSE 0 END) as w3_spk'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 4 AND sl_customer_activity.tipe != "Visit" and sl_customer_activity.notes like "%visit%" THEN 1 ELSE 0 END) as w4_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 4 AND sl_customer_activity.tipe = "visit" THEN 1 ELSE 0 END) as w4_visit'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 4 AND sl_customer_activity.notes like "%Quotation%terbentuk%" THEN 1 ELSE 0 END) as w4_quot'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 4 AND sl_customer_activity.notes like "%spk%terbentuk%" THEN 1 ELSE 0 END) as w4_spk')
            )
            ->whereMonth('sl_customer_activity.created_at', $bulan)
            ->whereYear('sl_customer_activity.created_at', $tahun)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->groupBy($db2.'.m_user.id',$db2.'.m_user.full_name', $db2.'.m_branch.name')
            ->orderBy($db2.'.m_branch.name', 'asc')
            ->orderBy($db2.'.m_user.full_name', 'asc');

        if($branch_id != ""){
            $data = $data->where($db2.'.m_branch.id', $branch_id);
        }
        $data = $data->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }
        return DataTables::of($data)
            ->make(true);
    }

    public function laporanBulananSales(Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $branch_id = $request->branch_id;

        $tahunLalu = $tahun;
        $bulanLalu = $bulan - 1;
        if($bulanLalu == 0){
            $bulanLalu = 12;
            $tahunLalu = $tahunLalu - 1;
        }

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->leftjoin($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
            ->leftjoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
            ->select(
                $db2.'.m_user.id as user_id',
                $db2.'.m_branch.id as branch_id',
                $db2.'.m_user.full_name as nama_sales',
                $db2.'.m_branch.name as cabang',
                DB::raw('SUM(
        CASE
            WHEN sl_customer_activity.tipe != "Visit" AND (
                 sl_customer_activity.notes LIKE "%visit%"
                 OR sl_customer_activity.notes LIKE "%appointment%"
								 OR sl_customer_activity.notes LIKE "%meeting%"
								 OR sl_customer_activity.notes LIKE "%zooom%"
             )
            THEN 1
            ELSE 0
        END
    ) AS jumlah_appt'),
                DB::raw('SUM(CASE WHEN sl_customer_activity.tipe = "visit" THEN 1 ELSE 0 END) as jumlah_visit'),
                DB::raw('SUM(CASE WHEN sl_customer_activity.notes like "%Quotation%terbentuk%" THEN 1 ELSE 0 END) as jumlah_quot'),
                DB::raw('SUM(CASE WHEN sl_customer_activity.notes like "%spk%terbentuk%" THEN 1 ELSE 0 END) as jumlah_spk'),
            )

            ->whereMonth('sl_customer_activity.created_at', $bulan)
            ->whereYear('sl_customer_activity.created_at', $tahun)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->groupBy($db2.'.m_user.full_name',$db2.'.m_user.id', $db2.'.m_branch.name', $db2.'.m_branch.id')
            ->orderBy($db2.'.m_branch.name', 'asc')
            ->orderBy($db2.'.m_user.full_name', 'asc');

        if($branch_id != ""){
            $data = $data->where($db2.'.m_branch.id', $branch_id);
        }
        $data = $data->get();

        $dataLalu = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->leftjoin($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
            ->leftjoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
            ->select(
                $db2.'.m_user.full_name as nama_sales',
                $db2.'.m_branch.name as cabang',
                DB::raw('SUM(CASE WHEN sl_customer_activity.tipe != "Visit" and sl_customer_activity.notes like "%visit%" THEN 1 ELSE 0 END) as jumlah_appt'),
                DB::raw('SUM(CASE WHEN sl_customer_activity.tipe = "visit" THEN 1 ELSE 0 END) as jumlah_visit'),
                DB::raw('SUM(CASE WHEN sl_customer_activity.notes like "%Quotation%terbentuk%" THEN 1 ELSE 0 END) as jumlah_quot'),
                DB::raw('SUM(CASE WHEN sl_customer_activity.notes like "%spk%terbentuk%" THEN 1 ELSE 0 END) as jumlah_spk'),
            )

            ->whereMonth('sl_customer_activity.created_at', $bulanLalu)
            ->whereYear('sl_customer_activity.created_at', $tahunLalu)
            ->whereIn($db2.'.m_user.role_id', [29, 31, 32, 33, 50])
            ->groupBy($db2.'.m_user.full_name', $db2.'.m_branch.name')
            ->orderBy($db2.'.m_branch.name', 'asc')
            ->orderBy($db2.'.m_user.full_name', 'asc');
        if($branch_id != ""){
            $dataLalu = $dataLalu->where($db2.'.m_branch.id', $branch_id);
        }
        $dataLalu = $dataLalu->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
            $value->persen_appt_to_visit = ($value->jumlah_visit == 0 ? 0 : round(($value->jumlah_appt / $value->jumlah_visit) * 100, 2))."%";
            $value->persen_visit_to_quot = ($value->jumlah_visit == 0 ? 0 : round(($value->jumlah_quot / $value->jumlah_visit) * 100, 2))."%";
            $value->persen_quot_to_spk = ($value->jumlah_quot == 0 ? 0 : round(($value->jumlah_spk / $value->jumlah_quot) * 100, 2))."%";
            $value->jumlah_aktual_spk = 0;

            // cari data bulan lalu
            //inisial data dulu
            $value->jumlah_appt_lalu = 0;
            $value->jumlah_visit_lalu = 0;
            $value->jumlah_quot_lalu = 0;
            $value->jumlah_spk_lalu = 0;
            $value->persen_appt_to_visit_lalu = 0;
            $value->persen_visit_to_quot_lalu = 0;
            $value->persen_quot_to_spk_lalu = 0;
            $value->jumlah_aktual_spk_lalu = 0;

            foreach ($dataLalu as $key2 => $value2) {
                if($value->nama_sales == $value2->nama_sales && $value->cabang == $value2->cabang){
                    $value->jumlah_appt_lalu = $value2->jumlah_appt;
                    $value->jumlah_visit_lalu = $value2->jumlah_visit;
                    $value->jumlah_quot_lalu = $value2->jumlah_quot;
                    $value->jumlah_spk_lalu = $value2->jumlah_spk;
                    $value->persen_appt_to_visit_lalu = ($value2->jumlah_visit == 0 ? 0 : round(($value2->jumlah_appt / $value2->jumlah_visit) * 100, 2))."%";
                    $value->persen_visit_to_quot_lalu = ($value2->jumlah_visit == 0 ? 0 : round(($value2->jumlah_quot / $value2->jumlah_visit) * 100, 2))."%";
                    $value->persen_quot_to_spk_lalu = ($value2->jumlah_quot == 0 ? 0 : round(($value2->jumlah_spk / $value2->jumlah_quot) * 100, 2))."%";
                    $value->jumlah_aktual_spk_lalu = 0;
                    break;
                }
            }
        }
        return DataTables::of($data)
            ->make(true);
    }

    public function laporanMingguanTelesales(Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $branch_id = $request->branch_id;

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->leftjoin($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
            ->leftjoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
            ->select(
                $db2.'.m_user.full_name as nama_sales',
                $db2.'.m_branch.name as cabang',
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 1 AND sl_customer_activity.tipe != "Visit" and (sl_customer_activity.notes LIKE "%visit%" OR sl_customer_activity.notes LIKE "%appo%") THEN 1 ELSE 0 END) as w1_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 2 AND sl_customer_activity.tipe != "Visit" and (sl_customer_activity.notes LIKE "%visit%" OR sl_customer_activity.notes LIKE "%appo%") THEN 1 ELSE 0 END) as w2_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 3 AND sl_customer_activity.tipe != "Visit" and (sl_customer_activity.notes LIKE "%visit%" OR sl_customer_activity.notes LIKE "%appo%") THEN 1 ELSE 0 END) as w3_appt'),
                DB::raw('SUM(CASE WHEN GetWeekOfMonth(sl_customer_activity.created_at) = 4 AND sl_customer_activity.tipe != "Visit" and (sl_customer_activity.notes LIKE "%visit%" OR sl_customer_activity.notes LIKE "%appo%") THEN 1 ELSE 0 END) as w4_appt'),
            )
            ->whereMonth('sl_customer_activity.created_at', $bulan)
            ->whereYear('sl_customer_activity.created_at', $tahun)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->groupBy($db2.'.m_user.full_name', $db2.'.m_branch.name')
            ->orderBy($db2.'.m_branch.name', 'asc')
            ->orderBy($db2.'.m_user.full_name', 'asc');

        if($branch_id != ""){
            $data = $data->where($db2.'.m_branch.id', $branch_id);
        }
        $data = $data->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }
        return DataTables::of($data)
            ->make(true);
    }

    public function laporanBulananTelesales(Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $branch_id = $request->branch_id;

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->leftjoin($db2.'.m_user', 'sl_customer_activity.user_id', '=', $db2.'.m_user.id')
            ->leftjoin($db2.'.m_branch', $db2.'.m_user.branch_id', '=', $db2.'.m_branch.id')
            ->select(
                $db2.'.m_user.full_name as nama_sales',
                $db2.'.m_branch.name as cabang',
                DB::raw('
                SUM(
        CASE
            WHEN sl_customer_activity.tipe != "Visit" AND (
                 sl_customer_activity.notes LIKE "%visit%"
                 OR sl_customer_activity.notes LIKE "%appointment%"
								 OR sl_customer_activity.notes LIKE "%meeting%"
								 OR sl_customer_activity.notes LIKE "%zoom%"
             )
            THEN 1
            ELSE 0
        END
    ) AS jumlah_appt'),
            )
            ->whereMonth('sl_customer_activity.created_at', $bulan)
            ->whereYear('sl_customer_activity.created_at', $tahun)
            ->whereIn($db2.'.m_user.role_id', [30])
            ->groupBy($db2.'.m_user.full_name', $db2.'.m_branch.name')
            ->orderBy($db2.'.m_branch.name', 'asc')
            ->orderBy($db2.'.m_user.full_name', 'asc');

        if($branch_id != ""){
            $data = $data->where($db2.'.m_branch.id', $branch_id);
        }
        $data = $data->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }
        return DataTables::of($data)
            ->make(true);
    }

    public function listAktifitasSalesBulananDetail(Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();
        $arrData = [];
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = DB::table('sl_customer_activity')
            ->join('sl_leads', 'sl_customer_activity.leads_id', '=', 'sl_leads.id')
            ->join($db2.'.m_user',$db2.'.m_user.id','sl_customer_activity.user_id')
            ->whereNull('sl_customer_activity.deleted_at')
            ->where('sl_customer_activity.is_activity',1)
            ->whereMonth('sl_customer_activity.created_at', $bulan)
            ->whereYear('sl_customer_activity.created_at', $tahun)
            ->where($db2.'.m_user.id', $request->user_id)
            ->select('sl_customer_activity.tgl_activity','sl_customer_activity.nomor','sl_leads.nama_perusahaan','sl_customer_activity.tipe','sl_customer_activity.notes','sl_customer_activity.created_by','sl_customer_activity.created_at')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function dashboardManagerCrm(Request $request)
    {
        return view('home.dashboard-manager-crm');
    }
    public function listPksSiapAktif(Request $request){
        $listPksAktif = \DB::table('sl_site')
            ->join('sl_pks', 'sl_site.pks_id', '=', 'sl_pks.id')
            ->whereNull('sl_site.deleted_at')
            ->whereNull('sl_pks.deleted_at')
            ->where('sl_pks.status_pks_id', 6)
            ->whereNotNull('sl_site.quotation_id')
            ->whereNotIn('sl_site.id', function($query) {
            $query->select('site_id')
                ->from('shelter3_hris.m_site')
                ->whereNotNull('site_id')
                ->where('is_active', 1);
            })
            ->select(
            'sl_site.id',
            'sl_pks.id as pks_id',
            'sl_site.nomor',
            'sl_site.nama_site',
            'sl_site.provinsi',
            'sl_site.kota',
            'sl_site.kebutuhan',
            'sl_pks.nomor as nomor_pks'
            )
            ->get();

        return DataTables::of($listPksAktif)
            ->addColumn('check', function ($data) {
            return '<input type="checkbox" class="check-site" value="'.$data->id.'">';
            })
            ->rawColumns(['check'])
            ->make(true);
    }
}

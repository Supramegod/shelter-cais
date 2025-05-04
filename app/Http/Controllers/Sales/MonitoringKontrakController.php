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
use App\Exports\MonitoringKontrakTemplateExport;
use \stdClass;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Helper\QuotationService;

class MonitoringKontrakController extends Controller
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

        return view('sales.monitoring-kontrak.list',compact('tglDari','tglSampai'));
    }
    public function indexTerminate (Request $request){
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

        return view('sales.monitoring-kontrak.list-terminate',compact('tglDari','tglSampai'));
    }

    public function view (Request $request,$pksId){
        try {
            $pks = DB::table('sl_pks')->where('id',$pksId)->first();

            $data = DB::table('sl_customer_activity')->whereNull('deleted_at')->where('pks_id',$pksId)->where('is_activity',1)->orderBy('created_at','desc')->get();
            foreach ($data as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y HH:mm');
                $value->stgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
            }

            $leads = DB::table('sl_leads')->where('id',$pks->leads_id)->first();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->where('id',$pks->jenis_perusahaan_id)->first();
            if($jenisPerusahaan !=null){
                $pks->jenis_perusahaan = $jenisPerusahaan->nama;
            }else{
                $pks->jenis_perusahaan = "";
            }

            $quotation = DB::table('sl_quotation')->where('id',$pks->quotation_id)->first();
            if($quotation != null){
                $quotation->detail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->get();
                $quotation->site = DB::table('sl_site')->where('quotation_id',$quotation->id)->get();
            }

            $pks->berakhir_dalam = $this->hitungBerakhirKontrak($pks->kontrak_akhir);

            $pks->mulai_kontrak = $pks->kontrak_awal ? Carbon::createFromFormat('Y-m-d', $pks->kontrak_awal)->isoFormat('D MMMM Y') : null;
            $pks->kontrak_selesai = $pks->kontrak_akhir ? Carbon::createFromFormat('Y-m-d', $pks->kontrak_akhir)->isoFormat('D MMMM Y') : null;

            $spk =  DB::table('sl_spk')->where('id',$pks->spk_id)->first();

            $issues = DB::table('sl_issue')->where('pks_id',$pksId)->whereNull('deleted_at')->get();
            foreach ($issues as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y HH:mm');
            }

            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $sales = DB::table($db2.'.m_user')->where('id',$pks->sales_id)->first();
            $pks->sales = "";
            if($sales !=null){
                $pks->sales = $sales->full_name;
            }
            $crm1 = DB::table($db2.'.m_user')->where('id',$pks->crm_id_1)->first();
            if($crm1 !=null){
                $pks->crm1 = $crm1->full_name."</br>";
            }
            $crm2 = DB::table($db2.'.m_user')->where('id',$pks->crm_id_2)->first();
            if($crm2 !=null){
                $pks->crm2 = $crm2->full_name."</br>";
            }
            $crm3 = DB::table($db2.'.m_user')->where('id',$pks->crm_id_3)->first();
            if($crm3 !=null){
                $pks->crm3 = $crm3->full_name."</br>";
            }
            $spvRo = DB::table($db2.'.m_user')->where('id',$pks->spv_ro_id)->first();
            if($spvRo !=null){
                $pks->spv_ro = $spvRo->full_name."</br>";
            }
            $ro1 = DB::table($db2.'.m_user')->where('id',$pks->ro_id_1)->first();
            if($ro1 !=null){
                $pks->ro1 = $ro1->full_name."</br>";
            }
            $ro2 = DB::table($db2.'.m_user')->where('id',$pks->ro_id_2)->first();
            if($ro2 !=null){
                $pks->ro2 = $ro2->full_name."</br>";
            }
            $ro3 = DB::table($db2.'.m_user')->where('id',$pks->ro_id_3)->first();
            if($ro3 !=null){
                $pks->ro3 = $ro3->full_name."</br>";
            }

            // hpp coss dan gpm
            $daftarTunjangan = [];
            if($quotation != null){
                $daftarTunjangan = DB::select("SELECT DISTINCT nama_tunjangan as nama FROM `sl_quotation_detail_tunjangan` WHERE deleted_at is null and quotation_id = $quotation->id");
                $quotationService = new QuotationService();
                $calcQuotation = $quotationService->calculateQuotation($quotation);
            }

            return view('sales.monitoring-kontrak.view',compact('daftarTunjangan','issues','data','leads','quotation','spk','pks'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $activitySub = DB::table('sl_customer_activity')
            ->select('pks_id', DB::raw('COUNT(*) as total_activity'))
            ->whereNull('deleted_at')
            ->where('is_activity',1)
            ->groupBy('pks_id');

        $issueSub = DB::table('sl_issue')
            ->select('pks_id', DB::raw('COUNT(*) as total_issue'))
            ->whereNull('deleted_at')
            ->groupBy('pks_id');

        $query = DB::table('sl_pks')
            ->leftJoin($db2.'.m_user as sales', 'sales.id', '=', 'sl_pks.sales_id')
            ->leftJoin($db2.'.m_user as crm1', 'crm1.id', '=', 'sl_pks.crm_id_1')
            ->leftJoin($db2.'.m_user as crm2', 'crm2.id', '=', 'sl_pks.crm_id_2')
            ->leftJoin($db2.'.m_user as crm3', 'crm3.id', '=', 'sl_pks.crm_id_3')
            ->leftJoin($db2.'.m_user as ro1', 'ro1.id', '=', 'sl_pks.ro_id_1')
            ->leftJoin($db2.'.m_user as ro2', 'ro2.id', '=', 'sl_pks.ro_id_2')
            ->leftJoin($db2.'.m_user as ro3', 'ro3.id', '=', 'sl_pks.ro_id_3')
            ->leftJoin($db2.'.m_user as rospv', 'rospv.id', '=', 'sl_pks.spv_ro_id')
            ->leftJoin('m_status_pks', 'sl_pks.status_pks_id', '=', 'm_status_pks.id')
            ->leftJoinSub($activitySub, 'activity', 'activity.pks_id', '=', 'sl_pks.id')
            ->leftJoinSub($issueSub, 'issue', 'issue.pks_id', '=', 'sl_pks.id')
            ->whereNull('sl_pks.deleted_at')
            ->select(
                'sl_pks.kontrak_awal','sl_pks.kontrak_akhir','sl_pks.nomor','sl_pks.id','sl_pks.leads_id','sl_pks.site_id','sl_pks.spk_id','sl_pks.quotation_id','sl_pks.tgl_pks','sl_pks.status_pks_id','sl_pks.created_at','sl_pks.created_by','sl_pks.nama_site','sl_pks.kebutuhan',
                'm_status_pks.nama as status',
                DB::raw('CONCAT_WS("<br />", crm1.full_name, crm2.full_name, crm3.full_name) as crm'),
                DB::raw('CONCAT_WS("<br />", rospv.full_name, ro1.full_name, ro2.full_name, ro3.full_name) as ro'),
                'sales.full_name as sales',
                DB::raw('IFNULL(activity.total_activity, 0) as aktifitas'),
                DB::raw('IFNULL(issue.total_issue, 0) as issue')
            );

        return DataTables::of($query)
        ->filterColumn('sales', function($query, $keyword) {
            $query->where('sales.full_name', 'like', "%{$keyword}%");
        })
        ->filterColumn('status', function($query, $keyword) {
            $query->where('m_status_pks.nama', 'like', "%{$keyword}%");
        })
        ->filterColumn('crm', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('crm1.full_name', 'like', "%{$keyword}%")
                  ->orWhere('crm2.full_name', 'like', "%{$keyword}%")
                  ->orWhere('crm3.full_name', 'like', "%{$keyword}%");
            });
        })
        ->filterColumn('ro', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('rospv.full_name', 'like', "%{$keyword}%")
                  ->orWhere('ro1.full_name', 'like', "%{$keyword}%")
                  ->orWhere('ro2.full_name', 'like', "%{$keyword}%")
                  ->orWhere('ro3.full_name', 'like', "%{$keyword}%");
            });
        })
        ->addColumn('s_mulai_kontrak', function ($data) {
            return $data->kontrak_awal ? Carbon::createFromFormat('Y-m-d', $data->kontrak_awal)->isoFormat('D MMMM Y') : null;
        })
        ->addColumn('s_kontrak_selesai', function ($data) {
            return $data->kontrak_akhir ? Carbon::createFromFormat('Y-m-d', $data->kontrak_akhir)->isoFormat('D MMMM Y') : null;
        })
        ->addColumn('aksi', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_akhir);

            $aksiIcon = "";

            if (is_null($data->leads_id) || is_null($data->site_id)) {
                $aksiIcon = '<a href="javascript:void(0)" class="text-body" onclick="Swal.fire({title: \'Pemberitahuan\', text: \'Leads atau Site tidak ditemukan, silahkan kontak administrator\', icon: \'warning\'})">
                    <i class="mdi mdi-magnify mdi-20px mx-1"></i>
                </a>';
                $aksiIcon .= '<a href="javascript:void(0)" class="text-body" onclick="Swal.fire({title: \'Pemberitahuan\', text: \'Leads atau Site tidak ditemukan, silahkan kontak administrator\', icon: \'warning\'})">
                    <i class="mdi mdi-calendar-plus mdi-20px mx-1"></i>
                </a>';
            } else {
                $aksiIcon = '<a href="'.route('monitoring-kontrak.view', $data->id).'" class="text-body">
                    <i class="mdi mdi-magnify mdi-20px mx-1"></i>
                </a>';
                $aksiIcon .= '<a href="'.route('customer-activity.add-activity-kontrak', $data->id).'" class="text-body">
                    <i class="mdi mdi-calendar-plus mdi-20px mx-1"></i>
                </a>';
            }

            $aksiDropdown = "";
            if($selisih<=90 && $selisih !=0){
                $aksiDropdown .= '<a href="javascript:;" class="dropdown-item">Send Email</a>';
            }
            if($selisih == 0){
                $aksiDropdown .= '<a href="javascript:;" class="dropdown-item btn-terminate-kontrak" data-id="'.$data->id.'">Terminate</a>';
            }

            if (empty($data->ro) && in_array(Auth::user()->role_id,[2,8,6,98])) {
                $aksiDropdown .= '<a href="'.route('customer-activity.add-ro-kontrak',$data->id).'" class="dropdown-item">Pilih RO</a>';
            }
            if (empty($data->crm) && in_array(Auth::user()->role_id,[2,55,56,96])) {
                $aksiDropdown .= '<a href="'.route('customer-activity.add-crm-kontrak',$data->id).'" class="dropdown-item">Pilih CRM</a>';
            }
            if(in_array(Auth::user()->role_id,[2,56])&&$data->status_pks_id > 9 ){
                if ($data->ro != "" && $data->crm != "") {
                    $aksiDropdown .= '<a href="javascript:void(0)" class="dropdown-item">Aktifkan Site</a>';
                } else {
                    $aksiDropdown .= '<a href="javascript:;" class="dropdown-item" onclick="Swal.fire({title: \'Pemberitahuan\', text: \'Belum memilih RO atau CRM\', icon: \'warning\'})">Aktifkan Site</a>';
                }
            }
            $aksiDropdown .= '<a href="'.route('customer-activity.add-status-kontrak',$data->id).'" class="dropdown-item">Update Status</a>';

            $dropdown = "";
            if($aksiDropdown != ""){
                $dropdown = '<div class="dropdown">
                    <a href="javascript:;" class="btn dropdown-toggle hide-arrow text-body p-0" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical mdi-20px"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="">
                        '.$aksiDropdown.'
                    </div>
                </div>';
            }
            $aksi = '<div class="d-flex align-items-center">
            '.$aksiIcon.'
            '.$dropdown.'
            </div>';
            return $aksi;
        })
        ->addColumn('berakhir_dalam', function ($data) {
            return $this->hitungBerakhirKontrak($data->kontrak_akhir);
        })
        ->addColumn('warna_row', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_akhir);
            if($selisih<=0){
                return '#2c3e5040';
            }else if($selisih<=60){
                return '#c0392b40';
            }else if($selisih<=90){
                return '#f39c1240';
            }else{
                return '#27ae6040';
            }
        })
        ->addColumn('warna_font', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_akhir);
            if($selisih<=0){
                return '#636578';
            }else if($selisih<=60){
                return '#636578';
            }else if($selisih<=90){
                return '#636578';
            }else{
                return '#636578';
            }
        })
        // ->editColumn('nomor', function ($data) {
        //     return '<a href="'.route('pks.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        // })
        ->editColumn('aktifitas', function ($data) {
            return '<button class="btn btn-sm btn-info" onclick="openNormalDataTableModal(`'.route('customer-activity.modal.list-activity-kontrak',['pks_id' => $data->id]).'`,`DATA AKTIFITAS PADA KONTRAK '.$data->nomor.'`)">'.$data->aktifitas.'</button>';
        })
        ->editColumn('issue', function ($data) {
            return '<button class="btn btn-sm btn-secondary" onclick="openNormalDataTableModal(`'.route('customer-activity.modal.list-issue',['pks_id' => $data->id]).'`,`DATA ISSUE PADA KONTRAK '.$data->nomor.'`)">'.$data->issue.'</button>';
        })
        ->addColumn('progress',function ($data) {
            $progress = 0;
            $bgColor = "";
            $param = 11.11;
            if($data->status_pks_id==1){
                $progress = $param*1;
                $bgColor = "bg-secondary";
            }else if($data->status_pks_id==2){
                $progress = $param*2;
                $bgColor = "bg-secondary";
            }else if($data->status_pks_id==3){
                $progress = $param*3;
                $bgColor = "bg-secondary";
            }else if($data->status_pks_id==4){
                $progress = $param*4;
                $bgColor = "bg-info";
            }else if($data->status_pks_id==5){
                $progress = $param*5;
                $bgColor = "bg-info";
            }else if($data->status_pks_id==6){
                $progress = $param*6;
                $bgColor = "bg-info";
            }else if($data->status_pks_id==7){
                $progress = $param*7;
                $bgColor = "bg-primary";
            }else if($data->status_pks_id==8){
                $progress = $param*8;
                $bgColor = "bg-primary";
            }else if($data->status_pks_id==9){
                $progress = 100;
                $bgColor = "bg-success";
            }

            return '<div class="progress" style="height: 5px;">
                <div class="progress-bar '.$bgColor.'" role="progressbar" style="width: '.$progress.'%" aria-valuenow="'.$progress.'" aria-valuemin="0" aria-valuemax="100"></div>
            </div>';
        })
        ->addColumn('status_berlaku', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_akhir);
            if($selisih<=0){
                return '<span class="badge rounded-pill bg-label-danger text-capitalized">Kontrak Habis</span>';
            }else if($selisih<=60){
                return '<span class="badge rounded-pill bg-label-danger text-capitalized">Berakhir dalam 2 bulan</span>';
            }else if($selisih<=90){
                return '<span class="badge rounded-pill bg-label-warning text-capitalized">Berakhir dalam 3 bulan</span>';
            }else{
                return '<span class="badge rounded-pill bg-label-success text-capitalized">Lebih dari 3 Bulan</span>';
            }
        })
        ->rawColumns(['aksi','nomor','nama_site','aktifitas','crm','ro','sales','progress','status_berlaku','issue'])
        ->make(true);
    }

    public function listTerminate (Request $request){
        $data = DB::table('sl_pks')
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->leftJoin('sl_quotation','sl_pks.quotation_id','sl_quotation.id')
                ->where('sl_pks.status_pks_id',100)
                ->select('sl_quotation.leads_id','sl_quotation.kontrak_selesai','sl_quotation.mulai_kontrak','sl_pks.spk_id','sl_pks.quotation_id','sl_pks.created_by','sl_pks.created_at','sl_pks.id','sl_pks.nomor','sl_spk.nomor as nomor_spk','sl_pks.tgl_pks','sl_quotation.nama_perusahaan','sl_quotation.kebutuhan','sl_pks.status_pks_id','sl_quotation.nomor as nomor_quotation',
                DB::raw('(SELECT GROUP_CONCAT(nama_site SEPARATOR "<br /> ")
                    FROM sl_quotation_site
                    WHERE sl_quotation_site.quotation_id = sl_quotation.id) as nama_site')
                )
                ->get();

        foreach ($data as $key => $value) {
            $value->tgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_pks)->isoFormat('D MMMM Y');
            $value->mulai_kontrak = Carbon::createFromFormat('Y-m-d',$value->mulai_kontrak)->isoFormat('D MMMM Y');
            $value->s_kontrak_selesai = Carbon::createFromFormat('Y-m-d',$value->kontrak_selesai)->isoFormat('D MMMM Y');
            $value->created_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_pks')->where('id',$value->status_pks_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('warna_row', function ($data) {
            return '#2c3e5040';
        })
        ->addColumn('warna_font', function ($data) {
            return '#636578';
        })
        ->editColumn('nomor', function ($data) {
            return '<a href="#" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        })
        ->editColumn('nomor_spk', function ($data) {
            return '<a href="#" style="font-weight:bold;color:#000056">'.$data->nomor_spk.'</a>';
        })
        ->editColumn('nomor_quotation', function ($data) {
            return '<a href="#" style="font-weight:bold;color:#000056">'.$data->nomor_quotation.'</a>';
        })
        ->rawColumns(['nomor','nama_site','nomor_spk','nomor_quotation'])
        ->make(true);
    }

    function hitungBerakhirKontrak($tanggalBerakhir) {
        if (is_null($tanggalBerakhir)) {
            return "-";
        }
        // Tanggal saat ini
        $tanggalSekarang = Carbon::now()->format('Y-m-d');
        $tanggalSekarang = Carbon::createFromFormat('Y-m-d', $tanggalSekarang);

        // Buat objek tanggal dari input
        $tanggalBerakhir = Carbon::createFromFormat('Y-m-d', $tanggalBerakhir);

        // Jika kontrak sudah habis
        if ($tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir)) {
            return "Kontrak habis";
        }

        // Hitung selisih
        $selisih = $tanggalSekarang->diff($tanggalBerakhir);

        // Format output hanya jika nilainya lebih dari 0
        $hasil = [];
        if ($selisih->y > 0) {
            $hasil[] = "{$selisih->y} tahun";
        }
        if ($selisih->m > 0) {
            $hasil[] = "{$selisih->m} bulan";
        }
        if ($selisih->d > 0) {
            $hasil[] = "{$selisih->d} hari";
        }

        // Gabungkan hasil menjadi string
        return implode(', ', $hasil);
    }
    function selisihKontrakBerakhir($tanggalBerakhir) {
        if (is_null($tanggalBerakhir)) {
            return 0;
        }
         // Tanggal sekarang
        $tanggalSekarang = Carbon::now();

        // Tanggal kontrak berakhir
        $tanggalBerakhir = Carbon::createFromFormat('Y-m-d', $tanggalBerakhir);

        // Jika kontrak sudah habis
        if ($tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir)) {
            return 0;
        }

        // Hitung selisih dalam hari
        $selisihHari = $tanggalSekarang->diffInDays($tanggalBerakhir);

        return $selisihHari;
    }

    public function terminate(Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $pksId = $request->id;
            $pks = DB::table('sl_pks')->where('id',$pksId)->first();
            $spkId = $pks->spk_id;
            $quotationId = $pks->quotation_id;

            DB::table('sl_pks')->where('id',$pksId)->update(
                ['status_pks_id'=>100,'deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_spk')->where('id',$spkId)->update(
                ['status_spk_id'=>100,'deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation')->where('id',$quotationId)->update(
                ['status_quotation_id'=>100,'deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );

            // update deleted at dan deleted by semua detail dari quotation
            DB::table('sl_quotation_aplikasi')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_chemical')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_coss')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_hpp')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_requirement')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_tunjangan')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_devices')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_kaporlap')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_kerjasama')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_margin')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_ohc')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_pic')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_site')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_training')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );

            // HRIS belum di terminate

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil di terminate']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function templateImport(Request $request) {
        $dt = Carbon::now()->toDateTimeString();

        return Excel::download(new MonitoringKontrakTemplateExport(), 'Template Monitoring Kontrak-'.$dt.'.xlsx');
    }

    public function import (Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('sales.monitoring-kontrak.import',compact('now'));
    }

    public function inquiryImport(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:csv,xls,xlsx',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
                'mimes' => 'tipe file harus csv,xls atau xlsx',
            ]);

            $array = null;
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $file = $request->file('file');
                $current_date_time = Carbon::now()->toDateTimeString();
                $importId = uniqid();
                $db2 = DB::connection('mysqlhris')->getDatabaseName();

                // Get the csv rows as an array
                $array = Excel::toArray(new stdClass(), $file);
                $jumlahError = 0;
                $jumlahWarning = 0;
                $jumlahSuccess = 0;
                foreach ($array[0] as $key => $v) {
                    if($key==0){
                        continue;
                    };
                    if($v[0]==null){
                        continue;
                    }
                    $layanan = $v[17] ? DB::table('m_kebutuhan')->where('nama', 'LIKE', $v[17])->first() : null;
                    $layananId = $layanan ? $layanan->id : null;
                    $bidangUsaha = $v[19] ? DB::table('m_bidang_perusahaan')->where('nama','LIKE',$v[19])->first() : null;
                    $bidangUsahaId = $bidangUsaha ? $bidangUsaha->id : null;
                    $jenisPerusahaan = $v[20] ? DB::table('m_jenis_perusahaan')->where('nama','LIKE',$v[20])->first() : null;
                    $jenisPerusahaanId = $jenisPerusahaan ? $jenisPerusahaan->id : null;
                    $statusPks = $v[16] ? DB::table('m_status_pks')->where('nama','LIKE',$v[16])->first() : null;
                    $statusPksId = $statusPks ? $statusPks->id : null;
                    $provinsi = $v[21] ? DB::table($db2.'.m_province')->where('name','LIKE',$v[21])->first() : null;
                    $provinsiId = $provinsi ? $provinsi->id : null;
                    $kota = $v[22] ? DB::table($db2.'.m_city')->where('name','LIKE',$v[22])->first() : null;
                    $kotaId = $kota ? $kota->id : null;
                    $crm = $v[1] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[1])->first() : null;
                    $crmId1 = $crm ? $crm->id : null;
                    $crm = $v[2] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[2])->first() : null;
                    $crmId2 = $crm ? $crm->id : null;
                    $crm = $v[3] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[3])->first() : null;
                    $crmId3 = $crm ? $crm->id : null;
                    $spvRo = $v[4] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[4])->first() : null;
                    $spvRoId = $spvRo ? $spvRo->id : null;
                    $ro = $v[5] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[5])->first() : null;
                    $roId1 = $ro ? $ro->id : null;
                    $ro = $v[6] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[6])->first() : null;
                    $roId2 = $ro ? $ro->id : null;
                    $ro = $v[7] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[7])->first() : null;
                    $roId3 = $ro ? $ro->id : null;
                    $loyalty = $v[24] ? DB::table('m_loyalty')->where('nama','LIKE',$v[24])->first() : null;
                    $loyaltyId = $loyalty ? $loyalty->id : null;
                    $company = $v[14] ? DB::table($db2.'.m_company')->where('name','LIKE',$v[14])->first() : null;
                    $companyId = $company ? $company->id : null;
                    $kategoriSesuaiHc = $v[66] ? DB::table('m_kategori_sesuai_hc')->where('nama','LIKE',$v[66])->first() : null;
                    $kategoriSesuaiHcId = $kategoriSesuaiHc ? $kategoriSesuaiHc->id : null;
                    $sales = $v[67] ? DB::table($db2.'.m_user')->where('full_name','LIKE',$v[67])->first() : null;
                    $salesId = $sales ? $sales->id : null;
                    $leads = $v[11] ? DB::table('sl_leads')->where('nama_perusahaan','LIKE',$v[11])->first() : null;
                    $leadsId = $leads ? $leads->id : null;
                    $site = $v[9] ? DB::table('sl_site')->where('nama_site','LIKE',$v[9])->first() : null;
                    $siteId = $site ? $site->id : null;
                    $branch = $v[13] ? DB::table($db2.'.m_branch')->where('name','LIKE',$v[18])->first() : null;
                    $branchId = $branch ? $branch->id : null;

                    DB::table('sl_pks_import')->insert([
                        'import_id' => $importId,
                        'quotation_id' => null,
                        'spk_id' => null,
                        'leads_id' => $leadsId,
                        'site_id' => $siteId,
                        'branch_id' => $branchId,
                        'company_id' => $companyId,
                        'kode_site' => $v[8],
                        'nomor' => $v[0],
                        'tgl_pks' => $v[25] ? Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($v[25])->toDateString() : null,
                        'nama_site' => $v[9],
                        'alamat_site' => $v[10],
                        'nama_proyek' => $v[15],
                        'kode_perusahaan' => $v[11],
                        'nama_perusahaan' => $v[12],
                        'alamat_perusahaan' => $v[13],
                        'layanan_id' => $layananId,
                        'layanan' => $layananId ? $v[17] : null,
                        'bidang_usaha_id' => $bidangUsahaId,
                        'bidang_usaha' => $bidangUsahaId ? $v[19] : null,
                        'jenis_perusahaan_id' => $jenisPerusahaanId,
                        'jenis_perusahaan' =>  $jenisPerusahaanId ? $v[20] : null,
                        'link_pks_disetujui' => null,
                        'status_pks_id' => $statusPksId,
                        'provinsi_id' => $provinsiId,
                        'provinsi' => $provinsiId ? $v[21] : null,
                        'kota_id' => $kotaId,
                        'kota' => $kotaId ? $v[22] : null,
                        'pma' => $v[23],
                        'sales_id' => $salesId,
                        'crm_id_1' => $crmId1,
                        'crm_id_2' => $crmId2,
                        'crm_id_3' => $crmId3,
                        'spv_ro_id' => $spvRoId,
                        'ro_id_1' => $roId1,
                        'ro_id_2' => $roId2,
                        'ro_id_3' => $roId3,
                        'loyalty_id' => $loyaltyId,
                        'loyalty' => $loyaltyId ? $v[24] : null,
                        'kontrak_awal' => $v[25] ? Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($v[25])->toDateString() : null,
                        'kontrak_akhir' => $v[26] ? Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($v[26])->toDateString() : null,
                        'jumlah_hc' => $v[27],
                        'total_sebelum_pajak' => $v[28],
                        'dasar_pengenaan_pajak' => $v[29],
                        'ppn' => $v[30],
                        'pph' => $v[31],
                        'total_invoice' => $v[32],
                        'persen_mf' => $v[33],
                        'nominal_mf' => $v[34],
                        'persen_bpjs_tk' => $v[35],
                        'nominal_bpjs_tk' => $v[36],
                        'persen_bpjs_ks' => $v[37],
                        'nominal_bpjs_ks' => $v[38],
                        'as_tk' => $v[39],
                        'as_ks' => $v[40],
                        'ohc' => $v[41],
                        'thr_provisi' => $v[42],
                        'thr_ditagihkan' => $v[43],
                        'penagihan_selisih_thr' => $v[44],
                        'kaporlap' => $v[45],
                        'device' => $v[46],
                        'chemical' => $v[47],
                        'training' => $v[48],
                        'biaya_training' => $v[49],
                        'tgl_kirim_invoice' => $v[50],
                        'jumlah_hari_top' => $v[51],
                        'tipe_hari_top' => $v[52],
                        'tgl_gaji' => $v[53],
                        'pic_1' => $v[54],
                        'jabatan_pic_1' => $v[55],
                        'email_pic_1' => $v[56],
                        'telp_pic_1' => $v[57],
                        'pic_2' => $v[58],
                        'jabatan_pic_2' => $v[59],
                        'email_pic_2' => $v[60],
                        'telp_pic_2' => $v[61],
                        'pic_3' => $v[62],
                        'jabatan_pic_3' => $v[63],
                        'email_pic_3' => $v[64],
                        'telp_pic_3' => $v[65],
                        'kategori_sesuai_hc_id' => $kategoriSesuaiHcId,
                        'kategori_sesuai_hc' => $kategoriSesuaiHcId ? $v[66] : null,
                        'is_aktif' => 1,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name,
                    ]);

                    $jumlahSuccess++;
                    // foreach ($v as $keyd => $value) {

                    // }
                }
            }
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            DB::commit();
            $datas = DB::table('sl_pks_import')->where('import_id',$importId)->get();
            return view('sales.monitoring-kontrak.inquiry',compact('importId','datas','now','jumlahError','jumlahSuccess','jumlahWarning'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveImport(Request $request){
        DB::beginTransaction();
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $importId = $request->importId;
            $datas = DB::table('sl_pks_import')->where('import_id',$importId)->get();
            foreach ($datas as $data) {
                // cek dulu apakah leads_id ada di tabel leads jika tidak ada maka membuat leads baru
                $leadsId = $data->leads_id;
                if($data->nama_perusahaan != null){
                    $leadsData = [
                        'nomor' => $data->kode_perusahaan,
                        'tgl_leads' => Carbon::now()->toDateString(),
                        'jenis_perusahaan_id' => $data->jenis_perusahaan_id,
                        'nama_perusahaan' => $data->nama_perusahaan,
                        'alamat' => $data->alamat_perusahaan,
                        'platform_id' => 99,
                        'branch_id' => $data->branch_id,
                        'kebutuhan_id' => $data->layanan_id,
                        'status_leads_id' => 1,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name,
                    ];

                    if($leadsId == null){
                        $leadsId = DB::table('sl_leads')->insertGetId($leadsData);
                    }else{
                        DB::table('sl_leads')->where('id',$data->leads_id)->update($leadsData);
                    };
                }

                // cek dulu apakah site ada di tabel kalau belum ada maka insert jika ada maka update
                $siteId = $data->site_id;
                if($data->nama_site != null){
                    $siteData = [
                        'leads_id' => $leadsId,
                        'nomor' => $data->kode_site,
                        'nama_site' => $data->nama_site,
                        'provinsi_id' => $data->provinsi_id,
                        'kota_id' => $data->kota_id,
                        'provinsi' => $data->provinsi,
                        'kota' => $data->kota,
                        'penempatan' => $data->alamat_site,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name,
                    ];
                    if($siteId == null){
                        $siteId = DB::table('sl_site')->insertGetId($siteData);
                    }else{
                        DB::table('sl_site')->where('id',$data->site_id)->update($siteData);
                    }
                }

                //cari dulu data di pks kalau tidak ada insert kalau ada maka update
                $pks = DB::table('sl_pks')->where('nomor',$data->nomor)->first();
                $dataUpdate = [
                    'quotation_id' => $data->quotation_id,
                    'spk_id' => $data->spk_id,
                    'leads_id' => $leadsId,
                    'site_id' => $siteId,
                    'branch_id' => $data->branch_id,
                    'company_id' => $data->company_id,
                    'kode_site' => $data->kode_site,
                    'nomor' => $data->nomor,
                    'tgl_pks' => $data->tgl_pks,
                    'nama_site' => $data->nama_site,
                    'alamat_site' => $data->alamat_site,
                    'nama_proyek' => $data->nama_proyek,
                    'kode_perusahaan' => $data->kode_perusahaan,
                    'nama_perusahaan' => $data->nama_perusahaan,
                    'alamat_perusahaan' => $data->alamat_perusahaan,
                    'layanan_id' => $data->layanan_id,
                    'layanan' => $data->layanan,
                    'bidang_usaha_id' => $data->bidang_usaha_id,
                    'bidang_usaha' => $data->bidang_usaha,
                    'jenis_perusahaan_id' => $data->jenis_perusahaan_id,
                    'jenis_perusahaan' => $data->jenis_perusahaan,
                    'link_pks_disetujui' => $data->link_pks_disetujui,
                    'status_pks_id' => $data->status_pks_id,
                    'provinsi_id' => $data->provinsi_id,
                    'provinsi' => $data->provinsi,
                    'kota_id' => $data->kota_id,
                    'kota' => $data->kota,
                    'pma' => $data->pma,
                    'sales_id' => $data->sales_id,
                    'crm_id_1' => $data->crm_id_1,
                    'crm_id_2' => $data->crm_id_2,
                    'crm_id_3' => $data->crm_id_3,
                    'spv_ro_id' => $data->spv_ro_id,
                    'ro_id_1' => $data->ro_id_1,
                    'ro_id_2' => $data->ro_id_2,
                    'ro_id_3' => $data->ro_id_3,
                    'loyalty_id' => $data->loyalty_id,
                    'loyalty' => $data->loyalty,
                    'kontrak_awal' => $data->kontrak_awal,
                    'kontrak_akhir' => $data->kontrak_akhir,
                    'jumlah_hc' => $data->jumlah_hc,
                    'total_sebelum_pajak' => $data->total_sebelum_pajak,
                    'dasar_pengenaan_pajak' => $data->dasar_pengenaan_pajak,
                    'ppn' => $data->ppn,
                    'pph' => $data->pph,
                    'total_invoice' => $data->total_invoice,
                    'persen_mf' => $data->persen_mf,
                    'nominal_mf' => $data->nominal_mf,
                    'persen_bpjs_tk' => $data->persen_bpjs_tk,
                    'nominal_bpjs_tk' => $data->nominal_bpjs_tk,
                    'persen_bpjs_ks' => $data->persen_bpjs_ks,
                    'nominal_bpjs_ks' => $data->nominal_bpjs_ks,
                    'as_tk' => $data->as_tk,
                    'as_ks' => $data->as_ks,
                    'ohc' => $data->ohc,
                    'thr_provisi' => $data->thr_provisi,
                    'thr_ditagihkan' => $data->thr_ditagihkan,
                    'penagihan_selisih_thr' => $data->penagihan_selisih_thr,
                    'kaporlap' => $data->kaporlap,
                    'device' => $data->device,
                    'chemical' => $data->chemical,
                    'training' => $data->training,
                    'biaya_training' => $data->biaya_training,
                    'tgl_kirim_invoice' => $data->tgl_kirim_invoice,
                    'jumlah_hari_top' => $data->jumlah_hari_top,
                    'tipe_hari_top' => $data->tipe_hari_top,
                    'tgl_gaji' => $data->tgl_gaji,
                    'pic_1' => $data->pic_1,
                    'jabatan_pic_1' => $data->jabatan_pic_1,
                    'email_pic_1' => $data->email_pic_1,
                    'telp_pic_1' => $data->telp_pic_1,
                    'pic_2' => $data->pic_2,
                    'jabatan_pic_2' => $data->jabatan_pic_2,
                    'email_pic_2' => $data->email_pic_2,
                    'telp_pic_2' => $data->telp_pic_2,
                    'pic_3' => $data->pic_3,
                    'jabatan_pic_3' => $data->jabatan_pic_3,
                    'email_pic_3' => $data->email_pic_3,
                    'telp_pic_3' => $data->telp_pic_3,
                    'kategori_sesuai_hc_id' => $data->kategori_sesuai_hc_id,
                    'kategori_sesuai_hc' => $data->kategori_sesuai_hc,
                    'is_aktif' => $data->is_aktif
                ];
                if($pks){
                    $dataUpdate['updated_at'] = $current_date_time;
                    $dataUpdate['updated_by'] = Auth::user()->full_name;
                    DB::table('sl_pks')->where('id',$pks->id)->update($dataUpdate);
                }else{
                    $dataUpdate['created_at'] = $current_date_time;
                    $dataUpdate['created_by'] = Auth::user()->full_name;
                    DB::table('sl_pks')->insert($dataUpdate);
                }
            }

            $msgSave = 'Import Monitoring Kontrak berhasil Dilakukan !';

            DB::commit();
            return redirect()->route('monitoring-kontrak')->with('success', $msgSave);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    public function addIssue ($id){
        try {
            $pks = DB::table('sl_pks')->where('id',$id)->first();
            if($pks == null){
                abort(404);
            }
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $nowd = Carbon::now()->toDateString();

            $pks->s_mulai_kontrak = $pks->kontrak_awal ? Carbon::createFromFormat('Y-m-d', $pks->kontrak_awal)->isoFormat('D MMMM Y') : null;
            $pks->s_kontrak_selesai = $pks->kontrak_akhir ? Carbon::createFromFormat('Y-m-d', $pks->kontrak_akhir)->isoFormat('D MMMM Y') : null;

            $pks->status_kontrak = "";
            $statusKontrak = DB::table('m_status_pks')->where('id',$pks->status_pks_id)->first();
            if($statusKontrak !=null){
                $pks->status_kontrak = $statusKontrak->nama;
            }

            $jenisVisit = DB::table('m_jenis_visit')->whereNull('deleted_at')->get();

            return view('sales.pks.add-issue',compact('now','nowd','pks','jenisVisit'));
        } catch (\Exception $e) {
            dd($e);
            abort(500);
        }
    }

    public function saveIssue (Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $id = $request->id;
            $pks = DB::table('sl_pks')->where('id',$id)->first();
            $leads = DB::table('sl_leads')->where('id',$pks->leads_id)->first();

            $file = $request->file('lampiran');
            $extension = $file->getClientOriginalExtension();

            $filename = $file->getClientOriginalName();
            $filename = str_replace(".".$extension,"",$filename);
            $originalName = $filename.date("YmdHis").rand(10000,99999).".".$extension."";

            Storage::disk('lampiran-issue')->put($originalName, file_get_contents($file));

            $link = env('APP_URL').'/public/uploads/lampiran-issue/'.$originalName;

            DB::table('sl_issue')->insert([
                'leads_id' => $leads->id,
                'pks_id' => $pks->id,
                'site_id' => $pks->site_id,
                'tgl' => $request->tgl,
                'judul' => $request->judul,
                'jenis_keluhan' => $request->jenis_keluhan,
                'kolaborator' => $request->kolaborator,
                'deskripsi' => $request->deskripsi,
                'url_lampiran' => $link,
                'status' => $request->status,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Issue berhasil disimpan !']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.']);
        }
    }
    public function deleteIssue(Request $request){
        $id = $request->id;
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();

            $issue = DB::table('sl_issue')->where('id', $id)->whereNull('deleted_at')->first();
            if (!$issue) {
                return response()->json(['status' => 'error', 'message' => 'Issue tidak ditemukan.']);
            }

            DB::table('sl_issue')->where('id', $id)->whereNull('deleted_at')->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->id
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Issue berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), request());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus issue.']);
        }
    }

    public function deleteActivity(Request $request){
        $id = $request->id;
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();

            $activity = DB::table('sl_customer_activity')->where('id', $id)->whereNull('deleted_at')->first();
            if (!$activity) {
                return response()->json(['status' => 'error', 'message' => 'Aktivitas tidak ditemukan.']);
            }

            DB::table('sl_customer_activity')->where('id', $id)->whereNull('deleted_at')->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->id
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Aktivitas berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), request());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus aktivitas.']);
        }
    }
}

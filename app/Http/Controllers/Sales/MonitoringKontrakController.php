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

            $data = DB::table('sl_customer_activity')->whereNull('deleted_at')->where('pks_id',$pksId)->orderBy('created_at','desc')->get();
            foreach ($data as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y HH:mm');
                $value->stgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
            }

            $leads = DB::table('sl_leads')->where('id',$pks->leads_id)->first();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->where('id',$leads->jenis_perusahaan_id)->first();
            if($jenisPerusahaan !=null){ $leads->jenis_perusahaan = $jenisPerusahaan->nama; }else{ $leads->jenis_perusahaan = ""; }

            $quotation = DB::table('sl_quotation')->where('id',$pks->quotation_id)->first();
            $quotation->detail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->get();
            $quotation->site = DB::table('sl_site')->where('quotation_id',$quotation->id)->get();

            $pks->berakhir_dalam = $this->hitungBerakhirKontrak($quotation->kontrak_selesai);
            $quotation->mulai_kontrak = Carbon::createFromFormat('Y-m-d',$quotation->mulai_kontrak)->isoFormat('D MMMM Y');
            $quotation->kontrak_selesai = Carbon::createFromFormat('Y-m-d',$quotation->kontrak_selesai)->isoFormat('D MMMM Y');

            $spk =  DB::table('sl_spk')->where('id',$pks->spk_id)->first();

            return view('sales.monitoring-kontrak.view',compact('data','leads','quotation','spk','pks'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $data = DB::table('sl_pks')
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->leftJoin('sl_quotation','sl_pks.quotation_id','sl_quotation.id')
                ->Join('sl_leads','sl_pks.leads_id','sl_leads.id')
                ->whereNull('sl_pks.deleted_at')
                ->whereNull('sl_spk.deleted_at')
                ->whereNull('sl_quotation.deleted_at')
                ->whereNotNull('sl_quotation.mulai_kontrak')
                ->whereNotNull('sl_quotation.kontrak_selesai')
                ->where('sl_pks.status_pks_id','<>',100)
                ->select('sl_pks.status_pks_id','sl_quotation.leads_id','sl_pks.id','sl_pks.nomor','sl_pks.nama_perusahaan','sl_quotation.mulai_kontrak','sl_quotation.kontrak_selesai',
                DB::raw('(SELECT GROUP_CONCAT(nama_site SEPARATOR "<br /> ")
                    FROM sl_quotation_site
                    WHERE sl_quotation_site.quotation_id = sl_quotation.id) as nama_site'),
                    DB::raw("(select full_name from ".$db2.".m_user where id in (select user_id from m_tim_sales_d where id = sl_leads.tim_sales_d_id)) as sales"),
                    DB::raw('(SELECT GROUP_CONCAT(full_name SEPARATOR "<br /> ")
                        FROM '.$db2.'.m_user
                        WHERE '.$db2.'.m_user.id IN (sl_leads.ro_id, sl_leads.ro_id_1, sl_leads.ro_id_2, sl_leads.ro_id_3)) as ro'),
                    DB::raw('(SELECT GROUP_CONCAT(full_name SEPARATOR "<br /> ")
                        FROM '.$db2.'.m_user
                        WHERE '.$db2.'.m_user.id IN (sl_leads.crm_id, sl_leads.crm_id_1, sl_leads.crm_id_2)) as crm'),
                    DB::raw('(SELECT COUNT(*) FROM sl_customer_activity WHERE sl_customer_activity.pks_id = sl_pks.id AND sl_customer_activity.deleted_at IS NULL AND sl_customer_activity.is_activity = 1) as aktifitas'),
                )
                ->get();

        foreach ($data as $key => $value) {
            $value->s_mulai_kontrak = Carbon::createFromFormat('Y-m-d',$value->mulai_kontrak)->isoFormat('D MMMM Y');
            $value->s_kontrak_selesai = Carbon::createFromFormat('Y-m-d',$value->kontrak_selesai)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_pks')->where('id',$value->status_pks_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            // if (!in_array(Auth::user()->role_id,[2,54,55])) {
            //     return "";
            // }
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
            $aksi = "";
            $aksi .= '&nbsp;<a href="'.route('monitoring-kontrak.view', $data->id).'" class="btn btn-sm btn-secondary">View</a>';
            $aksi .= '&nbsp;<a href="'.route('customer-activity.add-activity-kontrak',$data->id).'" class="btn btn-sm btn-info">Buat Activity</a>';

            if($selisih<=90 && $selisih !=0){
                $aksi .= '&nbsp;<a href="#" class="btn btn-sm btn-success">Send Email</a>';
                $aksi .= '&nbsp;<a href="'.route('quotation.add', ['leads_id' => $data->leads_id, 'tipe' => 'Quotation Lanjutan']).'" class="btn btn-sm btn-primary">Buat Quotation</a>';
            }
            if($selisih == 0){
                $aksi .= '&nbsp;<a href="javscript:void(0)" class="btn btn-sm btn-danger btn-terminate-kontrak" data-id="'.$data->id.'">Terminate Kontrak</a>';
            }

            if (empty($data->ro) && in_array(Auth::user()->role_id,[2,8,6,98])) {
                $aksi .= '&nbsp;<a href="'.route('customer-activity.add-ro-kontrak',$data->id).'" class="btn btn-sm btn-warning">Pilih RO</a>';
            }
            if (empty($data->crm) && in_array(Auth::user()->role_id,[2,55,56,96])) {
                $aksi .= '&nbsp;<a href="'.route('customer-activity.add-crm-kontrak',$data->id).'" class="btn btn-sm btn-warning">Pilih CRM</a>';
            }
            if(in_array(Auth::user()->role_id,[2,56])&&$data->status_pks_id != 7 ){
                if ($data->ro != "" && $data->crm != "") {
                    $aksi .= '&nbsp;<a href="javascript:void(0)" class="btn btn-sm btn-success">Aktifkan Site</a>';
                } else {
                    $aksi .= '&nbsp;<button class="btn btn-sm btn-success" onclick="Swal.fire({title: \'Pemberitahuan\', text: \'Belum memilih RO atau CRM\', icon: \'warning\'})">Aktifkan Site</button>';
                }
            }

            return '<div class="justify-content-center d-flex">
                                '.$aksi.'
                    </div>';
        })
        ->addColumn('berakhir_dalam', function ($data) {
            return $this->hitungBerakhirKontrak($data->kontrak_selesai);
        })
        ->addColumn('warna_row', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
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
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
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
        ->rawColumns(['aksi','nomor','nama_site','aktifitas','crm','ro','sales'])
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

}

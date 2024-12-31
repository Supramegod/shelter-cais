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

    public function list (Request $request){
        $data = DB::table('sl_pks')
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->leftJoin('sl_quotation','sl_pks.quotation_id','sl_quotation.id')
                ->whereNull('sl_pks.deleted_at')
                ->whereNull('sl_spk.deleted_at')
                ->whereNull('sl_quotation.deleted_at')
                ->where('sl_pks.status_pks_id',7)
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
        ->addColumn('aksi', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
            $aksi = "";

            if($selisih<=90 && $selisih !=0){
                $aksi .= '&nbsp;<a href="#" class="btn btn-sm btn-success">Send Email</a>';
                $aksi .= '&nbsp;<a href="'.route('customer-activity.add',['leads_id'=>$data->leads_id]).'" class="btn btn-sm btn-info">Buat Activity</a>';
                $aksi .= '&nbsp;<a href="'.route('quotation.add', ['leads_id' => $data->leads_id, 'tipe' => 'Quotation Lanjutan']).'" class="btn btn-sm btn-primary">Buat Quotation</a>';
            }
            if($selisih == 0){
                $aksi .= '&nbsp;<a href="'.route('pks.view',$data->id).'" class="btn btn-sm btn-danger">Terminate Kontrak</a>';
            }
            // if($selisih<=0){
            //     return '#636578';
            // }else if($selisih<=60){
            //     return '#636578';
            // }else if($selisih<=90){
            //     return '#636578';
            // }else{
            //     return '#636578';
            // }

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
        ->editColumn('nomor', function ($data) {
            return '<a href="'.route('pks.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        })
        ->editColumn('nomor_spk', function ($data) {
            return '<a href="'.route('spk.view',$data->spk_id).'" style="font-weight:bold;color:#000056">'.$data->nomor_spk.'</a>';
        })
        ->editColumn('nomor_quotation', function ($data) {
            return '<a href="'.route('quotation.view',$data->quotation_id).'" style="font-weight:bold;color:#000056">'.$data->nomor_quotation.'</a>';
        })
        ->rawColumns(['aksi','nomor','nama_site','nomor_spk','nomor_quotation'])
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
}

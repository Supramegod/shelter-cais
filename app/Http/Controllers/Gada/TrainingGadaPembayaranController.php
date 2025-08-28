<?php

namespace App\Http\Controllers\Gada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use \PDF;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use \stdClass;
use Illuminate\Support\Facades\Storage;


class TrainingGadaPembayaranController extends Controller
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
        $status = DB::table('m_status_leads')->whereNull('deleted_at')->get();
        $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

        $error =null;
        $success = null;
        if($ctglDari->gt($ctglSampai)){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        };
        if($ctglSampai->lt($ctglDari)){
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }
        return view('sdt.gada-pembayaran.list',compact('branch','platform','status','tglDari','tglSampai','request','error','success'));
    }

    public function list (Request $request){
        try {
            // <a href="'.route('training-gada.generateInvoice').'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
            $data = DB::table('training_gada_transaksi as transaksi')
            ->leftjoin('training_gada_bukti_bayar as bukti','bukti.training_id', '=', 'transaksi.training_gada_id')
            ->leftjoin('training_gada_calon as data','transaksi.training_gada_id', '=', 'data.id')
            ->where('transaksi.is_active', 1)
            ->select('data.id', 'data.nik', 'data.nama', 'data.jenis_pelatihan', 'bukti.path', 
                DB::raw("IF(transaksi.status = 1, 'Sudah Bayar', 'Belum Bayar') as status_bayar"), 
                'transaksi.status',
                'transaksi.harga', DB::raw("DATE_FORMAT(transaksi.payment_date,'%d-%m-%Y %H:%i') as payment_date"))
            ->orderBy('transaksi.id', 'DESC')
            ->get();          
            return DataTables::of($data)
            ->editColumn('bukti_bayar', function ($data) {
                if($data->status == 1){
                    return '<a target="_blank" href="'.$data->path.'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-download"></i> Download</a>&nbsp;';
                }else{
                    return '-';
                }
            })
            ->editColumn('invoice', function ($data) {
                if($data->status == 1){
                    return '<a target="_blank" href="'.route('training-gada.generateInvoice', $data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-download"></i> Download</a>&nbsp;';
                }else{
                    return '-';
                }
            })
            ->editColumn('status_bayar', function ($data) {
                if($data->status == 1){
                    return '<span class="text-success">'.$data->status_bayar.'</span>';
                }else{
                    return '<span class="text-danger">'.$data->status_bayar.'</span>';
                }
            })
            ->editColumn('harga', function ($data) {
                return number_format($data->harga);
            })
            // ->editColumn('status_bayar', function ($data) {
            //     if($data->data_registrasi == 1 && $data->status_bayar == 0){
            //         return '<div><button onclick="showMessageWhatsappInvoice('.$data->id.','.$data->no_wa.',\''.$data->last_sent.'\',\''.$data->jenis_pelatihan.'\',\''.$data->nama.'\', 0, \'\', \'\')" class="btn btn-danger btn-sm">Belum Bayar</buuton></div>';
            //     }else if($data->data_registrasi == 1 && $data->status_bayar == 1){
            //         return '<div><button onclick="showMessageWhatsappInvoice('.$data->id.','.$data->no_wa.',\''.$data->last_sent.'\',\''.$data->jenis_pelatihan.'\',\''.$data->nama.'\', 1, \''.$data->path.'\', \''.route('training-gada.generateInvoice', $data->id).'\')" class="btn btn-success btn-sm">Sudah Bayar</buuton></div>';
            //     }else{
            //         return '-';
            //     }
            // })
            // ->addColumn('aksi', function ($data) {
            //     if($data->data_registrasi == 1){
            //         if(null == $data->path){
            //             return '<div class="justify-content-center d-flex">
            //                 <div onclick="showChangeStatus('.$data->id.','.$data->status.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Status</div>&nbsp;
            //                 <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
            //                 <div onclick="showDataRegistrasi('.$data->id.')" class="btn btn-success waves-effect btn-xs"><i class="mdi mdi-account-plus"></i>&nbsp;Registrasi</div>&nbsp;
            //                 <div onclick="showDataBuktiBayar('.$data->id.')" class="btn btn-warning waves-effect btn-xs"><i class="mdi mdi-upload"></i>&nbsp;Bukti Bayar</div>
            //                 </div>';
            //         }else{
            //             return '<div class="justify-content-center d-flex">
            //                 <div onclick="showChangeStatus('.$data->id.','.$data->status.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Status</div>&nbsp;
            //                 <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
            //                 <div onclick="showDataRegistrasi('.$data->id.')" class="btn btn-success waves-effect btn-xs"><i class="mdi mdi-account-plus"></i>&nbsp;Registrasi</div>&nbsp;
            //                 </div>';
            //         }
            //     }else{
            //         return '<div class="justify-content-center d-flex">
            //             <div onclick="showChangeStatus('.$data->id.','.$data->status.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Status</div>&nbsp;
            //             <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
            //         </div>';
            //     }
            // })
            ->rawColumns(['bukti_bayar','invoice', 'status_bayar'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

}

<?php

namespace App\Http\Controllers\Gada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use \stdClass;
use Illuminate\Support\Facades\Storage;


class TrainingGadaController extends Controller
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
        return view('sdt.gada.list',compact('branch','platform','status','tglDari','tglSampai','request','error','success'));
    }

    public function list (Request $request){
        try {
            $data = DB::table('training_gada_calon')
            ->where('is_active', 1)
            ->select('*', DB::raw("IF(status = 1, 'New Register', IF(status = 2, 'Leads', IF(status = 3, 'Cold Prospect', IF(status = 4, 'Hot Prospect', 'Peserta')))) as status_name"))
            ->get();          
            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
                    <div onclick="showChangeStatus('.$data->id.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Ubah Status</div>
                </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function listLog(Request $request){
        try {
            $pendaftarId =  $request->pendaftar_id;
            // dd($pendaftarId);
            $data = DB::table('training_gada_log')
            ->where('is_active', 1)
            ->where('calon_id', $pendaftarId)
            ->select('status_name', 'keterangan', DB::raw("DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as created_date"))
            ->get();          

            return DataTables::of($data)->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function updateStatus(Request $request) {
        try {
            // dd($request->id.' '.$request->status_id.' '.$request->keterangan);
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            
            // 1. New 
            // 2. Leads
            // 3. Cold Prospect (interview manual by WA)
            // 4. Hot Prospect (dikirim link lanjutan)
            // 5. Peserta

            $statusName = '';
            if($request->status_id == 1){
                $statusName = 'New';
            } else if($request->status_id == 2){
                $statusName = 'Leads';
            } else if($request->status_id == 3){
                $statusName = 'Cold Prospect';
            } else if($request->status_id == 4){
                $statusName = 'Hot Prospect';
            } else if($request->status_id == 5){
                $statusName = 'Peserta';
            }
            
            DB::table('training_gada_calon')->where('id', $request->id)->update([
                'status' => $request->status_id,
                'keterangan' => $request-> keterangan
            ]);

            $logId = DB::table('training_gada_log')->insertGetId([
                'calon_id' => $request->id,
                'status' => $request->status_id,
                'status_name' => $statusName,
                'keterangan' => $request-> keterangan,
                'created_date' => $current_date_time,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->full_name,
                'is_active' => 1
            ]);
            
            $msgSave = 'Status berhasil diubah ';
            DB::commit();
            // return redirect()->back()->with('success', $msgSave);
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil ubah status"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

}

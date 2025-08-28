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
use App\Http\Controllers\Sales\LeadsController;

class SubmissionController extends Controller
{

    public function index (Request $request){
        try {
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

            return view('sales.submission.list',compact('branch','tglDari','tglSampai','request','error','success'));
        } catch (\Exception $e) {
            dd($e);
        }

    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $branch = DB::connection('mysqlhris')->table('m_branch')->where('id','!=',1)->where('is_active',1)->get();
            $jabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
            $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
            $platform = DB::table('m_platform')->whereNull('deleted_at')->where('id','<>',11)->get();
            $provinsi = DB::connection('mysqlhris')->table('m_province')->get();
            $kota = DB::connection('mysqlhris')->table('m_city')->get();
            $kecamatan = DB::connection('mysqlhris')->table('m_district')->get();
            $kelurahan = DB::connection('mysqlhris')->table('m_village')->get();

            return view('sales.submission.add',compact('provinsi','branch','jabatanPic','jenisPerusahaan','kebutuhan','platform','now','kota','kecamatan','kelurahan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();

            $data = DB::table('sl_submission')
                        ->join('m_status_leads','sl_submission.status_leads_id','=','m_status_leads.id')
                        ->leftJoin($db2.'.m_branch','sl_submission.branch_id','=',$db2.'.m_branch.id')
                        ->leftJoin('m_platform','sl_submission.platform_id','=','m_platform.id')
                        ->leftJoin('m_tim_sales_d','sl_submission.tim_sales_d_id','=','m_tim_sales_d.id')
                        ->select('m_tim_sales_d.nama as sales','sl_submission.*', 'm_status_leads.nama as status', $db2.'.m_branch.name as branch', 'm_platform.nama as platform','m_status_leads.warna_background','m_status_leads.warna_font')
                        ->whereNull('sl_submission.deleted_at')
                        ->whereNull('sl_submission.customer_id');

            if(!empty($request->tgl_dari)){
                $data = $data->where('sl_submission.tgl_leads','>=',$request->tgl_dari);
            }else{
                $data = $data->where('sl_submission.tgl_leads','==',carbon::now()->toDateString());
            }
            if(!empty($request->tgl_sampai)){
                $data = $data->where('sl_submission.tgl_leads','<=',$request->tgl_sampai);
            }else{
                $data = $data->where('sl_submission.tgl_leads','==',carbon::now()->toDateString());
            }
            if(!empty($request->branch)){
                $data = $data->where('sl_submission.branch_id',$request->branch);
            }

            $data = $data->get();

            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
            ->addColumn('aksi', function ($data) use ($tim) {
                return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request) {
        try {
            $leadsController = new LeadsController();
            DB::beginTransaction();
            $arrId = $request->id;
            $current_date_time = Carbon::now()->toDateTimeString();
            $nomor = "";

            foreach ($arrId as $key => $value) {
                $submission = DB::table('sl_submission')->where('id',$value)->first();
                if($key==0){
                    $nomor = $leadsController->generateNomor();
                }else{
                    $nomor = $leadsController->generateNomorLanjutan($nomor);
                }

                $newId = DB::table('sl_leads')->insertGetId([
                    'nomor' =>  $nomor,
                    'tgl_leads' => $submission->tgl_leads,
                    'nama_perusahaan' => $submission->nama_perusahaan,
                    'branch_id' => $submission->branch_id,
                    'platform_id' => $submission->platform_id,
                    'kebutuhan_id' =>  $submission->kebutuhan_id,
                    'pic' =>  $submission->pic,
                    'jabatan' =>  $submission->jabatan,
                    'no_telp' => $submission->no_telp,
                    'email' => $submission->email,
                    'status_leads_id' => $submission->status_leads_id,
                    'notes' => $submission->notes,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name,
                ]);

                DB::table('sl_submission')->where('id',$value)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);
            }

            DB::commit();
            return json_encode(['status' => 'success', 'message' => 'Submission berhasil ditambahkan ke leads']);
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

            $arrId = $request->id;
            foreach ($arrId as $key => $value) {
                DB::table('sl_submission')->where('id',$value)->update([
                    'deleted_at' => $current_date_time,
                    'deleted_by' => Auth::user()->full_name
                ]);
            }
            DB::commit();
            return json_encode(['status' => 'success', 'message' => 'Submission berhasil dihapus']);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

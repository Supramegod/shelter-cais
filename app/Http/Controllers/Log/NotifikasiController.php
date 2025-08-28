<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotifikasiController extends Controller
{
    public function index(Request $request){
        DB::table('log_notification')->where('user_id',Auth::user()->id)->update(['is_read'=>1]);
        return view('log.notifikasi.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('log_notification')
                    ->whereNull('log_notification.deleted_at')
                    ->where('user_id',Auth::user()->id)
                    ->get();
            return DataTables::of($data)
                ->editColumn('created_at', function ($data) {
                    return Carbon::parse($data->created_at)->format('d-m-Y H:i:s');
                })
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="#" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-web"></i>&nbsp;Lihat Transaksi</a>&nbsp;
                    </div>';
                })
                ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function read (Request $request){
        try {
            DB::table('log_notification')->where('id',$request->id)->update(['is_read'=>1]);
            return response()->json(['status'=>200,'message'=>'Data berhasil diupdate']);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return response()->json(['status'=>500,'message'=>'Data gagal diupdate']);
        }
    }

}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KebutuhanController extends Controller
{
    public function index(Request $request){

        return view('master.kebutuhan.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_kebutuhan')
                    ->whereNull('deleted_at')
                    ->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('kebutuhan.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>
                    </div>';
                })
                ->addColumn('icon', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <i class="'.$data->icon.'"></i> &nbsp;
                    </div>';
                })
                ->rawColumns(['aksi', 'icon'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view(Request $request,$id){
        try {
            $kebutuhan = DB::table('m_kebutuhan')->where('id', $id)->first();
            
            $listPosition = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->get();

            return view('master.kebutuhan.view',compact('kebutuhan', 'listPosition'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function listDetail(Request $request){
        try {
            $data = DB::table('m_kebutuhan_detail')->where('kebutuhan_id', $request->kebutuhan_id)->whereNull('deleted_at')->get();

            return DataTables::of($data)
                ->addColumn('tunjangan', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <button class="btn-view-tunjangan btn btn-info waves-effect btn-xs" data-id="'.$data->id.'" data-nama="'.$data->nama.'"><i class="mdi mdi-eye"></i>&nbsp;View</button>
                    </div>';
                })
                ->addColumn('requirement', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <button class="btn-view-requirement btn btn-info waves-effect btn-xs" data-id="'.$data->id.'" data-nama="'.$data->nama.'"><i class="mdi mdi-eye"></i>&nbsp;View</button>
                    </div>';
                })
                ->rawColumns(['requirement', 'tunjangan'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // DETAIL TUNJANGAN
    public function listDetailTunjangan(Request $request){
        try {
            $data = DB::table('m_kebutuhan_detail_tunjangan')->where('kebutuhan_id', $request->kebutuhan_id)->whereNull('deleted_at')->get();

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                    </div>';
                })
                ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addDetailTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $nama = $request->nama;
            $nominal = $request->nominal;
            $position_id = 0;

            DB::table('m_kebutuhan_detail_tunjangan')->insert([
                'kebutuhan_id' => $request->kebutuhan_id,
                'position_id' => $position_id,
                'nama' => $nama,
                'nominal' => $nominal,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_kebutuhan_detail_tunjangan')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
            
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // DETAIL REQUIREMENT
    public function listDetailRequirement (Request $request){        
        try {
            $data = DB::table('m_kebutuhan_detail_requirement')->where('kebutuhan_id', $request->kebutuhan_id)->whereNull('deleted_at')->get();

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                    </div>';
                })
                ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            DB::table('m_kebutuhan_detail_requirement')->insert([
                'kebutuhan_id' => $request->kebutuhan_id,
                'position_id' => 0,
                'requirement' => $request->requirement,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Data Berhasil Ditambahkan"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return response()->json([
                'success'   => false,
                'data'      => [],
                'message'   => "Error '.$e.'"
            ], 200);
        }
    }

    public function deleteDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_kebutuhan_detail_requirement')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
            
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

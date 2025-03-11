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

class UmkController extends Controller
{
    public function index(Request $request){

        return view('master.umk.list');
    }

    public function list(Request $request){
        try {
            
            $data = DB::table('m_umk')->where('is_aktif',1)->get();
            return DataTables::of($data)
                ->addColumn('sumber', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                <a target="_blank" href="'.$data->sumber.'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-link"></i></a> &nbsp;
                            </div>';
                })
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('umk.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                    </div>';
                })
                ->rawColumns(['aksi', 'sumber'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function add(Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('master.umk.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_umk')->where('id',$id)->first();

            return view('master.umk.view',compact('data'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function listUmk(Request $request){
        try {
            $data = DB::table('m_umk')->where('city_id',$request->id)->whereNull('deleted_at')->get();
                return DataTables::of($data)
                ->addColumn('sumber', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                <a target="_blank" href="'.$data->sumber.'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-link"></i></a> &nbsp;
                            </div>';
                })
                ->addColumn('is_aktif', function ($data) {
                    if($data->is_aktif == 1){
                        return '<div class="justify-content-center d-flex">
                                    <a href="#" class="btn btn-success waves-effect btn-xs">Active</a> &nbsp;
                                </div>';
                    }else{
                        return '<div class="justify-content-center d-flex">
                                    <a href="#" class="btn btn-warning waves-effect btn-xs">Inactive</a> &nbsp;
                                </div>';
                    }
                })
                ->rawColumns(['sumber', 'is_aktif'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $umk = str_replace("Rp.", "",$request->umk);
            $umk = str_replace(".", "",$umk);
            $umk = str_replace(",", ".",$umk);

            DB::table('m_umk')->where('city_id',$request->city_id)->update([
                'is_aktif' => 0,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::table('m_umk')->insert([
                'city_id'       => $request->city_id,
                'city_name'     => $request->city_name,
                'umk'               => $umk,
                'tgl_berlaku'       => $request->tgl_berlaku,
                'sumber'            => $request->sumber,
                'is_aktif'          => 1,
                'created_at'        => $current_date_time,
                'created_by'        => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }
}

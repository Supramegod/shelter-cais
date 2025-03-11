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

class PlatformController extends Controller
{
    public function index(Request $request){

        return view('master.platform.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_platform')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->addColumn('link', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                        <a href="'.route('contact',['platform' => $data->nama]).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-web"></i></a> &nbsp;
                            </div>';
                })
                ->addColumn('qr', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                        <a target="_blank" href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data='.route('contact',['platform' => $data->nama]).'" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-qrcode"></i></a> &nbsp;
                            </div>';
                })
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('platform.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                        <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                    </div>';
                })
                ->rawColumns(['link','qr', 'aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function add(Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('master.platform.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_platform')->where('id',$id)->first();

            return view('master.platform.view',compact('data'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nama' => 'required',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_platform')->where('id',$request->id)->update([
                        'nama' => $request->nama,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_platform')->insert([
                        'nama' => $request->nama,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Sumber Leads '.$request->nama.' berhasil disimpan.';
            }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_platform')->where('id',$request->id)->update([
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

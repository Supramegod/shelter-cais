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

class JenisPerusahaanController extends Controller
{
    public function index(Request $request){

        return view('master.perusahaan.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->editColumn('nama', function ($data) {
                    return '<a href="'.route('perusahaan.view',$data->id).'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nama.'</a>';
                })
                ->rawColumns(['nama'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function add(Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('master.perusahaan.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_jenis_perusahaan')->where('id',$id)->first();

            return view('master.perusahaan.view',compact('data'));
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
                'resiko' => 'required'
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
                    DB::table('m_jenis_perusahaan')->where('id',$request->id)->update([
                        'nama' => $request->nama,
                        'resiko' => $request->resiko,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_jenis_perusahaan')->insert([
                        'nama' => $request->nama,
                        'resiko' => $request->resiko,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Jenis Perusahaan '.$request->nama.' berhasil disimpan.';
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
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_jenis_perusahaan')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->name
            ]);

            $msgSave = 'Jenis Perusahaan '.$request->nama.' berhasil dihapus.';
            
            DB::commit();
            return redirect()->route('perusahaan')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

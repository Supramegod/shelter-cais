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

class TrainingController extends Controller
{
    public function index(Request $request){

        return view('master.training.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_training')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('training.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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

    public function add(Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('master.training.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_training')->where('id',$id)->first();

            return view('master.training.view',compact('data'));
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
                'jenis' => 'required',
                'jp' => 'required',
                'menit' => 'required',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
                $total = $request->jp * $request->menit;

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_training')->where('id',$request->id)->update([
                        'jenis'         => $request->jenis,
                        'nama'          => $request->nama,
                        'jp'            => $request->jp,
                        'menit'         => $request->menit,
                        'total'         => $total,
                        'updated_at'    => $current_date_time,
                        'updated_by'    => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_training')->insert([
                        'jenis'         => $request->jenis,
                        'nama'          => $request->nama,
                        'jp'            => $request->jp,
                        'menit'         => $request->menit,
                        'total'         => $total,
                        'created_at'    => $current_date_time,
                        'created_by'    => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Training '.$request->nama.' berhasil disimpan.';
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
            DB::table('m_training')->where('id',$request->id)->update([
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

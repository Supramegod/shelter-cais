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

class TrainingGadaJadwalController extends Controller
{
    public function index(Request $request){

        return view('master.training-gada-jadwal.list');
    }

    
    public function list(Request $request){
        try {
            
            $data = DB::table('m_training_gada_jadwal')
                    ->where('is_active', 1)
                    ->get();
            
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('training-gada-jadwal.view',$data->id).'" class="btn-view btn btn-warning waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                        <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>&nbsp;
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

        return view('master.training-gada-jadwal.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_training_gada_jadwal')->where('id',$id)->first();

            return view('master.training-gada-jadwal.view',compact('data'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_training_gada_jadwal')->where('id',$request->id)->update([
                'last_updated' => $current_date_time,
                'updated_who' => Auth::user()->id,
                'is_active' => 0
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

    public function save(Request $request){
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            $msg = '';
            
            if(!empty($request->id)){
                $msg = 'Data Berhasil Diubah';
                
                DB::table('m_training_gada_jadwal')->where('id',$request->id)->update([
                    'jenis_training'=> $request->jenis,
                    'hari'          => $request->hari,
                    'tanggal'       => $request->tanggal,
                    'keterangan'    => $request->keterangan,
                    'updated_who'   => Auth::user()->id,
                    'last_updated'  => $current_date_time
                ]);
            }else{
                DB::table('m_training_gada_jadwal')->insert([
                    'jenis_training'    => $request->jenis,
                    'hari'              => $request->hari,
                    'tanggal'           => $request->tanggal,
                    'keterangan'        => $request->keterangan,
                    'is_active'         => 1,
                    'updated_who'       => Auth::user()->id
                ]);
                $msg = 'Data Berhasil Ditambahkan';
            }
            
            DB::commit();
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }
}

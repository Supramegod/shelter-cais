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

class TrainingMateriController extends Controller
{
    public function index(Request $request){

        return view('master.training-materi.list');
    }

    public function list(Request $request){
        try {
            
            $data = DB::table('m_training_materi as materi')
                    ->leftjoin('m_training_laman as laman','laman.id', '=', 'materi.laman_id')
                    ->leftjoin('sdt_training as st','materi.id', '=', 'st.id_materi')
                    ->select('materi.id', 'materi.materi', 'materi.tujuan', 'materi.kompetensi', 'laman.laman',  DB::raw("count(distinct st.id_training) AS training", 'materi.updated_at'))
                    ->where('materi.is_aktif', 1)
                    ->groupBy('materi.id')
                    ->get();
            
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('training-materi.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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

        return view('master.training-materi.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_training_materi')->where('id',$id)->first();

            return view('master.training-materi.view',compact('data'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_training_materi')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->id,
                'is_aktif' => 0
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
                
                DB::table('m_training_materi')->where('id',$request->id)->update([
                    'materi'        => $request->judul,
                    'tujuan'        => $request->tujuan,
                    'kompetensi'    => $request->kompetensi,
                    'laman_id'      => $request->laman,
                    'user_id'       => Auth::user()->id,
                    'updated_at'    => $current_date_time
                ]);
            }else{
                DB::table('m_training_materi')->insert([
                    'materi'        => $request->judul,
                    'tujuan'        => $request->tujuan,
                    'kompetensi'    => $request->kompetensi,
                    'laman_id'      => $request->laman,
                    'user_id'       => Auth::user()->id
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

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

class TrainingTrainerController extends Controller
{
    public function index(Request $request){

        return view('master.training-trainer.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_training_trainer')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('training-trainer.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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
        $listDivisi = DB::table('m_training_divisi')->where('is_aktif', 1)->orderBy('divisi', 'ASC')->get();

        return view('master.training-trainer.add',compact('now', 'listDivisi'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_training_trainer')->where('id',$id)->first();
            $listDivisi = DB::table('m_training_divisi')->where('is_aktif', 1)->orderBy('divisi', 'ASC')->get();

            return view('master.training-trainer.view',compact('data', 'listDivisi'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'trainer' => 'required'
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
                $total = $request->jp * $request->menit;

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_training_trainer')->where('id',$request->id)->update([
                        'trainer'       => $request->trainer,
                        'divisi_id'     => $request->divisi_id,
                        'updated_at'    => $current_date_time
                    ]);
                }else{
                    DB::table('m_training_trainer')->insert([
                        'trainer'         => $request->trainer,
                        'divisi_id'     => $request->divisi_id,
                        'created_at'    => $current_date_time
                    ]);
                }
                $msgSave = 'Training trainer'.$request->nama.' berhasil disimpan.';
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
            DB::table('m_training_trainer')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->id
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

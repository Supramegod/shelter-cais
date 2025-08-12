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

    public function historyTrainingByMateri(Request $request){
        try {
            // $data = DB::table('m_training_client as mtc')
            //             ->leftjoin('m_training_area as mta','mta.id', '=', 'mtc.area_id')
            //             ->leftJoin('sdt_training_client as stc', 'stc.id_client', '=', DB::raw('mtc.id and stc.is_active = 1'))
            //             ->leftJoin('sdt_training as st', 'st.id_training' ,'=', DB::raw('stc.id_training and st.is_aktif = 1'))
            //             ->leftJoin('sdt_training_client_detail as stcd', 'stcd.client_id', '=', DB::raw('mtc.id and stcd.training_id = st.id_training and stcd.is_active = 1'))
            //             ->leftJoin('m_training_materi as mtm', 'mtm.id', '=', DB::raw('st.id_materi and mtm.is_aktif = 1'))
            //             ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('st.id_training and stt.is_active = 1'))
            //             ->leftJoin('m_training_trainer as mtt', 'mtt.id', '=', DB::raw('stt.id_trainer and mtt.is_aktif = 1'))

            //             ->select(
            //                 "mtm.materi", 
            //                 "st.waktu_mulai", 
            //                 DB::raw("IF(st.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') AS tipe"), 
            //                 DB::raw("IF(st.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') as tempat"),  
            //                 DB::raw("count(distinct stcd.id) AS total_peserta"), 
            //                 DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"))
            //             ->where('mtc.is_aktif', 1)
            //             ->where('st.id_training', '!=', ' null')
            //             ->where('mtm.id', '=', $request->materi_id)
                        
            //             ->groupBy('mtm.materi', 'st.waktu_mulai', 'tipe', 'tempat');

            $data = DB::table('sl_site as site')
            ->leftJoin('sl_leads as lead', 'site.leads_id', '=', 'lead.id')
            ->leftJoin('m_branch as branch', 'branch.id', '=', 'lead.branch_id')
            ->leftJoin('sdt_training_client as stc', function($join) {
                $join->on('stc.id_client', '=', 'site.id')
                    ->where('stc.is_active', '=', 1);
            })
            ->leftJoin('sdt_training as st', function($join) {
                $join->on('st.id_training', '=', 'stc.id_training')
                    ->where('st.is_aktif', '=', 1);
            })
            ->leftJoin('sdt_training_client_detail as stcd', function($join) {
                $join->on('stcd.client_id', '=', 'site.id')
                    ->on('stcd.training_id', '=', 'st.id_training')
                    ->where('stcd.is_active', '=', 1);
            })
            ->leftJoin('m_training as mtm', 'mtm.id', '=', 'st.id_materi')
            ->leftJoin('sdt_training_trainer as stt', function($join) {
                $join->on('stt.id_training', '=', 'st.id_training')
                    ->where('stt.is_active', '=', 1);
            })
            ->leftJoin('m_training_trainer as mtt', function($join) {
                $join->on('mtt.id', '=', 'stt.id_trainer')
                    ->where('mtt.is_aktif', '=', 1);
            })
            ->select(
                'mtm.nama as materi',
                'st.waktu_mulai',
                DB::raw("IF(st.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') AS tipe"),
                DB::raw("IF(st.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') AS tempat"),
                DB::raw('COUNT(DISTINCT stcd.id) AS total_peserta'),
                DB::raw("GROUP_CONCAT(DISTINCT mtt.trainer SEPARATOR ', ') AS trainer")
            )
            ->whereNotNull('st.id_training')
            ->where('mtm.id', $request->materi_id)
            ->groupBy('mtm.nama', 'st.waktu_mulai', 'tipe', 'tempat');
            // ->get();
            
            $data = $data->get();          

            return DataTables::of($data)
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function list(Request $request){
        try {
            
            // $data = DB::table('m_training_materi as materi')
            //         ->leftjoin('m_training_laman as laman','laman.id', '=', 'materi.laman_id')
            //         ->leftjoin('sdt_training as st','materi.id', '=', 'st.id_materi')
            //         ->select('materi.id', 'materi.materi', 'materi.tujuan', 'materi.kompetensi', 'laman.laman',  DB::raw("count(distinct st.id_training) AS training", 'materi.updated_at'))
            //         ->where('materi.is_aktif', 1)
            //         ->groupBy('materi.id')
            //         ->get();

            $data = DB::table('m_training as materi')
            ->leftJoin('sdt_training as st', function($join) {
                $join->on('materi.id', '=', 'st.id_materi')
                    ->where('st.is_aktif', '=', 1);
            })
            ->select(
                'materi.id',
                'materi.jenis',
                'materi.nama',
                DB::raw('COUNT(DISTINCT st.id_training) AS training'),
                'materi.updated_at'
            )
            ->groupBy('materi.id', 'materi.jenis', 'materi.nama', 'materi.updated_at')
            ->get();
            
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('training-materi.view',$data->id).'" class="btn-view btn btn-warning waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                        <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>&nbsp;
                        <div class="btn-detail btn btn-info waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-eye"></i>&nbsp;Detail</div>&nbsp;
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
            $data = DB::table('m_training')->where('id',$id)->first();
            dd($data);
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

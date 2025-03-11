<?php

namespace App\Http\Controllers\Sdt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrainingSiteController extends Controller
{
    public function index(Request $request){
        return view('sdt.training-site.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_training_client as mtc')
            ->leftJoin('m_training_area as mta','mta.id','=', 'mtc.area_id')
            ->leftJoin('sdt_training_client as stc','stc.id_client','=', DB::raw('mtc.id and stc.is_active = 1'))
            ->leftJoin('sdt_training as st', 'st.id_training', '=', DB::raw('stc.id_training and st.is_aktif = 1'))

            ->select("mtc.id", "mtc.client", "mta.area", "mtc.kab_kota", "mtc.tgl_gabung", "mtc.target_per_tahun", DB::raw("count(st.id_training) as jml_training")) 
            ->where('mtc.is_aktif', 1)
            ->groupBy('mtc.id', 'mtc.client', 'mta.area', 'mtc.kab_kota', 'mtc.tgl_gabung', 'mtc.target_per_tahun')
            ->get();

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <div class="btn-detail btn btn-info waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-eye"></i>&nbsp;Detail</div>
                    </div>';
                })
                ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function historyTrainingByClient (Request $request){
        try {
            $data = DB::table('m_training_client as mtc')
                        ->leftjoin('m_training_area as mta','mta.id', '=', 'mtc.area_id')
                        ->leftJoin('sdt_training_client as stc', 'stc.id_client', '=', DB::raw('mtc.id and stc.is_active = 1'))
                        ->leftJoin('sdt_training as st', 'st.id_training' ,'=', DB::raw('stc.id_training and st.is_aktif = 1'))
                        ->leftJoin('sdt_training_client_detail as stcd', 'stcd.client_id', '=', DB::raw('mtc.id and stcd.training_id = st.id_training and stcd.is_active = 1'))
                        ->leftJoin('m_training_materi as mtm', 'mtm.id', '=', DB::raw('st.id_materi and mtm.is_aktif = 1'))
                        ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('st.id_training and stt.is_active = 1'))
                        ->leftJoin('m_training_trainer as mtt', 'mtt.id', '=', DB::raw('stt.id_trainer and mtt.is_aktif = 1'))
                        
                        ->select(
                            "mtm.materi", 
                            "st.waktu_mulai", 
                            DB::raw("IF(st.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') AS tipe"), 
                            DB::raw("IF(st.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') as tempat"),  
                            DB::raw("count(distinct stcd.id) AS total_peserta"), 
                            DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"))
                        ->where('mtc.is_aktif', 1)
                        ->where('st.id_training', '!=', ' null')
                        ->where('mtc.id', '=', $request->client_id)
                        
                        ->groupBy('mtm.materi', 'st.waktu_mulai', 'tipe', 'tempat');
            
            $data = $data->get();          

            return DataTables::of($data)
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }
    
//     public function add(Request $request){
//         $now = Carbon::now()->isoFormat('DD MMMM Y');
//         $listArea = DB::table('m_training_area')->where('is_aktif', 1)->orderBy('area', 'ASC')->get();
//         $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();

//         return view('master.training-client.add',compact('now', 'listArea', 'listBu'));
//     }

//     public function view(Request $request,$id){
//         try {
//             $data = DB::table('m_training_client')->where('id',$id)->first();
//             $listArea = DB::table('m_training_area')->where('is_aktif', 1)->orderBy('area', 'ASC')->get();
//             $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();

//             return view('master.training-client.view',compact('data', 'listArea', 'listBu'));
//         } catch (\Exception $e) {
//             SystemController::saveError($e,Auth::user(),$request);
//             abort(500);
//         }
//     }

//     public function save(Request $request){
//         try {
//             DB::beginTransaction();

//             $validator = Validator::make($request->all(), [
//                 'client' => 'required'
//             ]);
    
//             if ($validator->fails()) {
//                 return back()->withErrors($validator->errors())->withInput();
//             }else{
//                 $current_date_time = Carbon::now()->toDateTimeString();
            
//                 $msgSave = '';
//                 if(!empty($request->id)){
//                     DB::table('m_training_client')->where('id',$request->id)->update([
//                         'client'       => $request->client,
//                         'laman_id'     => $request->laman_id,
//                         'area_id'     => $request->area_id,
//                         'kab_kota'     => $request->kab_kota,
//                         'jml_karyawan'     => $request->jml_karyawan,
//                         'tgl_gabung'     => $request->tgl_gabung,
//                         'target_per_tahun'     => $request->target_per_tahun,
//                         'updated_at'    => $current_date_time
//                     ]);
//                 }else{
//                     DB::table('m_training_client')->insert([
//                         'client'       => $request->client,
//                         'laman_id'     => $request->laman_id,
//                         'area_id'     => $request->area_id,
//                         'kab_kota'     => $request->kab_kota,
//                         'jml_karyawan'     => $request->jml_karyawan,
//                         'tgl_gabung'     => $request->tgl_gabung,
//                         'target_per_tahun'     => $request->target_per_tahun,
//                         'created_at'    => $current_date_time
//                     ]);
//                 }
//                 $msgSave = 'Training client'.$request->client.' berhasil disimpan.';
//             }
//             DB::commit();
//             return redirect()->back()->with('success', $msgSave);
//         } catch (\Exception $e) {
//             SystemController::saveError($e,Auth::user(),$request);
//             abort(500);
//         }
//     }

//     public function delete(Request $request){
//         try {
//             $current_date_time = Carbon::now()->toDateTimeString();
//             DB::table('m_training_client')->where('id',$request->id)->update([
//                 'deleted_at' => $current_date_time,
//                 'is_aktif' => 0
//             ]);

//             return response()->json([
//                 'success'   => true,
//                 'data'      => [],
//                 'message'   => "Berhasil menghapus data"
//             ], 200);
//         } catch (\Exception $e) {
//             SystemController::saveError($e,Auth::user(),$request);
//             abort(500);
//         }
//     }
}

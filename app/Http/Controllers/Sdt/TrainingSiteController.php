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

    // public function list(Request $request){
    //     try {
    //         $data = DB::table('m_training_client as mtc')
    //         ->leftJoin('m_training_area as mta','mta.id','=', 'mtc.area_id')
    //         ->leftJoin('m_training_laman as mtl','mtl.id','=', 'mta.laman_id')
    //         ->leftJoin('sdt_training_client as stc','stc.id_client','=', DB::raw('mtc.id and stc.is_active = 1'))
    //         ->leftJoin('sdt_training as st', 'st.id_training', '=', DB::raw('stc.id_training and st.is_aktif = 1'))

    //         ->select("mtc.id", "mtc.client", "mtl.laman", "mta.area", "mtc.kab_kota", "mtc.tgl_gabung", "mtc.target_per_tahun", DB::raw("count(st.id_training) as jml_training")) 
    //         ->where('mtc.is_aktif', 1)
    //         ->groupBy('mtc.id', 'mtc.client', "mtl.laman", 'mta.area', 'mtc.kab_kota', 'mtc.tgl_gabung', 'mtc.target_per_tahun')
    //         ->get();

    //         return DataTables::of($data)
    //             ->addColumn('aksi', function ($data) {
    //                 return '<div class="justify-content-center d-flex">
    //                     <div class="btn-detail btn btn-info waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-eye"></i>&nbsp;Detail</div>
    //                 </div>';
    //             })
    //             ->rawColumns(['aksi'])
    //         ->make(true);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    public function list(Request $request){
        try {
            $data = DB::table('sl_site as site')
                ->leftJoin('sl_leads as lead', 'site.leads_id', '=', 'lead.id')
                ->leftJoin('sl_pks as pks', 'site.leads_id', '=', 'pks.leads_id')
                ->leftJoin('m_company as company', 'pks.company_id', '=', 'company.id')
                ->leftJoin('m_branch as branch', 'lead.branch_id', '=', 'branch.id')
                ->leftJoin('sdt_training_client as stc','stc.id_client','=', DB::raw('site.id and stc.is_active = 1'))
                ->leftJoin('sdt_training as st', function($join) {
                    $join->on('st.id_training', '=', 'stc.id_training')
                        ->where('st.is_aktif', '=', 1);
                })
                ->select(
                    'site.id',
                    'site.nama_site',
                    'company.name as bu',
                    'branch.name as branch',
                    'site.kota',
                    DB::raw("DATE_FORMAT(pks.created_at, '%d-%m-%Y') as tanggal_gabung"),
                    'site.training_tahun',
                    DB::raw('COUNT(st.id_training) as jml_training')
                )
                ->groupBy(
                    'site.id',
                    'site.nama_site',
                    'company.name',
                    'branch.name',
                    'site.kota',
                    'site.training_tahun',
                    'tanggal_gabung'
                )
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
            // $data = DB::table('m_training_client as mtc')
            //             ->leftjoin('m_training_area as mta','mta.id', '=', 'mtc.area_id')
            //             ->leftJoin('sdt_training_client as stc', 'stc.id_client', '=', DB::raw('mtc.id and stc.is_active = 1'))
            //             ->leftJoin('sdt_training as st', 'st.id_training' ,'=', DB::raw('stc.id_training and st.is_aktif = 1'))
            //             ->leftJoin('sdt_training_client_detail as stcd', 'stcd.client_id', '=', DB::raw('mtc.id and stcd.training_id = st.id_training and stcd.is_active = 1'))
            //             ->leftJoin('m_training_materi as mtm', 'mtm.id', '=', DB::raw('st.id_materi and mtm.is_aktif = 1'))
            //             ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('st.id_training and stt.is_active = 1'))
            //             ->leftJoin('m_training_trainer as mtt', 'mtt.id', '=', DB::raw('stt.id_trainer and mtt.is_aktif = 1'))
                        
            //             ->select(
            //                 "st.id_training",
            //                 "mtm.materi", 
            //                 "st.waktu_mulai", 
            //                 DB::raw("IF(st.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') AS tipe"), 
            //                 DB::raw("IF(st.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') as tempat"),  
            //                 DB::raw("count(distinct stcd.id) AS total_peserta"), 
            //                 DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"))
            //             ->where('mtc.is_aktif', 1)
            //             ->where('st.id_training', '!=', ' null')
            //             ->where('mtc.id', '=', $request->client_id)
                        
            //             ->groupBy('st.id_training', 'mtm.materi', 'st.waktu_mulai', 'tipe', 'tempat');
            
            // $data = $data->get();  
            
            $data = DB::table('sl_site as mtc')
                ->leftJoin('sl_leads as lead', 'mtc.leads_id', '=', 'lead.id')
                ->leftJoin('sdt_training_client as stc', function($join) {
                    $join->on('stc.id_client', '=', 'mtc.id')
                        ->where('stc.is_active', '=', 1);
                })
                ->leftJoin('sdt_training as st', function($join) {
                    $join->on('st.id_training', '=', 'stc.id_training')
                        ->where('st.is_aktif', '=', 1);
                })
                ->leftJoin('sdt_training_client_detail as stcd', function($join) {
                    $join->on('stcd.client_id', '=', 'mtc.id')
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
                    'st.id_training',
                    'mtm.nama as materi',
                    'st.waktu_mulai',
                    DB::raw("IF(st.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') AS tipe"),
                    DB::raw("IF(st.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') AS tempat"),
                    DB::raw('COUNT(DISTINCT stcd.id) AS total_peserta'),
                    DB::raw("GROUP_CONCAT(DISTINCT mtt.trainer SEPARATOR ', ') AS trainer")
                )
                ->whereNotNull('st.id_training')
                ->where('mtc.id', $request->client_id)
                ->groupBy(
                    'st.id_training',
                    'mtm.nama',
                    'st.waktu_mulai',
                    'tipe',
                    'tempat'
                )
                ->get();

            return DataTables::of($data)
            ->addColumn('report', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <div id="btn-report" class="btn-report btn btn-warning waves-effect btn-xs" data-id="'.$data->id_training.'"><i class="mdi mdi-file-pdf-box"></i>&nbsp;Report</div>
                </div>';
            })
            ->rawColumns(['report'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

}

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

class TrainingClientController extends Controller
{
    public function index(Request $request){

        return view('master.training-client.list');
    }

    public function list(Request $request){
        try {
            // $data = DB::table('m_training_client as cl')
            // ->leftJoin('m_training_area as area','area.id','=', 'cl.area_id')
            // ->select("cl.id", "cl.client", "area.area as area_name", "cl.kab_kota", "cl.tgl_gabung", "cl.jml_karyawan", "cl.target_per_tahun") 
            // ->where('cl.is_aktif', 1)
            // ->get();

            $data = DB::table('sl_site as site')
            ->leftJoin('sl_leads as lead', 'site.leads_id', '=', 'lead.id')
            ->leftJoin('sl_pks as pks', 'site.leads_id', '=', 'pks.leads_id')
            ->leftJoin('m_branch as branch', 'lead.branch_id', '=', 'branch.id')
            ->select(
                'site.id',
                'site.nama_site as client',
                'branch.name as area_name',
                'site.kota as kab_kota',
                DB::raw("DATE_FORMAT(pks.created_at, '%d-%m-%Y') as tgl_gabung"),
                'site.training_tahun as target_per_tahun'
            )
            ->whereNull('site.deleted_at')
            ->get();

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('training-client.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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
        $listArea = DB::table('m_training_area')->where('is_aktif', 1)->orderBy('area', 'ASC')->get();
        $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();

        return view('master.training-client.add',compact('now', 'listArea', 'listBu'));
    }

    public function view(Request $request,$id){
        try {
            // $data = DB::table('sl_site')->where('id',$id)->first();
            $data = DB::table('sl_site as site')
            ->leftJoin('sl_leads as lead', 'site.leads_id', '=', 'lead.id')
            ->leftJoin('sl_pks as pks', 'site.leads_id', '=', 'pks.leads_id')
            ->leftJoin('m_branch as branch', 'lead.branch_id', '=', 'branch.id')
            ->leftJoin('m_company as company', 'pks.company_id', '=', 'company.id')
            ->select(
                'site.id',
                'site.nama_site',
                'branch.name as area',
                'site.kota',
                'company.name as bu',
                DB::raw("DATE_FORMAT(pks.created_at, '%d-%m-%Y') as tgl_gabung"),
                'site.training_tahun as training_tahun'
            )
            ->where('site.id', $id)
            ->first();

            $listArea = DB::table('m_training_area')->where('is_aktif', 1)->orderBy('area', 'ASC')->get();
            $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();

            return view('master.training-client.view',compact('data', 'listArea', 'listBu'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'target_per_tahun' => 'required'
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
            
                $msgSave = '';
                if(!empty($request->id)){
                    // DB::table('m_training_client')->where('id',$request->id)->update([
                    //     'client'       => $request->client,
                    //     'laman_id'     => $request->laman_id,
                    //     'area_id'     => $request->area_id,
                    //     'kab_kota'     => $request->kab_kota,
                    //     'jml_karyawan'     => $request->jml_karyawan,
                    //     'tgl_gabung'     => $request->tgl_gabung,
                    //     'target_per_tahun'     => $request->target_per_tahun,
                    //     'updated_at'    => $current_date_time
                    // ]);

                    DB::table('sl_site')->where('id',$request->id)->update([
                        'training_tahun'     => $request->target_per_tahun
                    ]);
                }else{
                    // DB::table('m_training_client')->insert([
                    //     'client'       => $request->client,
                    //     'laman_id'     => $request->laman_id,
                    //     'area_id'     => $request->area_id,
                    //     'kab_kota'     => $request->kab_kota,
                    //     'jml_karyawan'     => $request->jml_karyawan,
                    //     'tgl_gabung'     => $request->tgl_gabung,
                    //     'target_per_tahun'     => $request->target_per_tahun,
                    //     'created_at'    => $current_date_time
                    // ]);
                }
                $msgSave = 'Training client '.$request->client.' berhasil disimpan.';
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
            DB::table('m_training_client')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
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
}

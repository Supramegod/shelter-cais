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

class TunjanganController extends Controller
{
    public function index(Request $request){

        return view('master.tunjangan.list');
    }

    public function list(Request $request){
        try {
            
            $db2 = DB::connection('mysqlhris')->getDatabaseName();

            $data = DB::table('m_tunjangan_posisi')
                        ->join('m_kebutuhan','m_tunjangan_posisi.kebutuhan_id','=','m_kebutuhan.id')
                        ->leftJoin($db2.'.m_position','m_tunjangan_posisi.position_id','=',$db2.'.m_position.id')
                        ->select('m_tunjangan_posisi.id','m_kebutuhan.nama as nama_kebutuhan',$db2.'.m_position.name as nama_jabatan','m_tunjangan_posisi.nama as nama_tunjangan','m_tunjangan_posisi.nominal','m_tunjangan_posisi.created_at','m_tunjangan_posisi.created_by')
                        ->whereNull('m_tunjangan_posisi.deleted_at')
                        ->get();

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('tunjangan.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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

        return view('master.tunjangan.add',compact('now'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_tunjangan_posisi')->where('id',$id)->first();
            $listKebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
            $listPosition = DB::connection('mysqlhris')->table('m_position')->where('is_active',1)->get();

            return view('master.tunjangan.view',compact('data', 'listPosition', 'listKebutuhan'));
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
                $nominal = str_replace(",", "",$request->nominal);

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_tunjangan_posisi')->where('id',$request->id)->update([
                        'kebutuhan_id' => $request->kebutuhan_id,
                        'position_id' => $request->position_id,
                        'nama' => $request->nama,
                        'nominal'   => $nominal,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_tunjangan_posisi')->insert([
                        'kebutuhan_id' => $request->kebutuhan_id,
                        'position_id' => $request->position_id,
                        'nama' => $request->nama,
                        'nominal'   => $nominal,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Tunjangan : '.$request->nama.' berhasil disimpan.';
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
            DB::table('m_tunjangan_posisi')->where('id',$request->id)->update([
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

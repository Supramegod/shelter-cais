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

class TunjanganJabatanController extends Controller
{
    public function index(Request $request){

        return view('master.tunjangan-jabatan.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_kebutuhan_detail_tunjangan')
                    ->join('m_kebutuhan', 'm_kebutuhan_detail_tunjangan.kebutuhan_id', '=', 'm_kebutuhan.id')
                    ->join('m_kebutuhan_detail', 'm_kebutuhan_detail_tunjangan.kebutuhan_detail_id', '=', 'm_kebutuhan_detail.id')
                    ->join('m_tunjangan', 'm_kebutuhan_detail_tunjangan.tunjangan_id', '=', 'm_tunjangan.id')
                    ->select('m_kebutuhan_detail_tunjangan.*', 'm_kebutuhan.nama as nama_kebutuhan', 'm_kebutuhan_detail.nama as nama_kebutuhan_detail', 'm_tunjangan.nama as nama_tunjangan')
                    ->whereNull('m_kebutuhan_detail_tunjangan.deleted_at')
                    ->get();
            return DataTables::of($data)
                ->editColumn('nama', function ($data) {
                    return '<a href="'.route('tunjangan-jabatan.view',$data->id).'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nama.'</a>';
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
        $listKebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->orderBy('id', 'ASC')->get();
        $listKebutuhanDetail = DB::table('m_kebutuhan_detail')->whereNull('deleted_at')->orderBy('id', 'ASC')->get();
        $listTunjangan = DB::table('m_tunjangan')->whereNull('deleted_at')->orderBy('id', 'ASC')->get();

        return view('master.tunjangan-jabatan.add',compact('now', 'listKebutuhan', 'listKebutuhanDetail', 'listTunjangan'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_kebutuhan_detail_tunjangan')
                    ->join('m_kebutuhan', 'm_kebutuhan_detail_tunjangan.kebutuhan_id', '=', 'm_kebutuhan.id')
                    ->join('m_kebutuhan_detail', 'm_kebutuhan_detail_tunjangan.kebutuhan_detail_id', '=', 'm_kebutuhan_detail.id')
                    ->join('m_tunjangan', 'm_kebutuhan_detail_tunjangan.tunjangan_id', '=', 'm_tunjangan.id')
                    ->select('m_kebutuhan_detail_tunjangan.*', 'm_kebutuhan.nama as nama_kebutuhan', 'm_kebutuhan_detail.nama as nama_kebutuhan_detail', 'm_tunjangan.nama as nama_tunjangan')
                    ->where('m_kebutuhan_detail_tunjangan.id', $id)
                    ->first();
            $listKebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
            $listKebutuhanDetail = DB::table('m_kebutuhan_detail')->whereNull('deleted_at')->get();
            $listTunjangan = DB::table('m_tunjangan')->whereNull('deleted_at')->get();

            return view('master.tunjangan-jabatan.view',compact('data', 'listKebutuhan', 'listKebutuhanDetail', 'listTunjangan'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function getKebutuhanDetail(Request $request){
        try {
            $data = DB::table('m_kebutuhan_detail')->where('kebutuhan_id',$request->kebutuhan_id)->first();

            return $data;
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
                'kebutuhan_id' => 'required',
                'kebutuhan_detail_id' => 'required',
                'tunjangan_id' => 'required',
                'nominal' => 'required',
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
                    DB::table('m_kebutuhan_detail_tunjangan')->where('id',$request->id)->update([
                        'nama' => $request->nama,
                        'tunjangan_id' => $request->tunjangan_id,
                        'nominal' => $nominal,
                        'kebutuhan_detail_id' => $request->kebutuhan_detail_id,
                        'kebutuhan_id' => $request->kebutuhan_id,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_kebutuhan_detail_tunjangan')->insert([
                        'nama' => $request->nama,
                        'tunjangan_id' => $request->tunjangan_id,
                        'nominal' => $nominal,
                        'kebutuhan_detail_id' => $request->kebutuhan_detail_id,
                        'kebutuhan_id' => $request->kebutuhan_id,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Tunjangan Jabatan '.$request->nama.' berhasil disimpan.';
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
            DB::table('m_kebutuhan_detail_tunjangan')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->name
            ]);

            $msgSave = 'Tunjangan Jabatan '.$request->nama.' berhasil dihapus.';
            
            DB::commit();
            return redirect()->route('tunjangan-jabatan')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

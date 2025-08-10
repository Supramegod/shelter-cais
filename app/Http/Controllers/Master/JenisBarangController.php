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

class JenisBarangController extends Controller
{
    public function index(Request $request){

        return view('master.jenis-barang.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_jenis_barang')->whereNull('deleted_at')->get();
              foreach ($data as $key => $value) {
                $value->created_at =  Carbon::parse($value->created_at)->isoFormat('D MMMM Y');
            }
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('jenis-barang.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                    </div>';
                })
                ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function detailBarang(Request $request){
        try {
            $data = DB::table('m_jenis_barang')
                    ->join('m_barang', 'm_jenis_barang.id', '=', 'm_barang.jenis_barang_id')
                    ->select('m_jenis_barang.nama as jenis_barang', 'm_barang.nama as nama_barang', 'm_barang.harga as harga', 'm_barang.satuan as satuan', 'm_barang.masa_pakai as masa_pakai', 'm_barang.merk as merk')
                    ->where('m_jenis_barang.id',$request->id)
                    ->whereNull('m_jenis_barang.deleted_at')
                    ->get();
            return DataTables::of($data)->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_jenis_barang')->where('id',$id)->first();

            return view('master.jenis-barang.view',compact('data'));
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
                $harga = str_replace(",", "",$request->harga);

                $msgSave = '';
                DB::table('m_jenis_barang')->insert([
                    'nama' => $request->nama,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
                $msgSave = 'Jenis Barang '.$request->nama.' berhasil disimpan.';
            }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

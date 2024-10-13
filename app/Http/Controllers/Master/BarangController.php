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

class BarangController extends Controller
{
    public function index(Request $request){

        return view('master.barang.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_barang')
                    ->join('m_jenis_barang', 'm_barang.jenis_barang_id', '=', 'm_jenis_barang.id')
                    ->select('m_barang.*', 'm_jenis_barang.nama as nama_jenis_barang')
                    ->whereNull('m_barang.deleted_at')
                    ->get();
            return DataTables::of($data)
                ->editColumn('nama', function ($data) {
                    return '<a href="'.route('barang.view',$data->id).'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nama.'</a>';
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
        $listJenisBarang = DB::table('m_jenis_barang')->whereNull('deleted_at')->orderBy('id', 'ASC')->get();

        return view('master.barang.add',compact('now', 'listJenisBarang'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_barang')->where('id', $id)->first();
            $listJenisBarang = DB::table('m_jenis_barang')->whereNull('deleted_at')->orderBy('id', 'ASC')->get();

            return view('master.barang.view',compact('data', 'listJenisBarang'));
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
                'jenis_barang_id' => 'required',
                'harga' => 'required',
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
                $jenisBarang = DB::table('m_jenis_barang')->where('id',$request->jenis_barang_id)->first();

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_barang')->where('id',$request->id)->update([
                        'nama' => $request->nama,
                        'jenis_barang_id' => $request->jenis_barang_id,
                        'jenis_barang' => $jenisBarang->nama,
                        'harga' => $harga,
                        'satuan' => $request->satuan,
                        'masa_pakai' => $request->masa_pakai,
                        'merk' => $request->merk,
                        'jumlah_default' => $request->jumlah_default,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_barang')->insert([
                        'nama' => $request->nama,
                        'jenis_barang_id' => $request->jenis_barang_id,
                        'jenis_barang' => $jenisBarang->nama,
                        'harga' => $harga,
                        'satuan' => $request->satuan,
                        'masa_pakai' => $request->masa_pakai,
                        'merk' => $request->merk,
                        'jumlah_default' => $request->jumlah_default,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Barang '.$request->nama.' berhasil disimpan.';
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
            DB::table('m_barang')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->name
            ]);

            $msgSave = 'Barang '.$request->nama.' berhasil dihapus.';
            
            DB::commit();
            return redirect()->route('barang')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

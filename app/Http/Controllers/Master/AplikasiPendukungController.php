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

class AplikasiPendukungController extends Controller
{
    public function index(Request $request){

        return view('master.aplikasi-pendukung.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_aplikasi_pendukung')
                    ->join('m_barang', 'm_aplikasi_pendukung.barang_id', '=', 'm_barang.id')
                    ->select('m_aplikasi_pendukung.*', 'm_barang.nama as nama_barang')
                    ->whereNull('m_aplikasi_pendukung.deleted_at')
                    ->get();
            return DataTables::of($data)
                ->addColumn('link_icon', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                <a target="_blank" href="'.$data->link_icon.'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-image"></i></a> &nbsp;
                            </div>';
                })
                ->editColumn('nama', function ($data) {
                    return '<a href="'.route('aplikasi-pendukung.view',$data->id).'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nama.'</a>';
                })
                ->rawColumns(['nama', 'link_icon'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function add(Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $listBarang = DB::table('m_barang')->whereNull('deleted_at')->get();

        return view('master.aplikasi-pendukung.add',compact('now', 'listBarang'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_aplikasi_pendukung')->where('id',$id)->first();
            $listBarang = DB::table('m_barang')->whereNull('deleted_at')->get();

            return view('master.aplikasi-pendukung.view',compact('data', 'listBarang'));
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
                'harga' => 'required',
                'barang_id' => 'required',
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
                if(!empty($request->id)){
                    DB::table('m_aplikasi_pendukung')->where('id',$request->id)->update([
                        'nama' => $request->nama,
                        'link_icon' => $request->link_icon,
                        'harga' => $harga,
                        'barang_id' => $request->barang_id,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_aplikasi_pendukung')->insert([
                        'nama' => $request->nama,
                        'link_icon' => $request->link_icon,
                        'harga' => $harga,
                        'barang_id' => $request->barang_id,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Aplikasi Pendukung '.$request->nama.' berhasil disimpan.';
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
            DB::table('m_aplikasi_pendukung')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->name
            ]);

            $msgSave = 'Aplikasi Pendukung '.$request->nama.' berhasil dihapus.';
            
            DB::commit();
            return redirect()->route('aplikasi-pendukung')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

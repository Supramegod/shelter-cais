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

class KebutuhanController extends Controller
{
    public function index(Request $request){

        return view('master.kebutuhan.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_kebutuhan')
                    ->whereNull('deleted_at')
                    ->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('kebutuhan.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>
                    </div>';
                })
                ->addColumn('icon', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <i class="'.$data->icon.'"></i> &nbsp;
                    </div>';
                })
                ->rawColumns(['aksi', 'icon'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view(Request $request,$id){
        try {
            $kebutuhan = DB::table('m_kebutuhan')->where('id', $id)->first();
            $detailKebutuhan = DB::table('m_kebutuhan_detail')->where('kebutuhan_id', $id)->whereNull('deleted_at')->get();

            return view('master.kebutuhan.view',compact('detailKebutuhan', 'kebutuhan'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // TAMBAH DETAIL
    public function addDetail(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $nama = $request->nama;
            $kebutuhanId = $request->kebutuhan_id;
            $maxUrutan = DB::select("select MAX(urutan) as max from m_kebutuhan_detail WHERE kebutuhan_id = ".$kebutuhanId."")[0]->max;

            DB::table('m_kebutuhan_detail')->insert([
                'kebutuhan_id' => $kebutuhanId,
                'nama' => $nama,
                'urutan' => $maxUrutan + 1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    // DETAIL TUNJANGAN
    public function listDetailTunjangan (Request $request){        
        $data = DB::table('m_kebutuhan_detail_tunjangan')
        ->where('kebutuhan_detail_id',$request->kebutuhan_detail_id)
        ->whereNull('deleted_at')
        ->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete-tunjangan btn btn-danger waves-effect btn-xs" data-detail="'.$data->kebutuhan_detail_id.'" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function addDetailTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $nama = $request->nama;
            $nominal = $request->nominal;
            $kebutuhanDetailId = $request->kebutuhan_detail_id;

            $data = DB::table('m_kebutuhan_detail')->where('id',$kebutuhanDetailId)->first();
            DB::table('m_kebutuhan_detail_tunjangan')->insert([
                'kebutuhan_id' => $data->kebutuhan_id,
                'kebutuhan_detail_id' => $data->id,
                'nama' => $nama,
                'nominal' => $nominal,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailTunjangan(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_kebutuhan_detail_tunjangan')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // DETAIL REQUIREMENT
    public function listDetailRequirement (Request $request){        
        $data = DB::table('m_kebutuhan_detail_requirement')
        ->where('kebutuhan_detail_id',$request->kebutuhan_detail_id)
        ->whereNull('deleted_at')
        ->get();

        foreach ($data as $key => $value) {
            $value->nomor = $key+1;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete-requirement btn btn-danger waves-effect btn-xs" data-detail="'.$data->kebutuhan_detail_id.'" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function addDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $requirement = $request->requirement;
            $kebutuhanDetailId = $request->kebutuhan_detail_id;

            $data = DB::table('m_kebutuhan_detail')->where('id',$kebutuhanDetailId)->first();
            DB::table('m_kebutuhan_detail_requirement')->insert([
                'kebutuhan_id' => $data->kebutuhan_id,
                'kebutuhan_detail_id' => $data->id,
                'requirement' => $requirement,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailRequirement(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_kebutuhan_detail_requirement')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    // public function save(Request $request){
    //     try {
    //         DB::beginTransaction();

    //         $validator = Validator::make($request->all(), [
    //             'nama' => 'required',
    //             'jenis_barang_id' => 'required',
    //             'harga' => 'required',
    //         ], [
    //             'min' => 'Masukkan :attribute minimal :min',
    //             'max' => 'Masukkan :attribute maksimal :max',
    //             'required' => ':attribute harus di isi',
    //         ]);
    
    //         if ($validator->fails()) {
    //             return back()->withErrors($validator->errors())->withInput();
    //         }else{
    //             $current_date_time = Carbon::now()->toDateTimeString();
    //             $harga = str_replace(",", "",$request->harga);
    //             $jenisBarang = DB::table('m_jenis_barang')->where('id',$request->jenis_barang_id)->first();

    //             $msgSave = '';
    //             if(!empty($request->id)){
    //                 DB::table('m_barang')->where('id',$request->id)->update([
    //                     'nama' => $request->nama,
    //                     'jenis_barang_id' => $request->jenis_barang_id,
    //                     'jenis_barang' => $jenisBarang->nama,
    //                     'harga' => $harga,
    //                     'satuan' => $request->satuan,
    //                     'masa_pakai' => $request->masa_pakai,
    //                     'merk' => $request->merk,
    //                     'jumlah_default' => $request->jumlah_default,
    //                     'updated_at' => $current_date_time,
    //                     'updated_by' => Auth::user()->full_name
    //                 ]);
    //             }else{
    //                 DB::table('m_barang')->insert([
    //                     'nama' => $request->nama,
    //                     'jenis_barang_id' => $request->jenis_barang_id,
    //                     'jenis_barang' => $jenisBarang->nama,
    //                     'harga' => $harga,
    //                     'satuan' => $request->satuan,
    //                     'masa_pakai' => $request->masa_pakai,
    //                     'merk' => $request->merk,
    //                     'jumlah_default' => $request->jumlah_default,
    //                     'created_at' => $current_date_time,
    //                     'created_by' => Auth::user()->full_name
    //                 ]);
    //             }
    //             $msgSave = 'Barang '.$request->nama.' berhasil disimpan.';
    //         }
    //         DB::commit();
    //         return redirect()->back()->with('success', $msgSave);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    // public function delete(Request $request){
    //     try {
    //         DB::beginTransaction();

    //         $current_date_time = Carbon::now()->toDateTimeString();
    //         DB::table('m_barang')->where('id',$request->id)->update([
    //             'deleted_at' => $current_date_time,
    //             'deleted_by' => Auth::user()->name
    //         ]);

    //         $msgSave = 'Barang '.$request->nama.' berhasil dihapus.';
            
    //         DB::commit();
    //         return redirect()->route('barang')->with('success', $msgSave);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }
}

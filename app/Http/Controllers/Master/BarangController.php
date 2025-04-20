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
use App\Exports\BarangTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use \stdClass;

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
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('barang.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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
                        'urutan' => $request->urutan,
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
                        'urutan' => $request->urutan,
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
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_barang')->where('id',$request->id)->update([
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

    public function templateImport(Request $request) {
        $dt = Carbon::now()->toDateTimeString();

        return Excel::download(new BarangTemplateExport(), 'Template Barang-'.$dt.'.xlsx');
    }

    public function import (Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('master.barang.import',compact('now'));
    }
    public function inquiryImport(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:csv,xls,xlsx',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
                'mimes' => 'tipe file harus csv,xls atau xlsx',
            ]);

            $array = null;
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $file = $request->file('file');
                $current_date_time = Carbon::now()->toDateTimeString();
                $importId = uniqid();

                // Get the csv rows as an array
                $array = Excel::toArray(new stdClass(), $file);
                $jumlahError = 0;
                $jumlahWarning = 0;
                $jumlahSuccess = 0;
                foreach ($array[0] as $key => $v) {
                    if($key==0){
                        continue;
                    };
                    if ($v[1] == null) {
                        continue;
                    }

                    $barangId = $v[0];
                    $namaBarang = $v[1];
                    $jenisBarangId = $v[2];
                    $jenisBarang = $v[3];
                    $harga = $v[4];
                    $satuan = $v[5];
                    $masaPakai = $v[6];
                    $merk = $v[7];
                    $jumlahDefault = $v[8];
                    $urutan = $v[9];

                    $jenisBarang = DB::table('m_jenis_barang')->where('id',$jenisBarangId)->first();
                    $arrayBarang = [
                        'import_id' => $importId,
                        'barang_id' => $barangId,
                        'nama' => $namaBarang,
                        'jenis_barang_id' => $jenisBarangId,
                        'jenis_barang' => $jenisBarang != null ? $jenisBarang->nama : null,
                        'harga' => $harga,
                        'satuan' => $satuan,
                        'masa_pakai' => $masaPakai,
                        'merk' => $merk,
                        'jumlah_default' => $jumlahDefault,
                        'urutan' => $urutan,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ];

                    DB::table('m_barang_import')->insert($arrayBarang);

                    $jumlahSuccess++;
                }
            }
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            DB::commit();
            $datas = DB::table('m_barang_import')->where('import_id',$importId)->get();
            return view('master.barang.inquiry',compact('importId','datas','now','jumlahError','jumlahSuccess','jumlahWarning'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveImport(Request $request){
        DB::beginTransaction();
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $importId = $request->importId;
            $datas = DB::table('m_barang_import')->where('import_id',$importId)->get();
            foreach ($datas as $data) {
                $arrBarang = [
                    'nama' => $data->nama,
                    'jenis_barang_id' => $data->jenis_barang_id,
                    'jenis_barang' => $data->jenis_barang,
                    'harga' => $data->harga,
                    'satuan' => $data->satuan,
                    'masa_pakai' => $data->masa_pakai,
                    'merk' => $data->merk,
                    'jumlah_default' => $data->jumlah_default,
                    'urutan' => $data->urutan
                ];



                if($data->barang_id != null){
                    $arrBarang['updated_at'] = $current_date_time;
                    $arrBarang['updated_by'] = Auth::user()->full_name;

                    DB::table('m_barang')->where('id',$data->barang_id)->update($arrBarang);
                }else{
                    $arrBarang['created_at'] = $current_date_time;
                    $arrBarang['created_by'] = Auth::user()->full_name;

                    DB::table('m_barang')->insert($arrBarang);
                }
            }

            $msgSave = 'Import Barang berhasil Dilakukan !';

            DB::commit();
            return redirect()->route('barang')->with('success', $msgSave);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

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

class SalaryRuleController extends Controller
{
    public function index(Request $request){

        return view('master.salary-rule.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_salary_rule')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                        <a href="'.route('salary-rule.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
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

        return view('master.salary-rule.add',compact('now'));
    }
    

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_salary_rule')->where('id',$id)->first();

            return view('master.salary-rule.view',compact('data'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nama_salary_rule' => 'required',
                'cutoff_awal' => 'required',
                'cutoff_akhir' => 'required',
                'crosscheck_absen_awal' => 'required',
                'crosscheck_absen_akhir' => 'required',
                'pengiriman_invoice_awal' => 'required',
                'pengiriman_invoice_akhir' => 'required',
                'perkiraan_invoice_diterima_awal' => 'required',
                'perkiraan_invoice_diterima_akhir' => 'required',
                'pembayaran_invoice' => 'required',
                'rilis_payroll' => 'required',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
                $cutoff = "Tanggal ".$request->cutoff_awal." - ".$request->cutoff_akhir;
                $crosscheckAbsen = "Tanggal ".$request->crosscheck_absen_awal." - ".$request->crosscheck_absen_akhir;
                $pengirimanInvoice = "Tanggal ".$request->pengiriman_invoice_awal." - ".$request->pengiriman_invoice_akhir;
                $perkiraanInvoiceDiterima = "Tanggal ".$request->perkiraan_invoice_diterima_awal." - ".$request->perkiraan_invoice_diterima_akhir;
                $pembayaranInvoice = "Tanggal ".$request->pembayaran_invoice." bulan berikutnya";
                $rilisPayroll = "Tanggal ".$request->rilis_payroll." bulan berikutnya";

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_salary_rule')->where('id',$request->id)->update([
                        'nama_salary_rule' => $request->nama_salary_rule,
                        'cutoff' => $cutoff,
                        'cutoff_awal' => $request->cutoff_awal,
                        'cutoff_akhir' => $request->cutoff_akhir,
                        'crosscheck_absen' => $crosscheckAbsen,
                        'crosscheck_absen_awal' => $request->crosscheck_absen_awal,
                        'crosscheck_absen_akhir' => $request->crosscheck_absen_akhir,
                        'pengiriman_invoice' => $pengirimanInvoice,
                        'pengiriman_invoice_awal' => $request->pengiriman_invoice_awal,
                        'pengiriman_invoice_akhir' => $request->pengiriman_invoice_akhir,
                        'perkiraan_invoice_diterima' => $perkiraanInvoiceDiterima,
                        'perkiraan_invoice_diterima_awal' => $request->perkiraan_invoice_diterima_awal,
                        'perkiraan_invoice_diterima_akhir' => $request->perkiraan_invoice_diterima_akhir,
                        'pembayaran_invoice' => $pembayaranInvoice,
                        'tgl_pembayaran_invoice' => $request->pembayaran_invoice,
                        'rilis_payroll' => $rilisPayroll,
                        'tgl_rilis_payroll' => $request->rilis_payroll,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_salary_rule')->insert([
                        'nama_salary_rule' => $request->nama_salary_rule,
                        'cutoff' => $cutoff,
                        'cutoff_awal' => $request->cuttoff_awal,
                        'cutoff_akhir' => $request->cuttoff_akhir,
                        'crosscheck_absen' => $crosscheckAbsen,
                        'crosscheck_absen_awal' => $request->crosscheck_absen_akhir,
                        'crosscheck_absen_akhir' => $request->crosscheck_absen_akhir,
                        'pengiriman_invoice' => $pengirimanInvoice,
                        'pengiriman_invoice_awal' => $request->pengiriman_invoice_awal,
                        'pengiriman_invoice_akhir' => $request->pengiriman_invoice_akhir,
                        'perkiraan_invoice_diterima' => $perkiraanInvoiceDiterima,
                        'perkiraan_invoice_diterima_awal' => $request->perkiraan_invoice_diterima_awal,
                        'perkiraan_invoice_diterima_akhir' => $request->perkiraan_invoice_diterima_akhir,
                        'pembayaran_invoice' => $pembayaranInvoice,
                        'tgl_pembayaran_invoice' => $request->pembayaran_invoice,
                        'rilis_payroll' => $rilisPayroll,
                        'tgl_rilis_payroll' => $request->rilis_payroll,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Salary Rule '.$request->nama_salary_rule.' berhasil disimpan.';
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
            DB::table('m_salary_rule')->where('id',$request->id)->update([
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

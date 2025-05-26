<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringKontrakTemplateExport;
use \stdClass;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Helper\QuotationService;

class PutusKontrakController extends Controller
{
    public function index (Request $request){
        $tglDari = $request->tgl_dari;
        $tglSampai = $request->tgl_sampai;

        if($tglDari==null){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
        }
        if($tglSampai==null){
            $tglSampai = carbon::now()->toDateString();
        }

        $ctglDari = Carbon::createFromFormat('Y-m-d',  $tglDari);
        $ctglSampai = Carbon::createFromFormat('Y-m-d',  $tglSampai);

        return view('sales.putus-kontrak.list',compact('tglDari','tglSampai'));
    }

    public function list (Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $data = DB::table('sl_putus_kontrak')->select([
            'id',
            'nomor_pks',
            'nama_perusahaan',
            'alamat',
            'crm',
            'bm',
            'ro',
            'layanan',
            'jumlah_hc',
            'kronologi',
            'tindakan',
            'created_at',
            'created_by',
        ])
        ->whereNull('deleted_at');

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<a href="' . route('putus-kontrak.view', $data->id) . '" class="btn btn-sm btn-info" title="View"><i class="mdi mdi-magnify"></i></a>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            return view('sales.putus-kontrak.add',compact('now'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addPutusKontrak ($id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $pks = DB::table('sl_pks')
                ->whereNull('deleted_at')
                ->where('id',$id)
                ->first();
            if($pks==null){
                return redirect()->back()->with('error','Data PKS tidak ditemukan');
            }

            $pks->bm = "";
            $pks->awal_kontrak = "";
            $pks->akhir_kontrak = "";
            $pks->nominal_invoice = 0;
            return view('sales.putus-kontrak.add-putus-kontrak',compact('now','pks'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function availableKontrak (Request $request){
        try {
            $data = DB::table('sl_pks')
                ->select('id', 'nomor', 'nama_perusahaan', 'nama_site', 'nama_proyek')
                ->whereNull('deleted_at')
                ->whereNotIn('id', function ($query) {
                    $query->select('pks_id')
                        ->from('sl_putus_kontrak')
                        ->whereNull('deleted_at');
                })
                ->get();

            return DataTables::of($data)
            ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            $id = $request->pks_id;
            $pks = DB::table('sl_pks')
                ->whereNull('deleted_at')
                ->where('id',$id)
                ->first();
            if($pks==null){
                return redirect()->back()->with('error','Data PKS tidak ditemukan');
            }

            DB::table('sl_putus_kontrak')->insert([
                'tgl' => Carbon::now()->toDateString(),
                'leads_id' => $pks->leads_id,
                'pks_id' => $pks->id,
                'site_id' => $pks->site_id,
                'crm_id' => $pks->crm_id,
                'bm_id' => null,
                'crm' => $pks->crm,
                'bm' => null,
                'nama_perusahaan' => $pks->nama_perusahaan,
                'alamat' => $pks->alamat_perusahaan,
                'nomor_pks' => $pks->nomor,
                'awal_kontrak' => $pks->kontrak_awal ? Carbon::createFromFormat('Y-m-d', $pks->kontrak_awal)->toDateString() : null,
                'akhir_kontrak' => $pks->kontrak_akhir ? Carbon::createFromFormat('Y-m-d', $pks->kontrak_akhir)->toDateString() : null,
                'ro_id' => $pks->ro_id,
                'ro' => $pks->ro,
                'layanan_id' => $pks->layanan_id,
                'layanan' => $pks->layanan,
                'jumlah_hc' => $pks->jumlah_hc,
                'nominal_invoice' => $pks->total_invoice,
                'kronologi' => $request->kronologi,
                'tindakan' => $request->tindakan,
                'gm_id' => null,
                'dirkeu_id' => null,
                'dirut_id' => null,
                'created_at' => Carbon::now(),
                'created_by' => Auth::user()->full_name ?? null,
            ]);

            return redirect()->route('putus-kontrak')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view($id)
    {
        try {
            $data = DB::table('sl_putus_kontrak')
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->first();

            if (!$data) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }

            return view('sales.putus-kontrak.view', compact('data'));
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
            abort(500);
        }
    }
}

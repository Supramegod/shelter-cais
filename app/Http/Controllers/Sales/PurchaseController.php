<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\SystemController;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function purchaseRequestIndex(Request $request)
    {
        $data = DB::table('sl_purchase_request')->get();
        $sales = DB::table('sl_purchase_request')
            ->select('sales')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();
        $jenis_barang = DB::table('sl_purchase_request')
            ->select('jenis_barang')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();
        $company =  DB::table('sl_purchase_request')
            ->select('perusahaan')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();
        return view('sales.purchase.purchase_request.list', compact('data', 'request', 'company', 'sales', 'jenis_barang'));
    }
    public function purchaseRequestList(Request $request)
    {
        try {
            $data = DB::table('sl_purchase_request')
                ->select('id', 'kode_pr', 'tanggal_cetak as tanggal', 'sales', 'perusahaan', 'jenis_barang', 'created_by')
                ->whereNull('deleted_at');

            if (!empty($request->sales)) {
                $data = $data->where('sl_purchase_request.sales', $request->sales);
            }
            if (!empty($request->jenis_barang)) {
                $data = $data->where('sl_purchase_request.jenis_barang', $request->jenis_barang);
            }
            if (!empty($request->company)) {
                $data = $data->where('sl_purchase_request.perusahaan', $request->company);
            }

            $data = $data->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                    <a href="' . route('purchase-request.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                        </div>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function purchaseRequestView($id)
    {
        try {
            $data = DB::table('sl_purchase_request')
                ->where('id', $id)
                ->first();
            $dataBarang = DB::table('sl_purchase_request_d')
                ->where('purchase_request_id', $id)
                ->get();
            $jenisBarang = DB::table('sl_purchase_request_d')
                ->select('jenis_barang')
                ->where('purchase_request_id', $id)
                ->distinct()
                ->get();
            return view('sales.purchase.purchase_request.view', compact('data', 'dataBarang', 'jenisBarang'));
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
            abort(500);
        }
    }
    public function purchaseRequestSave(Request $request)
    {
        try {
            if (!$request->isMethod('post')) {
                abort(405);
            }
            $id = $request->input('quotation_id');
            $jenis = $request->input('jenis');
            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $quotation = DB::table('sl_quotation')->where('id', $id)->first();
            $leads = DB::table('sl_leads')->where('id', $quotation->leads_id)->first();
            $wilayah = DB::table('sl_leads')
                ->leftJoin($db2 . '.m_branch', 'sl_leads.branch_id', '=', $db2 . '.m_branch.id')
                ->where('sl_leads.id', $leads->id)
                ->first();
            if ($jenis == "Kaporlap") {
                $listBarang = DB::table('sl_quotation_kaporlap')->join('m_barang', 'm_barang.id', '=', 'sl_quotation_kaporlap.barang_id')->where('jumlah', '>', 0)->where('quotation_id', $id)->select('sl_quotation_kaporlap.*', 'm_barang.merk', 'm_barang.satuan')->whereNull('sl_quotation_kaporlap.deleted_at')->get();
                $listJenisBarang = DB::select("select distinct jenis_barang from sl_quotation_kaporlap where deleted_at is null and jumlah>0 and quotation_id = " . $id);
            } else if ($jenis == "Chemical") {
                $listBarang = DB::table('sl_quotation_chemical')->join('m_barang', 'm_barang.id', '=', 'sl_quotation_chemical.barang_id')->where('quotation_id', $id)->where('jumlah', '>', 0)->select('sl_quotation_chemical.*', 'm_barang.merk', 'm_barang.satuan')->whereNull('sl_quotation_chemical.deleted_at')->get();
                $listJenisBarang = DB::select("select distinct jenis_barang from sl_quotation_chemical where deleted_at is null and jumlah > 0 and quotation_id = " . $id);
            } else if ($jenis == "Devices") {
                $listBarang = $listDevices = DB::table('sl_quotation_devices')->join('m_barang', 'm_barang.id', '=', 'sl_quotation_devices.barang_id')->where('quotation_id', $id)->select('sl_quotation_devices.*', 'm_barang.merk', 'm_barang.satuan')->where('jumlah', '>', 0)->whereNull('sl_quotation_devices.deleted_at')->get();
                $listJenisBarang = DB::select("select distinct jenis_barang from sl_quotation_devices where deleted_at is null and jumlah>0 and quotation_id = " . $id);
            }

            $data = (object)[
                'nomor' => $this->cetakNomorDokumen('PR'),
                'tipe_barang' => $jenis,
                'tanggal' => Carbon::now()->toDateTimeString(),
                'wilayah' => $wilayah,
                'perusahaan' => $leads->nama_perusahaan,
                'sales' => $quotation->created_by,
                'pencetak' => Auth::user()->full_name,

            ];
            $insertedId = DB::table('sl_purchase_request')->insertGetId([
                'kode_pr' => $data->nomor,
                'tanggal_cetak' => $data->tanggal,
                'sales' => $quotation->created_by,
                'wilayah' => $wilayah->name,
                'perusahaan' => $leads->nama_perusahaan,
                'quotation_id' => $id,
                'jenis_barang' => $jenis,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name
            ]);

            $dataInsert = [];
            foreach ($listBarang as $item) {
                $dataInsert[] = [
                    'purchase_request_id' => $insertedId,
                    'barang_id' => $item->barang_id,
                    'nama_barang' => $item->nama,
                    'qty' => $item->jumlah,
                    'satuan' => $item->satuan,
                    'merk' => $item->merk,
                    'jenis_barang' => $item->jenis_barang,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'created_by' => Auth::user()->full_name
                ];
            }

            DB::table('sl_purchase_request_d')->insert($dataInsert);

            $nomor = str_replace(['/', '\\'], '-', $data->nomor);
            $pdf = Pdf::loadView('sales.purchase.purchase_request.cetak', compact('data', 'listBarang', 'listJenisBarang', 'leads'))->setPaper('A4', 'portrait');
            return $pdf->stream('Purchase-Request-Nomor-' . $nomor . '.pdf');
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function cariNomorRequest(Request $request)
    {
        try {

            $data = DB::table('sl_purchase_request')
                ->select('id', 'kode_pr', 'jenis_barang', 'tanggal_cetak as tanggal_buat', 'created_by')
                ->where('perusahaan', $request->company)
                ->whereNull('deleted_at')
                ->get();

            return DataTables::of($data)
                ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function purchaseOrderIndex(Request $request)
    {
        $data = DB::table('sl_purchase_order')->get();
        $sales = DB::table('sl_purchase_order')
            ->select('sales')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();
        $jenis_barang = DB::table('sl_purchase_order')
            ->select('jenis_barang')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();
        $company =  DB::table('sl_purchase_order')
            ->select('perusahaan')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();
        return view('sales.purchase.purchase_order.list', compact('data', 'request', 'company', 'sales', 'jenis_barang'));
    }
    public function purchaseOrderList(Request $request)
    {
        try {
            $data = DB::table('sl_purchase_order')
                ->select('id', 'kode_po', 'tanggal_cetak as tanggal', 'sales', 'perusahaan', 'jenis_barang', 'created_by') // pastikan semua yang dibutuhkan ada
                ->whereNull('deleted_at');

            if (!empty($request->sales)) {
                $data = $data->where('sl_purchase_order.sales', $request->sales);
            }
            if (!empty($request->jenis_barang)) {
                $data = $data->where('sl_purchase_order.jenis_barang', $request->jenis_barang);
            }
            if (!empty($request->company)) {
                $data = $data->where('sl_purchase_order.perusahaan', $request->company);
            }

            $data = $data->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                    <a href="' . route('purchase-order.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs">View</a> &nbsp;
                        </div>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function purchaseOrderAdd(Request $request)
    {
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table('sl_purchase_request')->get();
            $company = DB::table('sl_purchase_request')
                ->select('perusahaan')
                ->whereNull('deleted_at')
                ->distinct()
                ->get();
            return view('sales.purchase.purchase_order.add', compact('request', 'company', 'data', 'now'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function purchaseOrderView($id)
    {
        try {
            $data = DB::table('sl_purchase_order')
                ->where('id', $id)
                ->first();
            $listBarang = DB::table('sl_purchase_order_d')
                ->where('purchase_order_id', $id)
                ->get();
            $listJenisBarang = DB::table('sl_purchase_order_d')
                ->select('jenis_barang')
                ->where('purchase_order_id', $id)
                ->distinct()
                ->get();

            return view('sales.purchase.purchase_order.view', compact('data', 'listBarang', 'listJenisBarang'));
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
            abort(500);
        }
    }
    public function purchaseOrdersave(Request $request)
    {
        // try {
        set_time_limit(120);


        $dataRequest = DB::table('sl_purchase_request')
            ->where('id', $request->purchase_request_id)
            ->first();

        $insertedId = DB::table('sl_purchase_order')->insertGetId([
            'kode_po' => $this->cetakNomorDokumen('PO'),
            'kode_pr' => $dataRequest->kode_pr ?? '',
            'tanggal_cetak' => Carbon::now()->toDateTimeString(),
            'sales' => $dataRequest->sales ?? '',
            'wilayah' => $dataRequest->wilayah ?? '',
            'perusahaan' => $dataRequest->perusahaan ?? 'Logistik',
            'quotation_id' => $dataRequest->quotation_id ?? 0,
            'jenis_barang' => $dataRequest->jenis_barang ?? '',
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => Auth::user()->full_name
        ]);
        $checkPO = DB::table('sl_purchase_order')->where('id', $insertedId)->first();

        $listBarang = $request->input('listRequest_ids', []);
        $jumlahPoArray = $request->input('jumlah_po', []);
        $listBarangPO = [];
        foreach ($listBarang as $barangId) {
            if ($checkPO->kode_pr != null) {
                $barang = DB::table('sl_purchase_request_d')->where('id', $barangId)->first();
            } else {
                $barang = DB::table('m_barang')->where('id', $barangId)->select('id as barang_id', 'nama as nama_barang', 'satuan', 'merk', 'jenis_barang')->first();
            }

            $listBarangPO[] = [
                'purchase_order_id' => $insertedId,
                'barang_id' => $barang->barang_id,
                'nama_barang' => $barang->nama_barang,
                'qty' => $jumlahPoArray[$barangId],
                'satuan' => $barang->satuan,
                'merk' => $barang->merk,
                'jenis_barang' => $barang->jenis_barang,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name,
            ];
        }
        DB::table('sl_purchase_order_d')->insert($listBarangPO);
        $nomor = DB::table('sl_purchase_order')
            ->where('id', $insertedId)
            ->select('kode_po')
            ->get();
        return redirect()->route('purchase_order.pdf', ['id' => $insertedId]);
        // } catch (\Exception $e) {
        //     SystemController::saveError($e, Auth::user(), request());
        //     abort(500);
        // }
    }
    public function cetakOrderPdf($id)
    {
        $dataRequest = DB::table('sl_purchase_order')->where('id', $id)->first();
        $listJenisBarang = DB::table('sl_purchase_order_d')
            ->select('jenis_barang')
            ->where('purchase_order_id', $id)
            ->distinct()
            ->get();
        $listBarangRequest = DB::table('sl_purchase_order_d')
            ->where('purchase_order_id', $id)
            ->get();

        $data = (object)[
            'nomor' => $dataRequest->kode_po,
            'tanggal' => $dataRequest->tanggal_cetak,
            'wilayah' => $dataRequest->wilayah,
            'sales' => $dataRequest->sales,
            'perusahaan' => $dataRequest->perusahaan,
            'pencetak' => $dataRequest->created_by,
            'jenis_barang' => $dataRequest->jenis_barang,
        ];

        $nomor = str_replace(['/', '\\'], '-', $data->nomor);
        $pdf = PDF::loadView('sales.purchase.purchase_order.cetak', compact('dataRequest', 'data', 'listBarangRequest', 'listJenisBarang'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('Purchase-Order-Nomor-' . $nomor . '.pdf');
    }

    public function cetakNomorDokumen($kode)
    {
        try {

            $now = Carbon::now();
            $tahun = $now->format('y');
            $bulan = $now->format('m');
            if ($kode == 'PO') {
                $jumlah = DB::table('sl_purchase_order')
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $now->year)
                    ->count();
            } else if ($kode == 'PR') {
                $jumlah = DB::table('sl_purchase_request')
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $now->year)
                    ->count();
            }

            $urutan = str_pad($jumlah + 1, 4, '0', STR_PAD_LEFT);
            $nomor = $kode . '-' . $tahun . $bulan . '-' . $urutan;
            return $nomor;
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), request());
            abort(500);
        }
    }
    public function getRequestList(Request $request)
    {



        if ($request->kode_pr && $request->perusahaan) {

            $data = DB::table('sl_purchase_request_d')
                ->join('sl_purchase_request', 'sl_purchase_request.id', '=', 'sl_purchase_request_d.purchase_request_id')
                ->join('m_barang', 'm_barang.id', '=', 'sl_purchase_request_d.barang_id')
                ->select('sl_purchase_request_d.*', 'm_barang.stok_barang')
                ->where('sl_purchase_request.kode_pr', $request->kode_pr)
                ->where('sl_purchase_request.perusahaan', $request->perusahaan)
                ->where('sl_purchase_request_d.qty', '>', 0)
                ->whereNull('sl_purchase_request_d.deleted_at')
                ->get();

            return $data;
        } else {
            $data = DB::table('m_barang')
                ->select('id', 'nama as nama_barang', 'stok_barang', 'satuan', 'merk', 'jenis_barang')
                ->whereNull('deleted_at')

                ->get();
            return $data;
        }
    }
}

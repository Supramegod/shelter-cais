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
    public function purchaseRequestAdd(Request $request)
    {
        $kode_pr = $this->cetakNomorDokumen('PR');
        return view('sales.purchase.purchase_request.add', compact('kode_pr'));
    }
    public function cariNomorPKS(Request $request)
    {
        try {

            $data = DB::table('sl_pks')
                ->select('id', 'nomor', 'nama_perusahaan',  DB::raw('DATE(created_at) as created_at'), 'created_by')
                ->where('status_pks_id', '=', 7)
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
        set_time_limit(120);
        $quotation = DB::table('sl_quotation')->join('sl_pks', 'sl_quotation.id', '=', 'sl_pks.quotation_id')->where('sl_pks.id', $request->id_pks)->select('sl_quotation.*')->first();

        $db = DB::connection('mysqlhris')->getDatabaseName();
        $leads = DB::table('sl_leads')->where('id', $quotation->leads_id)->first();
        $wilayah = DB::table('sl_leads')
            ->join($db . '.m_branch as m_branch', 'sl_leads.branch_id', '=', 'm_branch.id')
            ->where('sl_leads.id', $leads->id)
            ->first();

        $insertedId = DB::table('sl_purchase_request')->insertGetId([
            'kode_pr' => $request->kode_pr,
            'tanggal_cetak' => Carbon::now()->isoFormat('YYYY-MM-DD'),
            'sales' =>  $quotation->created_by,
            'wilayah' => $wilayah->name,
            'perusahaan' => $request->nama_perusahaan,
            'quotation_id' => $quotation->id,
            'jenis_barang' => $request->jenis_barang,
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => Auth::user()->full_name
        ]);
        if ($request->jenis_barang == "Kaporlap") {
            $dataBarang = DB::table('sl_quotation_kaporlap')
                ->join('m_barang', 'm_barang.id', '=', 'sl_quotation_kaporlap.barang_id')
                ->where('sl_quotation_kaporlap.quotation_id', $quotation->id)
                ->where('sl_quotation_kaporlap.jumlah', '>', 0)
                ->whereNull('sl_quotation_kaporlap.deleted_at')
                ->select('sl_quotation_kaporlap.*', 'm_barang.merk', 'm_barang.satuan')
                ->get();
        } elseif ($request->jenis_barang == "Chemical") {
            $dataBarang = DB::table('sl_quotation_chemical')
                ->join('m_barang', 'm_barang.id', '=', 'sl_quotation_chemical.barang_id')
                ->where('sl_quotation_chemical.quotation_id', $quotation->id)
                ->where('sl_quotation_chemical.jumlah', '>', 0)
                ->whereNull('sl_quotation_chemical.deleted_at')
                ->select('sl_quotation_chemical.*', 'm_barang.merk', 'm_barang.satuan')
                ->get();
        } elseif ($request->jenis_barang == "Devices") {
            $dataBarang = DB::table('sl_quotation_devices')
                ->join('m_barang', 'm_barang.id', '=', 'sl_quotation_devices.barang_id')
                ->where('sl_quotation_devices.quotation_id', $quotation->id)
                ->where('sl_quotation_devices.jumlah', '>', 0)
                ->whereNull('sl_quotation_devices.deleted_at')
                ->select('sl_quotation_devices.*', 'm_barang.merk', 'm_barang.satuan')
                ->get();
        }
        $listBarang = $request->input('listBarang_ids', []);
        $jumlahPRArray = $request->input('jumlah_pr', []);
        $listBarangPR = [];
        foreach ($listBarang as $barangId) {

            $barang = $dataBarang->where('id', $barangId)->first();

            if (!$barang) continue;
            $listBarangPR[] = [
                'purchase_request_id' => $insertedId,
                'barang_id' => $barang->barang_id,
                'nama_barang' => $barang->nama,
                'qty' => $jumlahPRArray[$barangId],
                'satuan' => $barang->satuan,
                'merk' => $barang->merk,
                'jenis_barang' => $barang->jenis_barang,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name,
            ];
        }
        DB::table('sl_purchase_request_d')->insert($listBarangPR);
        $pr = DB::table('sl_purchase_request')
            ->where('id', $insertedId)
            ->select('id', 'kode_pr')
            ->first();
        return redirect()->back()->with([
            'success' => 'Data Berhasil Disimpan',
            'kode_pr' => $pr->kode_pr,
            'id' => $pr->id,
        ]);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
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
            $now = Carbon::now()->isoFormat('YYYY-MM-DD');
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
        try {
        set_time_limit(120);
        $dataRequest = DB::table('sl_purchase_request')
            ->where('id', $request->purchase_request_id)
            ->first();

        $insertedId = DB::table('sl_purchase_order')->insertGetId([
            'kode_po' => $this->cetakNomorDokumen('PO'),
            'kode_pr' => $dataRequest->kode_pr ?? '',
            'tanggal_cetak' => Carbon::now()->isoFormat('YYYY-MM-DD'),
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
        $po = DB::table('sl_purchase_order')
            ->where('id', $insertedId)
            ->select('id', 'kode_po')
            ->first();
        return redirect()->back()->with([
            'success' => 'Data Berhasil Disimpan',
            'kode_po' => $po->kode_po,
            'id' => $po->id,
        ]);

        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
            abort(500);
        }
    }
    public function printRequestPdf($id)
    {
        $dataRequest = DB::table('sl_purchase_request')->where('id', $id)->first();
        $listJenisBarang = DB::table('sl_purchase_request_d')
            ->select('jenis_barang')
            ->where('purchase_request_id', $id)
            ->distinct()
            ->get();
        $listBarang = DB::table('sl_purchase_request_d')
            ->where('purchase_request_id', $id)
            ->get();

        $data = (object)[
            'nomor' => $dataRequest->kode_pr,
            'tanggal' => Carbon::parse($dataRequest->tanggal_cetak)->translatedFormat('d F Y'),
            'wilayah' => $dataRequest->wilayah,
            'sales' => $dataRequest->sales,
            'perusahaan' => $dataRequest->perusahaan,
            'pencetak' => $dataRequest->created_by,
            'jenis_barang' => $dataRequest->jenis_barang,
        ];

        $nomor = str_replace(['/', '\\'], '-', $data->nomor);
        $pdf = PDF::loadView('sales.purchase.purchase_request.cetak', compact('dataRequest', 'data', 'listBarang', 'listJenisBarang'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('Purchase-Request-Nomor-' . $nomor . '.pdf');
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
            $query = DB::table('sl_purchase_request_d')
                ->join('sl_purchase_request', 'sl_purchase_request.id', '=', 'sl_purchase_request_d.purchase_request_id')
                ->join('m_barang', 'm_barang.id', '=', 'sl_purchase_request_d.barang_id')
                ->select('sl_purchase_request_d.*', 'm_barang.stok_barang', 'sl_purchase_request.quotation_id')
                ->where('sl_purchase_request.kode_pr', $request->kode_pr)
                ->where('sl_purchase_request.perusahaan', $request->perusahaan)
                ->where('sl_purchase_request_d.qty', '>', 0)
                ->whereNull('sl_purchase_request_d.deleted_at');

            if ($request->status && $request->status != 'all') {
                if ($request->status == 'closed') {
                    $query->where(function ($sub) {
                        $sub->WhereExists(function ($subquery) {
                            $subquery->select(DB::raw(1))
                                ->from('sl_purchase_request_d')
                                ->whereColumn('sl_purchase_request_d.purchase_request_id', 'sl_purchase_request.id')
                                ->where('sl_purchase_request_d.is_open', 0);
                        });
                    });
                } elseif ($request->status == 'open') {
                    $query->where(function ($sub) {
                        $sub->WhereExists(function ($subquery) {
                            $subquery->select(DB::raw(1))
                                ->from('sl_purchase_request_d')
                                ->whereColumn('sl_purchase_request_d.purchase_request_id', 'sl_purchase_request.id')
                                ->where('sl_purchase_request_d.is_open', 1);
                        });
                    });
                }
            }

            return $query->get();
        } else {
            return DB::table('m_barang')
                ->select('id', 'nama as nama_barang', 'stok_barang', 'satuan', 'merk', 'jenis_barang')
                ->whereNull('deleted_at')
                ->get();
        }
    }
    public function getListBarang(Request $request)
    {
        $quotation = DB::table('sl_pks')->join('sl_quotation', 'sl_quotation.id', '=', 'sl_pks.quotation_id')->where('sl_pks.id', $request->id_pks)->select('sl_quotation.*')->first();

        if ($request->jenis_barang == "Kaporlap") {
            $query = DB::table('sl_quotation_kaporlap')
                ->join('m_barang', 'm_barang.id', '=', 'sl_quotation_kaporlap.barang_id')
                ->where('quotation_id', $quotation->id)
                ->where('sl_quotation_kaporlap.jumlah', '>', 0)
                ->whereNull('sl_quotation_kaporlap.deleted_at')
                ->select('sl_quotation_kaporlap.*', 'm_barang.merk', 'm_barang.satuan');
        } else if ($request->jenis_barang == "Chemical") {
            $query = DB::table('sl_quotation_chemical')
                ->join('m_barang', 'm_barang.id', '=', 'sl_quotation_chemical.barang_id')
                ->where('quotation_id', $quotation->id)
                ->where('sl_quotation_chemical.jumlah', '>', 0)
                ->whereNull('sl_quotation_chemical.deleted_at')
                ->select('sl_quotation_chemical.*', 'm_barang.merk', 'm_barang.satuan');
        } else if ($request->jenis_barang == "Devices") {
            $query = DB::table('sl_quotation_devices')
                ->join('m_barang', 'm_barang.id', '=', 'sl_quotation_devices.barang_id')
                ->where('quotation_id', $quotation->id)
                ->where('sl_quotation_devices.jumlah', '>', 0)
                ->whereNull('sl_quotation_devices.deleted_at')
                ->select('sl_quotation_devices.*', 'm_barang.merk', 'm_barang.satuan');
        }


        if ($request->status && $request->status != 'all') {
            if ($request->status == 'closed') {
                $query->where(function ($query) use ($quotation) {
                    $query->whereExists(function ($subquery) use ($quotation) {
                        $subquery->from('sl_receiving_notes')
                            ->join('sl_receiving_notes_d', 'sl_receiving_notes_d.receiving_notes_id', '=', 'sl_receiving_notes.id')
                            ->where('sl_receiving_notes.quotation_id', $quotation->id)
                            ->where('sl_receiving_notes_d.is_open', 0);
                    })->orWhereExists(function ($subquery) use ($quotation) {
                        $subquery->from('sl_purchase_request')
                            ->join('sl_purchase_request_d', 'sl_purchase_request_d.purchase_request_id', '=', 'sl_purchase_request.id')
                            ->where('sl_purchase_request.quotation_id', $quotation->id)
                            ->where('sl_purchase_request_d.is_open', 0);
                    });
                });
            } elseif ($request->status == 'open') {

                $query->where(function ($query) use ($quotation) {
                    $query->whereNotExists(function ($sub) use ($quotation) {
                        $sub->from('sl_receiving_notes')
                            ->join('sl_receiving_notes_d', 'sl_receiving_notes_d.receiving_notes_id', '=', 'sl_receiving_notes.id')
                            ->where('sl_receiving_notes.quotation_id', $quotation->id);
                    })
                        ->orWhereExists(function ($sub) use ($quotation) {
                            $sub->from('sl_receiving_notes')
                                ->join('sl_receiving_notes_d', 'sl_receiving_notes_d.receiving_notes_id', '=', 'sl_receiving_notes.id')
                                ->where('sl_receiving_notes.quotation_id', $quotation->id)
                                ->where('sl_receiving_notes_d.is_open', 1);
                        });
                });
            }
        }
        $data = $query->get();
        return $data;
    }
}

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
use App\Exports\supplierTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use \stdClass;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    public function index(Request $request)
    {

        return view('master.supplier.list');
    }

    public function indexadd(Request $request)
    {
        return view('master.supplier.add');
    }
    public function indexaddGr(Request $request)
    {
        return view('master.supplier.addGr');
    }
    public function indexaddRn(Request $request)
    {
        return view('master.supplier.addRn');
    }
    public function indexGr(Request $request)
    {
        $tglDari = $request->tgl_dari;
        $tglSampai = $request->tgl_sampai;

        if ($tglDari == null) {
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
        }
        if ($tglSampai == null) {
            $tglSampai = carbon::now()->toDateString();
        }

        $ctglDari = Carbon::createFromFormat('Y-m-d', $tglDari);
        $ctglSampai = Carbon::createFromFormat('Y-m-d', $tglSampai);

        $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();


        $error = null;
        $success = null;
        if ($ctglDari->gt($ctglSampai)) {
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        }
        ;
        if ($ctglSampai->lt($ctglDari)) {
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }
        return view('master.Supplier.listGr', compact('tglDari', 'tglSampai', 'request', 'error', 'success', 'kebutuhan'));
    }
    public function indexRn(Request $request)
    {
        $tglDari = $request->tgl_dari;
        $tglSampai = $request->tgl_sampai;

        if ($tglDari == null) {
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
        }
        if ($tglSampai == null) {
            $tglSampai = carbon::now()->toDateString();
        }

        $ctglDari = Carbon::createFromFormat('Y-m-d', $tglDari);
        $ctglSampai = Carbon::createFromFormat('Y-m-d', $tglSampai);

        $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();


        $error = null;
        $success = null;
        if ($ctglDari->gt($ctglSampai)) {
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        }
        ;
        if ($ctglSampai->lt($ctglDari)) {
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }
        return view('master.Supplier.listRn', compact('tglDari', 'tglSampai', 'request', 'error', 'success', 'kebutuhan'));
    }
    public function list(Request $request)
    {
        try {
            \Log::info('Datatables request received', $request->all());
            $data = DB::table('m_supplier')
                ->select(
                    'm_supplier.id',
                    'nama_supplier',
                    'alamat',
                    'kontak',
                    'pic',
                    'npwp',
                    'kategori_barang',
                    'created_at',
                    'created_by'
                );


            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '
            <div class="d-flex justify-content-center">
                <a href="' . route('supplier.view', $data->id) . '" class="btn btn-info btn-sm me-1">
                    <i class="mdi mdi-eye"></i> View
                </a>
                <button class="btn btn-danger btn-sm btn-delete" data-id="' . $data->id . '">
                    <i class="mdi mdi-trash-can"></i> Delete
                </button>
            </div>';
                })
                ->rawColumns(['aksi']) // penting agar HTML tidak di-escape
                ->make(true); // penting agar DataTables menerima JSON

        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500, 'Internal Server Error');
        }
    }
    public function add(Request $request)
    {
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('master.supplier.add', compact('now'));
    }
    // public function save(Request $request)
    // {
    //     $request->validate([
    //         'nama' => 'required|string|max:255',
    //     ]);

    //     DB::table('m_supplier')->insert([
    //         'nama_supplier' => $request->nama,
    //         'pic' => $request->pic,
    //         'alamat' => $request->alamat,
    //         'kontak' => $request->kontak,
    //         'npwp' => $request->npwp,
    //         'kategori_barang' => $request->kategori_barang,
    //         'created_by' => Auth::user()->full_name,
    //         'created_at' => now()
    //     ]);

    //     return redirect()->route('supplier')->with('success', 'Data supplier berhasil disimpan.');
    // }
    public function delete(Request $request)
    {
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_supplier')->where('id', $request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            return response()->json([
                'success' => true,
                'data' => [],
                'message' => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function view(Request $request, $id)
    {
        $data = DB::table('m_supplier')->where('id', $id)->first();


        return view('master.supplier.view', compact('data'));

    }
    public function save(Request $request)
    {

        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nama' => 'required',
                'pic' => 'required',
                'alamat' => 'required',
                'kontak' => 'required',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            } else {
                $current_date_time = Carbon::now()->toDateTimeString();
                $msgSave = '';
                if (!empty($request->id)) {
                    DB::table('m_supplier')->where('id', $request->id)->update([
                        'nama_supplier' => $request->nama,
                        'pic' => $request->pic,
                        'alamat' => $request->alamat,
                        'kontak' => $request->kontak,
                        'npwp' => $request->npwp,
                        'kategori_barang' => $request->kategori_barang,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                } else {
                    DB::table('m_supplier')->insert([
                        'nama_supplier' => $request->nama,
                        'pic' => $request->pic,
                        'alamat' => $request->alamat,
                        'kontak' => $request->kontak,
                        'npwp' => $request->npwp,
                        'kategori_barang' => $request->kategori_barang,
                        'created_by' => Auth::user()->full_name,
                        'created_at' => now()
                    ]);
                }
                $msgSave = 'supplier ' . $request->nama . ' berhasil disimpan.';
            }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function listGr(Request $request)
    {
        $data = DB::table('sl_good_receipt')
            ->whereNull('sl_good_receipt.deleted_at')
            ->select(
                'sl_good_receipt.id',
                'sl_good_receipt.nomor_gr',
                'sl_good_receipt.tanggal_cetak',
                'sl_good_receipt.kategori_barang',
                'sl_good_receipt.created_by',
                'sl_good_receipt.updated_at'
            )
            ->get(); // Pindahkan get() ke sini

        // Format data
        // $data->transform(function ($item) {
        //     if ($item->tanggal_cetak) {
        //         $item->tanggal_cetak = Carbon::parse($item->tanggal_cetak)->isoFormat('D MMMM Y');
        //     }

        //     if ($item->updated_at) {
        //         $item->updated_at = Carbon::parse($item->updated_at)->isoFormat('D MMMM Y');
        //     }

        //     $item->kategori_barang = strtoupper($item->kategori_barang);


        //     return $item;
        // });

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                                    <a href="' . route('supplier.viewGr', $data->id) . '" class="btn btn-primary waves-effect btn-xs">View</a> &nbsp;
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function listRn(Request $request)
    {
        $data = DB::table('sl_receiving_notes')
            ->whereNull('sl_receiving_notes.deleted_at')
            ->select(
                'id',
                'nomor_rn',
                'tanggal_cetak',
                'kategori_barang',
                'created_by',
                'updated_at'
            )
            ->get(); // Pindahkan get() ke sini

        // Format data
        // $data->transform(function ($item) {
        //     if ($item->tanggal_cetak) {
        //         $item->tanggal_cetak = Carbon::parse($item->tanggal_cetak)->isoFormat('D MMMM Y');
        //     }

        //     if ($item->updated_at) {
        //         $item->updated_at = Carbon::parse($item->updated_at)->isoFormat('D MMMM Y');
        //     }

        //     $item->kategori_barang = strtoupper($item->kategori_barang);


        //     return $item;
        // });

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                                    <a href="' . route('supplier.viewRn', $data->id) . '" class="btn btn-primary waves-effect btn-xs">View</a> &nbsp;
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function addGr(Request $request)
    {
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $po = DB::table('sl_purchase_order')->get();


            // if ($request->id != null) {
            //     // Ambil data PO berdasarkan ID dari request
            //     $po = DB::table('sl_purchase_order')
            //         ->where('id', $request->id)
            //         ->whereNull('deleted_at')
            //         ->first();

            //     // Jika PO ditemukan, ambil semua barang (detail) dalam PO tersebut

            //     if ($po) {
            //         $baranglist = DB::table('sl_purchase_order_d')
            //             ->where('purchase_order_id', $po->id)
            //             ->whereNull('deleted_at')
            //             ->get();

            //     }


            return view('master.supplier.create', compact('po', 'now'));


        } catch (\Exception $e) {
            dd($e); // Debug error jika terjadi
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function addRn(Request $request)
    {
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $pr = DB::table('sl_purchase_request')->get();


            // if ($request->id != null) {
            //     // Ambil data PO berdasarkan ID dari request
            //     $po = DB::table('sl_purchase_order')
            //         ->where('id', $request->id)
            //         ->whereNull('deleted_at')
            //         ->first();

            //     // Jika PO ditemukan, ambil semua barang (detail) dalam PO tersebut

            //     if ($po) {
            //         $baranglist = DB::table('sl_purchase_order_d')
            //             ->where('purchase_order_id', $po->id)
            //             ->whereNull('deleted_at')
            //             ->get();

            //     }


            return view('master.supplier.create2', compact('pr', 'now'));


        } catch (\Exception $e) {
            dd($e); // Debug error jika terjadi
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function getPurchaseOrderList(Request $request)
    {
        try {
            $data = DB::table('sl_purchase_order')
                ->whereNull('deleted_at')
                ->select([
                    'id',
                    'kode_po as nomor_po',
                    'perusahaan',
                    'sales',
                    'wilayah',
                ])
                ->get();

            return DataTables::of($data)->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }

    }
    public function availableBarangPO(Request $request)
    {
        $data = DB::table('sl_purchase_order_d')
            ->leftJoin('sl_purchase_order', 'sl_purchase_order.id', '=', 'sl_purchase_order_d.purchase_order_id')
            ->whereNull('sl_purchase_order_d.deleted_at')
            ->where('sl_purchase_order.kode_po', $request->kode_po)
            ->select(
                'sl_purchase_order.kode_po as nomor_po',
                'sl_purchase_order_d.id',
                'sl_purchase_order_d.purchase_order_id',
                'sl_purchase_order_d.nama_barang',
                'sl_purchase_order_d.qty',
                'sl_purchase_order_d.merk',
                'sl_purchase_order_d.jenis_barang',
                'sl_purchase_order.tanggal_cetak',
                'sl_purchase_order_d.created_by',

            )
            ->orderBy('sl_purchase_order.kode_po', 'asc')
            ->get();



        return $data;

    }
    public function getPurchaseRequestList(Request $request)
    {
        try {
            $data = DB::table('sl_purchase_request')
                ->whereNull('deleted_at')
                ->select([
                    'id',
                    'kode_pr as nomor_pr',
                    'perusahaan',
                    'sales',
                    'wilayah',
                    'jenis_barang',
                ])
                ->get();

            return DataTables::of($data)->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function availableBarangPR(Request $request)
    {
        $data = DB::table('sl_purchase_request_d')
            ->leftJoin('sl_purchase_request', 'sl_purchase_request.id', '=', 'sl_purchase_request_d.purchase_request_id')
            ->leftJoin('m_barang', 'm_barang.id', '=', 'sl_purchase_request_d.barang_id')
            ->whereNull('sl_purchase_request_d.deleted_at')
            ->where('sl_purchase_request.kode_pr', $request->kode_pr)
            ->select(
                'sl_purchase_request.kode_pr as nomor_pr',
                'sl_purchase_request_d.id',
                'sl_purchase_request_d.purchase_request_id',
                'sl_purchase_request_d.nama_barang',
                'sl_purchase_request_d.qty',
                'sl_purchase_request_d.merk',
                'sl_purchase_request_d.jenis_barang',
                'sl_purchase_request.tanggal_cetak',
                DB::raw('IFNULL(m_barang.stok_barang, 0) as stok_barang')
            )
            ->orderBy('sl_purchase_request.kode_pr', 'asc')
            ->get();

        return $data;
    }


    public function saveGr(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kode_po' => 'required',
            'po_ids' => 'required|array|min:1',
            'po_ids.*' => 'integer',
            'qty' => 'required|array',
            'qty.*' => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $requestpo = DB::table('sl_purchase_order')
            ->where('kode_po', $request->kode_po)
            ->whereNull('deleted_at')
            ->first();

        if (!$requestpo) {
            abort(404, 'Purchase Order not found');
        }

        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $month = date('m', strtotime($requestpo->created_at));
        $year = date('Y', strtotime($requestpo->created_at));

        $countPerMonth = DB::table('sl_good_receipt')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->count();

        // 1. Hanya ambil barang yang dipilih (po_ids)
        $selectedItems = DB::table('sl_purchase_order_d')
            ->whereIn('id', $request->po_ids)
            ->whereNull('deleted_at')
            ->get();

        $nomor_surat = 'GR-' . date('ym', strtotime($requestpo->created_at)) . '-' . str_pad($countPerMonth, 4, '0', STR_PAD_LEFT);

        // Simpan header GR
        $insertedId = DB::table('sl_good_receipt')->insertGetId([
            'nomor_gr' => $nomor_surat,
            'tanggal_cetak' => Carbon::now()->toDateTimeString(),
            'kategori_barang' => $requestpo->jenis_barang,
            'kode_po' => $requestpo->kode_po,
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => Auth::user()->full_name
        ]);

        // 2. Gunakan qty dari input user
        $dataInsert = [];
        foreach ($selectedItems as $item) {
            $inputQty = $request->qty[$item->id] ?? $item->qty;

            $dataInsert[] = [
                'good_receipt_id' => $insertedId,
                'nama_barang' => $item->nama_barang,
                'qty' => $inputQty, // Gunakan qty dari input user
                'satuan' => $item->satuan,
                'merk' => $item->merk,
                'kategori_barang' => $item->jenis_barang,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name
            ];
        }

        DB::table('sl_good_receipt_d')->insert($dataInsert);

        // 3. Siapkan data untuk cetak dengan qty yang diinput user
        $printItems = [];
        foreach ($selectedItems as $item) {
            $item->qty = $request->qty[$item->id] ?? $item->qty;
            $printItems[] = $item;
        }

        $listChemical = collect($printItems)->groupBy('jenis_barang');

        $created_by = Auth::user()->full_name ?? 'System';
        $kode_po = $requestpo->kode_po;

        $pdf = PDF::loadView('master.Supplier.print-gr', compact(
            'created_by',
            'kode_po',
            'nomor_surat',
            'now',
            'requestpo',
            'listChemical',
        ));

        $filename = 'GR-' . str_replace('/', '_', $kode_po) . '.pdf';
        return $pdf->stream($filename);
    }
    public function saveRn(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kode_pr' => 'required',
            'pr_ids' => 'required|array|min:1',
            'pr_ids.*' => 'integer',
            'qty' => 'required|array',
            'qty.*' => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $requestpr = DB::table('sl_purchase_request')
            ->where('kode_pr', $request->kode_pr)
            ->whereNull('deleted_at')
            ->first();
        // Ambil data quotation (penempatan) jika ada
        $quotation = null;
        if ($requestpr->quotation_id) {
            $quotation = DB::table('sl_quotation_site')
                ->where('quotation_id', $requestpr->quotation_id)
                ->first();
        }
        if (!$requestpr) {
            abort(404, 'Purchase Request not found');
        }

        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $month = date('m', strtotime($requestpr->created_at));
        $year = date('Y', strtotime($requestpr->created_at));

        $countPerMonth = DB::table('sl_receiving_notes')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->count();

        // Ambil data barang dari PR
        $selectedItems = DB::table('sl_purchase_request_d')
            ->whereIn('id', $request->pr_ids)
            ->whereNull('deleted_at')
            ->get();

        $nomor_surat = 'RN-' . date('ym', strtotime($requestpr->created_at)) . '-' . str_pad($countPerMonth + 1, 4, '0', STR_PAD_LEFT);


        // Simpan header RN
        $insertedId = DB::table('sl_receiving_notes')->insertGetId([
            'nomor_rn' => $nomor_surat,
            'tanggal_cetak' => Carbon::now()->toDateTimeString(),
            'kategori_barang' => $requestpr->jenis_barang,
            'kode_pr' => $requestpr->kode_pr,
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => Auth::user()->full_name
        ]);

        $dataInsert = [];
        $printItems = [];

        foreach ($selectedItems as $item) {
            $inputQty = $request->qty[$item->id] ?? $item->qty;

            // Hitung qty yang sudah diterima sebelumnya dari RN lain
            $receivedQty = DB::table('sl_receiving_notes_d')
                ->where('purchase_request_d_id', $item->id)
                ->sum('qty');

            $totalQty = $receivedQty + $inputQty;

            if ($totalQty >= $item->qty) {
                $is_open = 0; // permintaan terpenuhi
                $finalQty = $item->qty - $receivedQty; // batasi sesuai sisa kebutuhan
            } else {
                $is_open = 1; // masih terbuka
                $finalQty = $inputQty;
            }

            // Jika tidak ada yang bisa diterima lagi, skip
            if ($finalQty <= 0) {
                continue;
            }

            // Simpan detail RN
            $dataInsert[] = [
                'receiving_notes_id' => $insertedId,
                'purchase_request_d_id' => $item->id,
                'nama_barang' => $item->nama_barang,
                'qty' => $finalQty,
                'satuan' => $item->satuan,
                'merk' => $item->merk,
                'kategori_barang' => $item->jenis_barang,
                'is_open' => $is_open,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name
            ];

            // Untuk keperluan cetak
            $item->qty = $finalQty;
            $printItems[] = $item;
        }

        if (count($dataInsert)) {
            DB::table('sl_receiving_notes_d')->insert($dataInsert);
        } else {
            // Jika tidak ada item yang bisa dimasukkan, hapus header RN yang sudah dibuat
            DB::table('sl_receiving_notes')->where('id', $insertedId)->delete();
            return back()->with('error', 'Semua barang sudah terpenuhi sebelumnya.');
        }

        $listChemical = collect($printItems)->groupBy('jenis_barang');

        $created_by = Auth::user()->full_name ?? 'System';
        $kode_pr = $requestpr->kode_pr;

        $pdf = PDF::loadView('master.supplier.print-rn', compact(
            'created_by',
            'kode_pr',
            'nomor_surat',
            'now',
            'requestpr',
            'listChemical',
            'quotation'

        ));

        $filename = 'RN-' . str_replace('/', '_', $kode_pr) . '.pdf';
        return $pdf->stream($filename);
    }


    // public function saveRn(Request $request)
    // {
    //     // 1. Validasi input
    //     $validator = Validator::make($request->all(), [
    //         'kode_pr' => 'required',
    //         'pr_ids' => 'required|array|min:1',
    //         'pr_ids.*' => 'integer',
    //         'qty' => 'required|array',
    //         'qty.*' => 'integer|min:1'
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     DB::beginTransaction();

    //     try {
    // 2. Ambil data PR dari tabel
    // $requestpr = DB::table('sl_purchase_request')
    //     ->where('kode_pr', $request->kode_pr)
    //     ->whereNull('deleted_at')
    //     ->first();

    // $quotation = DB::table('sl_quotation_site')
    //     ->where('quotation_id', $requestpr->quotation_id ?? null)
    //     ->first();


    //         if (!$requestpr) {
    //             abort(404, 'Purchase Request not found');
    //         }

    //         $now = Carbon::now()->isoFormat('DD MMMM Y');
    //         $carbonDate = Carbon::parse($now);

    //         $month = $carbonDate->format('m');   // Output: 07
    //         $year = $carbonDate->format('Y');   // Output: 2025
    //         $countPerMonth = DB::table('sl_receiving_notes')
    //             ->whereMonth('created_at', $month)
    //             ->whereYear('created_at', $year)
    //             ->whereNull('deleted_at')
    //             ->count() + 1;

    //         // 3. Ambil item PR yang dipilih
    //         $selectedItems = DB::table('sl_purchase_request_d')
    //             ->whereIn('id', $request->pr_ids)
    //             ->whereNull('deleted_at')
    //             ->get();

    //         // 4. Buat nomor RN
    //         $createdAt = Carbon::parse($now);
    //         $nomor_surat = 'RN-' . $createdAt->format('ym') . '-' . str_pad($countPerMonth, 4, '0', STR_PAD_LEFT);
    //         // 5. Simpan ke tabel sl_receiving_notes (header)
    //         $insertedId = DB::table('sl_receiving_notes')->insertGetId([
    //             'nomor_rn' => $nomor_surat,
    //             'tanggal_cetak' => Carbon::now()->toDateTimeString(),
    //             'kategori_barang' => $requestpr->jenis_barang,
    //             'kode_pr' => $requestpr->kode_pr,
    //             'created_at' => Carbon::now()->toDateTimeString(),
    //             'created_by' => Auth::user()->full_name
    //         ]);

    //         // 6. Simpan ke tabel sl_receiving_notes_d (detail)
    //         $dataInsert = [];
    //         foreach ($selectedItems as $item) {
    //             $inputQty = $request->qty[$item->id] ?? $item->qty;

    //             $dataInsert[] = [
    //                 'receiving_notes_id' => $insertedId, // <- disesuaikan
    //                 'nama_barang' => $item->nama_barang,
    //                 'qty' => $inputQty,
    //                 'satuan' => $item->satuan,
    //                 'merk' => $item->merk,
    //                 'kategori_barang' => $item->jenis_barang,
    //                 'created_at' => Carbon::now()->toDateTimeString(),
    //                 'created_by' => Auth::user()->full_name
    //             ];
    //         }

    //         DB::table('sl_receiving_notes_d')->insert($dataInsert);

    //         DB::commit(); // Simpan ke DB

    //         // 7. Siapkan data untuk cetak PDF
    //         $printItems = [];
    //         foreach ($selectedItems as $item) {
    //             $item->qty = $request->qty[$item->id] ?? $item->qty;
    //             $printItems[] = $item;
    //         }

    //         $listChemical = collect($printItems)->groupBy('jenis_barang');

    //         $created_by = Auth::user()->full_name ?? 'System';
    //         $kode_pr = $requestpr->kode_pr;

    //         $pdf = PDF::loadView('master.supplier.print-rn', compact(
    //             'created_by',
    //             'nomor_surat',
    //             'now',
    //             'kode_pr',
    //             'requestpr',
    //             'listChemical'
    //         ));

    //         $filename = 'RN-' . str_replace('/', '_', $kode_pr) . '.pdf';
    //         return $pdf->stream($filename);

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         // Debug ke log Laravel
    //         \Log::error('RN Error: ' . $e->getMessage());

    //         // Debug ke browser (sementara untuk development)
    //         return back()->with('error', 'Gagal membuat Receiving Note: ' . $e->getMessage());
    //     }

    // }

    public function viewGr($id)
    {
        $requestpo = DB::table('sl_good_receipt')
            ->where('id', $id) // pakai id dari URL
            ->whereNull('deleted_at')
            ->first();

        if (!$requestpo) {
            abort(404, 'Purchase Order not found');
        }

        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $month = date('m', strtotime($requestpo->created_at));
        $year = date('Y', strtotime($requestpo->created_at));

        $countPerMonth = DB::table('sl_good_receipt')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->count();

        $listChemical = DB::table('sl_good_receipt_d')
            ->where('good_receipt_id', $requestpo->id)
            ->get()
            ->groupBy('kategori_barang');

        $nomor_surat = $requestpo->nomor_gr ?? '-';

        $created_by = Auth::user()->full_name ?? 'System';
        $kode_po = $requestpo->kode_po ?? 'QUOT/RCI/CHEM-' . date('mY') . '-' . str_pad($requestpo->id, 5, '0', STR_PAD_LEFT);

        return view('master.Supplier.viewGr', compact(
            'created_by',
            'kode_po',
            'nomor_surat',
            'now',
            'requestpo',
            'listChemical',
        ));
    }
    public function viewRn($id)
    {
        // 1. Ambil RN terlebih dahulu
        $receivingNote = DB::table('sl_receiving_notes')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$receivingNote) {
            abort(404, 'Receiving Note not found');
        }

        // 2. Ambil data PR berdasarkan kode_pr dari RN
        $requestpr = DB::table('sl_purchase_request')
            ->where('kode_pr', $receivingNote->kode_pr)
            ->whereNull('deleted_at')
            ->first();

        // 3. Ambil data Quotation jika ada quotation_id
        $quotation = null;
        if ($requestpr && $requestpr->quotation_id) {
            $quotation = DB::table('sl_quotation_site')
                ->where('quotation_id', $requestpr->quotation_id)
                ->first();
        }

        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $month = date('m', strtotime($receivingNote->created_at));
        $year = date('Y', strtotime($receivingNote->created_at));

        $countPerMonth = DB::table('sl_receiving_notes')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->count();

        $listChemical = DB::table('sl_receiving_notes_d')
            ->where('receiving_notes_id', $receivingNote->id)
            ->get()
            ->groupBy('kategori_barang');

        $nomor_surat = $receivingNote->nomor_rn ?? '-';
        $created_by = Auth::user()->full_name ?? 'System';
        $kode_pr = $receivingNote->kode_pr ?? 'PR/RCI/CHEM-' . date('mY') . '-' . str_pad($receivingNote->id, 5, '0', STR_PAD_LEFT);

        // Kirim data tambahan ke view jika ingin ditampilkan (misalnya nama perusahaan)
        return view('master.Supplier.viewRn', compact(
            'created_by',
            'kode_pr',
            'nomor_surat',
            'now',
            'receivingNote',
            'listChemical',
            'requestpr',
            'quotation' // berisi informasi perusahaan
        ));
    }




}
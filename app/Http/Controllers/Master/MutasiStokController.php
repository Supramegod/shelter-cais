<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class MutasiStokController extends Controller
{
    public function index()
    {
        return view('master.mutasi-stok.list');
    }

    public function getJenisBarang()
    {
        try {
            $jenisBarang = DB::table('m_jenis_barang')
                ->select('id', 'nama')
                ->orderBy('nama', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $jenisBarang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data jenis barang'
            ]);
        }
    }

    public function getBarang()
    {
        try {
            $barang = DB::table('m_barang')
                ->select('id', 'nama')
                ->orderBy('nama', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data barang'
            ]);
        }
    }

    public function stokBarangList(Request $request)
    {
        try {
            $query = DB::table('m_barang as mb')
                ->leftJoin('m_jenis_barang as mjb', 'mb.jenis_barang_id', '=', 'mjb.id')
                ->select(
                    'mb.id',
                    'mjb.nama as jenis_barang',
                    DB::raw('COALESCE(mb.stok_barang, 0) as stok_barang'),
                    'mb.satuan',
                    'mb.nama',
                    'mb.merk'
                );

            // Filter berdasarkan jenis barang
            if ($request->filled('jenis_barang')) {
                $query->where('mb.jenis_barang_id', $request->jenis_barang);
            }

            // Filter berdasarkan barang
            if ($request->filled('barang')) {
                $query->where('mb.id', $request->barang);
            }

            // Filter berdasarkan tanggal (jika diperlukan untuk stok pada tanggal tertentu)
            if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
                // Implementasi filter tanggal jika diperlukan
                // Misalnya untuk menghitung stok pada periode tertentu
            }

            $data = $query->get();

            return datatables($data)
                ->addColumn('stok_barang', function ($row) {
                    return number_format($row->stok_barang, 0, ',', '.');
                })
                ->rawColumns(['stok_barang'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data stok barang: ' . $e->getMessage()
            ]);
        }
    }

    public function mutasiList(Request $request)
    {
        try {
            $query = DB::table('mutasi_stok as ms')
                ->leftJoin('m_barang as mb', 'ms.barang_id', '=', 'mb.id')
                ->leftJoin('m_jenis_barang as mjb', 'mb.jenis_barang_id', '=', 'mjb.id')
                ->select(
                    'ms.id',
                    'ms.transaksi',
                    'ms.ref_id',
                    'mb.nama as nama_barang',
                    'ms.qty',
                    'ms.tgl',
                    'ms.pic',
                    'ms.keterangan',
                    'ms.created_at'
                );

            // Filter berdasarkan jenis barang
            if ($request->filled('jenis_barang')) {
                $query->where('mb.jenis_barang_id', $request->jenis_barang);
            }

            // Filter berdasarkan barang
            if ($request->filled('barang')) {
                $query->where('ms.barang_id', $request->barang);
            }

            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('ms.tgl', '>=', $request->tanggal_dari);
            }

            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('ms.tgl', '<=', $request->tanggal_sampai);
            }

            $query->orderBy('ms.created_at', 'desc');

            $data = $query->get();

            return datatables($data)
                ->addColumn('qty', function ($row) {
                    $color = '';
                    $sign = '';
                    
                    // Tentukan warna berdasarkan jenis transaksi
                    if (in_array(strtolower($row->transaksi), ['penerimaan barang', 'good_receive'])) {
                        $color = 'color: green;';
                        $sign = '+';
                    } elseif (in_array(strtolower($row->transaksi), ['pengeluaran stok', 'pengiriman stok'])) {
                        $color = 'color: red;';
                        $sign = '-';
                    }
                    
                    return '<span style="' . $color . '">' . $sign . number_format($row->qty, 2, ',', '.') . '</span>';
                })
                ->addColumn('tgl', function ($row) {
                    return date('Y-m-d H:i:s', strtotime($row->tgl));
                })
                ->rawColumns(['qty'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data mutasi stok: ' . $e->getMessage()
            ]);
        }
    }
    // MutasiStokController.php
public function searchBarang(Request $request)
{
    try {
        $search = $request->input('search');
        $jenisBarangId = $request->input('jenis_barang');

        $query = DB::table('m_barang')
            ->select('id',  'nama as nama_barang', 'merk');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('merk', 'like', '%' . $search . '%');
            });
        }

        if (!empty($jenisBarangId)) {
            $query->where('jenis_barang_id', $jenisBarangId);
        }

        $query->orderBy('nama', 'asc')
              ->limit(50); // Batasi hasil untuk menghindari terlalu banyak data

        $barang = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $barang
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal melakukan pencarian barang: ' . $e->getMessage()
        ]);
    }
}
}
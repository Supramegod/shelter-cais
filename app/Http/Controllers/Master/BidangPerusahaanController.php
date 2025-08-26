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
use Maatwebsite\Excel\Facades\Excel;
use \stdClass;
use Barryvdh\DomPDF\Facade\Pdf;

class BidangPerusahaanController extends Controller
{
    public function index(Request $request)
    {
        return view('master.bidang-perusahaan.list');
    }

    public function indexadd(Request $request)
    {
        return view('master.bidang-perusahaan.add');
    }
  
    // Method untuk DataTables AJAX
    public function list(Request $request)
    {
        try {
            \Log::info('Datatables request received', $request->all());

            // Ambil data dari tabel m_bidang_perusahaan yang belum dihapus dengan count leads
            $data = DB::table('m_bidang_perusahaan')
                ->leftJoin('sl_leads', 'm_bidang_perusahaan.id', '=', 'sl_leads.bidang_perusahaan_id')
                ->whereNull('m_bidang_perusahaan.deleted_at') // Hanya tampilkan data yang belum dihapus
                ->select(
                    'm_bidang_perusahaan.id',
                    'm_bidang_perusahaan.nama',
                    'm_bidang_perusahaan.created_at',
                    'm_bidang_perusahaan.created_by',
                    'm_bidang_perusahaan.updated_at',
                    'm_bidang_perusahaan.updated_by',
                    'm_bidang_perusahaan.deleted_at',
                    'm_bidang_perusahaan.deleted_by',
                    DB::raw('COUNT(sl_leads.id) as total_leads')
                )
                ->groupBy('m_bidang_perusahaan.id', 'm_bidang_perusahaan.nama', 'm_bidang_perusahaan.created_at', 'm_bidang_perusahaan.created_by', 'm_bidang_perusahaan.updated_at', 'm_bidang_perusahaan.updated_by', 'm_bidang_perusahaan.deleted_at', 'm_bidang_perusahaan.deleted_by');

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex justify-content-center">
                            <a href="' . route('bidang-perusahaan.view', $row->id) . '" class="btn btn-info btn-sm me-1">
                                <i class="mdi mdi-eye"></i> View
                            </a>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-leads="' . $row->total_leads . '">
                                <i class="mdi mdi-trash-can"></i> Delete
                            </button>
                        </div>
                    ';
                })
                ->addColumn('total_leads', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_leads . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? Carbon::parse($row->created_at)->format('d/m/Y') : '';
                })
                ->rawColumns(['action', 'total_leads']) // biar HTML tombol tidak di-escape
                ->make(true);

        } catch (\Exception $e) {
            \Log::error('Error in DataTables: ' . $e->getMessage());
            SystemController::saveError($e, Auth::user(), $request);
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function add(Request $request)
    {
        return view('master.bidang-perusahaan.add');
    }
    
    public function delete(Request $request)
    {
        try {
            // Check if there are leads associated with this bidang perusahaan
            $leadsCount = DB::table('sl_leads')
                ->where('bidang_perusahaan_id', $request->id)
                ->count();

            if ($leadsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus bidang perusahaan ini karena masih memiliki {$leadsCount} leads. Hapus atau pindahkan leads terlebih dahulu."
                ], 400);
            }

            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_bidang_perusahaan')->where('id', $request->id)->update([
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
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    public function view(Request $request, $id)
    {
        try {
            // Get bidang perusahaan data
            $data = DB::table('m_bidang_perusahaan')->where('id', $id)->first();
            
            if (!$data) {
                return redirect()->route('bidang-perusahaan')->with('error', 'Data tidak ditemukan');
            }

            // Get leads associated with this bidang perusahaan
            $leads = DB::table('sl_leads')
                ->leftJoin('m_jenis_perusahaan', 'sl_leads.jenis_perusahaan_id', '=', 'm_jenis_perusahaan.id')
                ->leftJoin('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
                ->where('sl_leads.bidang_perusahaan_id', $id)
                ->select(
                    'sl_leads.*',
                    'm_jenis_perusahaan.nama as jenis_perusahaan',
                    'm_status_leads.nama as status_leads',
                    'm_status_leads.warna_background',
                    'm_status_leads.warna_font'
                )
                ->orderBy('sl_leads.created_at', 'desc')
                ->get();

            // Count total leads
            $totalLeads = $leads->count();

            return view('master.bidang-perusahaan.view', compact('data', 'leads', 'totalLeads'));

        } catch (\Exception $e) {
            \Log::error('Error in view method: ' . $e->getMessage());
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->route('bidang-perusahaan')->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    public function edit(Request $request, $id)
    {
        $data = DB::table('m_bidang_perusahaan')->where('id', $id)->first();
        if (!$data) {
            return redirect()->route('bidang-perusahaan')->with('error', 'Data tidak ditemukan');
        }
        return view('master.bidang-perusahaan.edit', compact('data'));
    }

    public function save(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nama' => 'required|max:255',
            ], [
                'required' => ':attribute harus diisi',
                'max' => ':attribute maksimal :max karakter',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            $current_date_time = Carbon::now()->toDateTimeString();
            
            if (!empty($request->id)) {
                // Update data
                DB::table('m_bidang_perusahaan')->where('id', $request->id)->update([
                    'nama' => $request->nama,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name ?? Auth::user()->name
                ]);
                $msgSave = 'Bidang perusahaan "' . $request->nama . '" berhasil diperbarui.';
            } else {
                // Insert data baru
                DB::table('m_bidang_perusahaan')->insert([
                    'nama' => $request->nama,
                    'created_by' => Auth::user()->full_name ?? Auth::user()->name,
                    'created_at' => $current_date_time
                ]);
                $msgSave = 'Bidang perusahaan "' . $request->nama . '" berhasil ditambahkan.';
            }
            
            DB::commit();
            return redirect()->route('bidang-perusahaan')->with('success', $msgSave);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving bidang perusahaan: ' . $e->getMessage());
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data')->withInput();
        }
    }

    /**
     * Export leads by bidang perusahaan to Excel
     */
    public function exportLeads(Request $request, $id)
    {
        try {
            $bidangPerusahaan = DB::table('m_bidang_perusahaan')->where('id', $id)->first();
            
            if (!$bidangPerusahaan) {
                return redirect()->back()->with('error', 'Bidang perusahaan tidak ditemukan');
            }

            $leads = DB::table('sl_leads')
                ->leftJoin('m_jenis_perusahaan', 'sl_leads.jenis_perusahaan_id', '=', 'm_jenis_perusahaan.id')
                ->where('sl_leads.bidang_perusahaan_id', $id)
                ->select(
                    'sl_leads.nama_perusahaan',
                    'sl_leads.telp_perusahaan', 
                    'm_jenis_perusahaan.nama as jenis_perusahaan',
                    'sl_leads.branch_id',
                    'sl_leads.platform_id',
                    'sl_leads.created_at'
                )
                ->orderBy('sl_leads.created_at', 'desc')
                ->get();

            // You can implement Excel export here using Maatwebsite\Excel
            // For now, return JSON data
            return response()->json([
                'success' => true,
                'data' => $leads,
                'bidang_perusahaan' => $bidangPerusahaan->nama
            ]);

        } catch (\Exception $e) {
            \Log::error('Error exporting leads: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export data'
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class IssueController extends Controller
{
    public function index()
    {
        return view('sales.issue.list');
    }
    public function list(Request $request)
    {
        try {
            $data = DB::table('sl_issue')
                ->leftJoin('sl_pks', 'sl_pks.id', '=', 'sl_issue.pks_id')->leftJoin('sl_leads', 'sl_leads.id', '=', 'sl_issue.leads_id')
                ->leftJoin('sl_site', 'sl_site.id', '=', 'sl_issue.site_id')
                ->select('sl_issue.*', 'sl_leads.nama_perusahaan', 'sl_pks.nomor', 'sl_site.nama_site')->whereNull('sl_issue.deleted_at')->get();

            foreach ($data as  $value) {
                $value->created_at = Carbon::parse($value->created_at)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '
        <div class="justify-content-center d-flex">
            <a href="' . route('issue.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a> &nbsp;
            <form id="delete-form-' . $data->id . '" action="' . route('issue.delete', $data->id) . '" method="POST" style="display:inline;">
                ' . csrf_field() . '
                ' . method_field('POST') . '
                <button type="button" class="btn btn-danger waves-effect btn-xs" onclick="confirmDelete(' . $data->id . ')"><i class="mdi mdi-delete"></i>&nbsp;Delete</button>
            </form>
        </div>
    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function add()
    {
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        return view('sales.issue.add', compact('now'));
    }
    public function save(Request $request)
    {
        try {
            DB::beginTransaction();
            $link = null;

            if($request->hasFile('lampiran')) {
           
            $file = $request->file('lampiran');
            $extension = $file->getClientOriginalExtension();

            $filename = $file->getClientOriginalName();
            $filename = str_replace(".".$extension,"",$filename);
            $originalName = $filename.date("YmdHis").rand(10000,99999).".".$extension."";

            Storage::disk('lampiran-issue')->put($originalName, file_get_contents($file));

            $link = env('APP_URL').'/public/uploads/lampiran-issue/'.$originalName;
            }
            DB::table('sl_issue')->insert([
                'leads_id' => $request->lead_id,
                'pks_id' => $request->pks_id,
                'site_id' => $request->site_id,
                'judul' => $request->judul,
                'jenis_keluhan' => $request->jenis_keluhan,
                'kolaborator' => $request->kolaborator,
                'status' => $request->status ?? 'Open',
                'deskripsi' => $request->issue_deskripsi,
                'url_lampiran' => $link ,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Gagal Disimpan');
        }
    }
    public function view($id)
    {
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $data = DB::table('sl_issue')
            ->leftJoin('sl_pks', 'sl_pks.id', '=', 'sl_issue.pks_id')
            ->leftJoin('sl_leads', 'sl_leads.id', '=', 'sl_issue.leads_id')
            ->leftJoin('sl_site', 'sl_site.id', '=', 'sl_issue.site_id')
            ->select('sl_issue.*', 'sl_leads.nama_perusahaan', 'sl_pks.nomor', 'sl_site.nama_site')
            ->where('sl_issue.id', $id)
            ->first();

        if (!$data) {
            abort(404);
        }

        return view('sales.issue.view', compact('data','now'));
    }
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

             $link = null;

            if($request->hasFile('lampiran')) {
           
            $file = $request->file('lampiran');
            $extension = $file->getClientOriginalExtension();

            $filename = $file->getClientOriginalName();
            $filename = str_replace(".".$extension,"",$filename);
            $originalName = $filename.date("YmdHis").rand(10000,99999).".".$extension."";

            Storage::disk('lampiran-issue')->put($originalName, file_get_contents($file));

            $link = env('APP_URL').'/public/uploads/lampiran-issue/'.$originalName;
            }
            DB::table('sl_issue')->where('id', $id)->update([
               'judul' => $request->judul,
                'jenis_keluhan' => $request->jenis_keluhan,
                'kolaborator' => $request->kolaborator,
                'status' => $request->status ?? 'Open',
                'deskripsi' => $request->issue_deskripsi,
                'url_lampiran' => $link,
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::user()->full_name,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Gagal Diupdate');
        }
    }
    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            DB::table('sl_issue')->where('id', $id)->update([
                'deleted_at' => Carbon::now()->toDateTimeString(),
                'deleted_by' => Auth::user()->full_name,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Gagal Dihapus');
        }
    }
    public function leadsList(Request $request)
    {
        try {
            $data = DB::table('sl_leads')
                ->where('is_aktif', '!=', 0)
                ->whereNull('deleted_at')
                ->get();
            foreach ($data as $value) {
                $value->created_at = Carbon::parse($value->created_at)->isoFormat('D/MM/Y');
            };

            return DataTables::of($data)
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function pksList(Request $request)
    {
        try {
            $data = DB::table('sl_pks')
                ->where('leads_id', $request->lead_id)
                ->where('is_aktif', '!=', '0')
                ->whereNull('deleted_at')
                ->get();
            foreach ($data as $value) {
                $value->created_at = Carbon::parse($value->created_at)->isoFormat('D/MM/Y');
            };

            return DataTables::of($data)
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function siteList(Request $request)
    {
        try {
            $data = DB::table('sl_site')
                ->where('leads_id', $request->lead_id)
                ->whereNull('deleted_at')->get();

            return DataTables::of($data)
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->isoFormat('D/MM/Y');
                })
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
}

<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index()
    {
        return view('master.loyalty.list');
    }
    public function list(Request $request)
    {
        try {
            $data = DB::table('m_loyalty')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data)  {
                    return '
        <div class="justify-content-center d-flex">
            <a href="' . route('loyalty.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a> &nbsp;
            <form id="delete-form-' . $data->id . '" action="' . route('loyalty.delete', $data->id) . '" method="POST" style="display:inline;">
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
        return view('master.loyalty.add', compact('now'));
    }
public function save(Request $request)
    {

        try {
            DB::beginTransaction();
            DB::table('m_loyalty')->insert([
                'nama' => $request->loyalty,
                'raw_icon' => $request->icon,
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
        $data = DB::table('m_loyalty')->where('id', $id)->first();
        if (!$data) {
            return redirect()->route('loyalty')->with('error', 'Data tidak ditemukan');
        }
        return view('master.loyalty.view', compact('data', 'now'));
    }
    public function delete(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            DB::table('m_loyalty')->where('id', $id)->update([
                'deleted_at' => Carbon::now()->toDateTimeString(),
                'deleted_by' => Auth::user()->full_name]);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Gagal Dihapus');
        }
    }

    public function update(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            DB::table('m_loyalty')->where('id', $id)->update([
                'nama' => $request->loyalty,
                'raw_icon' => $request->icon,
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
}

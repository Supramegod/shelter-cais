<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Http\Controllers\SystemController;

class RoleController extends Controller
{
    public function index()
    {
        return view('master.role.list');
    }
    public function list(Request $request)
    {
        try {
            $db = DB::connection('mysqlhris')->getDatabaseName();
            $data = DB::table($db . '.m_role')
                ->where('is_active', '!=', 0)->get();


            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<div class="justify-content-center d-flex">
                                    <a href="' . route('role.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a> &nbsp;
                        </div>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function view($id)
    {
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $db = DB::connection('mysqlhris')->getDatabaseName();
            $data = DB::table($db . '.m_role')
                ->where('id', $id)
                ->first();
            return view('master.role.view', compact('data', 'now'));
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
            abort(500);
        }
    }
    public function listMenu(Request $request)
    {


        try {
            $data = DB::table('sysmenu')
                ->join('sysmenu_role', 'sysmenu_role.sysmenu_id', '=', 'sysmenu.id')
                ->where('sysmenu_role.role_id', $request->id)
                ->select(
                    'sysmenu.id',
                    'sysmenu.nama',
                    'sysmenu.kode',
                    'sysmenu_role.is_view',
                    'sysmenu_role.is_add',
                    'sysmenu_role.is_edit',
                    'sysmenu_role.is_delete',
                )->get();

            return DataTables::of($data)
                ->addColumn('is_view', function ($row) {
                    return '<input type="checkbox" class="perm-check" data-id="' . $row->id . '" data-field="is_view" ' . ($row->is_view ? 'checked' : '') . ' />';
                })
                ->addColumn('is_add', function ($row) {
                    return '<input type="checkbox" class="perm-check" data-id="' . $row->id . '" data-field="is_add" ' . ($row->is_add ? 'checked' : '') . ' />';
                })
                ->addColumn('is_edit', function ($row) {
                    return '<input type="checkbox" class="perm-check" data-id="' . $row->id . '" data-field="is_edit" ' . ($row->is_edit ? 'checked' : '') . ' />';
                })
                ->addColumn('is_delete', function ($row) {
                    return '<input type="checkbox" class="perm-check" data-id="' . $row->id . '" data-field="is_delete" ' . ($row->is_delete ? 'checked' : '') . ' />';
                })
                ->rawColumns(['is_view', 'is_add', 'is_edit', 'is_delete'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function updateAkses(Request $request)
{
    DB::beginTransaction();
    try {
        $akses = $request->input('akses', []);
        $roleId = $request->input('role_id');

        foreach ($akses as $item) {
            $sysmenuId = $item['sysmenu_id'];
            $field = $item['field'];
            $value = $item['value'];
            $record = DB::table('sysmenu_role')
                ->where('role_id', $roleId)
                ->where('sysmenu_id', $sysmenuId)
                ->first();

            if ($record) {
                
                DB::table('sysmenu_role')
                    ->where('role_id', $roleId)
                    ->where('sysmenu_id', $sysmenuId)
                    ->update([$field => $value]);
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Akses berhasil disimpan');
    } catch (\Exception $e) {
        DB::rollBack();
        SystemController::saveError($e, Auth::user(), $request);
          return redirect()->back()->with('error', 'Akses gagal disimpan');
    }
}

}

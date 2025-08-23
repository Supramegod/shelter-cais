<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;


class MasterMenuController extends Controller
{
    public function index()
    {
        return view('master.menu.list');
    }
    public function list(Request $request)
    {
        try {

            $menu = DB::table('sysmenu')->whereNull('deleted_at')->get();
            $data = collect($this->formatMenu($menu));
            foreach ($data as  $value) {
                $value->created_at = Carbon::parse($value->created_at)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) use ($menu) {
                    $childNames = $this->getChildNames($menu, $data->id);
                    $childJson = htmlspecialchars(json_encode($childNames), ENT_QUOTES, 'UTF-8');
                    return '
        <div class="justify-content-center d-flex">
           
            <a href="' . route('master.menu.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a> &nbsp;
            <form id="delete-form-' . $data->id . '" action="' . route('master.menu.delete', $data->id) . '" method="POST" style="display:inline;">
                ' . csrf_field() . '' . method_field('POST') . '
                <button type="button" class="btn btn-danger waves-effect btn-xs" onclick="confirmDelete(' . $data->id . ',' . $childJson . ')"><i class="mdi mdi-delete"></i>&nbsp;Delete</button>
            </form>
        </div>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function add(Request $request)
    {
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $parent = DB::table('sysmenu')->whereNull('deleted_at')->get();
        return view('master.menu.add', compact('request', 'parent', 'now'));
    }
    public function view(Request $request, $id)
    {
        $data = DB::table('sysmenu')->where('id', $id)->first();
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $parent = DB::table('sysmenu')->whereNull('deleted_at')->get();
        return view('master.menu.view', compact('request', 'data', 'parent', 'now'));
    }
    public function save(Request $request)
    {

        try {

            if ($request->menu_parent == null) {
                $kode = $request->kode;
            } else {
                $kode = $this->generateKode($request->menu_parent);
            }
            DB::table('sysmenu')->insert([
                'nama' => $request->nama,
                'kode' => $kode,
                'parent_id' => $request->menu_parent,
                'url' => $request->url,
                'icon' => $request->icon,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->full_name,

            ]);
            return redirect()->back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Gagal Disimpan');
            
        }
    }
    public function listRole(Request $request)
    {
        try {
            $db = DB::connection('mysqlhris')->getDatabaseName();
            $data = DB::table($db . '.m_role as m_role')
                ->leftJoin('sysmenu_role', function ($join) use ($request) {
                    $join->on('sysmenu_role.role_id', '=', 'm_role.id')
                        ->where('sysmenu_role.sysmenu_id', '=', $request->sysmenu_id);
                })
                ->select(
                    'm_role.id',
                    'm_role.name',
                    DB::raw('IFNULL(sysmenu_role.is_view, 0) as is_view'),
                    DB::raw('IFNULL(sysmenu_role.is_add, 0) as is_add'),
                    DB::raw('IFNULL(sysmenu_role.is_edit, 0) as is_edit'),
                    DB::raw('IFNULL(sysmenu_role.is_delete, 0) as is_delete')
                )
                ->where('m_role.is_active', '!=', 0)
                ->get();

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

    public function simpanRole(Request $request)
    {
        try {
            $sysmenuId = $request->sysmenu_id;
            $groupedAkses = collect($request->akses)
                ->groupBy('role_id')
                ->map(function ($fields) {
                    return [
                        'is_view'   => (int)($fields->firstWhere('field', 'is_view')['value'] ?? 0),
                        'is_add'    => (int)($fields->firstWhere('field', 'is_add')['value'] ?? 0),
                        'is_edit'   => (int)($fields->firstWhere('field', 'is_edit')['value'] ?? 0),
                        'is_delete' => (int)($fields->firstWhere('field', 'is_delete')['value'] ?? 0),
                    ];
                });

            $existingData = DB::table('sysmenu_role')
                ->where('sysmenu_id', $sysmenuId)
                ->get()
                ->keyBy('role_id');

            $listInsert = [];
            $listUpdate = [];

            foreach ($groupedAkses as $roleId => $permissions) {
                if (isset($existingData[$roleId])) {
                    $old = (array)$existingData[$roleId];
                    $changed = [];

                    foreach ($permissions as $key => $val) {
                        if ((int)$old[$key] !== (int)$val) {
                            $changed[$key] = $val;
                        }
                    }

                    if (!empty($changed)) {
                        $changed['updated_at'] = Carbon::now()->toDateTimeString();
                        $changed['updated_by'] = Auth::user()->full_name;

                        $listUpdate[] = [
                            'role_id' => $roleId,
                            'fields'  => $changed
                        ];
                    }
                } else {
                    if (array_sum($permissions) > 0) {
                        $listInsert[] = array_merge([
                            'sysmenu_id' => $sysmenuId,
                            'role_id'    => $roleId,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'created_by' => Auth::user()->full_name,
                        ], $permissions);
                    }
                }
            }

            foreach ($listUpdate as $update) {
                DB::table('sysmenu_role')
                    ->where('sysmenu_id', $sysmenuId)
                    ->where('role_id', $update['role_id'])
                    ->update($update['fields']);
            }

            if (!empty($listInsert)) {
                DB::table('sysmenu_role')->insert($listInsert);
            }

            return redirect()->back()->with('success', 'Data Akses Berhasil Disimpan');
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Akses Gagal Disimpan');
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $menu = DB::table('sysmenu')->where('id', $id)->first();
            if (!$menu) {
                return redirect()->back()->with('error', 'Menu tidak ditemukan');
            }

            $oldUrl = rtrim($menu->url, '/');
            $newUrl = rtrim($request->url, '/');
            DB::table('sysmenu')->where('id', $id)->update([
                'nama'       => $request->nama,
                'kode'       => $request->kode,
                'url'        => $request->url,
                'icon'       => $request->icon,
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::user()->full_name,
            ]);

            $this->updateChildrenUrl($id, $oldUrl, $newUrl);

            return redirect()->back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            return redirect()->back()->with('error', 'Data Gagal Disimpan');
        }
    }

    private function updateChildrenUrl($parentId, $oldPrefix, $newPrefix)
    {
        $children = DB::table('sysmenu')
            ->where('parent_id', $parentId)
            ->get();

        foreach ($children as $child) {
            $childNewUrl = preg_replace(
                '#^' . preg_quote($oldPrefix, '#') . '#',
                $newPrefix,
                $child->url
            );

            DB::table('sysmenu')
                ->where('id', $child->id)
                ->update([
                    'url'        => $childNewUrl,
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'updated_by' => Auth::user()->full_name,
                ]);

            $this->updateChildrenUrl($child->id, rtrim($child->url, '/'), rtrim($childNewUrl, '/'));
        }
    }


    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $idDelete = [$id];
            $childIds = $this->getChildId($id);
            $idDelete = array_merge($idDelete, $childIds);

            DB::table('sysmenu')->whereIn('id', $idDelete)->update([
                'deleted_at' => Carbon::now()->toDateTimeString(),
                'deleted_by' => Auth::user()->full_name,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus menu.');
        }
    }
    private function getChildId($parentId)
    {
        $childs = DB::table('sysmenu')
            ->where('parent_id', $parentId)
            ->whereNull('deleted_at')
            ->pluck('id');
        $all = [];

        foreach ($childs as $childId) {
            $all[] = $childId;
            $all = array_merge($all, $this->getChildId($childId));
        }

        return $all;
    }
    private function getChildNames($data, $parentId)
    {
        $childNames = [];

        foreach ($data as $menu) {
            if ($menu->parent_id == $parentId) {
                $childNames[] = $menu->nama;
                $childNames = array_merge($childNames, $this->getChildNames($data, $menu->id));
            }
        }

        return $childNames;
    }


    private function formatMenu($data, $parentId = null, $prefix = '')
    {
        $result = [];

        foreach ($data as $menu) {
            if ($menu->parent_id == $parentId) {
                $menu->nama = $prefix . $menu->nama;
                $result[] = $menu;

                $children = $this->formatMenu($data, $menu->id, $prefix . '- ');
                $result = array_merge($result, $children);
            }
        }

        return $result;
    }

    private function generateKode($id)
    {
        $parent = DB::table('sysmenu')->where('id', $id)->select('kode')->first();
        $jumlahChild = DB::table('sysmenu')->where('parent_id', $id)->count();
        $newChild = str_pad($jumlahChild + 1, 2, '0', STR_PAD_LEFT);
        $kode = $parent->kode . '.' . $newChild;
        return $kode;
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use function PHPUnit\Framework\returnCallback;

class PositionController extends Controller
{

    public function index(Request $request)
    {
        $db =  DB::connection('mysqlhris')->getDatabaseName();
        $company = DB::table($db . '.m_company')->get();
        $service = DB::table('m_kebutuhan')->get();


        return view('master.position.list', compact('company', 'service', 'request'));
    }
    public function list(Request $request)
    {
        try {

            $db =  DB::connection('mysqlhris')->getDatabaseName();
            $data = DB::table($db . '.m_position as m_position')->join(
                $db . '.m_company as m_company',
                'm_company.id',
                '=',
                'm_position.company_id'
            )->join('m_kebutuhan', 'm_kebutuhan.id', '=', 'm_position.layanan_id')->join($db . '.m_user as m_user', 'm_user.id', '=', 'm_position.created_by')
                ->select('m_position.id', 'm_company.name as entitas', 'm_position.name', 'm_position.description', 'm_kebutuhan.nama as layanan',  DB::raw('DATE(m_position.created_at) as created_at'), 'm_user.username as created_by')
                ->where('m_position.is_active', '!=', 0);


            if (!empty($request->entitas)) {
                $data = $data->where('m_company.id', $request->entitas);
            }

            if (!empty($request->layanan)) {
                $data = $data->where('m_kebutuhan.id', $request->layanan);
            }

            $data = $data->get();
            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '
        <div class="justify-content-center d-flex">
            <a href="' . route('position.view', $data->id) . '" class="btn btn-primary waves-effect btn-xs">View</a> &nbsp;

            <form action="' . route('position.delete', $data->id) . '" method="POST" style="display:inline;">
                ' . csrf_field() . '
                ' . method_field('POST') . '
                <button type="submit" class="btn btn-danger waves-effect btn-xs" onclick="return confirm(\'Yakin ingin menghapus?\')">Delete</button>
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
    public function add(Request $request)
    {
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $db =  DB::connection('mysqlhris')->getDatabaseName();
        $company = DB::table($db . '.m_company')->get();
        $service = DB::table('m_kebutuhan')->get();
        return view('master.position.add', compact('company', 'service', 'request', 'now'));
    }
    public function save(Request $request)
    {
        try {
            $request->validate([
                'entitas' => 'required',
                'layanan' => 'required',
                'nama' => 'required|string|max:255',
                'deskripsi' => 'required|string',
            ]);

            $db =  DB::connection('mysqlhris')->getDatabaseName();
            DB::table($db . '.m_position')->insert([
                'company_id' => $request->entitas,
                'name' => $request->nama,
                'description' => $request->deskripsi,
                'layanan_id' => $request->layanan,
                'is_active' => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,


            ]);

            return redirect()->back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function edit(Request $request, $id)
    {
        try {


            $db =  DB::connection('mysqlhris')->getDatabaseName();
            DB::table($db . '.m_position')->where('id', $id)->update([
                'company_id' => $request->entitas,
                'name' => $request->nama,
                'description' => $request->deskripsi,
                'layanan_id' => $request->layanan,
                'updated_at' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
                'updated_by' => Auth::user()->id,
            ]);
            DB::table('m_requirement_posisi')->where('position_id', $id)->update([

                'kebutuhan_id' => $request->layanan,
                'updated_at' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
                'updated_by' => Auth::user()->id,
            ]);


            return redirect()->back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function delete(Request $request, $id)
    {
        try {
            $db =  DB::connection('mysqlhris')->getDatabaseName();
            DB::table($db . '.m_position')->where('id', $id)->update([
                'is_active' => 0,
                'updated_at' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
                'updated_by' => Auth::user()->id,
            ]);
            return redirect()->back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {

            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $db =  DB::connection('mysqlhris')->getDatabaseName();
            $data = DB::table($db . '.m_position as m_position')->where(
                'm_position.id',
                $id
            )->join(
                $db . '.m_company as m_company',
                'm_company.id',
                '=',
                'm_position.company_id'
            )->join('m_kebutuhan', 'm_kebutuhan.id', '=', 'm_position.layanan_id')->join($db . '.m_user as m_user', 'm_user.id', '=', 'm_position.created_by')
                ->select('m_position.id', 'm_position.name', 'm_company.id as company_id', 'm_kebutuhan.id as layanan_id', 'm_position.description as deskripsi')->first();
            $requirement = DB::table('m_requirement_posisi')->where('position_id', $data->id)->get();
            $company = DB::table($db . '.m_company')->get();
            $service = DB::table('m_kebutuhan')->get();
            return view('master.position.view', compact('data', 'requirement', 'now', 'company', 'service'));
        } catch (\Exception $e) {

            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function addRequirement(Request $request)
    {
        try {


            DB::table('m_requirement_posisi')->insert([
                'position_id' => $request->position_id,
                'requirement' => $request->nama,
                'kebutuhan_id' => $request->layanan_id,
                'created_by' => Auth::user()->full_name,
                'created_at' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
            ]);

            return redirect()->back()->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {

            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function requirementEdit(Request $request)
    {
        try {
            DB::table('m_requirement_posisi')->where('id', $request->id)->update([
                'requirement' => $request->requirement,
                'updated_by' => Auth::user()->full_name,
                'updated_at' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
            ]);
            return response()->json(['success' => true, 'message' => 'Data Berhasil Diubah']);
        } catch (\Exception $e) {

            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function requirementDelete(Request $request)
    {
        try {
            DB::table('m_requirement_posisi')->where('id', $request->id)->update([
                'deleted_at' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
                'deleted_by' => Auth::user()->full_name,
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {

            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }
    public function requirementList(Request $request)
    {
        try {

            $data = DB::table('m_requirement_posisi as m_requirement')
                ->where('m_requirement.position_id', $request->id)
                ->whereNull('deleted_at')
                ->get();

            return DataTables::of($data)
                ->addColumn('requirement', function ($data) {
                    return  '<input type="text" class="form-control" value="' . e($data->requirement) . '" data-id="' . $data->id . '">';
                })
                ->addColumn('aksi', function ($data) {
                    return '
                   <button class="btn btn-sm btn-primary btn-edit" data-id="' . $data->id . '">Edit</button>
        <button class="btn btn-danger btn-sm btn-delete" data-id="' . $data->id . '">Delete</button>
        ';
                })
                ->rawColumns(['requirement', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {

            SystemController::saveError($e, Auth::user(), $request);
            abort(500, 'Terjadi kesalahan saat memuat data.');
        }
    }
}

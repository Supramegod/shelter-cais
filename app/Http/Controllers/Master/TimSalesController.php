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

class TimSalesController extends Controller
{
    public function index(Request $request){

        return view('master.tim-sales.list');
    }

    public function list(Request $request){
        try {
            // $data = DB::table('m_tim_sales')->whereNull('deleted_at')->get();
            $data = DB::select('select m.id, m.nama, m.branch, m.branch_id, m.created_at, m.created_by, COUNT(d.id) AS jumlah
                    from m_tim_sales m
                    left join m_tim_sales_d d ON m.id = d.tim_sales_id
                    where d.deleted_at is null
                    group by m.id');
            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <a href="'.route('tim-sales.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                    <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function add(Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');
        $listBranch = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();

        return view('master.tim-sales.add',compact('now', 'listBranch'));
    }

    public function view(Request $request,$id){
        try {
            $data = DB::table('m_tim_sales')->where('id',$id)->first();
            $listBranch = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();
            $listUser = DB::connection('mysqlhris')->table('m_user')->where('is_active',1)->whereIn('role_id', [29,31])->where('branch_id',$data->branch_id)->get();

            return view('master.tim-sales.view',compact('data', 'listBranch', 'listUser'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nama' => 'required',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);
    
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $current_date_time = Carbon::now()->toDateTimeString();
                $branch = DB::connection('mysqlhris')->table('m_branch')->where('id',$request->branch_id)->first();

                $msgSave = '';
                if(!empty($request->id)){
                    DB::table('m_tim_sales')->where('id',$request->id)->update([
                        'nama'          => $request->nama,
                        'branch_id'     => $request->branch_id,
                        'branch'        => $branch->name,
                        'updated_at'    => $current_date_time,
                        'updated_by'    => Auth::user()->full_name
                    ]);
                }else{
                    DB::table('m_tim_sales')->insert([
                        'nama'          => $request->nama,
                        'branch_id'     => $request->branch_id,
                        'branch'        => $branch->name,
                        'created_at'    => $current_date_time,
                        'created_by'    => Auth::user()->full_name
                    ]);
                }
                $msgSave = 'Tim Sales '.$request->nama.' berhasil disimpan.';
            }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_tim_sales')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
            DB::table('m_tim_sales_d')->where('tim_sales_id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addDetailSales(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $tim_sales_id = $request->tim_sales_id;
            $user_id = $request->user_id;

            $user = DB::connection('mysqlhris')->table('m_user')->where('id',$user_id)->first();

            DB::table('m_tim_sales_d')->insert([
                'tim_sales_id' => $tim_sales_id,
                'nama' => $user->full_name,
                'user_id' => $user_id,
                'username' => $user->username,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function listDetailSales (Request $request){
        $data = DB::table('m_tim_sales_d')
        ->where('tim_sales_id',$request->tim_sales_id)
        ->whereNull('deleted_at')
        ->get();

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            if ($data->id==0) {
                return "";
            }
            return '<div class="justify-content-center d-flex">
                        <a href="javascript:void(0)" class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can-outline"></i></a> &nbsp;
                    </div>';
        })
        ->addColumn('is_leader', function ($data) {
            if($data->id==0){
                return "";
            }

            $checked = "";

            if ($data->is_leader==1) {
                $checked = "checked";
            }
            return '<input name="is_leader[]" class="form-check-input set-is-leader" type="radio" value="" data-id="'.$data->id.'" '.$checked.' >';
        })
        ->rawColumns(['aksi','is_leader'])
        ->make(true);
    }

    public function changeIsLeader(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_tim_sales_d')->where('tim_sales_id',$request->tim_sales_id)->update([
                'is_leader' => 0,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('m_tim_sales_d')->where('id',$request->id)->update([
                'is_leader' => 1,
                'updated_by' => Auth::user()->full_name
            ]);
            return "Data Berhasil Ditambahkan";
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
            return "Data Gagal Ditambahkan";
        }
    }

    public function deleteDetailSales(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('m_tim_sales_d')->where('id',$request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

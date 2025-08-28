<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){
                 
        $db = DB::connection('mysqlhris')->getDatabaseName();
                 $data =Auth::user();
                 $role = DB::table($db.'.m_role')->where('id',Auth::user()->role_id)->first();
                 $branch = DB::table($db.'.m_branch')->where('id',Auth::user()->branch_id)->first();
                 $company = DB::table($db.'.m_company')->where('id',Auth::user()->company_id)->first();
                 

                 return view('profile.view',compact('data','role','branch','company'));
    }
    public function listActivity(){
        $activities = DB::table('sl_customer_activity')->join('sl_leads', 'sl_leads.id', 'sl_customer_activity.leads_id')
                 ->leftJoin('m_kebutuhan', 'm_kebutuhan.id', '=', 'sl_leads.kebutuhan_id')
                 ->where('sl_customer_activity.created_by',Auth::user()->full_name)
                 ->select('sl_customer_activity.id','sl_leads.nama_perusahaan','sl_customer_activity.tipe','m_kebutuhan.nama as kebutuhan','sl_customer_activity.created_at','sl_customer_activity.notes')
                 ->whereNull('sl_customer_activity.deleted_at')->get();
                
             return DataTables::of($activities)
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->isoFormat('D MMM Y');
        })
        ->make(true);

    }
}

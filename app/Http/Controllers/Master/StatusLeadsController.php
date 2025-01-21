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

class StatusLeadsController extends Controller
{
    public function index(Request $request){

        return view('master.status-leads.list');
    }

    public function list(Request $request){
        try {
            $data = DB::table('m_status_leads')->whereNull('deleted_at')->get();
            return DataTables::of($data)
                ->addColumn('badge', function ($data) {
                    return '<h6>
                                <span style="background-color: '.$data->warna_background.';color: '.$data->warna_font.';padding: 4px 8px;border-radius: 5px;">'.$data->nama.'</span>
                            </h6>';
                })
                ->rawColumns(['badge'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

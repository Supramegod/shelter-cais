<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function userLoginData () {
        $user = Auth::user();
        $userData = DB::connection('mysqlhris')->table('m_user')->where('id',$user->id)->first();
        $timSalesD = DB::table('m_tim_sales_d')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();
        $userData->tim_sales_id = $timSalesD->tim_sales_id ?? null;
        $userData->tim_sales_d_id = $timSalesD->id ?? null;
        return $userData;
    }

    public static function saveError($e,$user,$request){
        try {
            DB::table('log_error')->insert([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'header_data' => json_encode($request->header()),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'msg' => $e->getMessage()
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

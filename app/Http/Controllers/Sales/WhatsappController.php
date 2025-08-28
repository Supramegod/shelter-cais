<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use \stdClass;



class WhatsappController extends Controller
{
    public function index (Request $request){
        $tglDari = $request->tgl_dari;
        $tglSampai = $request->tgl_sampai;

        if($tglDari==null){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
        }
        if($tglSampai==null){
            $tglSampai = carbon::now()->toDateString();
        }

        $ctglDari = Carbon::createFromFormat('Y-m-d',  $tglDari);
        $ctglSampai = Carbon::createFromFormat('Y-m-d',  $tglSampai);
        

        // $branch = DB::connection('mysqlhris')->table('m_branch')->where('id','!=',1)->where('is_active',1)->get();
        // $status = DB::table('m_status_leads')->whereNull('deleted_at')->get();
        // $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

        $error =null;
        $success = null;
        if($ctglDari->gt($ctglSampai)){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        };
        if($ctglSampai->lt($ctglDari)){
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }

        $tokenWhatsapp = DB::table('m_setting')->where('id', 1)->first();
        $status = '';
        $response = Http::withToken($tokenWhatsapp->value)
            ->get('https://whatsapp.ulilworld.com/server/find-by-cabang/16');
            // dd($response->json()['data']);
        foreach ($response->json()['data'] as $data) {
            // dd($data['status']);
            $status = $data['status'];
        }   
        
        return view('master.whatsapp.list',compact('tglDari','tglSampai','request','error','success', 'status'));
    }

    public function login (Request $request){
        try {
            $response = Http::withBasicAuth('keys', 'secret')
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post('https://whatsapp.ulilworld.com/login', [
                    'username' => 'cais',
                    'password' => 1
                ]);

                // dd($response->json()['data']);
            if( $response->successful() ){
                $current_date_time = Carbon::now()->toDateTimeString();
                DB::table('m_setting')->where('id', 1)->update([
                        'value' => $response->json()['data'],
                        'last_updated' => $current_date_time
                    ]);        
            }elseif( $response->failed() ){
                //do some logic
                //redirect https://www.test2.com/
            }
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function connectQr (Request $request){
        try {
            // $response = Http::withToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6NiwiaWF0IjoxNzM4NzI0MTE1LCJleHAiOjE3Mzg4MTA1MTV9.CII_cSMK0_J2SAEXHOidSXWD7vLatv9GYPuz2CCNw7U')
            //     ->withHeaders([
            //         'Content-Type' => 'application/x-www-form-urlencoded'
            //     ])
            //     ->post('https://whatsapp.ulilworld.com/sessions/add', [
            //         'isLegacy' => 'false',
            //         'id' => 'cais_prod'
            //     ]);

            $response = Http::asForm()->post('https://whatsapp.ulilworld.com/sessions/add', [
                'isLegacy' => 'false',
                'id' => 'cais_prod',
            ]);

            // dd($response->successful());
            if( $response->successful() ){
                // if($response->json()['success'] == 'true'){
                //     dd($response->json()['data']['qr']);
                // }else{
                //     dd($response->json()['message']);
                // }
                
                return response()->json([
                    'success'   => true,
                    'data'      => $response->json()['data']['qr'],
                    'message'   => $response->json()['message']
                ], 200);
                // $current_date_time = Carbon::now()->toDateTimeString();
                // DB::table('m_setting')->where('id', 1)->update([
                //         'value' => $response->json()['data'],
                //         'last_updated' => $current_date_time
                //     ]);        
            }else{
                return response()->json([
                    'success'   => false,
                    'data'      => [],
                    'message'   => $response->json()['message']
                ], 200);
                // dd($response->json());
                //do some logic
                //redirect https://www.test2.com/
            }
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function message (Request $request){
        try {
            $tokenWhatsapp = DB::table('m_setting')->where('id', 1)->first();
            $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Bearer '.$tokenWhatsapp->value
            ])
            ->post('https://whatsapp.ulilworld.com/chats/send?id=cais_prod', [
                'receiver' => '628986362990',
                'message' => 'cais_prod',
            ]);

            // dd($response->json());
            if( $response->successful() ){
                return response()->json([
                    'success'   => true,
                    'data'      => $response->json()['data'],
                    'message'   => $response->json()['message']
                ], 200);
            }else{
                return response()->json([
                    'success'   => false,
                    'data'      => [],
                    'message'   => $response->json()['message']
                ], 200);
            }
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function sendMessage(Request $request){
        try {
            $tokenWhatsapp = DB::table('m_setting')->where('id', 1)->first();
            $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Bearer '.$tokenWhatsapp->value
            ])
            ->post('https://whatsapp.ulilworld.com/chats/send?id=cais_prod', [
                'receiver' => $request->no_wa,
                'message' => $request->message,
            ]);

            if( $response->successful() ){
                $current_date_time = Carbon::now()->toDateTimeString();
                DB::table('training_gada_calon')->where('id', $request->id)->update([
                    'last_sent_notif_register' => $current_date_time
                ]);        
                return response()->json([
                    'success'   => true,
                    'data'      => $response->json()['data'],
                    'message'   => $response->json()['message']
                ], 200);
            }else{
                return response()->json([
                    'success'   => false,
                    'data'      => [],
                    'message'   => $response->json()['message']
                ], 200);
            }
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function connectStatus (Request $request){
        try {
            $tokenWhatsapp = DB::table('m_setting')->where('id', 1)->first();
            $response = Http::withToken($tokenWhatsapp->value)
            ->get('https://whatsapp.ulilworld.com/server/find-by-cabang/16');
            foreach ($response->json()['data'] as $data) {
                return response()->json([
                    'success'   => true,
                    'data'      => $data,
                    'message'   => $response->json()['message']
                ], 200);
            }
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list(Request $request){
        try {

            $data = DB::table('whatsapp_message')->where('is_active', 1)->orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                // ->addColumn('aksi', function ($data) {
                //     return '<div class="justify-content-center d-flex">
                        
                //     </div>';
                // })
                // ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

}

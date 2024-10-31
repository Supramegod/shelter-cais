<?php

namespace App\Http\Controllers\Fitur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\IpUtils;
use Carbon\Carbon;


class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $branch = DB::connection('mysqlhris')->table('m_branch')->where('is_active',1)->get();
        $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();

        $platform = "";
        if(!empty($request->platform)){
            $platform = $request->platform;
        }
        return view('home.landing',compact('branch','platform','kebutuhan'));
    }

    public function contactSave(Request $request){
        try {
            $recaptcha_response = $request->input('g-recaptcha-response');

            if (is_null($recaptcha_response)) {
                return redirect()->back()->with('error', 'Selesaikan Recaptcha untuk melanjutkan');
            }

            $url = "https://www.google.com/recaptcha/api/siteverify";
            $body = [
                'secret' => "6Lf-yCAqAAAAAF1WPcv-X5-AZcHU9xkmxad93fM7",
                'response' => $recaptcha_response,
                'remoteip' => IpUtils::anonymize($request->ip()) //anonymize the ip to be GDPR compliant. Otherwise just pass the default ip address
            ];
    
            $response = Http::asForm()->post($url, $body);
    
            $result = json_decode($response);
    
            if ($response->successful() && $result->success == true) {
                DB::beginTransaction();
                $current_date_time = Carbon::now()->toDateTimeString();
                $nomor = $this->generateNomor();

                $qplatform = DB::table('m_platform')->where('nama',$request->platform)->whereNull('deleted_at')->first();
                $platform = null;
                if($qplatform!=null){
                    $platform = $qplatform->id;
                }

                DB::table('sl_leads')->insert([
                    'nomor' =>  $nomor,
                    'tgl_leads' => $current_date_time,
                    'nama_perusahaan' => $request->nama_perusahaan,
                    'branch_id' => $request->branch,
                    'platform_id' => $platform,
                    'kebutuhan_id' =>  $request->kebutuhan,
                    'pic' =>  $request->nama,
                    'jabatan' =>  $request->jabatan,
                    'no_telp' => $request->no_telepon,
                    'email' => $request->email,
                    'status_leads_id' => 1,
                    'notes' => $request->pesan,
                    'created_at' => $current_date_time,
                    'created_by' => 'form'
                ]);    
                DB::commit();
                return redirect()->back()->with('success', "Terima kasih telah mengisi form.");
            } else {
                return redirect()->back()->with('error', 'Selesaikan Recaptcha lagi untuk melanjutkan');
            }


        } catch (\Exception $e) {
            abort(500);
        }
        return null;
    }

    public function apiContactSave(Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $nomor = $this->generateNomor();

            $qplatform = DB::table('m_platform')->where('nama',$request->platform)->whereNull('deleted_at')->first();
            $platform = null;
            if($qplatform!=null){
                $platform = $qplatform->id;
            }

            DB::table('sl_leads')->insert([
                'nomor' =>  $nomor,
                'tgl_leads' => $current_date_time,
                'nama_perusahaan' => $request->nama_perusahaan,
                'branch_id' => $request->branch,
                'platform_id' => $platform,
                'kebutuhan_id' =>  $request->kebutuhan,
                'pic' =>  $request->nama,
                'jabatan' =>  $request->jabatan,
                'no_telp' => $request->no_telepon,
                'email' => $request->email,
                'status_leads_id' => 1,
                'notes' => $request->pesan,
                'created_at' => $current_date_time,
                'created_by' => 'api'
            ]);    
            DB::commit();
            return response()->json([
                'success' => true,
                'msg' => 'Berhasil mengirim data',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Gagal mengirim data , silahkan cek kembali inputan anda',
            ]);
            abort(500);
        }
    }

    public function generateNomor (){
        //generate nomor 065 = A , 090 = Z , 048 = 1 , 057 = 9;

        //dapatkan dulu last leads 
        $nomor = "AAAAA";

        $lastLeads = DB::table('sl_leads')->orderBy('id', 'DESC')->first();
        if($lastLeads!=null){
            $nomor = $lastLeads->nomor;
            $chars = str_split($nomor);
            for ($i=count($chars)-1; $i >= 0; $i--) { 
                //dapatkan ascii dari character
                $ascii = ord($chars[$i]);

                if(($ascii >= 48 && $ascii < 57) || ($ascii >= 65 && $ascii < 90)){
                    $ascii += 1;
                }else if ($ascii == 90 ) {
                    $ascii = 48;
                }else{
                    continue;
                }

                $ascchar = chr($ascii);
                $nomor = substr_replace($nomor,$ascchar,$i);
                break;
            }
            if(strlen($nomor)<5){
                $jumlah = 5-strlen($nomor);
                for ($i=0; $i < $jumlah; $i++) { 
                    $nomor = $nomor."A";
                }
            }
        }

        return $nomor;
    }

    public function handleWebhook(Request $request,$key){
        // Process webhook payload
        // Perform actions based on the webhook data
        try {
            if($key =="zRdjjhBKq8g4Xn1Xojp2oOggh8Ar4jpr"){
                $name = $request->name;
                $message = $request->message;
                $email = $request->email;
                $kebutuhan = $request->field_755c2e6; // securityservice,cleaning_service,labour_supply,shelter_App,pelatihan,lowongan_kerja,Logistic
                $kota = $request->field_41ddce4;
                $jabatan = $request->field_c71451e;
                $namaPerusahaan = $request->field_1f9a4aa;
                $pesan = $request->field_d6eada5;
                $recaptcha = $request->field_33bab84;
                $wilayah = $request->field_41ddce4;
                
                DB::table('wh_shelterapp')->insert([
                    'name' => $name,
                    'no_telepon' => $message,
                    'email' => $email,
                    'kebutuhan' => $kebutuhan,
                    'kota' => $kota,
                    'jabatan' => $jabatan,
                    'nama_perusahaan' => $namaPerusahaan,
                    'pesan' => $pesan,
                    'message' => $message,
                    'wilayah' => $wilayah,
                    'recaptcha' => $recaptcha
                ]);
                
                $current_date_time = Carbon::now()->toDateTimeString();
                $nomor = $this->generateNomor();
                $kebutuhanId = null;
                if($kebutuhan=='securityservice'){
                    $kebutuhanId = 1;
                }else if($kebutuhan=='cleaning_service'){
                    $kebutuhanId = 3;
                }else if($kebutuhan=='labour_supply'){
                    $kebutuhanId = 2;
                }else if($kebutuhan=='Logistic'){
                    $kebutuhanId = 4;
                }else{
                    $kebutuhanId = 99;
                };

                $branch = null;
                $province = DB::connection('mysqlhris')->table('m_province')->where('name','like',$wilayah)->first();
                if($province !=null){
                    $branch = $province->branch_id;
                };
                
                DB::table('sl_leads')->insert([
                    'nomor' =>  $nomor,
                    'tgl_leads' => $current_date_time,
                    'nama_perusahaan' => $namaPerusahaan,
                    'branch_id' => $branch,
                    'platform_id' => 4,
                    'kebutuhan_id' =>  $kebutuhanId,
                    'pic' =>  $name,
                    'jabatan' =>  $jabatan,
                    'no_telp' => $message,
                    'email' => $email,
                    'status_leads_id' => 1,
                    'notes' => $pesan,
                    'created_at' => $current_date_time,
                    'created_by' => 'webhook'
                ]);    
                
                // $arrService = array("securityservice", "cleaning_service", "labour_supply","Logistic");
                // if(in_array($kebutuhan, $arrService)){
                    
                // };
                
                return response()->json(['success' => true]);
            }else{
                return response()->json(['success' => false]);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }
    }

}

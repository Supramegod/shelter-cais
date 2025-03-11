<?php

namespace App\Http\Controllers\Fitur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \PDF;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\IpUtils;
use Carbon\Carbon;
use App\Http\Controllers\Sales\CustomerActivityController;


class SdtTrainingInviteController extends Controller
{
    public function testPdf(Request $request)
    {
        // $pdf = PDF::loadHTML('<h1>Test</h1>');
        // $pdf = PDF::make('dompdf.wrapper');
        // $pdf = PDF::loadHTML('<h1>Test</h1>');
        // $pdf = PDF::loadView('cetaksantri', ['santri' => $santri]);

        $trainer = DB::table('sdt_training_trainer as stt')
            ->leftJoin('m_training_trainer as mtt','mtt.id','=', 'stt.id_trainer')
            ->leftJoin('m_training_divisi as mtd','mtd.id','=', 'mtt.divisi_id')
            ->select(
                "stt.id_pel_trainer as id",
                "mtt.trainer as nama", 
                "mtd.divisi")
            ->where('stt.is_active', 1)
            ->where('stt.id_training', $request->training_id)
            ->get();

        $client = DB::table('sdt_training_client as stc')
            ->leftJoin('m_training_client as mtc','mtc.id','=', 'stc.id_client')
            ->leftJoin('m_training_area as mta','mta.id','=', 'mtc.area_id')
            ->leftJoin('m_training_laman  as mtl','mtl.id','=', 'mta.laman_id')
            ->select("stc.id_client", "mtc.client", "mta.area", "mtl.laman", "mtc.kab_kota")
            ->where('stc.is_active', 1)
            ->where('stc.id_training', $request->training_id)
            ->get();

        $peserta = DB::table('sdt_training_client_detail as stcd')
            ->leftJoin('m_training_client as mtc','mtc.id','=', 'stcd.client_id')
            ->where('stcd.is_active', 1)
            ->where('stcd.training_id', $request->training_id)
            ->get();

            $data = DB::table('sdt_training as tr')
            ->leftjoin('m_training_materi as mtm','mtm.id', '=', 'tr.id_materi')
            ->leftJoin('sdt_training_client as stc','stc.id_training', '=', DB::raw('tr.id_training AND stc.is_active = 1'))
            ->leftJoin('m_training_client as mtc', 'mtc.id' ,'=', 'stc.id_client')
            ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('tr.id_training AND stt.is_active = 1'))
            ->leftJoin('m_training_trainer as mtt','mtt.id', '=', 'stt.id_trainer')
            ->where('tr.is_aktif', 1)
            ->where('tr.id_training', $request->training_id)
            ->select("tr.id_training as id", "mtm.materi", "tr.keterangan", "tr.waktu_mulai", "tr.waktu_selesai", DB::raw("group_concat(distinct mtc.client separator ' , ') AS client"), 
            DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"),
            DB::raw("IF(tr.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') as tipe"),
            DB::raw("IF(tr.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') AS tempat"))
            ->groupBy('tr.id_training')
            ->first();

        $listImage = DB::table('sdt_training_file')->where('is_active', 1)->where('type', 'image')->where('training_id', $request->training_id)->orderBy('id', 'ASC')->get();
        $pdf = PDF::loadView('sdt.training.report', ['trainer' => $trainer, 'client' => $client, 'peserta' => $peserta, 'data' => $data, 'listImage' => $listImage]);
        $pdf->set_option('isRemoteEnabled', true);
        return $pdf->stream();
    }

    public function testPdfWeb(Request $request)
    {
        $trainer = DB::table('sdt_training_trainer as stt')
            ->leftJoin('m_training_trainer as mtt','mtt.id','=', 'stt.id_trainer')
            ->leftJoin('m_training_divisi as mtd','mtd.id','=', 'mtt.divisi_id')
            ->select(
                "stt.id_pel_trainer as id",
                "mtt.trainer as nama", 
                "mtd.divisi")
            ->where('stt.is_active', 1)
            ->where('stt.id_training', $request->training_id)
            ->get();
            
            $client = DB::table('sdt_training_client as stc')
            ->leftJoin('m_training_client as mtc','mtc.id','=', 'stc.id_client')
            ->leftJoin('m_training_area as mta','mta.id','=', 'mtc.area_id')
            ->leftJoin('m_training_laman  as mtl','mtl.id','=', 'mta.laman_id')
            ->select("stc.id_client", "mtc.client", "mta.area", "mtl.laman", "mtc.kab_kota")
            ->where('stc.is_active', 1)
            ->where('stc.id_training', $request->training_id)
            ->get();

        $peserta = DB::table('sdt_training_client_detail as stcd')
            ->leftJoin('m_training_client as mtc','mtc.id','=', 'stcd.client_id')
            ->where('stcd.is_active', 1)
            ->where('stcd.training_id', $request->training_id)
            ->get();

        $data = DB::table('sdt_training')
                ->where('is_aktif', 1)
                ->where('id_training', $request->training_id)
                ->first();

        return view('sdt.training.report',compact('trainer', 'client', 'peserta', 'data'));
    }

    public function invite(Request $request)
    {
        $data = DB::table('sdt_training as tr')
                ->leftjoin('m_training_materi as mtm','mtm.id', '=', 'tr.id_materi')
                ->leftJoin('sdt_training_client as stc','stc.id_training', '=', DB::raw('tr.id_training AND stc.is_active = 1'))
                ->leftJoin('m_training_client as mtc', 'mtc.id' ,'=', 'stc.id_client')
                ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('tr.id_training AND stt.is_active = 1'))
                ->leftJoin('m_training_trainer as mtt','mtt.id', '=', 'stt.id_trainer')
                ->where('tr.is_aktif', 1)
                ->where('tr.id_training', $request->id)
                ->select("tr.id_training as id", "mtm.materi", "tr.keterangan", "tr.waktu_mulai", "tr.waktu_selesai", DB::raw("group_concat(distinct mtc.client separator ' , ') AS client"), 
                DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"),
                DB::raw("IF(tr.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') as tipe"),
                DB::raw("IF(tr.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') AS tempat"))
                ->groupBy('tr.id_training')
                ->first();
        
        $client = DB::table('sdt_training_client as tr')
                        ->leftJoin('m_training_client as mtc', 'mtc.id' ,'=', 'tr.id_client')
                        ->select("mtc.id", "mtc.client")
                        ->where('tr.id_training', $request->id)
                        ->get();

        return view('home.sdt-training-invite',compact('data', 'client'));
    }

    public function dataNik(Request $request){
        try {            
            $data = DB::connection('mysqlhris')->table('m_employee as me')
                ->leftjoin('m_site as ms','ms.id', '=', 'me.site_id')
                ->leftJoin('m_position as mp','mp.id', '=', 'me.position_id')
                ->where('me.status_approval', 3)
                ->where('me.full_name', '!=', '')
                // ->where('and me.full_name', '!= "" ')
                ->select("me.id", "me.full_name", "me.id_card", "me.site_id", "ms.name as site_name", "me.position_id", "mp.name as position_name", "me.phone_number", DB::raw("IF(me.site_id = 2, 'indirect', IF(me.site_id = 0, 'lain', 'direct')) as status"))
                ->first();
            return response()->json([
                    'success' => true,
                    'message' => 'Data Ditemukan',
                    'data' => $data,
                ]);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function pesertaSave(Request $request){
        try {
            // dd($request->status);
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
            // dd($response);

            $result = json_decode($response);
            // dd($result);

            if ($response->successful() && $result->success == true) {
                DB::beginTransaction();
                $current_date_time = Carbon::now()->toDateTimeString();
                

                $trainerId = DB::table('sdt_training_client_detail')->insertGetId([
                    'training_id' => $request->training_id,
                    'employee_id' => $request->employee_id,
                    'client_id' => $request->client_id,
                    'nik' => $request->nik,
                    'nama' => $request->nama,
                    'no_whatsapp' => $request->no_wa,
                    'status_whatsapp' => '-',
                    'status_hadir' => '',
                    'status_employee' => $request->status,
                    'position_id' => $request->jabatan_id,
                    'position' => $request->jabatan,
                    'is_active' => 1,
                    'created_at' => $current_date_time
                ]);
                
                DB::commit();
                return redirect()->back()->with('success', "Terima kasih telah mengisi absensi.");
            } else {
                return redirect()->back()->with('error', 'Selesaikan Recaptcha lagi untuk melanjutkan');
            }
        } catch (\Exception $e) {
            abort(500);
        }
        return null;
    }

    // public function apiContactSave(Request $request){
    //     try {
    //         DB::beginTransaction();
    //         $current_date_time = Carbon::now()->toDateTimeString();
    //         $nomor = $this->generateNomor();

    //         $qplatform = DB::table('m_platform')->where('nama',$request->platform)->whereNull('deleted_at')->first();
    //         $platform = null;
    //         if($qplatform!=null){
    //             $platform = $qplatform->id;
    //         }

    //         $newId = DB::table('sl_leads')->insertGetId([
    //             'nomor' =>  $nomor,
    //             'tgl_leads' => $current_date_time,
    //             'nama_perusahaan' => $request->nama_perusahaan,
    //             'branch_id' => $request->branch,
    //             'platform_id' => $platform,
    //             'kebutuhan_id' =>  $request->kebutuhan,
    //             'pic' =>  $request->nama,
    //             'jabatan' =>  $request->jabatan,
    //             'no_telp' => $request->no_telepon,
    //             'email' => $request->email,
    //             'status_leads_id' => 1,
    //             'notes' => $request->pesan,
    //             'created_at' => $current_date_time,
    //             'created_by' => 'api'
    //         ]);

    //         $customerActivityController = new CustomerActivityController();
    //         $nomorActivity = $customerActivityController->generateNomor($newId);

    //         $activityId = DB::table('sl_customer_activity')->insertGetId([
    //             'leads_id' => $newId,
    //             'branch_id' => $request->branch,
    //             'tgl_activity' => $current_date_time,
    //             'nomor' => $nomorActivity,
    //             'notes' => 'Leads Terbentuk',
    //             'tipe' => 'Leads',
    //             'status_leads_id' => 1,
    //             'is_activity' => 0,
    //             'user_id' => Auth::user()->id,
    //             'created_at' => $current_date_time,
    //             'created_by' => Auth::user()->full_name
    //         ]);
            
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'msg' => 'Berhasil mengirim data',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'msg' => 'Gagal mengirim data , silahkan cek kembali inputan anda',
    //         ]);
    //         abort(500);
    //     }
    // }

    // public function generateNomor (){
    //     //generate nomor 065 = A , 090 = Z , 048 = 1 , 057 = 9;

    //     //dapatkan dulu last leads 
    //     $nomor = "AAAAA";

    //     $lastLeads = DB::table('sl_leads')->orderBy('id', 'DESC')->first();
    //     if($lastLeads!=null){
    //         $nomor = $lastLeads->nomor;
    //         $chars = str_split($nomor);
    //         for ($i=count($chars)-1; $i >= 0; $i--) { 
    //             //dapatkan ascii dari character
    //             $ascii = ord($chars[$i]);

    //             if(($ascii >= 48 && $ascii < 57) || ($ascii >= 65 && $ascii < 90)){
    //                 $ascii += 1;
    //             }else if ($ascii == 90 ) {
    //                 $ascii = 48;
    //             }else{
    //                 continue;
    //             }

    //             $ascchar = chr($ascii);
    //             $nomor = substr_replace($nomor,$ascchar,$i);
    //             break;
    //         }
    //         if(strlen($nomor)<5){
    //             $jumlah = 5-strlen($nomor);
    //             for ($i=0; $i < $jumlah; $i++) { 
    //                 $nomor = $nomor."A";
    //             }
    //         }
    //     }

    //     return $nomor;
    // }

    // public function handleWebhook(Request $request,$key){
    //     // Process webhook payload
    //     // Perform actions based on the webhook data
    //     try {
    //         if($key =="zRdjjhBKq8g4Xn1Xojp2oOggh8Ar4jpr"){
    //             // Ambil 'fields' dari data JSON
    //             $name = $request->input('name');
    //             $message = $request->input('message');
    //             $email = $request->input('email');
    //             $kebutuhan = $request->input('field_755c2e6');
    //             $kota = $request->input('field_41ddce4');
    //             $jabatan = $request->input('field_c71451e');
    //             $namaPerusahaan = $request->input('field_1f9a4aa');
    //             $pesan = $request->input('field_d6eada5');
    //             $recaptcha = $request->input('field_33bab84');
    //             $wilayah = $request->input('field_41ddce4');

    //             DB::table('wh_shelterapp')->insert([
    //                 'name' => $name,
    //                 'no_telepon' => $message,
    //                 'email' => $email,
    //                 'kebutuhan' => $kebutuhan,
    //                 'kota' => $kota,
    //                 'jabatan' => $jabatan,
    //                 'nama_perusahaan' => $namaPerusahaan,
    //                 'pesan' => $pesan,
    //                 'message' => $message,
    //                 'wilayah' => $wilayah,
    //                 'recaptcha' => $recaptcha
    //             ]);
                
    //             $current_date_time = Carbon::now()->toDateTimeString();
    //             $nomor = $this->generateNomor();
    //             $kebutuhanId = null;
    //             if($kebutuhan=='securityservice'){
    //                 $kebutuhanId = 1;
    //             }else if($kebutuhan=='cleaning_service'){
    //                 $kebutuhanId = 3;
    //             }else if($kebutuhan=='labour_supply'){
    //                 $kebutuhanId = 2;
    //             }else if($kebutuhan=='Logistic'){
    //                 $kebutuhanId = 4;
    //             }else{
    //                 $kebutuhanId = 99;
    //             };

    //             $branch = null;
    //             $province = DB::connection('mysqlhris')->table('m_province')->where('name','like',$wilayah)->first();
    //             if($province !=null){
    //                 $branch = $province->branch_id;
    //             };
                
    //             $newId = DB::table('sl_leads')->insertGetId([
    //                 'nomor' =>  $nomor,
    //                 'tgl_leads' => $current_date_time,
    //                 'nama_perusahaan' => $namaPerusahaan,
    //                 'branch_id' => $branch,
    //                 'platform_id' => 4,
    //                 'kebutuhan_id' =>  $kebutuhanId,
    //                 'pic' =>  $name,
    //                 'jabatan' =>  $jabatan,
    //                 'no_telp' => $message,
    //                 'email' => $email,
    //                 'status_leads_id' => 1,
    //                 'notes' => $pesan,
    //                 'created_at' => $current_date_time,
    //                 'created_by' => 'webhook'
    //             ]);    
                
    //             //insert ke activity sebagai activity pertama
    //             $customerActivityController = new CustomerActivityController();
    //             $nomorActivity = $customerActivityController->generateNomor($newId);

    //             $activityId = DB::table('sl_customer_activity')->insertGetId([
    //                 'leads_id' => $newId,
    //                 'branch_id' => $branch,
    //                 'tgl_activity' => $current_date_time,
    //                 'nomor' => $nomorActivity,
    //                 'notes' => 'Leads baru dari web',
    //                 'tipe' => 'Leads',
    //                 'status_leads_id' => 1,
    //                 'is_activity' => 0,
    //                 'user_id' => Auth::user()->id,
    //                 'created_at' => $current_date_time,
    //                 'created_by' => Auth::user()->full_name
    //             ]);

    //             return response()->json(['success' => true]);
    //         }else{
    //             return response()->json(['success' => false]);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json(['success' => false]);
    //     }
    // }

}

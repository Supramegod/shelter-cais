<?php

namespace App\Http\Controllers\Sdt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use \stdClass;
use App\Exports\LeadsTemplateExport;
use App\Exports\LeadsExport;
use App\Http\Controllers\Sales\CustomerActivityController;
use Illuminate\Support\Facades\Storage;


class SdtTrainingController extends Controller
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
        

        $branch = DB::connection('mysqlhris')->table('m_branch')->where('id','!=',1)->where('is_active',1)->get();
        $status = DB::table('m_status_leads')->whereNull('deleted_at')->get();
        $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

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
        return view('sdt.training.list',compact('branch','platform','status','tglDari','tglSampai','request','error','success'));
    }
    
    public function indexTerhapus (Request $request){
        return view('sales.leads.list-terhapus');
    }

    public function add (Request $request){
        try {
            // $now = Carbon::now()->isoFormat('DD MMMM Y');
            $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();
            $listClient = DB::table('m_training_client')->where('is_aktif', 1)->orderBy('client', 'ASC')->get();
            $listMateri = DB::table('m_training_materi')->where('is_aktif', 1)->orderBy('materi', 'ASC')->get();
            $listTrainer = DB::table('m_training_trainer')->where('is_aktif', 1)->orderBy('trainer', 'ASC')->get();

            return view('sdt.training.add',compact('listBu','listClient','listMateri', 'listTrainer'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function view(Request $request, $id){
        try {
            $data = DB::table('sdt_training')
                    ->where('is_aktif', 1)
                    ->where('id_training', $id)
                    ->first();

            $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();
            $listMateri = DB::table('m_training_materi')->where('is_aktif', 1)->orderBy('materi', 'ASC')->get();

            $listImage = DB::table('sdt_training_file')->where('is_active', 1)->where('type', 'image')->where('training_id', $id)->orderBy('id', 'ASC')->get();
            $listClient = DB::table('m_training_client')->where('is_aktif', 1)->orderBy('client', 'ASC')->get();
            $listTrainer = DB::table('m_training_trainer')->where('is_aktif', 1)->orderBy('trainer', 'ASC')->get();
            $namaPerusahaan = DB::table('sdt_training_client as tr')
                        ->leftJoin('m_training_client as mtc', 'mtc.id' ,'=', 'tr.id_client')
                        ->select("mtc.id", "mtc.client")
                        ->where('tr.id_training', $id)
                        ->get();

            //LIST PSERTA TRAININH, HRIS
            $listPeserta = DB::connection('mysqlhris')
                ->table('m_employee as empl')
                ->leftJoin('m_position as position', 'position.id' ,'=', 'empl.position_id')
                ->select("empl.id", "empl.id_card", "empl.full_name", "empl.phone_number", "position.name as position")
                ->where('empl.status_approval', '=' , 3)
                ->where('empl.is_active', '=', 1)
                ->where('position.description', '!=', 'Security')
                ->orderBy('empl.full_name','asc')
                ->get();
            
                // dd($peserta);

            return view('sdt.training.view', compact('listClient', 'listTrainer','namaPerusahaan', 'data', 'listPeserta', 'listBu', 'listMateri', 'listImage'));
            // return view('sdt.training.view',compact('activity','data','branch','jabatanPic','namaPerusahaan','kebutuhan','platform'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
        
    }

    // public function list(Request $request){
    //     try {
    //         $data = DB::table('m_training_area')->whereNull('deleted_at')->get();
    //         return DataTables::of($data)
    //             ->addColumn('aksi', function ($data) {
    //                 return '<div class="justify-content-center d-flex">
    //                     <a href="'.route('training-area.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
    //                     <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
    //                 </div>';
    //             })
    //             ->rawColumns(['aksi'])
    //         ->make(true);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    public function list (Request $request){
        try {
            $data = DB::table('sdt_training as tr')
                        ->leftjoin('m_training_materi as mtm','mtm.id', '=', 'tr.id_materi')
                        ->leftJoin('sdt_training_client as stc','stc.id_training', '=', DB::raw('tr.id_training AND stc.is_active = 1'))
                        ->leftJoin('m_training_client as mtc', 'mtc.id' ,'=', 'stc.id_client')
                        ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('tr.id_training AND stt.is_active = 1'))
                        ->leftJoin('m_training_trainer as mtt','mtt.id', '=', 'stt.id_trainer')
                        ->leftJoin('sdt_training_client_detail as stcd', 'stcd.training_id', '=', DB::raw('tr.id_training AND stcd.is_active = 1'))
                        
                        ->select(
                            "tr.id_training as id",
                            "mtm.materi", 
                            "tr.waktu_mulai", 
                            "tr.waktu_selesai", 
                            DB::raw("IF(tr.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') as tipe"),
                            DB::raw("IF(tr.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') AS tempat"),
                            DB::raw("group_concat(distinct mtc.client separator ', ') AS client"),
                            DB::raw("count(mtc.client) AS total_client"),
                            // DB::raw("sum(stc.peserta_hadir) AS total_peserta"),
                            DB::raw("count(distinct stcd.id) AS total_peserta"),
                            DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"), 
                            DB::raw("count(distinct mtt.id) AS total_trainer"))
                        ->where('tr.is_aktif', 1)
                        ->groupBy('tr.id_training');
            
            $data = $data->get();          

            // foreach ($data as $key => $value) {
            //     $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
            // }

            // $dd($data);
            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <a href="'.route('sdt-training.view',$data->id).'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
                    <div class="btn-delete btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function clientpeserta (Request $request){
        try {
            // dd($request);
            $data = DB::table('sdt_training_client_detail')
            ->where('training_id', $request->training_id)
            ->where('client_id', $request->client_id)
            ->where('is_active', 1)
            ->get();          
            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <div class="btn-delete-peserta btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                </div>';
            })
            
            ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function dataGaleri (Request $request){
        try {
            $data = DB::table('sdt_training_file')
            ->where('training_id', $request->training_id)
            ->where('is_active', 1)
            ->get();          

            // dd($data);
            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <div class="btn-delete-gallery btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                </div>';
            })
            ->editColumn('path', function ($data) {
                return '<img src="'.$data->path.'" alt="" border=3 height=100 width=150></img>';
            })
            ->rawColumns(['aksi', 'path'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function dataTrainer (Request $request){
        try {
            $data = DB::table('sdt_training_trainer as stt')
            ->leftJoin('m_training_trainer as mtt','mtt.id','=', 'stt.id_trainer')
            ->leftJoin('m_training_divisi as mtd','mtd.id','=', 'mtt.divisi_id')
            ->select(
                "stt.id_pel_trainer as id",
                "mtt.trainer as nama", 
                "mtd.divisi")
            ->where('stt.is_active', 1)
            ->where('stt.id_training', $request->training_id)
            ->get();          

            // dd($data);
            return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<div class="justify-content-center d-flex">
                    <div class="btn-delete-trainer btn btn-danger waves-effect btn-xs" data-id="'.$data->id.'"><i class="mdi mdi-trash-can"></i>&nbsp;Delete</div>
                </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    // public function listTerhapus (Request $request){
    //     try {
    //         $db2 = DB::connection('mysqlhris')->getDatabaseName();
    //         $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();

    //         $data = DB::table('sl_leads')
    //                     ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
    //                     ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
    //                     ->leftJoin('m_platform','sl_leads.platform_id','=','m_platform.id')
    //                     ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
    //                     ->select('m_tim_sales_d.nama as sales','sl_leads.*', 'm_status_leads.nama as status', $db2.'.m_branch.name as branch', 'm_platform.nama as platform','m_status_leads.warna_background','m_status_leads.warna_font')
    //                     ->whereNotNull('sl_leads.deleted_at')
    //                     ->whereNull('sl_leads.customer_id');
            
    //         $data = $data->get();          

    //         foreach ($data as $key => $value) {
    //             $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
    //         }

    //         return DataTables::of($data)
    //         ->make(true);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    public function uploadImage(Request $request)
    {
        $file = $request->file('image');
        $id = $request->id;
        $extension = $file->getClientOriginalExtension();

        $filename = $file->getClientOriginalName();
        $filename = str_replace(".".$extension,"",$filename);
        $originalName = $id.$filename.".".$extension."";
        $current_date_time = Carbon::now()->toDateTimeString();

        // dd($originalName);
        Storage::disk('sdt-training-image')->put($originalName, file_get_contents($file));

        $link = env('APP_URL').'/public/uploads/sdt-training/image/'.$originalName;

        DB::table('sdt_training_file')->insert([
            'training_id' => $id,
            'type' => 'image',
            'path' => $link,
            'file_name' => $originalName,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'created_at' => $current_date_time,
            'is_active' => 1
        ]);

        return redirect()->back()->with('success', "Berhasil menambahkan gallery");
    }

    public function save(Request $request) {
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            $msgSave = '';
            
            if(!empty($request->id)){
                DB::table('sdt_training')->where('id_training',$request->id)->update([
                    'keterangan' => $request->keterangan,
                    'waktu_mulai' => $request->start_date,
                    'waktu_selesai' => $request->end_date,
                    'id_pel_tipe' => $request->tipe_id,
                    'id_pel_tempat' => $request->tempat_id,
                    'id_materi' => $request->materi_id,
                    'id_laman' => $request->laman_id,
                    'updated_at' => $current_date_time
                ]);
                $msgSave = 'Training berhasil diubah.';
            }else{
                
                // $nomor = $this->generateNomor();
                $trainingId = DB::table('sdt_training')->insertGetId([
                    'keterangan' => $request->keterangan,
                    'waktu_mulai' => $request->start_date,
                    'waktu_selesai' => $request->end_date,
                    'id_pel_tipe' => $request->tipe_id,
                    'id_pel_tempat' => $request->tempat_id,
                    'id_materi' => $request->materi_id,
                    'id_laman' => $request->laman_id,
                    'id_user' => Auth::user()->id,
                    'created_at' => $current_date_time
                ]);
                
                foreach ($request->client_id as $x) {
                    $trainingClient = DB::table('sdt_training_client')->insertGetId([
                        'id_client' => (int) $x,
                        // 'peserta_hadir' => $request->peserta,
                        'id_training' => $trainingId
                    ]);    
                    // dd($trainingClient);
                }
                
                foreach ($request->trainer_id as $x) {
                    $trainingTrainer = DB::table('sdt_training_trainer')->insertGetId([
                        'id_trainer' => (int) $x,
                        'id_training' => $trainingId
                    ]);
                }
                
                $msgSave = 'Training berhasil disimpan ';
            }
            // }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addClient(Request $request) {
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            
            $employeeExist = DB::table('sdt_training_client as client')
                ->leftJoin('m_training_client as m_client', 'client.id_client', '=', 'm_client.id')
                ->select("client.id_pel_client", "m_client.client")
                ->where('client.id_training', '=' , $request->id)
                ->where('client.id_client', '=' , $request->client_id)
                ->where('client.is_active', '=' , 1)
                ->first();

            // dd($employeeExist);
            if(!empty($employeeExist->id_pel_client)){
                return response()->json([
                    'success'   => false,
                    'data'      => [],
                    'message'   => "Data client " . $employeeExist->client . " sudah ada"
                ], 200);
            }

            $clientId = DB::table('sdt_training_client')->insertGetId([
                'id_training' => $request->id,
                'id_client' => $request->client_id,
                'is_active' => 1
            ]);
            
            
            $msgSave = 'Client berhasil disimpan ';
            DB::commit();
            // return redirect()->back()->with('success', $msgSave);
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menambahkan client"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addPeserta(Request $request) {
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            
            $employee = DB::connection('mysqlhris')
                ->table('m_employee as empl')
                ->leftJoin('m_position as position', 'position.id' ,'=', 'empl.position_id')
                ->select("empl.id", "empl.id_card", "empl.full_name", "empl.phone_number", "position.name as position")
                ->where('empl.id', '=' , $request->employee_id)
                ->first();

            // dd($employee->id_card);
            $trainerId = DB::table('sdt_training_client_detail')->insertGetId([
                'training_id' => $request->id,
                'employee_id' => $request->employee_id,
                'client_id' => $request->client_id,
                'nik' => $employee->id_card,
                'nama' => $employee->full_name,
                'no_whatsapp' => $employee->phone_number,
                'status_whatsapp' => 'Belum Kirim',
                'status_hadir' => '',
                'position' => $employee->position,
                'is_active' => 1,
                'created_at' => $current_date_time
            ]);
            // dd($request);
            
            $msgSave = 'Peserta berhasil disimpan ';
            DB::commit();
            // return redirect()->back()->with('success', $msgSave);
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menambahkan peserta"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function addTrainer(Request $request) {
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            
            // $nomor = $this->generateNomor();
            $trainerId = DB::table('sdt_training_trainer')->insertGetId([
                'id_trainer' => $request->trainer_id,
                'id_training' => $request->id,
                'is_active' => 1,
                'created_at' => $current_date_time
            ]);
            // dd($request);
            
            $msgSave = 'Trainer berhasil disimpan ';
            DB::commit();
            // return redirect()->back()->with('success', $msgSave);
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menambahkan trainer"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function delete(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sdt_training')->where('id_training',$request->id)->update([
                'updated_at' => $current_date_time,
                'is_aktif' => 0 
            ]);

            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function deleteGallery(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sdt_training_file')
                    ->where('id', $request->id)
                    ->first();

            DB::table('sdt_training_file')->where('id', $request->id)->update([
                'is_active' => 0 
            ]);

            Storage::disk('sdt-training-image')->delete($data->file_name);

            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return response()->json([
                'success'   => false,
                'data'      => [],
                'message'   => "Gagal menghapus data"
            ], 200);
        }
    }

    public function deleteTrainer(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sdt_training_trainer')->where('id_pel_trainer', $request->id)->update([
                'is_active' => 0 
            ]);

            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function deletePeserta(Request $request){
        try {
            DB::table('sdt_training_client_detail')->where('id', $request->id)->update([
                'is_active' => 0 
            ]);

            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil menghapus data"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function sendMessage(Request $request){
        try {
            // $dataPeserta = DB::table('sdt_training_client_detail')
            //     ->where('status_whatsapp', 'Belum Kirim')
            //     ->where('is_active', 1)
            //     ->where('no_whatsapp', '!=', '')
            //     ->where('training_id', $request->id)
            //     ->get();
                
            $myarray = explode(',', $request->no_wa);
            $current_date_time = Carbon::now()->toDateTimeString();
            foreach ($myarray as $key => $value) {
                
                // DB::table('sdt_training_client_detail')->where('id', $value->id)
                // ->update([
                //     'status_whatsapp' => 'Waiting'
                // ]);
                
                DB::table('whatsapp_message')->insert([
                    'nomor_wa' => $value,
                    'message' => 'test',
                    'type' => '',
                    'status' => 'Waiting',
                    'created_date' => $current_date_time,
                    'support_id' => $request->id,
                    'is_active' => 1
                ]);

                // dd($value);
            }   
            
            // dd($myarray);
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil mengirim undangan"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            return response()->json([
                'success'   => false,
                'data'      => [],
                'message'   => "Gagal mengirim undangan"
            ], 200);
        }
    }

    // public function delete (Request $request){
    //     try {
    //         DB::beginTransaction();

    //         $current_date_time = Carbon::now()->toDateTimeString();
    //         DB::table('sdt_training')->where('id_training',$request->id)->update([
    //             'updated_at' => $current_date_time,
    //             'is_aktif' => 0 
    //         ]);

    //         $msgSave = 'Training berhasil dihapus.';
            
    //         DB::commit();
    //         return redirect()->route('sdt-training')->with('success', $msgSave);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

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

    public function generateNomorLanjutan ($nomor){
        //generate nomor 065 = A , 090 = Z , 048 = 1 , 057 = 9;

        //dapatkan dulu last leads 
        // $nomor = "AAAAA";

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

        return $nomor;
    }

    // public function import (Request $request){
    //     $now = Carbon::now()->isoFormat('DD MMMM Y');

    //     return view('sales.leads.import',compact('now'));
    // }

    // public function inquiryImport(Request $request){
    //     try {
    //         DB::beginTransaction();

    //         $validator = Validator::make($request->all(), [
    //             'file' => 'required|mimes:csv,xls,xlsx',
    //         ], [
    //             'min' => 'Masukkan :attribute minimal :min',
    //             'max' => 'Masukkan :attribute maksimal :max',
    //             'required' => ':attribute harus di isi',
    //             'mimes' => 'tipe file harus csv,xls atau xlsx',
    //         ]);
    
    //         $array = null;
    //         $datas = [];
    //         if ($validator->fails()) {
    //             return back()->withErrors($validator->errors())->withInput();
    //         }else{
    //             $file = $request->file('file');
    //             $current_date_time = Carbon::now()->toDateTimeString();

    //             // Get the csv rows as an array
    //             $array = Excel::toArray(new stdClass(), $file);
    //             $jumlahError = 0;
    //             $jumlahWarning = 0;
    //             $jumlahSuccess = 0;
    //             $data = [];
    //             foreach ($array as $key => $v) {
    //                 foreach ($v as $keyd => $value) {
    //                     if($keyd==0){
    //                         continue;
    //                     };
    //                     if($value[0]==null&&$value[1]==null&&$value[2]==null&&$value[3]==null&&$value[4]==null&&$value[5]==null&&$value[6]==null&&$value[7]==null&&$value[8]==null&&$value[9]==null&&$value[10]==null&&$value[11]==null&&$value[12]==null){
    //                         continue;
    //                     }

    //                     $value[15] = "";
    //                     $value[16] = 1; // status : 1 success,2 warning , 3 error
    //                     //convert date
    //                     $UNIX_DATE = Carbon::now()->toDateString();
    //                     try {
    //                         $UNIX_DATE = ($value[1] - 25569) * 86400;
    //                         $UNIX_DATE = gmdate("Y-m-d", $UNIX_DATE);
    //                     } catch (\Throwable $th) {
    //                     }
    //                     $value[1] = $UNIX_DATE;

    //                     //Cek Data Master
    //                     $lbranch = DB::connection('mysqlhris')->table('m_branch')->where('name',$value[10])->first();
    //                     $lplatform = DB::table('m_platform')->where('nama',$value[11])->first();
    //                     $lkebutuhan = DB::table('m_kebutuhan')->where('nama',$value[9])->first();
    //                     $lJenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->where('nama',$value[3])->first();
    //                     $ltimSalesD = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('username',$value[14])->first();

    //                     if($lbranch==null){
    //                         $value[10] ="";
    //                         if($value[15]!=""){
    //                             $value[15] .= " , ";
    //                             $value[16] = 2;
    //                         }
    //                         $value[15] .= "Wilayah Tidak ditemukan";
    //                     }

    //                     if($lplatform==null){
    //                         $value[11] ="";
    //                         if($value[15]!=""){
    //                             $value[15] .= " , ";
    //                             $value[16] = 2;
    //                         }
    //                         $value[15] .= "Sumber Leads Tidak ditemukan";
    //                     }

    //                     if($lkebutuhan==null){
    //                         $value[9] ="";
    //                         if($value[15]!=""){
    //                             $value[15] .= " , ";
    //                             $value[16] = 2;
    //                         }
    //                         $value[15] .= "Kebutuhan Tidak ditemukan";
    //                     }

    //                     if ($value[3]!=null && $value[3]!="" && $value[3]!="-") {
    //                         if($lJenisPerusahaan==null){
    //                             DB::table('m_jenis_perusahaan')->insert([
    //                                 'nama' => $value[3],
    //                                 'resiko' => "",
    //                                 'created_at' => $current_date_time,
    //                                 'created_by' => Auth::user()->full_name
    //                             ]);
    //                         }else{
    //                             if($value[15]!=""){
    //                                 $value[15] .= " , ";
    //                                 $value[16] = 2;
    //                             }
    //                             $value[15] .= "Jenis Perusahaan Tidak ditemukan";
    //                         }
    //                     }

    //                     if($ltimSalesD==null){
    //                         $value[14] ="";
    //                         if($value[15]!=""){
    //                             $value[15] .= " , ";
    //                             $value[16] = 2;
    //                         }
    //                         $value[15] .= "Tim Sales Tidak ditemukan";
    //                     }

    //                     if($value[15]==""){
    //                         $jumlahSuccess++;
    //                     }else{
    //                         $jumlahWarning++;
    //                     }

    //                     array_push($data,$value);
    //                 }
    //             }
    //             array_push($datas,$data);
    //         }
    //         $now = Carbon::now()->isoFormat('DD MMMM Y');
    //         DB::commit();
    //         return view('sales.leads.inquiry',compact('datas','now','jumlahError','jumlahSuccess','jumlahWarning'));
    //     } catch (\Exception $e) {
    //         dd($e);
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    // public function saveImport(Request $request){
    //     DB::beginTransaction();

    //     try {
    //         $current_date_time = Carbon::now()->toDateTimeString();
    //         $datas = $request->value;

    //         $msgSave = '';

    //         $nomor = "";

    //         foreach ($datas as $key => $value) {
    //             if($key==0){
    //                 $nomor = $this->generateNomor();
    //             }else{
    //                 $nomor = $this->generateNomorLanjutan($nomor);
    //             }

    //             $data = explode("||",$value);

    //             $dtgl = $data[1];
    //             $dperusahaan = $data[2];
    //             $djenisPerusahaan = $data[3];
    //             $dnoTelpPerusahaan =$data[4];
    //             $dpic = $data[5];
    //             $djabatanPic = $data[6];
    //             $dnoTelpPic = $data[7];
    //             $demailPic = $data[8];
    //             $dkebutuhan = $data[9];
    //             $dbranch = $data[10];
    //             $dsumberLeads = $data[11];
    //             $dalamat = $data[12];
    //             $dketerangan = $data[13];
    //             $dSales = $data[14];

    //             $lbranch = DB::connection('mysqlhris')->table('m_branch')->where('name',$dbranch)->first();
    //             $lplatform = DB::table('m_platform')->where('nama',$dsumberLeads)->first();
    //             $lkebutuhan = DB::table('m_kebutuhan')->where('nama',$dkebutuhan)->first();
    //             $lJenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->where('nama',$djenisPerusahaan)->first();
    //             $ltimSalesD = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('username',$dSales)->first();

    //             $branch = null;
    //             if($lbranch!=null){
    //                 $branch = $lbranch->id;
    //             }

    //             $platform = null;
    //             if($lplatform!=null){
    //                 $platform = $lplatform->id;
    //             }

    //             $kebutuhan = null;
    //             if($lkebutuhan!=null){
    //                 $kebutuhan = $lkebutuhan->id;
    //             }

    //             $jenisPerusahaan = null;
    //             if($lJenisPerusahaan!=null){
    //                 $jenisPerusahaan = $lJenisPerusahaan->id;
    //             }
                
    //             $timSalesD = null;
    //             $timSales = null;
    //             if($ltimSalesD!=null){
    //                 $timSalesD = $ltimSalesD->id;
    //                 $timSales = $ltimSalesD->tim_sales_id;
    //             }

    //             $newId = DB::table('sl_leads')->insertGetId([
    //                 'nomor' =>  $nomor,
    //                 'tgl_leads' => $dtgl,
    //                 'nama_perusahaan' => $dperusahaan,
    //                 'telp_perusahaan' => $dnoTelpPerusahaan,
    //                 'jenis_perusahaan_id' =>$jenisPerusahaan ,
    //                 'branch_id' => $branch,
    //                 'platform_id' => 99,
    //                 'kebutuhan_id' =>  $kebutuhan,
    //                 'alamat' => $dalamat,
    //                 'notes' => $dketerangan,
    //                 'pic' =>  $dpic,
    //                 'jabatan' =>  $djabatanPic,
    //                 'no_telp' => $dnoTelpPic,
    //                 'email' => $demailPic,
    //                 'status_leads_id' => 1,
    //                 'tim_sales_id' => $timSales,
    //                 'tim_sales_d_id' => $timSalesD,
    //                 'created_at' => $current_date_time,
    //                 'created_by' => Auth::user()->full_name
    //             ]);
    //             //insert ke activity sebagai activity pertama
    //             $customerActivityController = new CustomerActivityController();
    //             $nomorActivity = $customerActivityController->generateNomor($newId);

    //             $activityId = DB::table('sl_customer_activity')->insertGetId([
    //                 'leads_id' => $newId,
    //                 'branch_id' => $branch,
    //                 'tgl_activity' => $current_date_time,
    //                 'nomor' => $nomorActivity,
    //                 'notes' => 'Leads Import',
    //                 'tipe' => 'Leads',
    //                 'status_leads_id' => 1,
    //                 'is_activity' => 0,
    //                 'user_id' => Auth::user()->id,
    //                 'created_at' => $current_date_time,
    //                 'created_by' => Auth::user()->full_name
    //             ]);    
    //         }
            
    //         $msgSave = 'Import Leads berhasil Dilakukan !';

    //         DB::commit();
    //         return redirect()->route('leads')->with('success', $msgSave);
    //     } catch (\Exception $e) {
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    // public function templateImport(Request $request) {
    //     $dt = Carbon::now()->toDateTimeString();

    //     return Excel::download(new LeadsTemplateExport(), 'Template Import Leads-'.$dt.'.xlsx');
    // }

    // public function exportExcel(Request $request){
    //     $dt = Carbon::now()->toDateTimeString();

    //     return Excel::download(new LeadsExport(), 'Leads-'.$dt.'.xlsx');
    // }

    // public function childLeads (Request $request){
    //     return DB::table('sl_leads')
    //     ->whereNull('deleted_at')
    //     ->where(function ($query) use ($request) {
    //         $query->where('leads_id', $request->id)
    //               ->orWhere('id', $request->id);
    //     })
    //     ->OrderBy('id','asc')
    //     ->get();
    // }
    

    // public function availableLeads (Request $request){
    //     try {
    //         $db2 = DB::connection('mysqlhris')->getDatabaseName();
    //         $data = DB::table('sl_leads')
    //                     ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
    //                     ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
    //                     ->leftJoin('m_platform','sl_leads.platform_id','=','m_platform.id')
    //                     ->leftJoin('m_kebutuhan','sl_leads.kebutuhan_id','=','m_kebutuhan.id')
    //                     ->leftJoin('m_tim_sales','sl_leads.tim_sales_id','=','m_tim_sales.id')
    //                     ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
    //                     ->select('sl_leads.email','sl_leads.branch_id','m_tim_sales_d.user_id','sl_leads.ro','sl_leads.crm','m_tim_sales.nama as tim_sales','m_tim_sales_d.nama as sales','sl_leads.tim_sales_id','sl_leads.tim_sales_d_id','sl_leads.status_leads_id','sl_leads.id','sl_leads.tgl_leads','sl_leads.nama_perusahaan','m_kebutuhan.nama as kebutuhan','sl_leads.pic','sl_leads.no_telp','sl_leads.email', 'm_status_leads.nama as status', $db2.'.m_branch.name as branch', 'm_platform.nama as platform','m_status_leads.warna_background','m_status_leads.warna_font')
    //                     ->whereNull('sl_leads.deleted_at');
    //         // dd($data);
    //         //divisi sales
    //         if(in_array(Auth::user()->role_id,[29,30,31,32,33])){
    //             // sales
    //             if(Auth::user()->role_id==29){
    //                 $data = $data->where('m_tim_sales_d.user_id',Auth::user()->id);
    //             }else if(Auth::user()->role_id==30){
    //             }
    //             // spv sales
    //             else if(Auth::user()->role_id==31){
    //                 $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();
    //                 $memberSales = [];
    //                 $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$tim->tim_sales_id)->get();
    //                 foreach ($sales as $key => $value) {
    //                     array_push($memberSales,$value->user_id);
    //                 }
    //                 $data = $data->whereIn('m_tim_sales_d.user_id',$memberSales);
    //             }
    //             // Asisten Manager Sales , Manager Sales
    //             else if(Auth::user()->role_id==32 || Auth::user()->role_id==33){

    //             }
    //         }
    //         //divisi RO
    //         else if(in_array(Auth::user()->role_id,[4,5,6,8])){
    //             if(in_array(Auth::user()->role_id,[4,5])){
    //                 $data = $data->where('sl_leads.ro_id',Auth::user()->id);
    //             }else if(in_array(Auth::user()->role_id,[6,8])){

    //             }
    //         }
    //         //divisi crm
    //         else if(in_array(Auth::user()->role_id,[54,55,56])){
    //             if(in_array(Auth::user()->role_id,[54])){
    //                 $data = $data->where('sl_leads.crm_id',Auth::user()->id);
    //             }else if(in_array(Auth::user()->role_id,[55,56])){

    //             }
    //         };
            
    //         $data = $data->get();
                        

    //         foreach ($data as $key => $value) {
    //             $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
    //             $value->salesEmail = "";
    //             // if($value->user_id != null){
    //             //     $salesUser = DB::connection('mysqlhris')->table('m_user')->where('id',$value->user_id)->first();
    //             //     if($salesUser !=null){
    //             //         $value->salesEmail = $salesUser->email;
    //             //     }
    //             // }

    //             // cari branch manager dari m_branch mysqlhris dimana branch_id = branch_id leads dan role = 52
    //             // $branchManager = DB::connection('mysqlhris')->table('m_user')->where('role_id',52)->where('branch_id',$value->branch_id)->first();
    //             $value->branchManagerEmail = "";
    //             $value->branchManager = "";
    //             // if($branchManager !=null){
    //             //     $value->branchManagerEmail = $branchManager->email;
    //             //     $value->branchManager = $branchManager->full_name;
    //             // }

    //         }
    //         return DataTables::of($data)
    //         ->make(true);
    //     } catch (\Exception $e) {
    //         dd($e);
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    // public function availableQuotation (Request $request){
    //     try {
    //         $db2 = DB::connection('mysqlhris')->getDatabaseName();

    //         $data = DB::table('sl_leads')
    //                     ->join('m_status_leads','sl_leads.status_leads_id','=','m_status_leads.id')
    //                     ->leftJoin($db2.'.m_branch','sl_leads.branch_id','=',$db2.'.m_branch.id')
    //                     ->leftJoin('m_platform','sl_leads.platform_id','=','m_platform.id')
    //                     ->leftJoin('m_kebutuhan','sl_leads.kebutuhan_id','=','m_kebutuhan.id')
    //                     ->leftJoin('m_tim_sales','sl_leads.tim_sales_id','=','m_tim_sales.id')
    //                     ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
    //                     ->select('sl_leads.ro','sl_leads.crm','m_tim_sales.nama as tim_sales','m_tim_sales_d.nama as sales','sl_leads.tim_sales_id','sl_leads.tim_sales_d_id','sl_leads.status_leads_id','sl_leads.id','sl_leads.tgl_leads','sl_leads.nama_perusahaan','m_kebutuhan.nama as kebutuhan','sl_leads.pic','sl_leads.no_telp','sl_leads.email', 'm_status_leads.nama as status', $db2.'.m_branch.name as branch', 'm_platform.nama as platform','m_status_leads.warna_background','m_status_leads.warna_font')
    //                     ->whereNull('sl_leads.deleted_at')
    //                     ->whereNull('sl_leads.leads_id');
            
    //         //divisi sales
    //         if(in_array(Auth::user()->role_id,[29,30,31,32,33])){
    //             // sales
    //             if(Auth::user()->role_id==29){
    //                 $data = $data->where('m_tim_sales_d.user_id',Auth::user()->id);
    //             }else if(Auth::user()->role_id==30){
    //             }
    //             // spv sales
    //             else if(Auth::user()->role_id==31){
    //                 $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();
    //                 $memberSales = [];
    //                 $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$tim->tim_sales_id)->get();
    //                 foreach ($sales as $key => $value) {
    //                     array_push($memberSales,$value->user_id);
    //                 }
    //                 $data = $data->whereIn('m_tim_sales_d.user_id',$memberSales);
    //             }
    //             // Asisten Manager Sales , Manager Sales
    //             else if(Auth::user()->role_id==32 || Auth::user()->role_id==33){

    //             }
    //         }
    //         //divisi RO
    //         else if(in_array(Auth::user()->role_id,[4,5,6,8])){
    //             if(in_array(Auth::user()->role_id,[4,5])){
    //                 $data = $data->where('sl_leads.ro_id',Auth::user()->id);
    //             }else if(in_array(Auth::user()->role_id,[6,8])){

    //             }
    //         }
    //         //divisi crm
    //         else if(in_array(Auth::user()->role_id,[54,55,56])){
    //             if(in_array(Auth::user()->role_id,[54])){
    //                 $data = $data->where('sl_leads.crm_id',Auth::user()->id);
    //             }else if(in_array(Auth::user()->role_id,[55,56])){

    //             }
    //         };
            
    //         $data = $data->get();
                        

    //         foreach ($data as $key => $value) {
    //             $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_leads)->isoFormat('D MMMM Y');
    //         }
    //         return DataTables::of($data)
    //         ->make(true);
    //     } catch (\Exception $e) {
    //         dd($e);
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

    // public function saveChildLeads(Request $request) {
    //     try {
    //         DB::beginTransaction();
    //         $current_date_time = Carbon::now()->toDateTimeString();
    //         $nomor = $this->generateNomor();
    //         $leadsParent = DB::table('sl_leads')->where('id',$request->leads_id)->first();

    //         $newId = DB::table('sl_leads')->insertGetId([
    //             'nomor' =>  $nomor,
    //             'leads_id' => $leadsParent->id,
    //             'tgl_leads' => $current_date_time,
    //             'nama_perusahaan' => $request->nama_perusahaan,
    //             'telp_perusahaan' => $leadsParent->telp_perusahaan,
    //             'jenis_perusahaan_id' => $leadsParent->jenis_perusahaan_id,
    //             'branch_id' => $leadsParent->branch_id,
    //             'platform_id' => 8,
    //             'kebutuhan_id' => $leadsParent->kebutuhan_id,
    //             'alamat' => $leadsParent->alamat,
    //             'pic' => $leadsParent->pic,
    //             'jabatan' => $leadsParent->jabatan,
    //             'no_telp' => $leadsParent->no_telp,
    //             'email' => $leadsParent->email,
    //             'status_leads_id' => 1,
    //             'notes' => $leadsParent->notes,
    //             'created_at' => $current_date_time,
    //             'created_by' => Auth::user()->full_name
    //         ]);

    //         //insert ke activity sebagai activity pertama
    //         $customerActivityController = new CustomerActivityController();
    //         $nomorActivity = $customerActivityController->generateNomor($newId);

    //         $activityId = DB::table('sl_customer_activity')->insertGetId([
    //             'leads_id' => $newId,
    //             'branch_id' => $leadsParent->branch_id,
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

    //         if (Auth::user()->role_id==29) {
    //             //cari tim sales
    //             $timSalesD = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();
    //             if($timSalesD != null){
    //                 DB::table('sl_leads')->where('id',$newId)->update([
    //                     'tim_sales_id' => $timSalesD->tim_sales_id,
    //                     'tim_sales_d_id' =>$timSalesD->id
    //                 ]);

    //                 DB::table('sl_customer_activity')->where('id',$activityId)->update([
    //                     'tim_sales_id' => $timSalesD->tim_sales_id,
    //                     'tim_sales_d_id' =>$timSalesD->id
    //                 ]);
    //             }
    //         }
    //         DB::commit();
    //         return response()->json(['status' => 'success', 'message' => 'Leads '.$request->nama_perusahaan.' berhasil disimpan.']);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    //         SystemController::saveError($e,Auth::user(),$request);
    //         abort(500);
    //     }
    // }

}

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
            // $listArea = DB::table('m_training_area')->where('is_aktif', 1)->orderBy('area', 'ASC')->get();
            // $listClient = DB::table('m_training_client')->where('is_aktif', 1)->orderBy('client', 'ASC')->get();
            $listMateri = DB::table('m_training_materi')->where('is_aktif', 1)->orderBy('materi', 'ASC')->get();
            $listTrainer = DB::table('m_training_trainer')->where('is_aktif', 1)->orderBy('trainer', 'ASC')->get();

            return view('sdt.training.add',compact('listBu','listMateri', 'listTrainer'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function listArea(Request $request){
        try {
            // $now = Carbon::now()->isoFormat('DD MMMM Y');
            
            $listArea = DB::table('m_training_area')
            ->where('is_aktif', 1)
            ->where('laman_id', $request->id)
            ->orderBy('area', 'ASC')->get();
            
            return response()->json([
                'success'   => false,
                'data'      => $listArea,
                'message'   => "Berhasil get data area"
            ], 200);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function listClient(Request $request){
        try {
            // $now = Carbon::now()->isoFormat('DD MMMM Y');
            
            $listClient = DB::table('m_training_client')
            ->where('is_aktif', 1)
            ->where('laman_id', $request->laman_id)
            ->where('area_id', $request->area_id)
            ->orderBy('client', 'ASC')->get();
            
            return response()->json([
                'success'   => false,
                'data'      => $listClient,
                'message'   => "Berhasil get data client"
            ], 200);

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

            $message = $data->whatsapp_message;
            // $message = str_replace("{tanggal}", "", $data->waktu_mulai);

            $listBu = DB::table('m_training_laman')->orderBy('laman', 'ASC')->get();
            $listArea = DB::table('m_training_area')->where('is_aktif', 1)->orderBy('area', 'ASC')->get();
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

            return view('sdt.training.view', compact('listClient', 'listTrainer','namaPerusahaan', 'data', 'listPeserta', 'listBu', 'listMateri', 'listImage', 'message', 'listArea'));
            // return view('sdt.training.view',compact('activity','data','branch','jabatanPic','namaPerusahaan','kebutuhan','platform'));
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(), $request);
            abort(500);
        }
        
    }

    public function list (Request $request){
        try {
            $data = DB::table('sdt_training as tr')
                        ->leftjoin('m_training_materi as mtm','mtm.id', '=', 'tr.id_materi')
                        ->leftJoin('sdt_training_client as stc','stc.id_training', '=', DB::raw('tr.id_training AND stc.is_active = 1'))
                        ->leftJoin('m_training_client as mtc', 'mtc.id' ,'=', 'stc.id_client')
                        ->leftJoin('sdt_training_trainer as stt', 'stt.id_training', '=', DB::raw('tr.id_training AND stt.is_active = 1'))
                        ->leftJoin('m_training_trainer as mtt','mtt.id', '=', 'stt.id_trainer')
                        ->leftJoin('m_training_area as mta','mta.id', '=', 'tr.id_area')
                        ->leftJoin('sdt_training_client_detail as stcd', 'stcd.training_id', '=', DB::raw('tr.id_training AND stcd.is_active = 1'))
                        
                        ->select(
                            "tr.id_training as id",
                            "mtm.materi", 
                            DB::raw("DATE_FORMAT(tr.waktu_mulai,'%d-%m-%Y %H:%i') as waktu_mulai"),
                            DB::raw("DATE_FORMAT(tr.waktu_selesai,'%d-%m-%Y %H:%i') as waktu_selesai"),
                            "mta.area", 
                            DB::raw("IF(tr.id_pel_tipe = 1, 'ON SITE', 'OFF SITE') as tipe"),
                            DB::raw("IF(tr.id_pel_tempat = 1, 'IN DOOR', 'OUT DOOR') AS tempat"),
                            DB::raw("group_concat(distinct mtc.client separator ', ') AS client"),
                            DB::raw("count(mtc.client) AS total_client"),
                            // DB::raw("sum(stc.peserta_hadir) AS total_peserta"),
                            DB::raw("count(distinct stcd.id) AS total_peserta"),
                            DB::raw("group_concat(distinct mtt.trainer separator ', ') AS trainer"), 
                            DB::raw("count(distinct mtt.id) AS total_trainer"))
                        ->where('tr.is_aktif', 1)
                        ->orderBy('tr.id_training', 'DESC')
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
                    // 'id_pel_tipe' => $request->tipe_id,
                    'id_pel_tempat' => $request->tempat_id,
                    'id_materi' => $request->materi_id,
                    // 'id_laman' => $request->laman_id,
                    'alamat' => $request->alamat,
                    'link_zoom' => $request->link_zoom,
                    'updated_at' => $current_date_time,
                    // 'id_area' => $request->area_id,
                    'enable' => ($request->enable == 'on' ? 1 : 0)
                ]);
                $msgSave = 'Training berhasil diubah.';
                
            }else{
                $message = "*Undangan Training Shelter*
                Tanggal Jam : {tanggal}
                Materi : {materi}
                Trainer : {trainer}
                Tempat : {tempat}
                Tipe : {tipe}
                Alamat : {alamat}
                Link Zoom : {zoom}
                Keterangan : {keterangan}
                Link Kehadiran : {link}";

                // $nomor = $this->generateNomor();
                $trainingId = DB::table('sdt_training')->insertGetId([
                    'keterangan' => $request->keterangan,
                    'waktu_mulai' => $request->start_date,
                    'waktu_selesai' => $request->end_date,
                    // 'id_pel_tipe' => $request->tipe_id,
                    'id_pel_tempat' => $request->tempat_id,
                    'id_materi' => $request->materi_id,
                    'id_laman' => $request->laman_id,
                    'alamat' => $request->alamat,
                    'link_zoom' => $request->link_zoom,
                    'id_user' => Auth::user()->id,
                    'created_at' => $current_date_time,
                    'whatsapp_message' => $message,
                    'id_area' => $request->area_id,
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

    public function saveMessage(Request $request) {
        try {
            // dd($request->id . " " . $request->pesan_undangan);
            DB::beginTransaction();
            DB::table('sdt_training')->where('id_training',$request->id)->update([
                'whatsapp_message' => $request->pesan_undangan
            ]);
            
            DB::commit();
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil merubah pesan whatsapp"
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
            
            // $data = DB::table('sdt_training')
            //         ->where('is_aktif', 1)
            //         ->where('id_training', $request->id)
            //         ->first();

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
                            DB::raw("count(distinct mtt.id) AS total_trainer"),
                            "tr.link_zoom",
                            "tr.keterangan",
                            "tr.alamat",
                            "tr.whatsapp_message"
                        )
                        ->where('tr.is_aktif', 1)
                        ->where('tr.id_training', $request->id)
                        ->groupBy('tr.id_training')
                        ->first();

            $message = str_replace(
                array('{tanggal}', '{keterangan}', '{tempat}', '{zoom}', '{alamat}', '{tipe}', '{materi}', '{trainer}', '{link}'), 
                array($data->waktu_mulai, $data->keterangan, $data->tempat, $data->link_zoom, $data->alamat, $data->tipe, $data->materi, $data->trainer, url('sdt-training?id=').$request->id), 
                $data->whatsapp_message); 

            $myarray = explode(',', $request->no_wa);
            $current_date_time = Carbon::now()->toDateTimeString();
            foreach ($myarray as $key => $value) {
                $baseNumber = substr($value,0, 2);
                if($baseNumber == '08'){
                    $baseNumber = '62' . substr($value,1);
                }else if($baseNumber == '+6'){
                    $baseNumber = '62' . substr($value,2);
                }

                // dd($baseNumber);    
                          
                DB::table('whatsapp_message')->insert([
                    'nomor_wa' => $baseNumber,
                    'message' => $message,
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

}

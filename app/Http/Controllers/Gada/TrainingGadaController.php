<?php

namespace App\Http\Controllers\Gada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use \PDF;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use \stdClass;
use Illuminate\Support\Facades\Storage;


class TrainingGadaController extends Controller
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
        return view('sdt.gada.list',compact('branch','platform','status','tglDari','tglSampai','request','error','success'));
    }

    public function generateInvoice(Request $request, $id)
    {
        // dd($id);
        $dataGada = DB::table('training_gada_calon')
            ->where('is_active', 1)
            ->where('id', $id)
            ->first();

        $dataTransaksi = DB::table('training_gada_transaksi')
            ->where('is_active', 1)
            ->where('training_gada_id', $id)
            ->select('*', DB::raw("DATE_FORMAT(payment_date,'%d-%m-%Y') as tanggal_bayar"))
            ->first();

        $data = ['title' => 'Tanda Terima', 'name' => $dataGada->nama, 'nominal' => number_format($dataTransaksi->harga), 'terbilang' => $this->terbilang($dataTransaksi->harga),
            'jenis_pelatihan' => $dataGada->jenis_pelatihan, 'transaksi_id' => $dataTransaksi->id, 'tanggal' => $dataTransaksi->tanggal_bayar];
        $pdf = Pdf::loadView('sdt.gada.invoice', $data);
        return $pdf->stream('invoice.pdf');
    }


    public function terbilang($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";

        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai/10) . " Puluh " . $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = "seratus " . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai/100) . " Ratus " . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = "seribu " . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai/1000) . " Ribu " . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai/1000000) . " Juta " . $this->terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->terbilang($nilai/1000000000) . " Milyar " . $this->terbilang($nilai % 1000000000);
        } else {
            $temp = $this->terbilang($nilai/1000000000000) . " Trilyun " . $this->terbilang($nilai % 1000000000000);
        }

        return trim($temp);
    }

    public function list (Request $request){
        try {
            // <a href="'.route('training-gada.generateInvoice').'" class="btn-view btn btn-info waves-effect btn-xs"><i class="mdi mdi-eye"></i>&nbsp;View</a>&nbsp;
            $data = DB::table('training_gada_calon as data')
            ->leftjoin('training_gada_bukti_bayar as bukti','bukti.training_id', '=', 'data.id')
            ->where('data.is_active', 1)
            ->select('data.*', DB::raw("IF(data.status = 1, 'New Register', IF(data.status = 2, 'Leads', IF(data.status = 3, 'Cold Prospect', IF(data.status = 4, 'Hot Prospect', 'Peserta')))) as status_name"),
                DB::raw("DATE_FORMAT(data.last_sent_notif_register,'%d-%m-%Y %H:%i') as last_sent"), 'bukti.path')
            ->orderBy('data.id', 'DESC')
            ->get();
            return DataTables::of($data)
            ->editColumn('status_name', function ($data) {
                if($data->status == 1 || $data->status == 2 || $data->status == 3 || $data->status == 1){
                    return '<span class="text-info">'.$data->status_name.'</span>';
                }else if($data->status == 5){
                    return '<span class="text-info">'.$data->status_name.'</span>';
                }else{
                    return '<div><button onclick="showMessageWhatsapp('.$data->id.','.$data->no_wa.',\''.$data->last_sent.'\',\''.$data->jenis_pelatihan.'\',\''.$data->nama.'\')" class="btn btn-warning btn-sm">'.$data->status_name.' </button></div>';
                }
            })
            ->editColumn('status_bayar', function ($data) {
                if($data->data_registrasi == 1 && $data->status_bayar == 0){
                    return '<div><button onclick="showMessageWhatsappInvoice('.$data->id.','.$data->no_wa.',\''.$data->last_sent.'\',\''.$data->jenis_pelatihan.'\',\''.$data->nama.'\', 0, \'\', \'\')" class="btn btn-danger btn-sm">Belum Bayar</buuton></div>';
                }else if($data->data_registrasi == 1 && $data->status_bayar == 1){
                    return '<div><button onclick="showMessageWhatsappInvoice('.$data->id.','.$data->no_wa.',\''.$data->last_sent.'\',\''.$data->jenis_pelatihan.'\',\''.$data->nama.'\', 1, \''.$data->path.'\', \''.route('training-gada.generateInvoice', $data->id).'\')" class="btn btn-success btn-sm">Sudah Bayar</buuton></div>';
                }else{
                    return '-';
                }
            })
            ->addColumn('aksi', function ($data) {
                if($data->data_registrasi == 1){
                    if(null == $data->path){
                        return '<div class="justify-content-center d-flex">
                            <div onclick="showChangeStatus('.$data->id.','.$data->status.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Status</div>&nbsp;
                            <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
                            <div onclick="showDataRegistrasi('.$data->id.')" class="btn btn-success waves-effect btn-xs"><i class="mdi mdi-account-plus"></i>&nbsp;Registrasi</div>&nbsp;
                            <div onclick="showDataBuktiBayar('.$data->id.')" class="btn btn-warning waves-effect btn-xs"><i class="mdi mdi-upload"></i>&nbsp;Bukti Bayar</div>
                            </div>';
                    }else{
                        return '<div class="justify-content-center d-flex">
                            <div onclick="showChangeStatus('.$data->id.','.$data->status.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Status</div>&nbsp;
                            <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
                            <div onclick="showDataRegistrasi('.$data->id.')" class="btn btn-success waves-effect btn-xs"><i class="mdi mdi-account-plus"></i>&nbsp;Registrasi</div>&nbsp;
                            </div>';
                    }
                }else{
                    return '<div class="justify-content-center d-flex">
                        <div onclick="showChangeStatus('.$data->id.','.$data->status.')" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-tag"></i>&nbsp;Status</div>&nbsp;
                        <div onclick="showlistLog('.$data->id.')" class="btn btn-info waves-effect btn-xs"><i class="mdi mdi-information"></i>&nbsp;Log</div>&nbsp;
                    </div>';
                }
            })
            ->rawColumns(['aksi','status_name', 'status_bayar'])
            ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function listLog(Request $request){
        try {
            $pendaftarId =  $request->pendaftar_id;
            // dd($pendaftarId);
            $data = DB::table('training_gada_log')
            ->where('is_active', 1)
            ->where('calon_id', $pendaftarId)
            ->select('status_name', 'keterangan', DB::raw("DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as created_date"))
            ->get();

            return DataTables::of($data)->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            // dd($e);
            abort(500);
        }
    }

    public function uploadBuktiBayar(Request $request)
    {
        $file = $request->file('image');
        $id = $request->bukti_bayar_id;
        $extension = $file->getClientOriginalExtension();

        $filename = $file->getClientOriginalName();
        $filename = str_replace(".".$extension,"",$filename);
        $originalName = $id.$filename.".".$extension."";
        $current_date_time = Carbon::now()->toDateTimeString();

        $dataUpload = DB::table('training_gada_bukti_bayar')
            ->where('is_active', 1)
            ->where('training_id', $id)
            ->first();

        $link = env('APP_URL').'/public/uploads/training-gada/bukti-bayar/'.$originalName;
        if(null == $dataUpload){
            Storage::disk('training-gada-bukti-bayar')->put($originalName, file_get_contents($file));
            DB::table('training_gada_bukti_bayar')->insert([
                'training_id' => $id,
                'type' => 'image',
                'path' => $link,
                'file_name' => $originalName,
                'keterangan' => $request->bukti_bayar_keterangan,
                'created_at' => $current_date_time,
                'is_active' => 1
            ]);
        }else{
            Storage::disk('training-gada-bukti-bayar')->delete($dataUpload->file_name);
            Storage::disk('training-gada-bukti-bayar')->put($originalName, file_get_contents($file));
            DB::table('training_gada_bukti_bayar')
                ->where('id', $dataUpload->id)
                ->update([
                    'training_id' => $id,
                    'type' => 'image',
                    'path' => $link,
                    'file_name' => $originalName,
                    'keterangan' => $request->bukti_bayar_keterangan,
                    'created_at' => $current_date_time,
                    'is_active' => 1
                ]
            );
        }

        $dataTransaksi = DB::table('training_gada_transaksi')
            ->where('training_gada_id', $id)
            ->update([
                'payment_who' => Auth::user()->id,
                'status' => 1,
                'payment_date' => $current_date_time,
            ]
        );

        $dataTransaksi = DB::table('training_gada_transaksi')
            ->where('is_active', 1)
            ->where('training_gada_id', $id)
            ->first();

        DB::table('training_gada_transaksi_detail')
            ->where('training_gada_transaksi_id', $dataTransaksi->id)
            ->update([
                'payment_who' => Auth::user()->id,
                'status' => 1,
                'payment_date' => $current_date_time,
            ]
        );

        DB::table('training_gada_calon')->where('id', $id)->update([
            'status_bayar' => 1
        ]);

        DB::table('training_gada_calon')->where('id', $id)->update([
            'status' => 5,
            'keterangan' => 'Peserta'
        ]);

        $logId = DB::table('training_gada_log')->insertGetId([
            'calon_id' => $id,
            'status' => 5,
            'status_name' => 'Peserta',
            'created_date' => $current_date_time,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->full_name,
            'is_active' => 1
        ]);

        return redirect()->back()->with('success', "Berhasil upload bukti bayar");
    }

    public function dataRegistrasi(Request $request){
        try {
            $dataRegistrasi = DB::table('training_gada_registrasi')
            ->where('is_active', 1)
            ->where('training_gada_calon_id', $request->pendaftar_id)
            ->orderBy('id', 'ASC')->first();

            return response()->json([
                'success'   => false,
                'data'      => $dataRegistrasi,
                'message'   => "Berhasil get data registrasi"
            ], 200);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function dataInvoice(Request $request){
        try {
            // $dataRegistrasi = DB::table('training_gada_calon')
            // ->where('id', $request->pendaftar_id)
            // ->select('jenis_pelatihan')
            // ->orderBy('id', 'ASC')->first();

            // $jenisGada = strtolower(str_replace('GADA', '', $dataRegistrasi->jenis_pelatihan));
            // $jenisGada = str_replace(' ', '', $jenisGada);

            $totalHarga = DB::table('training_gada_transaksi')->where('training_gada_id', $request->pendaftar_id)->where('is_active', 1)->select('harga')->first();
            // dd($totalHarga->harga);
            // foreach(explode(',', $jenisGada) as $jenis)
            // {
            //     $harga = DB::table('m_training_gada_harga')->where('jenis_training', $jenis)->where('is_active', 1)->select('harga')->first();
            //     $totalHarga += (int)$harga->harga;
            // }

            // dd($totalHarga);

            return response()->json([
                'success'   => false,
                'totalHarga'=> number_format($totalHarga->harga),
                'message'   => "Berhasil get data invoice"
            ], 200);

        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function updateStatus(Request $request) {
        try {
            // dd($request->id.' '.$request->status_id.' '.$request->keterangan);
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();

            // 1. New
            // 2. Leads
            // 3. Cold Prospect (interview manual by WA)
            // 4. Hot Prospect (dikirim link lanjutan)
            // 5. Peserta

            $statusName = '';
            if($request->status_id == 1){
                $statusName = 'New';
            } else if($request->status_id == 2){
                $statusName = 'Leads';
            } else if($request->status_id == 3){
                $statusName = 'Cold Prospect';
            } else if($request->status_id == 4){
                $statusName = 'Hot Prospect';
            } else if($request->status_id == 5){
                $statusName = 'Peserta';
            }

            DB::table('training_gada_calon')->where('id', $request->id)->update([
                'status' => $request->status_id,
                'keterangan' => $request-> keterangan
            ]);

            $logId = DB::table('training_gada_log')->insertGetId([
                'calon_id' => $request->id,
                'status' => $request->status_id,
                'status_name' => $statusName,
                'keterangan' => $request-> keterangan,
                'created_date' => $current_date_time,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->full_name,
                'is_active' => 1
            ]);

            $msgSave = 'Status berhasil diubah ';
            DB::commit();
            // return redirect()->back()->with('success', $msgSave);
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => "Berhasil ubah status"
            ], 200);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

}

<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringKontrakTemplateExport;
use \stdClass;

class MonitoringKontrakController extends Controller
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

        return view('sales.monitoring-kontrak.list',compact('tglDari','tglSampai'));
    }
    public function indexTerminate (Request $request){
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

        return view('sales.monitoring-kontrak.list-terminate',compact('tglDari','tglSampai'));
    }

    public function view (Request $request,$pksId){
        try {
           $pks = DB::table('sl_pks')->where('id',$pksId)->first();

            $data = DB::table('sl_customer_activity')->whereNull('deleted_at')->where('pks_id',$pksId)->orderBy('created_at','desc')->get();
            foreach ($data as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y HH:mm');
                $value->stgl_activity = Carbon::createFromFormat('Y-m-d',$value->tgl_activity)->isoFormat('D MMMM Y');
            }

            $leads = DB::table('sl_leads')->where('id',$pks->leads_id)->first();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->where('id',$leads->jenis_perusahaan_id)->first();
            if($jenisPerusahaan !=null){ $leads->jenis_perusahaan = $jenisPerusahaan->nama; }else{ $leads->jenis_perusahaan = ""; }

            $quotation = DB::table('sl_quotation')->where('id',$pks->quotation_id)->first();
            $quotation->detail = DB::table('sl_quotation_detail')->where('quotation_id',$quotation->id)->get();
            $quotation->site = DB::table('sl_site')->where('quotation_id',$quotation->id)->get();

            $pks->berakhir_dalam = $this->hitungBerakhirKontrak($quotation->kontrak_selesai);
            $quotation->mulai_kontrak = Carbon::createFromFormat('Y-m-d',$quotation->mulai_kontrak)->isoFormat('D MMMM Y');
            $quotation->kontrak_selesai = Carbon::createFromFormat('Y-m-d',$quotation->kontrak_selesai)->isoFormat('D MMMM Y');

            $spk =  DB::table('sl_spk')->where('id',$pks->spk_id)->first();

            return view('sales.monitoring-kontrak.view',compact('data','leads','quotation','spk','pks'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $db2 = DB::connection('mysqlhris')->getDatabaseName();

        $data = DB::table('sl_pks')
                ->whereNull('sl_pks.deleted_at')
                ->select('id','status_pks_id','nomor','kode_site','nama_site','nama_proyek','crm_id_1','crm_id_2','crm_id_3','spv_ro_id','ro_id_1','ro_id_2','ro_id_3','loyalty','kontrak_awal as mulai_kontrak','kontrak_akhir as kontrak_selesai','jumlah_hc',
                    DB::raw("(select full_name from ".$db2.".m_user where id = sl_pks.sales_id) as sales"),

                DB::raw('(SELECT GROUP_CONCAT(full_name SEPARATOR "<br /> ")
                        FROM '.$db2.'.m_user
                        WHERE '.$db2.'.m_user.id IN (spv_ro_id,ro_id_1, ro_id_2, ro_id_3)) as ro'),
                    DB::raw('(SELECT GROUP_CONCAT(full_name SEPARATOR "<br /> ")
                        FROM '.$db2.'.m_user
                        WHERE '.$db2.'.m_user.id IN (crm_id_1, crm_id_2, crm_id_3)) as crm'),
                    DB::raw('(SELECT COUNT(*) FROM sl_customer_activity WHERE sl_customer_activity.pks_id = sl_pks.id AND sl_customer_activity.deleted_at IS NULL) as aktifitas'),
                )
                ->get();

        foreach ($data as $key => $value) {
            $value->s_mulai_kontrak = Carbon::createFromFormat('Y-m-d',$value->mulai_kontrak)->isoFormat('D MMMM Y');
            $value->s_kontrak_selesai = Carbon::createFromFormat('Y-m-d',$value->kontrak_selesai)->isoFormat('D MMMM Y');
            $status = DB::table('m_status_pks')->where('id',$value->status_pks_id)->first();
            if($status){
                $value->status = $status->nama;
            }else{
                $value->status = "";
            }
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            // if (!in_array(Auth::user()->role_id,[2,54,55])) {
            //     return "";
            // }
            // return '<div class="d-inline-flex" data-bs-toggle="tooltip" data-bs-html="true" aria-label="<span>Sent<br> <span class=&quot;fw-medium&quot;>Balance:</span> 0<br> <span class=&quot;fw-medium&quot;>Due Date:</span> 05/09/2020</span>" data-bs-original-title="<span>Sent<br> <span class=&quot;fw-medium&quot;>Balance:</span> 0<br> <span class=&quot;fw-medium&quot;>Due Date:</span> 05/09/2020</span>"><span class="avatar avatar-sm"> <span class="avatar-initial rounded-circle bg-label-secondary"><i class="mdi mdi-email-outline"></i></span></span></div>';
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
            $aksi = "";
            $aksi .= '&nbsp;<a href="'.route('monitoring-kontrak.view', $data->id).'" class="btn btn-sm btn-secondary">View</a>';
            $aksi .= '&nbsp;<a href="'.route('customer-activity.add-activity-kontrak',$data->id).'" class="btn btn-sm btn-info">Buat Activity</a>';

            if($selisih<=90 && $selisih !=0){
                $aksi .= '&nbsp;<a href="#" class="btn btn-sm btn-success">Send Email</a>';
                // $aksi .= '&nbsp;<a href="'.route('quotation.add', ['leads_id' => $data->leads_id, 'tipe' => 'Quotation Lanjutan']).'" class="btn btn-sm btn-primary">Buat Quotation</a>';
            }
            if($selisih == 0){
                $aksi .= '&nbsp;<a href="javscript:void(0)" class="btn btn-sm btn-danger btn-terminate-kontrak" data-id="'.$data->id.'">Terminate Kontrak</a>';
            }

            if (empty($data->ro) && in_array(Auth::user()->role_id,[2,8,6,98])) {
                $aksi .= '&nbsp;<a href="'.route('customer-activity.add-ro-kontrak',$data->id).'" class="btn btn-sm btn-warning">Pilih RO</a>';
            }
            if (empty($data->crm) && in_array(Auth::user()->role_id,[2,55,56,96])) {
                $aksi .= '&nbsp;<a href="'.route('customer-activity.add-crm-kontrak',$data->id).'" class="btn btn-sm btn-warning">Pilih CRM</a>';
            }
            if(in_array(Auth::user()->role_id,[2,56])&&$data->status_pks_id != 7 ){
                if ($data->ro != "" && $data->crm != "") {
                    $aksi .= '&nbsp;<a href="javascript:void(0)" class="btn btn-sm btn-success">Aktifkan Site</a>';
                } else {
                    $aksi .= '&nbsp;<button class="btn btn-sm btn-success" onclick="Swal.fire({title: \'Pemberitahuan\', text: \'Belum memilih RO atau CRM\', icon: \'warning\'})">Aktifkan Site</button>';
                }
            }

            return '<div class="justify-content-center d-flex">
                                '.$aksi.'
                    </div>';
        })
        ->addColumn('berakhir_dalam', function ($data) {
            return $this->hitungBerakhirKontrak($data->kontrak_selesai);
        })
        ->addColumn('warna_row', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
            if($selisih<=0){
                return '#2c3e5040';
            }else if($selisih<=60){
                return '#c0392b40';
            }else if($selisih<=90){
                return '#f39c1240';
            }else{
                return '#27ae6040';
            }
        })
        ->addColumn('warna_font', function ($data) {
            $selisih = $this->selisihKontrakBerakhir($data->kontrak_selesai);
            if($selisih<=0){
                return '#636578';
            }else if($selisih<=60){
                return '#636578';
            }else if($selisih<=90){
                return '#636578';
            }else{
                return '#636578';
            }
        })
        // ->editColumn('nomor', function ($data) {
        //     return '<a href="'.route('pks.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        // })
        ->editColumn('aktifitas', function ($data) {
            return '<button class="btn btn-sm btn-info" onclick="openNormalDataTableModal(`'.route('customer-activity.modal.list-activity-kontrak',['pks_id' => $data->id]).'`,`DATA AKTIFITAS PADA KONTRAK '.$data->nomor.'`)">'.$data->aktifitas.'</button>';
        })
        ->rawColumns(['aksi','nomor','nama_site','aktifitas','crm','ro','sales'])
        ->make(true);
    }

    public function listTerminate (Request $request){
        $data = DB::table('sl_pks')
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->leftJoin('sl_quotation','sl_pks.quotation_id','sl_quotation.id')
                ->where('sl_pks.status_pks_id',100)
                ->select('sl_quotation.leads_id','sl_quotation.kontrak_selesai','sl_quotation.mulai_kontrak','sl_pks.spk_id','sl_pks.quotation_id','sl_pks.created_by','sl_pks.created_at','sl_pks.id','sl_pks.nomor','sl_spk.nomor as nomor_spk','sl_pks.tgl_pks','sl_quotation.nama_perusahaan','sl_quotation.kebutuhan','sl_pks.status_pks_id','sl_quotation.nomor as nomor_quotation',
                DB::raw('(SELECT GROUP_CONCAT(nama_site SEPARATOR "<br /> ")
                    FROM sl_quotation_site
                    WHERE sl_quotation_site.quotation_id = sl_quotation.id) as nama_site')
                )
                ->get();

        foreach ($data as $key => $value) {
            $value->tgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_pks)->isoFormat('D MMMM Y');
            $value->mulai_kontrak = Carbon::createFromFormat('Y-m-d',$value->mulai_kontrak)->isoFormat('D MMMM Y');
            $value->s_kontrak_selesai = Carbon::createFromFormat('Y-m-d',$value->kontrak_selesai)->isoFormat('D MMMM Y');
            $value->created_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_pks')->where('id',$value->status_pks_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('warna_row', function ($data) {
            return '#2c3e5040';
        })
        ->addColumn('warna_font', function ($data) {
            return '#636578';
        })
        ->editColumn('nomor', function ($data) {
            return '<a href="#" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        })
        ->editColumn('nomor_spk', function ($data) {
            return '<a href="#" style="font-weight:bold;color:#000056">'.$data->nomor_spk.'</a>';
        })
        ->editColumn('nomor_quotation', function ($data) {
            return '<a href="#" style="font-weight:bold;color:#000056">'.$data->nomor_quotation.'</a>';
        })
        ->rawColumns(['nomor','nama_site','nomor_spk','nomor_quotation'])
        ->make(true);
    }

    function hitungBerakhirKontrak($tanggalBerakhir) {
        // Tanggal saat ini
        $tanggalSekarang = Carbon::now()->format('Y-m-d');
        $tanggalSekarang = Carbon::createFromFormat('Y-m-d', $tanggalSekarang);

        // Buat objek tanggal dari input
        $tanggalBerakhir = Carbon::createFromFormat('Y-m-d', $tanggalBerakhir);

        // Jika kontrak sudah habis
        if ($tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir)) {
            return "Kontrak habis";
        }

        // Hitung selisih
        $selisih = $tanggalSekarang->diff($tanggalBerakhir);

        // Format output hanya jika nilainya lebih dari 0
        $hasil = [];
        if ($selisih->y > 0) {
            $hasil[] = "{$selisih->y} tahun";
        }
        if ($selisih->m > 0) {
            $hasil[] = "{$selisih->m} bulan";
        }
        if ($selisih->d > 0) {
            $hasil[] = "{$selisih->d} hari";
        }

        // Gabungkan hasil menjadi string
        return implode(', ', $hasil);
    }
    function selisihKontrakBerakhir($tanggalBerakhir) {
         // Tanggal sekarang
        $tanggalSekarang = Carbon::now();

        // Tanggal kontrak berakhir
        $tanggalBerakhir = Carbon::createFromFormat('Y-m-d', $tanggalBerakhir);

        // Jika kontrak sudah habis
        if ($tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir)) {
            return 0;
        }

        // Hitung selisih dalam hari
        $selisihHari = $tanggalSekarang->diffInDays($tanggalBerakhir);

        return $selisihHari;
    }

    public function terminate(Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $pksId = $request->id;
            $pks = DB::table('sl_pks')->where('id',$pksId)->first();
            $spkId = $pks->spk_id;
            $quotationId = $pks->quotation_id;

            DB::table('sl_pks')->where('id',$pksId)->update(
                ['status_pks_id'=>100,'deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_spk')->where('id',$spkId)->update(
                ['status_spk_id'=>100,'deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation')->where('id',$quotationId)->update(
                ['status_quotation_id'=>100,'deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );

            // update deleted at dan deleted by semua detail dari quotation
            DB::table('sl_quotation_aplikasi')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_chemical')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_coss')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_hpp')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_requirement')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_detail_tunjangan')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_devices')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_kaporlap')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_kerjasama')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_margin')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_ohc')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_pic')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_site')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );
            DB::table('sl_quotation_training')->where('quotation_id',$quotationId)->update(
                ['deleted_at'=>$current_date_time,'deleted_by'=>Auth::user()->id]
            );

            // HRIS belum di terminate

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil di terminate']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function templateImport(Request $request) {
        $dt = Carbon::now()->toDateTimeString();

        return Excel::download(new MonitoringKontrakTemplateExport(), 'Template Monitoring Kontrak-'.$dt.'.xlsx');
    }

    public function import (Request $request){
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('sales.monitoring-kontrak.import',compact('now'));
    }

    public function inquiryImport(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:csv,xls,xlsx',
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
                'mimes' => 'tipe file harus csv,xls atau xlsx',
            ]);

            $array = null;
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }else{
                $file = $request->file('file');
                $current_date_time = Carbon::now()->toDateTimeString();
                $importId = uniqid();
                $db2 = DB::connection('mysqlhris')->getDatabaseName();

                // Get the csv rows as an array
                $array = Excel::toArray(new stdClass(), $file);
                $jumlahError = 0;
                $jumlahWarning = 0;
                $jumlahSuccess = 0;
                foreach ($array[0] as $key => $v) {
                    if($key==0){
                        continue;
                    };
                    if($v[0]==null){
                        continue;
                    }
                    $layanan = DB::table('m_kebutuhan')->where('nama', 'LIKE',$v[17])->first();
                    $layananId = $layanan ? $layanan->id : null;
                    $bidangUsaha = DB::table('m_bidang_perusahaan')->where('nama','LIKE',$v[19])->first();
                    $bidangUsahaId = $bidangUsaha ? $bidangUsaha->id : null;
                    $jenisPerusahaan = DB::table('m_jenis_perusahaan')->where('nama','LIKE',$v[20])->first();
                    $jenisPerusahaanId = $jenisPerusahaan ? $jenisPerusahaan->id : null;
                    $statusPks = DB::table('m_status_pks')->where('nama','LIKE',$v[18])->first();
                    $statusPksId = $statusPks ? $statusPks->id : null;
                    $provinsi = DB::table($db2.'.m_province')->where('name','LIKE',$v[21])->first();
                    $provinsiId = $provinsi ? $provinsi->id : null;
                    $kota = DB::table($db2.'.m_city')->where('name','LIKE',$v[22])->first();
                    $kotaId = $kota ? $kota->id : null;
                    $crm = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[54])->first();
                    $crmId1 = $crm ? $crm->id : null;
                    $crm = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[58])->first();
                    $crmId2 = $crm ? $crm->id : null;
                    $crm = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[62])->first();
                    $crmId3 = $crm ? $crm->id : null;
                    $spvRo = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[55])->first();
                    $spvRoId = $spvRo ? $spvRo->id : null;
                    $ro = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[56])->first();
                    $roId1 = $ro ? $ro->id : null;
                    $ro = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[59])->first();
                    $roId2 = $ro ? $ro->id : null;
                    $ro = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[63])->first();
                    $roId3 = $ro ? $ro->id : null;
                    $loyalty = DB::table('m_loyalty')->where('nama','LIKE',$v[24])->first();
                    $loyaltyId = $loyalty ? $loyalty->id : null;
                    $company = DB::table($db2.'.m_company')->where('name','LIKE',$v[14])->first();
                    $companyId = $company ? $company->id : null;
                    $kategoriSesuaiHc = DB::table('m_kategori_sesuai_hc')->where('nama','LIKE',$v[66])->first();
                    $kategoriSesuaiHcId = $kategoriSesuaiHc ? $kategoriSesuaiHc->id : null;
                    $sales = DB::table($db2.'.m_user')->where('full_name','LIKE',$v[67])->first();
                    $salesId = $sales ? $sales->id : null;

                    DB::table('sl_pks_import')->insert([
                        'import_id' => $importId,
                        'quotation_id' => null,
                        'spk_id' => null,
                        'leads_id' => null,
                        'site_id' => null,
                        'company_id' => $companyId,
                        'kode_site' => $v[8],
                        'nomor' => $v[0],
                        'tgl_pks' => Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($v[25])->toDateString(),
                        'nama_site' => $v[9],
                        'alamat_site' => $v[10],
                        'nama_proyek' => $v[15],
                        'layanan_id' => $layananId,
                        'layanan' => $layananId ? $v[17] : null,
                        'bidang_usaha_id' => $bidangUsahaId,
                        'bidang_usaha' => $bidangUsahaId ? $v[19] : null,
                        'jenis_perusahaan_id' => $jenisPerusahaanId,
                        'jenis_perusahaan' =>  $jenisPerusahaanId ? $v[20] : null,
                        'link_pks_disetujui' => null,
                        'status_pks_id' => $statusPksId,
                        'provinsi_id' => $provinsiId,
                        'provinsi' => $provinsiId ? $v[21] : null,
                        'kota_id' => $kotaId,
                        'kota' => $kotaId ? $v[22] : null,
                        'pma' => $v[23],
                        'sales_id' => $salesId,
                        'crm_id_1' => $crmId1,
                        'crm_id_2' => $crmId2,
                        'crm_id_3' => $crmId3,
                        'spv_ro_id' => $spvRoId,
                        'ro_id_1' => $roId1,
                        'ro_id_2' => $roId2,
                        'ro_id_3' => $roId3,
                        'loyalty_id' => $loyaltyId,
                        'loyalty' => $loyaltyId ? $v[24] : null,
                        'kontrak_awal' => Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($v[25])->toDateString(),
                        'kontrak_akhir' => Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($v[26])->toDateString(),
                        'jumlah_hc' => $v[27],
                        'total_sebelum_pajak' => $v[28],
                        'dasar_pengenaan_pajak' => $v[29],
                        'ppn' => $v[30],
                        'pph' => $v[31],
                        'total_invoice' => $v[32],
                        'persen_mf' => $v[33],
                        'nominal_mf' => $v[34],
                        'persen_bpjs_tk' => $v[35],
                        'nominal_bpjs_tk' => $v[36],
                        'persen_bpjs_ks' => $v[37],
                        'nominal_bpjs_ks' => $v[38],
                        'as_tk' => $v[39],
                        'as_ks' => $v[40],
                        'ohc' => $v[41],
                        'thr_provisi' => $v[42],
                        'thr_ditagihkan' => $v[43],
                        'penagihan_selisih_thr' => $v[44],
                        'kaporlap' => $v[45],
                        'device' => $v[46],
                        'chemical' => $v[47],
                        'training' => $v[48],
                        'biaya_training' => $v[49],
                        'tgl_kirim_invoice' => $v[50],
                        'jumlah_hari_top' => $v[51],
                        'tipe_hari_top' => $v[52],
                        'tgl_gaji' => $v[53],
                        'pic_1' => $v[54],
                        'jabatan_pic_1' => $v[55],
                        'email_pic_1' => $v[56],
                        'telp_pic_1' => $v[57],
                        'pic_2' => $v[58],
                        'jabatan_pic_2' => $v[59],
                        'email_pic_2' => $v[60],
                        'telp_pic_2' => $v[61],
                        'pic_3' => $v[62],
                        'jabatan_pic_3' => $v[63],
                        'email_pic_3' => $v[64],
                        'telp_pic_3' => $v[65],
                        'kategori_sesuai_hc_id' => $kategoriSesuaiHcId,
                        'kategori_sesuai_hc' => $kategoriSesuaiHcId ? $v[66] : null,
                        'is_aktif' => 1,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name,
                    ]);

                    $jumlahSuccess++;
                    // foreach ($v as $keyd => $value) {

                    // }
                }
            }
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            DB::commit();
            $datas = DB::table('sl_pks_import')->where('import_id',$importId)->get();
            return view('sales.monitoring-kontrak.inquiry',compact('importId','datas','now','jumlahError','jumlahSuccess','jumlahWarning'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveImport(Request $request){
        DB::beginTransaction();
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $importId = $request->importId;
            $datas = DB::table('sl_pks_import')->where('import_id',$importId)->get();
            foreach ($datas as $data) {
                DB::table('sl_pks')->insert([
                    'quotation_id' => $data->quotation_id,
                    'spk_id' => $data->spk_id,
                    'leads_id' => $data->leads_id,
                    'site_id' => $data->site_id,
                    'company_id' => $data->company_id,
                    'kode_site' => $data->kode_site,
                    'nomor' => $data->nomor,
                    'tgl_pks' => $data->tgl_pks,
                    'nama_site' => $data->nama_site,
                    'alamat_site' => $data->alamat_site,
                    'nama_proyek' => $data->nama_proyek,
                    'layanan_id' => $data->layanan_id,
                    'layanan' => $data->layanan,
                    'bidang_usaha_id' => $data->bidang_usaha_id,
                    'bidang_usaha' => $data->bidang_usaha,
                    'jenis_perusahaan_id' => $data->jenis_perusahaan_id,
                    'jenis_perusahaan' => $data->jenis_perusahaan,
                    'link_pks_disetujui' => $data->link_pks_disetujui,
                    'status_pks_id' => $data->status_pks_id,
                    'provinsi_id' => $data->provinsi_id,
                    'provinsi' => $data->provinsi,
                    'kota_id' => $data->kota_id,
                    'kota' => $data->kota,
                    'pma' => $data->pma,
                    'sales_id' => $data->sales_id,
                    'crm_id_1' => $data->crm_id_1,
                    'crm_id_2' => $data->crm_id_2,
                    'crm_id_3' => $data->crm_id_3,
                    'spv_ro_id' => $data->spv_ro_id,
                    'ro_id_1' => $data->ro_id_1,
                    'ro_id_2' => $data->ro_id_2,
                    'ro_id_3' => $data->ro_id_3,
                    'loyalty_id' => $data->loyalty_id,
                    'loyalty' => $data->loyalty,
                    'kontrak_awal' => $data->kontrak_awal,
                    'kontrak_akhir' => $data->kontrak_akhir,
                    'jumlah_hc' => $data->jumlah_hc,
                    'total_sebelum_pajak' => $data->total_sebelum_pajak,
                    'dasar_pengenaan_pajak' => $data->dasar_pengenaan_pajak,
                    'ppn' => $data->ppn,
                    'pph' => $data->pph,
                    'total_invoice' => $data->total_invoice,
                    'persen_mf' => $data->persen_mf,
                    'nominal_mf' => $data->nominal_mf,
                    'persen_bpjs_tk' => $data->persen_bpjs_tk,
                    'nominal_bpjs_tk' => $data->nominal_bpjs_tk,
                    'persen_bpjs_ks' => $data->persen_bpjs_ks,
                    'nominal_bpjs_ks' => $data->nominal_bpjs_ks,
                    'as_tk' => $data->as_tk,
                    'as_ks' => $data->as_ks,
                    'ohc' => $data->ohc,
                    'thr_provisi' => $data->thr_provisi,
                    'thr_ditagihkan' => $data->thr_ditagihkan,
                    'penagihan_selisih_thr' => $data->penagihan_selisih_thr,
                    'kaporlap' => $data->kaporlap,
                    'device' => $data->device,
                    'chemical' => $data->chemical,
                    'training' => $data->training,
                    'biaya_training' => $data->biaya_training,
                    'tgl_kirim_invoice' => $data->tgl_kirim_invoice,
                    'jumlah_hari_top' => $data->jumlah_hari_top,
                    'tipe_hari_top' => $data->tipe_hari_top,
                    'tgl_gaji' => $data->tgl_gaji,
                    'pic_1' => $data->pic_1,
                    'jabatan_pic_1' => $data->jabatan_pic_1,
                    'email_pic_1' => $data->email_pic_1,
                    'telp_pic_1' => $data->telp_pic_1,
                    'pic_2' => $data->pic_2,
                    'jabatan_pic_2' => $data->jabatan_pic_2,
                    'email_pic_2' => $data->email_pic_2,
                    'telp_pic_2' => $data->telp_pic_2,
                    'pic_3' => $data->pic_3,
                    'jabatan_pic_3' => $data->jabatan_pic_3,
                    'email_pic_3' => $data->email_pic_3,
                    'telp_pic_3' => $data->telp_pic_3,
                    'kategori_sesuai_hc_id' => $data->kategori_sesuai_hc_id,
                    'kategori_sesuai_hc' => $data->kategori_sesuai_hc,
                    'is_aktif' => $data->is_aktif,
                    'created_at' => $data->created_at,
                    'created_by' => $data->created_by,
                ]);
            }

            $msgSave = 'Import Monitoring Kontrak berhasil Dilakukan !';

            DB::commit();
            return redirect()->route('monitoring-kontrak')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
}

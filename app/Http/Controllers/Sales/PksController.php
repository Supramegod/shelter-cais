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
use \stdClass;
use App\Exports\LeadsTemplateExport;
use App\Exports\LeadsExport;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Helper\QuotationService;
use App\Http\Controllers\Sales\CustomerActivityController;

class PksController extends Controller
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
        $company = DB::connection('mysqlhris')->table('m_company')->where('is_active',1)->get();
        $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();

        $error = null;
        $success = null;
        if($ctglDari->gt($ctglSampai)){
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        };
        if($ctglSampai->lt($ctglDari)){
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }
        return view('sales.pks.list',compact('branch','tglDari','tglSampai','request','error','success','company','kebutuhan'));
    }

    public function add (Request $request){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');

            $data=null;
            $spk =null;
            if($request->id!=null){
                $spk = DB::table('sl_spk')->whereNull('deleted_at')->where('id',$request->id)->first();
            }
            return view('sales.pks.add',compact('now','spk'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function list (Request $request){
        $data = DB::table('sl_pks')
                ->leftJoin('sl_spk','sl_spk.id','sl_pks.spk_id')
                ->leftJoin('sl_quotation','sl_pks.quotation_id','sl_quotation.id')
                ->whereNull('sl_pks.deleted_at')
                ->whereNull('sl_spk.deleted_at')
                ->select('sl_pks.created_by','sl_pks.created_at','sl_pks.id','sl_pks.nomor','sl_spk.nomor as nomor_spk','sl_pks.tgl_pks','sl_quotation.nama_perusahaan','sl_quotation.kebutuhan','sl_pks.status_pks_id')
                ->get();

        foreach ($data as $key => $value) {
            $value->tgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$value->tgl_pks)->isoFormat('D MMMM Y');
            $value->created_at = Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->isoFormat('D MMMM Y');
            $value->status = DB::table('m_status_pks')->where('id',$value->status_pks_id)->first()->nama;
        }

        return DataTables::of($data)
        ->addColumn('aksi', function ($data) {
            return '<div class="justify-content-center d-flex">
                                <a href="'.route('pks.view',$data->id).'" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                    </div>';
        })
        ->editColumn('nomor', function ($data) {
            return '<a href="'.route('pks.view',$data->id).'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';
        })
        ->rawColumns(['aksi','nomor'])
        ->make(true);
    }

    public function availableSpk (Request $request){
        try {
            $data = DB::table('sl_spk')
                ->leftJoin('sl_leads','sl_leads.id','sl_spk.leads_id')
                ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                ->where('m_tim_sales_d.user_id',Auth::user()->id)
                ->whereNull('sl_spk.deleted_at')
                ->where('sl_spk.status_spk_id',2)
                ->select("sl_spk.id","sl_spk.nomor","sl_spk.nama_perusahaan","sl_spk.tgl_spk","sl_spk.kebutuhan")
                ->get();
            
            return DataTables::of($data)
            ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $dataSpk = DB::table('sl_spk')->whereNull('deleted_at')->where('id',$request->spk_id)->first();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$dataSpk->quotation_id)->first();
            $leads = DB::table('sl_leads')->where('id',$dataSpk->leads_id)->first();
            $quotation = DB::table('sl_quotation')->where('id',$dataSpk->quotation_id)->first();

            $pksNomor = $this->generateNomor($quotation->leads_id,$quotation->company_id);
            $newId = DB::table('sl_pks')->insertGetId([
                'quotation_id' => $dataSpk->quotation_id,
                'spk_id' => $dataSpk->id,
                'leads_id' => $dataSpk->leads_id,
                'nomor' => $pksNomor,
                'tgl_pks' => $current_date_time,
                'nama_perusahaan' => $dataSpk->nama_perusahaan,
                'link_pks_disetujui' => null,
                'status_pks_id' => 5,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('id',$quotation->id)->update([
                'status_quotation_id' => 5,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            
            DB::table('sl_spk')->where('id',$dataSpk->id)->update([
                'status_spk_id' => 3,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            //insert perjanjian
            $pembukaan = '<p class="NoSpacing1" align="center" style="margin-bottom:6.0pt;text-align:center"><b><span lang="EN-US" style="font-size:14.0pt;
font-family:&quot;Arial&quot;,sans-serif">PERJANJIAN KERJASAMA ALIH DAYA<o:p></o:p></span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="EN-US" style="font-size:14.0pt;font-family:&quot;Arial&quot;,sans-serif">ANTARA<o:p></o:p></span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="EN-US" style="font-size:14.0pt;font-family:&quot;Arial&quot;,sans-serif">'.$quotation->nama_perusahaan.'</span></b><b><span lang="IN" style="font-size:14.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN"><o:p></o:p></span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="EN-US" style="font-size:14.0pt;font-family:&quot;Arial&quot;,sans-serif">DENGAN<o:p></o:p></span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="EN-US" style="font-size:14.0pt;font-family:&quot;Arial&quot;,sans-serif">'.$quotation->company.'<o:p></o:p></span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">No:
'.$pksNomor.'</span></b><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN"><o:p></o:p></span></b></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">&nbsp;</span></b></p>

<p class="MsoNoSpacing" style="text-align:justify;text-justify:inter-ideograph"><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN">Pada Hari ini, Jumat Tanggal Sembilan Belas Bulan Juli Tahun Dua Ribu Dua Puluh Empat (19-7-2024), telah disepakati Perjanjian Kerjasama Alih Daya:<o:p></o:p></span></b></p>

<p class="NoSpacing1"><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">&nbsp;</span></p>

<p class="NoSpacing1" align="center" style="text-align:center"><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">Antara:<o:p></o:p></span></b></p>

<p class="NoSpacing1"><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">&nbsp;</span></b></p>

<p class="MsoNoSpacing" style="margin-left:211.5pt;text-align:justify;text-justify:
inter-ideograph;text-indent:-211.5pt;tab-stops:202.5pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">'.$quotation->nama_perusahaan.'</span></b><b><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></b><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">:<b>&nbsp; </b>Dalam hal ini diwakili oleh </span><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">YAKOBUS
JIMMY SAPUTRO </span></b><span lang="IN" style="font-size:12.0pt;font-family:
&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">sebagai</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"> </span><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Direktur </span></b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN">yang berkedudukan di <span style="background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;">Jala</span></span><span lang="EN-US" style="font-size: 12pt; font-family: Arial, sans-serif; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;">n </span><span lang="EN-US" style="font-size: 12pt; font-family: Arial, sans-serif; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;">Pos
No. 2, Ps. Baru, Kecamatan Sawah Besar, Kota Jakarta Pusat, Daerah Khusus Ibukota
Jakarta 10710 </span><span lang="IN" style="font-size: 12pt; font-family: Arial, sans-serif; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;">dan b</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">ertindak
untuk dan atas nama <b>'.$quotation->nama_perusahaan.'</b>, untuk selanjutnya dalam
perjanjian ini disebut sebagai <b>PIHAK PERTAMA</b>.<span style="background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><o:p></o:p></span></span></p>

<p class="MsoNoSpacing" style="margin-left:247.5pt;text-indent:-247.5pt"><span lang="IN" style="font-size: 12pt; font-family: Arial, sans-serif; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;">&nbsp;</span></p>

<p class="MsoNoSpacing" align="center" style="text-align:center"><b><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">dan<o:p></o:p></span></b></p>

<p class="MsoNoSpacing" style="text-align:justify;text-justify:inter-ideograph"><b><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>

<p class="MsoNoSpacing" style="margin-left:211.5pt;text-align:justify;text-justify:
inter-ideograph;text-indent:-211.5pt;tab-stops:202.5pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">'.$quotation->company.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN;mso-bidi-font-weight:bold">:</span><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp; </span></b><span lang="EN-US" style="font-size:
12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">Dalam hal ini
diwakili oleh </span><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">MARIN RISTANTI </span></b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:
bold">sebagai </span><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Direktur </span></b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN">yang</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">
</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">berkedudukan</span><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif"> </span><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">di Jalan</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"> </span><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Jatiluhur
Raya No. 206E, Kel. Ngesrep, Kec. Banyumanik, Kota Semarang. dan bertindak
untuk dan atas nama</span><span lang="EN-US" style="font-size:12.0pt;font-family:
&quot;Arial&quot;,sans-serif;mso-ansi-language:IN"> </span><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">'.$quotation->company.'</span></b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">,</span><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif"> </span><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">untuk</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"> </span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">selanjutnya</span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"> </span><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN">di dalam perjanjian ini disebut sebagai <b>PIHAK</b></span><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"> </span></b><b><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">KEDUA</span></b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
IN;mso-bidi-font-weight:bold">.</span><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN"><o:p></o:p></span></p>

<p class="MsoNormal" style="text-align:justify;text-justify:inter-ideograph;
tab-stops:117.0pt 144.0pt"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>

<p class="MsoNormal" style="text-align:justify;text-justify:inter-ideograph"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK PERTAMA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">dan
<b>PIHAK KEDUA </b>selanjutnya secara bersama-sama akan disebut sebagai <b>PARA
PIHAK</b></span><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">.<o:p></o:p></span></p>

<p class="MsoNormal" style="margin-bottom:6.0pt;text-align:justify;text-justify:
inter-ideograph;tab-stops:117.0pt"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">Sebelumnya<b> PIHAK PERTAMA </b>dan<b> PIHAK KEDUA </b>menerangkan
hal-hal sebagai berikut:<o:p></o:p></span></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:36.0pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:
Arial">1)<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span></b><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif">Bahwa <b>PIHAK
PERTAMA</b> adalah perseroan terbatas yang bergerak dalam bidang pemberdayaan
bisnis UKM dan UMKM serta ruang hiburan, seni, budaya dan pertemuan komunitas
kreatif;<b><o:p></o:p></b></span></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:36.0pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">2)<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Bahwa <b>PIHAK KEDUA</b> adalah badan usaha yang
secara hukum diizinkan menjalankan usaha Perusahaan Alih Daya dan sanggup
memenuhi kebutuhan <b>PIHAK PERTAMA</b>;<o:p></o:p></span></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:36.0pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">3)<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Bahwa <b>PIHAK
PERTAMA </b>membutuhkan Jasa '.$quotation->kebutuhan.' dan oleh karena itu <b>PIHAK
PERTAMA </b>menunjuk <b>PIHAK KEDUA</b>;<o:p></o:p></span></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:36.0pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">4)<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Bahwa <b>PIHAK
KEDUA </b>dengan ini bersedia untuk melaksanakan penyediaan Jasa '.$quotation->kebutuhan.' sesuai yang disepakati dengan <b>PIHAK PERTAMA</b>;<o:p></o:p></span></p>

<p class="MsoListParagraph" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-pagination:widow-orphan;
mso-list:l0 level1 lfo1;mso-layout-grid-align:auto;text-autospace:ideograph-numeric ideograph-other"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">5)<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Bahwa apabila terjadi
pergantian perusahaan penyedia jasa alih daya dan jenis pekerjaan masih tetap
ada, maka semua Tenaga Kerja yang masih ada akan beralih kepada perusahaan
penyedia jasa alih daya selanjutnya dengan berdasarkan hasil evaluasi kinerja Tenaga
Kerja yang telah ditetapkan dan disepakati;<o:p></o:p></span></p>

<p class="MsoListParagraph" style="text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-pagination:widow-orphan;mso-list:l0 level1 lfo1;
mso-layout-grid-align:auto;text-autospace:ideograph-numeric ideograph-other"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">6)<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family: Arial, sans-serif;">Bahwa apabila
terjadi perubahan terhadap Upah Minimum Kota/Kabupaten (<b>UMK</b>) yang ditetapkan oleh Gubernur melalui <b>PERATURAN GUBERNUR</b> maka <b>PARA
PIHAK </b>sepakat untuk musyawarah terlebih dahulu.</span><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></p>

<p class="NoSpacing1" style="text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></p>

<p class="NoSpacing1" style="text-align:justify;text-justify:inter-ideograph;
tab-stops:0cm"><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Berdasarkan
hal - hal tersebut di atas <b>PARA PIHAK</b> sepakat untuk mengadakan
Perjanjian dengan ketentuan sebagai berikut:<o:p></o:p></span></p><p></p>';
            $pasal1 = '<p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 1<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">RUANG LINGKUP
PEKERJAAN<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" style="margin-left:0cm;mso-add-space:auto;
tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-left:0cm;text-align:justify;text-justify:
inter-ideograph;tab-stops:0cm">





</p><p class="ListParagraph1" style="margin-left:0cm;text-align:justify;text-justify:
inter-ideograph;tab-stops:0cm"><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA </span></b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;
font-family:&quot;Arial&quot;,sans-serif">menunjuk<b>
PIHAK KEDUA </b>sebagai jasa penyedia dan pengelola Tenaga Kerja alih daya<b> </b>(<i>outsourcing</i>)<b> </b>Jasa '.$quotation->kebutuhan.' untuk <b>PIHAK
PERTAMA </b>yang akan ditempatkan di jalan
Kebon Rojo, Krembangan Sel., Kec. Krembangan, Surabaya, Jawa Timur 60175 dan
atas pelaksanaan pekerjaan tersebut <b>PIHAK
PERTAMA </b>akan membayarkan <i>Management Fee</i> kepada <b>PIHAK KEDUA</b>.<o:p></o:p></span></p>';
            $pasal2 = '<p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 2<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">HAK &amp;
KEWAJIBAN PARA PIHAK<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" style="margin-left:0cm;mso-add-space:auto;
tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-indent:-18.0pt;mso-list:l0 level1 lfo2"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">KEWAJIBAN
PIHAK PERTAMA:<o:p></o:p></span></b></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l2 level1 lfo1;
tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">a.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">Memberikan
segala informasi terkait dengan pelaksanaan pekerjaan yang tidak terbatas pada
syarat - syarat dilaksanakannya pekerjaan termasuk mematuhi segala perintah, instruksi
termasuk peraturan dan atau ketentuan yang diberlakukan oleh </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">, sepanjang tidak bertentangan dengan isi perjanjian
ini, ketertiban kesusilaan,dan atau peraturan dibidang ketenagakerjaan pada
pekerja dari </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">;</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></b></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l2 level1 lfo1;
tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">b.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">Atas
Pelaksanaan pekerjaan yang diberikan oleh </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK PERTAMA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold"> kepada
</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">maka akan
diterbitkan <i>invoice</i> pembayaran oleh </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">
dan menjadi kewajiban </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK PERTAMA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">
untuk melakukan pembayaran sesuai dengan waktu yang sudah disepakati;</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></b></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l2 level1 lfo1;
tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">c.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">
berhak meminta/mengajukan penggantian Tenaga Kerja <b>PIHAK KEDUA</b> yang dinilai atau dianggap tidak produktif dalam
performa bertugas berdasarkan hasil penilaian dan evaluasi yang ditetapkan pada
SOP; <b><o:p></o:p></b></span></p><p class="ListParagraph1CxSpMiddle" style="text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l2 level1 lfo1;tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">d.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Mematuhi
segala peraturan perundang-undangan terkait dengan perjanjian kerjasama alih
daya dan peraturan lain terkait ketenagakerjaan yang berlaku.<b><o:p></o:p></b></span></p><p class="ListParagraph1CxSpMiddle" style="margin-left:0cm;mso-add-space:auto"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1CxSpMiddle" style="margin-left:0cm;mso-add-space:auto"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1CxSpMiddle" style="margin-left:0cm;mso-add-space:auto"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-indent:-18.0pt;mso-list:l0 level1 lfo2"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">KEWAJIBAN
PIHAK KEDUA:<o:p></o:p></span></b></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial;
mso-bidi-font-weight:bold">a.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Menyediakan Tenaga Kerja
berdasarkan permintaan secara tertulis dari <b>PIHAK PERTAMA </b>kepada <b>PIHAK
KEDUA</b> yang berisikan surat penunjukan kesepakatan kerjasama, jenis
pekerjaan, dan jumlah Tenaga Kerja yang dibutuhkan;<b><o:p></o:p></b></span></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial;
mso-bidi-font-weight:bold">b.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Menjaga kerahasiaan <b>PIHAK PERTAMA</b> tidak terbatas pada semua
keterangan, data – data, catatan – catatan yang diperoleh baik langsung maupun
tidak langsung, kepada pihak lain tanpa izin tertulis dari <b>PIHAK PERTAMA</b> baik selama berlakunya Perjanjian ini maupun sesudah
Perjanjian ini berakhir. Untuk keperluan ini <b>PIHAK KEDUA</b> wajib memasikan bahwa Tenaga Kerja telah menandatangani
Surat Pernyataan untuk menjaga kerahasiaan <b>PIHAK
PERTAMA</b>;<b><o:p></o:p></b></span></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial;
mso-bidi-font-weight:bold">c.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">Membebaskan
</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK PERTAMA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">dari
segala tuntutan ketenagakerjaan dari pekerja </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">akibat
timbulnya dari perjanjian ini;</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></b></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial;
mso-bidi-font-weight:bold">d.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK
KEDUA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">akan menugaskan
Tenaga Kerja di lokasi <b>PIHAK PERTAMA</b>
dengan jadwal kerja yang telah disepakati oleh <b>PARA PIHAK</b>;<o:p></o:p></span></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial;
mso-bidi-font-weight:bold">e.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">Menyelesaikan
secara tuntas segala permasalahan yang timbul baik dalam hubungan dengan Tenaga
Kerja atau pihak lain terkait dengan pelaksanaan perjanjian ini, termasuk
memberikan sanksi secara tegas atas tindakan pelanggaran atau penyelewengan
dari Tenaga Kerja </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">terhadap
tata tertib dan segala peraturan yang berlaku di lokasi </span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA</span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">;</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></b></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3;
tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial;mso-bidi-font-weight:bold">f.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><!--[endif]--><span lang="FI" style="font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:FI">Apabila ada Tenaga Kerja yang melakukan pelanggaran
indisipliner maupun pelanggaran lain terhadap SOP, maka <b>PIHAK KEDUA</b>
wajib melakukan pembinaan dan konseling terhadap Tenaga Kerja, jika tidak
terjadi perubahan dan perbaikan pada Tenaga Kerja, maka <b>PIHAK KEDUA</b>
wajib melakukan pengajuan pergantian/penarikan Tenaga Kerja, dengan persetujuan
<b>PIHAK PERTAMA</b>;</span><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3;
tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial;mso-bidi-font-weight:bold">g.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span><!--[endif]--><b><span lang="ES" style="font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:ES">PIHAK KEDUA</span></b><span lang="ES" style="font-family:
&quot;Arial&quot;,sans-serif;mso-ansi-language:ES"> </span><span lang="FI" style="font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:FI">wajib segera
mengirimkan </span><span lang="IN" style="font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">Tenaga Kerja</span><span lang="IN" style="font-family:&quot;Arial&quot;,sans-serif">
</span><span lang="FI" style="font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:
FI">pengganti sementara jika terjadi <i>turn over</i> dalam waktu 1x24 jam
untuk memastikan tidak terjadi kekosongan, selanjutnya untuk proses rekrutmen
kandidat dan sampai adanya keputusan penerimaan yakni selambat-lambatnya 4
(empat) hari kerja sejak terjadinya <i>turn over</i> dan disepakati oleh <b>PARA
PIHAK</b>;</span><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l1 level1 lfo3;
tab-stops:0cm"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial;mso-bidi-font-weight:bold">h.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila
Tenaga Kerja <b>PIHAK KEDUA</b> melakukan pelanggaran yang mengakibatkan
kerugian pada <b>PIHAK PERTAMA</b> baik secara materil maupun immateril, maka
akan dilakukan investigasi dan musyawarah mufakat untuk menentukan nilai
kerugian yang harus ditanggung oleh <b>PIHAK KEDUA </b>sesuai dengan hasil
investigasi dan nilai kesepakatan <b>PARA PIHAK</b>;<o:p></o:p></span></p><p class="ListParagraph1" style="text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l1 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial;
mso-bidi-font-weight:bold">i.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Menghitung dan membayar
gaji/upah pokok beserta <i>variable</i> dan pembayaran lainnya (apabila ada)
atas setiap Tenaga Kerja yang dikaryakan di <b>PIHAK PERTAMA</b>.<o:p></o:p></span></p><p class="ListParagraph1" style="margin-left:0cm;text-align:justify;text-justify:
inter-ideograph"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo2"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">HAK
PIHAK PERTAMA:<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" style="margin-left:18.0pt;mso-add-space:auto;
text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Menerima Tenaga Kerja dari <b>PIHAK
KEDUA </b>sesuai dengan syarat-syarat kualifikasi, kompetensi, kualitas dan
pencapaian hasil yang telah ditentukan oleh<b> PIHAK PERTAMA</b>.<o:p></o:p></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo2"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">4.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">HAK
PIHAK KEDUA:<o:p></o:p></span></b></p><p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm">

















































</p><p class="ListParagraph1" style="margin-left:18.0pt;text-align:justify;
text-justify:inter-ideograph"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Menerima
pembayaran atau <i>management fee</i> atas pekerjaan yang telah dilaksanakan <b>PIHAK KEDUA</b> dari <b>PIHAK PERTAMA </b>dengan nominal/nilai yang telah disepakati.<b><o:p></o:p></b></span></p>';
            $pasal3 = '<p class="MsoNoSpacing" align="center" style="text-align:center;tab-stops:22.5pt"><b><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif">Pasal</span></b><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN"> </span></b><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">3<o:p></o:p></span></b></p><p class="MsoNoSpacing" align="center" style="text-align:center;tab-stops:22.5pt"><b><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN">TUNJANGAN HARI RAYA<o:p></o:p></span></b></p><p class="MsoNoSpacing"><b><span lang="IN" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA </span></b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold">akan membayarkan Tunjangan
Hari Raya Keagamaan selanjutnya disebut </span><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif">THR
</span></b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">kepada Tenaga Kerja </span><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA</span></b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:
bold"> yang ditempatkan di </span><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA</span></b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:
&quot;Arial&quot;,sans-serif;mso-bidi-font-weight:bold"> sesuai dengan ketentuan
peraturan yang berlaku;<o:p></o:p></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">Bahwa <b>PIHAK KEDUA </b>akan menagihkan komponen
THR pada <b>PIHAK PERTAMA</b> dengan perhitungan berdasarkan upah pokok sesuai
dengan peraturan menteri Tenaga Kerja (<b>Permenaker</b>) no.6 tahun
2016/tentang tunjangan hari raya keagamaan bagi pekerja/buruh di perusahaan;<o:p></o:p></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1">









</p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">THR</span><b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif"> </span></b><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">Keagamaan pada saat Hari Raya Idul Fitri sebesar 1
(satu) kali gaji kepada Tenaga Kerja yang memiliki masa kerja selama 12 (dua
belas) bulan berturut-turut. Apabila masa kerja Tenaga Kerja belum mencapai 1
(satu) tahun namun telah melebihi 1 (satu) bulan maka akan diberikan THR secara
Prorata, dengan skema penagihan <i>invoice</i> sesuai tabel dibawah.</span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold"><br></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold">&nbsp; &nbsp;&nbsp;</span><img src="data:image/png;base64,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" data-filename="image.png" style="width: 549.004px;"><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-bidi-font-weight:bold"><o:p></o:p></span></p>';
            $pasal4 = '<p class="MsoNormal" align="center" style="text-align:center"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 4<o:p></o:p></span></b></p>

<p class="MsoNormal" align="center" style="text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">BIAYA
DAN PEMBAYARAN<o:p></o:p></span></b></p>

<p class="MsoNormal" align="center" style="text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-18.0pt;mso-list:l0 level1 lfo2"><!--[if !supportLists]--><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif">BIAYA KONTRAK<o:p></o:p></span></b></p>

<p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:31.5pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-13.5pt;mso-pagination:widow-orphan;mso-list:l1 level1 lfo1;mso-layout-grid-align:
auto;text-autospace:ideograph-numeric ideograph-other"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">a.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp; </span></span><!--[endif]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PIHAK
PERTAMA </span></b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">sepakat
untuk membayar <b>Biaya Kontrak</b> setiap
bulan (menyesuaikan <i>actual</i> <i>invoice</i>) kepada <b>PIHAK KEDUA </b>untuk pekerjaan alih daya '.$quotation->kebutuhan.' yang
dikaryakan di tempat <b>PIHAK PERTAMA</b>;<o:p></o:p></span></p>

<p class="MsoNormal" style="margin-left:31.7pt;text-align:justify;text-justify:
inter-ideograph;text-indent:-13.7pt;mso-pagination:widow-orphan;mso-list:l1 level1 lfo1;
mso-layout-grid-align:auto;text-autospace:ideograph-numeric ideograph-other"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">b.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp; </span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Biaya kontrak sudah termasuk
upah pokok beserta <i>variable</i> <i>breakdown</i> lainnya, seperti tunjangan, premi BPJS Ketenagakerjaan, BPJS
Kesehatan, biaya monitoring dan kontrol, biaya provisi seragam, biaya provisi
chemical dan tools, ppn 11%, pph -2% dan <i>managemen</i><i>t fee</i> yang telah disepakati.<o:p></o:p></span></p>

<p class="MsoNormal" style="margin-left:31.7pt;text-align:justify;text-justify:
inter-ideograph;mso-pagination:widow-orphan;mso-layout-grid-align:auto;
text-autospace:ideograph-numeric ideograph-other"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-18.0pt;mso-list:l0 level1 lfo2"><!--[if !supportLists]--><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><i><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif">INVOICE</span></i></b><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">/PENAGIHAN</span></b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></p>

<p class="NoSpacing1" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:31.5pt;text-align:justify;text-justify:inter-ideograph;text-indent:
-13.5pt;mso-list:l2 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:
Arial">a.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;
</span></span><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif">Jadwal Penagihan &amp; Pembayaran</span><span lang="EN-US" style="font-size:14.0pt;mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></p>

<p class="LO-normal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:31.7pt;margin-bottom:.0001pt;text-align:justify;text-justify:inter-ideograph;
text-indent:0cm"><b><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA</span></b><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">
menerbitkan tagihan/<i>invoice</i> kepada <b>PIHAK PERTAMA</b> dengan perhitungan <i>cut-off</i> 21 bulan sebelumnya 20 bulan
selanjutnya dan rilis penggajian Tenaga Kerja pada tanggal 1 bulan berikutnya
dengan skema tabel dibawah ini:</span></p><p class="LO-normal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:31.7pt;margin-bottom:.0001pt;text-align:justify;text-justify:inter-ideograph;
text-indent:0cm"><img src="data:image/png;base64,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" data-filename="image.png" style="width: 641.992px;"><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif"><br></span></p>

<p class="LO-normal" style="margin-top:0cm;margin-right:-.05pt;margin-bottom:
6.0pt;margin-left:31.5pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-13.5pt;mso-list:l2 level1 lfo3"><!--[if !supportLists]--><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">b.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp; </span></span><!--[endif]--><b><span lang="EN-US" style="mso-bidi-font-size:
12.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK PERTAMA</span></b><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">
melakukan pembayaran terhitung maksimal 14 hari kalender setelah menerima <i>invoice </i>asli bermaterai dari<b> PIHAK KE</b></span><b><span lang="IN" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:IN">DU</span></b><b><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">A </span></b><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">melalui
transfer bank ke:<o:p></o:p></span></p>

<p class="MsoNormal" style="text-indent: 32.4pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><span lang="NL" style="font-family: Arial, sans-serif;">Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :
<b>MANDIRI</b></span><span lang="NL" style="font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:NL"><o:p></o:p></span></p>

<p class="MsoNormal" style="text-indent: 32.4pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><span lang="NL" style="font-family: Arial, sans-serif;">Cabang Pembantu &nbsp;&nbsp; :
<b>KCP SURABAYA RUNGKUT MEGAH RAYA</b></span><span lang="NL" style="font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:NL"><o:p></o:p></span></p>

<p class="MsoNormal" style="text-indent: 32.4pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><span lang="NL" style="font-family: Arial, sans-serif;">Nomor Rekening&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :
<b>1420001290823</b></span><span lang="NL" style="font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:NL"><o:p></o:p></span></p>

<p class="MsoNormal" style="margin-bottom: 6pt; text-indent: 32.4pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><span lang="IN" style="font-family: Arial, sans-serif;">Nama Rekening</span><span lang="NL" style="font-family: Arial, sans-serif;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b>'.$quotation->company.'</b></span><b><span lang="NL" style="font-family:&quot;Arial&quot;,sans-serif;
mso-ansi-language:NL"><o:p></o:p></span></b></p>

<p class="LO-normal" style="margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;
margin-left:0cm;text-align:justify;text-justify:inter-ideograph;text-indent:
0cm"><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Jika
tanggal tempo pembayaran jatuh pada hari Sabtu, Minggu atau hari Libur
Nasional, maka <b>PIHAK PERTAMA</b> dapat
melakukan pembayaran pada hari efektif berikutnya;<o:p></o:p></span></p>

<p class="LO-normal" style="margin-top:0cm;margin-right:1.45pt;margin-bottom:
0cm;margin-left:0cm;margin-bottom:.0001pt;text-align:justify;text-justify:inter-ideograph;
text-indent:0cm"><span lang="EN-US" style="mso-bidi-font-size:12.0pt;font-family:
&quot;Arial&quot;,sans-serif">Dalam hal terjadi perubahan data rekening, <b>PIHAK KEDUA</b> wajib segera memberitahukan
secara tertulis kepada <b>PIHAK PERTAMA</b>
sebelum jadwal pembayaran berikutnya. Kelalaian <b>PIHAK KEDUA</b> dalam menyampaikan pemberitahuan tersebut tidak akan
menimbulkan akibat atau kompensasi apapun terhadap <b>PIHAK PERTAMA</b>.<o:p></o:p></span></p>';
            $pasal5 = '<p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 5<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">JAMINAN PIHAK
KEDUA<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:26.25pt;
mso-add-space:auto;text-align:center;text-indent:-26.25pt;mso-char-indent-count:
-2.18;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:0cm;text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pekerja yang ditempatkan oleh
<b>PIHAK KEDUA</b> telah melalui proses:<b><o:p></o:p></b></span></p><p class="ListParagraph1" style="margin-bottom:6.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">a.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Wawancara
dalam proses seleksi dan penerimaan sesuai dengan kualifikasi yang dibutukan;<o:p></o:p></span></p><p class="ListParagraph1" style="text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">b.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pemeriksaan
dokumen calon Tenaga Kerja/kandidat mencakup identitas diri/<i>CV</i> (termasuk foto), ijazah atau sertifikat
pendidikan formal maupun non formal, sertifikat pelatihan, surat
referensi/pengalaman kerja, dll.<o:p></o:p></span></p><p class="ListParagraph1" style="margin-left:0cm;text-align:justify;text-justify:
inter-ideograph"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:0cm;text-align:justify;text-justify:inter-ideograph">













</p><p class="ListParagraph1CxSpLast" style="margin-left:0cm;mso-add-space:auto;
text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Bahwa Tenaga Kerja yang ditempatkan pada
<b>PIHAK PERTAMA </b>tunduk kepada
peraturan <b>PIHAK PERTAMA</b> dan <b>PIHAK KEDUA</b>. Jika terjadi pelanggaran
atas peraturan internal <b>PIHAK PERTAMA</b>
maka <b>PIHAK PERTAMA</b> wajib
memberitahukan kepada <b>PIHAK KEDUA</b>
untuk penindakan secara tertulis melalui Surat Peringatan (<b>SP</b>) tahap pertama sampai dengan tahap ketiga beserta pengambilan
sanksi tindakan sebagaimana mestinya sesuai dengan SOP &amp; peraturan
perundang - undangan yang berlaku.<o:p></o:p></span></p>';
            $pasal6 = '<p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Yang dimaksud dengan <i>force majeure</i> adalah keadaan yang tidak
dapat dipenuhinya pelaksanaan Perjanjian oleh <b>PARA PIHAK</b>, karena terjadi
suatu peristiwa yang bukan karena kesalahan Para Pihak, peristiwa mana tidak
dapat diketahui/ tidak dapat diduga sebelumnya dan di luar kemampuan manusia, seperti
bencana alam (gempa bumi, angin topan, kebakaran, banjir), huru-hara, perang,
pemogokan umum yang bukan kesalahan <b>PARA PIHAK</b>, <i>sabotase</i>, pemberontakan, dan <i>epidemi</i>
yang secara keseluruhan ada hubungan langsung dengan penyelesaian pelaksanaan
Perjanjian ini;<b><o:p></o:p></b></span></p>

<p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila terjadi <i>force majeure</i>, maka Pihak yang terkena <i>force majeure</i> harus memberitahukan
secara tertulis kepada Pihak yang tidak terkena <i>force majeure</i> selambat-lambatnya 7 (tujuh) hari kalender sejak
terjadinya <i>force majeure</i> tersebut
disertai bukti-bukti yang sah, selanjutnya Pihak yang tidak terkena <i>force majeure</i> akan menanggapi;<b><o:p></o:p></b></span></p>

<p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila hal tersebut tidak
dilakukan oleh Pihak yang terkena <i>force
majeure</i>, maka Pihak yang tidak terkena <i>force
majeure</i> menganggap tidak terjadi <i>force
majeure</i>;<b>&nbsp;</b></span><span style="font-family: Arial, sans-serif; font-size: 12pt; background-color: var(--bs-card-bg); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);"><br></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><span style="font-family: Arial, sans-serif; font-size: 12pt; background-color: var(--bs-card-bg); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">4.&nbsp; Dalam hal terjadi </span><i style="font-family: Arial, sans-serif; font-size: 12pt; background-color: var(--bs-card-bg); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">force majeure</i><span style="font-family: Arial, sans-serif; font-size: 12pt; background-color: var(--bs-card-bg); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">, maka pelaksanaan kewajiban masing-masing Pihak akan
ditunda berdasarkan kesepakatan </span><b style="font-family: Arial, sans-serif; font-size: 12pt; background-color: var(--bs-card-bg); text-align: var(--bs-body-text-align);">PARA PIHAK</b><span style="font-family: Arial, sans-serif; font-size: 12pt; background-color: var(--bs-card-bg); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align);">.</span></p>';
            $pasal7 = '<p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 6<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">FORCE MAJEURE<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" style="margin-left:0cm;mso-add-space:auto;
text-align:justify;text-justify:inter-ideograph"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Yang dimaksud dengan <i>force majeure</i> adalah keadaan yang tidak
dapat dipenuhinya pelaksanaan Perjanjian oleh <b>PARA PIHAK</b>, karena terjadi
suatu peristiwa yang bukan karena kesalahan Para Pihak, peristiwa mana tidak
dapat diketahui/ tidak dapat diduga sebelumnya dan di luar kemampuan manusia, seperti
bencana alam (gempa bumi, angin topan, kebakaran, banjir), huru-hara, perang,
pemogokan umum yang bukan kesalahan <b>PARA PIHAK</b>, <i>sabotase</i>, pemberontakan, dan <i>epidemi</i>
yang secara keseluruhan ada hubungan langsung dengan penyelesaian pelaksanaan
Perjanjian ini;<b><o:p></o:p></b></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila terjadi <i>force majeure</i>, maka Pihak yang terkena <i>force majeure</i> harus memberitahukan
secara tertulis kepada Pihak yang tidak terkena <i>force majeure</i> selambat-lambatnya 7 (tujuh) hari kalender sejak
terjadinya <i>force majeure</i> tersebut
disertai bukti-bukti yang sah, selanjutnya Pihak yang tidak terkena <i>force majeure</i> akan menanggapi;<b><o:p></o:p></b></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila hal tersebut tidak
dilakukan oleh Pihak yang terkena <i>force
majeure</i>, maka Pihak yang tidak terkena <i>force
majeure</i> menganggap tidak terjadi <i>force
majeure</i>;<b><o:p></o:p></b></span></p><p class="MsoNoSpacing" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level2 lfo1">











</p><p class="ListParagraph1CxSpLast" style="margin-left:18.0pt;mso-add-space:auto;
text-align:justify;text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:
l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">4.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Dalam
hal terjadi <i>force majeure</i>, maka
pelaksanaan kewajiban masing-masing Pihak akan ditunda berdasarkan kesepakatan <b>PARA
PIHAK</b>.<b><o:p></o:p></b></span></p>';
            $pasal8 = '<p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 8<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PENYELESAIAN
PERSELISIHAN<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila dikemudian hari
terjadi perselisihan atau permasalahan antara kedua belah pihak, sehubungan
dengan pelaksanaan dan penafsiran perjanjian ini, maka <b>PARA PIHAK</b> setuju untuk menyelesaikan permasalahan atau
perselisihan dengan musyawarah untuk mufakat;<b><o:p></o:p></b></span></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila
cara penyelesaian musyawarah tersebut di atas gagal untuk mencapai kata
mufakat, maka <b>PARA PIHAK</b> setuju menunjuk Kantor Panitera Pengadilan
Negeri Surabaya untuk lembaga penyelesaian sengketa;<b><o:p></o:p></b></span></p><p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm">









</p><p class="ListParagraph1" style="margin-left:18.0pt;text-align:justify;
text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="mso-bidi-font-size:11.0pt;font-family:&quot;Arial&quot;,sans-serif">Selama
masa penyelesaian sengketa di pengadilan, <b>PARA PIHAK </b>tetap diwajibkan
untuk menjalankan masing-masing kewajibannya.</span><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif"><o:p></o:p></span></b></p>';
            $pasal9 = '<p class="ListParagraph1CxSpFirst" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Pasal 9<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">PENUTUP<o:p></o:p></span></b></p><p class="ListParagraph1CxSpMiddle" align="center" style="margin-left:0cm;
mso-add-space:auto;text-align:center;tab-stops:0cm"><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;
mso-fareast-font-family:Arial">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Apabila terdapat perubahan,
tambahan dan atau hal – hal lain yang belum cukup diatur dalam Perjanjian ini,
maka akan dibuat secara tertulis dan ditanda tangani oleh kedua belah pihak dan
merupakan bagian yang tidak terpisahkan dari Perjanjian ini;<o:p></o:p></span></p><p class="ListParagraph1CxSpLast" style="margin-left:18.0pt;mso-add-space:auto;
text-align:justify;text-justify:inter-ideograph;text-indent:-18.0pt;mso-list:
l0 level1 lfo1"><!--[if !supportLists]--><b><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif;mso-fareast-font-family:Arial">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-size-adjust: none; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-variant-position: normal; font-variant-emoji: normal; font-weight: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;
</span></span></b><!--[endif]--><span lang="EN-US" style="font-family:&quot;Arial&quot;,sans-serif">Perjanjian
ini dibuat rangkap dua (2), masing – masing bermaterai cukup dan memiliki
kekuatan hukum yang sama dan berlaku sejak ditandatangani oleh kedua belah
pihak.<o:p></o:p></span></p><p class="MsoNoSpacing"><span lang="EN-US" style="font-size:12.0pt;font-family:
&quot;Arial&quot;,sans-serif">&nbsp;</span></p><p class="MsoNoSpacing"><span lang="EN-US" style="font-size:12.0pt;font-family:
&quot;Arial&quot;,sans-serif">&nbsp;</span></p><table class="MsoNormalTable" border="1" cellspacing="0" cellpadding="0" width="618" style="margin-left: -4.5pt; border: none;">
 <tbody><tr>
  <td width="390" valign="top" style="width: 292.5pt; border-width: initial; border-style: none; border-color: initial; padding: 0cm 5.4pt;">
  <p class="NoSpacing1" style="margin-left:-.9pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK
  PERTAMA<o:p></o:p></span></b></p>
  <p class="NoSpacing1" style="margin-left:-.9pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">'.$quotation->nama_perusahaan.'<o:p></o:p></span></b></p>
  <p class="NoSpacing1" style="margin-left:-.9pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1" style="margin-left:-.9pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1" style="margin-left:-.9pt"><b><u><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">YAKOBUS JIMMY SAPUTRO<o:p></o:p></span></u></b></p>
  <p class="NoSpacing1" style="margin-left:-.9pt"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Direktur<o:p></o:p></span></b></p>
  </td>
  <td width="228" valign="top" style="width: 171pt; border-width: initial; border-style: none; border-color: initial; padding: 0cm 5.4pt;">
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">PIHAK KEDUA<o:p></o:p></span></b></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">'.$quotation->company.'<o:p></o:p></span></b></p>
  <p class="NoSpacing1"><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></p>
  <p class="NoSpacing1"><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></p>
  <p class="NoSpacing1"><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></p>
  <p class="NoSpacing1"><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></p>
  <p class="NoSpacing1"><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>
  <p class="NoSpacing1"><b><u><span lang="EN-US" style="font-size:12.0pt;
  font-family:&quot;Arial&quot;,sans-serif">MARIN RISTANTI<o:p></o:p></span></u></b></p>
  <p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:
  &quot;Arial&quot;,sans-serif">Direktur<u><o:p></o:p></u></span></b></p>
  </td>
 </tr>
</tbody></table><p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p><p class="ListParagraph1" style="margin-top:0cm;margin-right:0cm;margin-bottom:
6.0pt;margin-left:18.0pt;text-align:justify;text-justify:inter-ideograph;
text-indent:-18.0pt;mso-list:l0 level1 lfo1">



















</p><p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">&nbsp;</span></b></p>';
            $lampiran = '<p class="NoSpacing1"><b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Arial&quot;,sans-serif">Lampiran PKS No: '.$pksNomor.'</span></b><b><span lang="IN" style="font-size:12.0pt;
font-family:&quot;Arial&quot;,sans-serif;mso-ansi-language:IN"><o:p></o:p></span></b></p><p></p>';

            //insert ke pks kerjasama
            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pembukaan",
                'judul' => "Pembukaan",
                'raw_text' => $pembukaan,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 1",
                'judul' => "RUANG LINGKUP PEKERJAAN",
                'raw_text' => $pasal1,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 2",
                'judul' => "HAK & KEWAJIBAN PARA PIHAK",
                'raw_text' => $pasal2,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 3",
                'judul' => "TUNJANGAN HARI RAYA",
                'raw_text' => $pasal3,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 4",
                'judul' => "BIAYA DAN PEMBAYARAN",
                'raw_text' => $pasal4,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 5",
                'judul' => "JAMINAN PIHAK KEDUA",
                'raw_text' => $pasal5,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 6",
                'judul' => "FORCE MAJEURE",
                'raw_text' => $pasal6,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 7",
                'judul' => "JANGKA WAKTU PERJANJIAN",
                'raw_text' => $pasal7,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 8",
                'judul' => "PENYELESAIAN PERSELISIHAN",
                'raw_text' => $pasal8,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "Pasal 9",
                'judul' => "PENUTUP",
                'raw_text' => $pasal9,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::table('sl_pks_perjanjian')->insert([
                'pks_id' => $newId,
                'pasal' => "LAMPIRAN",
                'judul' => "Lampiran Lampiran PKS No: ".$pksNomor,
                'raw_text' => $lampiran,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);


            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($quotation->leads_id);

            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $quotation->leads_id,
                'quotation_id' => $quotation->id,
                'spk_id' => $dataSpk->id,
                'pks_id' => $newId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'PKS',
                'notes' => 'PKS dengan nomor :'.$pksNomor.' terbentuk dari SPK dengan nomor :'.$dataSpk->nomor,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            DB::commit();
            return redirect()->route('pks.view',$newId);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }
    
    public function generateNomor ($leadsId,$companyId){
        // generate nomor QUOT/SIG/AAABB-092024-00001
        $now = Carbon::now();

        $nomor = "PKS/";
        $dataLeads = DB::table('sl_leads')->where('id',$leadsId)->first();
        $company = DB::connection('mysqlhris')->table('m_company')->where('id',$companyId)->first();
        if($company != null){
            $nomor = $nomor.$company->code."/";
            $nomor = $nomor.$dataLeads->nomor."-";
        }else{
            $nomor = $nomor."NN/NNNNN-";
        }

        $month = $now->month;
        if($month<10){
            $month = "0".$month;
        }

        $urutan = "00001";

        $jumlahData = DB::select("select * from sl_pks where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }

    public function view (Request $request,$id){
        try {
            $data = DB::table('sl_pks')->whereNull('deleted_at')->where('id',$id)->first();
            $spk = DB::table('sl_spk')->whereNull('deleted_at')->where('id',$data->spk_id)->first();
            $quotation = DB::table('sl_quotation')->whereNull('deleted_at')->where('id',$spk->quotation_id)->whereNull('deleted_at')->first();

            $data->stgl_pks = Carbon::createFromFormat('Y-m-d H:i:s',$data->tgl_pks)->isoFormat('D MMMM Y');
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s',$data->created_at)->isoFormat('D MMMM Y');
            $data->status = DB::table('m_status_pks')->whereNull('deleted_at')->where('id',$data->status_pks_id)->first()->nama;
            $perjanjian = DB::table('sl_pks_perjanjian')->whereNull('deleted_at')->where('pks_id',$id)->whereNull('deleted_at')->get();

            return view('sales.pks.view',compact('perjanjian','quotation','spk','data'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function uploadPks (Request $request) {
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $originalFileName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = $originalFileName.date("YmdHis").rand(10000,99999).".".$fileExtension;

            Storage::disk('pks')->put($originalName, file_get_contents($request->file('file')));
            
            DB::table('sl_pks')->where('id',$request->id)->update([
                'status_pks_id' => 6,
                'link_pks_disetujui' =>env('APP_URL')."/public/pks/".$originalName,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        };
    }

    public function approve(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $ot = $request->ot;
            $id = $request->id;
            $status = 1;
            if($ot==1){
                $status = 2;
            }else if($ot==2){
                $status = 3;
            }else if($ot==3){
                $status = 4;
            }else if($ot==4){
                $status = 5;
            }

            $approve ="ot".$ot;
            DB::table('sl_pks')->where('id',$id)->update([
                $approve => Auth::user()->full_name,
                'status_pks_id' => $status,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);



        } catch (\Exception $e) {
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function aktifkanSite(Request $request){
        try {
            DB::beginTransaction();
            DB::connection('mysqlhris')->beginTransaction();
            
            $current_date_time = Carbon::now()->toDateTimeString();
            $pks = DB::table('sl_pks')->where('id',$request->id)->first();
            DB::table('sl_pks')->where('id',$request->id)->update([
                'ot5' => Auth::user()->full_name,
                'status_pks_id' => 7,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            DB::table('sl_quotation')->where('id',$pks->quotation_id)->update([
                'status_quotation_id' => 6,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);
            DB::table('sl_spk')->where('id',$pks->spk_id)->update([
                'status_spk_id' => 4,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);


            // dimasukkan ke customer dan site
            $quotation = DB::table('sl_quotation')->where('id',$pks->quotation_id)->whereNull('deleted_at')->first();
            $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();

            // cek leads dulu apakah ada pic_id_1,2,3 dan ro_id
            if($leads->ro_id==null || ( $leads->ro_id_1==null && $leads->ro_id_2==null && $leads->ro_id_3==null)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor dan RO Belum Diisi'
                ]);
            }else{
                if($leads->ro_id_1 == null){
                    $leads->ro_id_1 = 0;
                }
                if($leads->ro_id_2 == null){
                    $leads->ro_id_2 = 0;
                }
                if($leads->ro_id_3 == null){
                    $leads->ro_id_3 = 0;
                }
            }

            $custId = null;

            if($leads->customer_id!=null){
                $custId = $leads->customer_id;
            }else{ 
                $custId = DB::table('sl_customer')->insertGetId([
                    'leads_id' => $leads->id,
                    'nomor' =>  $leads->nomor,
                    'tgl_customer' => $current_date_time,
                    'nama_perusahaan' => $leads->nama_perusahaan,
                    'tim_sales_id' => $leads->tim_sales_id,
                    'tim_sales_d_id' => $leads->tim_sales_d_id,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                DB::table('sl_leads')->where('id',$leads->id)->update([
                    'customer_id' => $custId,
                    'status_leads_id' => 102,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->full_name
                ]);
            }

            // SINGKRON KE CLIENT HRIS
            $clientId = null;
            if($leads->customer_id!=null){
                $clientId = DB::connection('mysqlhris')->table('m_client')->where('customer_id',$custId)->first()->id;
            }else{ 
                $clientId = DB::connection('mysqlhris')->table('m_client')->insertGetId([
                    'customer_id' => $custId,
                    'name' => $leads->nama_perusahaan,
                    'address' => $leads->alamat,
                    'is_active' => 1,
                    'created_at' => $current_date_time, 
                    'created_by' => Auth::user()->id,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->id
                ]);
            }

            $quotationSite = DB::table('sl_quotation_site')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();

            foreach ($quotationSite as $ks => $site) {
                $siteId = DB::table('sl_site')->insertGetId([
                    'quotation_id' => $quotation->id,
                    'quotation_site_id' => $site->id,
                    'leads_id' =>  $leads->id,
                    'customer_id' => $custId,
                    'nama_site' => $site->nama_site,
                    'provinsi_id' => $site->provinsi_id,
                    'provinsi' => $site->provinsi,
                    'kota_id' => $site->kota_id,
                    'kota' => $site->kota,
                    'penempatan' => $site->penempatan,
                    'tim_sales_id' => $leads->tim_sales_id,
                    'tim_sales_d_id' => $leads->tim_sales_d_id,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name,
                ]);
    
                // SINGKRON KE SITE HRIS
                $siteHrisId = DB::connection('mysqlhris')->table('m_site')->insertGetId([
                    'site_id' => $siteId,
                    'code' => $leads->nomor,
                    'proyek_id' => 0, // ACCURATE
                    'contract_number' => $pks->nomor,
                    'name' => $site->nama_site,
                    'address' => $site->penempatan,
                    'layanan_id' => $quotation->kebutuhan_id,
                    'client_id' => $clientId,
                    'city_id' => $site->kota_id,
                    'branch_id' => $leads->branch_id,
                    'company_id' => $quotation->company_id,
                    'pic_id_1' => $leads->ro_id_1,
                    'pic_id_2' => $leads->ro_id_2,
                    'pic_id_3' => $leads->ro_id_3,
                    'supervisor_id' => $leads->ro_id,
                    'reliever' => $quotation->joker_reliever,
                    'contract_value' => 0,
                    'contract_start' => $quotation->mulai_kontrak,
                    'contract_end' => $quotation->kontrak_selesai,
                    'contract_terminated' => null,
                    'note_terminated' => '',
                    'contract_status' => 'Aktif',
                    'health_insurance_status' => 'Terdaftar',
                    'labor_insurance_status' => 'Terdaftar',
                    'vacation' => 0,
                    'attendance_machine' => '',
                    'is_active' => 1,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->id,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->id
                ]);
            }
            
            // BUAT VACANCY
            $detailQuotation = DB::table('sl_quotation_detail')->whereNull('deleted_at')->where('quotation_id',$quotation->id)->get();

            foreach ($detailQuotation as $kd => $d) {
                $icon = 1;
                if ($quotation->kebutuhan_id == 1) {
                    $icon = 6;
                } else if ($quotation->kebutuhan_id == 2) {
                    $icon = 4;
                } else if ($quotation->kebutuhan_id == 3) {
                    $icon = 2;
                } else if ($quotation->kebutuhan_id == 4) {
                    $icon = 3;
                };

                $siteCais = DB::table('sl_site')->where('quotation_site_id',$d->quotation_site_id)->first();
                $siteHris = DB::connection('mysqlhris')->table('m_site')->where('site_id',$siteCais->id)->first();

                DB::connection('mysqlhris')->table('m_vacancy')->insert([
                    'icon_id' => $icon,
                    'start_date' => $current_date_time,
                    'end_date' => Carbon::now()->addDays(7)->toDateTimeString(),
                    'company_id' => $quotation->company_id,
                    'site_id' => $siteHris->id,
                    'position_id' => $d->position_id,
                    'province_id' => $siteCais->provinsi_id,
                    'city_id' => $siteCais->kota_id,
                    'title' => $d->jabatan_kebutuhan,
                    'type' => '',
                    'content' => '',
                    'needs' => $d->jumlah_hc,
                    'phone_number1' => '',
                    'phone_number2' => '',
                    'flyer' => '',
                    'is_active' => 1,
                    'durasi_ketelitian' => 0,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->id,
                    'updated_at' => $current_date_time,
                    'updated_by' => Auth::user()->id
                ]);
            }

            // masukkan COSS ke tabel 
            $totalNominal = 0;
            $totalNominalCoss = 0;
            $ppn = 0;
            $ppnCoss = 0;
            $totalBiaya = 0;
            $totalBiayaCoss = 0;
            $margin = 0;
            $marginCoss = 0;
            $gpm = 0;
            $gpmCoss = 0;
            $quotationService = new QuotationService();
            $calcQuotation = $quotationService->calculateQuotation($quotation);
            foreach ($calcQuotation->quotation_detail as $kd => $kbd) {
                DB::table("sl_quotation_detail_hpp")->insert([
                    'quotation_id' => $quotation->id,
                    'quotation_detail_id' => $kbd->id,
                    'position_id' => $kbd->position_id,
                    'leads_id' =>  $leads->id,
                    'jumlah_hc' => $calcQuotation->jumlah_hc,
                    'gaji_pokok' => $calcQuotation->nominal_upah,
                    'total_tunjangan' => $kbd->total_tunjangan,
                    'tunjangan_hari_raya' => $kbd->tunjangan_hari_raya,
                    'kompensasi' => $kbd->kompensasi,
                    'tunjangan_hari_libur_nasional' => $kbd->tunjangan_holiday,
                    'lembur' => $kbd->lembur,
                    'bpjs_jkk' => $kbd->bpjs_jkk,
                    'bpjs_jkm' => $kbd->bpjs_jkm,
                    'bpjs_jht' => $kbd->bpjs_jht,
                    'bpjs_jp' => $kbd->bpjs_jp,
                    'bpjs_ks' => $kbd->bpjs_kes,
                    'persen_bpjs_jkk' =>  $kbd->persen_bpjs_jkk,
                    'persen_bpjs_jkm' =>  $kbd->persen_bpjs_jkm,
                    'persen_bpjs_jht' =>  $kbd->persen_bpjs_jht,
                    'persen_bpjs_jp' =>  $kbd->persen_bpjs_jp,
                    'persen_bpjs_ks' =>  $kbd->persen_bpjs_kes,
                    'provisi_seragam' =>  $kbd->personil_kaporlap,
                    'provisi_peralatan' => $kbd->personil_devices ,
                    'chemical' => $kbd->personil_chemical,
                    'total_biaya_per_personil' => $kbd->total_personil,
                    'total_biaya_all_personil' => $kbd->sub_total_personil,
                    'management_fee' => $kbd->management_fee,
                    'persen_management_fee' => $quotation->persentase,
                    'ohc' => $kbd->total_ohc,
                    'grand_total' => $kbd->grand_total,
                    'ppn' => $kbd->ppn,
                    'pph' => $kbd->pph,
                    'total_invoice' => $kbd->total_invoice,
                    'pembulatan' => $kbd->pembulatan,
                    'is_pembulatan' => $kbd->is_pembulatan,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                DB::table("sl_quotation_detail_coss")->insert([
                    'quotation_id' => $quotation->id,
                    'quotation_detail_id' => $kbd->id,
                    'position_id' => $kbd->position_id,
                    'leads_id' =>  $leads->id,
                    'jumlah_hc' => $calcQuotation->jumlah_hc,
                    'gaji_pokok' => $calcQuotation->nominal_upah,
                    'total_tunjangan' => $kbd->total_tunjangan,
                    'total_base_manpower' => $kbd->total_base_manpower,
                    'tunjangan_hari_raya' => $kbd->tunjangan_hari_raya,
                    'kompensasi' => $kbd->kompensasi,
                    'tunjangan_hari_libur_nasional' => $kbd->tunjangan_holiday,
                    'lembur' => $kbd->lembur,
                    'bpjs_jkk' => $kbd->bpjs_jkk,
                    'bpjs_jkm' => $kbd->bpjs_jkm,
                    'bpjs_jht' => $kbd->bpjs_jht,
                    'bpjs_jp' => $kbd->bpjs_jp,
                    'bpjs_ks' => $kbd->bpjs_kes,
                    'persen_bpjs_jkk' =>  $kbd->persen_bpjs_jkk,
                    'persen_bpjs_jkm' =>  $kbd->persen_bpjs_jkm,
                    'persen_bpjs_jht' =>  $kbd->persen_bpjs_jht,
                    'persen_bpjs_jp' =>  $kbd->persen_bpjs_jp,
                    'persen_bpjs_ks' =>  $kbd->persen_bpjs_kes,
                    'provisi_seragam' =>  $kbd->personil_kaporlap,
                    'provisi_peralatan' => $kbd->personil_devices ,
                    'chemical' => $kbd->personil_chemical,
                    'total_exclude_base_manpower' => $kbd->total_exclude_base_manpower,
                    'bunga_bank' => $kbd->bunga_bank,
                    'insentif' => $kbd->insentif,
                    'management_fee' => $kbd->management_fee_coss,
                    'persen_bunga_bank' => $quotation->persen_bunga_bank,
                    'persen_insentif' => $quotation->persen_insentif,
                    'persen_management_fee' => $quotation->persentase,
                    'grand_total' => $kbd->grand_total_coss,
                    'ppn' => $kbd->ppn_coss,
                    'pph' => $kbd->pph_coss,
                    'total_invoice' => $kbd->total_invoice_coss,
                    'pembulatan' => $kbd->pembulatan_coss,
                    'is_pembulatan' => $kbd->is_pembulatan,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);

                $totalNominal += $kbd->total_invoice;
                $totalNominalCoss += $kbd->total_invoice_coss;
                $ppn += $kbd->ppn;
                $ppnCoss += $kbd->ppn_coss;
                $totalBiaya += $kbd->sub_total_personil;
                $totalBiayaCoss += $kbd->sub_total_personil;
                $margin = $totalNominal-$ppn-$totalBiaya;
                $marginCoss = $totalNominalCoss-$ppnCoss-$totalBiayaCoss;
                $gpm = ($margin/$totalBiaya)*100;
                $gpmCoss = ($marginCoss/$totalBiayaCoss)*100;
            }
            DB::table("sl_quotation_margin")->insert([
                'quotation_id' => $quotation->id,
                'leads_id' =>  $leads->id,
                'nominal_hpp' => $totalNominal,
                'nominal_harga_pokok' => $totalNominalCoss,
                'ppn_hpp' => $ppn,
                'ppn_harga_pokok' => $ppnCoss,
                'total_biaya_hpp' => $totalBiaya,
                'total_biaya_harga_pokok' => $totalBiayaCoss,
                'margin_hpp' => $margin,
                'margin_harga_pokok' => $marginCoss,
                'gpm_hpp' => $gpm,
                'gpm_harga_pokok' => $gpmCoss,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            // SINGKRON KE ACCURATE

            // Masukkan ke activity
            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($quotation->leads_id);

            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $quotation->leads_id,
                'quotation_id' => $quotation->id,
                'spk_id' => $pks->spk_id,
                'pks_id' => $pks->id,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'PKS',
                'notes' => 'PKS dengan nomor :'.$pks->nomor.' telah diaktifkan oleh '.Auth::user()->full_name,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);
            
            DB::commit();
            DB::connection('mysqlhris')->commit();

            return response()->json([
                'status' => 'sukses',
                'message' => 'berhasil mengaktifkan site'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            DB::connection('mysqlhris')->rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function isiChecklist(Request $request,$id){
        $pks = DB::table('sl_pks')->where('id',$id)->first();
        $spk = DB::table('sl_spk')->where('id',$pks->spk_id)->whereNull('deleted_at')->first();
        $quotation = DB::table('sl_quotation')->where('id',$spk->quotation_id)->whereNull('deleted_at')->first();

        $listRo = DB::connection('mysqlhris')->table('m_user')->whereIn('role_id',[4,5,6,8])->orderBy('full_name','asc')->get();
        $listCrm = DB::connection('mysqlhris')->table('m_user')->whereIn('role_id',[54,55,56])->orderBy('full_name','asc')->get();

        $listJabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
        $listTrainingQ = DB::table('sl_quotation_training')->where('quotation_id',$quotation->id)->whereNull('deleted_at')->get();
        $listTraining = DB::table('m_training')->whereNull('deleted_at')->get();
        $quotation->mulai_kontrak = Carbon::parse($quotation->mulai_kontrak)->format('d F Y');
        $quotation->kontrak_selesai = Carbon::parse($quotation->kontrak_selesai)->format('d F Y');
        $quotation->tgl_quotation = Carbon::parse($quotation->tgl_quotation)->format('d F Y');
        $quotation->tgl_penempatan = Carbon::parse($quotation->tgl_penempatan)->format('d F Y');

        $leads = DB::table('sl_leads')->where('id',$quotation->leads_id)->first();
        $salaryRuleQ = DB::table('m_salary_rule')->where('id',$quotation->salary_rule_id)->first();
        $sPersonil = "";
        $jPersonil = DB::select("SELECT sum(jumlah_hc) as jumlah_hc FROM sl_quotation_detail WHERE quotation_id = $quotation->id and deleted_at is null;");
        if($jPersonil!=null){
            if ($jPersonil[0]->jumlah_hc!=null && $jPersonil[0]->jumlah_hc!=0) {
                $sPersonil .= $jPersonil[0]->jumlah_hc." Manpower (";
                $detailPersonil = DB::table('sl_quotation_detail')
                ->whereNull('sl_quotation_detail.deleted_at')
                ->where('sl_quotation_detail.quotation_id',$quotation->id)
                ->get();
                foreach ($detailPersonil as $idp => $vdp) {
                    if($idp !=0){
                        $sPersonil .= ", ";
                    }
                    $sPersonil .= $vdp->jumlah_hc." ".$vdp->jabatan_kebutuhan;
                }

                $sPersonil .= " )";
            }else{
                $sPersonil = "-";
            }
        }else{
            $sPersonil = "-";
        }
        $quotation->jumlah_personel = $sPersonil;

        return view('sales.pks.checklist-form',compact('listCrm','listRo','pks','quotation','listJabatanPic','listTrainingQ','listTraining','salaryRuleQ','leads'));
    }

    public function saveChecklist(Request $request){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();

            $dataQuotation = DB::table('sl_quotation')->where('id',$request->id)->first();
            
            if($request->ada_serikat=="Tidak Ada"){
                $request->status_serikat ="Tidak Ada";
            }

            // $ro = DB::connection('mysqlhris')->table('m_user')->where('id',$request->ro)->first();
            // $crm = DB::connection('mysqlhris')->table('m_user')->where('id',$request->crm)->first();
        
            // DB::table('sl_pks')->where('id',$request->pks_id)->update([
            //     'ro_id' => $ro->id,
            //     'ro' => $ro->full_name,
            //     'crm_id' => $crm->id,
            //     'crm' => $crm->full_name,
            //     'updated_at' => $current_date_time,
            //     'updated_by' => Auth::user()->full_name
            // ]);

            DB::table('sl_quotation')->where('id',$request->quotation_id)->update([
                'npwp' => $request->npwp ,
                'alamat_npwp' => $request->alamat_npwp,
                'pic_invoice' => $request->pic_invoice ,
                'telp_pic_invoice' => $request->telp_pic_invoice ,
                'email_pic_invoice' => $request->email_pic_invoice ,
                'materai' => $request->materai ,
                'joker_reliever' => $request->joker_reliever ,
                'syarat_invoice' => $request->syarat_invoice ,
                'alamat_penagihan_invoice' => $request->alamat_penagihan_invoice ,
                'catatan_site' => $request->catatan_site ,
                'status_serikat' => $request->status_serikat ,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name
            ]);

            return redirect()->route('pks.view',$request->pks_id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function cetakPks (Request $request,$id){
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $data = DB::table('sl_pks_perjanjian')->where('pks_id',$id)->get();
            return view('sales.pks.cetakan.pks',compact('now','data'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function editPerjanjian ($id){
        try {
            $data = DB::table('sl_pks_perjanjian')->where('id',$id)->first();
            $pks = DB::table('sl_pks')->where('id',$data->pks_id)->first();
            
            return view('sales.pks.edit-perjanjian',compact('data','pks'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function saveEditPerjanjian(Request $request,$id){
        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $data = DB::table('sl_pks_perjanjian')->where('id',$id)->first();

            DB::table('sl_pks_perjanjian')->where('id',$id)->update([
                'pasal' => $request->pasal,
                'judul' => $request->judul,
                'raw_text' => $request->raw_text,
                'updated_at' => $current_date_time,
                'updated_by' => Auth::user()->full_name 
            ]);
            return redirect()->route('pks.view',$data->pks_id);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function ajukanUlangQuotation (Request $request,$pks){
        $current_date_time = Carbon::now()->toDateTimeString();
        $current_date = Carbon::now()->toDateString();
        try {
            $pks = DB::table('sl_pks')->where('id',$pks)->first();
            $spk = DB::table('sl_spk')->where('id',$pks->spk_id)->first();
            $leads = DB::table('sl_leads')->where('id',$spk->leads_id)->first();

            DB::beginTransaction();

            $qasalId = $spk->quotation_id;
            $qtujuan = DB::table("sl_quotation")->where('id',$qasalId)->first();

            $dataToInsertQuotation = (array) $qtujuan;
            unset($dataToInsertQuotation['id']);
            unset($dataToInsertQuotation['nomor']);

            $nomorQuotationBaru = $this->generateNomorQuotation($qtujuan->leads_id,$qtujuan->company_id);
            $dataToInsertQuotation['nomor'] = $nomorQuotationBaru;
            $dataToInsertQuotation['revisi'] = $qtujuan->revisi+1;
            $dataToInsertQuotation['alasan_revisi'] = $request->alasan;            
            $dataToInsertQuotation['quotation_asal_id'] = $qtujuan->id;
            $dataToInsertQuotation['step'] = 1;
            $dataToInsertQuotation['created_at'] = $current_date_time;
            $dataToInsertQuotation['created_by'] = Auth::user()->full_name;
            $qtujuanId = DB::table('sl_quotation')->insertGetId($dataToInsertQuotation);

            //Site
            $site = DB::table("sl_quotation_site")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_site")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);

            foreach ($site as $ks => $site) {
                $dataToInsert = (array) $site;
                unset($dataToInsert['id']);
                $dataToInsert['quotation_id'] = $qtujuanId;
                $dataToInsert['created_at'] = $current_date_time;
                $dataToInsert['created_by'] = Auth::user()->full_name;
                
                $newSiteId = DB::table("sl_quotation_site")->insertGetId($dataToInsert);

                $detail = DB::table("sl_quotation_detail")->whereNull('deleted_at')->where('quotation_site_id',$site->id)->where('quotation_id',$qasalId)->get();
                DB::table("sl_quotation_detail")->where('quotation_id',$qasalId)->update([
                    "deleted_at" => $current_date_time ,
                    "deleted_by" => Auth::user()->full_name,
                ]);

                foreach ($detail as $key => $value) {
                    $dataToInsert = (array) $value;
                    unset($dataToInsert['id']);
                    $dataToInsert['quotation_id'] = $qtujuanId;
                    $dataToInsert['quotation_site_id'] = $newSiteId;
                    $dataToInsert['created_at'] = $current_date_time;
                    $dataToInsert['created_by'] = Auth::user()->full_name;
                    
                    $newId = DB::table("sl_quotation_detail")->insertGetId($dataToInsert);

                    // Quotation Chemical
                    $chemical = DB::table("sl_quotation_chemical")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_chemical")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($chemical as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;
                        
                        DB::table("sl_quotation_chemical")->insert($dataToInsertD);
                    }

                    // Quotation Detail Requirement
                    $requirement = DB::table("sl_quotation_detail_requirement")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_detail_requirement")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($requirement as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;
                        
                        DB::table("sl_quotation_detail_requirement")->insert($dataToInsertD);
                    }
                    
                    // Quotation Detail Tunjangan
                    $tunjangan = DB::table("sl_quotation_detail_tunjangan")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_detail_tunjangan")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($tunjangan as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;
                        
                        DB::table("sl_quotation_detail_tunjangan")->insert($dataToInsertD);
                    }

                    // Quotation Kaporlap
                    $kaporlap = DB::table("sl_quotation_kaporlap")->where("quotation_detail_id",$value->id)->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
                    DB::table("sl_quotation_kaporlap")->where("quotation_detail_id",$value->id)->where('quotation_id',$qasalId)->update([
                        "deleted_at" => $current_date_time ,
                        "deleted_by" => Auth::user()->full_name,
                    ]);
                    foreach ($kaporlap as $keyd => $valued) {
                        $dataToInsertD = (array) $valued;
                        unset($dataToInsertD['id']);
                        $dataToInsertD['quotation_id'] = $qtujuanId;
                        $dataToInsertD['quotation_detail_id'] = $newId;
                        $dataToInsertD['created_at'] = $current_date_time;
                        $dataToInsertD['created_by'] = Auth::user()->full_name;
                        
                        DB::table("sl_quotation_kaporlap")->insert($dataToInsertD);
                    }
                }
            }

            // Quotation Devices
            $devices = DB::table("sl_quotation_devices")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_devices")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($devices as $keyd => $valued) {
                $dataToInsertD = (array) $valued;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;
                
                DB::table("sl_quotation_devices")->insert($dataToInsertD);
            }

            // Quotation Ohc
            $ohc = DB::table("sl_quotation_ohc")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_ohc")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($ohc as $keyd => $valued) {
                $dataToInsertD = (array) $valued;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;
                
                DB::table("sl_quotation_ohc")->insert($dataToInsertD);
            }

            // Quotation Aplikasi
            $aplikasi = DB::table("sl_quotation_aplikasi")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_aplikasi")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($aplikasi as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;
                
                DB::table("sl_quotation_aplikasi")->insert($dataToInsertD);
            }

            // Quotation Kerjasama
            $kerjasama = DB::table("sl_quotation_kerjasama")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_kerjasama")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($kerjasama as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;
                
                DB::table("sl_quotation_kerjasama")->insert($dataToInsertD);
            }

            // Quotation PIC
            $pic = DB::table("sl_quotation_pic")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_pic")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($pic as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;
                
                DB::table("sl_quotation_pic")->insert($dataToInsertD);
            }

            // Quotation Training
            $training = DB::table("sl_quotation_training")->whereNull('deleted_at')->where('quotation_id',$qasalId)->get();
            DB::table("sl_quotation_training")->where('quotation_id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            foreach ($training as $key => $value) {
                $dataToInsertD = (array) $value;
                unset($dataToInsertD['id']);
                $dataToInsertD['quotation_id'] = $qtujuanId;
                $dataToInsertD['created_at'] = $current_date_time;
                $dataToInsertD['created_by'] = Auth::user()->full_name;
                
                DB::table("sl_quotation_training")->insert($dataToInsertD);
            }

            // hapus data yang sudah di ajukan ulang
            DB::table("sl_quotation")->where('id',$qasalId)->update([
                "deleted_at" => $current_date_time ,
                "deleted_by" => Auth::user()->full_name,
            ]);
            DB::commit();

            // hapus spk yang diajukan ulang
            DB::table('sl_spk')->where('id',$spk->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            //insert ke activity sebagai activity pertama
            $qasal = DB::table('sl_quotation')->where('id',$qasalId)->first();
            $customerActivityController = new CustomerActivityController();

            // buat activity baru dari quotation yang diajukan ulang
            $nomorActivity = $customerActivityController->generateNomor($qtujuan->leads_id);
            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $qtujuan->leads_id,
                'quotation_id' => $qasalId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$qasal->nomor.' di ajukan ulang',
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            // buat activity baru dari quotation baru
            $nomorActivity = $customerActivityController->generateNomor($qtujuan->leads_id);
            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $qtujuan->leads_id,
                'quotation_id' => $qtujuanId,
                'branch_id' => $leads->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'tipe' => 'Quotation',
                'notes' => 'Quotation dengan nomor :'.$nomorQuotationBaru.' terbentuk dari ajukan ulang quotation dengan nomor :'.$qasal->nomor,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            return redirect()->route('quotation');

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            SystemController::saveError($e,Auth::user(),$request);
            abort(500);
        }
    }

    public function generateNomorQuotation ($leadsId,$companyId){
        // generate nomor QUOT/SIG/AAABB-092024-00001
        $now = Carbon::now();

        $nomor = "QUOT/";
        $dataLeads = DB::table('sl_leads')->where('id',$leadsId)->first();
        $company = DB::connection('mysqlhris')->table('m_company')->where('id',$companyId)->first();
        if($company != null){
            $nomor = $nomor.$company->code."/";
            $nomor = $nomor.$dataLeads->nomor."-";
        }else{
            $nomor = $nomor."NN/NNNNN-";
        }

        $month = $now->month;
        if($month<10){
            $month = "0".$month;
        }

        $urutan = "00001";

        $jumlahData = DB::select("select * from sl_quotation where nomor like '".$nomor.$month.$now->year."-"."%'");
        $urutan = sprintf("%05d", count($jumlahData)+1);
        $nomor = $nomor.$month.$now->year."-".$urutan;

        return $nomor;
    }
}

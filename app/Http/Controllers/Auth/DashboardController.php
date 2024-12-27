<?php

namespace App\Http\Controllers\Auth;

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

class DashboardController extends Controller
{
    public function dashboardApproval(Request $request){
        $jumlahMenungguApproval = 0;
        $jumlahMenungguDirSales = 0;
        $jumlahMenungguDirkeu = 0;
        $jumlahMenungguDirut = 0;
        $quotationBelumLengkap = 0;
        $jumlahMenungguManagerCrm = 0;
        $error = 0;

        $data = DB::table('sl_quotation')
        ->leftJoin('sl_quotation_client','sl_quotation_client.id','sl_quotation.quotation_client_id')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation_client.leads_id')
        ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.step','sl_quotation.top','sl_quotation.ot3','sl_quotation.ot2','sl_quotation.ot1','sl_quotation.nama_site','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
        ->whereNull('sl_leads.deleted_at')
        ->whereNull('sl_quotation_client.deleted_at')
        ->whereNull('sl_quotation.deleted_at')
        ->where('sl_quotation.is_aktif',0)->get();

        $quotationExisting = DB::table('sl_quotation')->whereNull('deleted_at')->where('is_aktif',0)->get();
        

        $dataMenungguAnda = [];
        $dataMenungguApproval = [];
        $dataBelumLengkap = [];

        foreach ($quotationExisting as $key => $quotation) {
            $jumlahMenungguApproval++;
            array_push($dataMenungguApproval,$quotation);

            if ($quotation->step == 100 && $quotation->is_aktif==0){
                if ($quotation->ot1 == null) {
                    $jumlahMenungguDirSales++;
                    if(Auth::user()->role_id==96){
                        array_push($dataMenungguAnda,$quotation);
                    }
                }
                if($quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari"){
                    $jumlahMenungguDirkeu++;
                    if(Auth::user()->role_id==97){
                        array_push($dataMenungguAnda,$quotation);
                    }
                }
                if ( $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari" ){
                    $jumlahMenungguDirut++;
                }
            } else if ( $quotation->step != 100){
                $quotationBelumLengkap++;
                if(Auth::user()->role_id==99){
                    array_push($dataMenungguAnda,$quotation);
                }
            }else{
                array_push($dataBelumLengkap,$quotation);
                $error++;
            }
        }
        return view('home.dashboard-approval',compact('jumlahMenungguManagerCrm','dataBelumLengkap','dataMenungguApproval','dataMenungguAnda','jumlahMenungguApproval','jumlahMenungguDirSales','jumlahMenungguDirkeu','jumlahMenungguDirut','quotationBelumLengkap'));
    }

    public function getListDashboardApprovalData(Request $request) {
        $arrData = [];

        $data = DB::table('sl_quotation')
        ->leftJoin('sl_quotation_client','sl_quotation_client.id','sl_quotation.quotation_client_id')
        ->leftJoin('sl_leads','sl_leads.id','sl_quotation_client.leads_id')
        ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
        ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
        ->select('sl_quotation.step','sl_quotation.top','sl_quotation.ot3','sl_quotation.ot2','sl_quotation.ot1','sl_quotation.nama_site','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
        ->whereNull('sl_leads.deleted_at')
        ->whereNull('sl_quotation_client.deleted_at')
        ->whereNull('sl_quotation.deleted_at')
        ->where('sl_quotation.is_aktif',0)->get();

        foreach ($data as $key => $value) {
            $value->tgl = Carbon::createFromFormat('Y-m-d',$value->tgl_quotation)->isoFormat('D MMMM Y');
        }
        
        if($request->tipe =="menunggu-anda"){
            foreach ($data as $key => $quotation) {    
                if ($quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot1 == null) {
                    if(Auth::user()->role_id==96){
                        array_push($arrData,$quotation);
                    }
                }else if($quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari"){
                    if(Auth::user()->role_id==97){
                        array_push($arrData,$quotation);
                    }
                }else if ( $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari" ){
                    if(Auth::user()->role_id==99){
                        array_push($arrData,$quotation);
                    }
                }
            }
        }else if($request->tipe =="menunggu-approval"){
            foreach ($data as $key => $quotation) {
                if ($quotation->step == 100 && $quotation->is_aktif==0){
                    array_push($arrData,$quotation);
                }
            }
        }else if($request->tipe =="quotation-belum-lengkap"){
            foreach ($data as $key => $quotation) {    
                if ($quotation->step != 100 && $quotation->is_aktif==0){
                    array_push($arrData,$quotation);
                }
            }
        };

        return DataTables::of($arrData)
            ->addColumn('aksi', function ($data) {
               return "";
            })
            ->editColumn('nomor', function ($data) {
                $ref = "";

                if($data->step != 100){
                    $ref = "#";
                }else{
                    $ref = route('quotation.view',$data->id);
                }
                return '<a href="'.$ref.'" style="font-weight:bold;color:#000056">'.$data->nomor.'</a>';

            })
            ->editColumn('nama_perusahaan', function ($data) {
                return '<a href="'.route('leads.view',$data->leads_id).'" style="font-weight:bold;color:#000056">'.$data->nama_perusahaan.'</a>';
            })
            ->rawColumns(['aksi','nomor','nama_perusahaan'])
            ->make(true);
    }
}

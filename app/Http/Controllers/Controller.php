<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private $user;
    private $signed_in;
    private $menu;
    private $notifikasi;
    private $pesan;
    private $approval;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Auth::check()){
                $user = Auth::user();
                $role = DB::connection('mysqlhris')->table('m_role')->where('id',$user->role_id)->first();
                $cabang = DB::connection('mysqlhris')->table('m_branch')->where('id',$user->branch_id)->first();
                $user->role = $role->name;
                $user->cabang = $cabang->name;
                $this->user = $user;
                $this->signed_in = Auth::check();

                $approval = [];
                // dirut dihapus
                // $arrRole = [96,97,40,98,99];
                $arrRole = [96,97,40,98];
                if(in_array($user->role_id,$arrRole)){
                    $dataApproval = DB::table('sl_quotation')
                    ->leftJoin('sl_leads','sl_leads.id','sl_quotation.leads_id')
                    ->leftJoin('m_status_quotation','sl_quotation.status_quotation_id','m_status_quotation.id')
                    ->leftJoin('m_tim_sales_d','sl_leads.tim_sales_d_id','=','m_tim_sales_d.id')
                    ->select('sl_quotation.step','sl_quotation.top','sl_quotation.ot3','sl_quotation.ot2','sl_quotation.ot1','sl_quotation.nama_site','m_status_quotation.nama as status','sl_quotation.is_aktif','sl_quotation.step','sl_quotation.id as quotation_id','sl_quotation.jenis_kontrak','sl_quotation.company','sl_quotation.kebutuhan','sl_quotation.created_by','sl_quotation.leads_id','sl_quotation.id','sl_quotation.nomor','sl_quotation.nama_perusahaan','sl_quotation.tgl_quotation')
                    ->whereNull('sl_leads.deleted_at')
                    ->whereNull('sl_quotation.deleted_at')
                    ->where('sl_quotation.is_aktif',0)->get();

                    $approval = [];
                    foreach ($dataApproval as $key => $quotation) {   
                        $quotation->tgl_quot = Carbon::createFromFormat('Y-m-d', $quotation->tgl_quotation)->toFormattedDateString();        
                        if ($quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot1 == null) {
                            if(Auth::user()->role_id==96){
                                array_push($approval,$quotation);
                            }
                        }else if($quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari"){
                            if(Auth::user()->role_id==97 || Auth::user()->role_id==40 ){
                                array_push($approval,$quotation);
                            }
                        }
                        // else if ( $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari" ){
                        //     if(Auth::user()->role_id==99){
                        //         array_push($approval,$quotation);
                        //     }
                        // }
                    }
                }

                $this->approval = $approval;

                $notifikasiList = DB::table('log_notification')->where('is_read',0)->whereNull('deleted_at')->where('user_id',$user->id)->orderBy('created_at','desc')->get();
                foreach ($notifikasiList as $key => $value) {
                    $value->waktu = Carbon::parse($value->created_at)->diffForHumans();
                    $value->url = "";
                };

                $this->notifikasi = $notifikasiList;

                view()->share('signed_in', $this->signed_in);
                view()->share('user', $this->user);
                view()->share('approval', $this->approval);
                view()->share('notifikasi', $this->notifikasi);
            }
            return $next($request);
        });
    }
}

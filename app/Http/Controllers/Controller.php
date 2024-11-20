<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Support\Facades\Auth;
// use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // if(Auth::check()){
            //     $user = Auth::user();
            //     $role = DB::table('sys_role')->where('id',$user->sys_role_id)->first();
            //     $cabang = DB::table('sys_cabang')->where('id',$user->sys_cabang_id)->first();
            //     $notif = DB::select("SELECT id,keterangan,tipe,doc_id,transaksi,tabel,'#' as link,(SELECT `name` from users WHERE id = user_id) as `user`,ftanggalwaktu_w_sec(created_at) as tgl,created_by
            //     from sys_notifikasi 
            //     WHERE 
            //     deleted_at is null
            //     and user_id = ".Auth::user()->id."
            //     and is_read = 0
            //     and tipe=1;");
            //     $pesan = DB::select("SELECT id,keterangan,tipe,(select sal_pelanggan_id from sal_prospect where id= doc_id) as doc_id,transaksi,tabel,'#' as link,(SELECT `name` from users WHERE id = user_id) as `user`,ftanggalwaktu_w_sec(created_at) as tgl,created_by
            //     from sys_notifikasi 
            //     WHERE 
            //     deleted_at is null
            //     and user_id = ".Auth::user()->id."
            //     and is_read = 0
            //     and tipe=2;");
            //     $approval = DB::select("
            //     SELECT 
            //         0 as id,
            //         id as doc_id,
            //         user_owner_id,
            //         'sal_prospect' as tabel,
            //         'Approval' as transaksi,
            //         CONCAT('Leads : <b>',(SELECT nama_perusahaan from sal_pelanggan WHERE id = sal_prospect.sal_pelanggan_id),'</b> , Layanan : <b>',(select nama_service from mas_service WHERE id = sal_prospect.mas_service_id),'</b>') as txt 
            //     FROM sal_prospect 
            //         WHERE 
            //             deleted_at is null 
            //             and mas_status_prospect_id = 2 
            //             and ot_now = (( SELECT tingkat_approve from sys_role WHERE id = $role->id limit 1 )-1)
            //             ;");
            //     $arrApproval = [];
            //     if($role->id == 31){
            //         foreach ($approval as $key => $value) {
            //             $sales = DB::select("select * from sys_user_parent where deleted_at is null and parent_id = ".Auth::user()->id." and users_id = ".$value->user_owner_id);
            //             if(count($sales)>0){
            //                 array_push($arrApproval,$value);
            //             }
            //         }
            //     }else{
            //         $arrApproval = $approval;
            //     }
                
            //     $masterKoef = DB::select("SELECT id,nama_key,`key` from mas_koef_form where deleted_at is null;");

            //     foreach ($notif as $key => $value) {
            //         $value->link = route('system.notifikasi.read',['id' => $value->id, 'doc_id' => $value->doc_id, 'to' => 'approval']);
            //     }
            //     foreach ($pesan as $key => $value) {
            //         $value->link = route('system.notifikasi.read',['id' => $value->id, 'doc_id' => $value->doc_id, 'to' => 'obrolan']);
            //     }
            //     foreach ($approval as $key => $value) {
            //         $value->link = route('system.notifikasi.read',['id' => $value->id, 'doc_id' => $value->doc_id, 'to' => 'approval']);
            //     }

            //     $user->role = $role->nama;
            //     $user->cabang = $cabang->nama_cabang;
            //     $this->user = $user;
            //     $this->signed_in = Auth::check();

            //     $this->menu = DB::select("SELECT m.sys_menu_id,m.nama_menu,m.has_child,m.icon,m.kode,m.link,m.level,m.urutan,rm.is_view,rm.is_add,rm.is_edit,rm.is_delete,m.id,(select nama_grup_menu from sys_menu_grup where id = m.sys_menu_grup_id) as grup_menu,m.nama_route
            //     FROM sys_role_menu rm
            //     INNER JOIN sys_menu m ON m.id = rm.sys_menu_id
            //     WHERE 
            //     rm.deleted_at is null 
            //     and rm.sys_role_id = ".Auth::user()->sys_role_id." and rm.is_view=1 and m.deleted_at is null
            //     ORDER BY m.level asc , m.urutan asc ,m.kode asc;");
            //     $this->notifikasi = $notif;
            //     $this->pesan = $pesan;
            //     $this->approval = $arrApproval;
            //     $this->masterKoef = $masterKoef;

            //     view()->share('signed_in', $this->signed_in);
            //     view()->share('user', $this->user);
            //     view()->share('menu', $this->menu);
            //     view()->share('notifikasi', $this->notifikasi);
            //     view()->share('pesan', $this->pesan);
            //     view()->share('approval', $this->approval);
            //     view()->share('master_koef', $this->masterKoef);

            // }
            return $next($request);
        });
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

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


class DashboardManagerCrmController extends Controller
{
    public function dashboardManagerCrm(Request $request)
    {
        $countKontrakBelumAdaQuotation = 0;
        $countKontrakBelumIsiChecklist = 0;
        $countKontrakBelumUploadPks = 0;
        $countKontrakSiapDiaktifkan = 0;

        $listKontrakBelumAdaQuotation = DB::table('sl_pks')
            ->select(
            'sl_pks.id',
            'sl_pks.nomor',
            'sl_pks.nama_perusahaan',
            DB::raw('(SELECT `name` FROM shelter3_hris.m_branch WHERE id = sl_pks.branch_id) as cabang'),
            'sl_pks.alamat_perusahaan',
            'sl_pks.layanan',
            'sl_pks.bidang_usaha',
            'sl_pks.jenis_perusahaan',
            'sl_pks.provinsi',
            'sl_pks.kota',
            'sl_pks.kategori_sesuai_hc',
            'sl_pks.created_by'
            )
            ->whereNull('sl_pks.deleted_at')
            ->whereIn('sl_pks.status_pks_id', [1,5,6])
            ->whereRaw('(SELECT COUNT(*) FROM sl_site WHERE deleted_at IS NULL AND pks_id = sl_pks.id AND quotation_id IS NOT NULL) = 0')
            ->get();
        $countKontrakBelumAdaQuotation = $listKontrakBelumAdaQuotation->count();

        $listKontrakBelumAktif = DB::table('sl_pks')
            ->select(
            'sl_pks.id',
            'sl_pks.nomor',
            'sl_pks.nama_perusahaan',
            DB::raw('(SELECT `name` FROM shelter3_hris.m_branch WHERE id = sl_pks.branch_id) as cabang'),
            'sl_pks.alamat_perusahaan',
            'sl_pks.layanan',
            'sl_pks.bidang_usaha',
            'sl_pks.jenis_perusahaan',
            'sl_pks.provinsi',
            'sl_pks.kota',
            'sl_pks.kategori_sesuai_hc',
            'sl_pks.created_by'
            )
            ->whereNull('sl_pks.deleted_at')
            ->whereIn('sl_pks.status_pks_id', [1,5,6])
            ->whereNotIn('sl_pks.id', $listKontrakBelumAdaQuotation->pluck('id')->toArray())
            ->get();

        $listKontrakBelumIsiChecklist = [];
        foreach ($listKontrakBelumAktif as $key => $value) {
            $quotationList = DB::table('sl_site')
                ->where('pks_id', $value->id)
                ->whereNull('deleted_at')
                ->whereNotNull('quotation_id')
                ->get();
            $isAllChecked = true;
            foreach ($quotationList as $keyd => $valued) {
                $quotationData = DB::table('sl_quotation')
                    ->where('id', $valued->quotation_id)
                    ->whereNull('deleted_at')
                    ->first();
                if (!$quotationData || $quotationData->materai == null) {
                    $isAllChecked = false;
                    break;
                }
            }
            if (!$isAllChecked) {
                $countKontrakBelumIsiChecklist++;
                $listKontrakBelumIsiChecklist[] = $value;
            }
        }

        $listKontrakBelumUploadPks = DB::table('sl_pks')
            ->select(
            'sl_pks.id',
            'sl_pks.nomor',
            'sl_pks.nama_perusahaan',
            DB::raw('(SELECT `name` FROM shelter3_hris.m_branch WHERE id = sl_pks.branch_id) as cabang'),
            'sl_pks.alamat_perusahaan',
            'sl_pks.layanan',
            'sl_pks.bidang_usaha',
            'sl_pks.jenis_perusahaan',
            'sl_pks.provinsi',
            'sl_pks.kota',
            'sl_pks.kategori_sesuai_hc',
            'sl_pks.created_by'
            )
            ->whereNull('sl_pks.deleted_at')
            ->whereIn('sl_pks.status_pks_id', [1,5,6])
            ->whereNotIn('sl_pks.id', $listKontrakBelumAdaQuotation->pluck('id')->toArray())
            ->whereNotIn('sl_pks.id', array_map(function($item) { return $item->id; }, $listKontrakBelumIsiChecklist))
            ->whereNull('sl_pks.link_pks_disetujui')
            ->get();
        $countKontrakBelumUploadPks = $listKontrakBelumUploadPks->count();

        $listKontrakSiapDiaktifkan = DB::table('sl_pks')
            ->select(
            'sl_pks.id',
            'sl_pks.nomor',
            'sl_pks.nama_perusahaan',
            DB::raw('(SELECT `name` FROM shelter3_hris.m_branch WHERE id = sl_pks.branch_id) as cabang'),
            'sl_pks.alamat_perusahaan',
            'sl_pks.layanan',
            'sl_pks.bidang_usaha',
            'sl_pks.jenis_perusahaan',
            'sl_pks.provinsi',
            'sl_pks.kota',
            'sl_pks.kategori_sesuai_hc',
            'sl_pks.created_by'
            )
            ->whereNull('sl_pks.deleted_at')
            ->whereIn('sl_pks.status_pks_id', [1,5,6])
            ->whereNotIn('sl_pks.id', $listKontrakBelumAdaQuotation->pluck('id')->toArray())
            ->whereNotIn('sl_pks.id', array_map(function($item) { return $item->id; }, $listKontrakBelumIsiChecklist))
            ->whereNotIn('sl_pks.id', $listKontrakBelumUploadPks->pluck('id')->toArray())
            ->get();
        $countKontrakSiapDiaktifkan = $listKontrakSiapDiaktifkan->count();


        return view('home.dashboard-manager-crm',compact(
            'countKontrakBelumAdaQuotation',
            'countKontrakBelumIsiChecklist',
            'countKontrakBelumUploadPks',
            'countKontrakSiapDiaktifkan',
            'listKontrakBelumAdaQuotation',
            'listKontrakBelumIsiChecklist',
            'listKontrakBelumUploadPks',
            'listKontrakSiapDiaktifkan'
        ));
    }
    public function listPksSiapAktif(Request $request){
        $listPksAktif = \DB::table('sl_site')
            ->join('sl_pks', 'sl_site.pks_id', '=', 'sl_pks.id')
            ->join('sl_leads', 'sl_site.lead_id', '=', 'sl_leads.id')
            ->whereNull('sl_site.deleted_at')
            ->whereNull('sl_pks.deleted_at')
            ->where('sl_pks.status_pks_id', 6)
            ->whereNotNull('sl_site.quotation_id')
            ->whereNotIn('sl_site.id', function($query) {
            $query->select('site_id')
                ->from('shelter3_hris.m_site')
                ->whereNotNull('site_id')
                ->where('is_active', 1);
            })
            ->select(
            'sl_site.id',
            'sl_pks.id as pks_id',
            'sl_site.nomor',
            'sl_leads.nama_perusahaan',
            'sl_site.nama_site',
            'sl_site.provinsi',
            'sl_site.kota',
            'sl_site.kebutuhan',
            'sl_pks.nomor as nomor_pks'
            )
            ->get();

        return DataTables::of($listPksAktif)
            ->addColumn('check', function ($data) {
            return '<input type="checkbox" class="check-site" value="'.$data->id.'">';
            })
            ->rawColumns(['check'])
            ->make(true);
    }
}

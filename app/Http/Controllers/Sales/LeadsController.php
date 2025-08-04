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
use App\Http\Controllers\Sales\CustomerActivityController;


class LeadsController extends Controller
{

    public function index(Request $request)
    {
        $tglDari = $request->tgl_dari;
        $tglSampai = $request->tgl_sampai;

        if ($tglDari == null) {
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
        }
        if ($tglSampai == null) {
            $tglSampai = carbon::now()->toDateString();
        }

        $ctglDari = Carbon::createFromFormat('Y-m-d', $tglDari);
        $ctglSampai = Carbon::createFromFormat('Y-m-d', $tglSampai);
        $rekomendasiPerusahaan = DB::table('sl_leads')
            ->select('id', 'nama_perusahaan')
            ->orderBy('nama_perusahaan')
            ->get();


        $branch = DB::connection('mysqlhris')->table('m_branch')->where('id', '!=', 1)->where('is_active', 1)->get();
        $status = DB::table('m_status_leads')->whereNull('deleted_at')->get();
        $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

        $error = null;
        $success = null;
        if ($ctglDari->gt($ctglSampai)) {
            $tglDari = carbon::now()->startOfMonth()->subMonths(3)->toDateString();
            $error = 'Tanggal dari tidak boleh melebihi tanggal sampai';
        }
        ;
        if ($ctglSampai->lt($ctglDari)) {
            $tglSampai = carbon::now()->toDateString();
            $error = 'Tanggal sampai tidak boleh kurang dari tanggal dari';
        }

        //untuk tampilan
        $jumlahBelumAktif = DB::table('sl_leads')
            ->whereNull('sl_leads.deleted_at')
            ->whereNull('sl_leads.is_aktif')
            ->count();
        $jumlahDitolak = DB::table('sl_leads')
            ->whereNull('sl_leads.deleted_at')
            ->where('sl_leads.is_aktif', 0)
            ->count();

        return view('sales.leads.list', compact('jumlahBelumAktif', 'jumlahDitolak', 'branch', 'platform', 'status', 'tglDari', 'tglSampai', 'request', 'error', 'success', 'rekomendasiPerusahaan'));
    }
    public function indexTerhapus(Request $request)
    {
        return view('sales.leads.list-terhapus');
    }

    public function add(Request $request)
    {
        try {
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            $branch = DB::connection('mysqlhris')->table('m_branch')->where('id', '!=', 1)->where('is_active', 1)->get();
            $jabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
            $bidangPerusahaan = DB::table('m_bidang_perusahaan')->whereNull('deleted_at')->get();
            $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
            $platform = DB::table('m_platform')->whereNull('deleted_at')->where('id', '<>', 11)->get();
            $provinsi = DB::connection('mysqlhris')->table('m_province')->get();
            $grupPerusahaan = DB::table('sl_perusahaan_groups')->get();
            $benua = DB::table('m_benua')->get();
            $negaraDefault = DB::table('m_negara')->where('id_benua', 2)->get();
            $kota = [];
            $kecamatan = [];
            $kelurahan = [];

            return view('sales.leads.add', compact('benua', 'negaraDefault', 'provinsi', 'branch', 'jabatanPic', 'jenisPerusahaan', 'kebutuhan', 'platform', 'now', 'kota', 'kecamatan', 'kelurahan', 'bidangPerusahaan', 'grupPerusahaan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function view(Request $request, $id)
    {
        try {
            $data = DB::table('sl_leads')->whereNull('customer_id')->where('id', $id)->first();

            $data->stgl_leads = Carbon::createFromFormat('Y-m-d', $data->tgl_leads)->isoFormat('D MMMM Y');
            $data->screated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->isoFormat('D MMMM Y');

            $branch = DB::connection('mysqlhris')->table('m_branch')->where('is_active', 1)->get();
            $jabatanPic = DB::table('m_jabatan_pic')->whereNull('deleted_at')->get();
            $jenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->get();
            $bidangPerusahaan = DB::table('m_bidang_perusahaan')->whereNull('deleted_at')->get();
            $kebutuhan = DB::table('m_kebutuhan')->whereNull('deleted_at')->get();
            $platform = DB::table('m_platform')->whereNull('deleted_at')->get();

            $activity = DB::table('sl_customer_activity')->whereNull('deleted_at')->where('leads_id', $id)->orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($activity as $key => $value) {
                $value->screated_at = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->isoFormat('D MMMM Y HH:mm');
                $value->stgl_activity = Carbon::createFromFormat('Y-m-d', $value->tgl_activity)->isoFormat('D MMMM Y');
            }

            $provinsi = DB::connection('mysqlhris')->table('m_province')->get();
            $kota = DB::connection('mysqlhris')->table('m_city')->where('id', $data->kota_id)->get();
            $kecamatan = DB::connection('mysqlhris')->table('m_district')->where('id', $data->kecamatan_id)->get();
            $kelurahan = DB::connection('mysqlhris')->table('m_village')->where('id', $data->kelurahan_id)->get();
            $benua = DB::table('m_benua')->get();
            $negaraDefault = DB::table('m_negara')->where('id_benua', $data->benua_id != null ? $data->benua_id : 2)->get();
            return view('sales.leads.view', compact('benua', 'negaraDefault', 'activity', 'data', 'branch', 'jabatanPic', 'jenisPerusahaan', 'kebutuhan', 'platform', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'bidangPerusahaan'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }

    }

    public function list(Request $request)
    {
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $tim = DB::table('m_tim_sales_d')->where('user_id', Auth::user()->id)->first();

            $data = DB::table('sl_leads')
                ->join('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
                ->leftJoin($db2 . '.m_branch', 'sl_leads.branch_id', '=', $db2 . '.m_branch.id')
                ->leftJoin('m_platform', 'sl_leads.platform_id', '=', 'm_platform.id')
                ->leftJoin('m_tim_sales_d', 'sl_leads.tim_sales_d_id', '=', 'm_tim_sales_d.id')
                ->select('m_tim_sales_d.nama as sales', 'sl_leads.*', 'm_status_leads.nama as status', $db2 . '.m_branch.name as branch', 'm_platform.nama as platform', 'm_status_leads.warna_background', 'm_status_leads.warna_font')
                ->whereNull('sl_leads.deleted_at')
                ->where('status_leads_id', '!=', 102);

            if (!empty($request->tgl_dari)) {
                $data = $data->where('sl_leads.tgl_leads', '>=', $request->tgl_dari);
            } else {
                $data = $data->where('sl_leads.tgl_leads', '==', carbon::now()->toDateString());
            }
            if (!empty($request->tgl_sampai)) {
                $data = $data->where('sl_leads.tgl_leads', '<=', $request->tgl_sampai);
            } else {
                $data = $data->where('sl_leads.tgl_leads', '==', carbon::now()->toDateString());
            }
            if (!empty($request->branch)) {
                $data = $data->where('sl_leads.branch_id', $request->branch);
            }
            if (!empty($request->platform)) {
                $data = $data->where('sl_leads.platform_id', $request->platform);
            }
            if (!empty($request->status)) {
                $data = $data->where('sl_leads.status_leads_id', $request->status);
            }

            //divisi sales
            // if(in_array(Auth::user()->role_id,[29,30,31,32,33])){
            //     // sales
            //     if(Auth::user()->role_id==29){
            //         $data = $data->where('m_tim_sales_d.user_id',Auth::user()->id);
            //     }else if(Auth::user()->role_id==30){
            //     }
            //     // spv sales
            //     else if(Auth::user()->role_id==31){
            //         $tim = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->first();
            //         $memberSales = [];
            //         $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id',$tim->tim_sales_id)->get();
            //         foreach ($sales as $key => $value) {
            //             array_push($memberSales,$value->user_id);
            //         }
            //         $data = $data->whereIn('m_tim_sales_d.user_id',$memberSales);
            //     }
            //     // Asisten Manager Sales , Manager Sales
            //     else if(Auth::user()->role_id==32 || Auth::user()->role_id==33){

            //     }
            // }
            // //divisi RO
            // else if(in_array(Auth::user()->role_id,[4,5,6,8])){
            //     if(in_array(Auth::user()->role_id,[4,5])){
            //         $data = $data->where('sl_leads.ro_id',Auth::user()->id);
            //     }else if(in_array(Auth::user()->role_id,[6,8])){

            //     }
            // }
            // //divisi crm
            // else if(in_array(Auth::user()->role_id,[54,55,56])){
            //     if(in_array(Auth::user()->role_id,[54])){
            //         $data = $data->where('sl_leads.crm_id',Auth::user()->id);
            //     }else if(in_array(Auth::user()->role_id,[55,56])){

            //     }
            // };

            $data = $data->get();

            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d', $value->tgl_leads)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
                ->addColumn('aksi', function ($data) use ($tim) {
                    $canView = false;
                    $href = "#";
                    if (Auth::user()->role_id == 29) {
                        if ($data->tim_sales_d_id == $tim->id) {
                            $canView = true;
                        }
                    } else {
                        $canView = true;
                    }

                    if ($canView) {
                        return '<div class="justify-content-center d-flex">
                        <a href="' . $href . '" class="btn btn-primary waves-effect btn-xs"><i class="mdi mdi-magnify"></i></a> &nbsp;
                    </div>';
                    } else {
                        return '<div class="justify-content-center d-flex">
                        <a href="#" class="btn btn-warning waves-effect btn-xs"><i class="mdi mdi-close"></i></a> &nbsp;
                    </div>';
                    }
                })
                ->addColumn('can_view', function ($data) use ($tim) {
                    $canView = false;
                    if (Auth::user()->role_id == 29) {
                        if ($data->tim_sales_d_id == $tim->id) {
                            $canView = true;
                        }
                    } else {
                        $canView = true;
                    }

                    return $canView;
                })
                ->editColumn('nomor', function ($data) use ($tim) {
                    $canView = false;
                    if (Auth::user()->role_id == 29) {
                        if ($data->tim_sales_d_id == $tim->id) {
                            $canView = true;
                        }
                    } else {
                        $canView = true;
                    }

                    $route = route('leads.view', $data->id);
                    if (!$canView) {
                        $route = "#";
                    }
                    return '<a href="javascript:void(0)" style="font-weight:bold;color:rgb(130, 131, 147)">' . $data->nomor . '</a>';
                })
                // ->editColumn('nama_perusahaan', function ($data) {
                //     return '<a href="'.route('leads.view',$data->id).'" style="font-weight:bold;color:rgb(130, 131, 147)">'.$data->nama_perusahaan.'</a>';
                // })
                ->rawColumns(['can_view', 'aksi', 'nomor'])
                ->make(true);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function filterRekomendasi(Request $request)
    {
        $namaGrup = $request->nama_grup;

        if (!$namaGrup) {
            return response()->json([]);
        }

        // Ambil perusahaan yang sudah tergabung di grup
        $sudahMasukGrup = DB::table('sl_perusahaan_groups_d')
            ->select('nama_perusahaan')
            ->pluck('nama_perusahaan')
            ->toArray();

        // Filter hanya perusahaan yang belum masuk grup
        $perusahaan = DB::table('sl_leads')
            ->select('sl_leads.id', 'sl_leads.nama_perusahaan', 'sl_leads.kota', 'm_jenis_perusahaan.nama as jenis_perusahan')
            ->leftJoin('m_jenis_perusahaan', 'sl_leads.jenis_perusahaan_id', '=', 'm_jenis_perusahaan.id')
            ->where('sl_leads.nama_perusahaan', 'like', '%' . $namaGrup . '%')
            ->whereNotIn('sl_leads.nama_perusahaan', $sudahMasukGrup) // ← mencegah duplikat
            ->orderBy('sl_leads.nama_perusahaan')
            ->limit(50)
            ->get();

        return response()->json($perusahaan);
    }



    public function groupkan(Request $request)
    {
        $perusahaanTerpilih = $request->perusahaan_terpilih ?? [];

        if (empty($perusahaanTerpilih)) {
            return redirect()->back()->with('error', 'Tidak ada perusahaan yang dipilih.');
        }

        // Cek apakah ada perusahaan yang sudah tergabung di grup
        $sudahTergabung = DB::table('sl_perusahaan_groups_d')
            ->whereIn('nama_perusahaan', function ($query) use ($perusahaanTerpilih) {
                $query->select('nama_perusahaan')
                    ->from('sl_leads')
                    ->whereIn('id', $perusahaanTerpilih);
            })
            ->pluck('nama_perusahaan');

        if ($sudahTergabung->isNotEmpty()) {
            return redirect()->back()->with('error', 'Beberapa perusahaan sudah tergabung dalam grup: ' . $sudahTergabung->implode(', '));
        }

        // Tentukan nama grup dari perusahaan pertama
        $grupSaranNama = DB::table('sl_leads')
            ->whereIn('id', $perusahaanTerpilih)
            ->orderBy('nama_perusahaan')
            ->first()->nama_perusahaan ?? 'Grup Otomatis';

        // Simpan grup baru
        $groupId = DB::table('sl_perusahaan_groups')->insertGetId([
            'nama_grup' => $grupSaranNama,
            'created_at' => now(),
            'created_by' => Auth::user()->full_name,
            'update_at' => now(),
            'update_by' => Auth::user()->full_name,
        ]);

        // Masukkan perusahaan-perusahaan ke dalam grup
        foreach ($perusahaanTerpilih as $perusahaanId) {
            $namaPerusahaan = DB::table('sl_leads')->where('id', $perusahaanId)->value('nama_perusahaan');

            DB::table('sl_perusahaan_groups_d')->insert([
                'group_id' => $groupId,
                'nama_perusahaan' => $namaPerusahaan,
                'created_at' => now(),
                'created_by' => Auth::user()->full_name,
                'update_at' => now(),
                'update_by' => Auth::user()->full_name,
            ]);
        }

        return redirect()->back()->with('success', 'Pengelompokan berhasil disimpan.');
    }



    public function listTerhapus(Request $request)
    {
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $tim = DB::table('m_tim_sales_d')->where('user_id', Auth::user()->id)->first();

            $data = DB::table('sl_leads')
                ->join('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
                ->leftJoin($db2 . '.m_branch', 'sl_leads.branch_id', '=', $db2 . '.m_branch.id')
                ->leftJoin('m_platform', 'sl_leads.platform_id', '=', 'm_platform.id')
                ->leftJoin('m_tim_sales_d', 'sl_leads.tim_sales_d_id', '=', 'm_tim_sales_d.id')
                ->select('sl_leads.deleted_by', 'sl_leads.deleted_at', 'm_tim_sales_d.nama as sales', 'sl_leads.*', 'm_status_leads.nama as status', $db2 . '.m_branch.name as branch', 'm_platform.nama as platform', 'm_status_leads.warna_background', 'm_status_leads.warna_font')
                ->whereNotNull('sl_leads.deleted_at')
                ->whereNull('sl_leads.customer_id');

            $data = $data->get();

            foreach ($data as $key => $value) {
                // $value->deleted_at = Carbon::createFromFormat('Y-m-d H:i',$value->deleted_at)->isoFormat('D MMMM Y H:i');
                $value->tgl = Carbon::createFromFormat('Y-m-d', $value->tgl_leads)->isoFormat('D MMMM Y');
            }

            return DataTables::of($data)
                ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function save(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nama_perusahaan' => 'required|max:100|min:3',
                'pic' => 'required',
                'branch' => 'required',
                'kebutuhan' => 'required',
                'provinsi' => 'required',
                'kota' => 'required'
            ], [
                'min' => 'Masukkan :attribute minimal :min',
                'max' => 'Masukkan :attribute maksimal :max',
                'required' => ':attribute harus di isi',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            } else {
                $current_date_time = Carbon::now()->toDateTimeString();
                $db2 = DB::connection('mysqlhris')->getDatabaseName();
                $provinsi = DB::table($db2 . '.m_province')->where('id', $request->provinsi)->first();
                $kota = DB::table($db2 . '.m_city')->where('id', $request->kota)->first();
                $kecamatan = DB::table($db2 . '.m_district')->where('id', $request->kecamatan)->first();
                $kelurahan = DB::table($db2 . '.m_village')->where('id', $request->kelurahan)->first();
                $benua = DB::table('m_benua')->where('id_benua', $request->benua)->first();
                $negara = DB::table('m_negara')->where('id_negara', $request->negara)->first();
                $jenisPerusahaan = DB::table('m_jenis_perusahaan')->where('id', $request->jenis_perusahaan)->first();
                $bidangPerusahaan = DB::table('m_bidang_perusahaan')->where('id', $request->bidang_perusahaan)->first();
                $msgSave = '';
                // Ambil ID grup, atau buat grup baru jika pilih "__new__"
                $groupId = $request->perusahaan_group_id;

                if ($groupId === '__new__') {
                    $groupId = DB::table('sl_perusahaan_groups')->insertGetId([
                        'nama_grup' => $request->new_nama_grup,
                        'created_at' => now(),
                        'created_by' => Auth::user()->full_name,
                        'update_at' => now(),
                        'update_by' => Auth::user()->full_name
                    ]);
                }

                // Simpan nama perusahaan ke detail grup
                DB::table('sl_perusahaan_groups_d')->insert([
                    'group_id' => $groupId,
                    'nama_perusahaan' => $request->nama_perusahaan,
                    'created_at' => now(),
                    'created_by' => Auth::user()->full_name,
                    'update_at' => now(),
                    'update_by' => Auth::user()->full_name
                ]);

                if (!empty($request->id)) {
                    DB::table('sl_leads')->where('id', $request->id)->update([
                        'nama_perusahaan' => $request->nama_perusahaan,
                        'telp_perusahaan' => $request->telp_perusahaan,
                        'jenis_perusahaan_id' => $request->jenis_perusahaan,
                        'jenis_perusahaan' => $jenisPerusahaan ? $jenisPerusahaan->nama : null,
                        'bidang_perusahaan_id' => $request->bidang_perusahaan,
                        'bidang_perusahaan' => $bidangPerusahaan ? $bidangPerusahaan->nama : null,
                        'branch_id' => $request->branch,
                        'platform_id' => $request->platform,
                        'kebutuhan_id' => $request->kebutuhan,
                        'alamat' => $request->alamat_perusahaan,
                        'pic' => $request->pic,
                        'jabatan' => $request->jabatan_pic,
                        'no_telp' => $request->no_telp,
                        'email' => $request->email,
                        'pma' => $request->pma,
                        'notes' => $request->detail_leads,
                        'provinsi_id' => $request->provinsi,
                        'provinsi' => $provinsi ? $provinsi->name : null,
                        'kota_id' => $request->kota,
                        'kota' => $kota ? $kota->name : null,
                        'kecamatan_id' => $request->kecamatan,
                        'kecamatan' => $kecamatan ? $kecamatan->name : null,
                        'kelurahan_id' => $request->kelurahan,
                        'kelurahan' => $kelurahan ? $kelurahan->name : null,
                        'benua_id' => $request->benua,
                        'benua' => $benua ? $benua->nama_benua : null,
                        'negara_id' => $request->negara,
                        'negara' => $negara ? $negara->nama_negara : null,
                        'updated_at' => $current_date_time,
                        'updated_by' => Auth::user()->full_name
                    ]);
                    // Ambil ID grup, atau buat grup baru jika pilih "__new__"


                    //insert ke activity sebagai activity pertama
                    // $customerActivityController = new CustomerActivityController();
                    // $nomorActivity = $customerActivityController->generateNomor($request->id);

                    // $activityId = DB::table('sl_customer_activity')->insertGetId([
                    //     'leads_id' => $request->id,
                    //     'branch_id' => $request->branch,
                    //     'tgl_activity' => $current_date_time,
                    //     'nomor' => $nomorActivity,
                    //     'notes' => 'Leads diubah',
                    //     'tipe' => 'Leads',
                    //     'is_activity' => 0,
                    //     'created_at' => $current_date_time,
                    //     'created_by' => Auth::user()->full_name
                    // ]);

                    $msgSave = 'Leads ' . $request->nama_perusahaan . ' berhasil disimpan.';
                } else {
                    $companies = DB::table('sl_leads')->whereNull('deleted_at')->pluck('nama_perusahaan');
                    foreach ($companies as $company) {
                        if (similar_text(strtolower($request->nama_perusahaan), strtolower($company), $percent)) {
                            if ($percent > 95) { // jika kemiripan lebih dari 80%
                                $validator->errors()->add('nama_perusahaan', 'Nama perusahaan terlalu mirip dengan : ' . $company . ' Silahkan infokan ke Telesales atau Admin IT');
                                return back()->withErrors($validator->errors())->withInput();
                            }
                        }
                    }

                    $nomor = $this->generateNomor();
                    $newId = DB::table('sl_leads')->insertGetId([
                        'nomor' => $nomor,
                        'tgl_leads' => $current_date_time,
                        'nama_perusahaan' => $request->nama_perusahaan,
                        'telp_perusahaan' => $request->telp_perusahaan,
                        'jenis_perusahaan_id' => $request->jenis_perusahaan,
                        'branch_id' => $request->branch,
                        'platform_id' => $request->platform,
                        'kebutuhan_id' => $request->kebutuhan,
                        'alamat' => $request->alamat_perusahaan,
                        'pic' => $request->pic,
                        'jabatan' => $request->jabatan_pic,
                        'no_telp' => $request->no_telp,
                        'email' => $request->email,
                        'pma' => $request->pma,
                        'status_leads_id' => 1,
                        'notes' => $request->detail_leads,
                        'provinsi_id' => $request->provinsi,
                        'provinsi' => $provinsi ? $provinsi->name : null,
                        'kota_id' => $request->kota,
                        'kota' => $kota ? $kota->name : null,
                        'kecamatan_id' => $request->kecamatan,
                        'kecamatan' => $kecamatan ? $kecamatan->name : null,
                        'kelurahan_id' => $request->kelurahan,
                        'kelurahan' => $kelurahan ? $kelurahan->name : null,
                        'benua_id' => $request->benua,
                        'benua' => $benua ? $benua->nama_benua : null,
                        'negara_id' => $request->negara,
                        'negara' => $negara ? $negara->nama_negara : null,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);

                    //insert ke activity sebagai activity pertama
                    $customerActivityController = new CustomerActivityController();
                    $nomorActivity = $customerActivityController->generateNomor($newId);

                    $activityId = DB::table('sl_customer_activity')->insertGetId([
                        'leads_id' => $newId,
                        'branch_id' => $request->branch,
                        'tgl_activity' => $current_date_time,
                        'nomor' => $nomorActivity,
                        'notes' => 'Leads Terbentuk',
                        'tipe' => 'Leads',
                        'status_leads_id' => 1,
                        'is_activity' => 0,
                        'user_id' => Auth::user()->id,
                        'created_at' => $current_date_time,
                        'created_by' => Auth::user()->full_name
                    ]);

                    //cari tim sales
                    $timSalesD = DB::table('m_tim_sales_d')->where('user_id', Auth::user()->id)->first();
                    if ($timSalesD != null) {
                        DB::table('sl_leads')->where('id', $newId)->update([
                            'tim_sales_id' => $timSalesD->tim_sales_id,
                            'tim_sales_d_id' => $timSalesD->id
                        ]);

                        DB::table('sl_customer_activity')->where('id', $activityId)->update([
                            'tim_sales_id' => $timSalesD->tim_sales_id,
                            'tim_sales_d_id' => $timSalesD->id
                        ]);
                    }

                    $msgSave = 'Leads ' . $request->nama_perusahaan . ' berhasil disimpan dengan nomor : ' . $nomor . ' !';
                }
            }
            DB::commit();
            return redirect()->back()->with('success', $msgSave);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function restore(Request $request)
    {
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_leads')->where('id', $request->id)->update([
                'deleted_at' => null,
                'deleted_by' => null
            ]);

            $msgSave = 'Leads ' . $request->nama_perusahaan . ' berhasil direstore.';

            DB::commit();
            return redirect()->route('leads')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $current_date_time = Carbon::now()->toDateTimeString();
            DB::table('sl_leads')->where('id', $request->id)->update([
                'deleted_at' => $current_date_time,
                'deleted_by' => Auth::user()->full_name
            ]);

            $msgSave = 'Leads ' . $request->nama_perusahaan . ' berhasil dihapus.';

            DB::commit();
            return redirect()->route('leads')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function generateNomor()
    {
        //generate nomor 065 = A , 090 = Z , 048 = 1 , 057 = 9;

        //dapatkan dulu last leads
        $nomor = "AAAAA";

        $lastLeads = DB::table('sl_leads')->orderBy('id', 'DESC')->first();
        if ($lastLeads != null) {
            $nomor = $lastLeads->nomor;
            $chars = str_split($nomor);
            for ($i = count($chars) - 1; $i >= 0; $i--) {
                //dapatkan ascii dari character
                $ascii = ord($chars[$i]);

                if (($ascii >= 48 && $ascii < 57) || ($ascii >= 65 && $ascii < 90)) {
                    $ascii += 1;
                } else if ($ascii == 90) {
                    $ascii = 48;
                } else {
                    continue;
                }

                $ascchar = chr($ascii);
                $nomor = substr_replace($nomor, $ascchar, $i);
                break;
            }
            if (strlen($nomor) < 5) {
                $jumlah = 5 - strlen($nomor);
                for ($i = 0; $i < $jumlah; $i++) {
                    $nomor = $nomor . "A";
                }
            }
        }

        return $nomor;
    }

    public function generateNomorLanjutan($nomor)
    {
        //generate nomor 065 = A , 090 = Z , 048 = 1 , 057 = 9;

        //dapatkan dulu last leads
        // $nomor = "AAAAA";

        $chars = str_split($nomor);
        for ($i = count($chars) - 1; $i >= 0; $i--) {
            //dapatkan ascii dari character
            $ascii = ord($chars[$i]);

            if (($ascii >= 48 && $ascii < 57) || ($ascii >= 65 && $ascii < 90)) {
                $ascii += 1;
            } else if ($ascii == 90) {
                $ascii = 48;
            } else {
                continue;
            }

            $ascchar = chr($ascii);
            $nomor = substr_replace($nomor, $ascchar, $i);
            break;
        }
        if (strlen($nomor) < 5) {
            $jumlah = 5 - strlen($nomor);
            for ($i = 0; $i < $jumlah; $i++) {
                $nomor = $nomor . "A";
            }
        }

        return $nomor;
    }

    public function import(Request $request)
    {
        $now = Carbon::now()->isoFormat('DD MMMM Y');

        return view('sales.leads.import', compact('now'));
    }

    public function inquiryImport(Request $request)
    {
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
            $datas = [];
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            } else {
                $file = $request->file('file');
                $current_date_time = Carbon::now()->toDateTimeString();

                // Get the csv rows as an array
                $array = Excel::toArray(new stdClass(), $file);
                $jumlahError = 0;
                $jumlahWarning = 0;
                $jumlahSuccess = 0;
                $data = [];
                foreach ($array as $key => $v) {
                    foreach ($v as $keyd => $value) {
                        if ($keyd == 0) {
                            continue;
                        }
                        ;
                        if ($value[0] == null && $value[1] == null && $value[2] == null && $value[3] == null && $value[4] == null && $value[5] == null && $value[6] == null && $value[7] == null && $value[8] == null && $value[9] == null && $value[10] == null && $value[11] == null && $value[12] == null) {
                            continue;
                        }

                        $value[15] = "";
                        $value[16] = 1; // status : 1 success,2 warning , 3 error
                        //convert date
                        $UNIX_DATE = Carbon::now()->toDateString();
                        try {
                            $UNIX_DATE = ($value[1] - 25569) * 86400;
                            $UNIX_DATE = gmdate("Y-m-d", $UNIX_DATE);
                        } catch (\Throwable $th) {
                        }
                        $value[1] = $UNIX_DATE;

                        //Cek Data Master
                        $lbranch = DB::connection('mysqlhris')->table('m_branch')->where('name', $value[10])->first();
                        $lplatform = DB::table('m_platform')->where('nama', $value[11])->first();
                        $lkebutuhan = DB::table('m_kebutuhan')->where('nama', $value[9])->first();
                        $lJenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->where('nama', $value[3])->first();
                        $ltimSalesD = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('username', $value[14])->first();

                        if ($lbranch == null) {
                            $value[10] = "";
                            if ($value[15] != "") {
                                $value[15] .= " , ";
                                $value[16] = 2;
                            }
                            $value[15] .= "Wilayah Tidak ditemukan";
                        }

                        if ($lplatform == null) {
                            $value[11] = "";
                            if ($value[15] != "") {
                                $value[15] .= " , ";
                                $value[16] = 2;
                            }
                            $value[15] .= "Sumber Leads Tidak ditemukan";
                        }

                        if ($lkebutuhan == null) {
                            $value[9] = "";
                            if ($value[15] != "") {
                                $value[15] .= " , ";
                                $value[16] = 2;
                            }
                            $value[15] .= "Kebutuhan Tidak ditemukan";
                        }

                        if ($value[3] != null && $value[3] != "" && $value[3] != "-") {
                            if ($lJenisPerusahaan == null) {
                                DB::table('m_jenis_perusahaan')->insert([
                                    'nama' => $value[3],
                                    'resiko' => "",
                                    'created_at' => $current_date_time,
                                    'created_by' => Auth::user()->full_name
                                ]);
                            } else {
                                if ($value[15] != "") {
                                    $value[15] .= " , ";
                                    $value[16] = 2;
                                }
                                $value[15] .= "Jenis Perusahaan Tidak ditemukan";
                            }
                        }

                        if ($ltimSalesD == null) {
                            $value[14] = "";
                            if ($value[15] != "") {
                                $value[15] .= " , ";
                                $value[16] = 2;
                            }
                            $value[15] .= "Tim Sales Tidak ditemukan";
                        }

                        if ($value[15] == "") {
                            $jumlahSuccess++;
                        } else {
                            $jumlahWarning++;
                        }

                        array_push($data, $value);
                    }
                }
                array_push($datas, $data);
            }
            $now = Carbon::now()->isoFormat('DD MMMM Y');
            DB::commit();
            return view('sales.leads.inquiry', compact('datas', 'now', 'jumlahError', 'jumlahSuccess', 'jumlahWarning'));
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function saveImport(Request $request)
    {
        DB::beginTransaction();

        try {
            $current_date_time = Carbon::now()->toDateTimeString();
            $datas = $request->value;

            $msgSave = '';

            $nomor = "";

            foreach ($datas as $key => $value) {
                if ($key == 0) {
                    $nomor = $this->generateNomor();
                } else {
                    $nomor = $this->generateNomorLanjutan($nomor);
                }

                $data = explode("||", $value);

                $dtgl = $data[1];
                $dperusahaan = $data[2];
                $djenisPerusahaan = $data[3];
                $dnoTelpPerusahaan = $data[4];
                $dpic = $data[5];
                $djabatanPic = $data[6];
                $dnoTelpPic = $data[7];
                $demailPic = $data[8];
                $dkebutuhan = $data[9];
                $dbranch = $data[10];
                $dsumberLeads = $data[11];
                $dalamat = $data[12];
                $dketerangan = $data[13];
                $dSales = $data[14];

                $lbranch = DB::connection('mysqlhris')->table('m_branch')->where('name', $dbranch)->first();
                $lplatform = DB::table('m_platform')->where('nama', $dsumberLeads)->first();
                $lkebutuhan = DB::table('m_kebutuhan')->where('nama', $dkebutuhan)->first();
                $lJenisPerusahaan = DB::table('m_jenis_perusahaan')->whereNull('deleted_at')->where('nama', $djenisPerusahaan)->first();
                $ltimSalesD = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('username', $dSales)->first();

                $branch = null;
                if ($lbranch != null) {
                    $branch = $lbranch->id;
                }

                $platform = null;
                if ($lplatform != null) {
                    $platform = $lplatform->id;
                }

                $kebutuhan = null;
                if ($lkebutuhan != null) {
                    $kebutuhan = $lkebutuhan->id;
                }

                $jenisPerusahaan = null;
                if ($lJenisPerusahaan != null) {
                    $jenisPerusahaan = $lJenisPerusahaan->id;
                }

                $timSalesD = null;
                $timSales = null;
                if ($ltimSalesD != null) {
                    $timSalesD = $ltimSalesD->id;
                    $timSales = $ltimSalesD->tim_sales_id;
                }

                $newId = DB::table('sl_leads')->insertGetId([
                    'nomor' => $nomor,
                    'tgl_leads' => $dtgl,
                    'nama_perusahaan' => $dperusahaan,
                    'telp_perusahaan' => $dnoTelpPerusahaan,
                    'jenis_perusahaan_id' => $jenisPerusahaan,
                    'branch_id' => $branch,
                    'platform_id' => 99,
                    'kebutuhan_id' => $kebutuhan,
                    'alamat' => $dalamat,
                    'notes' => $dketerangan,
                    'pic' => $dpic,
                    'jabatan' => $djabatanPic,
                    'no_telp' => $dnoTelpPic,
                    'email' => $demailPic,
                    'status_leads_id' => 1,
                    'tim_sales_id' => $timSales,
                    'tim_sales_d_id' => $timSalesD,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
                //insert ke activity sebagai activity pertama
                $customerActivityController = new CustomerActivityController();
                $nomorActivity = $customerActivityController->generateNomor($newId);

                $activityId = DB::table('sl_customer_activity')->insertGetId([
                    'leads_id' => $newId,
                    'branch_id' => $branch,
                    'tgl_activity' => $current_date_time,
                    'nomor' => $nomorActivity,
                    'notes' => 'Leads Import',
                    'tipe' => 'Leads',
                    'status_leads_id' => 1,
                    'is_activity' => 0,
                    'user_id' => Auth::user()->id,
                    'created_at' => $current_date_time,
                    'created_by' => Auth::user()->full_name
                ]);
            }

            $msgSave = 'Import Leads berhasil Dilakukan !';

            DB::commit();
            return redirect()->route('leads')->with('success', $msgSave);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function templateImport(Request $request)
    {
        $dt = Carbon::now()->toDateTimeString();

        return Excel::download(new LeadsTemplateExport(), 'Template Import Leads-' . $dt . '.xlsx');
    }

    public function exportExcel(Request $request)
    {
        $dt = Carbon::now()->toDateTimeString();

        return Excel::download(new LeadsExport(), 'Leads-' . $dt . '.xlsx');
    }

    public function childLeads(Request $request)
    {
        return DB::table('sl_leads')
            ->whereNull('deleted_at')
            ->where(function ($query) use ($request) {
                $query->where('leads_id', $request->id)
                    ->orWhere('id', $request->id);
            })
            ->OrderBy('id', 'asc')
            ->get();
    }


    public function availableLeads(Request $request)
    {
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();
            $data = DB::table('sl_leads')
                ->join('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
                ->leftJoin($db2 . '.m_branch', 'sl_leads.branch_id', '=', $db2 . '.m_branch.id')
                ->leftJoin('m_platform', 'sl_leads.platform_id', '=', 'm_platform.id')
                ->leftJoin('m_kebutuhan', 'sl_leads.kebutuhan_id', '=', 'm_kebutuhan.id')
                ->leftJoin('m_tim_sales', 'sl_leads.tim_sales_id', '=', 'm_tim_sales.id')
                ->leftJoin('m_tim_sales_d', 'sl_leads.tim_sales_d_id', '=', 'm_tim_sales_d.id')
                ->select('sl_leads.email', 'sl_leads.branch_id', 'm_tim_sales_d.user_id', 'sl_leads.ro', 'sl_leads.crm', 'm_tim_sales.nama as tim_sales', 'm_tim_sales_d.nama as sales', 'sl_leads.tim_sales_id', 'sl_leads.tim_sales_d_id', 'sl_leads.status_leads_id', 'sl_leads.id', 'sl_leads.tgl_leads', 'sl_leads.nama_perusahaan', 'm_kebutuhan.nama as kebutuhan', 'sl_leads.pic', 'sl_leads.no_telp', 'sl_leads.email', 'm_status_leads.nama as status', $db2 . '.m_branch.name as branch', 'm_platform.nama as platform', 'm_status_leads.warna_background', 'm_status_leads.warna_font')
                ->whereNull('sl_leads.deleted_at');
            // dd($data);
            //divisi sales
            if (in_array(Auth::user()->role_id, [29, 30, 31, 32, 33])) {
                // sales
                if (Auth::user()->role_id == 29) {
                    $data = $data->where('m_tim_sales_d.user_id', Auth::user()->id);
                } else if (Auth::user()->role_id == 30) {
                }
                // spv sales
                else if (Auth::user()->role_id == 31) {
                    $tim = DB::table('m_tim_sales_d')->where('user_id', Auth::user()->id)->first();
                    $memberSales = [];
                    $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id', $tim->tim_sales_id)->get();
                    foreach ($sales as $key => $value) {
                        array_push($memberSales, $value->user_id);
                    }
                    $data = $data->whereIn('m_tim_sales_d.user_id', $memberSales);
                }
                // Asisten Manager Sales , Manager Sales
                else if (Auth::user()->role_id == 32 || Auth::user()->role_id == 33) {

                }
            }
            //divisi RO
            else if (in_array(Auth::user()->role_id, [6, 8])) {
                if (in_array(Auth::user()->role_id, [999])) {
                    $data = $data->where('sl_leads.ro_id', Auth::user()->id);
                } else if (in_array(Auth::user()->role_id, [4, 5, 6, 8])) {

                }
            }
            //divisi crm
            else if (in_array(Auth::user()->role_id, [54, 55, 56])) {
                if (in_array(Auth::user()->role_id, [54])) {
                    $data = $data->where('sl_leads.crm_id', Auth::user()->id);
                } else if (in_array(Auth::user()->role_id, [55, 56])) {

                }
            }
            ;

            $data = $data->get();


            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d', $value->tgl_leads)->isoFormat('D MMMM Y');
                $value->salesEmail = "";
                // if($value->user_id != null){
                //     $salesUser = DB::connection('mysqlhris')->table('m_user')->where('id',$value->user_id)->first();
                //     if($salesUser !=null){
                //         $value->salesEmail = $salesUser->email;
                //     }
                // }

                // cari branch manager dari m_branch mysqlhris dimana branch_id = branch_id leads dan role = 52
                // $branchManager = DB::connection('mysqlhris')->table('m_user')->where('role_id',52)->where('branch_id',$value->branch_id)->first();
                $value->branchManagerEmail = "";
                $value->branchManager = "";
                // if($branchManager !=null){
                //     $value->branchManagerEmail = $branchManager->email;
                //     $value->branchManager = $branchManager->full_name;
                // }

            }
            return DataTables::of($data)
                ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function availableQuotation(Request $request)
    {
        try {
            $db2 = DB::connection('mysqlhris')->getDatabaseName();

            $data = DB::table('sl_leads')
                ->join('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
                ->leftJoin($db2 . '.m_branch', 'sl_leads.branch_id', '=', $db2 . '.m_branch.id')
                ->leftJoin('m_platform', 'sl_leads.platform_id', '=', 'm_platform.id')
                ->leftJoin('m_kebutuhan', 'sl_leads.kebutuhan_id', '=', 'm_kebutuhan.id')
                ->leftJoin('m_tim_sales', 'sl_leads.tim_sales_id', '=', 'm_tim_sales.id')
                ->leftJoin('m_tim_sales_d', 'sl_leads.tim_sales_d_id', '=', 'm_tim_sales_d.id')
                ->select('sl_leads.ro', 'sl_leads.crm', 'm_tim_sales.nama as tim_sales', 'm_tim_sales_d.nama as sales', 'sl_leads.tim_sales_id', 'sl_leads.tim_sales_d_id', 'sl_leads.status_leads_id', 'sl_leads.id', 'sl_leads.tgl_leads', 'sl_leads.nama_perusahaan', 'm_kebutuhan.nama as kebutuhan', 'sl_leads.pic', 'sl_leads.no_telp', 'sl_leads.email', 'm_status_leads.nama as status', $db2 . '.m_branch.name as branch', 'm_platform.nama as platform', 'm_status_leads.warna_background', 'm_status_leads.warna_font')
                ->whereNull('sl_leads.deleted_at')
                ->whereNull('sl_leads.leads_id');

            //divisi sales
            if (in_array(Auth::user()->role_id, [29, 30, 31, 32, 33])) {
                // sales
                if (Auth::user()->role_id == 29) {
                    $data = $data->where('m_tim_sales_d.user_id', Auth::user()->id);
                } else if (Auth::user()->role_id == 30) {
                }
                // spv sales
                else if (Auth::user()->role_id == 31) {
                    $tim = DB::table('m_tim_sales_d')->where('user_id', Auth::user()->id)->first();
                    $memberSales = [];
                    $sales = DB::table('m_tim_sales_d')->whereNull('deleted_at')->where('tim_sales_id', $tim->tim_sales_id)->get();
                    foreach ($sales as $key => $value) {
                        array_push($memberSales, $value->user_id);
                    }
                    $data = $data->whereIn('m_tim_sales_d.user_id', $memberSales);
                }
                // Asisten Manager Sales , Manager Sales
                else if (Auth::user()->role_id == 32 || Auth::user()->role_id == 33) {

                }
            }
            //divisi RO
            else if (in_array(Auth::user()->role_id, [4, 5, 6, 8])) {
                if (in_array(Auth::user()->role_id, [4, 5])) {
                    $data = $data->where('sl_leads.ro_id', Auth::user()->id);
                } else if (in_array(Auth::user()->role_id, [6, 8])) {

                }
            }
            //divisi crm
            else if (in_array(Auth::user()->role_id, [54, 55, 56])) {
                if (in_array(Auth::user()->role_id, [54])) {
                    $data = $data->where('sl_leads.crm_id', Auth::user()->id);
                } else if (in_array(Auth::user()->role_id, [55, 56])) {

                }
            }
            ;

            $data = $data->get();


            foreach ($data as $key => $value) {
                $value->tgl = Carbon::createFromFormat('Y-m-d', $value->tgl_leads)->isoFormat('D MMMM Y');
            }
            return DataTables::of($data)
                ->make(true);
        } catch (\Exception $e) {
            dd($e);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function saveChildLeads(Request $request)
    {
        try {
            DB::beginTransaction();
            $current_date_time = Carbon::now()->toDateTimeString();
            $nomor = $this->generateNomor();
            $leadsParent = DB::table('sl_leads')->where('id', $request->leads_id)->first();

            $newId = DB::table('sl_leads')->insertGetId([
                'nomor' => $nomor,
                'leads_id' => $leadsParent->id,
                'tgl_leads' => $current_date_time,
                'nama_perusahaan' => $request->nama_perusahaan,
                'telp_perusahaan' => $leadsParent->telp_perusahaan,
                'jenis_perusahaan_id' => $leadsParent->jenis_perusahaan_id,
                'branch_id' => $leadsParent->branch_id,
                'platform_id' => 8,
                'kebutuhan_id' => $leadsParent->kebutuhan_id,
                'alamat' => $leadsParent->alamat,
                'pic' => $leadsParent->pic,
                'jabatan' => $leadsParent->jabatan,
                'no_telp' => $leadsParent->no_telp,
                'email' => $leadsParent->email,
                'status_leads_id' => 1,
                'notes' => $leadsParent->notes,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            //insert ke activity sebagai activity pertama
            $customerActivityController = new CustomerActivityController();
            $nomorActivity = $customerActivityController->generateNomor($newId);

            $activityId = DB::table('sl_customer_activity')->insertGetId([
                'leads_id' => $newId,
                'branch_id' => $leadsParent->branch_id,
                'tgl_activity' => $current_date_time,
                'nomor' => $nomorActivity,
                'notes' => 'Leads Terbentuk',
                'tipe' => 'Leads',
                'status_leads_id' => 1,
                'is_activity' => 0,
                'user_id' => Auth::user()->id,
                'created_at' => $current_date_time,
                'created_by' => Auth::user()->full_name
            ]);

            if (Auth::user()->role_id == 29) {
                //cari tim sales
                $timSalesD = DB::table('m_tim_sales_d')->where('user_id', Auth::user()->id)->first();
                if ($timSalesD != null) {
                    DB::table('sl_leads')->where('id', $newId)->update([
                        'tim_sales_id' => $timSalesD->tim_sales_id,
                        'tim_sales_d_id' => $timSalesD->id
                    ]);

                    DB::table('sl_customer_activity')->where('id', $activityId)->update([
                        'tim_sales_id' => $timSalesD->tim_sales_id,
                        'tim_sales_d_id' => $timSalesD->id
                    ]);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Leads ' . $request->nama_perusahaan . ' berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            SystemController::saveError($e, Auth::user(), $request);
            abort(500);
        }
    }

    public function getKota($provinsiId)
    {
        $kota = DB::connection('mysqlhris')->table('m_city')->where('province_id', $provinsiId)->get();
        return response()->json($kota);
    }
    public function getKecamatan($kotaId)
    {
        $kecamatan = DB::connection('mysqlhris')->table('m_district')->where('city_id', $kotaId)->get();
        return response()->json($kecamatan);
    }

    public function getKelurahan($kecamatanId)
    {
        $kelurahan = DB::connection('mysqlhris')->table('m_village')->where('district_id', $kecamatanId)->get();
        return response()->json($kelurahan);
    }

    public function leadsBelumAktif(Request $request)
    {
        $arrData = [];

        $data = DB::table('sl_leads')
            ->join('m_status_leads', 'sl_leads.status_leads_id', '=', 'm_status_leads.id')
            ->leftJoin('m_kebutuhan', 'sl_leads.kebutuhan_id', '=', 'm_kebutuhan.id')
            ->leftJoin('m_platform', 'sl_leads.platform_id', '=', 'm_platform.id')
            ->select('sl_leads.id as aksi', 'sl_leads.created_by as dibuat_oleh', 'sl_leads.tgl_leads', 'sl_leads.nama_perusahaan', 'sl_leads.pic', 'sl_leads.no_telp', 'sl_leads.email', 'm_status_leads.nama as status', 'm_kebutuhan.nama as kebutuhan', 'm_platform.nama as platform', 'sl_leads.id')
            ->whereNull('sl_leads.deleted_at')
            ->whereNull('sl_leads.is_aktif')
            ->get();

        foreach ($data as $key => $value) {
            $value->tgl_leads = Carbon::createFromFormat('Y-m-d', $value->tgl_leads)->isoFormat('D MMMM Y');
        }

        return DataTables::of($data)
            ->editColumn('aksi', function ($data) {
                return '<span class="mdi mdi-text-box-check text-primary" style="cursor: pointer; font-size: 24px; display: flex; justify-content: center; align-items: center;" onclick="aktifkanLeads(' . $data->id . ')"></span>';
                return "";
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function getNegara($benuaId)
    {
        $negara = DB::table('m_negara')->where('id_benua', $benuaId)->get();
        return response()->json($negara);
    }

    public function generateNullKode()
    {
        try {
            $leads = DB::table('sl_leads')->whereNull('nomor')->whereNull('deleted_at')->get();
            $nomor = "";
            foreach ($leads as $key => $lead) {
                if ($key == 0) {
                    $nomor = $this->generateNomor();
                } else {
                    $nomor = $this->generateNomorLanjutan($nomor);
                }
                DB::table('sl_leads')->where('id', $lead->id)->update([
                    'nomor' => $nomor
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Nomor berhasil digenerate untuk semua leads yang belum memiliki nomor.']);
        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), request());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    // public function group(Request $request)
    // {
    //     // Ambil ID grup, atau buat grup baru jika pilih "__new__"
    //     $groupId = $request->perusahaan_group_id;

    //     if ($groupId === '__new__') {
    //         $groupId = DB::table('sl_perusahaan_groups')->insertGetId([
    //             'nama_grup' => $request->new_nama_grup,
    //             'created_at' => now(),
    //             'created_by' => auth()->user()->name ?? 'system',
    //             'update_at' => now(),
    //             'update_by' => auth()->user()->name ?? 'system',
    //         ]);
    //     }

    //     // Simpan nama perusahaan ke detail grup
    //     DB::table('sl_perusahaan_groups_d')->insert([
    //         'group_id' => $groupId,
    //         'nama_perusahaan' => $request->nama_perusahaan,
    //         'created_at' => now(),
    //         'created_by' => auth()->user()->name ?? 'system',
    //         'update_at' => now(),
    //         'update_by' => auth()->user()->name ?? 'system',
    //     ]);

    //     return redirect()->back()->with('success', 'Perusahaan berhasil dimasukkan ke grup.');
    // }

}

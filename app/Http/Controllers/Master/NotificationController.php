<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class NotificationController extends Controller
{
    /**
     * List semua notifikasi (yang belum dihapus)
     */
    public function list()
    {
        return view('master.notification.list');
    }

    /**
     * Data untuk Yajra DataTables
     */
    public function data(Request $request)
    {
        try {
            \Log::info('Datatables request received for notifications', $request->all());

            $data = DB::table('notifications')
                ->select(
                    'id',
                    'jenis',
                    DB::raw('`to` as tujuan'), // alias untuk kolom to
                    'title',
                    'body',
                    'lampiran',
                    'doc_type',
                    'doc_id',
                    'kirim_pada',
                    'created_by',
                    'status',
                    'created_at'
                )
                ->whereNull('deleted_at');

            return DataTables::of($data)
                ->addColumn('aksi', function ($row) {
                    return '
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '">
                                <i class="mdi mdi-trash-can"></i> Delete
                            </button>
                        </div>
                    ';
                })
                ->editColumn('kirim_pada', function ($row) {
                    return $row->kirim_pada
                        ? \Carbon\Carbon::parse($row->kirim_pada)->format('d/m/Y H:i:s')
                        : '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at
                        ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s')
                        : '-';
                })
                ->editColumn('lampiran', function ($row) {
                    if ($row->lampiran) {
                        $lampiran = json_decode($row->lampiran, true);
                        if (is_array($lampiran) && count($lampiran) > 0) {
                            $fileLinks = '';
                            foreach ($lampiran as $file) {
                                $fileLinks .= '<a href="' . asset($file['url']) . '" target="_blank" class="badge bg-primary me-1 text-decoration-none">' . $file['original_name'] . '</a>';
                            }
                            return $fileLinks;
                        }
                    }
                    return '<span class="badge bg-secondary">No files</span>';
                })
                ->rawColumns(['aksi', 'lampiran'])
                ->make(true);

        } catch (\Exception $e) {
            SystemController::saveError($e, Auth::user(), $request);
            abort(500, 'Internal Server Error');
        }
    }

    /**
     * Form input notifikasi
     */
    public function create()
    {
        return view('master.notification.create');
    }

    /**
     * Simpan notifikasi ke DB + langsung kirim email
     */
    public function save(Request $request)
    {
        try {
            $request->validate([
                'jenis' => 'required|string',
                'to' => 'required|email',
                'title' => 'required|string',
                'body' => 'required|string',
                'kirim_pada' => 'required|date',
                'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
            ]);

            $notifications = [
                'jenis' => $request->jenis,
                'to' => $request->to,
                'title' => $request->title,
                'body' => $request->body,
                'kirim_pada' => $request->kirim_pada,
                'created_by' => Auth::user()->full_name,
                'created_at' => now(),
                'status' => 'pending'
            ];

            // Handle multiple file uploads
            $attachments = [];
            $filePaths = []; // untuk menyimpan path file yang akan di-attach ke email

            if ($request->hasFile('files')) {
                // Buat folder berdasarkan tahun-bulan
                $dateFolder = now()->format('Y/m');
                $uploadPath = "uploads/lampiran_notification/{$dateFolder}";

                // Pastikan direktori ada
                if (!file_exists(public_path($uploadPath))) {
                    mkdir(public_path($uploadPath), 0755, true);
                }

                foreach ($request->file('files') as $file) {
                    // Ambil informasi file sebelum dipindah
                    $originalName = $file->getClientOriginalName();
                    $fileSize = $file->getSize();
                    $mimeType = $file->getMimeType();

                    // Generate unique filename
                    $fileName = time() . '_' . uniqid() . '_' . str_replace(' ', '_', $originalName);

                    // Pindahkan file ke public/upload/lampiran_notification/YYYY/MM/
                    $file->move(public_path($uploadPath), $fileName);

                    // Simpan informasi file dalam format yang lebih ringkas
                    $fileUrl = $uploadPath . '/' . $fileName;
                    $attachments[] = [
                        'original_name' => $originalName,
                        'url' => $fileUrl,
                        'size' => $fileSize
                    ];

                    // Simpan path lengkap untuk email attachment
                    $filePaths[] = [
                        'path' => public_path($fileUrl),
                        'name' => $originalName,
                        'mime' => $mimeType
                    ];
                }

                $notifications['lampiran'] = json_encode($attachments);
            }

            $id = DB::table('notifications')->insertGetId($notifications);

            // Send email
            Mail::raw($request->body, function ($message) use ($request, $filePaths) {
                $message->to($request->to)
                    ->subject($request->title);

                // Attach files if any
                foreach ($filePaths as $attachment) {
                    if (file_exists($attachment['path'])) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'],
                            'mime' => $attachment['mime']
                        ]);
                    }
                }
            });

            // Update status to sent
            DB::table('notifications')
                ->where('id', $id)
                ->update([
                    'sent_at' => now(),
                    'status' => 'sent'
                ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil disimpan dan email terkirim!'
                ]);
            }

            return redirect()->route('notifications.list')
                ->with('success', 'Notifikasi berhasil disimpan dan email terkirim!');

        } catch (\Exception $e) {
            \Log::error('Error saving notification: ' . $e->getMessage());

            // Update status to failed if notification was created
            if (isset($id)) {
                DB::table('notifications')
                    ->where('id', $id)
                    ->update(['status' => 'failed']);
            }

            // Hapus file yang sudah diupload jika terjadi error
            if (isset($attachments) && !empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $filePath = public_path($attachment['url']);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal kirim email: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal kirim email: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Soft delete notifikasi
     */
    public function destroy($id)
    {
        try {
            // Ambil data notifikasi untuk hapus file jika ada
            $notification = DB::table('notifications')->where('id', $id)->first();

            if ($notification && $notification->lampiran) {
                $attachments = json_decode($notification->lampiran, true);
                if (is_array($attachments)) {
                    foreach ($attachments as $attachment) {
                        $filePath = public_path($attachment['url']);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }

            DB::table('notifications')
                ->where('id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => Auth::user()->full_name,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi!'
            ], 500);
        }
    }
}
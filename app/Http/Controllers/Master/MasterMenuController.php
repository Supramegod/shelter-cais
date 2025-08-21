<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Bisa tambahkan logic tambahan di sini jika perlu
        // Misalnya: count data, statistik, dll
        
        return view('master.master-menu');
    }
}
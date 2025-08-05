<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\SystemController;

class AuthController extends Controller
{
    /**
     * Instantiate a new AuthController instance.
     */

    public function __construct(SystemController $systemController)
    {
        parent::__construct();
        $this->SystemController = $systemController;
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $error = [];
        if(empty($request->username)){
            $error['username'] = 'Masukkan username';
        };
        if(empty($request->password)){
            $error['password'] = 'Masukkan password';
        };

        if(!empty($error)){
            return back()->withErrors($error)->withInput();
        }

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $user = User::where('username',($credentials['username']))->where('password',(md5('SHELTER-'.$credentials['password'].'-SHELTER')))->first();
            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();

                //insert ke login log
                $user = DB::connection('mysqlhris')->table('m_user')->where('username',$request->username)->first();

                DB::table('log_users_login')->insert([
                    'login_at'      => Carbon::now()->toDateTimeString(),
                    'username'      => $request->username,
                    'branch_id'     => $user->branch_id,
                    'is_success'    => 1,
                    'ip'            => $request->ip(),
                    'user_agent'    => $request->header('user_agent'),
                    'header_data'   => json_encode($request->header())
                ]);

                // $arrRole = [2,48,98,99];
                // if(!in_array($dataUser->role_id,$arrRole)){
                //     return back()->withErrors([
                //         'username' => "User dengan Role anda belum bisa masuk ke Aplikasi CAIS , silahkan hubungi IT",
                //     ])->onlyInput('username');
                // }

                return redirect()->route('dashboard')
                    ->withSuccess('Berhasil Login !');

            }

            //insert ke login log
            DB::table('log_users_login')->insert([
                'login_at'      => Carbon::now()->toDateTimeString(),
                'username'      => $request->username,
                'is_success'    => 0,
                'ip'            => $request->ip(),
                'user_agent'    => $request->header('user_agent'),
                'header_data'   => json_encode($request->header())
            ]);

            return back()->withErrors([
                'username' => 'Username atau password tidak ditemukan.',
            ])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors([
                'username' => "Terdapat kesalahan dengan error code ".$e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if(Auth::check())
        {
            $userData = $this->SystemController->userLoginData();
            if(in_array($userData->role_id,[29,31])){
                $userData->tim_sales_id =null;
                $userData->tim_sales_d_id =null;

                $dataSalesD = DB::table('m_tim_sales_d')->where('user_id',Auth::user()->id)->whereNull('deleted_at')->first();
                if($dataSalesD !=null){
                    $userData->tim_sales_id = $dataSalesD->tim_sales_id;
                    $userData->tim_sales_d_id = $dataSalesD->id;
                }
            }

            if($userData->role_id == 56 || $userData->role_id == 55){
                return redirect()->route('dashboard-manager-crm');
            }
            return view('home.dashboard',compact('userData'));
        }

        return redirect()->route('login')
            ->withErrors([
            'username' => 'Silahkan login untuk masuk ke dashboard.',
        ])->withInput();
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }

}

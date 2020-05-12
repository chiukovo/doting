<?php

namespace App\Services;
use App\Models\Admin;

use Validator, Session, Auth;

class AdminService
{

    /**
     * 登入
     * @param $request
     * @return 
     */
    public static function login($request)
    {
        $credentials = $request->only('account', 'password');

        $attempt = Auth::attempt($credentials);

        if (!$attempt) {
            return redirect('/admin/login');
        }

        $admin = Auth::guard('admin')->user();

        //紀錄登入資訊
        $admin = Admin::find($admin->id);

        $admin->last_login = date('Y-m-d H:i:s');
        $admin->last_ip = $request->ip();
        $admin->save();

        if($admin->status !== '1'){
            Auth::logout();
            return redirect('/'.env('ADMIN_PREFIX').'/login');
        }

        return redirect('/'.env('ADMIN_PREFIX'));
    }
}
<?php

namespace App\Services;
use App\Models\Admin;

use Validator, Session, Auth;

class AdminServices
{

    /**
     * 登入
     * @param $request
     * @return 
     */
    public static function login($request)
    {
        $data = $request->all();
        $rules = ['account'=>'required', 'password'=>'required'];
        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
           $attempt = Auth::guard('admin')->attempt([
                'account'  => $data['account'],
                'password' => $data['password'],
                'status'   => '1'
            ]);

            if (!$attempt) {
                Auth::guard('admin')->logout();
                return redirect('/'.env('ADMIN_PREFIX').'/login');
            }

            $auth = Auth::guard('admin')->user();

            //紀錄登入資訊
            $admin = Admin::find($auth->id);

            $admin->last_login = date('Y-m-d H:i:s');
            $admin->last_ip = $request->ip();
            $admin->save();
        }else{
            return redirect('/'.env('ADMIN_PREFIX').'/login');
        }

        return redirect('/'.env('ADMIN_PREFIX'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AdminService;
use Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function login(Request $request)
    {
        return AdminService::login($request);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/'.env('ADMIN_PREFIX'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Curl, Log, DB;

class AnimalWebCrossingController extends Controller
{
    public function list(Request $request)
    {
        $lists = DB::table('animal')->get()->toArray();

        return view('animals.list', [
            'lists' => $lists
        ]);
    }
}
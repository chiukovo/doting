<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OtherServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class FishController extends Controller
{
    public function list(Request $request)
    {
        return view('fish.list');
    }

    public function getFishSearch(Request $request)
    {
        $result = [];
        $race = $request->input('race', []);
        $personality = $request->input('personality', []);
        $bd = $request->input('bd', []);
        $text = $request->input('text', '');
        $page = $request->input('page', 1);


        $fish = DB::table('fish');

        if ($text != '') {
           $fish->where('name', 'like', '%' . $text . '%');
        }

        $fish = $fish->select()
            ->orderBy('sell', 'desc')
            ->paginate(30)
            ->toArray();


        foreach ($fish['data'] as $key => $data) {
            $data->north = OtherServices::getFishMonth($data, '北');
            $data->south = OtherServices::getFishMonth($data, '南');

            $result[] = $data;
        }


        return $result;
    }
}
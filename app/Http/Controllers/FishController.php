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
        $text = $request->input('text', '');

        return view('fish.list', [
            'text' => $text,
        ]);
    }

    public function getFishSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');

        $fish = DB::table('fish');

        if ($text != '') {
           $fish->where('name', 'like', '%' . $text . '%');
        }

        $fish = $fish->select()
            ->orderBy('sell', 'desc')
            ->paginate(30)
            ->toArray();


        foreach ($fish['data'] as $key => $data) {
            $data->north = OtherServices::getMonthFormat($data, '北');
            $data->south = OtherServices::getMonthFormat($data, '南');

            $result[] = $data;
        }


        return $result;
    }
}
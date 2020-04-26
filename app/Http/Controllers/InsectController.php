<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OtherServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class InsectController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');

        return view('insect.list', [
            'text' => $text,
        ]);
    }

    public function getInsectSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');

        $insect = DB::table('insect');

        if ($text != '') {
           $insect->where('name', 'like', '%' . $text . '%');
        }

        $insect = $insect->select()
            ->orderBy('sell', 'desc')
            ->paginate(30)
            ->toArray();

        foreach ($insect['data'] as $key => $data) {
            $data->north = OtherServices::getMonthFormat($data, '北');
            $data->south = OtherServices::getMonthFormat($data, '南');

            $result[] = $data;
        }

        return $result;
    }
}
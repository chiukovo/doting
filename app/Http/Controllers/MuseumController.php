<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OtherServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class MuseumController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');

        return view('museum.list', [
            'text' => $text,
        ]);
    }

    public function getMuseumSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');
        $page = $request->input('page', 1);
        $target = $request->input('target', '');

        $museum = OtherServices::getDataByMessage($text, $page, false, $target);

        if (!is_array($museum)) {
            return [];
        }

        foreach ($museum as $key => $data) {
            $data->north = OtherServices::getMonthFormat($data, '北');
            $data->south = OtherServices::getMonthFormat($data, '南');

            $result[] = $data;
        }


        return $result;
    }
}
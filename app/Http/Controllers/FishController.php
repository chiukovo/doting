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

    public function detail(Request $request)
    {
        $name = $request->input('name');

        if ($name == '') {
            return redirect('/');
        }

        $detail = DB::table('fish')
            ->where('name', $name)
            ->first();

        if (is_null($detail)) {
            return redirect('/');
        }

        $detail = (array)$detail;

        //class check
        $months = range(1, 12);
        $nowMonth = date('m');

        //北
        foreach ($months as $month) {
            $class = '';

            if ($detail['m' . $month] == '全' || $detail['m' . $month] == '北') {
                $class = 'has';
            }

            if ($month == $nowMonth) {
                $class .= ' current';
            }

            $detail['n_' . $month . '_class'] = $class;
        }

        //南
        foreach ($months as $month) {
            $class = '';

            if ($detail['m' . $month] == '全' || $detail['m' . $month] == '南') {
                $class = 'has';
            }

            if ($month == $nowMonth) {
                $class .= ' current';
            }

            $detail['s_' . $month . '_class'] = $class;
        }

        $dateRange1 = range(0, 11);
        $dateRange2 = range(12, 23);

        return view('fish.detail', [
            'detail' => $detail,
            'months' => $months,
            'dateRange1' => $dateRange1,
            'dateRange2' => $dateRange2,
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
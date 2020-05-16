<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Services\OtherServices;
use App\Services\LineLoginServices;
use Curl, Log, DB;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return view('index');
    }

    public function indexData(Request $request)
    {
        //birthday
        $date = date('m.d');
        //first
        $first = substr($date, 0, 1);

        if ($first == 0) {
            $date = substr($date, 1, strlen($date));
        }

        //birthday
        $birthday = DB::table('animal')
            ->where('bd', $date)
            ->first();

        //排行榜
        $all = Redis::hGetAll('array');
        $ranking = [];
        $collection = collect($all)->sort(function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        })
        ->take(5)
        ->all();

        foreach ($collection as $text => $number) {
            $ranking[] = [
                'text' => $text,
                'number' => $number,
            ];
        }

        //first
        $month = date('m');
        $firstMonth = substr($month, 0, 1);

        if ($firstMonth == 0) {
            $month = substr($month, 1, strlen($month));
        }

        //魚
        $northFish = OtherServices::getDataByMessage('北' . $month . '月魚', '', true);
        $southFish = OtherServices::getDataByMessage('南' . $month . '月魚', '', true);

        //昆蟲
        $northInsect = OtherServices::getDataByMessage('北' . $month . '月蟲', '', true);
        $southInsect = OtherServices::getDataByMessage('南' . $month . '月蟲', '', true);

        return [
            'date' => $date,
            'birthday' => $birthday,
            'ranking' => $ranking,
            'northFish' => $northFish,
            'southFish' => $southFish,
            'northInsect' => $northInsect,
            'southInsect' => $southInsect,
        ];
    }
}
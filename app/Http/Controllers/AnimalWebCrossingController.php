<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AnimalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Curl, Log, DB, App;

class AnimalWebCrossingController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');
        $type = $request->route()->getName();
        $type = is_null($type) ? '' : $type;

        return view('animals.list', [
            'type' => $type,
            'text' => $text,
        ]);
    }

    public function detail(Request $request)
    {
        $name = $request->input('name');

        if ($name == '') {
            return redirect('animals/list');
        }

        $detail = DB::table('animal')
            ->where('name', $name)
            ->first();

        if ($detail->kk != '') {
            //format
            $detail->kk = str_replace(".", "", $detail->kk);
            $detail->kk = str_replace(" ", "_", $detail->kk);
            $detail->kk = str_replace("'", "", $detail->kk);
            $detail->kk = $detail->kk . '_Live';
        }

        if (is_null($detail)) {
            return redirect('animals/list');
        }

        //同種族
        $sameRaceArray = DB::table('animal')
            ->where('race', 'like', '%' . $detail->race . '%')
            ->where('id', '!=', $detail->id)
            ->get()
            ->toArray();

        return view('animals.detail', [
            'detail' => $detail,
            'sameRaceArray' => $sameRaceArray,
        ]);
    }

    public function getAnimalSearch(Request $request)
    {
        $race = $request->input('race', []);
        $personality = $request->input('personality', []);
        $bd = $request->input('bd', []);
        $text = $request->input('text', '');
        $page = $request->input('page', 1);
        $type = $request->input('type', '');

        if ($text != '') {
            $result = AnimalServices::getDataByMessage($text, $page, $type);

            if (is_array($result)) {
                return $result;
            }

            return [];
        }

        $lists = DB::table('animal');

        if ($type == 'npc') {
            $lists = $lists->where('info', '!=', '');
        }

        if (!empty($race) && is_array($race)) {
            $lists->whereIn('race', $race);
        }

        if (!empty($personality) && is_array($personality)) {
            foreach ($personality as $key => $data) {
                $lists->where('personality', 'like', '%' . $data . '%');

                if ($key != 0) {
                    $lists->orWhere('personality', 'like', '%' . $data . '%');
                }
            }
        }

        if (!empty($bd) && is_array($bd)) {
            $lists->whereIn('bd_m', $bd);
        }

        $lists = $lists->select()
            ->paginate(30)
            ->toArray();


        return $lists['data'];
    }

    public function getAllType(Request $request)
    {
        $type = $request->input('type', '');

        return AnimalServices::getAllType($type);
    }

    public function statistics(Request $request)
    {
        return view('statistics');
    }

    public function statisticsGetData(Request $request)
    {
        $all = Redis::hGetAll('array');
        $collection = collect($all)->sort(function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        })
        ->take(10)
        ->all();

        $class = App::make('App\Http\Controllers\AnimalCrossingController');

        $result = [];

        $num = 1;

        foreach ($collection as $text => $number) {
            $builder = $class->getSendBuilder($text);

            $img = '';
            $url = '';

            if (is_array($builder) && $text != '抽') {
                foreach ($builder as $detail) {
                    $build = $detail->buildMessage();

                    if (isset($build[0])) {
                        $img = $build[0]['contents']['contents'][0]['hero']['url'];
                        $url = $build[0]['contents']['contents'][0]['action']['uri'];
                    }
                }
            }

            $result[] = [
                'text' => $text,
                'number' => $number,
                'img' => $img,
                'url' => $url,
                'comment' => $this->statisticsComment($num),
            ];

            $num++;
        }

        return $result;
    }

    public function statisticsComment($number)
    {
        switch ($number) {
            case 1:
                return '看來大家抽得很開心~~ ٩(ˊᗜˋ )و';
                break;
            case 2:
                return '不意外 我就是這麼厲害 ≖‿≖';
                break;
            case 3:
                return '媽 我在這~ 〜(꒪꒳꒪)〜';
                break;
            case 4:
                return '樓上有事嗎? (-_-)';
                break;
            case 5:
                return '請繼續投我一票~~(＠゜▽゜)';
                break;
            case 6:
                return '我會加油的 (握拳';
                break;
            case 7:
                return '叫我人氣王 ´･ᴗ･`';
                break;
            case 8:
                return '首先我要感謝我的家人 (X';
                break;
            case 9:
                return '(＿ ＿*) Z z z';
                break;
            case 10:
                return '可憐娜 差點沒上榜';
                break;
        }
    }
}
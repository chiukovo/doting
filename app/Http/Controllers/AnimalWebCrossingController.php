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
                        //取得type
                        $type = mb_substr($text, 0, 1);
                        $type = $class->typeToUrl($type);

                        if (count($build) > 1) {
                            //取得字串
                            $target = mb_substr($text, 1);

                            $url = $class->getMoreText($type, $target);
                        }

                        if ($type == 'animal') {
                            $name = $build[0]['contents']['contents'][0]['body']['contents'][0]['text'];
                            $expName = explode(" ", $name);

                            $imgUrl = env('APP_URL') . '/animal/' . urlencode($expName[0]) . '_icon.png';
                            $headers = get_headers($imgUrl);
                            $code = substr($headers[0], 9, 3);

                            if ($code == 200) {
                                $img = $imgUrl;
                            }
                        }
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

    public function compatible()
    {
        return view('animals.compatible');
    }

    public function getAnimalsGroupRace()
    {
        //get all animal
        $lists = DB::table('animal')
            ->whereNull('info')
            ->get()
            ->toArray();

        $lists = collect($lists)->groupBy('race')->toArray();
        $races = collect($lists)->keys()->toArray();

        return [
            'lists' => $lists,
            'races' => $races,
        ];
    }

    //診斷
    public function analysis(Request $request)
    {
        $animalsName = $request->input('name');
        //去頭尾空白
        $animalsName = trim($animalsName);
        //去除前後空白
        $animalsName = preg_replace('/\s+/', '', $animalsName);
        $array = explode(",", $animalsName);

        //get all animal
        $lists = DB::table('animal')
            ->whereIn('name', $array)
            ->whereNull('info')
            ->get(['id', 'name', 'personality', 'sex', 'race', 'bd'])
            ->toArray();

        if (empty($lists)) {
            return redirect('animals/compatible');
        }

        foreach ($lists as $key => $list) {
            $list->constellation = constellation($list->bd);
            $lists[$key] = $list;
        }

        //媒合度
        $lists = matchmaking($lists);
    }

    public function statisticsComment($number)
    {
        switch ($number) {
            case 1:
                return '看來大家抽得很開心 ~~~ ٩(ˊᗜˋ )و';
                break;
            case 2:
                return '第二名 94棒 ≖‿≖';
                break;
            case 3:
                return '媽 我在這~ 〜(꒪꒳꒪)〜';
                break;
            case 4:
                return '樓上有事嗎? (-_-)';
                break;
            case 5:
                return '請繼續投我一票 ~~~ (＠゜▽゜)';
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
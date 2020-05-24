<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AnimalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Curl, Log, DB, App, File, Response;

class AnimalWebCrossingController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');
        $type = $request->route()->getName();
        $type = is_null($type) ? 'animal' : $type;

        return view('animals.list', [
            'type' => $type,
            'text' => $text,
        ]);
    }

    public function detail(Request $request)
    {
        $name = $request->input('name');
        $type = $request->route()->getName();

        if ($name == '') {
            return redirect('animals/list');
        }

        $detail = DB::table('animal')
            ->where('name', $name)
            ->first();

        if (is_null($detail)) {
            return redirect('animals/list');
        }

        $detail->kk_cn_name = '';

        //kk ch name
        $kk = DB::table('kk')
            ->where('name', $detail->kk)
            ->first(['cn_name']);

        if (!is_null($kk)) {
            $detail->kk_cn_name = $kk->cn_name;
        }

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

        if ($detail->colors != '' && $detail->colors != '[]') {
            $colors = json_decode($detail->colors);

            if (is_array($colors)) {
                $detail->colors = implode("、", $colors);
            }
        }

        if ($detail->styles != '' && $detail->styles != '[]') {
            $styles = json_decode($detail->styles);

            if (is_array($styles)) {
                $detail->styles = implode("、", $styles);
            }
        }

        //同種族
        $sameRaceArray = DB::table('animal')
            ->where('race', $detail->race)
            ->where('id', '!=', $detail->id)
            ->whereNull('info')
            ->get()
            ->toArray();

        $type = $detail->info == '' ? 'animal' : 'npc';
        $token = encrypt($detail->id);
        //encode id and like current
        $result = computedMainData([$detail], 'animal', $type);

        return view('animals.detail', [
            'detail' => $result[0],
            'type' => $type,
            'token' => $token,
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
        $target = $request->input('target', '');

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

        //check target
        if ($target != '') {
            $getCount = computedCount('animal', $type, true);

            switch ($target) {
                case 'like':
                    $lists->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $lists->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $lists->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $lists->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $lists = $lists->select()
            ->paginate(30)
            ->toArray();

        //encode id and like current
        $lists['data'] = computedMainData($lists['data'], 'animal', $type);

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

                            $imgUrl = env('APP_URL') . '/animal/icon/' . urlencode($expName[0]) . '.png';

                            if (file_exists(public_path('/animal/icon/' . $expName[0] . '.png'))) {
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

    public function compatiblePrint(Request $request)
    {
        $animalsName = $request->input('name', '');

        if ($animalsName != '') {
            //去頭尾空白
            $animalsName = trim($animalsName);
            //去除前後空白
            $animalsName = preg_replace('/\s+/', '', $animalsName);
            $array = explode(",", $animalsName);
        }

        return view('animals.compatiblePrint', [
            'animalsName' => $animalsName,
            'token' => encrypt(env('APP_KEY') . 'chiuko0121'),
        ]);
    }

    public function compatibleImage(Request $request)
    {
        $date = $request->input('date');
        $image = $request->input('image');

        if ($date == '' || $image == '') {
            return;
        }

        $filePath = storage_path('print') . '/' . $date . '/' . $image . '.jpg';

        if (!File::exists($filePath)) {
             return '';
        }

        $file = File::get($filePath);
        $type = File::mimeType($filePath);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function saveImg(Request $request)
    {
        $params = $request->input('params');
        $token = $request->input('token');
        $data = $request->input('data');
        $date = date('Y-m-d');

        if ($params == '' && $data == '') {
            return 'fail params';
        }

        $decode = decrypt($token);
        $checkDecode = env('APP_KEY') . 'chiuko0121';

        if ($decode != $checkDecode) {
            return 'fail token';
        }

        $image = str_replace('data:image/png;base64,', '', $request->input('data'));
        $image = str_replace(' ', '+', $image);

        $imageName = md5($params . $token . env('APP_KEY')) . '.jpg';

        $path = storage_path('print/' . $date);

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        File::put($path . '/' . $imageName, base64_decode($image));

        return 'success';
    }

    public function compatible(Request $request)
    {
        $animalsName = $request->input('name', '');

        if ($animalsName != '') {
            //去頭尾空白
            $animalsName = trim($animalsName);
            //去除前後空白
            $animalsName = preg_replace('/\s+/', '', $animalsName);
            $array = explode(",", $animalsName);
        }

        return view('animals.compatible', [
            'animalsName' => $animalsName
        ]);
    }

    public function getAnimalsGroupRace(Request $request)
    {
        $name = $request->input('name', '');

        //get all animal
        $lists = DB::table('animal');

        if ($name != '') {
            $lists->where('name', 'like', '%' . $name . '%');
        }

        $lists = $lists
            ->where('name', '!=', '豆丁')
            ->whereNull('info')
            ->get()
            ->toArray();

        foreach ($lists as $key => $value) {
            $value->show = true;
            $lists[$key] = $value;
        }


        $personality = collect($lists)->groupBy('personality')->keys()->toArray();
        $lists = collect($lists)->groupBy('race')->toArray();
        $races = collect($lists)->keys()->toArray();

        return [
            'lists' => $lists,
            'races' => $races,
            'personality' => $personality,
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
        $names = $animalsName;
        $array = explode(",", $animalsName);

        //get all animal
        $lists = DB::table('animal')
            ->whereIn('name', $array)
            ->whereNull('info')
            ->get()
            ->toArray();

        if (empty($lists)) {
            return redirect('animals/compatible');
        }

        //判斷是否 > 20
        if (count($lists) > 20) {
            $lists = collect($lists)->filter(function ($item, $key) {
                if ($key <= 19) {
                    return $item;
                }
            })->values()->all();
        }

        //重新排序
        $format = [];

        foreach ($array as $name) {
            foreach ($lists as $key => $list) {
                if ($list->name == $name) {
                    $format[] = $list;
                }
            }
        }

        foreach ($format as $key => $list) {
            $list->constellation = constellation($list->bd);
            $format[$key] = $list;
        }

        //媒合度
        $format = matchmaking($format, $names);

        return $format;
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
                return '我就帥';
                break;
        }
    }
}
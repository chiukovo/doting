<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use QL\QueryList;
use Curl, Log, Storage, DB, Url;

class ApiController extends Controller
{
    public function getDiy()
    {
        $url = 'https://wiki.biligame.com/dongsen/DIY%E9%85%8D%E6%96%B9';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'name' => ['td:eq(0) a', 'text'],
            'type' => ['td:eq(1)', 'text'],
            'get' => ['td:eq(2)', 'text'],
            'diy' => ['td:eq(4)', 'text'],
        ])
        ->range('#CardSelectTr tr')
        ->queryData();

        //DB DATA
        $dbData = DB::table('diy')->get()->toArray();

        foreach ($result as $data) {
            if ($data['name'] != '') {
                $isset = false;

                $name = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['name'])->asJson()->get();
                $name = $name->data->text;

                //檢查是否資料庫存在
                foreach ($dbData as $source) {
                    if ($source->name == $name) {
                        $isset = true;
                    }
                }

                if ($isset) {
                    continue;
                }

                $type = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['type'])->asJson()->get();
                $type = $type->data->text;

                $get = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['get'])->asJson()->get();
                $get = $get->data->text;

                $diy = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['diy'])->asJson()->get();
                $diy = $diy->data->text;

                //insert
                DB::table('diy')->insert([
                    'name' => $name,
                    'type' => $type,
                    'get' => $get,
                    'diy' => $diy,
                ]);

                echo 'insert: ' . $data['name'] . '<br>';
            }
        }
    }

    public function getInsectApi()
    {
        $url = 'http://e0game.com/animalcrossing/%e6%98%86%e8%9f%b2-%e5%9c%96%e9%91%91/';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['.column-1 img', 'src'],
            'name' => ['.column-3', 'text'],
            'position' => ['.column-4', 'text'],
            'time' => ['.column-5', 'text'],
            'sell' => ['.column-6', 'text'],
            'm1' => ['.column-7', 'text'],
            'm2' => ['.column-8', 'text'],
            'm3' => ['.column-9', 'text'],
            'm4' => ['.column-10', 'text'],
            'm5' => ['.column-11', 'text'],
            'm6' => ['.column-12', 'text'],
            'm7' => ['.column-13', 'text'],
            'm8' => ['.column-14', 'text'],
            'm9' => ['.column-15', 'text'],
            'm10' => ['.column-16', 'text'],
            'm11' => ['.column-17', 'text'],
            'm12' => ['.column-18', 'text'],
        ])
        ->range('#tablepress-8 .row-hover tr')
        ->queryData();

        //DB DATA
        $dbAnimal = DB::table('insect')->get()->toArray();

        //save api result
        foreach ($result as $key => $data) {
            if ($data['name'] != '') {
                $dbData = [];
                $isset = false;

                //檢查是否資料庫存在
                foreach ($dbAnimal as $source) {
                    if ($source->name == $data['name']) {
                        $isset = true;
                        $dbData = $source;
                    }
                }

                if (!$isset) {
                    //save img
                    $headers = get_headers($data['img']);
                    $code = substr($headers[0], 9, 3);
                    $imgUploadSuccess = 0;

                    if ($code == 200) {
                        $imgUploadSuccess = 1;
                        $content = file_get_contents($data['img']);
                        Storage::disk('other')->put($data['name'] . '.png', $content);
                    }

                    //insert
                    DB::table('insect')->insert([
                        'name' => $data['name'],
                        'position' => $data['position'],
                        'time' => $data['time'],
                        'sell' => $data['sell'],
                        'm1' => $data['m1'],
                        'm2' => $data['m2'],
                        'm3' => $data['m3'],
                        'm4' => $data['m4'],
                        'm5' => $data['m5'],
                        'm6' => $data['m6'],
                        'm7' => $data['m7'],
                        'm8' => $data['m8'],
                        'm9' => $data['m9'],
                        'm10' => $data['m10'],
                        'm11' => $data['m11'],
                        'm12' => $data['m12'],
                    ]);

                    echo 'insert: ' . $data['name'] . '<br>';
                }
            }
        }

        echo 'done';
    }

    public function getFishApi()
    {
        $url = 'http://e0game.com/animalcrossing/%e9%ad%9a-%e5%9c%96%e9%91%91/';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['.column-1 img', 'src'],
            'name' => ['.column-3', 'text'],
            'shadow' => ['.column-4', 'text'],
            'position' => ['.column-5', 'text'],
            'time' => ['.column-6', 'text'],
            'sell' => ['.column-7', 'text'],
            'm1' => ['.column-8', 'text'],
            'm2' => ['.column-9', 'text'],
            'm3' => ['.column-10', 'text'],
            'm4' => ['.column-11', 'text'],
            'm5' => ['.column-12', 'text'],
            'm6' => ['.column-13', 'text'],
            'm7' => ['.column-14', 'text'],
            'm8' => ['.column-15', 'text'],
            'm9' => ['.column-16', 'text'],
            'm10' => ['.column-17', 'text'],
            'm11' => ['.column-18', 'text'],
            'm12' => ['.column-19', 'text'],
        ])
        ->range('#tablepress-3 .row-hover tr')
        ->queryData();

        //DB DATA
        $dbAnimal = DB::table('fish')->get()->toArray();

        //save api result
        foreach ($result as $key => $data) {
            if ($data['name'] != '') {
                $dbData = [];
                $isset = false;

                //檢查是否資料庫存在
                foreach ($dbAnimal as $source) {
                    if ($source->name == $data['name']) {
                        $isset = true;
                        $dbData = $source;
                    }
                }

                if (!$isset) {
                    //save img
                    $headers = get_headers($data['img']);
                    $code = substr($headers[0], 9, 3);
                    $imgUploadSuccess = 0;

                    if ($code == 200) {
                        $imgUploadSuccess = 1;
                        $content = file_get_contents($data['img']);
                        Storage::disk('other')->put($data['name'] . '.png', $content);
                    }

                    //insert
                    DB::table('fish')->insert([
                        'name' => $data['name'],
                        'shadow' => $data['shadow'],
                        'position' => $data['position'],
                        'time' => $data['time'],
                        'sell' => $data['sell'],
                        'm1' => $data['m1'],
                        'm2' => $data['m2'],
                        'm3' => $data['m3'],
                        'm4' => $data['m4'],
                        'm5' => $data['m5'],
                        'm6' => $data['m6'],
                        'm7' => $data['m7'],
                        'm8' => $data['m8'],
                        'm9' => $data['m9'],
                        'm10' => $data['m10'],
                        'm11' => $data['m11'],
                        'm12' => $data['m12'],
                    ]);

                    echo 'insert: ' . $data['name'] . '<br>';
                }
            }
        }

        echo 'done';
    }

    public function getNewFishImg()
    {
        //採集
        $url = 'http://www.rbtips.com/ac-c-fish/';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['img', 'src'],
            'name' => ['.post-title', 'text'],

        ])
        ->range('.qfe_thumbnails li')
        ->queryData();

        foreach ($result as $data) {
            if ($data['name'] != '' && $data['img'] != '') {

                $fish = DB::table('fish')
                    ->where('name', $data['name'])
                    ->where('beautify_img', 0)
                    ->get()
                    ->toArray();

                if (empty($fish)) {
                    continue;
                }

                //save img
                $headers = get_headers($data['img']);
                $code = substr($headers[0], 9, 3);
                $imgUploadSuccess = 0;

                if ($code == 200) {
                    $imgUploadSuccess = 1;
                    $content = file_get_contents($data['img']);
                    Storage::disk('other')->put($data['name'] . '.webp', $content);

                    DB::table('fish')
                        ->where('name', $data['name'])
                        ->update([
                            'beautify_img' => 1,
                        ]);

                    echo 'update ' . $data['name'] . '</br>';
                }
            }
        }

        echo 'done';
    }

    public function getAnimalApi(Request $request)
    {
        //採集
        $url = 'http://e0game.com/animalcrossing/%e5%8b%95%e7%89%a9%e6%9d%91%e6%b0%91-%e5%9c%96%e9%91%91/';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['.column-1 img', 'src'],
            'name' => ['.column-2', 'text'],
            'sex' => ['.column-3', 'text'],
            'personality' => ['.column-4', 'text'],
            'race' => ['.column-5', 'text'],
            'bd' => ['.column-6', 'text'],
            'say' => ['.column-7', 'text'],

        ])
        ->range('#tablepress-29 tr')
        ->queryData();

        //DB DATA
        $dbAnimal = DB::table('animal')->get()->toArray();

        //save api result
        foreach ($result as $key => $data) {
            //圖片名稱不得為空
            if ($data['img'] != '' && $data['name'] != '') {
                $dbData = [];
                $isset = false;

                //檢查是否資料庫存在
                foreach ($dbAnimal as $source) {
                    if ($source->name == $data['name']) {
                        $isset = true;
                        $dbData = $source;
                    }
                }

                if (!$isset) {
                    //save img
                    $headers = get_headers($data['img']);
                    $code = substr($headers[0], 9, 3);
                    $imgUploadSuccess = 0;

                    if ($code == 200) {
                        $imgUploadSuccess = 1;
                        $content = file_get_contents($data['img']);
                        Storage::disk('animal')->put($data['name'] . '.png', $content);
                    }

                    $bd = explode('.', $data['bd']);
                    $sex = $data['sex'];

                    //insert
                    DB::table('animal')->insert([
                        'name' => $data['name'],
                        'sex' => $sex,
                        'bd_m' => $bd[0],
                        'bd_d' => $bd[1],
                        'img_upload_success' => $imgUploadSuccess,
                        'personality' => $data['personality'],
                        'race' => $data['race'],
                        'bd' => $data['bd'],
                        'say' => $data['say'],
                    ]);

                    echo 'insert: ' . $data['name'] . '<br>';
                }
            }
        }

        echo 'done';
    }

    public function getNewImgName()
    {
        //DB DATA
        $dbAnimal = DB::table('animal')->where('beautify_img', 0)->get()->toArray();

        foreach ($dbAnimal as $data) {
            $simplified = Curl::to('http://api.zhconvert.org/convert?converter=Simplified&text=' . $data->name)->asJson()->get();
            $target = $simplified->data->text;

            $url = 'https://wiki.biligame.com/dongsen/' . $target;
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'img' => ['.box-poke-right img', 'src'],
                'other_name' => ['.box-poke-left .box-poke .box-font:eq(5)', 'text'],
            ])
            ->range('.box-poke-big')
            ->queryData();

            if (!empty($result)) {
                $img = $result[0]['img'];
                $otherName = $result[0]['other_name'];

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);
                $imgUploadSuccess = 0;

                if ($code == 200) {
                    $imgUploadSuccess = 1;
                    $content = file_get_contents($img);
                    Storage::disk('animal')->put($data->name . '.png', $content);
                    $enName = '';
                    $jpName = '';

                    //name
                    if ($otherName != '') {
                        $otherName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($otherName));
                        //英文
                        $nameEx = explode('(英)', $otherName);
                        $enName = $nameEx[0] ?? '';


                        //日文
                        if (isset($nameEx[1])) {
                            $jpName = str_replace('(日)', '', $nameEx[1]);
                        }
                    }

                    DB::table('animal')
                        ->where('id', $data->id)
                        ->update([
                            'beautify_img' => 1,
                            'en_name' => strtolower($enName),
                            'jp_name' => $jpName,
                        ]);

                    echo 'update ' . $data->name . '</br>';
                }
            }
        }

        echo 'done';
    }
}
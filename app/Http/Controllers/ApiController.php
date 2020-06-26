<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use QL\QueryList;
use App\Services\AnimalServices;
use Curl, Log, Storage, DB, Url;

class ApiController extends Controller
{
    public function updateItems()
    {
        $ranges = range(1, 51);
        $result = Curl::to('https://api.nookplaza.net/items?category=Equippables')->asJson()->get();
        $result = $result->results;
        $transAll = [];

        $datas = DB::table('items_new')
            ->get()
            ->toArray();

        foreach ($ranges as $range) {
            //讀翻譯檔案
            $html = \File::get(public_path() . '/trans/' . $range . '.html');
            $ql = QueryList::html($html);
            $trans = $ql->rules([
                'en_name' => ['td:eq(2)', 'text'],
                'jp_name' => ['td:eq(14)', 'text'],
                'name' => ['td:eq(13)', 'text'],
            ])
            ->range('.grid-container tr')
            ->queryData();

            $transAll[] = $trans;
        }


        foreach ($result as $target) {
            //翻譯
            $name = '';
            $jpName = '';
            $insert = [];

            foreach ($transAll as $trans) {
                foreach ($trans as $tran) {
                    if (strtolower($tran['en_name']) == strtolower($target->name)) {
                        $name = $tran['name'];
                    }
                }
            }

            if ($name != '') {
                foreach ($datas as $data) {
                    if ($data->name == $name) {
                        continue;
                    }
                }

                $size = '';

                if (isset($target->content->size)) {
                    $sizeData = $target->content->size;
                    $size = (int)$sizeData->cols . 'x' . (int)$sizeData->rows;
                }

                $insert = [
                    'en_name' => strtolower($target->name),
                    'jp_name' => $jpName,
                    'name' => $name,
                    'buy' => isset($target->content->buy) ? $target->content->buy : 0,
                    'sell' => $target->content->sell,
                    'is_diy' => $target->content->dIY,
                    'customize' => isset($target->content->customize) ? $target->content->customize : false,
                    'catalog' => isset($target->content->catalog) ? $target->content->catalog : false,
                    'category' => $target->category,
                    'size' => $size,
                    'type' => 1,
                ];

                if (empty($target->variations)) {
                    if (isset($target->content->bodyColor)) {
                        $insert['color'] = $target->content->bodyColor;
                    }

                    if (isset($target->content->curtainColor)) {
                        $insert['color'] = $target->content->curtainColor;
                    }

                    $img = $target->content->image;
                    $insert['img_name'] = '';
                    $fileIsset = false;

                    $headers = get_headers($img);
                    $code = substr($headers[0], 9, 3);

                    if ($code == 200) {
                        $insert['img_name'] = $name;
                        $fileIsset = is_file(public_path('itemsNew/' .  $insert['img_name'] . '.png'));

                        if (!$fileIsset) {
                            $content = file_get_contents($img);
                            Storage::disk('itemsNew')->put($insert['img_name'] . '.png', $content);
                        }
                    }

                    if (!$fileIsset) {
                        //insert
                        DB::table('items_new')->insert($insert);

                        echo 'insert: ' . $name . '<br>';
                    }

                    continue;
                }

                foreach ($target->variations as $key => $detail) {
                    $insert['color'] = $detail->content->bodyColor;
                    $img = $detail->content->image;
                    $insert['img_name'] = '';
                    $fileIsset = false;

                    $headers = get_headers($img);
                    $code = substr($headers[0], 9, 3);
                    $code = substr($headers[0], 9, 3);

                    if ($code == 200) {
                        $insert['img_name'] = $name . '_' . $key;
                        $fileIsset = is_file(public_path('itemsNew/' .  $insert['img_name'] . '.png'));

                        if (!$fileIsset) {
                            $content = file_get_contents($img);
                            Storage::disk('itemsNew')->put($insert['img_name'] . '.png', $content);
                        }
                    }

                    if (!$fileIsset) {
                        //insert
                        DB::table('items_new')->insert($insert);

                        echo 'insert: ' . $name . '<br>';
                    }
                }
            }
        }
    }

    public function getAmiibo()
    {
        $result = Curl::to('https://animal-crossing.com/amiibo/assets/JSON/series14_en.json')->asJson()->get();
        $result = $result->series14;

        $dbData = DB::table('animal')->orderBy('id', 'desc')->get()->toArray();

        foreach ($result as $data) {
            $target = '';

            foreach ($dbData as $dt) {
                if (strtolower($dt->en_name) == strtolower($data->name)) {
                    $target = $dt;
                }
            }

            if ($target == '') {
                echo 'none: ' . $data->name . '</br>';
            } else {
                if ($dt->amiibo != '') {
                    continue;
                }

                $url = 'https://animal-crossing.com/amiibo/assets/img/cards/';
                $img = $url . $data->image;

                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);
                $number = str_pad($data->id, 3, '0',STR_PAD_LEFT);
                $name = $number . '_' . $data->name;

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('animalCard')->put($name . '.png', $content);

                    //insert
                    DB::table('animal')
                        ->where('id', $target->id)
                        ->update([
                            'amiibo' => $name,
                        ]);

                    echo 'update ' . $target->id . '</br>';
                }
            }
        }
    }

    public function transData()
    {
        $dbName = 'items_new';
        //DB DATA
        $dbData = DB::table($dbName)->orderBy('id', 'desc')->get()->toArray();
        $dbData = array_chunk($dbData, 100);

        foreach ($dbData as $data) {
            $nameData = [];
            foreach ($data as $detail) {
                $nameData[$detail->id] = $detail->name;
            }

            //get
            $url = 'https://api.zhconvert.org/convert?converter=Simplified&text=' . json_encode($nameData, JSON_UNESCAPED_UNICODE);

            $target = Curl::to($url)->asJson()->get();

            if (!isset($target->data)) {
                dd($target, $url);
            }
            $target = $target->data->text;

            $decodes = json_decode($target, true);

            foreach ($decodes as $id => $decode) {
                DB::table($dbName)
                    ->where('id', $id)
                    ->update([
                        'cn_name' => $decode,
                    ]);

                echo 'update ' . $decode . '</br>';
            }
        }
    }

    public function getExpression()
    {
        $ranges = range(1, 51);
        $result = Curl::to('https://api.nookplaza.net/items?category=Reactions')->asJson()->get();
        $result = $result->results;
        $transAll = [];

        foreach ($ranges as $range) {
            //讀翻譯檔案
            $html = \File::get(public_path() . '/trans/' . $range . '.html');
            $ql = QueryList::html($html);
            $trans = $ql->rules([
                'en_name' => ['td:eq(2)', 'text'],
                'jp_name' => ['td:eq(14)', 'text'],
                'name' => ['td:eq(13)', 'text'],
            ])
            ->range('.grid-container tr')
            ->queryData();

            $transAll[] = $trans;
        }

        foreach ($result as $target) {
            //翻譯
            $name = '';
            $jpName = '';
            $obtainedFrom = $target->content->obtainedFrom;
            $obtainedCn = [];

            foreach ($transAll as $trans) {
                foreach ($trans as $tran) {
                    if (strtolower($tran['en_name']) == strtolower($target->name)) {
                        $name = $tran['name'];
                        $jpName = $tran['jp_name'];
                    }

                    foreach ($obtainedFrom as $key => $obtained) {
                        if (strtolower($obtained) == strtolower($tran['en_name'])) {
                            $obtainedFrom[$key] = $tran['name'];
                        }
                    }
                }
            }

            if ($name == '') {
                continue;
            }

            $img = $target->content->image;

            $headers = get_headers($img);
            $code = substr($headers[0], 9, 3);

            if ($code == 200) {
                $content = file_get_contents($img);
                Storage::disk('expression')->put($target->name . '.png', $content);

                //insert
                DB::table('expression')->insert([
                    'name' => $name,
                    'en_name' => $target->name,
                    'jp_name' => $jpName,
                    'img_name' => $target->name,
                    'from' => json_encode($obtainedFrom, JSON_UNESCAPED_UNICODE),
                    'source' => isset($target->content->sourceNotes) ? $target->content->sourceNotes : ''
                ]);

                echo 'insert: ' . $name . '<br>';
            }
        }
    }

    public function getMuseum()
    {
        $ranges = range(1, 51);
        $result = Curl::to('https://api.nookplaza.net/items?category=Museum')->asJson()->get();
        $result = $result->results;
        $transAll = [];

        foreach ($ranges as $range) {
            //讀翻譯檔案
            $html = \File::get(public_path() . '/trans/' . $range . '.html');
            $ql = QueryList::html($html);
            $trans = $ql->rules([
                'en_name' => ['td:eq(2)', 'text'],
                'jp_name' => ['td:eq(14)', 'text'],
                'name' => ['td:eq(13)', 'text'],
            ])
            ->range('.grid-container tr')
            ->queryData();

            $transAll[] = $trans;
        }

        foreach ($result as $target) {
            //翻譯
            $name = '';

            if ($target->category != 'Fossils') {
                continue;
            }

            foreach ($transAll as $trans) {
                foreach ($trans as $tran) {
                    if (strtolower($tran['en_name']) == strtolower($target->name)) {
                        $name = $tran['name'];
                    }
                }
            }

            if ($name == '') {
                continue;
            }

            $img = $target->content->image;
            $fileIsset = false;

            $headers = get_headers($img);
            $code = substr($headers[0], 9, 3);

            if ($code == 200) {
                $content = file_get_contents($img);
                Storage::disk('fossil')->put($name . '.png', $content);
            }
        }
    }

    public function formatAnimalConstellation()
    {
        $datas = DB::table('animal')
            ->get()
            ->toArray();

        foreach ($datas as $data) {
            $constellation = '';

            foreach (constellation() as $name => $detail) {
                $explode = explode('-', $detail[1]);
                $start = strtotime($explode[0]);
                $end = strtotime($explode[1]);
                $target = strtotime(str_replace(".", "/", $data->bd));

                if ($target >= $start && $target <= $end) {
                    $constellation = $name;
                }
            }

            if ($constellation == '' && $data->bd != '') {
                $constellation = '魔羯座';
            }


            if ($constellation != '') {
                DB::table('animal')
                    ->where('id', $data->id)
                    ->update([
                        'constellation' => $constellation,
                    ]);
            }
        }
    }

    public function getRecipes()
    {
        $ranges = range(1, 51);
        $result = Curl::to('https://api.nookplaza.net/items?category=Recipes')->asJson()->get();
        $result = $result->results;
        $transAll = [];

        foreach ($ranges as $range) {
            //讀翻譯檔案
            $html = \File::get(public_path() . '/trans/' . $range . '.html');
            $ql = QueryList::html($html);
            $trans = $ql->rules([
                'en_name' => ['td:eq(2)', 'text'],
                'jp_name' => ['td:eq(14)', 'text'],
                'name' => ['td:eq(13)', 'text'],
            ])
            ->range('.grid-container tr')
            ->queryData();

            $transAll[] = $trans;
        }

        foreach ($result as $target) {
            $materials = [];
            //翻譯
            $name = '';
            $jpName = '';

            foreach ($transAll as $trans) {
                foreach ($trans as $tran) {
                    if (strtolower($tran['en_name']) == strtolower($target->name)) {
                        $name = $tran['name'];
                        $jpName = $tran['jp_name'];
                    }
                }

                foreach ($target->content->materials as $value) {
                    $itemName = '';
                    foreach ($trans as $tran) {
                        if (strtolower($tran['en_name']) == strtolower($value->itemName)) {
                            $itemName = $tran['name'];

                            $materials[] = [
                                'count' => $value->count,
                                'itemName' => $itemName,
                            ];
                        }
                    }
                }
            }

            if ($name == '') {
                dd($target);
            }

            $img = $target->content->itemImage;
            $fileIsset = false;

            $headers = get_headers($img);
            $code = substr($headers[0], 9, 3);
            $size = '';

            if (isset($target->content->size)) {
                $sizeData = $target->content->size;
                $size = (int)$sizeData->cols . 'x' . (int)$sizeData->rows;
            }


            if ($code == 200) {
                $fileIsset = is_file(public_path('diy/' .  $name . '.png'));

                if (!$fileIsset) {
                    $content = file_get_contents($img);
                    Storage::disk('diy')->put($name . '.png', $content);

                    //insert
                    DB::table('diy_new')->insert([
                        'name' => $name,
                        'en_name' => $target->name,
                        'jp_name' => $jpName,
                        'sell' => $target->content->sell,
                        'size' => $size,
                        'note' => $target->content->sourceNotes,
                        'type' => $target->content->itemCategory,
                        'get' => json_encode($target->content->obtainedFrom, JSON_UNESCAPED_UNICODE),
                        'diy' => json_encode($materials, JSON_UNESCAPED_UNICODE),
                        'img_name' => $name,
                    ]);

                    echo 'insert: ' . $name . '<br>';
                }
            }

            
        }
    }

    public function getKKZhName()
    {
        $url = 'https://handler.travel/stayhome-%E5%AE%85%E5%9C%A8%E5%AE%B6/%E5%8B%95%E7%89%A9%E4%B9%8B%E6%A3%AE%E6%94%BB%E7%95%A5-%E5%8B%95%E7%89%A9%E6%A3%AE%E5%8F%8B%E6%9C%83-kk-%E9%BB%9E%E6%AD%8C-%E9%9A%B1%E8%97%8F%E6%AD%8C%E6%9B%B2-%E5%85%A8%E6%AD%8C%E5%96%AE-%E8%A9%A6';

        $ql = QueryList::get($url);
        $result = $ql->rules([
            'name' => ['td:eq(2)', 'text'],
            'cn_name' => ['td:eq(1)', 'text'],
        ])
        ->range('.wp-block-table:eq(1) tr')
        ->queryData();

        $kks = DB::table('kk')
            ->get()
            ->toArray();

        foreach ($result as $data) {
            foreach ($kks as $kk) {
                if (strtolower($data['name']) == strtolower($kk->name)) {
                    DB::table('kk')
                        ->where('name', $kk->name)
                        ->update([
                            'cn_name' => $data['cn_name'],
                        ]);

                    echo 'update ' . $kk->name . '</br>';
                }
            }
        }
    }

    public function getNewFurniture()
    {
        set_time_limit(0);

        $result = Curl::to('https://api.nookplaza.net/items?category=Equippables')->asJson()->get();
        $result = $result->results;

        foreach ($result as $target) {
            //讀翻譯檔案
            $html = \File::get(public_path() . '/trans/' . $target->category . '.html');
            $ql = QueryList::html($html);
            $trans = $ql->rules([
                'en_name' => ['td:eq(2)', 'text'],
                'jp_name' => ['td:eq(14)', 'text'],
                'name' => ['td:eq(13)', 'text'],
            ])
            ->range('.grid-container tr')
            ->queryData();

            //翻譯
            $name = '';
            $jpName = '';
            $insert = [];

            foreach ($trans as $tran) {
                if (strtolower($tran['en_name']) == strtolower($target->name)) {
                    $name = $tran['name'];
                    $jpName = $tran['jp_name'];
                }
            }

            if ($name != '') {
                $size = '';

                if (isset($target->content->size)) {
                    $sizeData = $target->content->size;
                    $size = (int)$sizeData->cols . 'x' . (int)$sizeData->rows;
                }

                $insert = [
                    'en_name' => strtolower($target->name),
                    'jp_name' => $jpName,
                    'name' => $name,
                    'buy' => $target->content->buy,
                    'sell' => $target->content->sell,
                    'is_diy' => $target->content->dIY,
                    'category' => $target->category,
                    'size' => $size,
                    'type' => 1,
                ];

                if (empty($target->variations)) {
                    if (isset($target->content->bodyColor)) {
                        $insert['color'] = $target->content->bodyColor;
                    }

                    if (isset($target->content->curtainColor)) {
                        $insert['color'] = $target->content->curtainColor;
                    }

                    $img = $target->content->image;
                    $insert['img_name'] = '';
                    $fileIsset = false;

                    $headers = get_headers($img);
                    $code = substr($headers[0], 9, 3);

                    if ($code == 200) {
                        $insert['img_name'] = $name;
                        $fileIsset = is_file(public_path('itemsNew/' .  $insert['img_name'] . '.png'));

                        if (!$fileIsset) {
                            $content = file_get_contents($img);
                            Storage::disk('itemsNew')->put($insert['img_name'] . '.png', $content);
                        }
                    }

                    if (!$fileIsset) {
                        //insert
                        DB::table('items_new')->insert($insert);

                        echo 'insert: ' . $name . '<br>';
                    }

                    continue;
                }

                foreach ($target->variations as $key => $detail) {
                    $insert['color'] = $detail->content->bodyColor;
                    $img = $detail->content->image;
                    $insert['img_name'] = '';
                    $fileIsset = false;

                    $headers = get_headers($img);
                    $code = substr($headers[0], 9, 3);
                    $code = substr($headers[0], 9, 3);

                    if ($code == 200) {
                        $insert['img_name'] = $name . '_' . $key;
                        $fileIsset = is_file(public_path('itemsNew/' .  $insert['img_name'] . '.png'));

                        if (!$fileIsset) {
                            $content = file_get_contents($img);
                            Storage::disk('itemsNew')->put($insert['img_name'] . '.png', $content);
                        }
                    }

                    if (!$fileIsset) {
                        //insert
                        DB::table('items_new')->insert($insert);

                        echo 'insert: ' . $name . '<br>';
                    }
                }
            }
        }

        echo 'done';
    }

    public function getAnimalIcon()
    {
        set_time_limit(0);

        $animals = DB::table('animal')
            ->whereNotNull('en_name')
            ->whereNull('info')
            ->get()
            ->toArray();

        $result = Curl::to('https://api.nookplaza.net/items?category=Villagers')->asJson()->get();

        $result = $result->results;

        foreach ($result as $target) {
            foreach ($animals as $animal) {
                if (strtolower($target->name) == $animal->en_name) {
                    $icon = $target->content->image;
                    $houseImage = $target->content->houseImage;

                    $headers = get_headers($icon);
                    $code = substr($headers[0], 9, 3);
                    $fileIsset = is_file(public_path('animal/icon/' .  $animal->name . '.png'));

                    if ($code == 200 && !$fileIsset) {
                        $content = file_get_contents($icon);
                        Storage::disk('animalIcon')->put($animal->name . '.png', $content);
                    }

                    $headers = get_headers($houseImage);
                    $code = substr($headers[0], 9, 3);
                    $fileIsset = is_file(public_path('animal/house/' .  $animal->name . '.png'));

                    if ($code == 200) {
                        $content = file_get_contents($houseImage);
                        Storage::disk('animalHouse')->put($animal->name . '.png', $content);
                    }
                }

                if (strtolower($target->name) == $animal->en_name . "'s poster") {
                    $img = $target->content->image;

                    $headers = get_headers($img);
                    $code = substr($headers[0], 9, 3);
                    $fileIsset = is_file(public_path('animal/poster/' .  $animal->name . '.png'));

                    if ($code == 200 && !$fileIsset) {
                        $content = file_get_contents($img);
                        Storage::disk('animalPoster')->put($animal->name . '.png', $content);
                    }
                }
            }
        }
    }

    public function getKK()
    {
        $animals = DB::table('animal')
            ->whereNotNull('kk')
            ->where('kk', '!=', '')
            ->groupBy('kk')
            ->get()
            ->toArray();

        $kkDb = DB::table('kk')
            ->get()
            ->toArray();

        foreach ($animals as $animal) {
            $kk = $animal->kk;
            $kk = str_replace(" ", "_", $kk);
            $isset = false;

            foreach ($kkDb as $db) {
                if ($db->name == $animal->kk) {
                    $isset = true;
                }
            }

            if ($isset) {
                continue;
            }

            $url = 'https://animalcrossing.fandom.com/wiki/' . $kk;

            $ql = QueryList::get($url);
            $result = $ql->rules([
                'img' => ['img', 'src'],
            ])
            ->range('.roundy td:eq(1)')
            ->queryData();

            $img = $result[0]['img'];

            $headers = get_headers($img);
            $code = substr($headers[0], 9, 3);

            if ($code == 200) {
                $content = file_get_contents($img);
                Storage::disk('kk')->put($kk . '.png', $content);
            }

            //insert
            DB::table('kk')->insert([
                'name' => $animal->kk,
                'file_name' => $kk,
                'img_name' => $kk,
            ]);

            echo 'insert: ' . $kk . '<br>';
        }
    }

    public function getPlant()
    {
        $ql = QueryList::get('https://wiki.biligame.com/dongsen/%E6%A4%8D%E7%89%A9%E5%9B%BE%E9%89%B4');
        $result = $ql->rules([
            'name' => ['td:eq(1)', 'text'],
            'img' => ['td:eq(0) .floatnone a img', 'srcset'],
            'sell' => ['td:eq(2)', 'text'],
        ])
        ->range('.wikitable:eq(13) tr')
        ->queryData();

        foreach ($result as $data) {
            if ($data['name'] == '') {
                continue;
            }

            //name
            $name = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['name'])->asJson()->get();
            $name = $name->data->text;

            $imgName = $name;

            //save img
            if ($data['img'] != '' && !is_null($data['img'])) {
                $imgExplode = explode(',', $data['img']);
                $img = trim(substr($imgExplode[1], 0, -2));

                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('items')->put($name . '.png', $content);
                    $imgName = $name;
                }
            }

            //insert
            DB::table('items')->insert([
                'name' => $name,
                'cn_name' => $data['name'],
                'img_name' => $name,
                'sell' => $data['sell'],
            ]);

            echo 'insert: ' . $name . '<br>';
        }
    }

    public function getFossil()
    {
        $ql = QueryList::get('https://wiki.biligame.com/dongsen/%E5%8C%96%E7%9F%B3%E5%9B%BE%E9%89%B4');
        $result = $ql->rules([
            'name' => ['td:eq(0) a', 'text'],
            'img' => ['td:eq(0) .floatnone a img', 'srcset'],
            'en_name' => ['td:eq(1)', 'text'],
            'jp_name' => ['td:eq(2)', 'text'],
            'sell' => ['td:eq(3)', 'text'],
            'info' => ['td:eq(4)', 'text'],
        ])
        ->range('#CardSelectTr tr')
        ->queryData();

        foreach ($result as $data) {
            if ($data['name'] == '') {
                continue;
            }

            //name
            $name = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['name'])->asJson()->get();
            $name = $name->data->text;
            //info
            $info = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['info'])->asJson()->get();
            $info = $info->data->text;
            $imgName = '';

            //save img
            if ($data['img'] != '' && !is_null($data['img'])) {
                $imgExplode = explode(',', $data['img']);
                $img = trim(substr($imgExplode[1], 0, -2));

                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('fossil')->put($name . '.png', $content);
                    $imgName = $name;
                }
            }

            //insert
            DB::table('fossil')->insert([
                'name' => $name,
                'cn_name' => $data['name'],
                'en_name' => $data['en_name'],
                'jp_name' => $data['jp_name'],
                'sell' => $data['sell'],
                'img_name' => $imgName,
                'info' => $info
            ]);

            echo 'insert: ' . $name . '<br>';
        }
    }

    public function getArtwork()
    {
        $ql = QueryList::get('https://wiki.biligame.com/dongsen/%E8%89%BA%E6%9C%AF%E5%93%81%E9%89%B4%E4%BC%AA');
        $result = $ql->rules([
            'name' => ['td:eq(0)', 'text'],
            'img1' => ['td:eq(1) a img', 'srcset'],
            'img2' => ['td:eq(2) a img', 'srcset'],
            'img3' => ['td:eq(3) a img', 'srcset'],
            'info' => ['td:eq(4)', 'text'],
        ])
        ->range('#mw-content-text .wikitable tr')
        ->queryData();

        $dbData = DB::table('art')->get()->toArray();

        foreach ($result as $data) {
            $isset = false;

            if ($data['img1'] == '' && $data['img2'] == '' && $data['img3'] == '') {
                continue;
            }

            //檢查是否資料庫存在
            foreach ($dbData as $source) {
                if ($source->cn_name == $data['name']) {
                    $isset = true;
                }
            }

            if ($isset) {
                continue;
            }

            //name
            $name = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['name'])->asJson()->get();
            $name = $name->data->text;

            //info
            $info = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['info'])->asJson()->get();
            $info = $info->data->text;
            $img1 = '';
            $img2 = '';
            $img3 = '';

            //save img1
            if ($data['img1'] != '') {
                $img = trim(substr($data['img1'], 0, -5));
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('art')->put($name . '1.png', $content);
                    $img1 = $name . '1';
                }
            }

            //save img2
            if ($data['img2'] != '') {
                $img = trim(substr($data['img2'], 0, -5));
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('art')->put($name . '2.png', $content);
                    $img2 = $name . '2';
                }
            }

            //save img1
            if ($data['img3'] != '') {
                $img = trim(substr($data['img3'], 0, -5));
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('art')->put($name . '3.png', $content);
                    $img3 = $name . '3';
                }
            }

            //insert
            DB::table('art')->insert([
                'name' => $name,
                'cn_name' => $data['name'],
                'img1' => $img1,
                'img2' => $img2,
                'img3' => $img3,
                'info' => $info,
            ]);

            echo 'insert: ' . $name . '<br>';
        }

        echo 'done';
    }

    public function getAnimalCard()
    {
        $urls = [
            'https://wiki.biligame.com/dongsen/%E7%AC%AC%E4%B8%80%E5%BC%B9',
            'https://wiki.biligame.com/dongsen/%E7%AC%AC%E4%BA%8C%E5%BC%B9',
            'https://wiki.biligame.com/dongsen/%E7%AC%AC%E4%B8%89%E5%BC%B9',
            'https://wiki.biligame.com/dongsen/%E7%AC%AC%E5%9B%9B%E5%BC%B9',
        ];

        $dbData = DB::table('animal_card')->get()->toArray();

        foreach ($urls as $url) {
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'name' => ['img', 'alt'],
                'img' => ['img', 'srcset'],
            ])
            ->range('.gallery li')
            ->queryData();

            foreach ($result as $data) {
                $name = md5(str_replace('.png', '', $data['name']));

                $isset = false;

                if ($data['img'] == '' && $data['name'] == '') {
                    continue;
                }

                //檢查是否資料庫存在
                foreach ($dbData as $source) {
                    if ($source->name == $name) {
                        $isset = true;
                    }
                }

                $imgExplode = explode(',', $data['img']);
                $img = trim(substr($imgExplode[1], 0, -2));

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                $fileIsset = is_file(public_path('animal/card/' .  $name . '.png'));

                if (!$fileIsset) {
                    $content = file_get_contents($img);
                    Storage::disk('animalCard')->put($name . '.png', $content);

                    //insert
                    DB::table('animal_card')->insert([
                        'name' => $name,
                    ]);

                    echo 'insert: ' . $name . '<br>';
                }
            }
        }

        echo 'done';
    }

    public function getAnimalHome()
    {
        $urls = AnimalServices::getHomeImgUrls();

        foreach ($urls as $url) {
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'name' => ['td:eq(0)', 'text'],
                'img' => ['td:eq(1) img', 'src'],
            ])
            ->range('#arcbody table tr')
            ->queryData();

            foreach ($result as $data) {
                if ($data['name'] != '' && $data['img'] != '') {
                    //get
                    $name = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['name'])->asJson()->get();
                    $name = $name->data->text;
                    $headers = get_headers($data['img']);
                    $code = substr($headers[0], 9, 3);

                    //save img
                    $headers = get_headers($data['img']);
                    $code = substr($headers[0], 9, 3);

                    $fileIsset = is_file(public_path('animal/' .  $name . '_home.png'));

                    if (!$fileIsset) {
                        $content = file_get_contents($data['img']);
                        Storage::disk('animal')->put($name . '_home.png', $content);
                        echo 'insert: ' . $name . '<br>';
                    }
                }
            }
        }

        echo 'done';
    }

    public function getClothes()
    {
        $urls = [
            //上裝
            'https://wiki.biligame.com/dongsen/%E6%9C%8D%E9%A5%B0%E5%9B%BE%E9%89%B4',
            //下裝
            'https://wiki.biligame.com/dongsen/%E4%B8%8B%E8%A3%85',
            //連衣裙
            'https://wiki.biligame.com/dongsen/%E8%BF%9E%E8%A1%A3%E8%A3%99',
            //帽子
            'https://wiki.biligame.com/dongsen/%E5%B8%BD%E5%AD%90',
            //頭盔
            'https://wiki.biligame.com/dongsen/%E5%A4%B4%E7%9B%94',
            //飾品
            'https://wiki.biligame.com/dongsen/%E9%A5%B0%E5%93%81',
            //襪子
            'https://wiki.biligame.com/dongsen/%E8%A2%9C%E5%AD%90',
            //鞋
            'https://wiki.biligame.com/dongsen/%E9%9E%8B',
            //包
            'https://wiki.biligame.com/dongsen/%E5%8C%85'
        ];

        $this->apiClothes($urls);
    }


    public function apiClothes($urls)
    {
        foreach ($urls as $url) {
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'img' => ['td:eq(0) img', 'srcset'],
                'name' => ['td:eq(0) a', 'text'],
                'type' => ['td:eq(1)', 'text'],
                'detail_type' => ['td:eq(2)', 'text'],
                'source_sell' => ['td:eq(3)', 'text'],
                'sell' => ['td:eq(4)', 'text'],
                'sample_sell' => ['td:eq(5)', 'text'],
            ])
            ->range('.CardSelect tr')
            ->queryData();

            $dbData = DB::table('items')->get()->toArray();

            foreach ($result as $key => $data) {
                $isset = false;

                if ($data['img'] == '' && $data['name'] == '') {
                    continue;
                }

                //檢查是否資料庫存在
                foreach ($dbData as $source) {
                    if ($source->cn_name == $data['name'] && $source->detail_type == $data['detail_type']) {
                        $isset = true;
                    }
                }

                if ($isset) {
                    continue;
                }

                $imgExplode = explode(',', $data['img']);
                $img = trim(substr($imgExplode[1], 0, -2));

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);
                $imgName = '';

                if ($code == 200) {
                    $imgName = md5(rand(0, 10000) . $key . $url);
                    $content = file_get_contents($img);
                    Storage::disk('items')->put($imgName . '.png', $content);
                }

                //insert
                DB::table('items')->insert([
                    'cn_name' => $data['name'],
                    'type' => $data['type'],
                    'img_name' => $imgName,
                    'source_sell' => $data['source_sell'],
                    'sell' => $data['sell'],
                    'sample_sell' => $data['sample_sell'],
                    'detail_type' => $data['detail_type'],
                ]);

                echo 'insert: ' . $data['name'] . '<br>';
            }
        }

        echo 'done';
    }

    public function getItems()
    {
        $urls = [
            //訂購
            'https://wiki.biligame.com/dongsen/%E5%AE%B6%E5%85%B7%E5%9B%BE%E9%89%B4',
            //非賣品
            'https://wiki.biligame.com/dongsen/%E9%9D%9E%E5%8D%96%E5%93%81%E5%AE%B6%E5%85%B7',
            //不可訂購
            'https://wiki.biligame.com/dongsen/%E4%B8%8D%E5%8F%AF%E8%AE%A2%E8%B4%AD%E5%AE%B6%E5%85%B7',
        ];

        $this->getItems($urls);
    }

    public function apiItems($urls)
    {
        foreach ($urls as $url) {
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'img' => ['td:eq(0) img', 'srcset'],
                'type' => ['td:eq(1)', 'text'],
                'name' => ['td:eq(2)', 'text'],
                'source_sell' => ['td:eq(3)', 'text'],
                'sell' => ['td:eq(4)', 'text'],
                'sample_sell' => ['td:eq(5)', 'text'],
                'buy_type' => ['td:eq(6)', 'text'],
                'detail_type' => ['td:eq(7)', 'text'],
                'size' => ['td:eq(8)', 'text'],
            ])
            ->range('.CardSelect tr')
            ->queryData();

            $dbData = DB::table('items')->get()->toArray();

            foreach ($result as $key => $data) {
                $isset = false;

                if ($data['img'] == '' && $data['name'] == '') {
                    continue;
                }

                //檢查是否資料庫存在
                foreach ($dbData as $source) {
                    if ($source->cn_name == $data['name']) {
                        $isset = true;
                    }
                }

                if ($isset) {
                    continue;
                }

                $imgExplode = explode(',', $data['img']);
                $img = trim(substr($imgExplode[1], 0, -2));

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);
                $imgName = '';

                if ($code == 200) {
                    $imgName = md5(rand(0, 1000) . $url);
                    $content = file_get_contents($img);
                    Storage::disk('items')->put($imgName . '.png', $content);
                }

                //insert
                DB::table('items')->insert([
                    'cn_name' => $data['name'],
                    'type' => $data['type'],
                    'img_name' => $imgName,
                    'source_sell' => $data['source_sell'],
                    'sell' => $data['sell'],
                    'sample_sell' => $data['sample_sell'],
                    'buy_type' => $data['buy_type'],
                    'detail_type' => $data['detail_type'],
                    'size' => $data['size'],
                ]);

                echo 'insert: ' . $data['name'] . '<br>';
            }
        }

        echo 'done';
    }

    /*
        UPDATE `doting`.`items` SET `type` = '多顏色' WHERE (`type` = '多颜色');
        UPDATE `doting`.`items` SET `buy_type` = '非賣品' WHERE (`buy_type` = '非卖品');
        UPDATE `doting`.`items` SET `buy_type` = '無法訂購' WHERE (`buy_type` = '无法订购');
        UPDATE `doting`.`items` SET `buy_type` = '訂購' WHERE (`buy_type` = '订购');
    */
    public function itemsToZh()
    {
        set_time_limit(0);

        //DB DATA
        $dbData = DB::table('items')->get()->toArray();
        $dbData = array_chunk($dbData, 1000);

        foreach ($dbData as $data) {
            $nameData = [];
            foreach ($data as $detail) {
                $nameData[$detail->id] = $detail->cn_name;
            }

            //get
            $target = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . json_encode($nameData, JSON_UNESCAPED_UNICODE))->asJson()->get();
            $target = $target->data->text;

            $decodes = json_decode($target, true);

            foreach ($decodes as $id => $decode) {
                DB::table('items')
                    ->where('id', $id)
                    ->update([
                        'name' => $decode,
                    ]);

                echo 'update ' . $decode . '</br>';
            }
        }

        echo 'done';
    }

    public function getDiy()
    {
        $url = 'https://wiki.biligame.com/dongsen/DIY%E9%85%8D%E6%96%B9';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['td:eq(0) img', 'srcset'],
            'name' => ['td:eq(0) a', 'text'],
            'type' => ['td:eq(1)', 'text'],
            'get' => ['td:eq(2)', 'text'],
            'diy' => ['td:eq(4)', 'text'],
        ])
        ->range('#CardSelectTr tr')
        ->queryData();

        //DB DATA
        $dbData = DB::table('diy')
            ->where('img_name', '!=', '')
            ->get()
            ->toArray();

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

                //save img
                $imgExplode = explode(',', $data['img']);
                $img = trim(substr($imgExplode[1], 0, -2));

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('diy')->put($name . '.png', $content);

                    DB::table('diy')
                        ->where('name', $name)
                        ->update([
                            'img_name' => $name,
                        ]);

                    echo 'update: ' . $name . '<br>';
                }

                /*$type = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['type'])->asJson()->get();
                $type = $type->data->text;

                $get = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['get'])->asJson()->get();
                $get = $get->data->text;

                $diy = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['diy'])->asJson()->get();
                $diy = $diy->data->text;*/

                //insert
                /*DB::table('diy')->insert([
                    'name' => $name,
                    'type' => $type,
                    'get' => $get,
                    'diy' => $diy,
                ]);

                echo 'insert: ' . $data['name'] . '<br>';*/
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
        $url = 'https://wiki.biligame.com/dongsen/%E8%99%AB%E5%9B%BE%E9%89%B4';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['td:eq(0) img', 'srcset'],
            'name' => ['td:eq(0) a', 'text'],
        ])
        ->range('#CardSelectTr tr')
        ->queryData();


        //save api result
        foreach ($result as $key => $data) {
            if ($data['name'] != '' && $data['img']) {
                //get
                $name = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $data['name'])->asJson()->get();
                $name = $name->data->text;
                
                $imgExplode = explode(',', $data['img']);
                if (!isset($imgExplode[1])) {
                    $format = str_replace("1.5x", "", $imgExplode[0]);
                    $format = str_replace(" ", "", $format);
                    $imgExplode[1] = $format;
                }

                $img = trim(substr($imgExplode[1], 0, -2));

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($img);
                    Storage::disk('other')->put($name . '.png', $content);
                }

                echo 'update: ' . $name . '<br>';
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

    public function getAnimalDetail()
    {
        //DB DATA
        $dbAnimal = DB::table('animal')->where('target', '')->get()->toArray();

        foreach ($dbAnimal as $data) {
            $simplified = Curl::to('http://api.zhconvert.org/convert?converter=Simplified&text=' . $data->name)->asJson()->get();
            $name = $simplified->data->text;

            $url = 'https://wiki.biligame.com/dongsen/' . $name;
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'target' => ['.box-poke-left .box-poke .box-font:eq(3)', 'text'],
            ])
            ->range('.box-poke-big')
            ->queryData();

            if (!empty($result)) {
                $target = $result[0]['target'];
                //get
                $target = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $target)->asJson()->get();
                $target = $target->data->text;

                DB::table('animal')
                    ->where('id', $data->id)
                    ->update([
                        'motto' => $target,
                    ]);

                echo 'update ' . $data->name . '</br>';
            }
        }

        echo 'done';
    }

    public function getAnimalEnWeb()
    {
        //DB DATA
        $dbAnimal = DB::table('animal')
            ->whereNull('amiibo')
            ->get()
            ->toArray();

        foreach ($dbAnimal as $data) {
            $url = 'https://animalcrossing.fandom.com/wiki/' . $data->en_name;
            $ql = QueryList::get($url);

            $detail = $ql->rules([
                'name' => ['.pi-item[data-source=Song] .pi-font a', 'text'],
            ])
            ->range('.portable-infobox')
            ->queryData();

            if (empty($detail)) {
                continue;
            }

            $amiibo = $ql->rules([
                'name' => ['tr:eq(0) td', 'text'],
            ])
            ->range('.roundytop')
            ->queryData();

            $amiiboImg = $ql->rules([
                'name' => ['noscript img:eq(1)', 'src'],
            ])
            ->range('center .roundy')
            ->queryData();

            if (!empty($amiibo)) {
                $amiiboName = $amiibo[0]['name'];
                //分解
                $explode = explode(" ", $amiiboName);
                $num = str_replace("#", "", $explode[0]);
                $imgName = $num . '_' . $explode[1];
            }

            $kk = $detail[0]['name'];

            if (!empty($amiiboImg)) {
                $imgUrl = $amiiboImg[0]['name'];

                //save img
                $headers = get_headers($imgUrl);
                $code = substr($headers[0], 9, 3);

                if ($code == 200) {
                    $content = file_get_contents($imgUrl);
                    Storage::disk('animalCard')->put($imgName . '.png', $content);
                }
            } else {
                $imgName = '';
            }

            DB::table('animal')
                ->where('id', $data->id)
                ->update([
                    'kk' => $kk,
                    'amiibo' => $imgName,
                ]);

            echo 'update ' . $data->name . '</br>';
        }

        echo 'done';
    }
}
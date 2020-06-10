<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session;

class WebUserController extends Controller
{
    public function friendList(Request $request)
    {
        $text = $request->input('text', '');
        $target = $request->input('target', '');

        return view('webUser.list', [
            'text' => $text,
            'target' => $target,
        ]);
    }

    public function friendSearch(Request $request)
    {
        $type = 'friend';
        $target = $request->input('target', '');
        $text = $request->input('text', '');

        $users = DB::table('web_user')
            ->where('open_user_data', 1)
            ->whereNotNull('nick_name')
            ->whereNotNull('passport');

        if ($text != '') {
            $users->where(function($q) use ($text) {
              $q->where('nick_name', 'like', '%' . $text . '%')
                ->orWhere('island_name', 'like', '%' . $text . '%');
            });
        }

        //check target
        if ($target != '') {
            $getCount = computedCount($type, $type, true);

            switch ($target) {
                case 'like':
                    $users->whereIn('id', $getCount['likeIds']);
                    break;
            }
        }

        $users = $users
            ->orderBy('updated_at', 'desc')
            ->select([
                'id',
                'passport',
                'picture_url',
                'open_picture',
                'island_name',
                'nick_name',
                'fruit',
                'info',
                'like as likeCount',
                'flower',
                'position',
                'created_at as date'
            ])
            ->paginate(30)
            ->toArray();

        $format = [];

        foreach ($users['data'] as $key => $user) {
            $user->date = date("Y/m/d", strtotime($user->date));
            $user->fruit_name = fruitName($user->fruit);
            $user->position_name = positionName($user->position);

            if (!$user->open_picture) {
                $user->picture_url = '/image/empty.jpg';
            }

            $last = mb_substr($user->island_name, -1);

            if ($last != '島') {
                $user->island_name = $user->island_name . '島';
            }

            $format[] = $user;
        }


        //encode id and like current
        $format = computedMainData($format, $type, $type);

        return $format;
    }

    public function index(Request $request)
    {
        return view('webUser.info');
    }

    public function info(Request $request)
    {
    	//user info
    	$lineId = getUserData('lineId');
        $request = $request->input();
        $changeStart = isset($request['changeStart']) ? $request['changeStart'] : '';

    	//判斷是否有資料
    	$user = DB::table('web_user')
    	    ->where('line_id', $lineId)
    	    ->first([
    	    	'picture_url',
    	    	'passport',
    	    	'island_name',
                'nick_name',
    	    	'fruit',
                'info',
                'flower',
    	    	'position',
                'open_picture',
                'open_user_data',
    	    	'created_at'
    	    ]);

    	if (is_null($user)) {
    		return [
    			'code' => -1,
    			'msg' => 'need login',
    		];
    	}

    	$user->date = date("Y/m/d", strtotime($user->created_at));
    	$user->fruit_name = fruitName($user->fruit);
    	$user->position_name = positionName($user->position);

    	//找出所有集合
    	$countData = computedAllCount();
    	$countData['animalInfo'] = [
    		'track' => [],
    		'like' => [],
    	];

    	//動物
    	if (isset($countData['animal'])) {
	    	if (isset($countData['animal']['trackIds'])) {
	    		$animalTrack = DB::table('animal')
	    		    ->whereIn('id', $countData['animal']['trackIds'])
	    		    ->get([
	    		    	'name',
	    		    	'sex',
	    		    	'personality',
	    		    	'race',
	    		    	'bd',
	    		    ])->toArray();

	    		$countData['animalInfo']['track'] = $animalTrack;
	    	}

	    	if (isset($countData['animal']['likeIds'])) {
	    		$animalLike = DB::table('animal')
	    		    ->whereIn('id', $countData['animal']['likeIds'])
	    		    ->get([
	    		    	'name',
	    		    	'sex',
	    		    	'personality',
	    		    	'race',
	    		    	'bd',
	    		    ])->toArray();

	    		$countData['animalInfo']['like'] = $animalLike;
	    	}
    	}

    	//get all items
    	$countItems = getCountItems($countData);
        $compatibleUrl = '';
        $names = [];

        foreach ($countData['animalInfo']['like'] as $data) {
            $names[] = $data->name;
        }

        $nameStr = implode(",", $names);

        if ($nameStr != '') {
            $compatibleUrl = env('APP_URL') . '/animals/compatible?name=' . $nameStr;
        }
        //find user all cai
        $historyCai = DB::table('user_cai')
            ->where('line_id', $lineId)
            ->get(['start', 'end'])
            ->toArray();

        //find cai data
        $dates = getCaiDate();
        $start = $dates['start'];
        $end = $dates['end'];

        if ($changeStart != '') {
            $start = $changeStart;
            $end = date('Y-m-d', strtotime("$start + 6 days"));
        }

        $userCai = DB::table('user_cai')
            ->where('line_id', $lineId)
            ->where('start', $start)
            ->where('end', $end)
            ->first(['cai']);

        $caiData = getCaiFormat();

        if (!is_null($userCai)) {
            $checkCai = json_decode($userCai->cai);

            if (is_array($checkCai) && !empty($checkCai)) {
                //檢查是否正確格式
                $check = true;

                foreach ($checkCai as $cai) {
                    if (count($cai) != 3) {
                        $check = false;
                    }
                }

                if ($check) {
                    $caiData = json_decode($userCai->cai);
                }
            }
        }

        $needAdd = true;

        foreach ($historyCai as $cai) {
            if ($cai->start == $start) {
                $needAdd = false;
            }
        }

        if ($needAdd) {
            $historyCai[] = (object) [
                'start' => $start,
                'end' => $end,
            ];
        }

    	return [
    		'code' => 1,
    		'data' => $user,
    		'itemsData' => $countItems,
    		'animalInfo' => $countData['animalInfo'],
            'compatibleUrl' => $compatibleUrl,
            'start' => $start,
            'end' => $end,
            'caiData' => $caiData,
            'historyCai' => $historyCai,
    	];
    }

    public function editCai(Request $request)
    {
        //user info
        $lineId = getUserData('lineId');
        $postData = $request->input();
        $caiData = isset($postData['caiData']) ? $postData['caiData'] : [];
        $caiResult = isset($postData['caiResult']) ? $postData['caiResult'] : '';
        $start = isset($request['start']) ? $request['start'] : '';
        $end = isset($request['end']) ? $request['end'] : '';

        if ($lineId == '') {
            return [
                'code' => -1,
                'msg' => 'need login',
            ];
        }

        if (!is_array($caiData) || empty($caiData) || $caiResult == '') {
            return [
                'code' => -2,
                'msg' => '非正確參數'
            ];
        }

        //檢查是否正確格式
        foreach ($caiData as $cai) {
            if (count($cai) != 3) {
                return [
                    'code' => -3,
                    'msg' => '非正確格式'
                ];
            }
        }

        if ($start == '' || $end == '') {
            $dates = getCaiDate();
            $start = $dates['start'];
            $end = $dates['start'];
        }

        //find
        $userCai = DB::table('user_cai')
            ->where('line_id', $lineId)
            ->where('start', $start)
            ->where('end', $end)
            ->first();

        try {
            if (is_null($userCai)) {
                DB::table('user_cai')->insert([
                    'line_id' => $lineId,
                    'start' => $start,
                    'end' => $end,
                    'result' => $caiResult,
                    'cai' => json_encode($caiData, JSON_UNESCAPED_UNICODE),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                DB::table('user_cai')
                ->where('id', $userCai->id)
                ->update([
                    'line_id' => $lineId,
                    'result' => $caiResult,
                    'cai' => json_encode($caiData, JSON_UNESCAPED_UNICODE),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (Exception $e) {
            return [
                'code' => -99,
                'msg' => 'error'
            ];
        }

        return [
            'code' => 1,
            'msg' => 'success',
        ];
    }

    public function editInfo(Request $request)
    {
    	$postData = $request->input();

    	$passport = isset($postData['info']['passport']) ? $postData['info']['passport'] : '';
    	$islandName = isset($postData['info']['island_name']) ? $postData['info']['island_name'] : '';
        $info = isset($postData['info']['info']) ? $postData['info']['info'] : '';
    	$fruit = isset($postData['info']['fruit']) ? $postData['info']['fruit'] : '';
    	$position = isset($postData['info']['position']) ? $postData['info']['position'] : '';
        $nickName = isset($postData['info']['nick_name']) ? $postData['info']['nick_name'] : '';
        $flower = isset($postData['info']['flower']) ? $postData['info']['flower'] : '';
        $openPicture = isset($postData['info']['open_picture']) ? $postData['info']['open_picture'] : 0;
        $openUserData = isset($postData['info']['open_user_data']) ? $postData['info']['open_user_data'] : 0;

    	//判斷字串長度
    	if (strlen($islandName) > 40 || strlen($info) > 40 || strlen($nickName) > 40 || strlen($flower) > 40) {
    		return [
    			'code' => -2,
    			'msg' => '字串過長 母湯'
    		];
    	}

        $openPicture = $openPicture ? true : false;
        $openUserData = $openUserData ? true : false;

    	if (!is_numeric($fruit) || !is_numeric($position) || !is_bool($openPicture) || !is_bool($openUserData)) {
    		return [
    			'code' => -2,
    			'msg' => '非正確參數'
    		];
    	}

        if ($passport != '') {
            //判斷護照
            if (strlen($passport) != 12) {
                return [
                    'code' => -2,
                    'msg' => '護照格式錯誤 ex: 0000-1111-2222'
                ];
            }

            $str1 = mb_substr($passport, 0, 4);
            $str2 = mb_substr($passport, 4, 4);
            $str3 = mb_substr($passport, 8, 5);
            $passport = $str1 . '-' . $str2 . '-' . $str3;
        }

    	//update
    	$lineId = getUserData('lineId');

    	if ($lineId == '') {
    		return [
    			'code' => -1,
    			'msg' => 'need login',
    		];
    	}

    	DB::table('web_user')
    	    ->where('line_id', $lineId)
    	    ->update([
    	    	'passport' => $passport,
    	    	'island_name' => $islandName,
                'nick_name' => $nickName,
    	    	'position' => $position,
    	    	'fruit' => $fruit,
                'info' => $info,
                'flower' => $flower,
                'open_picture' => $openPicture,
                'open_user_data' => $openUserData,
    	    ]);

    	$user = session('web');
    	$user['position'] = $position;

    	Session::put('web', $user);

    	return [
    		'code' => 1,
    		'msg' => 'success',
    	];
    }
}
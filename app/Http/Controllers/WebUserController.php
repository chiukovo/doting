<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session;

class WebUserController extends Controller
{
    public function index(Request $request)
    {
        return view('webUser.info');
    }

    public function info(Request $request)
    {
    	//user info
    	$lineId = getUserData('lineId');

    	//判斷是否有資料
    	$user = DB::table('web_user')
    	    ->where('line_id', $lineId)
    	    ->first([
    	    	'picture_url',
    	    	'passport',
    	    	'island_name',
    	    	'fruit',
                'info',
    	    	'position',
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

    	return [
    		'code' => 1,
    		'data' => $user,
    		'itemsData' => $countItems,
    		'animalInfo' => $countData['animalInfo'],
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

    	//判斷字串長度
    	if (strlen($passport) > 40 || strlen($islandName) > 40 || strlen($info) > 40) {
    		return [
    			'code' => -2,
    			'msg' => '字串過長 母湯'
    		];
    	}

    	if (!is_numeric($fruit) || !is_numeric($position)) {
    		return [
    			'code' => -2,
    			'msg' => '非正確參數'
    		];
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
    	    	'position' => $position,
    	    	'fruit' => $fruit,
                'info' => $info,
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
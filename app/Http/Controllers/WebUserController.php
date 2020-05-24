<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

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

    	return [
    		'code' => 1,
    		'data' => $user,
    	];
    }

    public function editInfo(Request $request)
    {
    	$postData = $request->input();

    	$passport = isset($postData['info']['passport']) ? $postData['info']['passport'] : '';
    	$islandName = isset($postData['info']['island_name']) ? $postData['info']['island_name'] : '';
    	$fruit = isset($postData['info']['fruit']) ? $postData['info']['fruit'] : '';
    	$position = isset($postData['info']['position']) ? $postData['info']['position'] : '';

    	//判斷字串長度
    	if (strlen($passport) > 40 || strlen($islandName) > 40) {
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
    	    ]);

    	return [
    		'code' => 1,
    		'msg' => 'success',
    	];
    }
}
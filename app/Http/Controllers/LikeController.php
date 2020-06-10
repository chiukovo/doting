<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Curl, Log, DB;

class LikeController extends Controller
{
    public function toggleLike(Request $request)
    {
    	$lineId = getUserData('lineId');

    	if ($lineId == '') {
    		return [
    			'code' => -1,
    			'msg' => 'need login',
    		];
    	}

    	$likeType = $request->input('likeType', '');
    	$likeTarget = $request->input('likeTarget', '');
        $type = $request->input('type', '');
    	$id = '';
    	$token = $request->input('token', '');

    	//檢查
    	if ($likeType == '' || $likeTarget == '' || $token == '' || $type == '') {
    		return [
    			'code' => 0,
    			'msg' => 'params fail',
    		];
    	}

    	//檢查type
    	$allType = allLikeTypeTarget();
    	if (!in_array($likeType, $allType['likeType']) || !in_array($likeTarget, $allType['target']) || !in_array($type, $allType['type'])) {
    		return [
    			'code' => 0,
    			'msg' => 'unknown',
    		];
    	}

    	//decode
    	try {
    	    $id = decrypt($token);
    	} catch (DecryptException $e) {
    		return [
    			'code' => 0,
    			'msg' => 'token fail',
    		];
    	}

    	if (!is_integer($id)) {
    		return [
    			'code' => 0,
    			'msg' => 'token number fail',
    		];
    	}

    	//like
    	$like = Redis::hGet($lineId, $id . '_' . $likeType . '_' . $type . '_' . $likeTarget);

    	if ($like) {
    		Redis::hdel($lineId, $id . '_' . $likeType . '_' . $type . '_' . $likeTarget);

            //-好友
            if ($type == 'friend') {
                DB::table('web_user')
                    ->where('id', $id)
                    ->decrement('like');
            }
    	} else {
    		Redis::hSet($lineId, $id . '_' . $likeType . '_' . $type . '_' . $likeTarget, $likeTarget);

            //+好友
            if ($type == 'friend') {
                DB::table('web_user')
                    ->where('id', $id)
                    ->increment('like');
            }
    	}

    	return [
    		'code' => '1',
    		'msg' => 'success',
    		'count' => computedCount($likeType, $type),
    	];
    }

    public function getCount(Request $request)
    {
    	$likeType = $request->input('likeType', '');
        $type = $request->input('type', '');

    	return computedCount($likeType, $type);
    }
}
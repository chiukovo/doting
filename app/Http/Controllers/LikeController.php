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
    	if ($likeType == '' || $likeTarget == '' || $token == '') {
    		return [
    			'code' => 0,
    			'msg' => 'params fail',
    		];
    	}

    	//檢查type
    	$allType = allLikeTypeTarget();
    	if (!in_array($likeType, $allType['type']) || !in_array($likeTarget, $allType['target'])) {
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
    	$like = Redis::hGet($lineId, $id . '_' . $likeType . '_' . $likeTarget);

    	if ($like) {
    		Redis::hdel($lineId, $id . '_' . $likeType . '_' . $likeTarget);
    	} else {
    		Redis::hSet($lineId, $id . '_' . $likeType . '_' . $likeTarget, $likeTarget);
    	}

    	return [
    		'code' => '1',
    		'msg' => 'success',
    		'count' => $this->computedCount($likeType),
            'countAll' => $countAll,
    	];
    }

    public function getCount(Request $request)
    {
    	$likeType = $request->input('likeType', '');
        $type = $request->input('type', '');

    	return $this->computedCount($likeType, $type);
    }

    public function computedCount($likeType, $type)
    {
    	$likeCount = 0;
    	$trackCount = 0;

    	$lineId = getUserData('lineId');

    	//檢查type
    	$allType = allLikeTypeTarget();
    	if (!in_array($likeType, $allType['type'])) {
    		return [
    			'likeCount' => $likeCount,
    			'trackCount' => $trackCount,
    		];
    	}

    	//like
    	$like = Redis::hGetAll($lineId);

    	foreach ($like as $data) {
    		if ($data == 'like') {
    			$likeCount++;
    		}

    		if ($data == 'track') {
    			$trackCount++;
    		}
    	}

        $countAll = 0;

        switch ($likeType) {
            case 'animal':
                if ($type == '') {
                    $countAll = DB::table($likeType)->whereNull('info')->count();
                } else {
                    $countAll = DB::table($likeType)->where('info', '!=', '')->count();
                }

                break;
        }

    	return [
    		'likeCount' => $likeCount,
    		'trackCount' => $trackCount,
            'noLikeCount' => $countAll - $likeCount,
            'noTrackCount' => $countAll - $trackCount,
    	];
    }
}
<?php

use Illuminate\Support\Facades\Redis;
use App\Services\ItemsServices;

if (!function_exists('testHelper')) {

    /**
     * 測試helper function 是否能啟用
     *
     * @return string
     */
    function testHelper()
    {
        return 'ok';
    }
}

if (!function_exists('fruitName')) {

    function fruitName($fruitId)
    {
        $allFruit = [
        	'-',
        	'桃子',
        	'蘋果',
        	'梨子',
        	'櫻桃',
        	'橘子',
        ];

        return isset($allFruit[$fruitId]) ? $allFruit[$fruitId] : '';
    }
}

if (!function_exists('positionName')) {

    function positionName($positionId)
    {
        $allPosition = [
        	'-',
        	'南半球',
        	'北半球',
        ];

        return isset($allPosition[$positionId]) ? $allPosition[$positionId] : '';
    }
}

if (!function_exists('allLikeTypeTarget')) {

    /**
     * @return []
     */
    function allLikeTypeTarget()
    {
        return [
        	'type' => ['animal', 'npc', 'fish', 'insect', 'fossil', 'art', 'museum', 'diy', 'apparel', 'furniture', 'plant', 'kk'],
        	'likeType' => ['animal', 'fish', 'insect', 'fossil', 'art', 'museum', 'diy', 'items', 'kk'],
        	'target' => ['like', 'track'],
        ];
    }
}

if (!function_exists('computedCount')) {

    /**
     * @return []
     */
    function computedCount($likeType, $type, $needIds = false)
    {
		$likeCount = 0;
		$trackCount = 0;

		$lineId = getUserData('lineId');

		//檢查type
		$allType = allLikeTypeTarget();
		if (!in_array($likeType, $allType['likeType']) || !in_array($type, $allType['type'])) {
			return [
				'likeCount' => $likeCount,
				'trackCount' => $trackCount,
			];
		}

		//like
		$like = Redis::hGetAll($lineId);
	    $likeIds = [];
	    $trackIds = [];

		foreach ($like as $checkKey => $data) {
	        $explode = explode('_', $checkKey);

	        $id = isset($explode[0]) ? $explode[0] : '';
	        $checkLikeType = isset($explode[1]) ? $explode[1] : '';
	        $checkType = isset($explode[2]) ? $explode[2] : '';

	        if ($likeType == $checkLikeType && $type == $checkType) {
	            if ($data == 'like') {
	                $likeIds[] = $id;
	                $likeCount++;
	            }

	            if ($data == 'track') {
	                $trackIds[] = $id;
	                $trackCount++;
	            }
	        }

	        //for 博物館
	        if ($likeType == 'museum' && ($checkLikeType == 'fish' || $checkLikeType == 'insect')) {
	            if ($data == 'like') {
	                $likeIds[] = $id;
	                $likeCount++;
	            }

	            if ($data == 'track') {
	                $trackIds[] = $id;
	                $trackCount++;
	            }
	        }
		}

	    $countAll = 0;

	    switch ($likeType) {
	        case 'animal':
	            if ($type == 'animal') {
	                $countAll = DB::table($likeType)->whereNull('info')->count();
	            } else {
	                $countAll = DB::table($likeType)->where('info', '!=', '')->count();
	            }
            case 'fish':
            case 'insect':
            case 'fossil':
            case 'art':
            case 'diy':
            case 'kk':
                $countAll = DB::table($likeType)->count();
	            break;
            case 'museum':
                $countAll1 = DB::table('fish')->count();
                $countAll2 = DB::table('insect')->count();

                $countAll = $countAll1 + $countAll2;
	            break;
	        case 'items':
	        	$countAll = DB::table('items_new');

	        	//家具
	        	if ($type == 'furniture') {
	        	    $countAll->whereIn('category', ItemsServices::getFurnitureAllType());
	        	} else if ($type == 'apparel') {
	        	    $countAll->whereNotIn('category', ItemsServices::getFurnitureAllType());
	        	} else if ($type == 'plant') {
	        	    $countAll->where('category', '植物');
	        	}

	        	$countAll = $countAll->count();
	    }

	    if ($needIds) {
    		return [
    			'likeCount' => $likeCount,
    			'likeIds' => $likeIds,
    			'trackCount' => $trackCount,
    			'trackIds' => $trackIds,
    	        'noLikeCount' => $countAll - $likeCount,
    	        'noTrackCount' => $countAll - $trackCount,
    		];
	    }

		return [
			'likeCount' => $likeCount,
			'trackCount' => $trackCount,
	        'noLikeCount' => $countAll - $likeCount,
	        'noTrackCount' => $countAll - $trackCount,
		];
    }
}

if (!function_exists('isWebLogin')) {

    /**
     * is web login
     *
     * @return boolean
     */
    function isWebLogin()
    {
  		$webLogin = session('web');
  		$clientIp = request()->ip();

  		if (!is_null($webLogin)) {
  			$token = isset($webLogin['token']) ?? '';
  			$userId = isset($webLogin['userId']) ?? '';

  			$user = DB::table('web_user')
                ->where('line_id', $userId)
                ->first(['remember_token', 'login_ip']);

            if (!is_null($user) && $user->remember_token == $token) {
                if ($clientIp == $user->login_ip) {
                	return true;
                }
            }
  		}

  		return false;
    }
}

if (!function_exists('getUserData')) {

    /**
     * @return []
     */
    function getUserData($params)
    {
    	$loginData = session('web');

    	if (!is_null($loginData)) {
    		if (isset($loginData[$params])) {
    			return $loginData[$params];
    		}
    	}

    	return '';
    }
}

if (!function_exists('computedMainData')) {

    /**
     * @return []
     */
    function computedMainData($lists, $checkLikeType, $checkType)
    {
    	$lineId = getUserData('lineId');
    	//like
    	$like = Redis::hGetAll($lineId);

    	//default
    	foreach ($lists as $key => $value) {
    	   $lists[$key]->token = encrypt($value->id);
    	   $lists[$key]->like = false;
    	   $lists[$key]->track = false;
    	}

    	foreach ($lists as $key => $value) {
    		foreach ($like as $full => $likeData) {
    			$explode = explode("_", $full);
    			$likeId = isset($explode[0]) ? $explode[0] : '';
    			$likeType = isset($explode[1]) ? $explode[1] : '';
    			$type = isset($explode[2]) ? $explode[2] : '';
    			$likeTarget = isset($explode[3]) ? $explode[3] : '';
    			if ($value->id == $likeId && $likeType == $checkLikeType && $checkType == $type) {
    				switch ($likeTarget) {
    					case 'like':
    						$value->like = true;
    						break;
    					case 'track':
    						$value->track = true;
    						break;
    				}
    			}
    		}

    		$lists[$key] = $value;
    	}

    	return $lists;
    }
}

if (!function_exists('lingLoginUrl')) {

    /**
     * @return string
     */
    function lingLoginUrl()
    {
		$authCode = encrypt(env('APP_KEY') . 'lineLogin0121');

		// 組成 Line Login Url
		$url = config('lineLogin.authorize_base_url') . '?';
		$url .= 'response_type=code';
		$url .= '&client_id=' . config('lineLogin.channel_id');
		$url .= '&redirect_uri=' . config('lineLogin.redirect_uri');
		$url .= '&state=' . $authCode;
		$url .= '&scope=openid%20profile';

		return $url;
    }
}

if (!function_exists('getRealConstellation')) {

    /**
     * 星座array
     *
     * @return array
     */
    function getRealConstellation()
    {
    	return [
	    	'白羊座' => ['aries', '3/21-4/19'],
	    	'金牛座' => ['taurus', '4/20-5/20'],
	    	'雙子座' => ['gemini', '5/21-6/21'],
	    	'巨蟹座' => ['cancer', '6/22-7/22'],
	    	'獅子座' => ['leo', '7/23-8/22'],
	    	'處女座' => ['virgo', '8/23-9/22'],
	    	'天秤座' => ['libra', '9/23-10/23'],
	    	'天蠍座' => ['scorpio', '10/24-11/22'],
	    	'射手座' => ['sagittarius', '11/23-12/21'],
	    	'魔羯座' => ['capricorn', '12/22-1/19'],
	    	'水瓶座' => ['aquarius', '1/20-2/18'],
	    	'雙魚座' => ['pisces', '2/19-3/20']
    	];
    }
}

if (!function_exists('printDoc')) {

    /**
     * Doc
     * @return string
     */
    function printDoc()
    {
		$text = '你好 偶是豆丁' . "\n";
		$text .= 'ε٩(๑> ₃ <)۶з' . "\n";
		$text .= 'version ' . config('app.version') . "\n";
		$text .= "\n";
		$text .= '👇以下教您如何使用指令👇' . "\n";
		$text .= '1.輸入【豆丁】，重新查詢教學指令' . "\n";
		$text .= '範例 豆丁' . "\n";
		$text .= "\n";
		$text .= '2.【#】查詢島民、NPC相關資訊' . "\n";
		$text .= '範例 查名稱：#茶茶丸、#Dom、#ちゃちゃまる、#曹賣' . "\n";
		$text .= '範例 查個性：#運動' . "\n";
		$text .= '範例 查種族：#小熊' . "\n";
		$text .= '範例 查生日：#6、#1.21' . "\n";
		$text .= '範例 查戰隊：#阿戰隊' . "\n";
		$text .= '範例 查口頭禪：#哇耶' . "\n";
		$text .= '範例 模糊查詢：#傑' . "\n";
		$text .= "\n";
		$text .= '3.【$】查詢魚、昆蟲圖鑑' . "\n";
		$text .= '範例 查名稱：$黑魚、$金' . "\n";
		$text .= '範例 查月份：$南4月 魚、$北5月 蟲、$全5月 魚' . "\n";
		$text .= '範例 模糊查詢：$黑' . "\n";
		$text .= "\n";
		$text .= '4.【做】查詢DIY圖鑑' . "\n";
		$text .= '範例 查名稱：做石斧頭、做櫻花' . "\n";
		$text .= '範例 查反DIY：做雜草' . "\n";
		$text .= '範例 模糊查詢：做圓木' . "\n";
		$text .= "\n";
		$text .= '5.【找】查詢家具、服飾、雨傘、地墊、植物' . "\n";
		$text .= '範例 查名稱：找貓跳台、找咖啡杯' . "\n";
		$text .= '範例 查名稱：找熱狗、找黃金' . "\n";
		$text .= '範例 查名稱：找金色玫瑰' . "\n";
		$text .= '範例 模糊查詢：找電腦' . "\n";
		$text .= "\n";
		$text .= '6.【查】查詢藝術品' . "\n";
		$text .= '範例 查名稱：查充滿母愛的雕塑' . "\n";
		$text .= '範例 查名稱：查名畫' . "\n";
		$text .= "\n";
		$text .= '7.【化石】查詢化石' . "\n";
		$text .= '範例 查名稱：化石 三葉蟲' . "\n";
		$text .= '範例 查名稱：化石 暴龍' . "\n";
		$text .= '範例 模糊查詢：化石 暴龍' . "\n";
		$text .= "\n";
		$text .= '8.抽 amiibo卡片 (◑‿◐)' . "\n";
		$text .= '範例 抽' . "\n";
		$text .= "\n";
		$text .= '9.豆丁搜尋排行榜' . "\n";
		$text .= '範例 請輸入 搜尋排行榜' . "\n";
		$text .= "\n";
		$text .= '10.【#】動物相容性分析 (動物間需用空白隔開)' . "\n";
		$text .= '範例 查名稱：#阿一 阿二 阿三 阿四' . "\n";
		$text .= '範例 查名稱：#茶茶丸 傑客 美玲 小潤 章丸丸 草莓' . "\n";
		$text .= "\n";
		$text .= '👇 詳細圖文解說 👇' . "\n";
		$text .= env('APP_URL') . '/instructions';

		return $text;
    }
}

if (!function_exists('checkTimeClass')) {

    /**
     * @return string
     */
    function checkTimeClass($time, $target)
    {
    	$class = '';

    	if ($time == '全天') {
    	    $class = 'has';
    	} else {
    	    $checkDate = explode('~', $time);
    	    $start = isset($checkDate[0]) ? $checkDate[0] : 0;
    	    $end = isset($checkDate[1]) ? $checkDate[1] : 0;
    	    $range1 = [];
    	    $range2 = [];

			if ($start > $end) {
				$range1 = range($start, 23);
				$range2 = range(0, $end);
			} else {
				$range1 = range($start, $end);
			}

			if (!empty($range1) && in_array($target, $range1)) {
				$class = 'has';
			}

			if (!empty($range2) && in_array($target, $range2)) {
				$class = 'has';
			}
    	}

    	$nowHour = date('H');

    	if ($nowHour == $target) {
    		$class .= ' current';
    	}

    	return $class;
    }
}


if (!function_exists('constellation')) {

    /**
     * 判斷星座屬性
     *
     * @return string
     */
    function constellation($bd)
    {
    	//火
    	$fire = [
    		[
    			'start' => '3.21',
    			'end' => '4.19',
    		],
    		[
    			'start' => '7.23',
    			'end' => '8.22',
    		],
    		[
    			'start' => '11.23',
    			'end' => '12.21',
    		],
    	];

    	foreach ($fire as $data) {
    		if (strtotime($bd) >= strtotime($data['start']) && strtotime($bd) <= strtotime($data['end']))  {
    			return '火';
    		}
    	}

    	//地
    	$di = [
    		[
    			'start' => '4.20',
    			'end' => '5.20',
    		],
    		[
    			'start' => '8.23',
    			'end' => '9.22',
    		],
    		[
    			'start' => '12.22',
    			'end' => '12.31',
    		],
    		[
    			'start' => '1.1',
    			'end' => '1.19',
    		],
    	];

    	foreach ($di as $data) {
    		if (strtotime($bd) >= strtotime($data['start']) && strtotime($bd) <= strtotime($data['end']))  {
    			return '地';
    		}
    	}

    	//風
    	$phone = [
    		[
    			'start' => '5.21',
    			'end' => '6.21',
    		],
    		[
    			'start' => '9.23',
    			'end' => '10.23',
    		],
    		[
    			'start' => '1.20',
    			'end' => '2.18',
    		],
    	];

    	foreach ($phone as $data) {
    		if (strtotime($bd) >= strtotime($data['start']) && strtotime($bd) <= strtotime($data['end']))  {
    			return '風';
    		}
    	}

    	//剩下就是水
    	return '水';
    }
}

if (!function_exists('matchmaking')) {

    /**
     * 媒合度計算
     *
     * @return []
     */
    function matchmaking($lists, $names)
    {
    	$result = [];
    	$resultScore = 0;
    	$resultSum = 0;
    	$good = 0;
    	$bad = 0;

		foreach ($lists as $target) {
			$detail = [];
			//性格
			$perScore = 0;
			$perScoreTotal = 0;
			//星座
			$matchScore = 0;
			$matchScoreTotal = 0;
			//種族
			$raceScore = 0;
			$raceScoreTotal = 0;
			//全部加起來
			$totalSum = 0;
			/*
				分數
				如果10點以上，兼容性很好
				在5到9分的情況下，兼容性正常或良好
				4點以下時兼容性差
			 */
			$score = 0;

			foreach ($lists as $check) {
				if ($target->id != $check->id) {
					$perScore = computedPer($target, $check);
					$matchScore = computedMatch($target, $check);
					$raceScore = computedRace($target, $check);
					$class = '';

					$sum = $perScore + $matchScore + $raceScore;
					$perScoreTotal += $perScore;
					$matchScoreTotal += $matchScore;
					$raceScoreTotal += $raceScore;
					$totalSum = $perScoreTotal + $matchScoreTotal + $raceScoreTotal;

					if ($sum > 10) {
						$score++;
						$good++;
						$class = 'good';
					}

					if ($sum <= 3) {
						$score--;
						$bad++;
						$class = 'bad';
					}

					$detail[] = [
						'name' => $check->name,
						'perScore' => $perScore,
						'matchScore' => $matchScore,
						'raceScore' => $raceScore,
						'sum' => $sum,
						'class' => $class,
					];
				} else {
					$detail[] = [
						'name' => $check->name,
					];
				}
			}

			$target->perScoreTotal = $perScoreTotal;
			$target->matchScoreTotal = $matchScoreTotal;
			$target->raceScoreTotal = $raceScoreTotal;
			$target->totalSum = $totalSum;
			$target->detail = $detail;
			$target->score = $score;

			$resultScore += $score;
			$resultSum += $totalSum;

			$result[] = $target;
		}

		return [
			'data' => $result,
			'resultSum' => $resultSum,
			'resultScore' => round($resultScore / 2),
			'good' => round($good / 2),
			'bad' => round($bad / 2),
			'names' => $names,
			'perArray' => computedPer([], [], true),
			'matchArray' => computedMatch([], [], true),
		];
    }
}

if (!function_exists('computedPer')) {

    /**
     * 性格分數
     *
     * @return string
     */
    function computedPer($target, $check, $returnType = false)
    {
    	$formatScore = [];
    	$score = 0;
    	//all type
    	$allType = ['普通', '元氣', '成熟', '大姐姐', '悠閒', '運動', '暴躁', '自戀'];

    	foreach ($allType as $type) {
	    	foreach ($allType as $key => $checkType) {
	    		if ($type == '普通') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 3;
	    					break;
	    				case 1: //元氣
	    					$score = 1;
	    					break;
	    				case 2: //成熟
	    					$score = 5;
	    					break;
	    				case 3: //大姐姐
	    					$score = 2;
	    					break;
	    				case 4: //悠閒
	    					$score = 1;
	    					break;
	    				case 5: //運動
	    					$score = 2;
	    					break;
	    				case 6: //暴躁
	    					$score = 3;
	    					break;
	    				case 7: //自戀
	    					$score = 5;
	    					break;
	    			}
	    		}

	    		if ($type == '元氣') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 1;
	    					break;
	    				case 1: //元氣
	    					$score = 5;
	    					break;
	    				case 2: //成熟
	    					$score = 2;
	    					break;
	    				case 3: //大姐姐
	    					$score = 3;
	    					break;
	    				case 4: //悠閒
	    					$score = 2;
	    					break;
	    				case 5: //運動
	    					$score = 5;
	    					break;
	    				case 6: //暴躁
	    					$score = 1;
	    					break;
	    				case 7: //自戀
	    					$score = 3;
	    					break;
	    			}
	    		}

	    		if ($type == '成熟') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 5;
	    					break;
	    				case 1: //元氣
	    					$score = 2;
	    					break;
	    				case 2: //成熟
	    					$score = 3;
	    					break;
	    				case 3: //大姐姐
	    					$score = 1;
	    					break;
	    				case 4: //悠閒
	    					$score = 3;
	    					break;
	    				case 5: //運動
	    					$score = 1;
	    					break;
	    				case 6: //暴躁
	    					$score = 5;
	    					break;
	    				case 7: //自戀
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '大姐姐') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 2;
	    					break;
	    				case 1: //元氣
	    					$score = 3;
	    					break;
	    				case 2: //成熟
	    					$score = 1;
	    					break;
	    				case 3: //大姐姐
	    					$score = 5;
	    					break;
	    				case 4: //悠閒
	    					$score = 5;
	    					break;
	    				case 5: //運動
	    					$score = 2;
	    					break;
	    				case 6: //暴躁
	    					$score = 2;
	    					break;
	    				case 7: //自戀
	    					$score = 1;
	    					break;
	    			}
	    		}

	    		if ($type == '悠閒') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 1;
	    					break;
	    				case 1: //元氣
	    					$score = 2;
	    					break;
	    				case 2: //成熟
	    					$score = 3;
	    					break;
	    				case 3: //大姐姐
	    					$score = 5;
	    					break;
	    				case 4: //悠閒
	    					$score = 5;
	    					break;
	    				case 5: //運動
	    					$score = 1;
	    					break;
	    				case 6: //暴躁
	    					$score = 2;
	    					break;
	    				case 7: //自戀
	    					$score = 3;
	    					break;
	    			}
	    		}

	    		if ($type == '運動') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 2;
	    					break;
	    				case 1: //元氣
	    					$score = 5;
	    					break;
	    				case 2: //成熟
	    					$score = 1;
	    					break;
	    				case 3: //大姐姐
	    					$score = 2;
	    					break;
	    				case 4: //悠閒
	    					$score = 1;
	    					break;
	    				case 5: //運動
	    					$score = 5;
	    					break;
	    				case 6: //暴躁
	    					$score = 3;
	    					break;
	    				case 7: //自戀
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '暴躁') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 3;
	    					break;
	    				case 1: //元氣
	    					$score = 1;
	    					break;
	    				case 2: //成熟
	    					$score = 5;
	    					break;
	    				case 3: //大姐姐
	    					$score = 2;
	    					break;
	    				case 4: //悠閒
	    					$score = 2;
	    					break;
	    				case 5: //運動
	    					$score = 3;
	    					break;
	    				case 6: //暴躁
	    					$score = 5;
	    					break;
	    				case 7: //自戀
	    					$score = 1;
	    					break;
	    			}
	    		}

	    		if ($type == '自戀') {
	    			switch ($key) {
	    				case 0: //普通
	    					$score = 5;
	    					break;
	    				case 1: //元氣
	    					$score = 3;
	    					break;
	    				case 2: //成熟
	    					$score = 2;
	    					break;
	    				case 3: //大姐姐
	    					$score = 1;
	    					break;
	    				case 4: //悠閒
	    					$score = 3;
	    					break;
	    				case 5: //運動
	    					$score = 2;
	    					break;
	    				case 6: //暴躁
	    					$score = 1;
	    					break;
	    				case 7: //自戀
	    					$score = 5;
	    					break;
	    			}
	    		}

	    		$formatScore[] = [
	    			'from' => $type,
	    			'to' => $checkType,
	    			'score' => $score,
	    		];
	    	}
    	}

    	if ($returnType) {
    		return [
    			'type' => $allType,
    			'scoreDetail' => $formatScore,
    		];
    	}

    	//確認分數
    	foreach($formatScore as $chceckScore) {
    		if ($chceckScore['from'] == $target->personality && $chceckScore['to'] == $check->personality) {
    			return $chceckScore['score'];
    		}
    	}

    	return 0;
    }
}

if (!function_exists('computedMatch')) {

    /**
     * 星座分數
     *
     * @return string
     */
    function computedMatch($target, $check, $returnType = false)
    {
    	$allType = ['火', '地', '風', '水'];
    	$formatScore = [];
    	$score = 0;

		foreach ($allType as $type) {
	    	foreach ($allType as $key => $checkType) {
	    		if ($type == '火') {
	    			switch ($key) {
	    				case 0: //火
	    					$score = 5;
	    					break;
	    				case 1: //地
	    					$score = 2;
	    					break;
	    				case 2: //風
	    					$score = 2;
	    					break;
	    				case 3: //水
	    					$score = 0;
	    					break;
	    			}
	    		}

	    		if ($type == '地') {
	    			switch ($key) {
	    				case 0: //火
	    					$score = 2;
	    					break;
	    				case 1: //地
	    					$score = 5;
	    					break;
	    				case 2: //風
	    					$score = 0;
	    					break;
	    				case 3: //水
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '風') {
	    			switch ($key) {
	    				case 0: //火
	    					$score = 2;
	    					break;
	    				case 1: //地
	    					$score = 0;
	    					break;
	    				case 2: //風
	    					$score = 5;
	    					break;
	    				case 3: //水
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '水') {
	    			switch ($key) {
	    				case 0: //火
	    					$score = 0;
	    					break;
	    				case 1: //地
	    					$score = 2;
	    					break;
	    				case 2: //風
	    					$score = 2;
	    					break;
	    				case 3: //水
	    					$score = 5;
	    					break;
	    			}
	    		}

	    		$formatScore[] = [
	    			'from' => $type,
	    			'to' => $checkType,
	    			'score' => $score,
	    		];
	    	}
	    }

	    if ($returnType) {
	    	return [
	    		'type' => $allType,
	    		'scoreDetail' => $formatScore,
	    	];
	    }

	    //確認分數
	    foreach($formatScore as $chceckScore) {
	    	if ($chceckScore['from'] == $target->constellation && $chceckScore['to'] == $check->constellation) {
	    		return $chceckScore['score'];
	    	}
	    }

	    return 0;
    }
}

if (!function_exists('computedRace')) {

    /**
     * 種族分數
     *
     * @return string
     */
    function computedRace($target, $check)
    {
		/*  --5分--
	    	1.狗和狼
	    	2.熊和小熊
	    	3.山羊和綿羊
	    	4.老虎和貓
	    	5.公牛和母牛
	    	6.無尾熊和袋鼠
		*/
		if ($target->race == '狗' && $check->race == '狼' || $check->race == '狗' && $target->race == '狼') {
			return 5;
		}

		if ($target->race == '牛' && $check->race == '牛') {
			if ($target->sex == '♀' && $check->race == '♂' || $check->sex == '♀' && $target->race == '♂') {
				return 5;
			}
		}

		if ($target->race == '大熊' && $check->race == '小熊' || $check->race == '大熊' && $target->race == '小熊') {
			return 5;
		}

		if ($target->race == '山羊' && $check->race == '綿羊' || $check->race == '山羊' && $target->race == '綿羊') {
			return 5;
		}

		if ($target->race == '老虎' && $check->race == '貓' || $check->race == '老虎' && $target->race == '貓') {
			return 5;
		}

		if ($target->race == '無尾熊' && $check->race == '袋鼠' || $check->race == '無尾熊' && $target->race == '袋鼠') {
			return 5;
		}

		/*  --3分--
			次要:
			1.同種族
			2.松鼠和老鼠
			3.松鼠和倉鼠
			4.老鼠和倉鼠
			5.馬和鹿
		 */

		if ($target->race == $check->race) {
			return 3;
		}

		if ($target->race == '松鼠' && $check->race == '老鼠' || $check->race == '松鼠' && $target->race == '老鼠') {
			return 3;
		}

		if ($target->race == '松鼠' && $check->race == '倉鼠' || $check->race == '松鼠' && $target->race == '倉鼠') {
			return 3;
		}

		if ($target->race == '老鼠' && $check->race == '倉鼠' || $check->race == '老鼠' && $target->race == '倉鼠') {
			return 3;
		}

		if ($target->race == '馬' && $check->race == '鹿' || $check->race == '馬' && $target->race == '鹿') {
			return 3;
		}

		return 2;
    }
}

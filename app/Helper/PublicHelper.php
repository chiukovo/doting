<?php

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

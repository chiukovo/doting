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
    			'end' => '1.19',
    		],
    	];

    	foreach ($di as $data) {
    		if (strtotime($bd) >= strtotime($data['start']) && strtotime($bd) <= strtotime($data['end']))  {
    			return '地	';
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
    			return '地';
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
    function matchmaking($lists)
    {
    	$result = [];

		foreach ($lists as $target) {
			//性格
			$perScore = 0;
			//星座
			$matchScore = 0;
			//種族
			$raceScore = 0;

			foreach ($lists as $check) {
				if ($target->id != $check->id) {
					$perScore = computedPer($target, $check);
					$matchScore = computedMatch($target, $check);
				}
			}
		}
    }
}

if (!function_exists('computedPer')) {

    /**
     * 性格分數
     *
     * @return string
     */
    function computedPer($target, $check)
    {
    	$formatScore = [];
    	$score = 0;
    	//all type
    	$allType = ['普通', '元氣', '成熟', '大姐姐', '悠閒', '運動', '暴躁', '自戀'];
    	
    	foreach ($allType as $type) {
	    	foreach ($allType as $key => $checkType) {
	    		if ($type == '普通') {
	    			switch ($key) {
	    				case 0:
	    					$score = 2;
	    					break;
	    				case 1:
	    					$score = 1;
	    					break;
	    				case 2:
	    					$score = 5;
	    					break;
	    				case 3:
	    					$score = 2;
	    					break;
	    				case 4:
	    					$score = 1;
	    					break;
	    				case 5:
	    					$score = 2;
	    					break;
	    				case 6:
	    					$score = 2;
	    					break;
	    				case 7:
	    					$score = 5;
	    					break;
	    			}
	    		}

	    		if ($type == '元氣') {
	    			switch ($key) {
	    				case 0:
	    					$score = 1;
	    					break;
	    				case 1:
	    					$score = 5;
	    					break;
	    				case 2:
	    					$score = 2;
	    					break;
	    				case 3:
	    					$score = 2;
	    					break;
	    				case 4:
	    					$score = 2;
	    					break;
	    				case 5:
	    					$score = 5;
	    					break;
	    				case 6:
	    					$score = 1;
	    					break;
	    				case 7:
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '成熟') {
	    			switch ($key) {
	    				case 0:
	    					$score = 5;
	    					break;
	    				case 1:
	    					$score = 2;
	    					break;
	    				case 2:
	    					$score = 2;
	    					break;
	    				case 3:
	    					$score = 1;
	    					break;
	    				case 4:
	    					$score = 2;
	    					break;
	    				case 5:
	    					$score = 1;
	    					break;
	    				case 6:
	    					$score = 5;
	    					break;
	    				case 7:
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '大姐姐') {
	    			switch ($key) {
	    				case 0:
	    					$score = 2;
	    					break;
	    				case 1:
	    					$score = 2;
	    					break;
	    				case 2:
	    					$score = 1;
	    					break;
	    				case 3:
	    					$score = 5;
	    					break;
	    				case 4:
	    					$score = 5;
	    					break;
	    				case 5:
	    					$score = 2;
	    					break;
	    				case 6:
	    					$score = 2;
	    					break;
	    				case 7:
	    					$score = 1;
	    					break;
	    			}
	    		}

	    		if ($type == '悠閒') {
	    			switch ($key) {
	    				case 0:
	    					$score = 1;
	    					break;
	    				case 1:
	    					$score = 2;
	    					break;
	    				case 2:
	    					$score = 2;
	    					break;
	    				case 3:
	    					$score = 5;
	    					break;
	    				case 4:
	    					$score = 5;
	    					break;
	    				case 5:
	    					$score = 1;
	    					break;
	    				case 6:
	    					$score = 2;
	    					break;
	    				case 7:
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '運動') {
	    			switch ($key) {
	    				case 0:
	    					$score = 2;
	    					break;
	    				case 1:
	    					$score = 5;
	    					break;
	    				case 2:
	    					$score = 1;
	    					break;
	    				case 3:
	    					$score = 2;
	    					break;
	    				case 4:
	    					$score = 1;
	    					break;
	    				case 5:
	    					$score = 5;
	    					break;
	    				case 6:
	    					$score = 2;
	    					break;
	    				case 7:
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '暴躁') {
	    			switch ($key) {
	    				case 0:
	    					$score = 2;
	    					break;
	    				case 1:
	    					$score = 1;
	    					break;
	    				case 2:
	    					$score = 5;
	    					break;
	    				case 3:
	    					$score = 2;
	    					break;
	    				case 4:
	    					$score = 2;
	    					break;
	    				case 5:
	    					$score = 2;
	    					break;
	    				case 6:
	    					$score = 5;
	    					break;
	    				case 7:
	    					$score = 1;
	    					break;
	    			}
	    		}

	    		if ($type == '自戀') {
	    			switch ($key) {
	    				case 0:
	    					$score = 5;
	    					break;
	    				case 1:
	    					$score = 2;
	    					break;
	    				case 2:
	    					$score = 2;
	    					break;
	    				case 3:
	    					$score = 1;
	    					break;
	    				case 4:
	    					$score = 2;
	    					break;
	    				case 5:
	    					$score = 2;
	    					break;
	    				case 6:
	    					$score = 1;
	    					break;
	    				case 7:
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
    function computedMatch($target, $check)
    {
    	$allType = ['火', '地', '風', '水'];
    	$formatScore = [];
    	$score = 0;

		foreach ($allType as $type) {
	    	foreach ($allType as $key => $checkType) {
	    		if ($type == '火') {
	    			switch ($key) {
	    				case 0:
	    					$score = 5;
	    					break;
	    				case 1:
	    					$score = 2;
	    					break;
	    				case 2:
	    					$score = 2;
	    					break;
	    				case 3:
	    					$score = 1;
	    					break;
	    			}
	    		}

	    		if ($type == '地') {
	    			switch ($key) {
	    				case 0:
	    					$score = 2;
	    					break;
	    				case 1:
	    					$score = 5;
	    					break;
	    				case 2:
	    					$score = 1;
	    					break;
	    				case 3:
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '風') {
	    			switch ($key) {
	    				case 0:
	    					$score = 2;
	    					break;
	    				case 1:
	    					$score = 1;
	    					break;
	    				case 2:
	    					$score = 5;
	    					break;
	    				case 3:
	    					$score = 2;
	    					break;
	    			}
	    		}

	    		if ($type == '水') {
	    			switch ($key) {
	    				case 0:
	    					$score = 1;
	    					break;
	    				case 1:
	    					$score = 2;
	    					break;
	    				case 2:
	    					$score = 1;
	    					break;
	    				case 3:
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

	    //確認分數
	    foreach($formatScore as $chceckScore) {
	    	if ($chceckScore['from'] == $target->constellation && $chceckScore['to'] == $check->constellation) {
	    		return $chceckScore['score'];
	    	}
	    }

	    return 0;
    }
}
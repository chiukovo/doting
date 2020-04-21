<?php

namespace App\Services;

use DB;

class OtherServices
{
    public static function getDataByMessage($message)
    {
    	$other = [];
    	$notFound = '找不到捏...(¬_¬)';

    	//first
    	$first = mb_substr($message, 0, 1);

    	if ($first == '南' || $first == '北' || $first == '全') {
    	    $number = mb_substr($message, 1, 1);
    	    $dateRange = range(1, 12);
    	    //type
    	    $type = mb_substr($message, -1, 1);
    	    $table = '';

    	    if (in_array($number, $dateRange)) {
    	        if ($type == '魚') {
    	            $table = 'fish';
    	        } else if ($type == '蟲') {
    	            $table = 'insect';
    	        }

    	        if ($table != '') {
    	            $other = DB::table($table)
    	                ->where('m' . $number, $first)
    	                ->orderBy('sell', 'desc')
    	                ->get()
    	                ->toArray();
    	        }

    	        if (!empty($other)) {
    	            return $other;
    	        }
    	    }
    	}

    	//找蟲
    	$other = DB::table('insect')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orderBy('sell', 'desc')
    	    ->get()
    	    ->toArray();

    	if (!empty($other)) {
    	    return $other;
    	}

    	//找魚
    	$other = DB::table('fish')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orderBy('sell', 'desc')
    	    ->get()
    	    ->toArray();

    	if (empty($other)) {
    	    return $notFound;
    	}

    	return $other;
    }
}
<?php

namespace App\Services;

use DB;

class DiyServices
{
    public static function getDataByMessage($message)
    {
    	$message = strtolower($message);
    	$notFound = '找不到捏...(¬_¬)';

    	$dbAnimal = DB::table('diy')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->get()
    	    ->toArray();

    	if (empty($dbAnimal)) {
    	    return $notFound;
    	}

    	return $dbAnimal;
    }

    public static function getSendData($dataArray)
    {
    	$str = '';

    	if (is_array($dataArray) && !empty($dataArray)) {
    	    foreach ($dataArray as $data) {
    	        $str .= $data->name;

    	        if ($data->type != '') {
    	            $str .= ' (' . $data->type . ')';
    	        }

    	        $str .= "\n";

    	        if ($data->get != '') {
    	            $str .= $data->get;
    	            $str .= "\n";
    	        }

    	        $str .= $data->diy;
    	        $str .= "\n";
    	        $str .= "\n";
    	    }
    	} else {
    	    $str = '找不到此Diy捏...(¬_¬)';
    	}

    	return $str;
    }
}
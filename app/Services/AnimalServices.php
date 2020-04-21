<?php

namespace App\Services;

use DB;

class AnimalServices
{
    public static function getDataByMessage($message)
    {
    	$message = strtolower($message);
    	$notFound = '找不到捏...(¬_¬)';

    	//阿戰隊
    	if ($message == '阿戰隊') {
    	    $name = ['阿一', '阿二', '阿三', '阿四'];
    	    $dbAnimal = DB::table('animal')
    	        ->whereIn('name', $name)
    	        ->orderBy('jp_name', 'asc')
    	        ->get()
    	        ->toArray();

    	    return $dbAnimal;
    	}

    	$dbAnimal = DB::table('animal')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orWhere('race', 'like', '%' . $message . '%')
    	    ->orWhere('en_name', 'like', '%' . $message . '%')
    	    ->orWhere('jp_name', 'like', '%' . $message . '%')
    	    ->orWhere('personality', 'like', '%' . $message . '%')
    	    ->orWhere('bd_m', $message)
    	    ->orWhere('bd', $message)
    	    ->orderBy('bd', 'asc')
    	    ->get()
    	    ->toArray();

    	if (empty($dbAnimal)) {
    	    return $notFound;
    	}

    	return $dbAnimal;
    }
}
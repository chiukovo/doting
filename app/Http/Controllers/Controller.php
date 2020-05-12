<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successMsg($msg)
    {
        return new JsonResponse([
            'result'    => 'success',
            'msg'       => $msg
        ]);
    }

    public function errorMsg($msg)
    {
        return new JsonResponse([
            'result'    => 'error',
            'msg'       => $msg
        ]);
    }

    public function successData($data)
    {
    	return new JsonResponse([
            'result' 	 => 'success',
            'data' 		 => $data
        ]);
    }

    public function errorData($data)
    {
    	return new JsonResponse([
            'result' 	 => 'error',
            'data' 		 => $data
        ]);
    }

    public function errorMsgProcess($msg)
    {
        //語法錯誤時
        if(strpos($msg, 'SQL') !== false){
            $msg = 'SQL syntax error';
        }

        return $msg;
    }
}

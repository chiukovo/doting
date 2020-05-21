<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Curl, Log, DB;

class LikeController extends Controller
{
    public function toggleLike(Request $request)
    {
    	$request = $request->input();
    }
}
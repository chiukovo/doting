<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AnimalServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class AnimalWebCrossingController extends Controller
{
    public function list(Request $request)
    {
        return view('animals.list');
    }

    public function getAnimalSearch(Request $request)
    {
        $race = $request->input('race', '');
        $personality = $request->input('personality', '');
        $bd = $request->input('bd', '');

        $lists = DB::table('animal')
            ->select()
            ->paginate(30)
            ->toArray();

        return $lists['data'];
    }

    public function getAllType()
    {
        return AnimalServices::getAllType();
    }
}
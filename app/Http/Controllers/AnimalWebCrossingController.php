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

    public function detail(Request $request)
    {
        $name = $request->input('name');

        if ($name == '') {
            return redirect('animals/list');
        }

        $detail = DB::table('animal')
            ->where('name', $name)
            ->first();

        if (is_null($detail)) {
            return redirect('animals/list');
        }

        return view('animals.detail', [
            'detail' => $detail
        ]);
    }

    public function getAnimalSearch(Request $request)
    {
        $race = $request->input('race', []);
        $personality = $request->input('personality', []);
        $bd = $request->input('bd', []);

        $lists = DB::table('animal');

        if (!empty($race) && is_array($race)) {
            $lists->whereIn('race', $race);
        }

        if (!empty($personality) && is_array($personality)) {
            foreach ($personality as $data) {
                $lists->where('personality', 'like', '%' . $data . '%');
            }
        }

        if (!empty($bd) && is_array($bd)) {
            $lists->whereIn('bd_m', $bd);
        }

        $lists = $lists->select()
            ->paginate(30)
            ->toArray();


        return $lists['data'];
    }

    public function getAllType()
    {
        return AnimalServices::getAllType();
    }
}
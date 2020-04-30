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
        $text = $request->input('text', '');
        $type = $request->route()->getName();
        $type = is_null($type) ? '' : $type;

        return view('animals.list', [
            'type' => $type,
            'text' => $text,
        ]);
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

        if ($detail->kk != '') {
            //format
            $detail->kk = str_replace(".", "", $detail->kk);
            $detail->kk = str_replace(" ", "_", $detail->kk);
            $detail->kk = str_replace("'", "", $detail->file_name);
            $detail->kk = $detail->kk . '_Live';
        }

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
        $text = $request->input('text', '');
        $page = $request->input('page', 1);
        $type = $request->input('type', '');

        if ($text != '') {
            $result = AnimalServices::getDataByMessage($text, $page, $type);

            if (is_array($result)) {
                return $result;
            }

            return [];
        }

        $lists = DB::table('animal');

        if ($type == 'npc') {
            $lists = $lists->where('info', '!=', '');
        }

        if (!empty($race) && is_array($race)) {
            $lists->whereIn('race', $race);
        }

        if (!empty($personality) && is_array($personality)) {
            foreach ($personality as $key => $data) {
                $lists->where('personality', 'like', '%' . $data . '%');

                if ($key != 0) {
                    $lists->orWhere('personality', 'like', '%' . $data . '%');
                }
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

    public function getAllType(Request $request)
    {
        $type = $request->input('type', '');

        return AnimalServices::getAllType($type);
    }
}
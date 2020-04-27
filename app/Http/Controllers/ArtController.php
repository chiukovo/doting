<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OtherServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class ArtController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');

        return view('art.list', [
            'text' => $text,
        ]);
    }

    public function getArtSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');

        $art = DB::table('art');

        if ($text != '') {
           $art->where('name', 'like', '%' . $text . '%');
        }

        $art = $art->select()
            ->paginate(50)
            ->toArray();

        return $art['data'];
    }

    public function detail(Request $request)
    {
        $name = $request->input('name');

        if ($name == '') {
            return redirect('art/list');
        }

        $detail = DB::table('art')
            ->where('name', $name)
            ->first();

        if (is_null($detail)) {
            return redirect('animals/list');
        }

        return view('art.detail', [
            'detail' => $detail
        ]);
    }
}
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
        $type = $request->input('type', '');
        $target = $request->input('target', '');

        $art = DB::table('art');

        if ($text != '') {
           $art->where('name', 'like', '%' . $text . '%');
        }

        //check target
        if ($target != '') {
            $getCount = computedCount($type, $type, true);

            switch ($target) {
                case 'like':
                    $art->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $art->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $art->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $art->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $art = $art->select()
            ->paginate(50)
            ->toArray();

        //encode id and like current
        $art['data'] = computedMainData($art['data'], $type, $type);

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
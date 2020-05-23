<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Curl, Log, DB;

class FossilController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');

        return view('fossil.list', [
            'text' => $text,
        ]);
    }

    public function getFossilSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');
        $type = $request->input('type', '');
        $target = $request->input('target', '');

        $result = DB::table('fossil');

        if ($text != '') {
           $result->where('name', 'like', '%' . $text . '%');
        }

        //check target
        if ($target != '') {
            $getCount = computedCount($type, $type, true);

            switch ($target) {
                case 'like':
                    $result->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $result->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $result->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $result->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $result = $result->select()
            ->orderBy('sell', 'desc')
            ->paginate(30)
            ->toArray();

        //encode id and like current
        $result['data'] = computedMainData($result['data'], $type, $type);

        return $result['data'];
    }
}
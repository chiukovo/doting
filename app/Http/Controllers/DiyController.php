<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DiyServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class DiyController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');
        $target = $request->input('target', '');

        return view('diy.list', [
            'text' => $text,
            'target' => $target,
        ]);
    }

    public function getDiySearch(Request $request)
    {
        $text = $request->input('text', '');
        $type = $request->input('type', '');
        $target = $request->input('target', '');

        $diy = DB::table('diy');

        if ($text != '') {
           $diy->where('name', 'like', '%' . $text . '%')
                ->orWhere('diy', 'like', '%' . $text . '%');
        }

        //check target
        if ($target != '') {
            $getCount = computedCount($type, $type, true);

            switch ($target) {
                case 'like':
                    $diy->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $diy->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $diy->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $diy->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $diy = $diy->select()
            ->orderBy('id', 'desc')
            ->paginate(30)
            ->toArray();

        //encode id and like current
        $diy['data'] = computedMainData($diy['data'], $type, $type);

        return $diy['data'];
    }
}
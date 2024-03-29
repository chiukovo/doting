<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Curl, Log, DB;

class KKController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');
        $target = $request->input('target', '');

        return view('kk.list', [
            'text' => $text,
            'target' => $target,
        ]);
    }

    public function getKKSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');
        $type = $request->input('type', '');
        $target = $request->input('target', '');

        $kk = DB::table('kk');

        if ($text != '') {
           $kk->where('name', 'like', '%' . $text . '%');
           $kk->orWhere('cn_name', 'like', '%' . $text . '%');
        }

        //check target
        if ($target != '') {
            $getCount = computedCount($type, $type, true);

            switch ($target) {
                case 'like':
                    $kk->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $kk->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $kk->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $kk->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $kk = $kk->select()
            ->paginate(30)
            ->toArray();

        //encode id and like current
        $kk['data'] = computedMainData($kk['data'], $type, $type);

        return $kk['data'];
    }

    public function detail(Request $request)
    {
        $name = $request->input('name');

        if ($name == '') {
            return redirect('kk/list');
        }

        $detail = DB::table('kk')
            ->where('img_name', $name)
            ->first();

        if (is_null($detail)) {
            return redirect('kk/list');
        }
        
        if ($detail->file_name != '') {
            //format
            $detail->file_name = str_replace(".", "", $detail->file_name);
            $detail->file_name = str_replace(" ", "_", $detail->file_name);
            $detail->file_name = str_replace("'", "", $detail->file_name);
            $detail->file_name = $detail->file_name . '_Live';
        }

        $type = 'kk';
        $token = encrypt($detail->id);
        //encode id and like current
        $result = computedMainData([$detail], $type, $type);

        return view('kk.detail', [
            'type' => $type,
            'token' => $token,
            'detail' => $result[0]
        ]);
    }

}
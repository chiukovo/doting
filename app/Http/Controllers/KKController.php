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

        return view('kk.list', [
            'text' => $text,
        ]);
    }

    public function getKKSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');

        $kk = DB::table('kk');

        if ($text != '') {
           $kk->where('name', 'like', '%' . $text . '%');
        }

        $kk = $kk->select()
            ->paginate(30)
            ->toArray();

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

        if ($detail->file_name != '') {
            //format
            $detail->file_name = str_replace(".", "", $detail->file_name);
            $detail->file_name = str_replace(" ", "_", $detail->file_name);
            $detail->file_name = str_replace("'", "", $detail->file_name);
            $detail->file_name = $detail->file_name . '_Live';
        }


        if (is_null($detail)) {
            return redirect('kk/list');
        }

        return view('kk.detail', [
            'detail' => $detail
        ]);
    }

}
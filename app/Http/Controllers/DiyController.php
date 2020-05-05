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

        return view('diy.list', [
            'text' => $text,
        ]);
    }

    public function getDiySearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');

        $diy = DB::table('diy');

        if ($text != '') {
           $diy->where('name', 'like', '%' . $text . '%')
                ->orWhere('diy', 'like', '%' . $text . '%');
        }

        $diy = $diy->select()
            ->paginate(30)
            ->toArray();

        return $diy['data'];
    }
}
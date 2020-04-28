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

        $result = DB::table('fossil');

        if ($text != '') {
           $result->where('name', 'like', '%' . $text . '%');
        }

        $result = $result->select()
            ->orderBy('sell', 'desc')
            ->paginate(30)
            ->toArray();

        return $result['data'];
    }
}
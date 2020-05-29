<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Curl, Log, DB;

class ExpressionController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');
        $target = $request->input('target', '');

        return view('expression.list', [
            'text' => $text,
            'target' => $target,
        ]);
    }

    public function getSearch(Request $request)
    {
        $result = [];
        $text = $request->input('text', '');
        $type = $request->input('type', '');
        $category = $request->input('category', '');
        $target = $request->input('target', '');

        $expression = DB::table('expression');

        if ($text != '') {
           $expression->where('name', 'like', '%' . $text . '%');
        }

        if (!empty($category) && is_array($category)) {
            $expression->where(function($q) use ($category) {
              $q->whereIn('from', $category)
                ->orWhere('from', '全部');
            });
        }

        //check target
        if ($target != '') {
            $getCount = computedCount($type, $type, true);

            switch ($target) {
                case 'like':
                    $expression->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $expression->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $expression->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $expression->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $expression = $expression->select()
            ->paginate(50)
            ->toArray();

        //encode id and like current
        $expression['data'] = computedMainData($expression['data'], $type, $type);

        return $expression['data'];
    }

    public function getAllType()
    {
        $items = DB::table('expression');
        $items = $items->get(['from as category'])
            ->toArray();

        //race
        $category = collect($items)->unique('category');

        return [
            'category' => $category,
        ];
    }
}
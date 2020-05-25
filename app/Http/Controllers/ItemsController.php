<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ItemsServices;
use Illuminate\Http\Request;
use Curl, Log, DB;

class ItemsController extends Controller
{
    public function list(Request $request)
    {
        $text = $request->input('text', '');
        $type = $request->route()->getName();
        $type = is_null($type) ? 'apparel' : $type;
        $target = $request->input('target', '');

        return view('items.list', [
            'type' => $type,
            'text' => $text,
            'target' => $target,
        ]);
    }

    public function getItemsSearch(Request $request)
    {
        $category = $request->input('category', []);
        $text = $request->input('text', '');
        $page = $request->input('page', 1);
        $type = $request->input('type', 'apparel');
        $target = $request->input('target', '');

        if ($text != '') {
            $result = ItemsServices::getDataByMessage($text, $page, $type);

            if (is_array($result)) {
                //encode id and like current
                $result = computedMainData($result, 'items', $type);

                return $result;
            }

            return [];
        }

        $lists = DB::table('items_new');

        //家具
        if ($type == 'furniture') {
            $lists = $lists->whereIn('category', ItemsServices::getFurnitureAllType());
        } else if ($type == 'apparel') {
            $lists = $lists->whereNotIn('category', ItemsServices::getFurnitureAllType());
        } else if ($type == 'plant') {
            $lists = $lists->where('category', '植物');
        }

        if (!empty($category) && is_array($category)) {
            $lists->whereIn('category', $category);
        }

        //check target
        if ($target != '') {
            $getCount = computedCount('items', $type, true);

            switch ($target) {
                case 'like':
                    $lists->whereIn('id', $getCount['likeIds']);
                    break;
                case 'noLike':
                    $lists->whereNotIn('id', $getCount['likeIds']);
                    break;
                case 'track':
                    $lists->whereIn('id', $getCount['trackIds']);
                    break;
                case 'noTrack':
                    $lists->whereNotIn('id', $getCount['trackIds']);
                    break;
            }
        }

        $lists = $lists->select()
            ->paginate(30)
            ->toArray();

        //encode id and like current
        $lists['data'] = computedMainData($lists['data'], 'items', $type);

        return $lists['data'];
    }

    public function getAllType(Request $request)
    {
        $type = $request->input('type', '');

        return ItemsServices::getAllType($type);
    }
}
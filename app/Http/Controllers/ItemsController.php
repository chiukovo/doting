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

        return view('items.list', [
            'type' => $type,
            'text' => $text,
        ]);
    }

    public function getItemsSearch(Request $request)
    {
        $itemsType = $request->input('itemsType', []);
        $buyType = $request->input('buyType', []);
        $detailType = $request->input('detailType', []);
        $text = $request->input('text', '');
        $page = $request->input('page', 1);
        $type = $request->input('type', 'apparel');

        if ($text != '') {
            $result = ItemsServices::getDataByMessage($text, $page, $type);

            if (is_array($result)) {
                return $result;
            }

            return [];
        }

        $lists = DB::table('items');

        //家具
        if ($type == 'furniture') {
            $lists = $lists->whereIn('type', ItemsServices::getFurnitureAllType());
        } else if ($type == 'apparel') {
            $lists = $lists->whereNotIn('type', ItemsServices::getFurnitureAllType());
        }

        if (!empty($itemsType) && is_array($itemsType)) {
            $lists->whereIn('type', $itemsType);
        }

        if (!empty($buyType) && is_array($buyType)) {
            $lists->whereIn('buy_type', $buyType);
        }

        if (!empty($detailType) && is_array($detailType)) {
            $lists->whereIn('detail_type', $detailType);
        }

        $lists = $lists->select()
            ->paginate(30)
            ->toArray();


        return $lists['data'];
    }

    public function getAllType(Request $request)
    {
        $type = $request->input('type', '');

        return ItemsServices::getAllType($type);
    }
}
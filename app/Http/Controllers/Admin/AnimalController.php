<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnimalServices;
use DB;

class AnimalController extends Controller
{

    public function index(Request $request)
    {
        $text = $request->input('text', '');
        $type = $request->route()->getName();
        $type = is_null($type) ? '' : $type;

        return view('admin.animals.index', [
            'type' => $type,
            'text' => $text,
        ]);
    }

    public function getAnimalSearch(Request $request)
    {
        $race = $request->input('race', []);
        $personality = $request->input('personality', []);
        $bd = $request->input('bd', []);
        $text = $request->input('text', '');
        $page = $request->input('page', 1);
        $type = $request->input('type', '');

        $lists = DB::table('animal');

        if ($text != '') {
            $lists = $lists
                ->where('name', 'like', '%' . $text . '%')
                ->orWhere('race', 'like', '%' . $text . '%')
                ->orWhere('en_name', 'like', '%' . $text . '%')
                ->orWhere('jp_name', 'like', '%' . $text . '%')
                ->orWhere('personality', 'like', '%' . $text . '%')
                ->orWhere('say', $text)
                ->orWhere('bd_m', $text)
                ->orWhere('bd', $text);

            if ($type == 'npc') {
                $lists = $lists->where('info', '!=', '');
            }

            $lists = $lists
                ->orderBy('bd', 'asc')
                ->select()
                ->paginate(30)
                ->toArray();

            return $lists;
        }

        if ($type == 'npc') {
            $lists = $lists->where('info', '!=', '');
        }

        if (!empty($race) && is_array($race)) {
            $lists->whereIn('race', $race);
        }

        if (!empty($personality) && is_array($personality)) {
            foreach ($personality as $key => $data) {
                $lists->where('personality', 'like', '%' . $data . '%');

                if ($key != 0) {
                    $lists->orWhere('personality', 'like', '%' . $data . '%');
                }
            }
        }

        if (!empty($bd) && is_array($bd)) {
            $lists->whereIn('bd_m', $bd);
        }

        $lists = $lists->select()
            ->paginate(30)
            ->toArray();

        return $lists;
    }


    public function detail(Request $request)
    {
        $name = $request->input('name');

        if ($name == '') {
            return redirect('animals/list');
        }

        $detail = DB::table('animal')
            ->where('name', $name)
            ->first();

        $detail->kk_cn_name = '';

        //kk ch name
        $kk = DB::table('kk')
            ->where('name', $detail->kk)
            ->first(['cn_name']);

        if (!is_null($kk)) {
            $detail->kk_cn_name = $kk->cn_name;
        }

        if ($detail->kk != '') {
            //format
            $detail->kk = str_replace(".", "", $detail->kk);
            $detail->kk = str_replace(" ", "_", $detail->kk);
            $detail->kk = str_replace("'", "", $detail->kk);
            $detail->kk = $detail->kk . '_Live';
        }


        if (is_null($detail)) {
            return redirect('animals/list');
        }

        //同種族
        $sameRaceArray = DB::table('animal')
            ->where('race', 'like', '%' . $detail->race . '%')
            ->where('id', '!=', $detail->id)
            ->whereNull('info')
            ->get()
            ->toArray();

            //dd($detail);

        return view('admin.animals.detail', [
            'detail' => $detail,
            'sameRaceArray' => $sameRaceArray,
        ]);
    }


    public function edit($id, Request $request)
    {
        $bd = $request->bd_m.'.'.$request->bd_d;

        $request->bd = $bd;
        
        $columns = ['name', 'en_name', 'jp_name', 'sex', 'personality', 'race', 'name', 'bd', 'bd_d', 'bd_m', 'say', 'info', 'target', 'motto', 'kk', 'amiibo', 'level', 'status'];

        $update_data = [];
        foreach ($columns as $key => $val) {
            if($request->$val){
                $update_data[$val] = $request->$val;
            }
        }

        if($request->file('avatar_url')){
            $location = 'avatars/'.$id.'/';
            $file_name = 'avatar.jpg';
            $file = $request->file('avatar_url');
            if(!$file->move($location, $file_name)){
                return redirect('/'.env('ADMIN_PREFIX').'/animals/')->with('error', '頭像上傳失敗');
            }

            $update_data['avatar_url'] = $location.$file_name;
        }

        DB::table('animal')->where('id', $id)->update($update_data);

        return redirect('/'.env('ADMIN_PREFIX').'/animals/detail?name='.$request->name);
    }


    public function add(Request $request)
    {
        if(!$request->file('avatar_url')){
            return redirect('/'.env('ADMIN_PREFIX').'/animals/')->with('error', '請上傳頭像');
        }
        
        $bd = $request->bd_m.'.'.$request->bd_d;

        $request->bd = $bd;

        $columns = ['name', 'en_name', 'jp_name', 'sex', 'personality', 'race', 'name', 'bd', 'bd_d', 'bd_m', 'say', 'info', 'target', 'motto', 'kk', 'amiibo', 'level'];

        $insert_data = [];
        foreach ($columns as $key => $val) {
            if($request->$val){
                $insert_data[$val] = $request->$val;
            }
        }

        try{
            DB::beginTransaction();

            $id = DB::table('animal')->insertGetId($insert_data);

            $location = 'avatars/'.$id.'/';
            $file_name = 'avatar.jpg';
            $file = $request->file('avatar_url');
            if(!$file->move($location, $file_name)){
                return redirect('/'.env('ADMIN_PREFIX').'/animals/')->with('error', '頭像上傳失敗');
            }

            DB::table('animal')->where('id', $id)->update(['avatar_url' => $location.$file_name]);

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return redirect('/'.env('ADMIN_PREFIX').'/animals/')->with('error', 'syntax error');
        }

        return redirect('/'.env('ADMIN_PREFIX').'/animals/')->with('message', '新增成功');
    }

}

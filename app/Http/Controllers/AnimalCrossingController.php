<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Http\Request;
use QL\QueryList;
use Curl, Log, Storage, DB;

class AnimalCrossingController extends Controller
{

    public function __construct()
    {
        $lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient ($lineAccessToken);
        $this->lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);
    }

    public function index(Request $request)
    {
    	echo 'hi';
    }

    public function message(Request $request)
    {
        $lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);

        if ($signature == '') {
        	return;
        }

        if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {
            return;
        }

        try {
            $events = $this->lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                $text = '';
                $userId = $event->getUserId();
                $replyToken = $event->getReplyToken();
                $messageType = $event->getMessageType();

                //訊息的話
                if ($event instanceof MessageEvent) {
                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入
                        //取得須回傳資料
                        $replyText = $this->formatText($text);

                        if ($replyText == '') {
                            return; 
                        } else {
                            if (is_array($replyText)) {
                                $target = $replyText[0];
                                //發圖片
                                //發文
                                $text = $target->name . '%0D%0A';
                                $text .= $target->personality . '%0D%0A';
                                $text .= $target->race . '%0D%0A';
                                $text .= $target->bd . '%0D%0A';
                                $text .= $target->say . '%0D%0A';
                                $this->lineBot->replyText($replyToken, $text);
                            } else {
                                $this->lineBot->replyText($replyToken, $replyText);
                            }
                        }
                    }
                }

                //Log
                $log = [
                    'userId' => $userId,
                    'text' => $text,
                    'type' => $messageType,
                ];
                
                Log::info(json_encode($log));
            }
        } catch (Exception $e) {
            return;
        }
        return;
    }

    public function formatText($text)
    {
        //切割
        $format = explode(" ", $text);

        $type = isset($format[0]) ? $format[0] : '';
        $target = isset($format[1]) ? $format[1] : '';

        switch ($type) {
            case '動物':
                if ($target != '') {
                    return $this->getDbAnimal($target);
                }
                break;
            
            default:
                return '';
                break;
        }
    }

    public function getDbAnimal($target)
    {
        $dbAnimal = DB::table('animal')
            ->where('name', 'like', '%' . $target . '%')
            ->get()
            ->toArray();

        if (empty($dbAnimal)) {
            return '找不到捏...(¬_¬)';
        }

        if (count($dbAnimal) > 1) {
            $resultText = '你要找的是';

            foreach ($dbAnimal as $animal) {
                $resultText .= $animal->name . '%0D%0A';
            }

            $resultText .= '哪個阿 ( ・◇・)？';

            return $resultText;
        }


        //單一
        return $dbAnimal;
    }

    public function getAnimalApi(Request $request)
    {
        dd($this->formatText('動物 小芹'));
        //採集
        $url = 'http://e0game.com/animalcrossing/%e5%8b%95%e7%89%a9%e6%9d%91%e6%b0%91-%e5%9c%96%e9%91%91/';
        $ql = QueryList::get($url);
        $result = $ql->rules([
            'img' => ['.column-1 img', 'src'],
            'name' => ['.column-2', 'text'],
            'sex' => ['.column-3', 'text'],
            'personality' => ['.column-4', 'text'],
            'race' => ['.column-5', 'text'],
            'bd' => ['.column-6', 'text'],
            'say' => ['.column-7', 'text'],

        ])
        ->range('#tablepress-29 tr')
        ->queryData();

        //DB DATA
        $dbAnimal = DB::table('animal')->get()->toArray();

        //save api result
        foreach ($result as $key => $data) {
            //圖片名稱不得為空
            if ($data['img'] != '' && $data['name'] != '') {
                $isset = false;

                //檢查是否資料庫存在
                foreach ($dbAnimal as $source) {
                    if ($source->name == $data['name']) {
                        $isset = true;
                    }
                }

                if (!$isset) {
                    //save img
                    $headers = get_headers($data['img']);
                    $code = substr($headers[0], 9, 3);
                    $imgUploadSuccess = 0;
                    
                    if ($code == 200) {
                        $imgUploadSuccess = 1;
                        $content = file_get_contents($data['img']);
                        Storage::disk('animal')->put($data['name'] . '.png', $content);
                    }
                    
                    $data['img_path'] = '/animal/' . $data['name'] . '.png';

                    //insert
                    DB::table('animal')->insert([
                        'name' => $data['name'],
                        'img_source' => $data['img'],
                        'img_path' => $data['img_path'],
                        'img_upload_success' => $imgUploadSuccess,
                        'personality' => $data['personality'],
                        'race' => $data['race'],
                        'bd' => $data['bd'],
                        'say' => $data['say'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    echo 'insert: ' . $data['name'] . '<br>';
                }
            }
        }

        echo 'done';
    }
}
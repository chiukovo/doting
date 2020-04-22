<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use App\Services\AnimalServices;
use App\Services\DiyServices;
use App\Services\OtherServices;
use Illuminate\Http\Request;
use QL\QueryList;
use Curl, Log, Storage, DB, Url;

class AnimalCrossingController extends Controller
{
    public $userId = '';
    public $groupId = '';
    public $roomId = '';
    public $displayName = '';
    public $dbType = '';
    public $isSend = false;

    public function __construct()
    {
        $this->lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $this->lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->lineAccessToken);
        $this->lineBot = new LINEBot($httpClient, ['channelSecret' => $this->lineChannelSecret]);
    }

    public function index(Request $request)
    {
    	echo 'hi';
    }

    public function message(Request $request)
    {
        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);

        if ($signature == '') {
        	return;
        }

        if (!SignatureValidator::validateSignature($request->getContent(), $this->lineChannelSecret, $signature)) {
            return;
        }

        try {
            $events = $this->lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                $text = '';
                $messageType = '';
                $this->isSend = false;
                $this->userId = $event->getUserId();
                $replyToken = $event->getReplyToken();

                //訊息的話
                if ($event instanceof MessageEvent) {
                    $messageType = $event->getMessageType();

                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入

                        $sendBuilder = $this->getSendBuilder($text);

                        //卡片型態
                        if (is_array($sendBuilder)) {
                            foreach ($sendBuilder as $builder) {
                                $this->doSendMessage($replyToken, $builder);
                            }
                        }

                        //文字型態
                        if ($sendBuilder != '' && !is_array($sendBuilder)) {
                            $this->doSendMessage($replyToken, $sendBuilder);
                        }
                    }
                }

                if ($event instanceof JoinEvent) {
                   $textExample = $this->instructionExample();

                   $message = new TextMessageBuilder($textExample);

                   $this->doSendMessage($replyToken, $message);
                   $this->isSend = true;
                }

                if ($this->isSend) {
                    //Log
                    $log = [
                        'userId' => $this->userId,
                        'text' => $text,
                        'type' => $messageType,
                    ];

                    Log::info(json_encode($log, JSON_UNESCAPED_UNICODE));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return;
        }
        return;
    }

    public function getSendBuilder($text)
    {
        //取得須回傳資料
        $dataArray = $this->formatMessage($text);

        //Diy另外寫
        if ($this->dbType == 'diy') {
            $diyString = DiyServices::getSendData($dataArray);

            //send
            $message = new TextMessageBuilder($diyString);
            $this->isSend = true;

            return $message;
        }

        if ($dataArray == '') {
            return '';
        }

        if (is_array($dataArray)) {
            $returnArray = [];
            $dataArray = array_chunk($dataArray, 10);
            $dataArray = array_chunk($dataArray, 5);

            foreach ($dataArray as $data) {
                $multipleMessageBuilder = new MultiMessageBuilder();

                foreach ($data as $details) {
                    $result = [];

                    foreach ($details as $detail) {
                        switch ($this->dbType) {
                            case 'animal':
                                $result[] = AnimalServices::createItemBubble($detail);
                                break;
                            case 'other':
                                $result[] = OtherServices::createItemBubble($detail);
                                break;
                        }
                    }

                    $target = new CarouselContainerBuilder($result);

                    $msg = FlexMessageBuilder::builder()
                        ->setAltText('豆丁森友會圖鑑 d(`･∀･)b')
                        ->setContents($target);

                    $multipleMessageBuilder->add($msg);
                }

                $returnArray[] = $multipleMessageBuilder;
            }

            return $returnArray;

            $this->isSend = true;
        } else {
            $message = new TextMessageBuilder($dataArray);
            $this->isSend = true;

            return $message;
        }
    }

    public function doSendMessage($replyToken, $messageObj)
    {
        $response = $this->lineBot->replyMessage($replyToken, $messageObj);

        //error
        if (!$response->isSucceeded()) {
            Log::debug($response->getRawBody());
        }
    }

    public function getUserProfile($event)
    {
        //base
        if ($event instanceof BaseEvent) {
            //user
            if ($event->isUserEvent()) {
                $this->userId = $event->getUserId();
                if (!is_null($this->userId)) {
                    $response = $this->lineBot->getProfile($this->userId);

                    if ($response->isSucceeded()) {
                        $profile = $response->getJSONDecodedBody();
                        $this->displayName = $profile['displayName'];
                    } else {
                        Log::debug($response->getRawBody());
                    }
                }
            }

            //group
            if ($event->isGroupEvent()) {
                $this->userId = $event->getUserId();
                $this->groupId = $event->getGroupId();

                if (!is_null($this->userId) && !is_null($this->groupId)) {
                    $response = $this->lineBot->getGroupMemberProfile($this->groupId, $this->userId);

                    if ($response->isSucceeded()) {
                        $profile = $response->getJSONDecodedBody();
                        $this->displayName = $profile['displayName'];
                    } else {
                        Log::debug($response->getRawBody());
                    }
                }
            }


            //room
            if ($event->isRoomEvent()) {
                $this->userId = $event->getUserId();
                $this->roomId = $event->getRoomId();

                if (!is_null($this->userId) && !is_null($this->roomId)) {
                    $response = $this->lineBot->getRoomMemberProfile($this->roomId, $this->userId);

                    if ($response->isSucceeded()) {
                        $profile = $response->getJSONDecodedBody();
                        $this->displayName = $profile['displayName'];
                    } else {
                        Log::debug($response->getRawBody());
                    }
                }
            }
        }
    }

    public function instructionExample()
    {
        $text = '你好 偶是豆丁 ε٩(๑> ₃ <)۶з' . "\n";
        $text .= 'version ' . config('app.version') . "\n";
        $text .= "\n";
        $text .= '👇以下教您如何使用指令👇' . "\n";
        $text .= '1.輸入【豆丁】，重新查詢教學指令' . "\n";
        $text .= "\n";
        $text .= '2.輸入【#茶茶丸】，可查詢島民資訊' . "\n";
        $text .= '→ 同時可以使用外語名稱、個性、種族、生日月份查詢哦！' . "\n";
        $text .= '範例：#茶茶丸、#Dom、#ちゃちゃまる、#運動、#綿羊、#3、#阿戰隊' . "\n";
        $text .= "\n";
        $text .= '3.輸入【$鯊魚】，查詢魚、昆蟲圖鑑' . "\n";
        $text .= '→ 同時可以單獨只查詢南、北、全半球月份魚、昆蟲' . "\n";
        $text .= '範例：$南4月 魚、$北5月 蟲、$全5月 魚' . "\n";
        $text .= "\n";
        $text .= '4.輸入【做釣竿】，查詢DIY方程式配方' . "\n";
        $text .= '範例：做石斧頭、做櫻花' . "\n";
        $text .= "\n";
        $text .= '【#】查詢島民、NPC相關資訊' . "\n";
        $text .= '【$】查詢魚、昆蟲圖鑑' . "\n";
        $text .= '【做】查詢DIY圖鑑' . "\n";
        $text .= "\n";
        $text .= '歡迎提供缺漏或錯誤修正的資訊，以及功能建議。' . "\n";
        $text .= 'https://ppt.cc/fiZIDx';

        return $text;
    }

    public function getFunny($text)
    {
        $returnText = '';

        if ($text == '豆丁笨蛋') {
            return '你才笨蛋 (／‵Д′)／~ ╧╧';
        }

        if ($text == '#豬力力') {
            $returnText = '名稱: 豬力力' . "\n";
            $returnText .= '個性: 好吃懶做' . "\n";
            $returnText .= '種族: 耍廢星人' . "\n";
            $returnText .= '生日: 0112' . "\n";
            $returnText .= '口頭禪: 賊賊給我錢';

            return $returnText;
        }

        if ($text == '#軒哥') {
            $returnText = '名稱: 軒哥' . "\n";
            $returnText .= '個性: 火爆' . "\n";
            $returnText .= '種族: 苦命星人' . "\n";
            $returnText .= '生日: ??' . "\n";
            $returnText .= '口頭禪: 走了拉 夾';

            return $returnText;
        }

        if ($text == '#ㄦㄦ') {
            $returnText = '名稱: ㄦㄦ' . "\n";
            $returnText .= '個性: 溫和' . "\n";
            $returnText .= '種族: 不想上班星人' . "\n";
            $returnText .= '生日: 0119 or 0123' . "\n";
            $returnText .= '口頭禪: 想下班';

            return $returnText;
        }

        if ($text == '#Quni') {
            $returnText = '名稱: Quni' . "\n";
            $returnText .= '個性: 易怒' . "\n";
            $returnText .= '種族: 愛睏星人' . "\n";
            $returnText .= '生日: 0121' . "\n";
            $returnText .= '口頭禪: 想睡覺';

            return $returnText;
        }

        return $returnText;
    }

    public function formatMessage($text)
    {
        //去除前後空白
        $text = preg_replace('/\s+/', '', $text);

        if ($text == '豆丁') {
            return $this->instructionExample();
        }

        //惡搞
        $funny = $this->getFunny($text);

        if ($funny != '') {
            return $funny;
        }

        $type = mb_substr($text, 0, 1);
        $target = mb_substr($text, 1);

        switch ($type) {
            case '#':
                if ($target != '') {
                    $this->dbType = 'animal';

                    return AnimalServices::getDataByMessage($target);
                }
                break;
            case '$':
                if ($target != '') {
                    $this->dbType = 'other';

                    return OtherServices::getDataByMessage($target);
                }
                break;

            case '做':
                if ($target != '') {
                    $this->dbType = 'diy';

                    return DiyServices::getDataByMessage($target);
                }
                break;
            default:
                return '';
                break;
        }
    }
}
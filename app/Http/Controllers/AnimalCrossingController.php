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
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use App\Services\AnimalServices;
use App\Services\DiyServices;
use App\Services\OtherServices;
use App\Services\ItemsServices;
use App\Services\ArtServices;
use App\Services\FossilServices;
use Illuminate\Http\Request;
use QL\QueryList;
use Illuminate\Support\Facades\Redis;
use Curl, Log, Storage, DB, Url;

class AnimalCrossingController extends Controller
{
    public $userId = '';
    public $groupId = '';
    public $roomId = '';
    public $displayName = '';
    public $dbType = '';
    public $realText = '';
    public $isSend = false;
    public $notFound = true;

    public function __construct()
    {
        $this->lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $this->lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->lineAccessToken);
        $this->lineBot = new LINEBot($httpClient, ['channelSecret' => $this->lineChannelSecret]);
    }

    public function index(Request $request)
    {
        /*$target = $this->getSendBuilder('$大藍閃');
        $test = $target[0]->buildMessage()[0]['contents']['contents'][0];

        dd(json_encode($test));*/
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

                        //判斷關鍵字
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
                   $textExample = printDoc();

                   $message = new TextMessageBuilder($textExample);

                   $this->doSendMessage($replyToken, $message);
                }

                if ($this->isSend) {
                    //Log
                    $log = [
                        'userId' => $this->userId,
                        'text' => $text,
                        'type' => $messageType,
                    ];

                    Log::info(json_encode($log, JSON_UNESCAPED_UNICODE));

                    //統計
                    if (!$this->notFound && env('OPEN_REDIS_STATISTICS')) {
                        $this->statisticsMsg($text);
                    }
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
        //去頭尾空白
        $text = trim($text);

        if ($text == '抽') {
            $this->notFound = false;

            return $dataArray;
        }

        if ($dataArray == '') {
            return '';
        }

        //相容性 回圖片
        if ($this->dbType == 'compatible') {
            if ($dataArray['status'] == 'success') {
                $imgUrl = $dataArray['url'];
                $multipleMessageBuilder = new MultiMessageBuilder();

                $imgBuilder = new ImageMessageBuilder($imgUrl, $imgUrl);
                $multipleMessageBuilder->add($imgBuilder);

                $message = new TextMessageBuilder($dataArray['source_url']);
                $multipleMessageBuilder->add($message);

                $this->isSend = true;
                Log::info(json_encode($dataArray, JSON_UNESCAPED_UNICODE));

                return $multipleMessageBuilder;
            } else {
                $dataArray = $dataArray['msg'];
            }
        }

        if (is_array($dataArray)) {
            if ($text == '我的島民' || $text == '我的護照' || $text == '我的大頭菜') {
                $this->notFound = false;

                return $dataArray;
            }

            $more = count($dataArray) > 20 ? true : false;
            $formatData = [];

            if ($more) {
                foreach ($dataArray as $key => $value) {
                    if ($key <= 19) {
                        $formatData[] = $value;
                    }
                }
            } else {
                $formatData = $dataArray;
            }

            $returnArray = [];
            $formatData = array_chunk($formatData, 10);
            $formatData = array_chunk($formatData, 5);

            foreach ($formatData as $key => $data) {
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
                            case 'items':
                                $result[] = ItemsServices::createItemBubble($detail);
                                break;
                            case 'fossil':
                                $result[] = FossilServices::createItemBubble($detail);
                                break;
                            case 'diy':
                                $result[] = DiyServices::createItemBubble($detail);
                                break;
                            case 'art':
                                //img1
                                if ($detail->img1 != '') {
                                    $result[] = ArtServices::createItemBubble($detail, $detail->img1);
                                }

                                //img2
                                if ($detail->img2 != '') {
                                    $result[] = ArtServices::createItemBubble($detail, $detail->img2);
                                }

                                //img1
                                if ($detail->img3 != '') {
                                    $result[] = ArtServices::createItemBubble($detail, $detail->img3);
                                }
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

            foreach ($returnArray as $key => $info) {
                if (count($returnArray) == $key + 1) {
                    if ($more) {
                        $moreString = '';
                        $msg = $this->getMoreText();

                        $message = new TextMessageBuilder($msg);
                        $multipleMessageBuilder->add($message);
                    }
                }
            }

            $this->isSend = true;
            $this->notFound = false;

            return $returnArray;
        } else {
            $message = new TextMessageBuilder($dataArray);

            $this->isSend = true;

            return $message;
        }
    }

    //統計用
    public function statisticsMsg($text)
    {
        $key = 'array';
        $number = Redis::hGet('array', $text);

        if (is_null($number)) {
            //set
            Redis::hSet('array', $text, 1);
        } else {
            //++
            $number = $number + 1;
            Redis::hSet('array', $text, $number);
        }
    }

    public function getMoreText($paramType = null, $paramRealText = null)
    {
        $type = is_null($paramType) ? $this->dbType : $paramType;
        $realText = is_null($paramRealText) ? $this->realText : $paramRealText;

        $text = '👇👇 查看其他搜尋結果 👇👇' . "\n";
        $url = env('APP_URL');

        switch ($type) {
            case 'animal':
                $url .= '/animals/list';
                break;
            case 'other':
                $url .= '/museum/list';
                break;
            case 'items':
                $url .= '/items/all/list';
                break;
            case 'diy':
                $url .= '/diy/list';
                break;
            case 'fossil':
                $url .= '/fossil/list';
                break;
        }

        $text .= $url . '?text=' . urlencode($realText);

        if (!is_null($paramType) && !is_null($paramRealText)) {
            return $url . '?text=' . urlencode($realText);
        }

        return $text;
    }

    public function doSendMessage($replyToken, $messageObj)
    {
        $response = $this->lineBot->replyMessage($replyToken, $messageObj);
        $this->isSend = true;

        //error
        if (!$response->isSucceeded()) {
            Log::debug($response->getRawBody());
            Log::debug($this->realText);
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

    public function getFunny($text)
    {
        $returnText = '';

        if ($text == '哇耶') {
            return '哇耶 { @❛ꈊ❛@ }';
        }

        if ($text == '歐文') {
            return '帥哥';
        }

        if ($text == '我的居民') {
            return '你是不是要查 【我的島民】 哇耶 ಠ_ಠ?';
        }

        if ($text == '找女朋友' || $text == '找男朋友' || $text == '找老婆' || $text == '找老公') {
            return '找不到捏 不要灰心總有一天會找到的 123 加油加油加油 哇耶';
        }

        if ($text == '找妹妹') {
            return '你....沒有....妹妹 哇耶';
        }

        if ($text == '豆丁笨蛋') {
            return '你才笨蛋 哇耶 (／‵Д′)／~ ╧╧';
        }

        if ($text == '搜尋排行榜') {
            $returnText = '豆丁搜尋排行榜 哇耶 (´・ω・`)' . "\n";
            $returnText .= 'https://doting.tw/statistics';

            return $returnText;
        }

        if ($text == '豆丁交友區') {
            $returnText = '豆丁交友區 哇耶 (´・ω・`)' . "\n";
            $returnText .= 'https://doting.tw/friend/list';

            return $returnText;
        }

        if ($text == '動物相容性分析') {
            $returnText = '動物相容性分析 哇耶 (´・ω・`)' . "\n";
            $returnText .= 'https://doting.tw/animals/compatible';

            return $returnText;
        }

        //抽卡
        if ($text == '抽') {
            return AnimalServices::getRandomCard();
        }

        return $returnText;
    }

    public function formatMessage($text)
    {
        //去頭尾空白
        $text = trim($text);
        $source = $text;

        //去除前後空白
        $text = preg_replace('/\s+/', '', $text);

        if ($text == '豆丁') {
            return printDoc();
        }

        //我的島民
        if ($text == '我的島民') {
            $this->dbType = 'animal';
            $this->realText = $text;

            return AnimalServices::myAnimals($this->userId);
        }

        //我的護照
        if ($text == '我的護照') {
            return AnimalServices::myPassport($this->userId);
        }

        //我的大頭菜
        if ($text == '我的大頭菜') {
            return AnimalServices::myCai($this->userId);
        }

        //惡搞
        $funny = $this->getFunny($text);

        if ($funny != '') {
            return $funny;
        }

        $type = mb_substr($text, 0, 1);
        $target = mb_substr($text, 1);

        //化石
        $checkTwoWord = mb_substr($text, 0, 2);

        if ($checkTwoWord == '化石') {
            $target = str_replace("化石", "", $text);
            $target = trim($target);

            $this->dbType = 'fossil';
            $this->realText = $target;

            return FossilServices::getDataByMessage($target);
        }

        //相容性判斷
        if ($checkTwoWord == '##') {
            return '##已移除 改成只需一個# 哇耶';
        }

        if ($type == '#') {
            $source = str_replace("#", "", $source);
            $source = trim($source);
            $explode = explode(" ", $source);

            if (count($explode) >= 2) {
                if ($explode[0] != '' && $explode[1] != '') {
                    $this->dbType = 'compatible';
                    $this->realText = $source;

                    return AnimalServices::compatiblePrint($source);
                }
            }
        }

        switch ($type) {
            case '#':
                if ($target != '') {
                    $this->dbType = 'animal';
                    $this->realText = $target;

                    return AnimalServices::getDataByMessage($target);
                }
                break;
            case '$':
                if ($target != '') {
                    $this->dbType = 'other';
                    $this->realText = $target;

                    return OtherServices::getDataByMessage($target);
                }
                break;

            case '做':
                if ($target != '') {
                    $this->dbType = 'diy';
                    $this->realText = $target;

                    return DiyServices::getDataByMessage($target);
                }
                break;

            case '找':
                if ($target != '') {
                    $this->dbType = 'items';
                    $this->realText = $target;

                    return ItemsServices::getDataByMessage($target);
                }
                break;

            case '查':
                if ($target != '') {
                    $this->dbType = 'art';
                    $this->realText = $target;

                    return ArtServices::getDataByMessage($target);
                }
                break;
            default:
                return '';
                break;
        }
    }

    public function typeToUrl($type)
    {
        switch ($type) {
            case '#':
                return 'animal';
                break;
            case '$':
                return 'other';
                break;

            case '做':
                return 'diy';
                break;

            case '找':
                return 'items';
                break;

            case '查':
                return 'art';
                break;
            default:
                return '';
                break;
        }
    }
}
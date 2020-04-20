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
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use Illuminate\Http\Request;
use QL\QueryList;
use Curl, Log, Storage, DB, Url;

class AnimalCrossingController extends Controller
{
    public function __construct()
    {
        $lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient ($lineAccessToken);
        $this->lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

        $this->userId = '';
        $this->groupId = '';
        $this->roomId = '';
        $this->displayName = '';
        $this->dbType = '';
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
                $messageType = '';
                $isSend = false;
                $this->userId = $event->getUserId();
                $replyToken = $event->getReplyToken();

                $this->getUserProfile($event);

                //訊息的話
                if ($event instanceof MessageEvent) {
                    $messageType = $event->getMessageType();

                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入
                        //取得須回傳資料
                        $replyText = $this->formatText($text);

                        //測試
                        if ($text == '#testfav') {
                            $multipleMessageBuilder = new MultiMessageBuilder();

                            $result = [];

                            $animals = DB::table('animal')
                                ->take(5)
                                ->get()
                                ->toArray();

                            foreach ($animals as $animal) {
                                $result[] = $this->createTestItemBubble($animal);
                            }

                            $target = new CarouselContainerBuilder($result);

                            $msg = FlexMessageBuilder::builder()
                                ->setAltText('豆丁森友會圖鑑 d(`･∀･)b')
                                ->setContents($target);

                            $multipleMessageBuilder->add($msg);

                            //send
                            $response = $this->lineBot->replyMessage($replyToken, $multipleMessageBuilder);

                            //error
                            if (!$response->isSucceeded()) {
                                Log::debug($response->getRawBody());
                            }
                        }
                        //end

                        if ($replyText == '') {
                            return;
                        } else {
                            if (is_array($replyText)) {
                                $replyText = array_chunk($replyText, 10);
                                $replyText = array_chunk($replyText, 5);

                                foreach ($replyText as $detail) {
                                    $multipleMessageBuilder = new MultiMessageBuilder();

                                    foreach ($detail as $animals) {
                                        $result = [];

                                        foreach ($animals as $animal) {
                                            $result[] = $this->createItemBubble($animal);
                                        }

                                        $target = new CarouselContainerBuilder($result);

                                        $msg = FlexMessageBuilder::builder()
                                            ->setAltText('豆丁森友會圖鑑 d(`･∀･)b')
                                            ->setContents($target);


                                        $multipleMessageBuilder->add($msg);
                                    }

                                    //send
                                    $response = $this->lineBot->replyMessage($replyToken, $multipleMessageBuilder);

                                    //error
                                    if (!$response->isSucceeded()) {
                                        Log::debug($response->getRawBody());
                                    }
                                }

                                $isSend = true;
                            } else {
                                $message = new TextMessageBuilder($replyText);
                                $this->lineBot->replyMessage($replyToken, $message);
                                $isSend = true;
                            }
                        }
                    }
                }

                if ($event instanceof JoinEvent) {
                   $textExample = $this->instructionExample();
                   $message = new TextMessageBuilder($textExample);
                   $this->lineBot->replyMessage($replyToken, $message);
                   $isSend = true;
                }

                if ($event instanceof PostbackEvent) {
                   $postbackData = $event->getPostbackData();
                   $params = $event->getPostbackParams();

                   $this->doFavorite($postbackData, $replyToken);
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return;
        }
        return;
    }

    public function doFavorite($postbackData, $replyToken)
    {
        parse_str($postbackData, $targetArray);

        $action = $targetArray['action'] ?? '';
        $pbUserId = $targetArray['user_id'] ?? '';
        $pbDisplayName = $targetArray['display_name'] ?? '';
        $tableId = $targetArray['table_id'] ?? '';

        if ($action != '' && $pbUserId != '' && $tableId != '') {
            if ($action == 'add') {
                $favorite = DB::table('favorite')
                    ->where('user_id', $pbUserId)
                    ->where('table_id', $tableId)
                    ->get('id')
                    ->toArray();

                if (empty($favorite)) {
                    DB::table('favorite')->insert([
                        'user_id' => $pbUserId,
                        'table_id' => $tableId,
                        'display_name' => $pbDisplayName,
                        'table_name' => 'animal',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else if ($action == 'remove') {
                //remove
                DB::table('favorite')
                    ->where('user_id', $pbUserId)
                    ->where('table_id', $tableId)
                    ->where('table_name', 'animal')
                    ->delete();
            }
        }
    }

    public function getUserProfile($event)
    {
        //base
        if ($event instanceof BaseEvent) {
            //user
            if ($event->isUserEvent()) {
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
        $text = $this->displayName . ' 你好 偶是豆丁 ε٩(๑> ₃ <)۶з' . "\n";
        $text .= '版本 v' . config('app.version') . "\n";
        $text .= '以下教你如何使用指令~~' . "\n";
        $text .= '找指令: 請輸入 "豆丁"' . "\n";
        $text .= '找動物: 請輸入 "#茶茶丸" 也可以使用 個性 種族 生日查詢(月份)' . "\n";
        $text .= '英文查詢: 請輸入 "#joey"' . "\n";
        $text .= '日文查詢: 請輸入 "#チョコ"' . "\n";
        $text .= '動物戰隊: 請輸入 "#阿戰隊"' . "\n";

        return $text;
    }

    public function formatText($text)
    {
        if ($text == '豆丁') {
            return $this->instructionExample();
        }

        if ($text == '豆丁笨蛋') {
            return '你才笨蛋 (／‵Д′)／~ ╧╧';
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
            $returnText .= '生日: ??' . "\n";
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

        $type = substr($text, 0, 1);
        $target = substr($text, 1);

        switch ($type) {
            case '#':
                if ($target != '') {
                    $this->dbType = 'animal';

                    return $this->getDbAnimal($target);
                }
                break;
            case '$':
                if ($target != '') {
                    $this->dbType = 'fish';

                    return $this->getDbFish($target);
                }
                break;
            default:
                return '';
                break;
        }
    }

    public function getDbFish($target)
    {
        $notFound = '找不到捏...(¬_¬)';

        $fish = DB::table('fish')
            ->where('name', 'like', '%' . $target . '%')
            ->orderBy('sell', 'desc')
            ->get()
            ->toArray();

        if (empty($fish)) {
            return $notFound;
        }

        return $fish;
    }

    public function getDbAnimal($target)
    {
        $target = strtolower($target);
        $notFound = '找不到捏...(¬_¬)';

        //設定最愛

        //阿戰隊
        if ($target == '阿戰隊') {
            $name = ['阿一', '阿二', '阿三', '阿四'];
            $dbAnimal = DB::table('animal')
                ->whereIn('name', $name)
                ->orderBy('jp_name', 'asc')
                ->get()
                ->toArray();

            return $dbAnimal;
        }

        $dbAnimal = DB::table('animal')
            ->where('name', 'like', '%' . $target . '%')
            ->orWhere('race', 'like', '%' . $target . '%')
            ->orWhere('en_name', 'like', '%' . $target . '%')
            ->orWhere('jp_name', 'like', '%' . $target . '%')
            ->orWhere('personality', 'like', '%' . $target . '%')
            ->orWhere('bd_m', $target)
            ->orWhere('bd', $target)
            ->orderBy('bd', 'asc')
            ->get()
            ->toArray();

        if (empty($dbAnimal)) {
            return $notFound;
        }

        return $dbAnimal;
    }

    public function createItemBubble($item)
    {
        $target = BubbleContainerBuilder::builder()
            ->setHero($this->createItemHeroBlock($item));

        if ($this->dbType == 'animal') {
            return $target->setBody($this->createAnimalItemBodyBlock($item));
        } else if ($this->dbType == 'fish') {
            return $target->setBody($this->createFishItemBodyBlock($item));
        }
    }

    public function createTestItemBubble($item)
    {
        return BubbleContainerBuilder::builder()
            ->setHero($this->createItemHeroBlock($item))
            ->setBody($this->createAnimalItemBodyBlock($item))
            ->setFooter($this->createItemFooterBlock($item));
    }

    public function createItemFooterBlock($item)
    {
        $color = '#aaaaaa';
        $add = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setColor($color)
            ->setAction(
                new PostbackTemplateActionBuilder(
                    '加入最愛',
                    'action=add&table_id=' . $item->id . '&user_id=' . $this->userId . '&dispay_name=' . $this->displayName,
                    $item->name . '加入最愛'
                )
            );

        $remove = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setAction(
                new PostbackTemplateActionBuilder(
                    '移除最愛',
                    'action=remove&table_id=' . $item->id . '&user_id=' . $this->userId . '&dispay_name=' . $this->displayName,
                    $item->name . '移除最愛'
                )
            );
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::HORIZONTAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$add, $remove]);
    }

    public function createItemHeroBlock($item)
    {
        $imgPath = 'https://' . request()->getHttpHost() . '/' . $this->dbType . '/' . urlencode($item->name) . '.png';

        return ImageComponentBuilder::builder()
            ->setUrl($imgPath)
            ->setSize(ComponentImageSize::XXL)
            ->setAspectRatio('9:12')
            ->setAspectMode(ComponentImageAspectMode::FIT);
    }

    public function createAnimalItemBodyBlock($item)
    {
        $components = [];
        $components[] = TextComponentBuilder::builder()
            ->setText($item->name . ' ' . ucfirst($item->en_name) . ' ' . $item->jp_name)
            ->setWrap(true)
            ->setAlign('center')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        $components[] = TextComponentBuilder::builder()
            ->setText('性別: ' . $item->sex)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('個性: ' . $item->personality)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('種族: ' . $item->race)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('生日: ' . $item->bd)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('口頭禪: ' . $item->say)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#f1f1f1')
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($components);
    }

    public function createFishItemBodyBlock($item)
    {
        $components = [];
        $components[] = TextComponentBuilder::builder()
            ->setText($item->name . ' $' . $item->sell)
            ->setWrap(true)
            ->setAlign('center')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        $components[] = TextComponentBuilder::builder()
            ->setText('影子: ' . $item->shadow)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('位置: ' . $item->position)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('時間: ' . $item->time)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $south = '';
        $north = '';

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#f1f1f1')
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($components);
    }
}
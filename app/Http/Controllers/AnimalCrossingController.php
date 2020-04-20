<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\JoinEvent;
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
                $displayName = '';
                $isSend = false;
                $userId = $event->getUserId();
                $replyToken = $event->getReplyToken();

                //get profile
                if (!is_null($userId)) {
                    $response = $this->lineBot->getProfile($userId);

                    if ($response->isSucceeded()) {
                        $profile = $response->getJSONDecodedBody();
                        $displayName = $profile['displayName'];
                    }
                }

                //訊息的話
                if ($event instanceof MessageEvent) {
                    $messageType = $event->getMessageType();
                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入

                        //測試
                        if ($text == '#testfav') {
                            $multipleMessageBuilder = new MultiMessageBuilder();

                            $result = [];

                            $animals = DB::table('animal')
                                ->take(5)
                                ->get()
                                ->toArray();

                            foreach ($animals as $animal) {
                                $result[] = self::createTestItemBubble($animal);
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

                        //取得須回傳資料
                        $replyText = $this->formatText($text, $userId, $displayName);

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
                                            $result[] = self::createItemBubble($animal);
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
                   $textExample = $this->instructionExample($displayName);
                   $message = new TextMessageBuilder($textExample);
                   $this->lineBot->replyMessage($replyToken, $message);
                   $isSend = true;
                }

                if ($event instanceof PostbackEvent) {
                   $data = $event->getPostbackData();
                   $params = $event->getPostbackParams();

                   //Log
                   $log = [
                       'userId' => $userId,
                       'data' => $data,
                       'params' => $params,
                   ];

                   Log::info(json_encode($log, JSON_UNESCAPED_UNICODE));
                }

                if ($isSend) {
                    //Log
                    $log = [
                        'userId' => $userId,
                        'text' => $text,
                        'type' => $messageType,
                    ];

                    Log::info(json_encode($log, JSON_UNESCAPED_UNICODE));
                }
            }
        } catch (Exception $e) {
            return;
        }
        return;
    }

    public function instructionExample($displayName)
    {
        $text = $displayName . ' 你好 偶是豆丁 ε٩(๑> ₃ <)۶з' . "\n";
        $text .= '版本 v' . config('app.version') . "\n";
        $text .= '以下教你如何使用指令~~' . "\n";
        $text .= '找指令: 請輸入 "豆丁"' . "\n";
        $text .= '找動物: 請輸入 "#茶茶丸" 也可以使用 個性 種族 生日查詢(月份)' . "\n";
        $text .= '英文查詢: 請輸入 "#joey"' . "\n";
        $text .= '日文查詢: 請輸入 "#チョコ"' . "\n";
        $text .= '動物戰隊: 請輸入 "#阿戰隊"' . "\n";

        return $text;
    }

    public function formatText($text, $userId, $displayName)
    {
        if ($text == '豆丁') {
            return $this->instructionExample($displayName);
        }

        if ($text == '540') {
            return '487';
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
                    return $this->getDbAnimal($target, $userId, $displayName);
                }
                break;

            default:
                return '';
                break;
        }
    }

    public function getDbAnimal($target, $userId, $displayName)
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

    public function getNewImgName()
    {
        //DB DATA
        $dbAnimal = DB::table('animal')->where('beautify_img', 0)->get()->toArray();

        foreach ($dbAnimal as $data) {
            $simplified = Curl::to('http://api.zhconvert.org/convert?converter=Simplified&text=' . $data->name)->asJson()->get();
            $target = $simplified->data->text;

            $url = 'https://wiki.biligame.com/dongsen/' . $target;
            $ql = QueryList::get($url);
            $result = $ql->rules([
                'img' => ['.box-poke-right img', 'src'],
                'other_name' => ['.box-poke-left .box-poke .box-font:eq(5)', 'text'],
            ])
            ->range('.box-poke-big')
            ->queryData();

            if (!empty($result)) {
                $img = $result[0]['img'];
                $otherName = $result[0]['other_name'];

                //save img
                $headers = get_headers($img);
                $code = substr($headers[0], 9, 3);
                $imgUploadSuccess = 0;

                if ($code == 200) {
                    $imgUploadSuccess = 1;
                    $content = file_get_contents($img);
                    Storage::disk('animal')->put($data->name . '.png', $content);
                    $enName = '';
                    $jpName = '';

                    //name
                    if ($otherName != '') {
                        $otherName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($otherName));
                        //英文
                        $nameEx = explode('(英)', $otherName);
                        $enName = $nameEx[0] ?? '';


                        //日文
                        if (isset($nameEx[1])) {
                            $jpName = str_replace('(日)', '', $nameEx[1]);
                        }
                    }

                    DB::table('animal')
                        ->where('id', $data->id)
                        ->update([
                            'beautify_img' => 1,
                            'en_name' => strtolower($enName),
                            'jp_name' => $jpName,
                        ]);

                    echo 'update ' . $data->name . '</br>';
                }
            }
        }

        echo 'done';
    }

    public function getAnimalApi(Request $request)
    {
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
                $dbData = [];
                $isset = false;

                //檢查是否資料庫存在
                foreach ($dbAnimal as $source) {
                    if ($source->name == $data['name']) {
                        $isset = true;
                        $dbData = $source;
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
                    $bd = explode('.', $data['bd']);
                    $sex = $data['sex'];

                    //insert
                    DB::table('animal')->insert([
                        'name' => $data['name'],
                        'sex' => $sex,
                        'bd_m' => $bd[0],
                        'bd_d' => $bd[1],
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

    public function createItemBubble($animal)
    {
        return BubbleContainerBuilder::builder()
            ->setHero(self::createItemHeroBlock($animal))
            ->setBody(self::createItemBodyBlock($animal));
    }

    public function createTestItemBubble($animal)
    {
        return BubbleContainerBuilder::builder()
            ->setHero(self::createItemHeroBlock($animal))
            ->setBody(self::createItemBodyBlock($animal))
            ->setFooter(self::createItemFooterBlock($animal));
    }

    private static function createItemFooterBlock($item)
    {
        $color = '#aaaaaa';
        $button = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setColor($color)
            ->setAction(
                new PostbackTemplateActionBuilder(
                    '加入最愛',
                    "action=buy&itemid=111",
                    'test'
                )
            );

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$button]);
    }

    private static function createItemHeroBlock($item)
    {
        $imgPath = 'https://' . request()->getHttpHost() . '/animal/' . urlencode($item->name) . '.png';

        return ImageComponentBuilder::builder()
            ->setUrl($imgPath)
            ->setSize(ComponentImageSize::XXL)
            ->setAspectRatio('9:12')
            ->setAspectMode(ComponentImageAspectMode::FIT);
    }

    private static function createItemBodyBlock($item)
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
            ->setSize(ComponentFontSize::SM)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('個性: ' . $item->personality)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::SM)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('種族: ' . $item->race)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::SM)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('生日: ' . $item->bd)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::SM)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('口頭禪: ' . $item->say)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::SM)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#f1f1f1')
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($components);
    }
}
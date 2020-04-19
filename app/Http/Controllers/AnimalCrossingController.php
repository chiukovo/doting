<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
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
                $isSend = false;
                $userId = $event->getUserId();
                $replyToken = $event->getReplyToken();

                //訊息的話
                if ($event instanceof MessageEvent) {
                    $messageType = $event->getMessageType();
                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入

                        //測試用
                        if ($text = '#testasdf') {
                            $imgBuilder1 = new ImageMessageBuilder('https://doting.moneyroll.com.tw/animal/%E4%B8%83%E4%B8%83.png', 'https://doting.moneyroll.com.tw/animal/%E4%B8%83%E4%B8%83.png');
                            $imgBuilder2 = new ImageMessageBuilder('https://doting.moneyroll.com.tw/animal/%E8%8C%B6%E8%8C%B6%E4%B8%B8.png', 'https://doting.moneyroll.com.tw/animal/%E8%8C%B6%E8%8C%B6%E4%B8%B8.png');

                            $carouselTemplateBuilder = new CarouselTemplateBuilder([
                                new CarouselColumnTemplateBuilder('foo', 'bar', $imgBuilder1, []),
                                new CarouselColumnTemplateBuilder('buz', 'qux', $imgBuilder2, []),
                            ]);

                            $templateMessage = new TemplateMessageBuilder('Button alt text', $carouselTemplateBuilder);
                            $this->lineBot->replyMessage($replyToken, $templateMessage);
                        }
                        //結束

                        //取得須回傳資料
                        $replyText = $this->formatText($text);

                        if ($replyText == '') {
                            return;
                        } else {
                            if (is_array($replyText)) {
                                $target = $replyText[0];

                                //發文字
                                $returnText = '名稱: ' . $target->name . "\n";
                                $returnText .= '名稱(英): ' . ucfirst($target->en_name) . "\n";
                                $returnText .= '名稱(日): ' . $target->jp_name . "\n";
                                $returnText .= '個性: ' . $target->personality . "\n";
                                $returnText .= '種族: ' . $target->race . "\n";
                                $returnText .= '生日: ' . $target->bd . "\n";
                                $returnText .= '口頭禪: ' . $target->say;

                                //發圖片
                                $imgPath = 'https://' . request()->getHttpHost() . '/animal/' . urlencode($target->name) . '.png';
                                $imgBuilder = new ImageMessageBuilder($imgPath, $imgPath);

                                $message = new TextMessageBuilder($returnText);

                                $multipleMessageBuilder = new MultiMessageBuilder();
                                $multipleMessageBuilder
                                    ->add($imgBuilder)
                                    ->add($message);

                                $this->lineBot->replyMessage($replyToken, $multipleMessageBuilder);
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

    public function testFlex()
    {
                $target = array (
          'type' => 'carousel',
          'contents' => 
          array (
            0 => 
            array (
              'type' => 'bubble',
              'size' => 'micro',
              'hero' => 
              array (
                'type' => 'image',
                'url' => 'https://scdn.line-apps.com/n/channel_devcenter/img/flexsnapshot/clip/clip10.jpg',
                'size' => 'full',
                'aspectMode' => 'cover',
                'aspectRatio' => '320:213',
              ),
              'body' => 
              array (
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => 
                array (
                  0 => 
                  array (
                    'type' => 'text',
                    'text' => 'Brown Cafe',
                    'weight' => 'bold',
                    'size' => 'sm',
                    'wrap' => true,
                  ),
                  1 => 
                  array (
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'box',
                        'layout' => 'baseline',
                        'spacing' => 'sm',
                        'contents' => 
                        array (
                          0 => 
                          array (
                            'type' => 'text',
                            'text' => '東京旅行',
                            'wrap' => true,
                            'color' => '#8c8c8c',
                            'size' => 'xs',
                            'flex' => 5,
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
                'spacing' => 'sm',
                'paddingAll' => '13px',
              ),
            ),
            1 => 
            array (
              'type' => 'bubble',
              'size' => 'micro',
              'hero' => 
              array (
                'type' => 'image',
                'url' => 'https://scdn.line-apps.com/n/channel_devcenter/img/flexsnapshot/clip/clip11.jpg',
                'size' => 'full',
                'aspectMode' => 'cover',
                'aspectRatio' => '320:213',
              ),
              'body' => 
              array (
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => 
                array (
                  0 => 
                  array (
                    'type' => 'text',
                    'text' => 'Brow&Cony\'s Restaurant',
                    'weight' => 'bold',
                    'size' => 'sm',
                    'wrap' => true,
                  ),
                  1 => 
                  array (
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'box',
                        'layout' => 'baseline',
                        'spacing' => 'sm',
                        'contents' => 
                        array (
                          0 => 
                          array (
                            'type' => 'text',
                            'text' => '東京旅行',
                            'wrap' => true,
                            'color' => '#8c8c8c',
                            'size' => 'xs',
                            'flex' => 5,
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
                'spacing' => 'sm',
                'paddingAll' => '13px',
              ),
            ),
            2 => 
            array (
              'type' => 'bubble',
              'size' => 'micro',
              'hero' => 
              array (
                'type' => 'image',
                'url' => 'https://scdn.line-apps.com/n/channel_devcenter/img/flexsnapshot/clip/clip12.jpg',
                'size' => 'full',
                'aspectMode' => 'cover',
                'aspectRatio' => '320:213',
              ),
              'body' => 
              array (
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => 
                array (
                  0 => 
                  array (
                    'type' => 'text',
                    'text' => 'Tata',
                    'weight' => 'bold',
                    'size' => 'sm',
                  ),
                  1 => 
                  array (
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'box',
                        'layout' => 'baseline',
                        'spacing' => 'sm',
                        'contents' => 
                        array (
                          0 => 
                          array (
                            'type' => 'text',
                            'text' => '東京旅行',
                            'wrap' => true,
                            'color' => '#8c8c8c',
                            'size' => 'xs',
                            'flex' => 5,
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
                'spacing' => 'sm',
                'paddingAll' => '13px',
              ),
            ),
          ),
        );

        return $target;
    }

    public function instructionExample()
    {
        $text = '你好 偶是豆丁 ε٩(๑> ₃ <)۶з' . "\n";
        $text .= '以下教你如何使用指令~~' . "\n";
        $text .= '找指令: 請輸入 "豆丁"' . "\n";
        $text .= '找動物: 請輸入 "#茶茶丸"' . "\n";

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
        $target = strtolower($target);
        $notFound = '找不到捏...(¬_¬)';

        $dbAnimal = DB::table('animal')
            ->where('name', 'like', '%' . $target . '%')
            ->orWhere('race', 'like', '%' . $target . '%')
            ->orWhere('en_name', 'like', '%' . $target . '%')
            ->orWhere('jp_name', 'like', '%' . $target . '%')
            ->orWhere('personality', 'like', '%' . $target . '%')
            ->get()
            ->toArray();

        if (empty($dbAnimal)) {
            return $notFound;
        }

        if (count($dbAnimal) > 1) {

            foreach ($dbAnimal as $animal) {
                if ($animal->name == $target) {
                    return [$animal];
                }
            }


            $resultText = '你要找的是' . "\n";

            foreach ($dbAnimal as $animal) {
                $resultText .= '#' . $animal->name . "\n";
            }

            $resultText .= '哪個阿 ( ・◇・)？';

            return $resultText;
        }


        //單一
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
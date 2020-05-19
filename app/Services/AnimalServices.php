<?php

namespace App\Services;

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
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use Spatie\Browsershot\Browsershot;
use Log;
use QL\QueryList;
use Curl, DB, File;

class AnimalServices
{
    public static function getConstellation()
    {
        $urls = [
            'https://www.xzw.com/fortune/',
            'https://www.xzw.com/fortune/',
        ];

        $starArray = [
            'width:16px;' => '★',
            'width:32px;' => '★★',
            'width:48px;' => '★★★',
            'width:64px;' => '★★★★',
            'width:80px;' => '★★★★★',
        ];

        foreach ($urls as $key => $url) {
            $date = $key == 0 ? date('Y-m-d') : date('Y-m-d', strtotime(date('Y-m-d') . "+1 days"));

            foreach (getRealConstellation() as $name => $detail) {
                $url1 = $url . $detail[0];
                $url2 = $url . $detail[0] . '/1.html';

                //insert
                $check = DB::table('constellation')
                    ->where('date', $date)
                    ->where('name', $name)
                    ->first();

                if (!is_null($check)) {
                    continue;
                }

                $baseUrl = $key == 0 ? $url1 : $url2;
                $ql = QueryList::get($baseUrl);

                $result = $ql->rules([
                    'star1' => ['li:eq(0) em', 'style'],
                    'star2' => ['li:eq(1) em', 'style'],
                    'star3' => ['li:eq(2) em', 'style'],
                    'star4' => ['li:eq(3) em', 'style'],
                    'field1' => ['li:eq(4)', 'text'],
                    'field2' => ['li:eq(5)', 'text'],
                    'field3' => ['li:eq(6)', 'text'],
                    'field4' => ['li:eq(7)', 'text'],
                    'field5' => ['li:eq(8)', 'text'],
                    'field6' => ['li:eq(9)', 'text'],
                ])
                ->range('#view dl ul')
                ->queryData();

                foreach ($result as $insertData) {
                    $insertData = json_encode($insertData, JSON_UNESCAPED_UNICODE);

                    if (!$insertData) {
                        continue;
                    }

                    //get
                    $target = Curl::to('http://api.zhconvert.org/convert?converter=Traditional&text=' . $insertData)->asJson()->get();
                    $target = $target->data->text;
                    $format = json_decode($target, true);
                    $ranges = range(1, 4);

                    foreach ($ranges as $range) {
                        $format['star' . $range] = trim($format['star' . $range]);

                        foreach ($starArray as $w => $t) {
                            if ($w == $format['star' . $range]) {
                                $format['star' . $range] = $t;
                            }
                        }
                    }

                    $insertFormat = json_encode($format, JSON_UNESCAPED_UNICODE);

                    if (!$insertFormat) {
                        continue;
                    }

                    //insert
                    DB::table('constellation')->insert([
                        'date' => $date,
                        'name' => $name,
                        'result' => $insertFormat,
                    ]);
                }
            }
        }
    }

    public static function getRandomCard()
    {
        $date = date('Y-m-d');
        $item = DB::table('animal')
            ->inRandomOrder()
            ->first();

        $multipleMessageBuilder = new MultiMessageBuilder();

        $result[] = self::createItemBubble($item, true);

        $target = new CarouselContainerBuilder($result);
        $msg = FlexMessageBuilder::builder()
            ->setAltText('豆丁森友會圖鑑 d(`･∀･)b')
            ->setContents($target);
        $multipleMessageBuilder->add($msg);

        //星座廢話
        if ($item->constellation != '') {
            $constellation = $item->constellation;
            $constellationData = DB::table('constellation')
                ->where([
                    'date' => $date,
                    'name' => $constellation,
                ])
                ->first();

            if (is_null($constellationData)) {
                $constellationData = DB::table('constellation')
                    ->where([
                        'name' => $constellation,
                    ])
                    ->first();
            }

            if (!is_null($constellationData)) {
                $result = json_decode($constellationData->result);
                $str = '豆丁老師分析 <(ˉ^ˉ)>' . "\n";
                $str .= "\n";
                $str .= '恭喜你抽到 【' . $item->name . '】' . "\n";
                $str .= '綜合運勢: ' . $result->star1 . "\n";
                $str .= '愛情運勢: ' . $result->star2 . "\n";
                $str .= '事業運勢: ' . $result->star3 . "\n";
                $str .= '財富運勢: ' . $result->star4 . "\n";
                $str .= $result->field1 . "\n";
                $str .= $result->field2 . "\n";
                $str .= $result->field3 . "\n";
                $str .= $result->field4 . "\n";
                $str .= $result->field5 . "\n";
                $str .= "\n";
                $str .= $result->field6 . "\n";

                $message = new TextMessageBuilder($str);
                $multipleMessageBuilder->add($message);
            }
        }

        return [$multipleMessageBuilder];
    }

    public static function compatiblePrint($target)
    {
        //判斷是否需要截圖
        $explode = explode(" ", $target);

        if (count($explode) < 2 && count($explode) > 20) {
            return [
                'status' => 'error',
                'msg' => '格式錯誤 無法產生圖片 哇耶'
            ];
        }

        //2次檢查
        $animalsName = $target;
        //去頭尾空白
        $animalsName = trim($animalsName);
        $names = $animalsName;
        $array = explode(" ", $animalsName);

        //get all animal
        $lists = DB::table('animal')
            ->whereIn('name', $array)
            ->whereNull('info')
            ->get(['id'])
            ->toArray();

        if (count($lists) < 2 || count($lists) > 20) {
            return [
                'status' => 'error',
                'msg' => '動物只可選：2~20人 或找不到動物 (看是不是打錯字) 哇耶'
            ];
        }

        $target = implode(",", $array);
        $newsUrl = 'https://doting.tw/animals/compatible/print?name=' . $target;
        $image = md5($target . env('APP_KEY'));
        $date = date('Y-m-d');

        $path = storage_path('print/' . $date) . '/';

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $fullFilePath = $path . $image . '.jpg';

        try {
            Browsershot::url($newsUrl)
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36')
                ->touch()
                ->fullPage()
                ->noSandbox()
                ->setDelay(100)
                ->save($fullFilePath);
        } catch (Exception $e) {
            Log::error($e);
        }

        $sourceUrl = '詳情 ୧| ⁰ ᴥ ⁰ |୨' . "\n";
        $sourceUrl .= 'https://doting.tw/animals/compatible?name=' . $target;

        //real path
        return [
            'status' => 'success',
            'url' => 'https://doting.tw/animals/compatible/image?date=' . $date . '&image=' . $image,
            'source_url' => $sourceUrl,
        ];
    }

    public static function getAllType($type)
    {
        $animal = DB::table('animal');

        if ($type == 'npc') {
            $animal = $animal->where('info', '!=', '');
        } else {
            $animal = $animal->whereNull('info');
        }

        $animal = $animal->get(['race', 'personality'])
            ->toArray();

        //race
        $race = collect($animal)->unique('race');
        $personality = collect($animal)->unique('personality');

        $formatPersonality = [];

        foreach ($personality as $data) {
            $explode = explode("、", $data->personality);

            if ($data->personality != '' && $explode != '') {
                if (isset($explode[0])) {
                    $formatPersonality[] = $explode[0];
                }

                if (isset($explode[1])) {
                    $formatPersonality[] = $explode[1];
                }
            }
        }

        $formatPersonality = collect($formatPersonality)->unique();

        return [
            'race' => $race,
            'personality' => $formatPersonality,
            'bd' => [
                '一月',
                '二月',
                '三月',
                '四月',
                '五月',
                '六月',
                '七月',
                '八月',
                '九月',
                '十月',
                '十一月',
                '十二月'
            ]
        ];
    }

    public static function getDataByMessage($message, $page = '', $type = '')
    {
    	$message = strtolower($message);
    	$notFound = '找不到捏 哇耶...(¬_¬)';

    	//阿戰隊
    	if ($message == '阿戰隊' && $type == '') {
    	    $name = ['阿一', '阿二', '阿三', '阿四'];
    	    $dbAnimal = DB::table('animal')
    	        ->whereIn('name', $name)
    	        ->orderBy('jp_name', 'asc')
    	        ->get()
    	        ->toArray();

            if ($page != '' && $page > 1) {
                return [];
            }

    	    return $dbAnimal;
    	}

    	$dbAnimal = DB::table('animal')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orWhere('race', 'like', '%' . $message . '%')
    	    ->orWhere('en_name', 'like', '%' . $message . '%')
    	    ->orWhere('jp_name', 'like', '%' . $message . '%')
    	    ->orWhere('personality', 'like', '%' . $message . '%')
            ->orWhere('say', $message)
    	    ->orWhere('bd_m', $message)
    	    ->orWhere('bd', $message);

        if ($type == 'npc') {
            $dbAnimal = $dbAnimal->where('info', '!=', '');
        }

        if ($page != '') {
            $dbAnimal = $dbAnimal
                ->orderBy('bd', 'asc')
                ->select()
                ->paginate(30)
                ->toArray();

            $dbAnimal = $dbAnimal['data'];
        } else {
            $dbAnimal = $dbAnimal
                ->orderBy('bd', 'asc')
                ->get()
                ->toArray();
        }

    	if (empty($dbAnimal)) {
    	    return $notFound;
    	}

    	return $dbAnimal;
    }

    public static function createItemBubble($item, $amiibo = false)
    {
        $url = env('APP_URL') . '/animals/detail?name=' . urlencode($item->name);

        return BubbleContainerBuilder::builder()
            ->setSize('kilo')
            ->setHero(self::createItemHeroBlock($item, $amiibo))
            ->setAction(
                new UriTemplateActionBuilder(
                    'detail',
                    $url,
                    new AltUriBuilder($url)
                )
            )
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createItemHeroBlock($item, $amiibo)
    {
        if ($amiibo && $item->amiibo != '') {
            $imgPath = env('APP_URL') . '/animal/card/' . urlencode($item->amiibo) . '.png?v=' . config('app.version');
        } else {
            $imgPath = env('APP_URL') . '/animal/' . urlencode($item->name) . '.png?v=' . config('app.version');
        }

        return ImageComponentBuilder::builder()
            ->setUrl($imgPath)
            ->setSize(ComponentImageSize::XXL)
            ->setAspectRatio('9:12')
            ->setAspectMode(ComponentImageAspectMode::FIT);
    }

    public static function createItemBodyBlock($item)
    {
        $components = [];
        $components[] = TextComponentBuilder::builder()
            ->setText($item->name . ' ' . ucfirst($item->en_name) . ' ' . $item->jp_name)
            ->setWrap(true)
            ->setAlign('center')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        if ($item->bd != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('生日: ' . $item->bd)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        $components[] = TextComponentBuilder::builder()
            ->setText('種族: ' . $item->race . ' (' . $item->sex . ')')
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        if ($item->personality != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('個性: ' . $item->personality)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        if ($item->say != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('口頭禪: ' . $item->say)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        if ($item->colors != '' && $item->colors != '[]') {
            $colors = json_decode($item->colors);

            if (is_array($colors)) {
                $printColors = implode("、", $colors);
                $components[] = TextComponentBuilder::builder()
                    ->setText('顏色: ' . $printColors)
                    ->setWrap(true)
                    ->setAlign('center')
                    ->setSize(ComponentFontSize::XS)
                    ->setMargin(ComponentMargin::MD)
                    ->setFlex(0);
            }
        }

        if ($item->styles != '' && $item->styles != '[]') {
            $styles = json_decode($item->styles);

            if (is_array($styles)) {
                $printStyles = implode("、", $styles);
                $components[] = TextComponentBuilder::builder()
                    ->setText('風格: ' . $printStyles)
                    ->setWrap(true)
                    ->setAlign('center')
                    ->setSize(ComponentFontSize::XS)
                    ->setMargin(ComponentMargin::MD)
                    ->setFlex(0);
            }
        }

        if ($item->info != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('介紹: ' . $item->info)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#f1f1f1')
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($components);
    }

    public static function createItemFooterBlock($item)
    {
        $url = env('APP_URL') . '/animals/detail?name=' . urlencode($item->name);
        $link = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setHeight('sm')
            ->setAction(
                new UriTemplateActionBuilder(
                    '查看詳情',
                    $url,
                    new AltUriBuilder($url)
                )
            );

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::HORIZONTAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$link]);
    }

    public static function getHomeImgUrls()
    {
        return [
            //貓
            'http://m.0123366.com/news/5135.html',
            //狗
            'http://m.0123366.com/news/5138.html',
            //狼
            'http://m.0123366.com/news/5149.html',
            //兔
            'http://m.0123366.com/news/5150.html',
            //章魚
            'http://m.0123366.com/news/5151.html',
            //鹿
            'http://m.0123366.com/news/5152.html',
            //青蛙
            'http://m.0123366.com/news/5153.html',
            //食蚁兽
            'http://m.0123366.com/news/5154.html',
            //星星
            'http://m.0123366.com/news/5309.html',
            //鸡
            'http://m.0123366.com/news/5310.html',
            //松鼠
            'http://m.0123366.com/news/5311.html',
            //鹰
            'http://m.0123366.com/news/5315.html',
            //猪
            'http://m.0123366.com/news/5316.html',
            //马
            'http://m.0123366.com/news/5321.html',
            //狮子
            'http://m.0123366.com/news/5323.html',
            //鸟
            'http://m.0123366.com/news/5325.html',
            //老鼠
            'http://m.0123366.com/news/5326.html',
            //牛
            'http://m.0123366.com/news/5328.html',
            //小熊
            'http://m.0123366.com/news/5329.html',
            //大熊
            'http://m.0123366.com/news/5330.html',
            //2魚
            'http://m.0123366.com/news/5346.html',
            //奶牛
            'http://m.0123366.com/news/5347.html',
            //綿羊
            'http://m.0123366.com/news/5349.html',
            //山羊
            'http://m.0123366.com/news/5350.html',
            //鴨子
            'http://m.0123366.com/news/5352.html',
            //猴子
            'http://m.0123366.com/news/5354.html',
            //袋鼠
            'http://m.0123366.com/news/5355.html',
            //大象
            'http://m.0123366.com/news/5356.html',
            //犀牛
            'http://m.0123366.com/news/5357.html',
            //考拉
            'http://m.0123366.com/news/5360.html',
            //鸵鸟
            'http://m.0123366.com/news/5361.html',
            //河马
            'http://m.0123366.com/news/5362.html',
            //企鹅
            'http://m.0123366.com/news/5364.html',
            //仓鼠
            'http://m.0123366.com/news/5365.html',
            //老虎
            'http://m.0123366.com/news/5366.html',
        ];
    }
}
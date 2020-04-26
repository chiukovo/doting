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
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;

use DB;

class AnimalServices
{
    public static function getRandomCard()
    {
        $card = DB::table('animal')
            ->where('amiibo', '!=', '')
            ->inRandomOrder()
            ->first('amiibo as name');

        $imgPath = 'https://' . request()->getHttpHost() . '/animal/card/' . urlencode($card->name) . '.png';
        $imgBuilder = new ImageMessageBuilder($imgPath, $imgPath);

        return $imgBuilder;
    }

    public static function getAllType($type)
    {
        $animal = DB::table('animal');

        if ($type == 'npc') {
            $animal = $animal->where('info', '!=', '');
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
    	$notFound = '找不到捏...(¬_¬)';

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

    public static function createItemBubble($item)
    {
        return BubbleContainerBuilder::builder()
            ->setSize('kilo')
            ->setHero(self::createItemHeroBlock($item))
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createItemHeroBlock($item)
    {
        $imgPath = 'https://' . request()->getHttpHost() . '/animal/' . urlencode($item->name) . '.png';

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

        $components[] = TextComponentBuilder::builder()
            ->setText('性別: ' . $item->sex)
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

        $components[] = TextComponentBuilder::builder()
            ->setText('種族: ' . $item->race)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        if ($item->bd != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('生日: ' . $item->bd)
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
        $add = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setAction(
                new PostbackTemplateActionBuilder(
                    '❤',
                    'action=add&table_id=' . $item->id . '&user_id=' . $this->userId . '&dispay_name=' . $this->displayName,
                    $item->name . '加入最愛'
                )
            );

        $remove = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setAction(
                new PostbackTemplateActionBuilder(
                    '🤍',
                    'action=remove&table_id=' . $item->id . '&user_id=' . $this->userId . '&dispay_name=' . $this->displayName,
                    $item->name . '移除最愛'
                )
            );

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::HORIZONTAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$add, $remove]);
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
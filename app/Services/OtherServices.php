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
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SeparatorComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;

use DB;

class OtherServices
{
    public static function getDataByMessage($message, $page = '', $onlyNow = false)
    {
    	$other = [];
    	$notFound = '找不到捏 哇耶...(¬_¬)';

    	//first
    	$first = mb_substr($message, 0, 1);

    	if ($first == '南' || $first == '北' || $first == '全') {
    	    $number = mb_substr($message, 1, 1);
            $number = self::chStringToNumber($number);
            //判斷number是否為中文
    	    $dateRange = range(1, 12);
    	    //type
    	    $type = mb_substr($message, -1, 1);
    	    $table = '';

            if ($page != '' && $page > 1) {
                return [];
            }

    	    if (in_array($number, $dateRange)) {
    	        if ($type == '魚') {
    	            $table = 'fish';
    	        } else if ($type == '蟲') {
    	            $table = 'insect';
    	        }

    	        if ($table != '') {
    	            $other = DB::table($table)->where('m' . $number, $first);

                    if ($first != '全') {
                        $other->orWhere('m' . $number, '全');
                    }
    	            $other = $other->orderBy('sell', 'desc')
    	                ->get()
    	                ->toArray();
    	        }

                if ($onlyNow) {
                    $format = [];

                    foreach ($other as $key => $data) {
                        if ($data->time == '全天') {
                            $format[] = $data;
                        } else {
                            $checkDate = explode('~', $data->time);
                            $start = isset($checkDate[0]) ? $checkDate[0] : 0;
                            $end = isset($checkDate[1]) ? $checkDate[1] : 0;
                            $now = date('H');
                            $range1 = [];
                            $range2 = [];

                            if ($start > $end) {
                                $range1 = range($start, 23);
                                $range2 = range(0, $end);
                            } else {
                                $range1 = range($start, $end);
                            }

                            if (!empty($range1) && in_array($now, $range1)) {
                                $class = 'has';
                            }

                            if (!empty($range2) && in_array($now, $range2)) {
                                $class = 'has';
                            }
                        }
                    }

                    return $format;
                }

    	        if (!empty($other)) {
    	            return $other;
    	        }
    	    }
    	}

    	//找蟲
        $insect = DB::table('insect')->where('name', 'like', '%' . $message . '%');

        if ($page != '') {
            $insect = $insect
                ->orderBy('sell', 'desc')
                ->select()
                ->paginate(30)
                ->toArray();

            $insect = $insect['data'];
        } else {
            $insect = $insect
                ->orderBy('sell', 'desc')
                ->get()
                ->toArray();
        }

        //找魚
        $fish = DB::table('fish')->where('name', 'like', '%' . $message . '%');

        if ($page != '') {
            $fish = $fish
                ->orderBy('sell', 'desc')
                ->select()
                ->paginate(30)
                ->toArray();

            $fish = $fish['data'];
        } else {
            $fish = $fish
                ->orderBy('sell', 'desc')
                ->get()
                ->toArray();
        }

        $other = array_merge($fish, $insect);

    	if (empty($other)) {
    	    return $notFound;
    	}

    	return $other;
    }

    public static function chStringToNumber($string)
    {
        //判斷是否有國字
        $targetArray = ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'];

        foreach ($targetArray as $key => $target) {
            if ($string == $target) {
                return $key + 1;
            }
        }

        return $string;
    }

    public static function createItemBubble($item)
    {
        if (isset($item->shadow)) {
            $url = env('APP_URL') . '/fish/detail?name=' . urlencode($item->name);
        } else {
            $url = env('APP_URL') . '/insect/detail?name=' . urlencode($item->name);
        }

        return $target = BubbleContainerBuilder::builder()
            ->setSize('kilo')
            ->setHero(self::createItemHeroBlock($item))
            ->setAction(
                new UriTemplateActionBuilder(
                    'detail',
                    $url,
                    new AltUriBuilder($url)
                )
            )
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createItemHeroBlock($item)
    {
        $imgPath = env('APP_URL') . '/other/' . urlencode($item->name) . '.png';

        return ImageComponentBuilder::builder()
            ->setUrl($imgPath)
            ->setSize(ComponentImageSize::XXL)
            ->setAspectRatio('9:12')
            ->setAspectMode(ComponentImageAspectMode::FIT);
    }


    public static function createItemBodyBlock($item)
    {
        $box = [];
        //box1
        $box1Inline = [];
        $box1Inline[] = TextComponentBuilder::builder()
            ->setText('位置')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $box1Inline[] = TextComponentBuilder::builder()
            ->setText($item->position)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setWrap(true)
            ->setFlex(2);

        if (isset($item->shadow)) {
            $box1Inline[] = TextComponentBuilder::builder()
                ->setText('影子')
                ->setSize(ComponentFontSize::XS)
                ->setColor('#aaaaaa')
                ->setFlex(1);

            $box1Inline[] = TextComponentBuilder::builder()
                ->setText($item->shadow)
                ->setSize(ComponentFontSize::XS)
                ->setColor('#444444')
                ->setFlex(2);
        }

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($box1Inline);

        //line
        $box[] = SeparatorComponentBuilder::builder()
            ->setMargin(ComponentMargin::MD);

        $spacer[] = SpacerComponentBuilder::builder()
            ->setSize(ComponentFontSize::XS);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($spacer);
        //line end

        $north = self::getMonthFormat($item, '北');
        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('北半球月份')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($north)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($boxInline);

        $south = self::getMonthFormat($item, '南');
        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('南半球月份')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($south)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($boxInline);

        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('時間')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->time)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($boxInline);


        $texts = TextComponentBuilder::builder()
            ->setText($item->name . ' $' . number_format($item->sell))
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        $outBox = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setMargin(ComponentMargin::LG)
            ->setContents($box);

        $result = [$texts, $outBox];


        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#f1f1f1')
            ->setContents($result);
    }

    public static function getMonthFormat($item, $type)
    {
        $target = [];

        if ($item->m1 == $type || $item->m1 == '全') {
            $target[] = 1;
        }

        if ($item->m2 == $type || $item->m2 == '全') {
            $target[] = 2;
        }

        if ($item->m3 == $type || $item->m3 == '全') {
            $target[] = 3;
        }

        if ($item->m4 == $type || $item->m4 == '全') {
            $target[] = 4;
        }

        if ($item->m5 == $type || $item->m5 == '全') {
            $target[] = 5;
        }

        if ($item->m6 == $type || $item->m6 == '全') {
            $target[] = 6;
        }

        if ($item->m7 == $type || $item->m7 == '全') {
            $target[] = 7;
        }

        if ($item->m8 == $type || $item->m8 == '全') {
            $target[] = 8;
        }

        if ($item->m9 == $type || $item->m9 == '全') {
            $target[] = 9;
        }

        if ($item->m10 == $type || $item->m10 == '全') {
            $target[] = 10;
        }

        if ($item->m11 == $type || $item->m11 == '全') {
            $target[] = 11;
        }

        if ($item->m12 == $type || $item->m12 == '全') {
            $target[] = 12;
        }

        sort($target);
        $groups = [];
        $string = '';
        $pre = '';

        for($i = 0; $i < count($target); $i++) {
            if ($i > 0 && ($target[$i - 1] == $target[$i] - 1)) {
                array_push($groups[count($groups) - 1], $target[$i]);
            } else {
                array_push($groups, array($target[$i]));
            }
        }

        foreach($groups as $group) {
            $pre = $string == '' ? '' : '、';

            if(count($group) == 1) {
                $string .= $pre . $group[0] . '月' . "\n";
            } else {
                $string .=  $pre . $group[0] . "~" . $group[count($group) - 1] . '月' . "\n";
            }
        }

        return $string;
    }
}
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
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;

use DB;

class OtherServices
{
    public static function getDataByMessage($message)
    {
    	$other = [];
    	$notFound = '找不到捏...(¬_¬)';

    	//first
    	$first = mb_substr($message, 0, 1);

    	if ($first == '南' || $first == '北' || $first == '全') {
    	    $number = mb_substr($message, 1, 1);
    	    $dateRange = range(1, 12);
    	    //type
    	    $type = mb_substr($message, -1, 1);
    	    $table = '';

    	    if (in_array($number, $dateRange)) {
    	        if ($type == '魚') {
    	            $table = 'fish';
    	        } else if ($type == '蟲') {
    	            $table = 'insect';
    	        }

    	        if ($table != '') {
    	            $other = DB::table($table)
    	                ->where('m' . $number, $first)
    	                ->orderBy('sell', 'desc')
    	                ->get()
    	                ->toArray();
    	        }

    	        if (!empty($other)) {
    	            return $other;
    	        }
    	    }
    	}

    	//找蟲
    	$other = DB::table('insect')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orderBy('sell', 'desc')
    	    ->get()
    	    ->toArray();

    	if (!empty($other)) {
    	    return $other;
    	}

    	//找魚
    	$other = DB::table('fish')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orderBy('sell', 'desc')
    	    ->get()
    	    ->toArray();

    	if (empty($other)) {
    	    return $notFound;
    	}

    	return $other;
    }

    public static function createItemBubble($item)
    {
        return $target = BubbleContainerBuilder::builder()
            ->setHero(self::createItemHeroBlock($item))
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createItemHeroBlock($item)
    {
        $imgPath = 'https://' . request()->getHttpHost() . '/other/' . urlencode($item->name) . '.png';

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
            ->setText($item->name . ' $' . number_format($item->sell))
            ->setWrap(true)
            ->setAlign('center')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        if (isset($item->shadow)) {
            $components[] = TextComponentBuilder::builder()
                ->setText('影子: ' . $item->shadow)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

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

        $south = self::getFishMonth($item, '南');

        $components[] = TextComponentBuilder::builder()
            ->setText('南半球月份: ' . $south)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $north = self::getFishMonth($item, '北');

        $components[] = TextComponentBuilder::builder()
            ->setText('北半球月份: ' . $north)
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

    public static function getFishMonth($item, $type)
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

        $string = implode(",", $target);

        return $string;
    }
}
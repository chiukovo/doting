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

class ArtServices
{
    public static function getDataByMessage($message, $page = '')
    {
    	$other = [];
    	$notFound = '找不到捏...(¬_¬)';

        $art = DB::table('art')->where('name', 'like', '%' . $message . '%');

        if ($page != '') {
            $art = $art
                ->select()
                ->paginate(30)
                ->toArray();

            $art = $art['data'];
        } else {
            $art = $art
                ->get()
                ->toArray();
        }

    	if (empty($art)) {
    	    return $notFound;
    	}

        //太多就給關鍵字
        if (count($art) > 1) {
            $text = '請問是要找 (´･ω･`)？' . "\n";
            $text = "\n";

            foreach ($art as $data) {
                $text .= $data->name . "\n";
            }
            
            $text = "\n";
            $text .= '哪個呢 ( ・◇・)？';

            return $text;
        }

    	return $art;
    }

    public static function createItemBubble($item, $img)
    {
        return $target = BubbleContainerBuilder::builder()
            ->setSize('kilo')
            ->setHero(self::createItemHeroBlock($item, $img))
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createItemHeroBlock($item, $img)
    {
        $imgPath = 'https://' . request()->getHttpHost() . '/art/' . urlencode($img) . '.png';

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
            ->setText($item->name)
            ->setWrap(true)
            ->setAlign('center')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        if ($item->info != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('說明: ' . $item->info)
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

        $string = implode(",", $target);

        return $string;
    }
}
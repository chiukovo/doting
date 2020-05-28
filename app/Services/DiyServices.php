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
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
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

class DiyServices
{
    public static function getDataByMessage($message)
    {
    	$message = strtolower($message);
    	$notFound = notFoundData();

    	$dbAnimal = DB::table('diy')
    	    ->where('name', 'like', '%' . $message . '%')
            ->orWhere('diy', 'like', '%' . $message . '%')
    	    ->get()
    	    ->toArray();

    	if (empty($dbAnimal)) {
    	    return $notFound;
    	}

    	return $dbAnimal;
    }

    public static function getSendData($dataArray, $message)
    {
    	$str = '';

    	if (is_array($dataArray) && !empty($dataArray)) {
            //> 30
            if (count($dataArray) > 30) {
                $text = '挖哩勒...搜尋結果有 ' . count($dataArray) . ' 個' . "\n";
                $text .= '👇👇 查看更多搜尋結果 👇👇' . "\n";
                $text .= env('APP_URL') . '/diy/list?text=' . urlencode($message);

                return $text;
            }

    	    foreach ($dataArray as $data) {
    	        $str .= $data->name;

    	        if ($data->type != '') {
    	            $str .= ' (' . $data->type . ')';
    	        }

    	        $str .= "\n";

    	        if ($data->get != '') {
    	            $str .= $data->get;
    	            $str .= "\n";
    	        }

    	        $str .= $data->diy;
    	        $str .= "\n";
    	        $str .= "\n";
    	    }
    	} else {
    	    $str = '找不到此Diy捏...(¬_¬)';
    	}

    	return $str;
    }

    public static function createItemBubble($item)
    {
        $url = env('APP_URL') . '/diy/list?text=' . urlencode($item->name);

        return BubbleContainerBuilder::builder()
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
        $imgPath = env('APP_URL') . '/diy/' . urlencode($item->name) . '.png?v=' . config('app.version');

        return ImageComponentBuilder::builder()
            ->setUrl($imgPath)
            ->setSize(ComponentImageSize::XXL)
            ->setAspectRatio('9:12')
            ->setAspectMode(ComponentImageAspectMode::FIT);
    }

    public static function createItemBodyBlock($item)
    {
        $item->type = $item->type != '' ? $item->type : '-';
        $item->get = $item->get != '' ? $item->get : '-';
        $item->diy = $item->diy != '' ? $item->diy : '-';

        $box = [];
        //box1
        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('類型')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->type)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setWrap(true)
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($boxInline);

        //line
        $box[] = SeparatorComponentBuilder::builder()
            ->setMargin(ComponentMargin::MD);

        $spacer[] = SpacerComponentBuilder::builder()
            ->setSize(ComponentFontSize::XS);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($spacer);
        //line end

        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('取得方式')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->get)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($boxInline);

        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('製作方式')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->diy)
            ->setSize(ComponentFontSize::XS)
            ->setWrap(true)
            ->setColor('#444444')
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($boxInline);

        $texts = TextComponentBuilder::builder()
            ->setText($item->name)
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
}
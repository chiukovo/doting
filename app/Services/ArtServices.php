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
    	$notFound = '找不到捏 哇耶...(¬_¬)';

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

            $text .= "\n";
            $text .= '哪個呢 ( ・◇・)？';
            $text .= "\n";
            $text .= "\n";
            $text .= '或看全部 >> ' . env('APP_URL') . '/art/list';

            return $text;
        }

    	return $art;
    }

    public static function createItemBubble($item, $img)
    {
        $url = env('APP_URL') . '/art/detail?name=' . urlencode($item->name);

        return BubbleContainerBuilder::builder()
            ->setSize('kilo')
            ->setHero(self::createItemHeroBlock($item, $img))
            ->setAction(
                new UriTemplateActionBuilder(
                    'detail',
                    $url,
                    new AltUriBuilder($url)
                )
            )
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createItemHeroBlock($item, $img)
    {
        $imgPath = env('APP_URL') . '/art/' . urlencode($img) . '.png';

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

    public static function createItemFooterBlock($item)
    {
        $url = env('APP_URL') . '/art/detail?name=' . urlencode($item->name);
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
}
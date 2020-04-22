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

class AnimalServices
{
    public static function getDataByMessage($message)
    {
    	$message = strtolower($message);
    	$notFound = '找不到捏...(¬_¬)';

    	//阿戰隊
    	if ($message == '阿戰隊') {
    	    $name = ['阿一', '阿二', '阿三', '阿四'];
    	    $dbAnimal = DB::table('animal')
    	        ->whereIn('name', $name)
    	        ->orderBy('jp_name', 'asc')
    	        ->get()
    	        ->toArray();

    	    return $dbAnimal;
    	}

    	$dbAnimal = DB::table('animal')
    	    ->where('name', 'like', '%' . $message . '%')
    	    ->orWhere('race', 'like', '%' . $message . '%')
    	    ->orWhere('en_name', 'like', '%' . $message . '%')
    	    ->orWhere('jp_name', 'like', '%' . $message . '%')
    	    ->orWhere('personality', 'like', '%' . $message . '%')
    	    ->orWhere('bd_m', $message)
    	    ->orWhere('bd', $message)
    	    ->orderBy('bd', 'asc')
    	    ->get()
    	    ->toArray();

    	if (empty($dbAnimal)) {
    	    return $notFound;
    	}

    	return $dbAnimal;
    }

    public static function createItemBubble($item)
    {
        return BubbleContainerBuilder::builder()
            ->setHero(self::createItemHeroBlock($item))
            ->setBody(self::createItemBodyBlock($item));
    }

    public static function createTestItemBubble($item)
    {
        return BubbleContainerBuilder::builder()
            ->setHero(self::createItemHeroBlock($item))
            ->setBody(self::createTestItemBodyBlock($item));
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

    public static function createTestItemBodyBlock($item)
    {
        $components = [];
        $components[] = TextComponentBuilder::builder()
            ->setText($item->name . ' ' . ucfirst($item->en_name) . ' ' . $item->jp_name)
            ->setWrap(true)
            ->setAlign('center')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::MD);

        $components[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::NONE)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('性別')
                    ->setSize(ComponentFontSize::XS)
                    ->setFlex(1),
                TextComponentBuilder::builder()
                    ->setText($item->sex)
                    ->setWrap(true)
                    ->setSize(ComponentFontSize::XS)
                    ->setFlex(5)
            ]);

        if ($item->personality != '') {
            $components[] = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setSpacing(ComponentSpacing::NONE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('個性')
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(1),
                    TextComponentBuilder::builder()
                        ->setText($item->personality)
                        ->setWrap(true)
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(5)
                ]);
        }

        $components[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::NONE)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('種族')
                    ->setSize(ComponentFontSize::XS)
                    ->setFlex(1),
                TextComponentBuilder::builder()
                    ->setText($item->race)
                    ->setWrap(true)
                    ->setSize(ComponentFontSize::XS)
                    ->setFlex(5)
            ]);

        if ($item->bd != '') {
            $components[] = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setSpacing(ComponentSpacing::NONE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('生日')
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(1),
                    TextComponentBuilder::builder()
                        ->setText($item->bd)
                        ->setWrap(true)
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(5)
                ]);
        }

        if ($item->say != '') {
            $components[] = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setSpacing(ComponentSpacing::NONE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('口頭禪')
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(1),
                    TextComponentBuilder::builder()
                        ->setText($item->say)
                        ->setWrap(true)
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(5)
                ]);
        }

        if ($item->info != '') {
            $components[] = BoxComponentBuilder::builder()
                ->setLayout(ComponentLayout::BASELINE)
                ->setSpacing(ComponentSpacing::NONE)
                ->setContents([
                    TextComponentBuilder::builder()
                        ->setText('介紹')
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(1),
                    TextComponentBuilder::builder()
                        ->setText($item->info)
                        ->setWrap(true)
                        ->setSize(ComponentFontSize::XS)
                        ->setFlex(5)
                ]);
        }

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#f1f1f1')
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($components);
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
}
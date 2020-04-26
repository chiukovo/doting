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

class ItemsServices
{
    public static function getAllType($type)
    {
        $items = DB::table('items');

        //家具
        if ($type == 'furniture') {
            $items = $items->whereIn('type', self::getFurnitureAllType());
        } else {
            $items = $items->whereNotIn('type', self::getFurnitureAllType());
        }

        $items = $items->get(['type as items_type', 'buy_type', 'detail_type'])
            ->toArray();

        //race
        $itemsType = collect($items)->unique('items_type');
        $buyType = collect($items)->unique('buy_type');
        $detailType = collect($items)->unique('detail_type');

        return [
            'itemsType' => $itemsType,
            'buyType' => $buyType,
            'detailType' => $detailType,
        ];
    }

    public static function getFurnitureAllType()
    {
        return [
            '鞋',
            '包',
            '飾品',
            '頭盔',
            '帽子',
            '襪子',
            '連衣裙',
            '下裝',
            '上裝',
        ];
    }

    public static function getDataByMessage($message, $page = '', $type = '')
    {
    	$message = strtolower($message);
    	$notFound = '找不到捏...(¬_¬)';

        if ($message == '豆丁') {
            return '(*´∀`)~♥';
        }

    	$dbAnimal = DB::table('items')
    	    ->where('name', 'like', '%' . $message . '%');

        //家具
        if ($type == 'furniture') {
            $dbAnimal = $dbAnimal->whereIn('type', self::getFurnitureAllType());
        } else if ($type == 'apparel') {
            $dbAnimal = $dbAnimal->whereNotIn('type', self::getFurnitureAllType());
        }

        if ($page != '') {
            $dbAnimal = $dbAnimal
                ->select()
                ->paginate(30)
                ->toArray();

            $dbAnimal = $dbAnimal['data'];
        } else {
            $dbAnimal = $dbAnimal->get()
                ->toArray();
        }

        //> 50
        if (count($dbAnimal) > 30 && $page == '') {
            $text = '挖哩勒...搜尋結果有 ' . count($dbAnimal) . ' 個' . "\n";
            $text .= '👇👇 查看搜尋結果 👇👇' . "\n";
            $text .= 'https://' . request()->getHttpHost() . '/items/all/text=' . $message;
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
        $imgPath = 'https://' . request()->getHttpHost() . '/items/' . urlencode($item->img_name) . '.png';

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

        $components[] = TextComponentBuilder::builder()
            ->setText('價格: $' . number_format($item->source_sell))
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('賣出: $' . number_format($item->sell))
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('回收: $' . number_format($item->sample_sell))
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        $components[] = TextComponentBuilder::builder()
            ->setText('類型: ' . $item->type)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        if ($item->buy_type != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('訂購: ' . $item->buy_type)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }


        $components[] = TextComponentBuilder::builder()
            ->setText('分類: ' . $item->detail_type)
            ->setWrap(true)
            ->setAlign('center')
            ->setSize(ComponentFontSize::XS)
            ->setMargin(ComponentMargin::MD)
            ->setFlex(0);

        if ($item->size != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('尺寸: ' . $item->size)
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
}
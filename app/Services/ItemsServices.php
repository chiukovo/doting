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

class ItemsServices
{
    public static function getAllType($type)
    {
        $items = DB::table('items_new');

        //家具
        if ($type == 'furniture') {
            $items = $items->whereIn('category', self::getFurnitureAllType());
        } else if ($type == 'apparel') {
            $items = $items->whereNotIn('category', ItemsServices::getFurnitureAllType());
        } else if ($type == 'plant') {
            $items = $items->where('category', '植物');
        }

        $items = $items->whereNotNull('category');
        $items = $items->get(['category'])
            ->toArray();

        //race
        $category = collect($items)->unique('category');

        return [
            'category' => $category,
        ];
    }

    public static function getFurnitureAllType()
    {
        return [
            '頭飾',
            '臉部',
            '上衣',
            '下裝',
            '包包',
            '襪子',
            '鞋子',
            '飾品',
        ];
    }

    public static function getDataByMessage($message, $page = '', $type = '')
    {
    	$message = strtolower($message);
    	$notFound = '找不到捏 哇耶...(¬_¬)';

        if ($message == '豆丁') {
            return '(*´∀`)~♥';
        }

        $items = DB::table('items_new')
    	    ->where('name', 'like', '%' . $message . '%');

        //家具
        if ($type == 'furniture') {
            $items = $items->whereIn('category', self::getFurnitureAllType());
        } else if ($type == 'apparel') {
            $items = $items->whereNotIn('category', self::getFurnitureAllType());
        } else if ($type == 'plant') {
            $items = $items->where('category', '植物');
        }

        if ($page != '') {
            $items = $items
                ->select()
                ->paginate(30)
                ->toArray();

            $items = $items['data'];
        } else {
            $items = $items->get()
                ->toArray();
        }

        //> 30
        if (count($items) > 30 && $page == '') {
            $text = '挖哩勒...搜尋結果有 ' . count($items) . ' 個' . "\n";
            $text .= '👇👇 查看更多搜尋結果 👇👇' . "\n";
            $text .= env('APP_URL') . '/items/all/list?text=' . urlencode($message);
        }

        if (empty($items)) {
    	    return $notFound;
    	}

        return $items;
    }

    public static function createItemBubble($item)
    {
        $url = env('APP_URL') . '/items/all/list?text=' . urlencode($item->name);

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
        $imgPath = env('APP_URL') . '/itemsNew/' . urlencode($item->img_name) . '.png';

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


        if ($item->category != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('類型: ' . $item->category)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        if ($item->buy != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('價格: $' . number_format($item->buy))
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        if ($item->sell != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('賣出: $' . number_format($item->sell))
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

        if ($item->info != '') {
            $components[] = TextComponentBuilder::builder()
                ->setText('配方: ' . $item->info)
                ->setWrap(true)
                ->setAlign('center')
                ->setSize(ComponentFontSize::XS)
                ->setMargin(ComponentMargin::MD)
                ->setFlex(0);
        }

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
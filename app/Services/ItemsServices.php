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
        $item->buy = $item->buy != '' ? $item->buy : '-';
        $item->sell = $item->sell != '' ? $item->sell : '-';
        $item->size = $item->size != '' ? $item->size : '-';

        $box = [];
        //box1
        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('分類')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->category)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setWrap(true)
            ->setFlex(2);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText('size')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->size)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
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
            ->setText('商店販售價')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->buy)
            ->setSize(ComponentFontSize::XS)
            ->setColor('#444444')
            ->setFlex(2);

        $box[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents($boxInline);

        $boxInline = [];
        $boxInline[] = TextComponentBuilder::builder()
            ->setText('商店回收價')
            ->setSize(ComponentFontSize::XS)
            ->setColor('#aaaaaa')
            ->setFlex(1);

        $boxInline[] = TextComponentBuilder::builder()
            ->setText($item->sell)
            ->setSize(ComponentFontSize::XS)
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
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use DatabaseMigrations;
use Tests\TestCase;
use App;

class FormatTextTest extends TestCase
{
    /**
     * 查詢島民、NPC相關資訊
     */
    public function testAnimal()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['#阿一', '#茶茶丸', '#Dom', '#ちゃちゃまる', '#曹賣', '#運動', '#小熊', '#6', '#阿戰隊', '#1.21', '抽', '#一絲絲'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) || empty($result)) {
                $this->assertTrue(false);
            }
        }
        
        //test my animail
        $result = $class->testMyAnimal();

        if (!is_array($result)) {
            $this->assertTrue(false);
        }

        $this->assertTrue(true);
    }

    /**
     * 查詢魚、昆蟲圖鑑
     */
    public function testOther()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['$黑魚', '$金', '$南4月 魚', '$北5月 蟲', '$全5月 魚'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) || empty($result)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    /**
     * 排行榜
     */
    public function searchWinner()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['搜尋排行榜'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) || empty($result)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    /**
     * 查詢DIY圖鑑
     */
    public function testDiy()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['做石斧頭', '做櫻花'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) || empty($result)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    /**
     * 查詢家具、服飾、植物
     */
    public function testItems()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['找貓跳台', '找咖啡杯', '找熱狗', '找黃金'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) || empty($result)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    /**
     * 查詢藝術品
     */
    public function testArt()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['查充滿母愛的雕塑', '查名畫'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) && !is_object($result)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    /**
     * 查詢化石
     */
    public function testFossil()
    {
        $class = App::make('App\Http\Controllers\AnimalCrossingController');
        $texts = ['化石 三葉蟲', '化石 暴龍'];

        foreach ($texts as $text) {
            $result = $class->getSendBuilder($text);

            if (!is_array($result) || empty($result)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }
}

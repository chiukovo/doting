@extends('layouts.web')
@section('title', '豆丁指令')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="#">豆丁教學</a>
</div>
<div id="app" class="media">
  <div class="media-banner">
    <img class="w100" src="/image/info.jpg" style="border-radius: 16px;">
  </div>
  <div class="media-card">
    <div class="media-card-title">問題回復專區</div>
    <div class="update-card">
      <a href="https://reurl.cc/9ER9ya" target="_blank">https://reurl.cc/9ER9ya</a>
    </div>
  </div>
  <div class="media-card">
    <div class="media-card-title">豆丁教學</div>
      <div class="update-card">
        <div class="content">
          <b>加豆丁LINE好友 ID: <span style="color: red">@875uxytu</span></b>
          <br>
          <pre>
<img src="/image/doting.jpg">

👇以下教您如何使用指令👇
1.輸入【豆丁】，重新查詢教學指令
範例 豆丁

2.【#】查詢島民、NPC相關資訊
範例 查名稱：#茶茶丸
範例 查名稱：#Dom
範例 查名稱：#ちゃちゃまる
範例 查名稱：#曹賣
範例 查個性：#運動
範例 查種族：#小熊
範例 查生日：#1.21
範例 查生日：#6
範例 查戰隊：#阿戰隊

3.【$】查詢魚、昆蟲圖鑑
範例 查名稱：$黑魚
範例 查名稱：$金
範例 查月份：$南4月 魚
範例 查月份：$北5月 蟲
範例 查月份：$全5月 魚

4.【做】查詢DIY圖鑑
範例 查名稱：做石斧頭
範例 查名稱：做櫻花

5.【找】查詢家具、服飾、植物
範例 查名稱：找貓跳台
範例 查名稱：找咖啡杯
範例 查名稱：找熱狗
範例 查名稱：找黃金

6.【查】查詢藝術品
範例 查名稱：查充滿母愛的雕塑
範例 查名稱：查名畫

7.【化石】查詢化石
範例 查名稱：化石 三葉蟲
範例 查名稱：化石 暴龍

8.抽 amiibo卡片 (◑‿◐)
範例 抽

歡迎提供缺漏或錯誤修正的資訊，以及功能建議。
          </pre>
        </div>
      </div>
    </div>
    @include('layouts.goTop')
  </div>
</div>
<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
  })
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-136875596-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-136875596-3');
</script>
@endsection
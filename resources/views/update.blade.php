@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="#">更新資訊</a>
</div>
<div id="app" class="media">
  <div class="media-banner">
    <img class="w100" src="/image/info.jpg" style="border-radius: 16px;">
  </div>
  <div class="media-card">
    <div class="media-card-title">問題回復專區</div>
    <div class="update-card">
      <a href="https://reurl.cc/9ER9ya">https://reurl.cc/9ER9ya</a>
    </div>
  </div>
  <div class="media-card">
    <div class="media-card-title">更新資訊</div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v2.0.0</div>
          <div class="data">2020/4/20</div>
        </div>
        <div class="content">
          <pre>
優化UI 改用卡片顯示方式 更方便閱讀
<img src="/update/20200420-1.png">
1. 優化UI

2. 新增 "種族" "個性" 查詢
種族範例: #貓
個性範例: #運動

3. 新增 "英文" "日文" 查詢
範例: #boone
範例: #ポンチョ

4. 新增 "生日" 查詢 (只有月份)
範例: #1
範例: #2
範例: #3
或是精準查詢
範例: #3.14
範例: #7.03

5. 新增 性別
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
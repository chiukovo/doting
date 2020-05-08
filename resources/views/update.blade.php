@extends('layouts.web')
@section('title', '豆丁更新資訊')
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
      <a href="https://reurl.cc/9ER9ya" target="_blank">https://reurl.cc/9ER9ya</a>
    </div>
  </div>
  <div class="media-card">
    <div class="media-card-title">更新資訊</div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.5.3</div>
          <div class="data">2020/5/8</div>
        </div>
        <div class="content">
          <pre>
1.新增雨傘查詢 關鍵字【找】
範例: 找童話風雨傘
2.新增地墊查詢 關鍵字【找】
範例: 找活潑媽媽廚房地墊
3.新增地板查詢 關鍵字【找】
範例: 找楓葉地板
          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.5.2</div>
          <div class="data">2020/5/7</div>
        </div>
        <div class="content">
          <pre>
1.新增豆丁搜尋排行榜
範例: 搜尋排行榜
就會出現連結: <a href="https://doting.tw/statistics">https://doting.tw/statistics</a>
          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.5.1</div>
          <div class="data">2020/5/6</div>
        </div>
        <div class="content">
          <pre>
1.動物列表圖片調整成ICON顯示
2.動物個性相容度功能 (開發中)
3.大頭菜統計計算 (開發中)
          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.5.0</div>
          <div class="data">2020/5/5</div>
        </div>
        <div class="content">
          <pre>
1.新增動物相同種族
2.新增DIY也能用從材料反搜索DIY方程式
範例: 找硬木材
          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.4.0</div>
          <div class="data">2020/4/30</div>
        </div>
        <div class="content">
          <pre>
1.修改成所有卡片都能點擊連到網站
2.移除動物 藝術品卡片詳細按鈕,
  改成直接點擊卡片即可
3.新增網站-植物
4.新增網站-唱片
          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.3.0</div>
          <div class="data">2020/4/29</div>
        </div>
        <div class="content">
          <pre>
不好意思 今日豆丁被攻擊導致伺服器中斷
目前已復活嚕 哇耶!
1.新增植物圖鑑 + 植物合成配方
範例: 找金色玫瑰
<img src="/update/20200429-1.jpg">
範例: 找柊樹幼苗
<img src="/update/20200429-2.jpg">

          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.2.0</div>
          <div class="data">2020/4/28</div>
        </div>
        <div class="content">
          <pre>
新增博物館 查化石
範例: 化石 暴龍
<img src="/update/20200428-1.jpg">
          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.1.0</div>
          <div class="data">2020/4/26</div>
        </div>
        <div class="content">
          <pre>
1.新增博物館 (藝術品查詢)
2.新增DIY 圖片
範例: 查充滿母愛的雕塑
<img src="/update/20200427-1.jpg">

範例: 查畫
<img src="/update/20200427-2.jpg">

點選查看詳情
<img src="/update/20200427-3.jpg">

          </pre>
        </div>
      </div>
      <div class="update-card">
        <div class="update-title">
          <div class="title">更新 v3.0.0</div>
          <div class="data">2020/4/26</div>
        </div>
        <div class="content">
          <pre>
1. 豆丁森友會 網頁版上線拉 ｡:.ﾟヽ(*´∀`)ﾉﾟ.:｡
2. 新增動物細節 (居家照片, 喜愛唱片)
3. 優化搜尋
          </pre>
        </div>
      </div>
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
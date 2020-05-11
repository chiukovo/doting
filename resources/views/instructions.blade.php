@extends('layouts.web')
@section('title', '豆丁指令')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">豆丁教學</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="\">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">豆丁教學</li>
      </ol>
    </nav>
    <section class="post version">
      <div class="row justify-content-md-center">
        <div class="col">
          <div class="post-body">
            <div class="alert alert-primary mt-2" role="alert">
              <h4 class="alert-heading">問題回復專區</h4>
              <p class="mb-0">在意見回饋的問題都可以在這邊查詢並得到回復，感謝您的回饋，我們會盡力改善且盡可能幫助大家。</p>
              <a href="https://reurl.cc/9ER9ya" target="_blank">https://reurl.cc/9ER9ya</a>
            </div>
            <div class="card">
              <div class="card-body">
                <p><strong>加豆丁LINE好友 ID: <span class="text-danger">@875uxytu</span></strong>
                <br><img src="../image/doting_line.png" class="img-fluid"><img src="../image/doting_qrcode.png" class="img-fluid"></p>
                <p>👇以下教您如何使用指令👇<br>
                  歡迎提供缺漏或錯誤修正的資訊，以及功能建議。</p>
                <ul class="list-decimal">
                  <li>輸入【豆丁】，重新查詢教學指令<br>範例 豆丁<br></li>
                  <li>
                    【#】查詢島民、NPC相關資訊<br>
                    範例 查名稱：#茶茶丸<br>
                    範例 查名稱：#Dom<br>
                    範例 查名稱：#ちゃちゃまる<br>
                    範例 查名稱：#曹賣<br>
                    範例 查個性：#運動<br>
                    範例 查種族：#小熊<br>
                    範例 查生日：#1.21<br>
                    範例 查生日：#6<br>
                    範例 查戰隊：#阿戰隊<br>
                    範例 查口頭禪：#哇耶<br></li>
                  <li>
                    【$】查詢魚、昆蟲圖鑑<br>
                    範例 查名稱：$黑魚<br>
                    範例 查名稱：$金<br>
                    範例 查月份：$南4月 魚<br>
                    範例 查月份：$北5月 蟲<br>
                    範例 查月份：$全5月 魚
                  </li>
                  <li>
                    【做】查詢DIY圖鑑<br>
                    範例 查名稱：做石斧頭<br>
                    範例 查名稱：做櫻花
                  </li>
                  <li>
                    【找】查詢家具、服飾、植物<br>
                    範例 查名稱：找貓跳台<br>
                    範例 查名稱：找咖啡杯<br>
                    範例 查名稱：找熱狗<br>
                    範例 查名稱：找黃金
                  </li>
                  <li>
                    【查】查詢藝術品<br>
                    範例 查名稱：查充滿母愛的雕塑<br>
                    範例 查名稱：查名畫
                  </li>
                  <li>
                    【化石】查詢化石<br>
                    範例 查名稱：化石 三葉蟲<br>
                    範例 查名稱：化石 暴龍
                  </li>
                  <li>
                    抽 amiibo卡片 (◑‿◐)<br>
                    範例 抽
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  @include('layouts.goTop')
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
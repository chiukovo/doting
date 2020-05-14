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
<pre>
{{ printDoc() }}
</pre>
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
@extends('layouts.web')
@section('title', '贊助豆丁')
@section('content')
<div class="content-wrap">
  <div class="container">
    <h2 class="content-title">贊助</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">贊助</li>
      </ol>
    </nav>
    <section class="post">
      <div class="row justify-content-md-center">
        <div class="col col-lg-12">
          <div class="post-header mb-3">
            <div class="post-card">
              <img src="/image/info.jpg" class="img-fluid rounded">
            </div>
          </div>
          <div class="post-body">
            <div class="card">
              <div class="card-header">
                贊助豆丁 ٩(^ᴗ^)۶
              </div>
              <div class="card-body">
                <p>本資料站資訊一切免費，僅提供給喜愛 動物森友會 的玩家參考。</p>
                <p>我們維持網站運作必須花費許多精力與資源，包括但不限於：</p>
                <ul>
                  <li>網站伺服器機器與線路成本</li>
                  <li>網頁系統建置與維護</li>
                  <li>遊戲資料分析整理</li>
                  <li><s>無止境的修BUG</s></li>
                </ul>
                <p>由於遊戲資料眾多，也許無法將所有資訊呈現給大家，但我們會盡力將有用的資訊釋出。 盡可能幫助大家。</p>
                <p>
                  <a class="btn btn-primary" href="https://forms.gle/Q7StMmonyGdL4rCFA" target="_blank">意見回饋</a>
                </p>
                <p class="mb-0">
                  豆丁信箱: <a href="mailto:q8156697@gmail.com">q8156697@gmail.com</a>
                </p>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                實際行動 (๑´ㅂ`๑)
              </div>
              <div class="card-body">
                <p class="text-center"><img src="../image/donate.png?v=update1" alt="贊助豆丁"></p>
                <p class="text-center"><a href="https://p.ecpay.com.tw/88B51" target="_blank" class="btn btn-success">歡迎贊助、打賞豆丁</a></p>
                <p class="text-danger">如果覺得我們的資訊對您有幫助，歡迎以行動支持我們！</p>
                <p class="mb-0">轉跳頁面為金流服務由綠界科技ECPay 實況主贊助功能！</p>
                <p class="mb-0">當然，如果您只是想看看網頁瀏覽我們的廣告也是可以，完全不會影響瀏覽網站的功能， 原則上只要能力許可，我們仍會繼續維持網站的運作。</p>
              </div>
            </div>
            <div class="card">
              <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
              <ins class="adsbygoogle"
                   style="display:block"
                   data-ad-client="ca-pub-2560043137442562"
                   data-ad-slot="4286195067"
                   data-ad-format="auto"
                   data-full-width-responsive="true"></ins>
              <script>
                   $(document).ready(function(){(adsbygoogle = window.adsbygoogle || []).push({})})
              </script>
            </div>
            <div class="card donate">
              <div class="card-header">贊助名單 由衷感謝你們 ٩(●˙▿˙●)۶…⋆ฺ</div>
              <div class="card-body">
                <ul class="donate-list">
                  @foreach($donate as $detail)
                  <li>
                    <div class="donate-list-item">
                      <span class="donate-list-date">{{ $detail->date }}</span>
                      <span class="donate-list-name">{{ $detail->name }}</span>
                      <span class="donate-list-money">{{ $detail->money }}</span>
                    </div>
                    <div class="donate-list-re">
                      {{ $detail->reply }}
                    </div>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-136875596-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-136875596-3');
</script>
@endsection
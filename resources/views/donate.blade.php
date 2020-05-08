@extends('layouts.web')
@section('title', '贊助豆丁')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/donate">贊助</a>
</div>
<div class="media donate">
  <div class="media-banner"><img src="/image/info.jpg" class="w100" style="border-radius: 16px;"></div>
  <div class="media-card">
    <div class="media-card-title">贊助豆丁 ٩(^ᴗ^)۶</div>
    <div class="update-card">
      <p>本資料站資訊一切免費，僅提供給喜愛 動物森友會 的玩家參考。</p>
      <p>我們維持網站運作必須花費許多精力與資源，包括但不限於：</p>
      <ul class="media-list">
        <li>網站伺服器機器與線路成本</li>
        <li>網頁系統建置與維護</li>
        <li>遊戲資料分析整理</li>
        <li><s>無止境的修BUG</s></li>
      </ul>
      <p>由於遊戲資料眾多，也許無法將所有資訊呈現給大家，但我們會盡力將有用的資訊釋出。 盡可能幫助大家。</p>
      <p>
        👉<a href="https://forms.gle/Q7StMmonyGdL4rCFA" target="_blank">意見回饋</a>
      </p>
      <p>
        👉<a href="mailto:q8156697@gmail.com">聯絡豆丁</a> q8156697@gmail.com
      </p>
    </div>
  </div>
  <div class="media-card">
    <div class="media-card-title">實際行動 (๑´ㅂ`๑) </div>
    <div class="update-card">
      <p class="text-center">
        <img src="/image/donate.png?v=update1" alt="贊助豆丁">
      </p>
      <p class="text-center">
        <a href="https://p.ecpay.com.tw/88B51" target="_blank"><button class="btn btn-primary">歡迎贊助、打賞豆丁</button></a>
      </p>
      <p class="text-danger">如果覺得我們的資訊對您有幫助，歡迎以行動支持我們！</p>
      <p>轉跳頁面為金流服務由綠界科技ECPay 實況主贊助功能！</p>
      <p>當然，如果您只是想看看網頁瀏覽我們的廣告也是可以，完全不會影響瀏覽網站的功能， 原則上只要能力許可，我們仍會繼續維持網站的運作。</p>
    </div>
  </div>
  <div class="media-card">
    <div class="media-card-title">贊助名單 由衷感謝你們 ٩(●˙▿˙●)۶…⋆ฺ</div>
    <div class="update-card">
      <ul class="donate-list">
        <li>
          2019/05/08 --- 小虎的經紀人 NTD.300 ---
          <small class="donate-list-re">經紀人您好 不好意思虧待您家小虎了 圖片已更正 如有更漂亮的圖片也歡迎提供給我歐 (上方有信箱) ٩(ˊᗜˋ )و</small>
        </li>
        <li>
          2019/05/08 --- 胡利洋行 NTD.300 ---
          <small class="donate-list-re">感謝! 請繼續支持我們 ̋(๑˃́ꇴ˂̀๑)</small>
        </li>
        <li>
          2019/05/08 --- 黎客房貸 NTD.300 ---
          <small class="donate-list-re">謝謝支持 (๑¯◡¯๑)</small>
        </li>
        <li>
          2019/05/07 --- 星見曦 NTD.30 ---
          <small class="donate-list-re">沒問題歐 謝謝支持 ʘ‿ʘ</small>
        </li>
        <li>
          2019/05/05 --- 鹿島 NTD.100 ---
          <small class="donate-list-re">不客氣 謝謝支持 ＼( ^▽^ )／</small>
        </li>
        <li>
          2019/05/01 --- 黃*君 NTD.266 ---
          <small class="donate-list-re">謝謝支持 (๑¯◡¯๑)</small>
        </li>
        <li>
          2019/05/01 --- 星羽 NTD.100 ---
          <small class="donate-list-re">謝謝支持 ´･ᴗ･`</small>
        </li>
        <li>
          2019/05/01 --- Jeff NTD.100 ---
          <small class="donate-list-re">謝謝你Jeff大大 沒問題會持續更新的</small>
        </li>
        <li>
          2019/04/30 --- 葉*君 NTD.30 ---
          <small class="donate-list-re">謝謝你~~</small>
        </li>
        <li>
          2019/04/30 --- 陳*安 NTD.100 ---
          <small class="donate-list-re">謝謝大大 ⊙▽⊙</small>
        </li>
        <li>
          2019/04/30 --- 吳*穎 NTD.220 ---
          <small class="donate-list-re">感謝 ◉‿◉</small>
        </li>
        <li>
          2019/04/29 --- 陳*琪 NTD.77 ---
          <small class="donate-list-re">謝謝您 (´・ω・`)</small>
        </li>
        <li>
          2019/04/29 --- Quniiii NTD.5 ---
          <small class="donate-list-re">謝 Quniiii!</small>
        </li>
      </ul>
    </div>
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
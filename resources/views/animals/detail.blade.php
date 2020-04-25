@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/animal/list">動物居民</a>
  <span class="sep">/</span>
  <a href="#">{{ $detail->name }}</a>
</div>
<article>
  <section class="animals-info">
    <h1 class="media-title">{{ $detail->name }}</h1>
    <div class="media">
      <div class="media-photo">
        <img src="/animal/{{ $detail->name }}.png" alt="{{ $detail->name }}">
      </div>
      <div class="media-body">
        <div class="media-tag">
          <label>快速查詢</label>
          <button class="tag">{{ $detail->race }}</button>
          <button class="tag">{{ $detail->personality }}</button>
          <button class="tag">{{ $detail->bd }}</button>
        </div>
        <div class="media-card">
          <div class="animals-info-list">
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>名稱</label>
                <span>
                  {{ $detail->name }} /
                  @if($detail->en_name != '')
                  {{ $detail->en_name }} /
                  @endif
                  @if($detail->jp_name != '')
                  {{ $detail->jp_name }}
                  @endif
                </span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>種族</label>
                <span>{{ $detail->race }}</span>
              </div>
              <div class="animals-info-item">
                <label>個性</label>
                <span>{{ $detail->personality }}</span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>性別</label>
                <span>{{ $detail->sex }}</span>
              </div>
              <div class="animals-info-item">
                <label>生日</label>
                <span>{{ $detail->bd }}</span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>口頭禪</label>
                <span>{{ $detail->say }}</span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>座右銘</label>
                <span>{{ $detail->target }}</span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>目標</label>
                <span></span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label style="width: 100px;">Amiibo Card</label>
                <span>#249 Beardo</span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label style="width: 100px;">最喜歡的歌曲</label>
                <span>K.K. Country</span>
                <!-- <span><a href="#" target="_blank">K.K. Country</a></span> -->
                <div class="animals-info-audio">
                  <audio controls name="media">
                    <source src="https://vignette.wikia.nocookie.net/animalcrossing/images/3/38/KK_Sonata_Live.ogg/revision/latest?cb=20160529173914" type="application/ogg">
                  </audio>
                </div>
                <div class="animals-info-small">目前僅限使用電腦Chrome瀏覽器試聽</div>
              </div>
            </div>
          </div>
        </div>
        <div class="media-card">
          <div class="media-card-title">室內裝修</div>
          <img src="image/home/茶茶丸.jpg" alt="茶茶丸">
        </div>
        <div class="media-card">
          <div class="media-card-title">友情獎勵</div>
          <ul class="media-list">
            <li>在達到7級友誼時，他將為您獎勵彩虹T恤和閃閃發光的寶石(x1)</li>
            <li>在9級時，他會用閃閃發光的寶石(x1)獎勵您</li>
            <li>在15級，他將要求您製作踢腳踏板車</li>
            <li>在20級時，他將為您提供茶茶丸和閃閃發光的寶石(x1)的照片</li>
            <li>在25級時，他會用閃閃發光的寶石(x1)獎勵您</li>
            <li>在30級時，他會用閃閃發光的寶石(x1)獎勵您</li>
            <li>在35級時，他會用閃閃發光的寶石(x1)獎勵您</li>
            <li>在40級時，他會用閃閃發光的寶石(x1)獎勵您</li>
            <li>在45級時，他會用閃閃發光的寶石(x1)獎勵您</li>
          </ul>
        </div>
      </div>
      <div class="media-footer">
        <ul>
          <!-- <li><a href="">加入最愛</a></li> -->
        </ul>
      </div>
    </div>
  </section>
</article>
@endsection
@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/animals/list">動物居民</a>
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
                <label style="width: 100px;">最喜歡的歌曲</label>
                <span>{{ $detail->kk }}</span>
                <div class="animals-info-audio">
                  <audio controls name="media">
                    <source src="/animal/kk/{{ $detail->kk }}" type="application/ogg">
                  </audio>
                </div>
                <div class="animals-info-small">目前僅限使用電腦Chrome瀏覽器試聽</div>
              </div>
            </div>
          </div>
        </div>
        <div class="media-card">
          <div class="media-card-title">室內裝修</div>
          <img src="/animal/{{ $detail->name }}_home.png" alt="{{ $detail->name }}家">
        </div>
        <div class="media-card">
          <div class="media-card-title">Amiibo Card</div>
          <img src="/animal/card/{{ $detail->amiibo }}.png" alt="{{ $detail->name }}卡">
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
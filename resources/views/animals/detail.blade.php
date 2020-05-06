@extends('layouts.web')
@section('title', $detail->name . ' 圖鑑')
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
        @if($detail->info != '')
        <div class="media-card">
          <div class="media-card-title">NPC說明</div>
          <div class="media-text">
            {{ $detail->info }}
          </div>
        </div>
        @endif
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
                <span>{{ $detail->motto }}</span>
              </div>
            </div>
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>目標</label>
                <span>{{ $detail->target }}</span>
              </div>
            </div>
            @if($detail->kk != '')
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label style="width: 100px;">最喜歡的歌曲</label>
                <span>{{ $detail->kk }}</span>
                <div class="animals-info-audio">
                  <audio controls name="media">
                    <source src="/animal/kk/{{ $detail->kk }}.mp3" type="audio/mpeg">
                  </audio>
                </div>
              </div>
            </div>
            @endif
            @if($detail->name == 'KK')
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label style="width: 100px;">最喜歡的歌曲</label>
                <span>每一首都喜歡 (￣▽￣)~*</span>
              </div>
            </div>
            @endif
          </div>
        </div>
        @if($detail->info == '')
        <div class="media-card">
          <div class="media-card-title">室內裝修</div>
          <a href="/animal/{{ $detail->name }}_home.png" data-lightbox="{{ $detail->name }}家" data-title="{{ $detail->name }}家">
            <img src="/animal/{{ $detail->name }}_home.png" alt="{{ $detail->name }}家">
          </a>
        </div>
        @endif
        @if($detail->amiibo != '')
        <div class="media-card">
          <div class="media-card-title">Amiibo Card</div>
          <a href="/animal/card/{{ $detail->amiibo }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
            <img src="/animal/card/{{ $detail->amiibo }}.png" alt="{{ $detail->name }}卡">
          </a>
        </div>
        @endif
        @if(!empty($sameRaceArray))
        <div class="media-card">
          <div class="media-card-title">{{ $detail->name }}的族人</div>
          <ul class="media-card-list">
            @foreach($sameRaceArray as $animal)
            <li>
              <a href="/animals/detail?name={{ $animal->name }}">
                <span>{{ $animal->name }}</span>
                <div class="table-img">
                  @if($animal->info == '')
                    <img src="/animal/{{ $animal->name }}_icon.png" alt="{{ $animal->name }}">
                  @else
                    <img src="/animal/{{ $animal->name }}.png" alt="{{ $animal->name }}">
                  @endif
                </div>
              </a>
            </li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>
    </div>
  </section>
</article>
@endsection
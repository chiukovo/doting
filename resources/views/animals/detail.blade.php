@extends('layouts.web')
@section('title', $detail->name . '圖鑑')
@section('content')
<div class="content-wrap">
  <div class="container">
    <h2 class="content-title">動物居民</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/animals/list">動物居民</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $detail->name }}</li>
      </ol>
    </nav>
    <section class="post">
      <div class="row justify-content-md-center">
        <div class="col col-lg-6">
          <div class="post-header fixed">
            <div class="post-card">
              <h2 class="post-title">{{ $detail->name }}</h2>
              <div class="post-photo">
                <img src="/animal/{{ $detail->name }}.png" alt="{{ $detail->name }}">
              </div>
            </div>
          </div>
          <div class="post-body">
            <div class="post-card">
              <div class="post-card-info">
                <div class="post-info-group">
                  <div class="post-info-item">
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
                <div class="post-info-group">
                  <div class="post-info-item">
                    <label>種族</label>
                    <span>{{ $detail->race }}</span>
                  </div>
                  <div class="post-info-item">
                    <label>個性</label>
                    <span>{{ $detail->personality }}</span>
                  </div>
                </div>
                <div class="post-info-group">
                  <div class="post-info-item">
                    <label>性別</label>
                    <span>{{ $detail->sex }}</span>
                  </div>
                  <div class="post-info-item">
                    <label>生日</label>
                    <span>{{ $detail->bd }}</span>
                  </div>
                </div>
                <div class="post-info-group">
                  <div class="post-info-item">
                    <label>口頭禪</label>
                    <span>{{ $detail->say }}</span>
                  </div>
                </div>
                <div class="post-info-group">
                  <div class="post-info-item">
                    <label>座右銘</label>
                    <span>{{ $detail->motto }}</span>
                  </div>
                </div>
                <div class="post-info-group">
                  <div class="post-info-item">
                    <label>目標</label>
                    <span>{{ $detail->target }}</span>
                  </div>
                </div>
                <div class="post-info-group">
                  <div class="post-info-item">
                    <label style="width: 100px;">最喜歡的歌曲</label>
                    <span>{{ $detail->kk }}</span>
                    <div class="post-info-audio">
                      <audio controls="" name="media">
                        <source src="/animal/kk/{{ $detail->kk }}.mp3" type="audio/mpeg">
                      </audio>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="post-card">
              <div class="post-card-title">{{ $detail->name }}的家</div>
              <div class="post-card-img">
                <a href="/animal/house/{{ $detail->name }}.png" data-lightbox="{{ $detail->name }}家" data-title="{{ $detail->name }}家">
                  <img src="/animal/house/{{ $detail->name }}.png" alt="{{ $detail->name }}家">
                </a>
              </div>
            </div>
            <div class="post-card">
              <div class="post-card-title">{{ $detail->name }}的室內裝修</div>
              <div class="post-card-img">
                <a href="/animal/{{ $detail->name }}_home.png" data-lightbox="{{ $detail->name }}家" data-title="{{ $detail->name }}家">
                  <img src="/animal/{{ $detail->name }}_home.png" alt="{{ $detail->name }}家">
                </a>
              </div>
            </div>
            @if($detail->amiibo != '')
            <div class="post-card">
              <div class="post-card-title">Amiibo Card</div>
              <a href="/animal/card/{{ $detail->amiibo }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
                <img src="/animal/card/{{ $detail->amiibo }}.png" alt="{{ $detail->name }}卡">
              </a>
            </div>
            @endif
            @if(!empty($sameRaceArray))
            <div class="post-card">
              <div class="post-card-title">{{ $detail->name }}的族人</div>
              <ul class="post-card-list">
                @foreach($sameRaceArray as $animal)
                <li>
                  <a href="/animals/detail?name={{ $animal->name }}">
                    <span>{{ $animal->name }}</span>
                    <div class="table-img">
                      @if($animal->info == '')
                        <img src="/animal/icon/{{ $animal->name }}.png" alt="{{ $animal->name }}">
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
      </div>
    </section>
  </div>
</div>
@endsection
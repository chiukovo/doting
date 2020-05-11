@extends('layouts.web')
@section('title', $detail->name)
@section('content')
<div class="content-wrap">
  <div class="container">
    @if($detail->info != '')
    <h2 class="content-title">動物NPC</h2>
    @else
    <h2 class="content-title">動物居民</h2>
    @endif
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        @if($detail->info != '')
        <li class="breadcrumb-item"><a href="/npc/list">動物NPC</a></li>
        @else
        <li class="breadcrumb-item"><a href="/animals/list">動物居民</a></li>
        @endif
        <li class="breadcrumb-item active" aria-current="page">{{ $detail->name }}</li>
      </ol>
    </nav>
    <section class="post">
      <div class="row justify-content-md-center">
        <div class="col col-lg-9">
          <div class="post-header fixed">
            <div class="post-card">
              <h2 class="post-title">{{ $detail->name }}</h2>
            </div>
            <div class="row my-4">
              <div class="col-12 col-md-4">
                <div class="post-photo post-animal">
                  <img class="img-fluid" src="/animal/{{ $detail->name }}.png" alt="{{ $detail->name }}">
                </div>
              </div>
              <div class="col-12 col-md-8">
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
                    @if($detail->info != '')
                    <div class="post-info-group">
                      <div class="post-info-item">
                        <label>用途</label>
                        <span>{{ $detail->info }}</span>
                      </div>
                    </div>
                    @endif
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
                    @if($detail->info == '')
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
                    @endif
                    @if($detail->kk != '')
                    <div class="post-info-group">
                      <div class="post-info-item">
                        <label style="width: 100px;">最喜歡的歌曲</label>
                        <span>{{ $detail->kk_cn_name }} / {{ $detail->kk }}</span>
                        <div class="post-info-audio">
                          <audio controls="" name="media">
                            <source src="/animal/kk/{{ $detail->kk }}.mp3" type="audio/mpeg">
                          </audio>
                        </div>
                      </div>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          @if($detail->info == '')
          <div class="post-body">
            <div class="card">
              <div class="card-header">{{ $detail->name }}的家</div>
              <div class="card-body text-center">
                <a href="/animal/house/{{ $detail->name }}.png" data-lightbox="{{ $detail->name }}家" data-title="{{ $detail->name }}家">
                  <img class="img-fluid" src="/animal/house/{{ $detail->name }}.png" alt="{{ $detail->name }}家" style="width: 300px">
                </a>
              </div>
            </div>
            <div class="card">
              <div class="card-header">{{ $detail->name }}的室內裝修</div>
              <div class="card-body text-center">
                <a href="/animal/{{ $detail->name }}_home.png" data-lightbox="{{ $detail->name }}家" data-title="{{ $detail->name }}家">
                  <img class="img-fluid" src="/animal/{{ $detail->name }}_home.png" alt="{{ $detail->name }}家">
                </a>
              </div>
            </div>
            @endif
            @if($detail->amiibo != '')
            <div class="card">
              <div class="card-header">Amiibo Card</div>
              <div class="card-body text-center">
                <a href="/animal/card/{{ $detail->amiibo }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
                  <img class="img-fluid" src="/animal/card/{{ $detail->amiibo }}.png" alt="{{ $detail->name }}卡">
                </a>
              </div>
            </div>
            @endif
            @if(!empty($sameRaceArray))
            <div class="card">
              <div class="card-header">{{ $detail->name }}的族人</div>
              <ul class="post-card-list animal-list">
                @foreach($sameRaceArray as $animal)
                <li>
                  <a href="/animals/detail?name={{ $animal->name }}">
                    <span>{{ $animal->name }}</span>
                    <div class="table-img">
                      @if($animal->info == '')
                        <img class="img-fluid" src="/animal/icon/{{ $animal->name }}.png" alt="{{ $animal->name }}">
                      @else
                        <img class="img-fluid" src="/animal/{{ $animal->name }}.png" alt="{{ $animal->name }}">
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
@extends('layouts.web')
@section('title', $detail->name)
@section('content')
<div class="content-wrap">
  <div class="container">
    <h2 class="content-title">{{ $detail->name }}</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/museum/list">博物館</a></li>
        <li class="breadcrumb-item"><a href="/art/list">藝術品圖鑑</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $detail->name }}</li>
      </ol>
    </nav>
    <section class="post">
      <div class="row justify-content-md-center">
        <div class="col col-lg-6">
          <div class="post-header fixed">
            <div class="post-card">
              <h2 class="post-title">{{ $detail->name }}</h2>
            </div>
          </div>
          <div class="post-body">
            <div id="user-save" class="user-save">
              <div class="user-save-wrap">
                <a onclick="history.go(-1)" class="btn-back"></a>
                <ul class="user-save-btn">
                  <li onclick="toggleLike('track')">
                    @if($detail->track)
                      <button id="track" class="btn btn-outline-danger current"><i class="fas fa-bookmark"></i>已追蹤</button>
                    @else
                      <button id="track" class="btn btn-outline-danger"><i class="fas fa-bookmark"></i>追蹤</button>
                    @endif
                  </li>
                  <li onclick="toggleLike('like')">
                    @if($detail->like)
                      <button id="like" class="btn btn-outline-success current"><i class="fas fa-heart"></i>已捐贈</button>
                    @else
                      <button id="like" class="btn btn-outline-success"><i class="fas fa-heart"></i>捐贈</button>
                    @endif
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header">{{ $detail->name }}介紹</div>
              <div class="card-body">
                <span>{{ $detail->info }}</span>
              </div>
            </div>
            @include('layouts.ads3')
            @if($detail->img1 != '')
            <div class="card">
              <div class="card-header">圖片1</div>
              <div class="card-body text-center">
                <a href="/art/{{ $detail->img1 }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
                  <img class="img-fluid" src="/art/{{ $detail->img1 }}.png" alt="{{ $detail->name }}">
                </a>
              </div>
            </div>
            @endif
            @if($detail->img2 != '')
            <div class="post-card">
              <div class="card-header">圖片2</div>
              <div class="card-body text-center">
                <a href="/art/{{ $detail->img2 }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
                  <img class="img-fluid" src="/art/{{ $detail->img2 }}.png" alt="{{ $detail->name }}">
                </a>
              </div>
            </div>
            @endif
            @if($detail->img3 != '')
            <div class="post-card">
              <div class="card-header">圖片3</div>
              <div class="card-body text-center">
                <a href="/art/{{ $detail->img3 }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
                  <img class="img-fluid" src="/art/{{ $detail->img3 }}.png" alt="{{ $detail->name }}">
                </a>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </div>
  @include('layouts.modal')
</div>
<script>
  function toggleLike(target) {
    axios.post('/toggleLike', {
       likeType: "{{ $type }}",
       type: "{{ $type }}",
       likeTarget: target,
       token: "{{ $token }}",
     }).then((response) => {
      const result = response.data
      if (result.code == -1) {
        $('#lineLoginModel').modal()
      }

      //success
      if (result.code == 1) {
        $('#' + target).toggleClass('current')

        let message
        let prex = ''

        if (!$('#' + target).hasClass("current")) {
          prex = '取消'
        }

        if (target == 'track') {
          message = '已' + prex + '追蹤'
        } else if (target == 'like') {
          message = '已' + prex + '捐贈'
        }

        $('#hint-message .message').text(message)
        $('#hint-message').addClass('show')

        window.setTimeout(( () => $('#hint-message').removeClass('show') ), 1000)
      }
     })
  }
</script>
@endsection
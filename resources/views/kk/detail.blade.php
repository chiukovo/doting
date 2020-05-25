@extends('layouts.web')
@section('title', $detail->cn_name . ' 唱片')
@section('content')
<div class="content-wrap">
  <div class="container">
    <h2 class="content-title">{{ $detail->cn_name }}唱片</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/kk/list">唱片</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $detail->cn_name }}唱片</li>
      </ol>
    </nav>
    <section class="post">
      <div class="row justify-content-md-center">
        <div class="col col-lg-6">
          <div class="post-header fixed">
            <div class="post-card">
              <h2 class="post-title">{{ $detail->cn_name }} / {{ $detail->name }}</h2>
              <div class="post-photo">
                <img class="img-fluid" src="/kk/{{ $detail->img_name }}.png" alt="{{ $detail->cn_name }}">
              </div>
            </div>
            <div id="user-save" class="user-save">
              <div class="user-save-wrap">
                <a onclick="history.go(-1)" class="btn-back"></a>
                <ul class="user-save-btn">
                  <li onclick="toggleLike('track')">
                    @if($detail->track)
                      <button id="track" class="btn btn-outline-danger current"><i class="fas fa-bookmark"></i>已追蹤</button>
                    @else
                      <button id="track" class="btn btn-outline-danger"><i class="fas fa-bookmark"></i>已追蹤</button>
                    @endif
                  </li>
                  <li onclick="toggleLike('like')">
                    @if($detail->like)
                      <button id="like" class="btn btn-outline-success current"><i class="fas fa-heart"></i>擁有</button>
                    @else
                      <button id="like" class="btn btn-outline-success"><i class="fas fa-heart"></i>擁有</button>
                    @endif
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="post-body">
            <div class="card">
              <div class="card-header">聽歌</div>
              <div class="card-body">
                <audio controls name="media">
                  <source src="/animal/kk/{{ $detail->file_name }}.mp3" type="audio/mpeg">
                </audio>
              </div>
            </div>
            @include('layouts.ads3')
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
       likeType: 'kk',
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
      }
     })
  }
</script>
@endsection
@extends('layouts.web')
@section('title', $detail->cn_name . ' 唱片')
@section('content')
<div class="content-wrap">
  <div class="container">
    <h2 class="content-title">{{ $detail->cn_name }}唱片</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
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
            @include('layouts.ads')
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
@endsection
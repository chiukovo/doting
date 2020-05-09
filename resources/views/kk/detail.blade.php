@extends('layouts.web')
@section('title', $detail->name . ' 唱片')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/kk/list">唱片圖鑑</a>
  <span class="sep">/</span>
  <a href="#">{{ $detail->name }}</a>
</div>
<article>
  <section class="animals-info">
    <h1 class="media-title">{{ $detail->cn_name }} / {{ $detail->name }}</h1>
    <div class="media">
      <div class="media-photo">
        <img src="/kk/{{ $detail->img_name }}.png" alt="{{ $detail->name }}">
      </div>
      <div class="media-body">
        <div class="media-card">
          <div class="media-card-title">聽歌</div>
          <div style="text-align: center;">
            <audio controls name="media">
              <source src="/animal/kk/{{ $detail->file_name }}.mp3" type="audio/mpeg">
            </audio>
          </div>
        </div>
      </div>
    </div>
  </section>
</article>
@endsection
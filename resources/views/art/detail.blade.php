@extends('layouts.web')
@section('title', $detail->name)
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/museum/list">博物館</a>
  <span class="sep">/</span>
  <a href="/art/list">藝術品圖鑑</a>
  <span class="sep">/</span>
  <a href="#">{{ $detail->name }}</a>
</div>
<article>
  <section class="animals-info">
    <h1 class="media-title">{{ $detail->name }}</h1>
    <div class="media">
      <div class="media-body">
        <div class="media-card">
          <div class="animals-info-list">
            <div class="animals-info-group">
              <div class="animals-info-item">
                <label>介紹</label>
                <span>
                  {{ $detail->info }}
                </span>
              </div>
            </div>
          </div>
        </div>
        @if($detail->img1 != '')
        <div class="media-card art-card">
          <div class="media-card-title">圖片一</div>
          <a href="/art/{{ $detail->img1 }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
            <img src="/art/{{ $detail->img1 }}.png" alt="{{ $detail->name }}">
          </a>
        </div>
        @endif
        @if($detail->img2 != '')
        <div class="media-card art-card">
          <div class="media-card-title">圖片二</div>
          <a href="/art/{{ $detail->img2 }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
            <img src="/art/{{ $detail->img2 }}.png" alt="{{ $detail->name }}">
          </a>
        </div>
        @endif
        @if($detail->img3 != '')
        <div class="media-card art-card">
          <div class="media-card-title">圖片三</div>
          <a href="/art/{{ $detail->img3 }}.png" data-lightbox="{{ $detail->name }}" data-title="{{ $detail->name }}">
            <img src="/art/{{ $detail->img3 }}.png" alt="{{ $detail->name }}">
          </a>
        </div>
        @endif
      </div>
    </div>
  </section>
</article>
@endsection
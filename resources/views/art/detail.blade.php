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
            <div class="card">
              <div class="card-header">{{ $detail->name }}介紹</div>
              <div class="card-body">
                <span>{{ $detail->info }}</span>
              </div>
            </div>
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
</div>
@endsection
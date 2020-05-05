@extends('layouts.web')
@section('title', '搜尋統計')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/statistics">搜尋統計</a>
</div>
<div class="media">
  <table class="media-card table">
    <tr>
      <th>排名</th>
      <th>搜尋名稱</th>
      <th>次數</th>
    </tr>
    @foreach($lists as $key => $list)
    <tr>
      <td>{{ $key + 1 }}</td>
      <td>
        @if($list['url'] != '')
          <a href="{{ $list['url'] }}" target="_blank">
            <span>{{$list['text'] }}</span>
            <div class="table-img">
              <img src="{{ $list['img'] }}" alt="{{ $list['text'] }}">
            </div>
          </a>
        @else
            <span>{{$list['text'] }}</span>
        @endif
      </td>
      <td>{{ $list['number'] }}</td>
    </tr>
    @endforeach
  </table>
  <div class="media-card">
    <div class="media-card-title">保護個資說明</div>
    <ul class="media-list">
      <li>僅收集豆丁有搜尋出來的資料來做統計</li>
      <li><span style="color: red">私人訊息等資訊, 豆丁並不會紀錄 請放心</span></li>
    </ul>
  </div>
</div>
@endsection
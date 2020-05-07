@extends('layouts.web')
@section('title', '動物相容性分析')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/animals/analysis">動物相容性分析</a>
</div>
<article>
  <section class="animals-info">
    <h1 class="media-title">總分數: {{ $lists['resultSum'] }} 診斷結果為: {{ $lists['resultScore'] }}</h1>
    <div class="media">
      <div class="media-body">
        <div class="media-card">
          <div class="media-card-title">分析</div>
          <div class="media-text">
            <table>
              <tr>
                <th></th>
                @foreach($lists['data'] as $key => $list)
                  <th>{{ $list->race }}</th>
                @endforeach
              </tr>
              <tr>
                <th></th>
                @foreach($lists['data'] as $key => $list)
                  <th>{{ $list->name }}</th>
                @endforeach
              </tr>
              @foreach($lists['data'] as $list)
              <tr>
                <td>{{ $list->name }} {{ $list->totalSum }} {{ $list->score }}</td>
                @foreach($list->detail as $detail)
                  @if($list->name == $detail['name'])
                    <td></td>
                  @else
                    <td>
                      <div>{{ $detail['sum'] }}</div>
                      <div>{{ $detail['perScore'] }}, {{ $detail['matchScore'] }}, {{ $detail['raceScore'] }}</div>
                    </td>
                  @endif
                @endforeach
              </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</article>
@endsection
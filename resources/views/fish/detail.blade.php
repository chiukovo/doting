@extends('layouts.web')
@section('title', $detail['name'])
@section('content')
<div class="content-wrap">
  <div class="container">
    <h2 class="content-title">{{ $detail['name'] }}</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/fish/list">魚圖鑑</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $detail['name'] }}</li>
      </ol>
    </nav>
    <section class="post">
      <div class="row justify-content-md-center">
        <div class="col col-lg-9">
          <div class="post-header">
            <div class="post-card">
              <h2 class="post-title">{{ $detail['name'] }}</h2>
              <div class="post-photo">
                <img src="/other/{{ $detail['name'] }}.png" alt="{{ $detail['name'] }}">
              </div>
            </div>
          </div>
          <div class="post-body">
            <div id="user-save" class="user-save">
              <div class="user-save-wrap">
                <a href="#" class="btn-back"></a>
                <ul class="user-save-btn">
                  <li onclick="toggleLike('track')">
                    @if($detail['track'])
                      <button id="track" class="btn btn-outline-danger current"><i class="fas fa-bookmark"></i>已追蹤</button>
                    @else
                      <button id="track" class="btn btn-outline-danger"><i class="fas fa-bookmark"></i>追蹤</button>
                    @endif
                  </li>
                  <li onclick="toggleLike('like')">
                    @if($detail['like'])
                      <button id="like" class="btn btn-outline-success current"><i class="fas fa-heart"></i>已捐贈</button>
                    @else
                      <button id="like" class="btn btn-outline-success"><i class="fas fa-heart"></i>捐贈</button>
                    @endif
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header">季節性</div>
              <div class="card-group">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-center">北半球</h5>
                    <ul class="list-year">
                      @foreach($months as $month)
                      <li class="{{ $detail['n_' . $month . '_class'] }}">
                        <div class="list-year-item"><span>{{ $month }}月</span></div>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-center">南半球</h5>
                    <!-- 能捕捉月份 li+class "has" -->
                    <!-- 當前月份 li+class "current" -->
                    <ul class="list-year">
                      @foreach($months as $month)
                      <li class="{{ $detail['s_' . $month . '_class'] }}">
                        <div class="list-year-item"><span>{{ $month }}月</span></div>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">當前活動時間</div>
              <div class="card-body">
                <p>時間： {{ $detail['time'] }}</p>
                <!-- 能捕捉時間 li+class "has" -->
                <!-- 當前時間 li+class "current" -->
                <div class="list-time-wrap">
                  <ul class="list-time">
                    @foreach($dateRange1 as $time)
                      @if($time == 0)
                      <li class="{{ checkTimeClass($detail['time'], $time) }}">
                        <div class="list-time-title">
                          <span>AM</span>
                          <span>0</span>
                        </div>
                        <div class="list-time-item"></div>
                      </li>
                      @elseif($time == 6)
                      <li class="{{ checkTimeClass($detail['time'], $time) }}">
                        <div class="list-time-title">
                          <span>6</span>
                        </div>
                        <div class="list-time-item"></div>
                      </li>
                      @else
                      <li class="{{ checkTimeClass($detail['time'], $time) }}">
                        <div class="list-time-item"></div>
                      </li>
                      @endif
                    @endforeach
                  </ul>
                  <ul class="list-time">
                    @foreach($dateRange2 as $time)
                      @if($time == 12)
                      <li class="{{ checkTimeClass($detail['time'], $time) }}">
                        <div class="list-time-title">
                          <span>PM</span>
                          <span>12</span>
                        </div>
                        <div class="list-time-item"></div>
                      </li>
                      @elseif($time == 18)
                      <li class="{{ checkTimeClass($detail['time'], $time) }}">
                        <div class="list-time-title">
                          <span>18</span>
                        </div>
                        <div class="list-time-item"></div>
                      </li>
                      @else
                      <li class="{{ checkTimeClass($detail['time'], $time) }}">
                        <div class="list-time-item"></div>
                      </li>
                      @endif
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
            @include('layouts.ads3')
            <div class="card">
              <div class="card-header">其他資訊</div>
              <div class="card-body">
                <ul class="list-group list-group-flush list-custom">
                  <li class="list-group-item">
                    <label class="list-custom-label">位置</label>
                    <span>{{ $detail['position'] }}</span>
                  </li>
                  <li class="list-group-item">
                    <label class="list-custom-label">價錢</label>
                    <span>${{ $detail['sell'] }}</span>
                  </li>
                  <li class="list-group-item">
                    <label class="list-custom-label">陰影</label>
                    <span>{{ $detail['shadow'] }}</span>
                  </li>
                </ul>
              </div>
            </div>
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
       likeType: 'fish',
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
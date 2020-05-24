<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <title>豆丁森友會 - 動物森友會 @yield('title')</title>
  <meta name="description" content="動物森友會 豆丁森友會 動物圖鑑 家具 服飾 相容性分析" />
  <link rel="stylesheet" href="/css/style.css?v={{ config('app.version') }}">
  <link rel="stylesheet" href="/css/lightbox.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
  <script src="/js/vue.min.js"></script>
  <script src="/js/axios.min.js"></script>
  <script src="/js/vue-infinite-loading.js"></script>
  <script src="/js/vue-go-top.min.js"></script>
  <script src="/js/jquery-2.2.4.min.js"></script>
  <script src="/js/lightbox.min.js"></script>
  <script src="/js/popper.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
  <script src="/js/clipboard.min.js"></script>
  <script data-ad-client="ca-pub-2560043137442562" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <style>
    [v-cloak] {
      display: none;
    }
    #birthday {
      position: absolute;
      z-index: -1
    }
    .infinite-loading-container {
      min-height: 360px;
    }
  </style>
</head>
<body>
  @php
    $routeName = request()->route()->getName();
    $name = $routeName == 'analysis' ? 'analysis-fixed' : '';
    $isLogin = isWebLogin();
  @endphp
  <div id="root" class="{{ $name }}">
    <header id="header" class="header">
      <div id="donateMe">
        <a href="/donate">贊助豆丁，豆丁要活下去*.。(๑･∀･๑)*.。</a>
      </div>
      <div class="header-wrap">
        <a href="/" class="logo">
          <h1>豆丁森友會 Doting Animal crossing</h1>
          <h2>動物森友會</h2>
          <!-- <img src="../image/logo.png" alt="豆丁森友會"> -->
        </a>
        @if($isLogin)
        <div class="sub-login btn-user">
          <div class="btn-group">
            <div class="sub-login-img-wrap">
              <a href="/user">
                <div class="sub-login-img" style="background-image: url('{{ getUserData('pictureUrl') }}');"></div>
              </a>
            </div>
            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ getUserData('displayName') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="/user">收藏資訊</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/logout">登出</a>
            </div>
          </div>
        </div>
        @endif
        <button class="btn btn-nav" @click="menuShow = !menuShow"><ion-icon name="menu-outline"></ion-icon></button>
        <!-- <button class="btn btn-user"><ion-icon name="person-outline"></ion-icon></button> -->
        <div class="navigation-wrap" :class="menuShow ? 'show' : ''">
          <nav class="navigation">
            <ul>
              <li class="sub-nav">
                <a href="#">小動物</a>
                <ul>
                  <li><a href="/animals/list">動物居民</a></li>
                  <li><a href="/npc/list">動物NPC</a></li>
                  <li><a href="/animals/compatible">動物相容性分析</a></li>
                </ul>
              </li>
              <li class="sub-nav">
                <a href="/museum/list">博物館</a>
                <ul>
                  <li><a href="/fish/list">魚圖鑑</a></li>
                  <li><a href="/insect/list">昆蟲圖鑑</a></li>
                  <li><a href="/fossil/list">化石圖鑑</a></li>
                  <li><a href="/art/list">藝術品</a></li>
                </ul>
              </li>
              <li class="sub-nav">
                <a href="#">收藏</a>
                <ul>
                  <li><a href="/diy/list">DIY方程式</a></li>
                  <li><a href="/apparel/list">家具</a></li>
                  <li><a href="/furniture/list">服飾</a></li>
                  <li><a href="/plant/list">植物</a></li>
                  <li><a href="/kk/list">唱片</a></li>
                </ul>
              </li>
              <li class="sub-nav">
                <a href="#">豆丁</a>
                <ul>
                  <li><a href="/instructions">豆丁教學</a></li>
                  <li><a href="/update/version">更新資訊</a></li>
                  <li><a href="https://forms.gle/Q7StMmonyGdL4rCFA" target="_blank">意見回饋</a></li>
                  <li><a href="https://reurl.cc/9ER9ya" target="_blank">意見回饋(回應)</a></li>
                </ul>
              </li>
              <li><a href="/statistics">搜尋排行榜</a></li>
              @if(!$isLogin)
                <li><a href="{{ lingLoginUrl() }}">Sign In <ion-icon name="arrow-forward-outline"></ion-icon></a></li>
              @else
                <li class="sub-login">
                  <div class="btn-group">
                    <div class="sub-login-img-wrap">
                      <a href="/user">
                        <div class="sub-login-img" style="background-image: url('{{ getUserData('pictureUrl') }}');"></div>
                      </a>
                    </div>
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      {{ getUserData('displayName') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item" href="/user">收藏資訊</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="/logout">登出</a>
                    </div>
                  </div>
                </li>
              @endif
            </ul>
          </nav>
        </div>
      </div>
    </header>
    <div class="content">
      @yield('content')
    </div>
    <footer class="footer">
      <div class="copyright">
        copyright © 豆丁森友會 - LINE ID: @875uxytu
      </div>
    </footer>
  </div>
</body>
<script>
  new Vue({
    el: '#header',
    data: {
      menuShow: false
    },
    watch: {
      menuShow(show) {
        if (show) {
          $('#html').addClass('hidden')
        } else {
          $('#html').removeClass('hidden')
        }
      }
    }
  })
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-136875596-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-136875596-3');
</script>
</html>
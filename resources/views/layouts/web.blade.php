<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <title>豆丁森友會</title>
  <link rel="stylesheet" href="/css/normalize.css">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/lightbox.min.css">
  <script src="/js/vue.min.js"></script>
  <script src="/js/axios.min.js"></script>
  <script src="/js/vue-infinite-loading.js"></script>
  <script src="/js/vue-go-top.min.js"></script>
  <script src="/js/jquery-2.2.4.min.js"></script>
  <script src="/js/lightbox.min.js"></script>
</head>
<body>
  <div id="root">
    <div id="out">
      <header class="header">
        <div class="logo">
          <a href="/"><img src="/image/logo.png" alt="動物森友會"></a>
        </div>
        <div class="header-right">
          <button @click="menuShow = !menuShow" class="btn-menu" :class="menuShow ? 'current' : ''">
            <div class="menu-icon"></div>
          </button>
        </div>
      </header>
      <div id="menu" class="menu" v-if="menuShow">
        <div class="menu-list">
          <ul>
            <li><a href="/instructions">豆丁指令</a></li>
            <li><a href="/update/version">更新資訊</a></li>
            <li><a href="https://forms.gle/Q7StMmonyGdL4rCFA" target="_blank">意見回饋</a></li>
            <li><a href="/animals/list">動物居民</a></li>
            <li><a href="/npc/list">動物NPC</a></li>
            <li><a href="/museum/list">博物館</a></li>
            <li><a href="/diy/list">DIY方程式</a></li>
            <li><a href="/furniture/list">家具</a></li>
            <li><a href="/apparel/list">服飾</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container">
      @yield('content')
    </div>
    <footer class="footer">copyright © doting</footer>
  </div>
</body>
<script>
  new Vue({
    el: '#out',
    data: {
      menuShow: false
    },
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
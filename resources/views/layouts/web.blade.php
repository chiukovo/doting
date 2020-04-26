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
        <div class="help">
          <a href="#">問題回報</a>
        </div>
        <button @click="menuShow = !menuShow">toggle</button>
      </header>
      <div id="menu" class="menu" v-if="menuShow"></div>
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>豆丁森友會</title>
  <link rel="stylesheet" href="/css/normalize.css">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <div id="root">
    <header class="header">
      <div class="logo">
        <img src="/image/logo.png" alt="動物森友會">
      </div>
      <div class="help">
        <a href="#">問題回報</a>
      </div>
    </header>
    <div class="container">
      @yield('content')
    </div>
    <footer class="footer">copyright © doting</footer>
  </div>
</body>
</html>
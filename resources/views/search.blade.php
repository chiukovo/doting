<html>
  <head>
    <title>doting</title>
  </head>
  <body>
    <div id="result"></div>
    <script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
    <script>
      window.onload = function (e) {
        liff
          .init({
              liffId: '1654040846-322E0rPo'
          })
          .then(() => {
            // start to use LIFF's api
            initializeApp();
          })
      };

      function initializeApp() {
          var h = document.getElementById('result');
          h.innerHTML = 'success!!';
      }
    </script>
  </body>
</html>
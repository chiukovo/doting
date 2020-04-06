<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>doting</title>
  </head>
  <body>
    <div id="result"></div>
    <script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
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

        //debug
        vConsole = new VConsole();
      };

      function initializeApp()
      {
          var h = document.getElementById('result');
          h.innerHTML = 'success!!';

          console.log('is success')
      }
    </script>
  </body>
</html>
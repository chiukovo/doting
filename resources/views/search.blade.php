<html>
  <head>
  </head>
  <body>
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

        liff.openWindow({
          url: 'https://line.me',
          external: true
        });
      };
    </script>
  </body>
</html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>doting</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
  </head>
  <body>
    <div id="app" class="col-12">
      <form>
        <div class="form-group">
          <label for="zip">縣市: </label>
          <select class="form-control" v-model="city">
            <option value="台北市">台北市</option>
            <option value="台中市">台中市</option>
            <option value="高雄市">高雄市</option>
          </select>
        </div>
        <div class="form-group">
          <label for="zip">名稱: </label>
          <input type="text" class="form-control" v-model="kw" placeholder="請輸入社區名稱、路段或學校">
        </div>
        <button @click="doSearch" type="button" class="btn btn-primary btn-block">查詢</button>
      </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
    <script>
      new Vue({
        el: '#app',
        data: {
          city: '台北市',
          kw: '',
        },
        mounted() {
          const _this = this
          liff
            .init({
                liffId: '1654040846-322E0rPo'
            })
            .then(() => {
              // start to use LIFF's api
             _this.initializeApp();
            })

          //debug
          vConsole = new VConsole();
        },
        methods: {
          initializeApp() {
            console.log('is success')
          },
          doSearch() {
            $.ajax({
              url: "/getApi",
              data: {
                city: this.city,
                kw: this.kw,
              },
              success: function(response) {
                console.log(response)
              },
            });
          }
        }
      })
    </script>
  </body>
</html>
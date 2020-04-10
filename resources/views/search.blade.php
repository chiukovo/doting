<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>doting</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
  </head>
  <body>
    <div id="app" class="col-12" style="margin-top: 20px">
      <form>
        <div class="form-group">
          <select class="form-control" v-model="city">
            <option value="台北市">台北市</option>
            <option value="台中市">台中市</option>
            <option value="高雄市">高雄市</option>
          </select>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" v-model="kw" placeholder="請輸入社區名稱、路段或學校">
        </div>
        <button @click.prevent.stop="doSearch" type="button" class="btn btn-primary btn-block" :disabled="disabled">查詢</button>
      </form>
      <div class="card" v-for="list in lists" style="margin-bottom: 20px">
        <div class="card-body">
          <h5 class="card-title">@{{ list.date }} <span class="text-danger">@{{ list.price }}</span>萬 (<span class="text-danger">@{{ list.unit_price }}</span>/坪)</h5>
          <h6 class="card-subtitle mb-2 text-muted">@{{ list.name }}</h6>
          <h6 class="card-subtitle mb-2 text-muted">@{{ list.address }}</h6>
          <p>@{{ list.car_price }}</p>
          <p>@{{ list.age }}年</p>
          <p>樓別 @{{ list.f_start }}樓 共 @{{ list.f_end }}樓</p>
        </div>
      </div>
      <div class="text-center" v-if="disabled">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
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
          lists: [],
          disabled: false
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
            const _this = this
            this.disabled = true
            _this.lists = []

            $.ajax({
              url: "/getApi",
              data: {
                city: this.city,
                kw: this.kw,
              },
              success: function(response) {
                if (response.code == 200) {
                  _this.lists = response.data
                }

                _this.disabled = false
              },
            });
          }
        }
      })
    </script>
  </body>
</html>
@extends('layouts.web')
@section('title', '昆蟲圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">昆蟲圖鑑</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/museum/list">博物館</a></li>
        <li class="breadcrumb-item active" aria-current="page">昆蟲圖鑑</li>
      </ol>
    </nav>
    <section>
      <div class="section-search row">
        <div class="col">
          <div class="collapse show" id="collapseSearch">
            <table class="table table-bordered">
              <tr>
                <td class="text-center" width="80">查看全部</td>
                <td>
                  <button class="btn btn-search" :class="searchData.text == '' ? 'current' : ''" @click="clearAll">查看全部</button>
                </td>
              </tr>
              <tr>
                <th>類型</th>
                <td>
                  @include('layouts.museum-tabs')
                </td>
              </tr>
            </table>
          </div>
          <form>
            <div class="form-search">
              <input type="text" class="form-control" placeholder="請輸入關鍵字" v-model="searchData.text">
              <button class="btn btn-primary" native-type="submit" @click.prevent="searchDefault">搜尋</button>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <table class="table table-bordered table-hover text-center">
            <thead>
              <tr>
                <th scope="col">名稱</th>
                <th scope="col">位置</th>
                <th scope="col">時間</th>
                <th scope="col">南半球</th>
                <th scope="col">北半球</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td class="link" scope="row">
                  <a :href="'/other/' + list.name + '.png'" :data-lightbox="list.name" :data-title="list.name">
                    <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                    <div class="table-img">
                      <img :src="'/other/' + list.name + '.png'" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td>@{{ list.position }}</td>
                <td>@{{ list.time }}</td>
                <td>@{{ list.south }}</td>
                <td>@{{ list.north }}</td>
              </tr>
            </tbody>
          </table>
          <infinite-loading :identifier="infiniteId" @infinite="search">
            <div slot="no-more"></div>
            <div slot="no-results"></div>
          </infinite-loading>
        </div>
      </div>
    </section>
  </div>
  @include('layouts.goTop')
</div>

<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      lists: [],
      page: 1,
      infiniteId: +new Date(),
      searchData: {
        text: "{{ $text }}",
      }
    },
    mounted() {
    },
    methods: {
      formatPrice(money) {
        if (money == null) {
          return ''
        }

        return money.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      search($state) {
        axios.post('/insect/search', {
           page: this.page,
           text: this.searchData.text,
         }).then((response) => {
           if (response.data.length) {
             this.page += 1;
             this.lists.push(...response.data);
             $state.loaded();
           } else {
             $state.complete();
           }
         })
      },
      clearAll() {
        this.searchData = {
          text: '',
        }

        this.searchDefault()
      },
      searchDefault() {
        this.page = 1;
        this.lists = [];
        this.infiniteId += 1;
      }
    }
  })
</script>
@endsection
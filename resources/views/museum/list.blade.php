@extends('layouts.web')
@section('title', '博物館')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">博物館</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">博物館</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
          <div class="collapse show" id="collapseSearch">
            <table class="table table-bordered">
              <tr>
                <td class="text-center" width="80">查看全部</td>
                <td>
                  <button class="btn btn-search"@click.prevent="clearAll">查看全部</button>
                </td>
              </tr>
              <tr>
                <td>類型</td>
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
          <div class="row">
            <div class="col text-right mb-1">
              <button class="btn">全部: @{{ lists.length }} 個結果</button>
              <button class="btn btn-default" @click="isList = !isList"><i class="fas" :class="isList ? 'fa-list' : 'fa-grip-horizontal'"></i></button>
              <!-- table狀態顯示 fa-grip-horizontal
                  列表狀態顯示 fa-list
                -->
            </div>
          </div>
          <table class="table table-bordered table-hover text-center"  v-if="isList">
            <thead>
              <tr>
                <th class="table-label" scope="col">名稱</th>
                <th scope="col" v-show="!isMobile()">陰影</th>
                <th scope="col">位置</th>
                <th scope="col">時間</th>
                <th scope="col">南半球月份</th>
                <th scope="col">北半球月份</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td scope="row">
                  <a :href="'/fish/detail?name=' + list.name" class="link" v-if="list.shadow">
                    <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                    <div class="table-img">
                      <img :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                  <a :href="'/insect/detail?name=' + list.name" class="link" v-else>
                    <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                    <div class="table-img">
                      <img :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td v-show="!isMobile()">@{{ list.shadow }}</td>
                <td>@{{ list.position }}</td>
                <td>@{{ list.time }}</td>
                <td>@{{ list.south }}</td>
                <td>@{{ list.north }}</td>
              </tr>
            </tbody>
          </table>
          <!-- style: list -->
          <ul class="card-list" v-if="!isList">
            <li v-for="list in lists">
              <div class="card-list-item" @click="goDetail(list)">
                <div class="card-list-img">
                  <img class="img-fluid" :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }}</div>
                <div class="card-list-info">
                  <h5 class="text-danger font-weight-bold m-1">$@{{ formatPrice(list.sell) }}</h5>
                </div>
                <div class="card-list-info" v-if="list.shadow">
                  @{{ list.shadow }} / @{{ list.position }} / @{{ list.time }} 時
                </div>
                <div class="card-list-info" v-else>
                  @{{ list.position }} / @{{ list.time }} 時
                </div>
              </div>
            </li>
          </ul>
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
      isList: false,
      page: 1,
      version: "{{ config('app.version') }}",
      infiniteId: +new Date(),
      searchData: {
        text: "{{ $text }}",
      }
    },
    mounted() {
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      goDetail(list) {
        if (list.shadow) {
          location.href = '/fish/detail?name=' + list.name
        } else {
          location.href = '/insect/detail?name=' + list.name
        }
      },
      formatPrice(money) {
        if (money == null) {
          return ''
        }

        return money.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      search($state) {
        axios.post('/museum/search', {
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
@extends('layouts.web')
@section('title', '化石圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">化石圖鑑</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/museum/list">博物館</a></li>
        <li class="breadcrumb-item active" aria-current="page">化石圖鑑</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
          <div class="collapse show" id="collapseSearch">
            <table class="table table-bordered">
              <tr>
                <td class="text-center table-label">類型</td>
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
              <button class="btn btn-default" @click.prevent="clearAll">清除搜尋</button>
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
          <table class="table table-bordered table-hover text-center" v-if="isList">
            <thead>
              <tr>
                <th class="table-label" scope="col">名稱</th>
                <th scope="col">介紹</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td scope="row">
                  <a :href="'/fossil/' + list.img_name + '.png?v=' + version" :data-lightbox="list.name" :data-title="list.name" class="link">
                    <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                    <div class="table-img">
                      <img :src="'/fossil/' + list.img_name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td>@{{ list.info }}</td>
              </tr>
            </tbody>
          </table>
          <!-- style: list -->
          <ul class="card-list" v-if="!isList">
            <li v-for="list in lists">
              <div class="card-list-item">
                <a :href="'/fossil/' + list.img_name + '.png?v=' + version" :data-lightbox="list.name" :data-title="list.name" class="link">
                  <div class="card-list-img">
                    <img class="img-fluid" :src="'/fossil/' + list.name + '.png?v=' + version" :alt="list.name">
                  </div>
                </a>
                <div class="card-list-title">@{{ list.name }}</div>
                <div class="card-list-info">
                  <h5 class="text-danger font-weight-bold m-1">$@{{ formatPrice(list.sell) }}</h5>
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
      page: 1,
      version: "{{ config('app.version') }}",
      isList: false,
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
        axios.post('/fossil/search', {
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
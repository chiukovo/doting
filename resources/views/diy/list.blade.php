@extends('layouts.web')
@section('title', 'DIY圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">Diy圖鑑</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">DIY圖鑑</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
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
                <th scope="col">名稱</th>
                <th scope="col">類型</th>
                <th scope="col">取得</th>
                <th scope="col">Diy</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td class="link" scope="row">
                  <a :href="'/diy/' + list.name + '.png'" :data-lightbox="list.name" :data-title="list.name">
                    <span>@{{ list.name }}</span>
                    <div class="table-img">
                      <img :src="'/diy/' + list.name + '.png'" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td>@{{ list.type }}</td>
                <td>@{{ list.get }}</td>
                <td>@{{ list.diy }}</td>
              </tr>
            </tbody>
          </table>
          <!-- style: list -->
          <ul class="card-list" v-if="!isList">
            <li v-for="list in lists">
              <div class="card-list-item">
                <div class="card-list-img">
                  <img class="img-fluid" :src="'/diy/' + list.name + '.png?v=' + version" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }}</div>
                <div class="card-list-info">@{{ list.type }}</div>
                <div class="card-list-info">@{{ list.get }}</div>
                <div class="card-list-info">@{{ list.diy }}</div>
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
      search($state) {
        axios.post('/diy/search', {
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
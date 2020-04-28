@extends('layouts.web')
@section('title', '藝術品')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/museum/list">博物館</a>
  <span class="sep">/</span>
  <a href="/art/list">藝術品圖鑑</a>
</div>
<div id="app" class="media" v-cloak>
  <div class="search">
    <form>
      <table class="table">
        <tr>
          <th>類型</th>
          <td>
            @include('layouts.museum-tabs')
          </td>
        </tr>
        <tr>
          <th>搜尋</th>
          <td>
            <div class="form-search">
              <input type="text" class="input" v-model="searchData.text">
              <button native-type="submit" class="btn" @click.prevent="searchDefault">搜尋</button>
              <button class="btn" @click.prevent="clearAll">清除搜尋</button>
            </div>
          </td>
        </tr>
      </table>
    </form>
  </div>
  <table class="media-card table">
    <tr>
      <th width="100">名稱</th>
      <th>照片</th>
      <th>介紹</th>
    </tr>
    <tr v-for="list in lists">
      <td>
        <a :href="'/art/detail?name=' + list.name">
          <span>@{{ list.name }}</span>
        </a>
      </td>
      <td>
        <a :href="'/art/detail?name=' + list.name">
          <img :src="'/art/' + list.img1 + '.png'" :alt="list.name" v-if="list.img1 != ''">
        </a>
      </td>
      <td>@{{ list.info }}</td>
    </tr>
  </table>
  <infinite-loading :identifier="infiniteId" @infinite="search">
    <div slot="no-more"></div>
    <div slot="no-results"></div>
  </infinite-loading>
  @include('layouts.goodUrl')
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
      search($state) {
        axios.post('/art/search', {
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
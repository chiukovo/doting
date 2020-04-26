@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="#">首頁</a>
  <span class="sep">/</span>
  <a href="#">昆蟲圖鑑</a>
</div>
<div id="app" class="media" v-cloak>
  <div class="search">
    <form>
      <table class="table">
        <tr>
          <th>搜尋</th>
          <td>
            <input type="text" v-model="searchData.text">
            <button native-type="submit" @click.prevent="searchDefault">搜尋</button>
          </td>
        </tr>
      </table>
    </form>
  </div>
  <table class="media-card table">
    <tr>
      <th>名稱</th>
      <th>陰影</th>
      <th>位置</th>
      <th>時間</th>
      <th>南半球月份</th>
      <th>北半球月份</th>
    </tr>
    <tr v-for="list in lists">
      <td>
        <span>@{{ list.name }} $ @{{ list.sell }}</span>
        <div class="table-img">
          <img :src="'/other/' + list.name + '.png'" :alt="list.name">
        </div>
      </td>
      <td>@{{ list.shadow }}</td>
      <td>@{{ list.position }}</td>
      <td>@{{ list.time }}</td>
      <td>@{{ list.south }}</td>
      <td>@{{ list.north }}</td>
    </tr>
  </table>
  <infinite-loading :identifier="infiniteId" @infinite="search">
    <div slot="no-more"></div>
    <div slot="no-results"></div>
  </infinite-loading>
  @include('layouts.goodUrl')
  <go-top></go-top>
</div>

<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      lists: [],
      page: 1,
      infiniteId: +new Date(),
    },
    mounted() {
    },
    methods: {
      search($state) {
        axios.post('/insect/search', {
           page: this.page,
           race: this.searchData.race,
           personality: this.searchData.personality,
           bd: this.searchData.bd,
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
      searchDefault() {
        this.page = 1;
        this.lists = [];
        this.infiniteId += 1;
      }
    }
  })
</script>
@endsection
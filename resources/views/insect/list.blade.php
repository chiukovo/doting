@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="#">博物館</a>
  <span class="sep">/</span>
  <a href="/insect/list">昆蟲圖鑑</a>
</div>
<div id="app" class="media" v-cloak>
  <div class="search">
    <form>
      <table class="table">
        <tr>
          <th>類型</th>
          <td>
            <a href="/fish/list"><button type="button" class="btn">魚</button></a>
            <a href="/insect/list"><button type="button" class="btn current">昆蟲</button></a>
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
      <th>名稱</th>
      <!-- <th>陰影</th> -->
      <th>位置</th>
      <th width="60">時間</th>
      <th width="60">南半球月份</th>
      <th width="60">北半球月份</th>
    </tr>
    <tr v-for="list in lists">
      <td>
        <a :href="'/other/' + list.name + '.png'" :data-lightbox="list.name" :data-title="list.name">
          <span>@{{ list.name }}<br>$@{{ list.sell }}</span>
          <div class="table-img">
            <img :src="'/other/' + list.name + '.png'" :alt="list.name">
          </div>
        </a>
      </td>
      <!-- <td>@ { { list.shadow } } </td> -->
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
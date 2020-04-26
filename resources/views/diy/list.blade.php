@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="#">Diy圖鑑</a>
</div>
<div id="app" class="media" v-cloak>
  <div class="search">
    <form>
      <table class="table">
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
      <th>類型</th>
      <th>取得</th>
      <th>Diy</th>
    </tr>
    <tr v-for="list in lists">
      <td>
        <span>@{{ list.name }}</span>
      </td>
      <td>@{{ list.type }}</td>
      <td>@{{ list.get }}</td>
      <td>@{{ list.diy }}</td>
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
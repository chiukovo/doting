@extends('layouts.web')
@section('title', '服飾/家具/植物圖鑑')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  @if ($type == 'furniture')
    <a href="/furniture/list">服飾圖鑑</a>
  @elseif ($type == 'apparel')
    <a href="/apparel/list">家具圖鑑</a>
  @elseif ($type == 'plant')
    <a href="/plant/list">植物圖鑑</a>
  @else
    <a href="/items/all/list">家具服飾圖鑑</a>
  @endif
</div>
<div id="app" class="media" v-cloak>
  <div class="search">
    <table class="table">
      <tr>
        <td colspan="2">
          <button type="button" class="btn btn-senior" @click.preven="moreSearch = !moreSearch">進階搜尋</button>
        </td>
      </tr>
      <tr v-if="moreSearch">
        <th>查看全部</th>
        <td><button class="btn" :class="checkAllCurrent()" @click="clearAll">查看全部</button></td>
      </tr>
      <tr v-if="moreSearch">
        <th>類型</th>
        <td>
          <button class="btn" :class="searchData.category.indexOf(data.category) == '-1' ? '' : 'current'" v-for="data in category"  v-if="data.category != null" @click="addCategory(data.category)">
            @{{ data.category }}
          </button>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <form>
            <div class="form-search">
              <input type="text" class="input" v-model="searchData.text">
              <button native-type="submit" class="btn" @click.prevent="searchDefault">搜尋</button>
              <button class="btn" @click.prevent="clearAll">清除搜尋</button>
            </div>
          </form>
        </td>
      </tr>
    </table>
  </div>
  <table class="media-card table">
    <tr>
      <th>名稱</th>
      <th width="70">價格</th>
      <th width="60">賣出</th>
      <th width="60">類型</th>
      <th>尺寸</th>
    </tr>
    <tr v-for="list in lists">
      <td>
        <a :href="'/itemsNew/' + list.img_name + '.png'" :data-lightbox="list.name" :data-title="list.name">
          <span>@{{ list.name }}</span>
          <div class="table-img">
            <img :src="'/itemsNew/' + list.img_name + '.png'" :alt="list.name">
          </div>
        </a>
      </td>
      <td>@{{ formatPrice(list.buy) }}</td>
      <td>@{{ formatPrice(list.sell) }}</td>
      <td>@{{ list.category }}</td>
      <td>@{{ list.size }}</td>
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
      category: [],
      buyType: [],
      detailType: [],
      type: "{{ $type }}",
      moreSearch: false,
      searchData: {
        category: [],
        buyType: [],
        detailType: [],
        text: "{{ $text }}",
      }
    },
    mounted() {
      this.getAllType()
    },
    methods: {
      getAllType() {
        axios.get('/items/getAllType?type=' + this.type, {
         }).then((response) => {
           this.category = response.data.category
         })
      },
      search($state) {
        axios.post('/items/search', {
           page: this.page,
           category: this.searchData.category,
           text: this.searchData.text,
           type: this.type
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
      formatPrice(money) {
        if (money == null) {
          return ''
        }

        return money.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      clearAll() {
        this.searchData = {
          category: [],
          text: '',
        }

        this.searchDefault()
      },
      addCategory(category) {
        const key = this.searchData.category.indexOf(category)

        if (key == '-1') {
          //push
          this.searchData.category.push(category)
        } else {
          //add
          this.searchData.category.splice(key, 1);
        }

        this.searchDefault()
      },
      searchDefault() {
        this.page = 1;
        this.lists = [];
        this.infiniteId += 1;
      },
      checkAllCurrent() {
        if (this.searchData.category.length == 0) {
          return 'current'
        }
      }
    }
  })
</script>
@endsection
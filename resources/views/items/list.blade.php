@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  @if ($type == 'furniture')
    <a href="/furniture/list">服飾圖鑑</a>
  @else
    <a href="/apparel/list">家具圖鑑</a>
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
          <button class="btn" :class="searchData.itemsType.indexOf(data.items_type) == '-1' ? '' : 'current'" v-for="data in itemsType"  v-if="data.items_type != null" @click="addItemsType(data.items_type)">
            @{{ data.items_type }}
          </button>
        </td>
      </tr>
      @if($type != 'furniture')
      <tr v-if="moreSearch">
        <th>訂購</th>
        <td>
          <button class="btn" :class="searchData.buyType.indexOf(data.buy_type) == '-1' ? '' : 'current'" v-for="data in buyType"  v-if="data.buy_type != null" @click="addBuyType(data.buy_type)">
            @{{ data.buy_type }}
          </button>
        </td>
      </tr>
      @endif
      @if($type != 'furniture')
      <tr v-if="moreSearch">
        <th>分類</th>
        <td>
          <button class="btn" :class="searchData.detailType.indexOf(data.detail_type) == '-1' ? '' : 'current'" v-for="data in detailType"  v-if="data.detail_type != null" @click="addDetailType(data.detail_type)">
            @{{ data.detail_type }}
          </button>
        </td>
      </tr>
      @endif
      <tr>
        <td colspan="2">
          <form>
            <div class="form-search">
              <input type="text" class="input" v-model="searchData.text">
              <button native-type="submit" class="btn" @click.prevent="searchDefault">搜尋</button>
            </div>
          </form>
        </td>
      </tr>
    </table>
  </div>
  <table class="media-card table">
    <tr>
      <th>名稱</th>
      <th>價格</th>
      <th>賣出</th>
      <th>回收</th>
      <th>類型</th>
      <th>分類</th>
      @if($type != 'furniture')
      <th>訂購</th>
      <th>尺寸</th>
      @endif
    </tr>
    <tr v-for="list in lists">
      <td>
        <span>@{{ list.name }}</span>
        <div class="table-img">
          <img :src="'/items/' + list.img_name + '.png'" :alt="list.name">
        </div>
      </td>
      <td>@{{ list.source_sell }}</td>
      <td>@{{ list.sell }}</td>
      <td>@{{ list.sample_sell }}</td>
      <td>@{{ list.type }}</td>
      <td>@{{ list.detail_type }}</td>
      @if($type != 'furniture')
      <td>@{{ list.buy_type }}</td>
      <td>@{{ list.size }}</td>
      @endif
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
      itemsType: [],
      buyType: [],
      detailType: [],
      type: "{{ $type }}",
      moreSearch: false,
      searchData: {
        itemsType: [],
        buyType: [],
        detailType: [],
        text: '',
      }
    },
    mounted() {
      this.getAllType()
    },
    methods: {
      getAllType() {
        axios.get('/items/getAllType?type=' + this.type, {
         }).then((response) => {
           this.itemsType = response.data.itemsType
           this.buyType = response.data.buyType
           this.detailType = response.data.detailType
         })
      },
      search($state) {
        axios.post('/items/search', {
           page: this.page,
           detailType: this.searchData.detailType,
           buyType: this.searchData.buyType,
           itemsType: this.searchData.itemsType,
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
      clearAll() {
        this.searchData = {
          itemsType: [],
          buyType: [],
          detailType: [],
          text: '',
        }

        this.searchDefault()
      },
      addItemsType(itemsType) {
        const key = this.searchData.itemsType.indexOf(itemsType)

        if (key == '-1') {
          //push
          this.searchData.itemsType.push(itemsType)
        } else {
          //add
          this.searchData.itemsType.splice(key, 1);
        }

        this.searchDefault()
      },
      addBuyType(buyType) {
        const key = this.searchData.buyType.indexOf(buyType)

        if (key == '-1') {
          //push
          this.searchData.buyType.push(buyType)
        } else {
          //add
          this.searchData.buyType.splice(key, 1);
        }

        this.searchDefault()
      },
      addDetailType(detailType) {
        const key = this.searchData.detailType.indexOf(detailType)

        if (key == '-1') {
          //push
          this.searchData.detailType.push(detailType)
        } else {
          //add
          this.searchData.detailType.splice(key, 1);
        }

        this.searchDefault()
      },
      searchDefault() {
        this.page = 1;
        this.lists = [];
        this.infiniteId += 1;
      },
      checkAllCurrent() {
        if (this.searchData.detailType.length == 0 && this.searchData.buyType.length == 0 && this.searchData.itemsType.length == 0) {
          return 'current'
        }
      }
    }
  })
</script>
@endsection
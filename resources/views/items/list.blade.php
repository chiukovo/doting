@extends('layouts.web')
@section('title', '服飾/家具/植物 圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">
      @if ($type == 'furniture')
        服飾圖鑑
      @elseif ($type == 'apparel')
        家具圖鑑
      @elseif ($type == 'plant')
        植物圖鑑
      @else
        家具服飾圖鑑
      @endif
    </h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        @if ($type == 'furniture')
          <li class="breadcrumb-item active" aria-current="page">服飾圖鑑</li>
        @elseif ($type == 'apparel')
          <li class="breadcrumb-item active" aria-current="page">家具圖鑑</li>
        @elseif ($type == 'plant')
          <li class="breadcrumb-item active" aria-current="page">植物圖鑑</li>
        @else
          <li class="breadcrumb-item active" aria-current="page">家具服飾圖鑑</li>
        @endif
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
                  <button class="btn btn-search" :class="checkAllCurrent()" @click="clearAll">查看全部</button>
                </td>
              </tr>
              <tr>
                <td class="text-center">類型</td>
                <td>
                  <button class="btn btn-search" :class="searchData.category.indexOf(data.category) == '-1' ? '' : 'current'" v-for="data in category"  v-if="data.category != null" @click="addCategory(data.category)">
                    @{{ data.category }}
                  </button>
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
                <th scope="col">價格</th>
                <th scope="col">賣出</th>
                <th scope="col">類型</th>
                <th scope="col">尺寸</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td class="link" scope="row">
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
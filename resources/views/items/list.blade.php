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
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
          <div class="collapse show" id="collapseSearch">
            <table class="table table-bordered">
              <tr>
                <td class="text-center table-label">類型</td>
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
              <input type="text" class="form-control" placeholder="請輸入關鍵字(或顏色)" v-model="searchData.text">
              <button class="btn btn-primary" native-type="submit" @click.prevent="searchDefault">搜尋</button>
              <button class="btn btn-default" @click.prevent="clearAll">清除搜尋</button>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="row">
            <div class="col text-right">
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'track' ? 'current' : ''" @click="searchTarget('track')">已追蹤:@{{ trackCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'noTrack' ? 'current' : ''" @click="searchTarget('noTrack')">未追蹤:@{{ noTrackCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'like' ? 'current' : ''" @click="searchTarget('like')">已擁有:@{{ likeCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'noLike' ? 'current' : ''" @click="searchTarget('noLike')">未擁有:@{{ noLikeCount }}
              </button>
              <button class="btn btn-default" @click="isList = !isList"><i class="fas" :class="isList ? 'fa-list' : 'fa-grip-horizontal'"></i></button>
            </div>
          </div>
          <table class="table table-bordered table-hover text-center" v-if="isList">
            <thead>
              <tr>
                <th scope="col">名稱</th>
                <th scope="col">樣式</th>
                <th scope="col">價格</th>
                <th scope="col">賣出</th>
                <th scope="col">類型</th>
                <th style="width: 120px;">追蹤/擁有</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td class="link" scope="row">
                  <a :href="'/itemsNew/' + list.img_name + '.png?v=' + version" :data-lightbox="list.name" :data-title="list.name">
                    <span>@{{ list.name }}<br>@{{ list.jp_name }}</span>
                    <div class="table-img">
                      <img :src="'/itemsNew/' + list.img_name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td>@{{ list.color }}</td>
                <td>@{{ formatPrice(list.buy) }}</td>
                <td>@{{ formatPrice(list.sell) }}</td>
                <td>@{{ list.category }}(@{{ list.size }})</td>
                <td>
                  <ul class="user-save-btn">
                    <li>
                      <button class="btn btn-outline-danger" @click.prevent.stop="toggleLike('track', list)" :class="list.track ? 'current' : ''"><i class="fas fa-bookmark"></i></button>
                    </li>
                    <li>
                      <button class="btn btn-outline-success" @click.prevent.stop="toggleLike('like', list)" :class="list.like ? 'current' : ''"><i class="fas fa-heart"></i></button>
                    </li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- style: list -->
          <ul class="card-list" v-if="!isList">
            <li v-for="list in lists">
              <div class="card-list-item">
                <div class="card-list-img card-list-icon">
                  <div class="card-list-get">
                    <img src="/image/icon_catalog2.png" alt="商店購買" v-if="list.catalog && !list.is_diy">
                    <img src="/image/icon_diy2.png" alt="DIY方程式" v-if="list.is_diy">
                  </div>
                  <div class="card-list-get right">
                    <img src="/image/icon_paintbrush.png" alt="可改造" v-if="list.customize">
                  </div>
                  <img class="img-fluid" :src="'/itemsNew/' + list.img_name + '.png?v=' + version" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }}<br>@{{ list.jp_name }}</div>
                <div class="card-list-info" v-if="list.buy != null">
                  <h5 class="text-danger font-weight-bold m-1">$@{{ formatPrice(list.buy) }}</h5>
                </div>
                <div class="card-list-info" v-else>
                  -
                </div>
                <div class="card-list-info">@{{ list.color }}</div>
                <div class="card-list-info">@{{ list.category }} <span v-if="list.size != null">(@{{ list.size }})</span></div>
                <div class="card-list-btn">
                  <ul class="user-save-btn">
                    <li>
                      <button class="btn btn-outline-danger" @click.prevent.stop="toggleLike('track', list)" :class="list.track ? 'current' : ''"><i class="fas fa-bookmark"></i>追蹤</button>
                    </li>
                    <li>
                      <button class="btn btn-outline-success" @click.prevent.stop="toggleLike('like', list)" :class="list.like ? 'current' : ''"><i class="fas fa-heart"></i>擁有</button>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
          </ul>
          <infinite-loading :identifier="infiniteId" @infinite="search">
            <div slot="no-more"></div>
            <div slot="no-results"></div>
          </infinite-loading>
          <div class="card not-found">
            <div class="card-body text-center" v-show="lists.length == 0 && !loading">
              找不到捏 哇耶...(¬_¬) 
            </div>
          </div>
          @include('layouts.ads2')
        </div>
      </div>
    </section>
  </div>
  @include('layouts.goTop')
  @include('layouts.modal')
</div>

<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      lists: [],
      isList: false,
      page: 1,
      loading: false,
      likeType: 'items',
      likeCount: 0,
      noLikeCount: 0,
      trackCount: 0,
      noTrackCount: 0,
      version: "{{ config('app.version') }}",
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
        target: "{{ $target }}",
      }
    },
    mounted() {
      this.getAllType()
      this.getLikeCount()
    },
    methods: {
      getAllType() {
        axios.get('/items/getAllType?type=' + this.type, {
         }).then((response) => {
           this.category = response.data.category
         })
      },
      getLikeCount() {
        axios.get('/like/count', {
          params: {
            likeType: this.likeType,
            type: this.type,
          }
         }).then((response) => {
            const result = response.data

            this.trackCount = result.trackCount
            this.likeCount = result.likeCount
            this.noTrackCount = result.noTrackCount
            this.noLikeCount = result.noLikeCount
         })
      },
      searchTarget(target) {
        if (target == this.searchData.target) {
          this.searchData.target = ''
        } else {
          this.searchData.target = target
        }

        this.searchDefault()
      },
      toggleLike(target, list) {
        axios.post('/toggleLike', {
           likeType: this.likeType,
           type: this.type,
           likeTarget: target,
           token: list.token,
         }).then((response) => {
          const result = response.data
          if (result.code == -1) {
            $('#lineLoginModel').modal()
          }

          if (result.code == 1) {
            list[target] = !list[target]
            this.trackCount = result.count.trackCount
            this.noTrackCount = result.count.noTrackCount
            this.likeCount = result.count.likeCount
            this.noLikeCount = result.count.noLikeCount

            let message
            let prex = ''

            if (!list[target]) {
              prex = '取消'
            }

            if (target == 'track') {
              message = '已' + prex + '追蹤'
            } else if (target == 'like') {
              message = '已' + prex + '擁有'
            }

            $('#hint-message .message').text(message)
            $('#hint-message').addClass('show')

            window.setTimeout(( () => $('#hint-message').removeClass('show') ), 1000)
          }
         })
      },
      search($state) {
        this.loading = true
        axios.post('/items/search', {
           page: this.page,
           category: this.searchData.category,
           text: this.searchData.text,
           target: this.searchData.target,
           type: this.type
         }).then((response) => {
           if (response.data.length) {
             this.page += 1;
             this.lists.push(...response.data);
             $state.loaded();
           } else {
             $state.complete();
           }

           this.loading = false
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
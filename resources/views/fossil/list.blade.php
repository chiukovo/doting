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
            <div class="col text-right">
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'track' ? 'current' : ''" @click="searchTarget('track')">已追蹤:@{{ trackCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'noTrack' ? 'current' : ''" @click="searchTarget('noTrack')">未追蹤:@{{ noTrackCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'like' ? 'current' : ''" @click="searchTarget('like')">已捐贈:@{{ likeCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'noLike' ? 'current' : ''" @click="searchTarget('noLike')">未捐贈:@{{ noLikeCount }}
              </button>
              <button class="btn btn-default" @click="isList = !isList"><i class="fas" :class="isList ? 'fa-list' : 'fa-grip-horizontal'"></i></button>
            </div>
          </div>
          <table class="table table-bordered table-hover text-center" v-if="isList">
            <thead>
              <tr>
                <th class="table-label" scope="col">名稱</th>
                <th scope="col">介紹</th>
                <th style="width: 120px;">追蹤/捐贈</th>
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
                <a :href="'/fossil/' + list.img_name + '.png?v=' + version" :data-lightbox="list.name" :data-title="list.name" class="link">
                  <div class="card-list-img">
                    <img class="img-fluid" :src="'/fossil/' + list.name + '.png?v=' + version" :alt="list.name">
                  </div>
                </a>
                <div class="card-list-title">@{{ list.name }}</div>
                <div class="card-list-info">
                  <h5 class="text-danger font-weight-bold m-1">$@{{ formatPrice(list.sell) }}</h5>
                </div>
                <div class="card-list-btn">
                  <ul class="user-save-btn">
                    <li>
                      <button class="btn btn-outline-danger" @click.prevent.stop="toggleLike('track', list)" :class="list.track ? 'current' : ''"><i class="fas fa-bookmark"></i>追蹤</button>
                    </li>
                    <li>
                      <button class="btn btn-outline-success" @click.prevent.stop="toggleLike('like', list)" :class="list.like ? 'current' : ''"><i class="fas fa-heart"></i>捐贈</button>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
          </ul>
          <infinite-loading :identifier="infiniteId" @infinite="search" v-show="lists.length == 0">
            <div slot="no-more"></div>
            <div slot="no-results"></div>
          </infinite-loading>
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
      page: 1,
      likeType: 'fossil',
      likeCount: 0,
      noLikeCount: 0,
      trackCount: 0,
      noTrackCount: 0,
      type: 'fossil',
      version: "{{ config('app.version') }}",
      isList: false,
      infiniteId: +new Date(),
      searchData: {
        text: "{{ $text }}",
        target: "{{ $target }}",
      }
    },
    mounted() {
      this.getLikeCount()
    },
    methods: {
      formatPrice(money) {
        if (money == null) {
          return ''
        }

        return money.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
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
              message = '已' + prex + '捐贈'
            }

            $('#hint-message .message').text(message)
            $('#hint-message').addClass('show')

            window.setTimeout(( () => $('#hint-message').removeClass('show') ), 1000)
          }
         })
      },
      search($state) {
        axios.post('/fossil/search', {
           page: this.page,
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
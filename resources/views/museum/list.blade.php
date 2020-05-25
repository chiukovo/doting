@extends('layouts.web')
@section('title', '博物館')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">博物館</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">博物館</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
          <div class="collapse show" id="collapseSearch">
            <table class="table table-bordered">
              <tr>
                <td class="text-center" width="80">查看全部</td>
                <td>
                  <button class="btn btn-search"@click.prevent="clearAll">查看全部</button>
                </td>
              </tr>
              <tr>
                <td>類型</td>
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
          <table class="table table-bordered table-hover text-center"  v-if="isList">
            <thead>
              <tr>
                <th class="table-label" scope="col">名稱</th>
                <th scope="col" v-show="!isMobile()">陰影</th>
                <th scope="col">位置</th>
                <th scope="col">時間</th>
                <th scope="col">南半球月份</th>
                <th scope="col">北半球月份</th>
                <th style="width: 120px;">追蹤/捐贈</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td scope="row">
                  <a :href="'/fish/detail?name=' + list.name" class="link" v-if="list.shadow">
                    <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                    <div class="table-img">
                      <img :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                  <a :href="'/insect/detail?name=' + list.name" class="link" v-else>
                    <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                    <div class="table-img">
                      <img :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td v-show="!isMobile()">@{{ list.shadow }}</td>
                <td>@{{ list.position }}</td>
                <td>@{{ list.time }}</td>
                <td>@{{ list.south }}</td>
                <td>@{{ list.north }}</td>
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
              <div class="card-list-item" @click="goDetail(list)">
                <div class="card-list-img">
                  <img class="img-fluid" :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }}</div>
                <div class="card-list-info">
                  <h5 class="text-danger font-weight-bold m-1">$@{{ formatPrice(list.sell) }}</h5>
                </div>
                <div class="card-list-info" v-if="list.shadow">
                  @{{ list.shadow }} / @{{ list.position }} / @{{ list.time }} 時
                </div>
                <div class="card-list-info" v-else>
                  @{{ list.position }} / @{{ list.time }} 時
                </div>
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
      likeType: 'museum',
      likeCount: 0,
      noLikeCount: 0,
      trackCount: 0,
      noTrackCount: 0,
      type: 'museum',
      version: "{{ config('app.version') }}",
      infiniteId: +new Date(),
      searchData: {
        text: "{{ $text }}",
      }
    },
    mounted() {
      this.getLikeCount()
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      goDetail(list) {
        if (list.shadow) {
          location.href = '/fish/detail?name=' + list.name
        } else {
          location.href = '/insect/detail?name=' + list.name
        }
      },
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
           likeType: list.type,
           type: list.type,
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
        this.loading = true
        axios.post('/museum/search', {
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

           this.loading = false
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
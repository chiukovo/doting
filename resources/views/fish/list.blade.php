@extends('layouts.web')
@section('title', '魚圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">魚圖鑑</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item"><a href="/museum/list">博物館</a></li>
        <li class="breadcrumb-item active" aria-current="page">魚圖鑑</li>
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
                <tr>
                  <td class="text-center">所屬半球</td>
                  <td>
                    <button class="btn btn-search" :class="searchData.position == '南' ? 'current' : ''" @click="addPosition('南')">
                      南半球
                    </button>
                    <button class="btn btn-search" :class="searchData.position == '北' ? 'current' : ''" @click="addPosition('北')">
                      北半球
                    </button>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">月份</td>
                  <td>
                    <button class="btn btn-search" :class="searchData.month.indexOf(key + 1) == '-1' ? '' : 'current'" v-for="(data, key) in month" @click="addMonth(key + 1)">
                      @{{ data }}
                    </button>
                  </td>
                </tr>
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
                  <a :href="'/fish/detail?name=' + list.name" class="link">
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
              <div class="card-list-item">
                <div class="card-list-img" @click="goDetail(list)">
                  <img class="img-fluid" :src="'/other/' + list.name + '.png?v=' + version" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }}</div>
                <div class="card-list-info">
                  <h5 class="text-danger font-weight-bold m-1">$@{{ formatPrice(list.sell) }}</h5>
                </div>
                <div class="card-list-info">
                  @{{ list.shadow }} / @{{ list.position }} / @{{ list.time }} 時
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
      likeType: 'fish',
      likeCount: 0,
      noLikeCount: 0,
      trackCount: 0,
      noTrackCount: 0,
      type: 'fish',
      nowMounth: "{{ date('m') * 1 }}",
      month: [
        '一月',
        '二月',
        '三月',
        '四月',
        '五月',
        '六月',
        '七月',
        '八月',
        '九月',
        '十月',
        '十一月',
        '十二月'
      ],
      version: "{{ config('app.version') }}",
      infiniteId: +new Date(),
      searchData: {
        target: "{{ $target }}",
        text: "{{ $text }}",
        position: "{{ $position }}",
        month: [],
      }
    },
    mounted() {
      this.getLikeCount()
      if (this.searchData.position != '') {
        this.searchData.month.push(parseInt(this.nowMounth))
      }
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      goDetail(list) {
        location.href = '/fish/detail?name=' + list.name
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
      formatPrice(money) {
        if (money == null) {
          return ''
        }

        return money.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      search($state) {
        this.loading = true
        axios.post('/fish/search', {
           page: this.page,
           text: this.searchData.text,
           target: this.searchData.target,
           month: this.searchData.month,
           position: this.searchData.position,
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
      searchTarget(target) {
        if (target == this.searchData.target) {
          this.searchData.target = ''
        } else {
          this.searchData.target = target
        }

        this.searchDefault()
      },
      clearAll() {
        this.searchData = {
          text: '',
          month: [],
          position: '',
        }

        this.searchDefault()
      },
      addMonth(month) {
        const key = this.searchData.month.indexOf(month)

        if (key == '-1') {
          //push
          this.searchData.month.push(month)
        } else {
          //add
          this.searchData.month.splice(key, 1);
        }

        this.searchDefault()
      },
      addPosition(position) {
        let needAddMonth = false

        if (this.searchData.month.length == 0) {
          needAddMonth = true
        }

        if (position == this.searchData.position) {
          this.searchData.position = ''
        } else {
          this.searchData.position = position

          if (needAddMonth) {
            this.searchData.month.push(parseInt(this.nowMounth))
          }
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
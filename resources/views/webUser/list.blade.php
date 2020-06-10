@extends('layouts.web')
@section('title', '豆丁交友區')
@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-masonry@0.10.12/dist/vue-masonry-plugin.js"></script>
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">豆丁交友區</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">豆丁交友區</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
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
            <div class="col text-left mb-2">
              點選登入->我的資訊並 <span class="text-danger">公開護照</span> 即可曝光跟大家交朋友摟^_^
            </div>
            <div class="col text-right mb-2">
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'like' ? 'current' : ''" @click="searchTarget('like')">已按讚: @{{ likeCount }}
              </button>
            </div>
          </div>
          <div v-masonry class="row">
            <div v-masonry-tile class="col-12 col-sm-6 col-lg-4" v-for="list in lists" v-if="!isList && checkShow(list)">
              <div class="card friends-card mb-3">
                <div class="row no-gutters">
                  <div class="col">
                    <div class="card-body p-2 bg-light">
                      <div class="d-flex justify-content-between">
                        <h5 class="card-link m-0">SW-@{{ list.passport }}</h5>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row no-gutters">
                  <div class="friends-card-img col-md-4">
                    <div class="row">
                      <div class="col">
                        <img :src="list.picture_url">
                        <div class="text-center m-1">
                          <a href="#" class="card-link" @click.prevent.stop="toggleLike('like', list)" :class="list.like ? 'text-danger' : 'text-secondary'">
                            <i class="fab fa-gratipay"></i>
                            <span>@{{ list.likeCount }}人</span>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="friends-card-info col-md-8">
                    <div class="card-body">
                      <h5><b>@{{ list.nick_name }}</b></h5>
                      <h6>@{{ list.island_name }}</h6>
                      <hr class="m-1">
                      @{{ list.fruit_name }} / @{{ list.position_name }}<br>
                      <hr class="m-1">
                      島花：@{{ list.flower }}<br>
                      <hr class="m-1">
                      自介：@{{ list.info }}<br>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <infinite-loading :identifier="infiniteId" @infinite="search">
            <div slot="no-more"></div>
            <div slot="no-results"></div>
          </infinite-loading>
          <div class="card not-found">
            <div class="card-body text-center" v-show="lists.length == 0 && !loading">
              沒人分享捏 哇耶...(¬_¬) 
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
  Vue.use(window['vue-masonry-plugin'].VueMasonryPlugin)
  Vue.use(GoTop)
  new Vue({
    el: '#app',
    data: {
      lists: [],
      isList: false,
      page: 1,
      loading: false,
      likeType: 'friend',
      likeCount: 0,
      noLikeCount: 0,
      trackCount: 0,
      noTrackCount: 0,
      type: 'friend',
      version: "{{ config('app.version') }}",
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
              list.likeCount = list.likeCount - 1
            } else {
              list.likeCount = list.likeCount + 1
            }

            if (target == 'track') {
              message = '已' + prex + '追蹤'
            } else if (target == 'like') {
              message = '已' + prex + '按讚'
            }

            $('#hint-message .message').text(message)
            $('#hint-message').addClass('show')

            window.setTimeout(( () => $('#hint-message').removeClass('show') ), 1000)
          }
         })
      },
      search($state) {
        this.loading = true

        axios.post('/friend/search', {
           page: this.page,
           text: this.searchData.text,
           target: this.searchData.target,
           type: this.type
         }).then((response) => {
           if (response.data.length) {
             this.page += 1;
             this.lists.push(...response.data);
             if(typeof $state != "undefined") {
              $state.loaded()
             }
           } else {
            if(typeof $state != "undefined") {
              $state.complete();
            }
           }

           this.loading = false
         })
      },
      checkShow(list) {
        if (list.nick_name != '' && list.passport != null && list.passport != '') {
          return true
        }

        return false
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

        this.search()
      }
    }
  })
</script>
@endsection
@extends('layouts.web')
@section('title', '動物圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">
      @if($type == 'npc')
        動物NPC
      @else
        動物居民
      @endif
    </h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        @if($type == 'npc')
          <li class="breadcrumb-item active" aria-current="page">動物NPC</li>
        @else
          <li class="breadcrumb-item active" aria-current="page">動物居民</li>
        @endif
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="section-search row">
        <div class="col">
          <!-- 電腦版隱藏進階搜尋按鈕, class="collapse" 移除 -->
          <a class="collapse-search" data-toggle="collapse" href="#collapseSearch" role="button" v-show="isMobile()">進階搜尋<ion-icon name="chevron-down-outline"></ion-icon></a>
          <div class="collapse" :class="isMobile() ? '' : 'show'" id="collapseSearch">
            <table class="table table-bordered">
              <tr>
                <td class="table-label text-center">種族</td>
                <td>
                  <button class="btn btn-search" :class="searchData.race.indexOf(data.race) == '-1' ? '' : 'current'" v-for="data in race"  v-if="data.race != ''" @click="addRace(data.race)">
                    @{{ data.race }}
                  </button>
                </td>
              </tr>
              <tr>
                <td class="text-center">個性</td>
                <td>
                  <button class="btn btn-search" :class="searchData.personality.indexOf(data) == '-1' ? '' : 'current'" v-for="data in personality" @click="addPersonality(data)">
                    @{{ data }}
                  </button>
                </td>
              </tr>
              <tr>
                <td class="text-center">生日</td>
                <td>
                  <button class="btn btn-search" :class="searchData.bd.indexOf(key + 1) == '-1' ? '' : 'current'" v-for="(data, key) in bd" @click="addBd(key + 1)">
                    @{{ data }}
                  </button>
                </td>
              </tr>
            </table>
          </div>
          <form>
            <div class="form-search">
              <input type="text" class="form-control" placeholder="請輸入關鍵字" v-model="searchData.text">
              <button class="btn btn-primary" native-type="submit" @click.prevent.stop="searchDefault">搜尋</button>
              <button class="btn btn-default" :class="checkAllCurrent()" @click.prevent.stop="clearAll">清除搜尋</button>
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
              @if($type != 'npc')
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'like' ? 'current' : ''" @click="searchTarget('like')">已擁有:@{{ likeCount }}
              </button>
              <button class="badge badge-pill badge-light py-2 px-2 mt-1" :class="searchData.target == 'noLike' ? 'current' : ''" @click="searchTarget('noLike')">未擁有:@{{ noLikeCount }}
              </button>
              @endif
              <button class="btn btn-default" @click="isList = !isList"><i class="fas" :class="isList ? 'fa-list' : 'fa-grip-horizontal'"></i></button>
            </div>
          </div>
          <table class="table table-bordered table-hover text-center" v-if="isList">
            <thead>
              <tr>
                <th scope="col">名稱</th>
                <th scope="col" v-show="!isMobile()">性別</th>
                <th scope="col">個性</th>
                <th scope="col">種族</th>
                @if($type != 'npc')
                <th scope="col">生日</th>
                <th scope="col" v-show="!isMobile()">口頭禪</th>
                <th style="width: 120px;">追蹤/擁有</th>
                @endif
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td scope="row">
                  <a class="link" :href="'/animals/detail?name=' + list.name">
                    <span>@{{ list.name }}</span>
                    <div class="table-img" v-if="list.info == null">
                      <img :src="'/animal/icon/' + list.name + '.png?v=' + version" :alt="list.name">
                    </div>
                    <div class="table-img" v-else>
                      <img :src="'/animal/' + list.name + '.png?v=' + version" :alt="list.name">
                    </div>
                  </a>
                </td>
                <td v-show="!isMobile()">@{{ list.sex }}</td>
                <td>@{{ list.personality }}</td>
                <td>@{{ list.race }}</td>
                @if($type != 'npc')
                <td>@{{ list.bd }}</td>
                <td v-show="!isMobile()">@{{ list.say }}</td>
                @endif
                <td>
                  <ul class="user-save-btn">
                    <li>
                      <button class="btn btn-outline-danger" @click.prevent.stop="toggleLike('track', list)" :class="list.track ? 'current' : ''"><i class="fas fa-bookmark"></i></button>
                    </li>
                    @if($type != 'npc')
                    <li>
                      <button class="btn btn-outline-success" @click.prevent.stop="toggleLike('like', list)" :class="list.like ? 'current' : ''"><i class="fas fa-heart"></i></button>
                    </li>
                    @endif
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
                  <img class="img-fluid" :src="'/animal/' + list.name + '.png?v=' + version" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }} @{{ list.sex }}</div>
                <div class="card-list-info" v-if="list.info == null">
                  @{{ list.personality }} / @{{ list.race }} / @{{ list.bd }}
                </div>
                <div class="card-list-info" v-else>
                  @{{ list.race }}
                </div>
                <div class="card-list-btn">
                  <ul class="user-save-btn">
                    <li>
                      <button class="btn btn-outline-danger" @click.prevent.stop="toggleLike('track', list)" :class="list.track ? 'current' : ''"><i class="fas fa-bookmark"></i>追蹤</button>
                    </li>
                    @if($type != 'npc')
                    <li>
                      <button class="btn btn-outline-success" @click.prevent.stop="toggleLike('like', list)" :class="list.like ? 'current' : ''"><i class="fas fa-heart"></i>擁有</button>
                    </li>
                    @endif
                  </ul>
                </div>
              </div>
            </li>
          </ul>
          <infinite-loading :identifier="infiniteId" @infinite="search">
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
      version: "{{ config('app.version') }}",
      isList: false,
      infiniteId: +new Date(),
      race: [],
      personality: [],
      bd: [],
      likeType: 'animal',
      likeCount: 0,
      noLikeCount: 0,
      trackCount: 0,
      noTrackCount: 0,
      type: "{{ $type }}",
      moreSearch: false,
      searchData: {
        race: [],
        personality: [],
        bd: [],
        target: '',
        text: "{{ $text }}",
      }
    },
    mounted() {
      this.getAllType()
      this.getLikeCount()
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      goDetail(list) {
        location.href = '/animals/detail?name=' + list.name
      },
      getAllType() {
        axios.get('/animals/getAllType', {
          params: {
            likeType: this.likeType,
          }
         }).then((response) => {
           this.race = response.data.race
           this.personality = response.data.personality
           this.bd = response.data.bd
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
      search($state) {
        axios.post('/animals/search', {
           page: this.page,
           race: this.searchData.race,
           personality: this.searchData.personality,
           bd: this.searchData.bd,
           text: this.searchData.text,
           target: this.searchData.target,
           type: this.type
         }).then((response) => {
           if (response.data.length) {
             this.page += 1;
             this.lists.push(...response.data);
             $state.loaded()
           } else {
             $state.complete();
           }
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
          }
         })
      },
      clearAll() {
        this.searchData = {
          race: [],
          personality: [],
          bd: [],
          text: '',
        }

        this.searchDefault()
      },
      addPersonality(personality) {
        const key = this.searchData.personality.indexOf(personality)

        if (key == '-1') {
          //push
          this.searchData.personality.push(personality)
        } else {
          //add
          this.searchData.personality.splice(key, 1);
        }

        this.searchDefault()
      },
      addRace(race) {
        const key = this.searchData.race.indexOf(race)

        if (key == '-1') {
          //push
          this.searchData.race.push(race)
        } else {
          //add
          this.searchData.race.splice(key, 1);
        }

        this.searchDefault()
      },
      addBd(bd) {
        const key = this.searchData.bd.indexOf(bd)

        if (key == '-1') {
          //push
          this.searchData.bd.push(bd)
        } else {
          //add
          this.searchData.bd.splice(key, 1);
        }

        this.searchDefault()
      },
      searchDefault() {
        this.page = 1;
        this.lists = [];
        this.infiniteId += 1;
      },
      searchTarget(target) {
        if (target == this.searchData.target) {
          this.searchData.target = ''
        } else {
          this.searchData.target = target
        }

        this.searchDefault()
      },
      checkAllCurrent() {
        if (this.searchData.race.length == 0 && this.searchData.personality.length == 0 && this.searchData.bd.length == 0) {
          return 'current'
        }
      }
    }
  })
</script>
@endsection
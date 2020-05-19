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
              <button class="btn btn-primary" native-type="submit" @click.prevent="searchDefault">搜尋</button>
              <button class="btn btn-default" :class="checkAllCurrent()" @click.prevent="clearAll">清除搜尋</button>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <!-- 登入會員才顯示 -->
          <div class="row">
            <div class="col text-right mb-1">
              <button class="btn">全部: @{{ lists.length }} 個結果</button>
              <button class="btn btn-default" @click="isList = !isList"><i class="fas" :class="isList ? 'fa-list' : 'fa-grip-horizontal'"></i></button>
              <!-- table狀態顯示 fa-grip-horizontal
                  列表狀態顯示 fa-list
                -->
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
                @endif
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td scope="row">
                  <a class="link" :href="'/animals/detail?name=' + list.name">
                    <span>@{{ list.name }}</span>
                    <div class="table-img" v-if="list.info == null">
                      <img :src="'/animal/icon/' + list.name + '.png'" :alt="list.name">
                    </div>
                    <div class="table-img" v-else>
                      <img :src="'/animal/' + list.name + '.png'" :alt="list.name">
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
              </tr>
            </tbody>
          </table>
          <!-- style: list -->
          <ul class="card-list" v-if="!isList">
            <li v-for="list in lists">
              <div class="card-list-item" @click="goDetail(list)">
                <div class="card-list-img">
                  <img class="img-fluid" :src="'/animal/' + list.name + '.png'" :alt="list.name">
                </div>
                <div class="card-list-title">@{{ list.name }} @{{ list.sex }}</div>
                <div class="card-list-info" v-if="list.info == null">
                  @{{ list.personality }}/@{{ list.race }}/@{{ list.bd }}
                </div>
                <div class="card-list-info" v-else>
                  @{{ list.race }}
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
</div>
<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      lists: [],
      page: 1,
      isList: true,
      infiniteId: +new Date(),
      race: [],
      personality: [],
      bd: [],
      type: "{{ $type }}",
      moreSearch: false,
      searchData: {
        race: [],
        personality: [],
        bd: [],
        text: "{{ $text }}",
      }
    },
    mounted() {
      this.isList = this.isMobile() ? false : true
      this.getAllType()
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
        axios.get('/animals/getAllType?type=' + this.type, {
         }).then((response) => {
           this.race = response.data.race
           this.personality = response.data.personality
           this.bd = response.data.bd
         })
      },
      search($state) {
        axios.post('/animals/search', {
           page: this.page,
           race: this.searchData.race,
           personality: this.searchData.personality,
           bd: this.searchData.bd,
           text: this.searchData.text,
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
      checkAllCurrent() {
        if (this.searchData.race.length == 0 && this.searchData.personality.length == 0 && this.searchData.bd.length == 0) {
          return 'current'
        }
      }
    }
  })
</script>
@endsection
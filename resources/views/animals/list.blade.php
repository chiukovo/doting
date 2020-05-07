@extends('layouts.web')
@section('title', '動物圖鑑')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  @if ($type == 'npc')
    <a href="/npc/list">npc</a>
  @else
    <a href="/animals/list">動物島民</a>
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
        <th>種族</th>
        <td>
          <button class="btn" :class="searchData.race.indexOf(data.race) == '-1' ? '' : 'current'" v-for="data in race"  v-if="data.race != ''" @click="addRace(data.race)">
            @{{ data.race }}
          </button>
        </td>
      </tr>
      <tr v-if="moreSearch">
        <th>個性</th>
        <td>
          <button class="btn" :class="searchData.personality.indexOf(data) == '-1' ? '' : 'current'" v-for="data in personality" @click="addPersonality(data)">
            @{{ data }}
          </button>
        </td>
      </tr>
      <tr v-if="moreSearch">
        <th>生日</th>
        <td>
          <button class="btn" :class="searchData.bd.indexOf(key + 1) == '-1' ? '' : 'current'" v-for="(data, key) in bd" @click="addBd(key + 1)">
            @{{ data }}
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
      <th>種族</th>
      <th>個性</th>
      <th>生日</th>
    </tr>
    <tr v-for="list in lists">
      <td>
        <a :href="'/animals/detail?name=' + list.name">
          <span>@{{ list.name }}</span>
          <div class="table-img" v-if="list.info == null">
            <img :src="'/animal/icon/' + list.name + '.png'" :alt="list.name">
          </div>
          <div class="table-img" v-else>
            <img :src="'/animal/' + list.name + '.png'" :alt="list.name">
          </div>
        </a>
      </td>
      <td>@{{ list.race }}</td>
      <td>@{{ list.personality }}</td>
      <td>@{{ list.bd }}</td>
    </tr>
  </table>
  <infinite-loading :identifier="infiniteId" @infinite="search">
    <div slot="no-more"></div>
    <div slot="no-results"></div>
  </infinite-loading>
  <div class="media-card">
    <div class="media-card-title">島民說明</div>
    <ul class="media-list">
      <li>島上居民動物有親密度設定：認識的人、好友、親友</li>
      <li>親密度增加方式1：相遇初日以及每天對話會有加成、如果缺了一天會重置加成</li>
      <li>親密度增加方式2：完成動物賦予的任務、賣給小動物要的手上物件、信件往來</li>
      <li>親密度增加方式3：達成一定親密度才能送禮、生日當天送禮，禮物用包裝紙會有額外加成</li>
      <li>親密度降低方式：用補蟲網猛打、持續推擠、贈送空罐、長靴、雜草、壞掉的大頭菜</li>
      <li>長期沒有玩遊戲不會減低親密度</li>
      <li>親密度提昇至親友：會被主動對話，可獲得動物照片、可裝飾，能知道生日、星座、座右銘</li>
    </ul>
  </div>
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
      this.getAllType()
    },
    methods: {
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
             $state.loaded();
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
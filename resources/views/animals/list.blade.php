@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="#">首頁</a>
  <span class="sep">/</span>
  <a href="#">動物島民</a>
</div>
<div id="app" class="media" v-cloak>
  <div class="search">
    <table class="table">
      <tr v-if="moreSearch">
        <th>查看全部</th>
        <td><button class="btn current" @click="clearAll">查看全部</button></td>
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
        <th>搜尋</th>
        <td>
          <input type="text" v-model="searchData.text">
          <button @click="searchDefault">搜尋</button>
          <button @click="moreSearch = !moreSearch">進階搜尋</button>
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
          <div class="table-img">
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
  <div class="media-card">
    <div class="media-card-title">好用網址</div>
    <ul class="media-list">
      <li><a href="https://forum.gamer.com.tw/A.php?bsn=7287" target="_blank">巴哈姆特 動物森友會 哈啦區</a></li>
      <li><a href="https://ac-turnip.com/" target="_blank">動物森友會大頭菜計算機</a></li>
    </ul>
  </div>
</div>

<script>
  new Vue({
    el: '#app',
    data: {
      lists: [],
      page: 1,
      infiniteId: +new Date(),
      race: [],
      personality: [],
      bd: [],
      moreSearch: false,
      searchData: {
        race: [],
        personality: [],
        bd: [],
        text: '',
      }
    },
    mounted() {
      this.getAllType()
    },
    methods: {
      getAllType() {
        axios.get('/animals/getAllType', {
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
      }
    }
  })
</script>
@endsection
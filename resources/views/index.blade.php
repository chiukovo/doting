@extends('layouts.web')
@section('title', '豆丁森友會')
@section('content')
<canvas id="birthday"></canvas>
<div id="app" class="content-wrap first" v-cloak>
  <div class="first-birthday" v-if="birthday != null">
    <div class="first-birthday-img" :class="show ? 'show' : ''">
      <img class="img-fluid" :src="'/animal/' + birthday.name + '.png'" :alt="birthday.name" v-if="typeof birthday.name !== 'undefined'">
    </div>
    <div class="first-birthday-text" style="margin-top: 60px">
      <span>今天是 @{{ date }}</span>
      <span>是 <a :href="'/animals/detail?name=' + birthday.name">@{{ birthday.name }}</a> 的生日！</span>
    </div>
  </div>
  <div class="first-nav-wrap">
    <ul class="first-nav-list">
      <li>
        <a href="/instructions" class="first-nav-item">
          <span class="t1">豆丁教學</span>
          <span class="t2">豆丁教學</span>
          <div class="black icon__instructions"></div>
          <div class="white icon__instructions"></div>
        </a>
      </li>
      <li>
        <a href="/update/version" class="first-nav-item">
          <span class="t1">更新資訊</span>
          <span class="t2">更新資訊</span>
          <div class="black icon__version"></div>
          <div class="white icon__version"></div>
        </a>
      </li>
      <li>
        <a href="https://forms.gle/Q7StMmonyGdL4rCFA" class="first-nav-item" target="_blank">
          <span class="t1">意見回饋</span>
          <span class="t2">意見回饋</span>
          <div class="black icon__feedback"></div>
          <div class="white icon__feedback"></div>
        </a>
      </li>
      <li>
        <a href="/animals/list" class="first-nav-item">
          <span class="t1">動物居民</span>
          <span class="t2">動物居民</span>
          <div class="black icon__animals"></div>
          <div class="white icon__animals"></div>
        </a>
      </li>
      <li>
        <a href="/npc/list" class="first-nav-item">
          <span class="t1">動物NPC</span>
          <span class="t2">動物NPC</span>
          <div class="black icon__npc"></div>
          <div class="white icon__npc"></div>
        </a>
      </li>
      <li>
        <a href="/museum/list" class="first-nav-item">
          <span class="t1">博物館</span>
          <span class="t2">博物館</span>
          <div class="black icon__museum"></div>
          <div class="white icon__museum"></div>
        </a>
      </li>
      <li>
        <a href="/diy/list" class="first-nav-item">
          <span class="t1">DIY方程式</span>
          <span class="t2">DIY方程式</span>
          <div class="black icon__diy"></div>
          <div class="white icon__diy"></div>
        </a>
      </li>
      <li>
        <a href="/apparel/list" class="first-nav-item">
          <span class="t1">家具</span>
          <span class="t2">家具</span>
          <div class="black icon__apparel"></div>
          <div class="white icon__apparel"></div>
        </a>
      </li>
      <li>
        <a href="/furniture/list" class="first-nav-item">
          <span class="t1">服飾</span>
          <span class="t2">服飾</span>
          <div class="black icon__furniture"></div>
          <div class="white icon__furniture"></div>
        </a>
      </li>
      <li>
        <a href="/plant/list" class="first-nav-item">
          <span class="t1">植物</span>
          <span class="t2">植物</span>
          <div class="black icon__plant"></div>
          <div class="white icon__plant"></div>
        </a>
      </li>
      <li>
        <a href="/kk/list" class="first-nav-item">
          <span class="t1">唱片</span>
          <span class="t2">唱片</span>
          <div class="black icon__kk"></div>
          <div class="white icon__kk"></div>
        </a>
      </li>
      <li>
        <a href="/donate" class="first-nav-item">
          <span class="t1">贊助豆丁</span>
          <span class="t2">贊助豆丁</span>
          <div class="black icon__donate"></div>
          <div class="white icon__donate"></div>
        </a>
      </li>
    </ul>
  </div>
  <div class="first-container container">
    <div class="row">
      <div class="mt-3 col-12 col-md-4">
        <div class="card">
          <div class="card-header">
            豆丁搜尋排行榜
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush list-rank">
              <li class="list-group-item d-flex justify-content-between align-items-center" v-for="(rank, key) in ranking">
                <span class="list-rank-num" :class="'list-rank-num' + (key + 1)">@{{ key + 1}}</span>
                @{{ rank.text }}
                <span class="badge badge-light badge-pill">@{{ rank.number }}</span>
              </li>
            </ul>
            <div class="text-center mt-3">
              <a href="/statistics" class="btn btn-primary btn-block">查看更多</a>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-3 col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <nav class="nav nav-pills">
              <div class="dropdown">
                <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span v-if="isNorth">北半球</span>
                  <span v-else>南半球</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" href="#" @click.prevent="isNorth = true">北半球</a>
                  <a class="dropdown-item" href="#" @click.prevent="isNorth = false">南半球</a>
                </div>
              </div>
              <a class="nav-item nav-link disabled" href="#" tabindex="-1" aria-disabled="true">現在可捕捉的</a>
              <a class="nav-item nav-link" href="#" :class="isFish ? 'active' : ''" @click.prevent="isFish = true">魚</a>
              <a class="nav-item nav-link" href="#" :class="isFish ? '' : 'active'" @click.prevent="isFish = false">昆蟲</a>
            </nav>
          </div>
          <div class="card-body">
            <ul class="first-season-list" v-show="isFish && isNorth">
              <li v-for="list in northFish">
                <a :href="'/fish/detail?name=' + list.name" class="link">
                  <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                  <div class="table-img">
                    <img :src="'/other/' + list.name + '.png'" :alt="list.name">
                  </div>
                </a>
              </li>
            </ul>
            <ul class="first-season-list" v-show="isFish && !isNorth">
              <li v-for="list in southFish">
                <a :href="'/fish/detail?name=' + list.name" class="link">
                  <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                  <div class="table-img">
                    <img :src="'/other/' + list.name + '.png'" :alt="list.name">
                  </div>
                </a>
              </li>
            </ul>
            <ul class="first-season-list" v-show="!isFish && isNorth">
              <li v-for="list in northInsect">
                <a :href="'/insect/detail?name=' + list.name" class="link">
                  <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                  <div class="table-img">
                    <img :src="'/other/' + list.name + '.png'" :alt="list.name">
                  </div>
                </a>
              </li>
            </ul>
            <ul class="first-season-list" v-show="!isFish && !isNorth">
              <li v-for="list in southInsect">
                <a :href="'/insect/detail?name=' + list.name" class="link">
                  <span>@{{ list.name }}<br>$@{{ formatPrice(list.sell) }}</span>
                  <div class="table-img">
                    <img :src="'/other/' + list.name + '.png'" :alt="list.name">
                  </div>
                </a>
              </li>
            </ul>
          </div>
      </div>
    </div>
  </div>
</div>
<script src="/js/birthday.js"></script>
<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      date: '',
      show: false,
      isNorth: true,
      isFish: true,
      birthday: {},
      ranking: [],
      northFish: [],
      southFish: [],
      northInsect: [],
      southInsect: [],
    },
    mounted() {
      this.getIndexData()
    },
    methods: {
      formatPrice(money) {
        if (money == null) {
          return ''
        }

        return money.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      getIndexData() {
        axios.post('/indexData').then((response) => {
          const data = response.data
          this.birthday = data.birthday
          this.ranking = data.ranking
          this.date = data.date
          this.northFish = data.northFish
          this.southFish = data.southFish
          this.northInsect = data.northInsect
          this.southInsect = data.southInsect
          window.setTimeout(( () => this.show = true ), 200)
        })
      }
    }
  })
</script>
@endsection
@extends('layouts.web')
@section('title', '動物相容性分析')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">動物相容性分析</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="\">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">動物相容性分析</li>
      </ol>
    </nav>
    <section class="post">
      <div id="select-div" class="row fixed-bottom analysis-tag" v-show="selected.length != 0">
        <div class="col-12 col-md-10">
          <div class="table-responsive">
            <span>已選擇@{{ selected.length }}位居民</span>
            <button class="btn btn-tag" v-for="name in selected" @click="removeSelected(name)">@{{ name }}</button>
          </div>
        </div>
        <div class="col-12 col-md-2">
          <button class="btn btn-primary btn-block" native-type="submit" @click="goAnalysis">診斷分析</button>
        </div>
      </div>
      <div class="post-card">
        <a class="collapse-analysis" data-toggle="collapse" href="#collapse1" role="button" aria-expanded="true">居民選擇<ion-icon name="chevron-down-outline" role="img" class="md hydrated" aria-label="chevron down outline"></ion-icon></a>
        <div class="collapse" :class="collapseShow ? 'show' :''" id="collapse1">
          <div class="row section-search">
            <div class="col">
              <table class="table table-bordered">
                <tr>
                  <td class="text-center" width="80">顯示隱藏</td>
                  <td>
                    <button class="btn btn-search" @click="openAll">全部顯示</button>
                    <button class="btn btn-search" @click="searchSelected = []">全部隱藏</button>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">種族</td>
                  <td>
                    <button class="btn btn-search" :class="searchSelected.indexOf(race) == '-1' ? '' : 'current'" v-for="race in races" @click="toggleRace(race)">
                      @{{ race }}
                    </button>
                  </td>
                </tr>
              </table>
              <form>
                <div class="form-search">
                  <input type="text" class="form-control" placeholder="請輸入關鍵字" v-model="searchName">
                  <button class="btn btn-primary" native-type="submit" @click.prevent="getAnimalsGroupRace">搜尋</button>
                  <button class="btn btn-default" @click.prevent="searchName = ''; getAnimalsGroupRace()">清除搜尋</button>
                </div>
              </form>
            </div>
          </div>
          <div class="row my-2">
            <div class="col">選擇要診斷的居民，點擊診斷分析按鈕進行分析(人數可選：2~10人)</div>
          </div>
          <div class="row" v-for="(animal, race) in animals" v-show="searchSelected.indexOf(race) != '-1' && checkShow(animal)">
            <div class="col">
              <div class="card mb-3">
                <div class="card-header">@{{ race }}</div>
                <ul class="post-card-list animal-list check-list">
                  <li :class="selectedCurrent(detail.name)" v-for="detail in animal" @click="toggleSelected(detail.name)" v-show="detail.show">
                    <a href="javascript:void(0)">
                      <span>@{{ detail.name }}</span>
                      <div class="table-img">
                        <img :src="'/animal/icon/' + detail.name + '.png'" :alt="detail.name">
                      </div>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="row" v-if="animals.length == 0">
            <div class="col">
              <div class="post-card-title">查無任何動物</div>
            </div>
          </div>
          <div id="warning" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">提示訊息</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>分析人數範圍：2~10人</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="post-card" v-show="analysis.length > 0">
        <a class="collapse-analysis" data-toggle="collapse" href="#collapse2" role="button" aria-expanded="true">分析結果<ion-icon name="chevron-down-outline" role="img" class="md hydrated" aria-label="chevron down outline"></ion-icon></a>
        <div class="collapse" :class="collapseShow ? '' :'show'" id="collapse2">
          <div class="alert alert-primary mt-2" role="alert">
            <p class="mb-0">集合啦！豆丁森友會<br>
              總共有
              <span class="text-success">@{{ selected.length }}</span>
              人
              診斷結果為
              <span class="text-success h3" v-if="score >= 0">+@{{ score }}</span>
              <span class="text-danger h3" v-else-if="score < 0">@{{ score }}</span>
            </p>
            <p>在遊戲中 良好的兼容性:
              <span class="text-success">@{{ good }}</span>
              組 / 不兼容:
              <span class="text-danger">@{{ bad }}</span> 組
            </p>
            <hr>
            <p class="mb-0">診斷結果的值是通過從兼容對的數量中減去不兼容對的數量而獲得的數量。</p>
            <p class="mb-0">正值越大，居民的相容性越好。 相反，負值越大，居民的相容性越差。</p>
            <p class="mb-0">綠色框框代表相容性 <span class="text-success">高</span>，紅色框框代表相容性 <span class="text-danger">低</span></p>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-analysis">
              <tr>
                <td class="bg-light text-center" colspan="2" rowspan="2"><h5>總分數: <strong>@{{ sum }}</strong></h5></td>
                <td class="bg-light text-center" v-for="data in analysis">
                  <div class="analysis-scores">
                    <div>@{{ data.personality }} @{{ data.sex }}</div>
                    <div>@{{ data.constellation }} @{{ data.bd }}</div>
                    <div>@{{ data.race }}</div>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="bg-light" v-for="data in analysis">
                  <a :href="'/animals/detail?name=' + data.name" class="link" target="_blank">
                    <div class="analysis-info top">
                      <div class="analysis-icon">
                        <img :src="'/animal/icon/' + data.name + '.png'" :alt="data.name">
                      </div>
                      <div class="analysis-info-box">
                        <div class="analysis-name">@{{ data.name }}</div>
                      </div>
                    </div>
                  </a>
                </td>
              </tr>
              <tr v-for="data in analysis">
                <td class="bg-light text-center">
                  <div class="analysis-scores">
                    <div>@{{ data.personality }} @{{ data.sex }}</div>
                    <div>@{{ data.constellation }} @{{ data.bd }}</div>
                    <div>@{{ data.race }}</div>
                  </div>
                </td>
                <td class="bg-light">
                  <div class="analysis-info left">
                    <div class="analysis-icon">
                      <img :src="'/animal/icon/' + data.name + '.png'" :alt="data.name">
                    </div>
                    <div class="analysis-info-box text-center">
                      <div class="analysis-name">@{{ data.name }}
                        <span class="analysis-scores-total">
                          <strong class="text-success" v-if="data.score >= 0">+@{{ data.score }}</strong>
                          <strong class="text-danger" v-else-if="data.score < 0">@{{ data.score }}</strong>
                          <strong>@{{ data.totalSum }}</strong>
                        </span>
                      </div>
                      <div class="analysis-scores">
                        性格<span class="bg-danger-light">@{{ data.perScoreTotal }}</span>
                        星座<span class="bg-success-light">@{{ data.matchScoreTotal }}</span>
                        種族<span class="bg-primary-light">@{{ data.raceScoreTotal }}</span>
                      </div>
                    </div>
                  </div>
                </td>
                <td v-for="detail in data.detail" v-if="data.name != detail.name">
                  <div class="analysis-scores-total" :class="detail.class">@{{ detail.sum }}</div>
                  <div class="analysis-scores-subtotal clearfix">
                    <span class="bg-danger-light">@{{ detail.perScore }}</span>
                    <span class="bg-success-light">@{{ detail.matchScore }}</span>
                    <span class="bg-primary-light">@{{ detail.raceScore }}</span>
                  </div>
                </td>
                <td class="text-center" v-else>-</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="post-card" v-show="analysis.length > 0">
        <a class="collapse-analysis" data-toggle="collapse" href="#collapse3" role="button" aria-expanded="true">分數判定基準<ion-icon name="chevron-down-outline" role="img" class="md hydrated" aria-label="chevron down outline"></ion-icon></a>
        <div class="collapse" id="collapse3">
          <div class="card">
            <div class="card-header">分數算法</div>
            <div class="card-body">
              <p>如果10分以上(不含10), 兼容性很好 (+1)</p>
              <p>在5到9分的情況下, 兼容性正常或良好 (+0)</p>
              <p>4分以下(不含4), 兼容性差 (-1)</p>
            </div>
          </div>
          <div class="card">
            <div class="card-header">個性判定</div>
            <div class="card-body">
              <table>
                <tr>
                  <td></td>
                  <td v-for="type in perArray.type">
                    @{{ type }}
                  </td>
                </tr>
                <tr v-for="type in perArray.type">
                  <td>@{{ type }}</td>
                  <td v-for="detail in perArray.scoreDetail" v-if="type == detail.from">
                    @{{ detail.score }}
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="card">
            <div class="card-header">星座判定</div>
            <div class="card-body">
              <table>
                <tr>
                  <td></td>
                  <td v-for="type in matchArray.type">
                    @{{ type }}
                  </td>
                </tr>
                <tr v-for="type in matchArray.type">
                  <td>@{{ type }}</td>
                  <td v-for="detail in matchArray.scoreDetail" v-if="type == detail.from">
                    @{{ detail.score }}
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="card">
            <div class="card-header">種族判定</div>
            <div class="card-body">
              <p>--5分--</p>
              <p>1.狗和狼</p>
              <p>2.熊和小熊</p>
              <p>3.山羊和綿羊</p>
              <p>4.老虎和貓</p>
              <p>5.公牛和母牛</p>
              <p>6.無尾熊和袋鼠</p>
              <p>--3分--</p>
              <p>1.同種族</p>
              <p>2.松鼠和老鼠</p>
              <p>3.松鼠和倉鼠</p>
              <p>4.老鼠和倉鼠</p>
              <p>5.馬和鹿</p>
              <p>--2分--</p>
              <p>其他組合</p>
            </div>
          </div>
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
      animals: [],
      races: [],
      searchSelected: [],
      selected: [],
      perArray: [],
      matchArray: [],
      collapseShow: true,
      score: 0,
      sum: 0,
      good: 0,
      bad: 0,
      analysis: [],
      searchName: '',
    },
    mounted() {
      this.getAnimalsGroupRace()
      window.addEventListener('scroll', this.handleScroll)
    },
    watch: {
    },
    methods: {
      handleScroll() {
        let selected = document.getElementById("select-div");
        let sticky = selected.offsetTop

        if (window.pageYOffset > sticky) {
          selected.classList.add("fixed-selected");
        } else {
          selected.classList.remove("fixed-selected");
        }
      },
      checkShow(animal) {
        let show = false

        animal.forEach(function(val) {
          if (val.show) {
            show = true
          }
        })

        return show
      },
      getAnimalsGroupRace() {
        axios.post('/animals/getAnimalsGroupRace', {
          name: this.searchName,
         }).then((response) => {
           this.animals = response.data.lists
           this.races = response.data.races
           this.searchSelected = JSON.parse(JSON.stringify(this.races))
         })
      },
      toggleRace(race) {
        const key = this.searchSelected.indexOf(race)

        if (key == '-1') {
          //push
          this.searchSelected.push(race)
        } else {
          //add
          this.searchSelected.splice(key, 1);
        }
      },
      toggleSelected(name) {
        const key = this.selected.indexOf(name)

        if (key == '-1') {
          const num = this.selected.length

          if (num >= 10) {
            $('#warning').modal()

            return
          }
          //push
          this.selected.push(name)
        } else {
          //add
          this.selected.splice(key, 1);
        }
      },
      removeSelected(name) {
        const key = this.selected.indexOf(name)

        this.selected.splice(key, 1);
      },
      selectedCurrent(name) {
        const key = this.selected.indexOf(name)

        if (key == '-1') {
          //push
          return
        } else {
          //add
          return 'current'
        }
      },
      goAnalysis() {
        const num = this.selected.length

        if (num > 10 || num < 2) {
          $('#warning').modal()

          return
        }

        let url = '/animals/analysis?name='
        const _this = this

        this.selected.forEach(function(animal, key) {
          url += animal

          if (key < _this.selected.length - 1) {
            url += ','
          }
        })

        axios.get(url, {
         }).then((response) => {
          this.sum = response.data.resultSum
          this.score = response.data.resultScore
          this.good = response.data.good
          this.bad = response.data.bad
          this.analysis = response.data.data
          this.perArray = response.data.perArray
          this.matchArray = response.data.matchArray

          this.collapseShow = false
          $('#collapse1').removeClass('show')
         })
      },
      openAll() {
        this.searchSelected = JSON.parse(JSON.stringify(this.races))
      }
    }
  })
</script>
@endsection
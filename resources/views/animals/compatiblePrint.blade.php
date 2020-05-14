@extends('layouts.web')
@section('title', '動物森友會 動物相容性分析')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div id="print" class="container">
    <section class="post">
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
            <hr>
            <p class="mb-0">貼心提醒: <span class="text-danger">此分析僅供參考</span>, 還是有相容性高相處不好, 相容性低相處融價的情況歐~~٩(^ᴗ^)۶</p>
          </div>
          <div class="m-1" class='table-responsive'>
            <table id="test" class="table table-bordered table-analysis">
              <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th class="bg-light text-center" v-for="data in analysis">
                    <div class="analysis-scores">
                      <div>@{{ data.personality }} @{{ data.sex }}</div>
                      <div>@{{ data.constellation }} @{{ data.bd }}</div>
                      <div>@{{ data.race }}</div>
                    </div>
                  </th>
                </tr>
                <tr>
                  <th></th>
                  <th class="bg-light text-center"><h5>總分數: <strong>@{{ sum }}</strong></h5></th>
                  <th class="bg-light th-animal" v-for="data in analysis">
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
                  </th>
                </tr>
              </thead>
              <tbody>
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
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
<script>
  let app = new Vue({
    el: '#app',
    data: {
      loading: false,
      first: false,
      animals: [],
      races: [],
      racesSelected: [],
      personality: [],
      personalitySelected: [],
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
      params: "{{ $animalsName }}",
      token: "{{ $token }}",
      bottom: 80,
    },
    mounted() {
      if (this.params != '') {
        this.selected = this.params.split(",")
        $('#header').hide()
        if (this.selected.length >= 2 && this.selected.length <= 20) {
          this.goAnalysis()
        }
      }
    },
    methods: {
      goAnalysis() {
        const num = this.selected.length

        if (num > 20 || num < 2) {
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

        this.loading = true

        axios.get(url, {
         }).then((response) => {
          this.sum = response.data.resultSum
          this.score = response.data.resultScore
          this.good = response.data.good
          this.bad = response.data.bad
          this.analysis = response.data.data
          this.perArray = response.data.perArray
          this.matchArray = response.data.matchArray
          this.params = response.data.names

          this.collapseShow = false
          this.loading = false
          $('#collapse1').removeClass('show')
        })
      },
      openAll() {
        this.racesSelected = []
        this.personalitySelected = []
      },
    }
  })
</script>
@endsection
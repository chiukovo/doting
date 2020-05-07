@extends('layouts.web')
@section('title', '動物相容性分析')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/animals/list">動物相容性分析</a>
</div>
<article id="app" v-cloak>
  <div class="search">
    <table class="table">
      <tr>
        <th>種族</th>
        <td>
          <button class="btn" :class="searchSelected.indexOf(race) == '-1' ? '' : 'current'" v-for="race in races" @click="toggleRace(race)">
            @{{ race }}
          </button>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <button class="btn" @click="openAll">全部開啟</button>
          <button class="btn" @click="searchSelected = []">全部隱藏</button>
        </td>
      </tr>
    </table>
  </div>
  <section class="animals-info">
    <div class="media">
      <div class="media-body">
        <div class="media-card" v-for="(animal, race) in animals" v-show="searchSelected.indexOf(race) != '-1'">
          <div class="media-card-title">@{{ race }}</div>
          <ul class="media-card-list">
            <li v-for="detail in animal">
              <span>@{{ detail.name }}</span>
              <div class="table-img">
                <img :src="'/animal/icon/' + detail.name + '.png'" :alt="detail.name">
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  @include('layouts.goTop')
</article>
<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      animals: [],
      races: [],
      searchSelected: [],
    },
    mounted() {
      this.getAnimalsGroupRace()
    },
    methods: {
      getAnimalsGroupRace() {
        axios.post('/animals/getAnimalsGroupRace', {
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
      openAll() {
        this.searchSelected = JSON.parse(JSON.stringify(this.races))
      }
    }
  })
</script>
@endsection
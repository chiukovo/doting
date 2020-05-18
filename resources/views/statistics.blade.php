@extends('layouts.web')
@section('title', '豆丁搜尋排行榜')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">豆丁搜尋排行榜</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">豆丁搜尋排行榜</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section>
      <div class="row">
        <div class="col">
          <ul>
            <li>僅收集豆丁有搜尋出來的資料來做統計 5秒更新一次</li>
            <li class="text-danger">私人訊息等資訊, 豆丁並不會紀錄 請放心 m(_ _)m</li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <table class="table table-bordered table-hover text-center">
            <thead>
              <tr>
                <th scope="col">名次</th>
                <th scope="col">關鍵字</th>
                <th scope="col">次數</th>
                <th scope="col">得獎感言</th>
              </tr>
            </thead>
            <tbody class="list-rank">
              <tr v-for="(list, key) in lists">
                <td>
                  <span class="list-rank-num" :class="'list-rank-num' + (key + 1)" style="margin: auto;">@{{ key + 1}}</span>
                </td>
                <td>
                  <div v-if="list.url != ''">
                    <a :href="list.url" target="_blank">
                      <span>@{{ list.text }}</span>
                      <div class="table-img">
                        <img :src="list.img" :alt="list.text">
                      </div>
                    </a>
                  </div>
                  <div v-else>
                    <span>@{{ list.text }}</span>
                  </div>
                </td>
                <td>@{{ list.number }}</td>
                <td>@{{ list.comment }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
  @include('layouts.goTop')
</div>

<script>
  new Vue({
    el: '#app',
    data: {
      lists: [],
    },
    mounted() {
      this.getData()

      window.setInterval(( () => this.getData() ), 5000)
    },
    methods: {
      getData($state) {
        axios.post('/statistics/getData', {
         }).then((response) => {
            this.lists = response.data
         })
      },
    }
  })
</script>
@endsection
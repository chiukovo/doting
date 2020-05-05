@extends('layouts.web')
@section('title', '搜尋統計')
@section('content')
<div class="breadcrumbs">
  <a href="/">首頁</a>
  <span class="sep">/</span>
  <a href="/statistics">搜尋統計</a>
</div>
<div id="app" class="media" v-clock>
  <table class="media-card table">
    <tr>
      <th>排名</th>
      <th>搜尋名稱</th>
      <th>次數</th>
    </tr>
    <tr v-for="(list, key) in lists">
      <td>@{{ key + 1 }}</td>
      <td>
        <div v-if="list.url != ''">
          <a :href="list.img" target="_blank">
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
    </tr>
  </table>
  <div class="media-card">
    <div class="media-card-title">保護個資說明</div>
    <ul class="media-list">
      <li>僅收集豆丁有搜尋出來的資料來做統計 5秒更新一次</li>
      <li><span style="color: red">私人訊息等資訊, 豆丁並不會紀錄 請放心</span></li>
    </ul>
  </div>
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
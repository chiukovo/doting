@extends('layouts.admin')

@section('title', '動物圖鑑')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container-fluid">
    <h2 class="content-title">動物居民</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/{{env('ADMIN_PREFIX')}}">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">動物居民</li>
      </ol>
    </nav>
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
              <button class="btn btn-success" native-type="submit" @click.prevent="location.href='/{{env('ADMIN_PREFIX')}}/animals/add'">新增</button>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <table class="table table-bordered table-hover text-center">
            <thead>
              <tr>
                <th scope="col">名稱</th>
                <th scope="col" v-show="!isMobile()">性別</th>
                <th scope="col">個性</th>
                <th scope="col">種族</th>
                @if($type != 'npc')
                <th scope="col">生日</th>
                <th scope="col" v-show="!isMobile()">口頭禪</th>
                <th scope="col">狀態</th>
                @endif
              </tr>
            </thead>
            <tbody>
              <tr v-for="list in lists">
                <td scope="row">
                  <a class="link" :href="'/{{env('ADMIN_PREFIX')}}/animals/detail?name=' + list.name">
                    <div class="table-img" v-if="list.info == null">
                      <img :src="(list.avatar_url) ? '/'+list.avatar_url : '/animal/icon/' + list.name + '.png'" :alt="list.name">
                    </div>
                    <div class="table-img" v-else>
                      <img :src="(list.avatar_url) ? '/'+list.avatar_url : '/animal/' + list.name + '.png'" :alt="list.name">
                    </div>
                    <span>@{{ list.name }}</span>
                  </a>
                </td>
                <td>@{{ list.race }}</td>
                <td v-show="!isMobile()">@{{ list.personality }}</td>
                <td>@{{ list.race }}</td>
                @if($type != 'npc')
                <td>@{{ list.bd }}</td>
                <td v-show="!isMobile()">@{{ list.say }}</td>
                @endif

                <td :style="(list.status == 1) ? '' : { color: 'red' }">@{{ (list.status == 1) ? '啟用' : '停用' }}</td>
              </tr>
            </tbody>
          </table>

          <div style="float: right;" v-show="lists.length">
              <ul class="pagination">

                <li class="page-item" v-show="page > 5">
                  <a class="page-link" href="javascript:void(0)" @click="defaultPage(1)">1..</a>
                </li>

                <li class="page-item">
                  <a class="page-link" href="javascript:void(0)" aria-label="Previous" @click="defaultPage(page-1)">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>

                <li class="page-item" v-for="i in page_num" v-if="i>0" :class="i==page ? 'active' : ''" v-show="showPage(i)">
                  <a class="page-link" href="javascript:void(0)" @click="defaultPage(i)">@{{ i }}</a>
                </li>

                <li class="page-item">
                  <a class="page-link" href="javascript:void(0)" aria-label="Next" @click="defaultPage(page+1)">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                  </a>
                </li>

                <li class="page-item" v-show="page_num - page > 5">
                  <a class="page-link" href="javascript:void(0)" @click="defaultPage(page_num)">..@{{ page_num }}</a>
                </li>
              </ul>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
<script>

  new Vue({
    el: '#app',
    data: {
      lists: [],
      page: 1,
      race: [],
      personality: [],
      bd: [],
      type: "{{ $type }}",
      moreSearch: false,
      total_num: 0,
      per_page: 0,
      page_num: 0,
      admin_prefix: "{{ env('ADMIN_PREFIX') }}",
      searchData: {
        race: [],
        personality: [],
        bd: [],
        text: "{{ $text }}",
      }
    },
    mounted() {
      this.getAllType()
      this.search()
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      getAllType() {
        axios.get('/animals/getAllType?type=' + this.type, {
         }).then((response) => {
           this.race = response.data.race
           this.personality = response.data.personality
           this.bd = response.data.bd
         })
      },
      search() {
        axios.post(`/${this.admin_prefix}/animals/search`, {
           page: this.page,
           race: this.searchData.race,
           personality: this.searchData.personality,
           bd: this.searchData.bd,
           text: this.searchData.text,
           type: this.type
         }).then((response) => {
           let animals = response.data
           if (animals.data.length) {
             //this.page += 1;
             this.lists.push(...animals.data);
           }
           this.total_num = Number(animals.total);
           this.per_page = Number(animals.per_page);
           this.page_num = Math.ceil(this.total_num/this.per_page);
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
      defaultPage(page){
        if(page <= 0){
          page = 1;
        }

        if(page > this.page_num){
          page = this.page_num;
        }

        this.page = page;
        this.lists = [];
        this.search();
      },
      searchDefault() {
        this.page = 1;
        this.lists = [];
        this.search();
      },
      checkAllCurrent() {
        if (this.searchData.race.length == 0 && this.searchData.personality.length == 0 && this.searchData.bd.length == 0) {
          return 'current'
        }
      },
      showPage(page){
        if(this.page <= 5 && page  > 10){
          return false;
        }
        if(this.page > 5 && page - this.page > 5){
          return false;
        }
        if(this.page > 5 && this.page - page >= 5){
          return false;
        }
        return true;
      }
    }
  })
</script>
@endsection
@extends('layouts.web')
@section('title', '島民資訊')
@section('content')

<!-- cleave.js -->
<script src="/js/cleave.js"></script>
<!-- Lastly add this package -->
<script src="/js/vue-cleave-component.js"></script>

<div id="app" class="content-wrap" v-cloak>
  <section class="post">
    <div class="container">
      <div class="row justify-content-md-center mt-3 mb-5">
        <div class="col-12 col-md-10  col-lg-8">
          <div class="passport-warp">
            <div class="passport-header">
              <span>PASSPORT</span>
            </div>
            <div class="passport-body row">
              <div class="passport-info-img text-center col-12 col-sm-4">
                <img :src="info.picture_url" class="img-fluid bg-light border rounded p-2" v-if="info.picture_url != ''">
              </div>
              <ul class="passport-info col-12 col-sm-8" v-if="!is_edit">
                <li>
                  <div class="passport-info-item passport-sw">
                    <div class="label">護照號碼</div>
                    <div class="data" v-if="info.passport != ''">
                      SW-@{{ info.passport }}
                    </div>
                    <div class="data" v-else>
                      尚未填寫 哇耶
                    </div>
                  </div>
                  <button class="btn btn-sm btn-default btn-outline-secondary" @click="is_edit = true">編輯</button>
                </li>
                <li>
                  <div class="passport-info-item-group">
                    <div class="passport-info-item">
                      <div class="label">暱稱</div>
                      <div class="data" v-if="info.nick_name != ''">@{{ info.nick_name }}</div>
                      <div class="data" v-else>尚未填寫 哇耶</div>
                    </div>
                    <div class="passport-info-item">
                      <div class="label">島花</div>
                      <div class="data" v-if="info.flower != ''">@{{ info.flower }}</div>
                      <div class="data" v-else>尚未填寫 哇耶</div>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item-group">
                    <div class="passport-info-item">
                      <div class="label">島名</div>
                      <div class="data" v-if="info.island_name != ''">@{{ info.island_name }}島</div>
                      <div class="data" v-else>尚未填寫 哇耶</div>
                    </div>
                    <div class="passport-info-item">
                      <div class="label">特產</div>
                      <div class="data">@{{ info.fruit_name }}</div>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item">
                    <div class="label">所屬半球</div>
                    <div class="data">@{{ info.position_name }}</div>
                  </div>
                  <div class="passport-info-tip">
                    <span>選擇所屬半球，只要登入就會將您的條件自動帶入。</span>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item">
                    <div class="label">介紹</div>
                    <div class="data" v-if="info.info != ''">@{{ info.info }}</div>
                    <div class="data" v-else>尚未填寫 哇耶</div>
                  </div>
                </li>
              </ul>
              <ul class="passport-info col-12 col-sm-8" v-else>
                <li>
                  <div class="passport-info-item passport-sw">
                    <div class="label">護照號碼</div>
                    <div class="data">
                      <span>SW-</span><cleave class="form-control form-control-sm" v-model="info.passport" :options="options" style="width: 120px;display: unset;"></cleave>
                    </div>
                  </div>
                  <button class="btn btn-sm btn-primary" @click="saveUserInfo">儲存</button>
                </li>
                <li>
                  <div class="passport-info-item-group">
                    <div class="passport-info-item">
                      <div class="label">暱稱</div>
                      <div class="data">
                        <input type="text" class="form-control form-control-sm" v-model="info.nick_name">
                      </div>
                    </div>
                    <div class="passport-info-item">
                      <div class="label">島花</div>
                      <div class="data">
                        <input type="text" class="form-control form-control-sm" v-model="info.flower">
                      </div>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item-group">
                    <div class="passport-info-item">
                      <div class="label">島名</div>
                      <div class="data"><input type="text" class="form-control form-control-sm" v-model="info.island_name"></div>
                    </div>
                    <div class="passport-info-item">
                      <div class="label">特產</div>
                      <div class="data">
                        <select class="form-control form-control-sm" v-model="info.fruit">
                          <option value="0">請選擇</option>
                          <option value="1">桃子</option>
                          <option value="2">蘋果</option>
                          <option value="3">梨子</option>
                          <option value="4">櫻桃</option>
                          <option value="5">橘子</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item">
                    <div class="label">所屬半球</div>
                    <div class="data">
                      <select class="form-control form-control-sm" v-model="info.position">
                        <option value="0">請選擇</option>
                        <option value="1">南半球</option>
                        <option value="2">北半球</option>
                      </select>
                    </div>
                  </div>
                  <div class="passport-info-tip">
                    <span>選擇所屬半球，只要登入就會將您的條件自動帶入。</span>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item">
                    <div class="label">介紹</div>
                    <div class="data">
                      <input type="text" class="form-control form-control-sm" v-model="info.info">
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="passport-footer">
              <div class="passport-join">
                <label class="passport-join-title">登陸日</label>
                <span>@{{ info.date }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-md-center mb-5">
        <div class="col-12 col-md-10  col-lg-8">
          <div class="user-wrap">
            <div class="user-header">
              <h4 v-if="info.island_name != ''">@{{ info.island_name }}的大頭菜</h4>
              <h4 v-else>我的大頭菜</h4>
              <small v-if="info.island_name != ''">Residents of @{{ info.island_name }}</small>
              <small v-else>Residents of My</small>
            </div>
            <div class="user-body">
              <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <label>
                      菜價紀錄:
                      <select class="form-control form-control-sm" v-if="historyCai.length > 0" style="    display: inline-block; width: 180px;" @change="changeCaiDate" v-model="changeStart">
                        <option :value="cai.start" v-for="cai in historyCai">@{{ cai.start }} ~ @{{ cai.end}}</option>
                      </select>
                      <span v-else>@{{ start }} ~ @{{ end }}</span>
                      <span class="text-danger" v-if="hasMoney">
                        (有發財機會 請好好把握 ٩(●˙▿˙●)۶…⋆ฺ)
                      </span>
                      <span class="text-danger" v-if="noMoney">
                        (絕對虧錢啊啊啊啊 இдஇ)
                      </span>
                    </label>
                    <button class="btn btn-sm btn-default" v-if="!is_edit_cai" @click="is_edit_cai = !is_edit_cai">編輯</button>
                    <div class="button-group" v-else>
                      <button class="btn btn-sm btn-default" @click="clearCai">還原</button>
                      <button class="btn btn-sm btn-primary" @click="saveUserCai">儲存</button>
                    </div>
                  </div>
                </div>
                <div class="card-body rounded p-0" v-if="caiData.length > 0">
                  <div class="dish">
                    <div class="dish-header">
                      <ul class="dish-list">
                        <li>
                          <div class="item-group">
                            <div class="dish-list-item">
                              <label class="item-label">
                                @{{ caiData[0][0] }} <small>(@{{ start }})</small> 大頭菜購買價格
                              </label>
                              <div class="item-body">
                                <input type="number" class="form-control" placeholder="$" v-if="is_edit_cai" v-model="caiData[0][1]">
                                <span class="price" v-else>@{{ caiData[0][1] }}</span>
                              </div>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <ul class="dish-list">
                      <li v-for="(cai, key) in caiData" v-if="key != 0">
                        <div class="item-group">
                          <div class="dish-list-item" v-for="(dt, num) in cai" v-if="num != 0">
                            <label class="item-label" v-if="num == 1">@{{ caiData[key][0] }}上午</label>
                            <label class="item-label" v-else>@{{ caiData[key][0] }}下午</label>
                            <div class="item-body">
                              <input type="number" class="form-control" placeholder="$" v-if="is_edit_cai" v-model="caiData[key][num]">
                              <span class="price" v-else>@{{ caiData[key][num] }}</span>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="card-footer bg-white">
                  <h5 class="mb-0">菜價趨勢： <b>@{{ caiResult }}</b></h5>
                </div>
                <ul class="list-group list-group-flush list-custom test-sm mt-3">
                  <li class="list-group-item">
                    <label class="list-custom-label">波型</label>
                    <span>賣價隨機，最大值0.9 ~ 1.4倍</span>
                  </li>
                  <li class="list-group-item">
                    <label class="list-custom-label">遞減型</label>
                    <span>價格只會越來越低 (絕對會賠)</span>
                  </li>
                  <li class="list-group-item">
                    <label class="list-custom-label">3期型</label>
                    <span>價格遞減，發生「變調」後，2期的價格為1.4倍以上，會在第3期出現頂峰，<mark>賣價2 ~ 6倍 (即$180 ～ $660之間) </mark></span>
                  </li>
                  <li class="list-group-item">
                    <label class="list-custom-label">4期型</label>
                    <span>價格遞減，發生「變調」後，2期的價格不到1.4倍，會在第4期出現頂峰，賣價1.4~2倍</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-md-center mb-5">
        <div class="col-12 col-md-10  col-lg-8">
          <div class="user-wrap">
            <div class="user-header">
              <h4 v-if="info.island_name != ''">@{{ info.island_name }}的島民</h4>
              <h4 v-else>我的島民</h4>
              <small v-if="info.island_name != ''">Residents of @{{ info.island_name }}</small>
              <small v-else>Residents of My</small>
            </div>
            <div class="user-body">
              <ul class="nav nav-tabs nav-fill" role="tablist" v-if="animalLike.length != 0 || animalTrack.length != 0">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#animals1">擁有(@{{ animalLike.length }})</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#animals2">追蹤(@{{ animalTrack.length }})</a>
                </li>
              </ul>
              <div class="tab-content" v-if="animalLike.length != 0 || animalTrack.length != 0">
                <div class="tab-pane fade show active" id="animals1">
                  <ul class="card-list">
                    <li v-for="list in animalLike" @click="goDetail(list)">
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img :src="'/animal/icon/' + list.name + '.png'" :alt="list.name">
                        </div>
                        <div class="card-list-title">@{{ list.name }} @{{ list.sex }}</div>
                        <div class="card-list-info">@{{ list.personality }} / @{{ list.race }} / @{{ list.bd }}</div>
                      </div>
                    </li>
                  </ul>
                  <div class="text-center">
                    <a :href="compatibleUrl" class="btn btn-primary btn-sm m-2 text-white">我的島民相容性分析</a>
                  </div>
                </div>
                <div class="tab-pane fade" id="animals2">
                  <ul class="card-list">
                    <li v-for="list in animalTrack" @click="goDetail(list)">
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img :src="'/animal/icon/' + list.name + '.png'" :alt="list.name">
                        </div>
                        <div class="card-list-title">@{{ list.name }} @{{ list.sex }}</div>
                        <div class="card-list-info">@{{ list.personality }} / @{{ list.race }} / @{{ list.bd }}</div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-md-center">
        <div class="col-12 col-md-10  col-lg-8">
          <div class="user-wrap">
            <div class="user-header">
              <h4>收藏</h4>
              <small>Collection</small>
            </div>
            <div class="user-body">
              <ul class="card-list user-card-list">
                <li v-for="list in itemsData">
                  <div class="card-list-item">
                    <div class="card-list-img" @click="goHref(list.href, '')">
                      <img :src="list.imgUrl" class="img-fluid" :alt="list.name">
                    </div>
                    <div class="card-list-title">@{{ list.name }}</div>
                    <div class="card-list-btn">
                      <ul class="user-save-btn">
                        <li @click="goHref(list.href, 'track')">
                          <button class="btn btn-sm btn-outline-danger"><div>@{{ list.track }}</div>追蹤
                        </button>
                        </li>
                        <li @click="goHref(list.href, 'like')">
                          <button class="btn btm-sm btn-outline-success"><div>@{{ list.like }}</div>@{{ list.has }}</button>
                        </li>
                      </ul>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="m-2">
            @include('layouts.ads2')
          </div>
        </div>
      </div>
    </div>
  </section>
  @include('layouts.goTop')
</div>

<script>
  Vue.use(GoTop);
  Vue.use(VueCleave);
  new Vue({
    el: '#app',
    data: {
      is_edit: false,
      is_edit_cai: false,
      info: [],
      itemsData: [],
      animalLike: [],
      animalTrack: [],
      compatibleUrl: '',
      options: {
        blocks: [4, 4, 4],
        delimiter: '-'
      },
      caiData: [],
      historyCai: [],
      caiResult: '-',
      sourceCaiData: [],
      trend: 0,
      start: '',
      changeStart: '',
      end: '',
      hasMoney: false,
      noMoney: false,
    },
    mounted() {
      this.getUserInfo()
    },
    watch: {
      caiData: {
        handler(newVal, oldVal) {
          //計算大頭菜波型
          let number = 0
          let target1 = parseInt(newVal[0][1])
          let target2 = parseInt(newVal[1][1])
          this.hasMoney = false
          this.noMoney = false

          if (!isNaN(target1) && !isNaN(target2)) {
            number = target2 / target1

            if (number >= 0.9) {
              this.caiResult = this.check21(target1, newVal)
            }

            if (number < 0.9 && number >= 0.85) {
              this.caiResult = this.check3(target1, newVal)
            }

            if (number < 0.85 && number >= 0.8) {
              this.caiResult = '第四期型'
            }

            if (number < 0.8 && number >= 0.6) {
              this.caiResult = '第四期型(機率較高)or波型'
            }

            if (number < 0.6) {
              this.caiResult = '第四期型'
            }
          } else {
            this.caiResult = '-'
          }
        },
      },
    },
    methods: {
      changeCaiDate() {
        this.getUserInfo()
      },
      check21(base, target) {
        //周一下午
        let target1 = parseInt(target[1][2])
        //周二上午
        let target2 = parseInt(target[2][1])

        if (!isNaN(target1) && !isNaN(target2)) {
          let check1 = base * 0.8 <= target1 ? 1 : 0
          let check2 = base * 1.4 >= target2 ? 1 : 0

          if (check1 || check2) {
            return '波型'
          }

          if (!check1 && !check2) {
            return '第四期型'
          }
        } else {
          return '第四期型or波型(機率較高)'
        }
      },
      check3(base, target) {
        //周四下午
        let target4 = parseInt(target[4][2])

        if (!isNaN(target4)) {
          if (target4 >= base) {
            this.hasMoney = true
            return '第四期型or第三期型'
          } else {
            this.noMoney = true
            return '遞減型'
          }
        }

        this.hasMoney = true
        return '第四期型or第三期型or遞減型'
      },
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      goDetail(list) {
        location.href = '/animals/detail?name=' + list.name
      },
      getUserInfo() {
        axios.get('/user/info', {
          params: {
            changeStart: this.changeStart
          }
         }).then((response) => {
          const result = response.data

          if (result.code == -1) {
            location.href = '/'
          }

          if (result.code == 1) {
            this.info = result.data
            this.itemsData = result.itemsData
            this.animalLike = result.animalInfo.like
            this.animalTrack = result.animalInfo.track
            this.compatibleUrl = result.compatibleUrl
            this.caiData = result.caiData
            this.historyCai = result.historyCai
            this.start = result.start
            this.changeStart = result.start
            this.end = result.end
            this.sourceCaiData = JSON.parse(JSON.stringify(this.caiData))
          }
        })
      },
      clearCai() {
        this.caiData = this.sourceCaiData
      },
      saveUserCai() {
        axios.post('/user/cai/save', {
          caiData: this.caiData,
          caiResult: this.caiResult,
          start: this.start,
          end: this.end,
         }).then((response) => {

        })

         this.is_edit_cai = !this.is_edit_cai
      },
      saveUserInfo() {
        axios.post('/user/save', {
          info: this.info
         }).then((response) => {
          const result = response.data

          if (result.code == -1) {
            location.href = '/'
          }

          if (result.code == -2) {
            alert(result.msg)
          }

          if (result.code == 1) {
            this.is_edit = false
            this.getUserInfo()
          }
        })
      },
      goHref(href, target) {
        location.href = href + target
      },
    }
  })
</script>
@endsection
@extends('layouts.web')
@section('title', '島民資訊')
@section('content')
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
                    <div class="data">@{{ info.passport }}</div>
                  </div>
                  <button class="btn btn-sm btn-default btn-outline-secondary" @click="is_edit = true">編輯</button>
                </li>
                <li>
                  <div class="passport-info-item-group">
                    <div class="passport-info-item">
                      <div class="label">暱稱</div>
                      <div class="data">@{{ info.nick_name }}</div>
                    </div>
                    <div class="passport-info-item">
                      <div class="label">島花</div>
                      <div class="data">@{{ info.flower }}</div>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="passport-info-item-group">
                    <div class="passport-info-item">
                      <div class="label">島名</div>
                      <div class="data">@{{ info.island_name }}島</div>
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
                    <div class="data">@{{ info.info }}</div>
                  </div>
                </li>
              </ul>
              <ul class="passport-info col-12 col-sm-8" v-else>
                <li>
                  <div class="passport-info-item passport-sw">
                    <div class="label">護照號碼</div>
                    <div class="data"><input type="text" class="form-control form-control-sm" v-model="info.passport"></div>
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
              <h4 v-if="info.island_name != ''">@{{ info.island_name }}的居民</h4>
              <h4 v-else>我的居民</h4>
              <small v-if="info.island_name != ''">Residents of @{{ info.island_name }}</small>
              <small v-else>Residents of My</small>
            </div>
            <div class="user-body">
              <ul class="nav nav-tabs nav-fill" role="tablist" v-if="animalLike.length != 0 && animalTrack.length != 0">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#animals1">擁有(@{{ animalLike.length }})</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#animals2">追蹤(@{{ animalTrack.length }})</a>
                </li>
              </ul>
              <div class="tab-content" v-if="animalLike.length != 0 && animalTrack.length != 0">
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
        </div>
      </div>
    </div>
  </section>
  @include('layouts.goTop')
</div>

<script>
  Vue.use(GoTop);
  new Vue({
    el: '#app',
    data: {
      is_edit: false,
      info: [],
      itemsData: [],
      animalLike: [],
      animalTrack: [],
    },
    mounted() {
      this.getUserInfo()
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
      },
      goDetail(list) {
        location.href = '/animals/detail?name=' + list.name
      },
      getUserInfo() {
        axios.get('/user/info', {
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
          }
        })
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
      }
    }
  })
</script>
@endsection
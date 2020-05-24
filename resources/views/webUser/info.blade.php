@extends('layouts.web')
@section('title', '島民資訊')
@section('content')
<div id="app" class="content-wrap">
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
                <img :src="info.picture_url" class="img-fluid bg-light border rounded p-2">
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
                      <div class="label">島名</div>
                      <div class="data">@{{ info.island_name }}</div>
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
              <h4>OAO島的居民</h4>
              <small>Residents of OAO</small>
            </div>
            <div class="user-body">
              <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#animals1">擁有(2)</a>
                  <!-- 上限10 -->
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#animals2">追蹤(8)</a>
                  <!-- 無上限？ -->
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade show active" id="animals1">
                  <ul class="card-list">
                    <li>
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img src="../animal/icon/茶茶丸.png" class="img-fluid" alt="茶茶丸">
                        </div>
                        <div class="card-list-title">茶茶丸 ♂</div>
                        <div class="card-list-info">運動/綿羊/3.18</div>
                        <div class="card-list-btn">
                          <ul class="user-save-btn">
                            <li><button class="btn btn-outline-secondary current"><i class="fas fa-heart"></i>取消擁有</button></li>
                          </ul>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img src="../animal/icon/彭花.png" class="img-fluid" alt="彭花">
                        </div>
                        <div class="card-list-title">茶茶丸 ♂</div>
                        <div class="card-list-info">運動/綿羊/3.18</div>
                        <div class="card-list-btn">
                          <ul class="user-save-btn">
                            <li><button class="btn btn-outline-secondary current"><i class="fas fa-heart"></i>取消擁有</button></li>
                          </ul>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img src="../animal/icon/阿一.png" class="img-fluid" alt="阿一">
                        </div>
                        <div class="card-list-title">茶茶丸 ♂</div>
                        <div class="card-list-info">運動/綿羊/3.18</div>
                        <div class="card-list-btn">
                          <ul class="user-save-btn">
                            <li><button class="btn btn-outline-secondary current"><i class="fas fa-heart"></i>取消擁有</button></li>
                          </ul>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img src="../animal/icon/雪美.png" class="img-fluid" alt="雪美">
                        </div>
                        <div class="card-list-title">茶茶丸 ♂</div>
                        <div class="card-list-info">運動/綿羊/3.18</div>
                        <div class="card-list-btn">
                          <ul class="user-save-btn">
                            <li><button class="btn btn-outline-secondary current"><i class="fas fa-heart"></i>取消擁有</button></li>
                          </ul>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img src="../animal/icon/茶茶丸.png" class="img-fluid" alt="茶茶丸">
                        </div>
                        <div class="card-list-title">茶茶丸 ♂</div>
                        <div class="card-list-info">運動/綿羊/3.18</div>
                        <div class="card-list-btn">
                          <ul class="user-save-btn">
                            <li><button class="btn btn-outline-secondary current"><i class="fas fa-heart"></i>取消擁有</button></li>
                          </ul>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="tab-pane fade" id="animals2">
                  <ul class="card-list">
                    <li>
                      <div class="card-list-item">
                        <div class="card-list-img">
                          <img src="../animal/icon/茶茶丸.png" class="img-fluid" alt="茶茶丸">
                        </div>
                        <div class="card-list-title">茶茶丸 ♂</div>
                        <div class="card-list-info">運動/綿羊/3.18</div>
                        <div class="card-list-btn">
                          <ul class="user-save-btn">
                            <li><button class="btn btn-outline-secondary current"><i class="fas fa-bookmark"></i>取消追蹤</button></li>
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
      </div>
      <div class="row justify-content-md-center">
        <div class="col-12 col-md-10  col-lg-8">
          <div class="user-wrap">
            <div class="user-header">
              <h4>收藏</h4>
              <small>Collection</small>
            </div>
            <div class="user-body">
              <ul class="user-list">
                <li>
                  <a href="#" class="user-item">
                    <img src="../other/鯊魚.png" class="img-fluid">
                    <span class="user-item-title">0 / 80</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="user-item">
                    <img src="../other/大白斑蝶.png" class="img-fluid">
                    <span class="user-item-title">0 / 80</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="user-item">
                    <img src="../itemsNew/雨衣_0.png" class="img-fluid">
                    <span class="user-item-title">0 / 80</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="user-item">
                    <img src="../itemsNew/大熊熊_20.png" class="img-fluid">
                    <span class="user-item-title">0 / 80</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="user-item">
                    <img src="../kk/Hypno_K.K..png" class="img-fluid">
                    <span class="user-item-title">0 / 20</span>
                  </a>
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
    },
    mounted() {
      this.getUserInfo()
    },
    methods: {
      isMobile(){
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        return flag;
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
          }
        })
      }
    }
  })
</script>
@endsection
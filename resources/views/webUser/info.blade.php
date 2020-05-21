@extends('layouts.web')
@section('title', '島民資訊')
@section('content')
<div class="content-wrap">
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
                <img src="/other/鱘魚.png" class="img-fluid bg-light border rounded p-2">
              </div>
              <ul class="passport-info col-12 col-sm-8">
                <li class="passport-sw">
                  <span>SW-1234-1234-1234</span>
                </li>
                <li class="passport-info-top passport-hr">
                  <div class="passport-info-island">OAO島</div>
                  <div class="passport-info-fruit">桃子</div>
                </li>
                <li class="passport-info-name passport-hr">豆丁</li>
                <li class="passport-info-birthday">1月21日出生</li>
              </ul>
            </div>
            <div class="passport-footer">
              <div class="passport-join">
                <label class="passport-join-title">登陸日</label>
                <span>2020年3月26日</span>
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
</div>
@endsection
@extends('layouts.web')
@section('title', '豆丁更新資訊')
@section('content')
<div id="app" class="content-wrap" v-cloak>
  <div class="container">
    <h2 class="content-title">更新資訊</h2>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">更新資訊</li>
      </ol>
    </nav>
    @include('layouts.ads')
    <section class="post version">
      <div class="row justify-content-md-center">
        <div class="col">
          <div class="post-body">
            <div class="alert alert-primary mt-2" role="alert">
              <h4 class="alert-heading">問題回復專區</h4>
              <p class="mb-0">在意見回饋的問題都可以在這邊查詢並得到回復，感謝您的回饋，我們會盡力改善且盡可能幫助大家。</p>
              <a href="https://reurl.cc/9ER9ya" target="_blank">https://reurl.cc/9ER9ya</a>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.9.0</div>
                <div class="card-header-date">2020/5/26</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>新增 登入功能 收藏 擁有</li>
                  <li>新增 我的島民</li>
                  <li>新增 我的護照</li>
                  <li>會找時間再寫一篇教學文章><</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.8.0</div>
                <div class="card-header-date">2020/5/20</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>優化UI</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.7.7</div>
                <div class="card-header-date">2020/5/19</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>新增動物列表卡片模式</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.7.0</div>
                <div class="card-header-date">2020/5/14</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>
                    新增在LINE裡面也能分析相容性 使用【#】前贅字 動物用空白隔開<br>
                    ex: #阿一 阿二 阿三 阿四<br>
                    ex: #茶茶丸 傑客 美玲 小潤 章丸丸 草莓
                  </li>
                  <li>修正圖片錯誤問題</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.6.5</div>
                <div class="card-header-date">2020/5/12</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>
                    修正一堆BUG QAQ
                  </li>
                  <li>
                    優化UI<br>
                  </li>
                  <li>
                    新增魚圖鑑　昆蟲圖鑑詳細頁面
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.6.0</div>
                <div class="card-header-date">2020/5/10</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>
                    網站UI全新更新<br>
                    新增動物相容度分析<br>
                    <a href="https://doting.tw/animals/compatible">https://doting.tw/animals/compatible</a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.5.3</div>
                <div class="card-header-date">2020/5/8</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>新增 <span class="text-danger">雨傘</span> 查詢 關鍵字【找】<br>範例: 找童話風雨傘</li>
                  <li>新增 <span class="text-danger">地墊</span> 查詢 關鍵字【找】<br>範例: 找活潑媽媽廚房地墊</li>
                  <li>新增 <span class="text-danger">地板</span> 查詢 關鍵字【找】<br>範例: 找楓葉地板</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.5.2</div>
                <div class="card-header-date">2020/5/7</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>
                    新增豆丁搜尋排行榜<br>
                    範例: 搜尋排行榜<br>
                    就會出現連結: <a href="https://doting.tw/statistics">https://doting.tw/statistics</a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.5.1</div>
                <div class="card-header-date">2020/5/6</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>動物列表圖片調整成ICON顯示</li>
                  <li>動物個性相容度功能 <span class="text-danger">(開發中)</span></li>
                  <li>大頭菜統計計算 <span class="text-danger">(開發中)</span></li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.5.0</div>
                <div class="card-header-date">2020/5/6</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>新增動物相同種族</li>
                  <li>新增DIY也能用從材料反搜索DIY方程式<br>
                    範例: 找硬木材</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.4.0</div>
                <div class="card-header-date">2020/4/30</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>修改成所有卡片都能點擊連到網站</li>
                  <li>移除動物 藝術品卡片詳細按鈕, 改成直接點擊卡片即可</li>
                  <li>新增網站-植物</li>
                  <li>新增網站-唱片</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.3.0</div>
                <div class="card-header-date">2020/4/29</div>
              </div>
              <div class="card-body">
                <p class="text-danger">不好意思 今日豆丁被攻擊導致伺服器中斷<br>目前已復活嚕 哇耶!</p>
                <ul class="list-decimal">
                  <li>
                    新增植物圖鑑 + 植物合成配方<br>
                    範例: 找金色玫瑰<br>
                    <img src="/update/20200429-1.jpg" class="img-fluid"><br>
                    範例: 找柊樹幼苗<br>
                    <img src="/update/20200429-2.jpg" class="img-fluid"><br>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.2.0</div>
                <div class="card-header-date">2020/4/28</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>
                    新增博物館 查化石
                    範例: 化石 暴龍<br>
                    <img src="/update/20200428-1.jpg" class="img-fluid">
                  </li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.1.0</div>
                <div class="card-header-date">2020/4/26</div>
              </div>
              <div class="card-body">
                <ul class="list-decimal">
                  <li>
                    新增博物館 (藝術品查詢)<br>
                    範例: 查充滿母愛的雕塑<br>
                    <img src="/update/20200427-1.jpg" class="img-fluid"><br>
                    <img src="/update/20200427-2.jpg" class="img-fluid"><br>
                    點選查看詳情<br>
                    <img src="/update/20200427-3.jpg" class="img-fluid"><br>
                  </li>
                  <li>新增DIY 圖片</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v3.0.0</div>
                <div class="card-header-date">2020/4/26</div>
              </div>
              <div class="card-body">
                <p class="text-danger">豆丁森友會 網頁版上線拉 ｡:.ﾟヽ(*´∀`)ﾉﾟ.:｡</p>
                <ul class="list-decimal">
                  <li>新增動物細節 (居家照片, 喜愛唱片)</li>
                  <li>優化搜尋</li>
                </ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <div class="card-header-title">更新 v2.0.0</div>
                <div class="card-header-date">2020/4/20</div>
              </div>
              <div class="card-body">
                <p class="text-danger">優化UI 改用卡片顯示方式 更方便閱讀<br>
                  <img src="/update/20200420-1.png" class="img-fluid">
                </p>
                <ul class="list-decimal">
                  <li>優化UI</li>
                  <li>
                    新增 "種族" "個性" 查詢<br>
                    種族範例: #貓<br>
                    個性範例: #運動<br>
                  </li>
                  <li>
                    新增 "英文" "日文" 查詢<br>
                    範例: #boone<br>
                    範例: #ポンチョ<br>
                  </li>
                  <li>
                    新增 "生日" 查詢 (只有月份)<br>
                    範例: #1<br>
                    範例: #2<br>
                    範例: #3<br>
                    或是精準查詢<br>
                    範例: #3.14<br>
                    範例: #7.03<br>
                  </li>
                  <li>新增 性別</li>
                </ul>
              </div>
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
  })
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-136875596-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-136875596-3');
</script>
@endsection
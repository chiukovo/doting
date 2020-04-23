@extends('layouts.web')

@section('content')
<div class="breadcrumbs">
  <a href="#">首頁</a>
  <span class="sep">/</span>
  <a href="#">動物島民</a>
</div>
<div class="media">
  <div class="search">
    <table class="table">
      <tr>
        <th>查看全部</th>
        <td><button class="btn current">查看全部</button></td>
      </tr>
      <tr>
        <th>種族</th>
        <td>
          <button class="btn">綿羊</button>
          <button class="btn">鴨</button>
        </td>
      </tr>
      <tr>
        <th>個性</th>
        <td>
          <button class="btn">運動</button>
          <button class="btn">悠閒</button>
        </td>
      </tr>
      <tr>
        <th>生日</th>
        <td>
          <button class="btn">一月</button>
          <button class="btn">二月</button>
          <button class="btn">三月</button>
          <button class="btn">四月</button>
          <button class="btn">五月</button>
          <button class="btn">六月</button>
          <button class="btn">七月</button>
          <button class="btn">八月</button>
          <button class="btn">九月</button>
          <button class="btn">十月</button>
          <button class="btn">十一月</button>
          <button class="btn">十二月</button>
        </td>
      </tr>
    </table>
  </div>
  <table class="media-card table">
    <tr>
      <th>名稱</th>
      <th>種族</th>
      <th>個性</th>
      <th>生日</th>
    </tr>
    @foreach($lists as $list)
    <tr>
      <td>
        <a href="detail.html">
          <span>{{ $list->name }}</span>
          <div class="table-img">
            <img src="/animal/{{ $list->name }}.png" alt="茶茶丸">
          </div>
        </a>
      </td>
      <td>{{ $list->race }}</td>
      <td>{{ $list->personality }}</td>
      <td>{{ $list->bd }}</td>
    </tr>
    @endforeach
  </table>
  <div class="media-card">
    <div class="media-card-title">島民說明</div>
    <ul class="media-list">
      <li>島上居民動物有親密度設定：認識的人、好友、親友</li>
      <li>親密度增加方式1：相遇初日以及每天對話會有加成、如果缺了一天會重置加成</li>
      <li>親密度增加方式2：完成動物賦予的任務、賣給小動物要的手上物件、信件往來</li>
      <li>親密度增加方式3：達成一定親密度才能送禮、生日當天送禮，禮物用包裝紙會有額外加成</li>
      <li>親密度降低方式：用補蟲網猛打、持續推擠、贈送空罐、長靴、雜草、壞掉的大頭菜</li>
      <li>長期沒有玩遊戲不會減低親密度</li>
      <li>親密度提昇至親友：會被主動對話，可獲得動物照片、可裝飾，能知道生日、星座、座右銘</li>
    </ul>
  </div>
  <div class="media-card">
    <div class="media-card-title">好用網址</div>
    <ul class="media-list">
      <li><a href="https://forum.gamer.com.tw/A.php?bsn=7287" target="_blank">巴哈姆特 動物森友會 哈啦區</a></li>
      <li><a href="https://ac-turnip.com/" target="_blank">動物森友會大頭菜計算機</a></li>
    </ul>
  </div>
</div>
@endsection
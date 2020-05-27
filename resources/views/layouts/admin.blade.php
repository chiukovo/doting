<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>admin</title>

  <!-- Custom fonts for this template -->
  <link href="/adminData/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template -->
  <link href="/adminData/css/sb-admin-2.css" rel="stylesheet">

  <!-- Custom styles for this page -->
  <link href="/adminData/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

  <link rel="stylesheet" href="/css/style.css?v={{ config('app.version') }}">
  <link rel="stylesheet" href="/css/lightbox.min.css">

  <script src="/js/vue.min.js"></script>
  <script src="/js/axios.min.js"></script>
  <script src="/js/vue-infinite-loading.js"></script>
  <script src="/js/vue-go-top.min.js"></script>
  <script src="/js/jquery-2.2.4.min.js"></script>
  <script src="/js/lightbox.min.js"></script>
  <script src="/js/popper.min.js"></script>

  <link rel="stylesheet" href="/css/sweetalert2.css">
  <script type="text/javascript" src="/js/sweetalert2.min.js"></script>

  <style>
    [v-cloak] {
      display: none;
    }
  </style>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>首頁</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Nav Item - Charts -->
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}/animals">
          <i class="fas fa-fw fa-table"></i>
          <span>動物居民</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>動物NPC</span></a>
      </li>
      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
          <i class="fas fa-fw fa-folder"></i>
          <span>博物館</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="/{{env('ADMIN_PREFIX')}}">魚圖鑑</a>
            <a class="collapse-item" href="/{{env('ADMIN_PREFIX')}}">昆蟲圖鑑</a>
            <a class="collapse-item" href="/{{env('ADMIN_PREFIX')}}">化石圖鑑</a>
            <a class="collapse-item" href="/{{env('ADMIN_PREFIX')}}">藝術品圖鑑</a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>DIY方程式</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>家具圖鑑</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>服飾圖鑑</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>植物圖鑑</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{env('ADMIN_PREFIX')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>唱片圖鑑</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/filemanager" target="_blank">
          <i class="fas fa-fw fa-table"></i>
          <span>圖片庫</span></a>
      </li>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">admin</span>
                <img class="img-profile rounded-circle" src="/image/icon/animals.svg">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  登出
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        @yield('content')
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">登出</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">確定要登出?</div>
        <div class="modal-footer">
          <form action="/{{ env('ADMIN_PREFIX') }}/logout" method="post">
              {{ csrf_field() }}
              <button class="btn btn-secondary" type="button" data-dismiss="modal">取消</button>
              <button class="btn btn-primary">確定</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="/adminData/vendor/jquery/jquery.min.js"></script>
  <script src="/adminData/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="/adminData/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="/adminData/js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="/adminData/vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="/adminData/vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="/adminData/js/demo/datatables-demo.js"></script>

  @if(session()->has('success'))
      <script type="text/javascript">
        swal("{{ session()->get('success') }}", "", "success");
      </script>
  @endif

  @if(session()->has('error'))
      <script type="text/javascript">
        swal("{{ session()->get('error') }}", "", "error");
      </script>
  @endif

  @php 
    session()->forget('success');
    session()->forget('error');
  @endphp

</body>

</html>

<aside class="main-sidebar">

<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">

  <!-- Sidebar user panel (optional) -->
  <div class="user-panel">
    <div class="pull-left image">
      <img src="https://scontent.fmnl4-4.fna.fbcdn.net/v/t1.0-9/1661644_617617711649712_3475893462515529090_n.jpg?_nc_cat=100&_nc_oc=AQmWlX_yf-I5aV4Aa9UcZqmwlANLaQ1GMwBWyTnbkYE_nCVNMDURpKAHgAEa4rcP8Tg&_nc_ht=scontent.fmnl4-4.fna&oh=8d372707b23c1794a20f7b0ec13f2fed&oe=5DA3BC13" class="img-circle" alt="User Image">
    </div>
    <div class="pull-left info">
      <p>Admin</p>
      <!-- Status -->
      <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
    </div>
  </div>

  <!-- search form (Optional) -->
  <form action="#" method="get" class="sidebar-form">
    <div class="input-group">
      <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
    </div>
  </form>
  <!-- /.search form -->

  <!-- Sidebar Menu -->
  <ul class="sidebar-menu">
    <li class="header">Management</li>
    <!-- Optionally, you can add icons to the links -->
    <li class="active"><a href="{{ route('style.index')}}"><i class="fa fa-link"></i> <span>Style</span></a></li>
    <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
    <li class="treeview">
      <a href="#"><i class="fa fa-link"></i> <span>Post</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="">View</a></li>
        <li><a href="">Add</a></li>
      </ul>
    </li>
  </ul>
  <!-- /.sidebar-menu -->
</section>
<!-- /.sidebar -->
</aside>
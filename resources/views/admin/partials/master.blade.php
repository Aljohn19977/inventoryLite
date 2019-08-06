<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  @include('admin.partials._head')
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  @include('admin.partials._header')
  <!-- Left side column. contains the logo and sidebar -->

  @include('admin.partials._mainsidebar')

  <!-- Content Wrapper. Contains page content -->
  @yield('content')
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  @include('admin.partials._footer')

  <!-- Control Sidebar -->
  @include('admin.partials._controlsidebar')
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<!-- REQUIRED JS SCRIPTS -->
<!-- jQuery 3 -->
<script src="{{ asset('js/app.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('admin/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
@yield('script')
<!-- AdminLTE App -->
<script src="{{ asset('admin/dist/js/adminlte.min.js')}}"></script>
<!-- PACE -->
<script src="{{asset('admin/bower_components/PACE/pace.min.js')}}"></script>
</body>
</html>

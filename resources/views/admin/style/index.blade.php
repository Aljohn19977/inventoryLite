@extends('admin.partials.master')

@section('style')
<link rel="stylesheet" href="{{ asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
a{
    cursor: pointer;
}
</style>
@endsection

@section('script')
<script src="{{ asset('admin/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('admin/plugins/sweetAlert2/sweetalert2.all.min.js')}}"></script>

<script>
$.ajaxSetup({
    headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
});


 
$(document).ready(function(){
  
  
  view_all_style();
  

});

  // function view_style_info($id){

  //     var id = $id;

  //     $.ajax({
  //         type: 'get',
  //         url: "{{ url('/getStyleInfo') }}/"+id,
  //         success: function(data){
  //           console.log(data);    

  //           $.each(data, function(key, value){
  //                   $('#'+key).val(value);
  //           });

  //           $("#dummyimage").attr("src","http://inventorystock.test/storage/"+data.sku_style_id+".png");
            
  //           $("#view-modal-style-info").modal("show");
  //         },
  //         error: function(error){
  //           console.log(error);
  //         }
  //     });  
  // }

  function view_all_style(){

    $('#check_location').remove();

    $('#check_all').append('<span id="check_location">&#10004;</span>');

    $('#datatable_style').DataTable().clear();
    $('#datatable_style').DataTable().destroy();

    var table_styles = $('#datatable_style').DataTable({
                   processing: true,
                   serverSide: true,
                   "lengthChange": true,
                   "responsive": true,
                   "autoWidth": true,
                   "searching": true,
                   ajax: {
                          'url' : '{{ route("api.getAllStyle") }}',
                          'dataType' : 'json',
                          'type' : 'post',
                          },
                     columns : [
                                {"data" : "sku_style_id"},
                                {"data" : "name"},
                                {"data" : "brand_id"},
                                {"data" : "category_id"},
                                {"data" : "status"},
                                {"data" : "created_at"},
                                {"data" : "action","searchable": false,"sortable": false},
                               ],
     });
  }

  function view_active_style(){

    $('#check_location').remove();

    $('#check_active').append('<span id="check_location">&#10004;</span>');

    $('#datatable_style').DataTable().clear();
    $('#datatable_style').DataTable().destroy();
    var table_styles = $('#datatable_style').DataTable({
                   processing: true,
                   serverSide: true,
                   "lengthChange": true,
                   "responsive": true,
                   "autoWidth": true,
                   "searching": true,
                   ajax: {
                          'url' : '{{ route("api.getActiveStyle") }}',
                          'dataType' : 'json',
                          'type' : 'post',
                          },
                     columns : [
                                {"data" : "sku_style_id"},
                                {"data" : "name"},
                                {"data" : "brand_id"},
                                {"data" : "category_id"},
                                {"data" : "status"},
                                {"data" : "created_at"},
                                {"data" : "action","searchable": false,"sortable": false},
                               ],
     });
  }

  function view_deactive_style(){
  
    $('#check_location').remove();

    $('#check_deactive').append('<span id="check_location">&#10004;</span>');

    $('#datatable_style').DataTable().clear();
    $('#datatable_style').DataTable().destroy();
    var table_styles = $('#datatable_style').DataTable({
                   processing: true,
                   serverSide: true,
                   "lengthChange": true,
                   "responsive": true,
                   "autoWidth": true,
                   "searching": true,
                   ajax: {
                          'url' : '{{ route("api.getDeactiveStyle") }}',
                          'dataType' : 'json',
                          'type' : 'post',
                          },
                     columns : [
                                {"data" : "sku_style_id"},
                                {"data" : "name"},
                                {"data" : "brand_id"},
                                {"data" : "category_id"},
                                {"data" : "status"},
                                {"data" : "created_at"},
                                {"data" : "action","searchable": false,"sortable": false},
                               ],
     });
  }

  function delete_style_info($id){

    var id = $id;
 

    Swal({
    title: 'Are you sure?',
      // text: 'You will not be able to recover this imaginary file!',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    }).then((result) => {
      if (result.value) {

        Pace.restart();
        Pace.track(function () {  
          $.ajax({
            type: 'delete',
            url: "{{ url('/style') }}/"+id,
            success: function(data){
              $('#datatable_style').DataTable().ajax.reload();   
            },
            error: function(error){
              console.log('sucess');
            }
           });   
        })
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal(
          'Cancelled',
          '',
          'error'
        )
      }
    })

  }



</script>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Style Management
        <small>Style List</small>
      </h1>
      <!--
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables</a></li>
        <li class="active">Data tables</li>
      </ol>
      -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Style List</h3>
              
              <div class="dropdown pull-right">
                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a id="check_all" onclick="view_all_style()()">All </a></li>
                  <li><a id="check_active" onclick="view_active_style()">Active </a></li>
                  <li><a id="check_deactive" onclick="view_deactive_style()">Deactive </a></li>
                </ul>
              </div>
              <a href="{{ route('style.create')}}" class="btn btn-default pull-right" style="margin-right:5px;"><i class="fa fa-fw fa-plus"></i> Add Style</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="datatable_style" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>SKU ID</th>
                  <th>Name</th>
                  <th>Brand</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Date Added</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

    <div class="modal fade" id="view-modal-style-info">
    <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Style Information</h4>
                </div>
                <div class="modal-body">
                <form class="form-horizontal">
                <div class="text-center"><img id="dummyimage" src="" alt="barcode"></div>
                    <div class="box-body">
                      <div class="form-group">
                        <label class="col-sm-2 control-label">SKU ID</label>
                        <div class="col-sm-8">
                          <input class="form-control" id="sku_style_id" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-8">
                          <input class="form-control" id="name" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Brand</label>
                        <div class="col-sm-8">
                          <input class="form-control" id="brand_id" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="category_id" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="status" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Date Added</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="date_added" disabled>
                        </div>
                      </div> 
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-8">
                          <textarea class="form-control" rows="3" id="description" disabled></textarea>
                        </div>
                      </div>
                    </div>
                    <!-- /.box-body -->
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
              </div>
              <!-- /.modal-content -->
      </div>
            <!-- /.modal-dialog -->
  </div>
@endsection
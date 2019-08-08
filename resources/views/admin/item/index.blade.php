@extends('admin.partials.master')

@section('style')
<link rel="stylesheet" href="{{ asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">

<style>
a{
    cursor: pointer;
}
</style>

@endsection

@section('script')
<script src="{{ asset('admin/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
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

  var startDate;
  var endDate;
  $('input[name="datefilter"]').daterangepicker({
      autoUpdateInput: false,
      locale: {
          cancelLabel: 'Clear'
      },
      ranges: {
              'Today': [moment(), moment()],
              'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
              'Last 7 Days': [moment().subtract('days', 6), moment()],
              'Last 30 Days': [moment().subtract('days', 29), moment()],
              'This Month': [moment().startOf('month'), moment().endOf('month')],
              'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
              'Last Year': [moment().subtract('year', 1),moment().subtract('year', 1)]
            },
  });

  $('input[name="datefilter"]').val('Click to select date range');
  
  $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
      startDate = picker.startDate;
      endDate = picker.endDate;  
  });

  $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });

  var table_styles = $('#styles').DataTable({
                   processing: true,
                   serverSide: true,
                   "lengthChange": true,
                   "responsive": true,
                   "autoWidth": true,
                   "searching": true,
                   ajax: {
                          'url' : '/item/apiGetItemActiveStyle',
                          'dataType' : 'json',
                          'type' : 'post',
                          'data' :
                                  {
                                     '_token': $('input[name=_token]').val(),
                                  }
                          },
                     columns : [
                                {"data" : "sku_style_id"},
                                {"data" : "name"},
                                {"data" : "brand_id"},
                                {"data" : "category_id"},
                                {"data" : "quantity"},
                                {"data" : "status"},
                                {"data" : "created_at"},
                                {"data" : "action"}
                               ],
  
  });
  
});

</script>
@endsection

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Item Management
        <small>Item List</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Item List</h3>
              <div class="row" style="padding-top:10px;">
                  <div class="col-md-6">
                    <div class="form-group">

                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" name="datefilter" id="reservation">
                      </div>
                      <!-- /.input group -->
                    </div>
                  </div>
                  <div class="col-md-6">
                    <button type="button" id="apply_date_item" class="btn btn-default hide" data-dismiss="modal">Apply</button>
                    <button type="button" id="apply_date_style" class="btn btn-default" data-dismiss="modal">Apply</button>
                    <div class="dropdown pull-right">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                        <li><a id="check_all" onclick="view_all_style()()">View By Style </a></li>
                        <li><a id="check_active" onclick="view_active_style()">View By Item </a></li>
                        </ul>
                    </div>
                    <a href="" class="btn btn-default pull-right" style="margin-right:5px;"><i class="fa fa-fw fa-plus"></i>New Item</a>
                  </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="styles" class="table table-bordered table-striped">
              <thead>
                <tr>
                   <th>STYLE SKU ID</th>
                   <th>Name</th>
                   <th>Brand</th>      
                   <th>Category</th>
                   <th>Quantity</th>
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
  <div class="modal fade" id="view-modal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Style Information</h4>
              </div>
              <div class="modal-body">
              <form class="form-horizontal">
                  <div class="box-body">
                        <div class="form-group">
                          <label class="col-sm-2 control-label">ID</label>
                          <div class="col-sm-8">
                            <input type="email" class="form-control" id="id" disabled>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-2 control-label">Name</label>

                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="style_name" disabled>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-2 control-label">Category</label>

                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="category" disabled>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-2 control-label">Brand</label>

                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="brand" disabled>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-2 control-label">Description</label>
                          <div class="col-sm-8">
                            <textarea class="form-control" rows="3" id="style_description" disabled></textarea>
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
  <div class="modal fade" id="view-modal-item">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal_item_title"></h4>
              </div>
              <div class="modal-body">
                  <div class="box-body table-responsive">
                  <table id="style_item" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Item ID</th>
                      <th>Color</th>
                      <th>Size</th>      
                      <th>Status</th>             
                    </tr>
                  </thead>
                  <tbody> 
                  </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
  </div>
  <div class="modal fade" id="view-modal-item-info">
    <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Item Information</h4>
                </div>
                <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Item ID</label>
                        <div class="col-sm-8">
                          <input class="form-control" id="item_id" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Style ID</label>
                        <div class="col-sm-8">
                          <input class="form-control" id="style_id" disabled>
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
                          <input type="text" class="form-control" id="brand_name" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="category_name" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Color</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="color" disabled>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Size</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="size" disabled>
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
</div>
@endsection
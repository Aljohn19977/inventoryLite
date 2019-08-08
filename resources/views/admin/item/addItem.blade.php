@extends('admin.partials.master')

@section('style')
<!-- MultiSelect -->
<link rel="stylesheet" href="{{ asset('admin/dist/css/bootstrap-multiselect.css')}}">
@endsection

@section('script')
<!-- MultiSelect -->
<script src="{{ asset('admin/dist/js/bootstrap-multiselect.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('admin/dist/js/sweetalert2.min.js') }}"></script>

<script>
$(document).ready(function(){


    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

var selected_brand = {{ $styles->brand_id }};
var selected_category = {{ $styles->category_id }};

multiselect();

var html = '';
          html += '<tr>';
          html += '<td><div class="form-group"><input type="text" class="form-control" name="style_size[]" required></div></td>';
          html += '<td><div class="form-group"><input type="text" class="form-control" name="style_color[]" required></div></td>';
          html += '<td><div class="form-group"><input type="number" class="form-control" name="quantity[]" required></div></td>';
          html += '<td><button class="btn btn-danger" id="remove"><i class="fa fa-fw fa-remove"></i></button></td>';
          html += '</tr>';

$(document).on('click', '#add_row', function(){
    $('#item_table').append(html);
});

$(document).on('click', '#remove', function(){
     var rowCount = $('#item_table tr').length;
     if(rowCount!=2){
        $(this).closest('tr').remove();
     }
});


 $('#insert_form').on('submit',function(event){
    
    event.preventDefault();
    Pace.restart();
    
    var form_data = $('#insert_form').serialize();
    var style_id = $("#style_id").val();
    var name = $("#name").val();
  
      Pace.track(function () {
               $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
               });
                $.ajax({
                      type: 'post',
                      url: '/item',
                      data: form_data
                             + "&style_id=" + style_id,
                      success: function(data) {
                          $('#item_table').find("tr:gt(0)").remove();
                          $('#item_table').append(html);
  
                             $('#error').html('<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Success!</strong> Stocks for <strong>'+name+'</strong> succesfully added.</div>');
                             window.setTimeout(function() {
                                            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                                                $(this).remove(); 
                                            });
                                    }, 4000);    
                      },
                      error: function(error){
                        $('#error').html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Failed!</strong> Please input right value for each input fields.</div>');
                                window.setTimeout(function() {
                                            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                                                $(this).remove(); 
                                            });
                        }, 5000);   
                      }
                  });
              
      });
    });       


$('.select-ajax').multiselect({
    maxHeight: 400,
    buttonWidth: '100%',
    includeSelectAllOption: true,
    enableFiltering: true,
}); 
    
  function multiselect (){
      $.ajax({
        type: 'GET',
        url: '/apiGetAllBrand',
        dataType: 'json',
        success: function(data) {
          $('#select_brand').find('option').remove()
          $.each(data.items, function (i, item) {
              if(item.id == selected_brand){
                $('#select_brand').append('<option value="' + item.id + '" selected>' + item.name + '</option>'); 
              }else{
                $('#select_brand').append('<option value="' + item.id + '">' + item.name + '</option>');   
              }  
          });
          $('#select_brand').multiselect('rebuild');
          $('#select_brand').multiselect('disable');
        },
        error: function() {
              alert('error loading items');
        }
      });

      $.ajax({
        type: 'GET',
        url: '/apiGetAllCategory',
        dataType: 'json',
        success: function(data) {
          $.each(data.items, function (i, item) {
              if(item.id == selected_category){
                $('#select_category').append('<option value="' + item.id + '" selected>' + item.name + '</option>'); 
              }else{
                $('#select_category').append('<option value="' + item.id + '">' + item.name + '</option>');   
              }  
          });
          $('#select_category').multiselect('rebuild');
          $('#select_category').multiselect('disable');
        },
        error: function() {
              alert('error loading items');
        }
      });
  }  

});

</script>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Style Management
        <small>Add Style</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-md-12">
         <!-- general form elements -->
         <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Style</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" id="insert_form">
              <div class="box-body">
              <div id="error"></div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group" id="sku_style_id_this">
                      <label for="style_id">ID</label>
                      <input type="email" class="form-control" id="sku_style_id" value="{{ $styles->sku_style_id }}" disabled>
                      <input type="name" class="form-control hide" id="style_id" value="{{ $styles->id }}">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" id="brand_id_this">
                      <label>Brand</label>
                      <select class="select-ajax form-control" id="select_brand"></select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" id="category_id_this">
                      <label>Category</label>
                      <select class="select-ajax form-control" id="select_category"></select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group" id="name_this">
                      <label for="name">Name</label>
                      <input type="text" value="{{ $styles->name }}" class="form-control" id="name" placeholder="Enter Name" disabled>
                    </div>
                  </div>
                  <div class="col-md-2">
                   <div class="form-group" id="status_this">
                      <label>Status</label>
                      <select class="form-control select2" data-placeholder="Select Status" style="width: 100%;" id="status" disabled>
                            @if($styles->status == 'Active')
                            <option selected="selected" value="Active">Active</option>
                            @else
                            <option selected="selected" value="Deactive">Deactive</option>
                            @endif
                      </select>
                   </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group" id="description_this">
                      <label>Description</label>
                      <textarea class="form-control" rows="3" id="description" placeholder="Enter Description" disabled>{{ $styles->description }}</textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                  <table class="table table-bordered table-responsive" id="item_table">
                    <thead>
                    <tr>
                      <th style="width: 100px">Size</th>
                      <th style="width: 300px">Color</th>
                      <th style="width: 100px">Quantity</th>
                      <th style="width: 110px"><button type="button" class="btn btn-success" id="add_row"><i class="fa fa-fw fa-plus"></i></button></th>
                    </tr>
                    </thead>
                    <tbody> 
                    <tr>
                      <td>
                        <div class="form-group">
                          <input type="text" class="form-control" name="style_size[]" required>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <input type="text" class="form-control" name="style_color[]" required>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                        <input type="number" class="form-control" name="quantity[]" required>
                        </div>
                      </td>
                      <td>
                        <button class="btn btn-danger" id="remove"><i class="fa fa-fw fa-remove"></i></button>
                      </td>
                    </tr>
                    <tbody> 
                  </table>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <a href="{{ URL::previous() }}" class="btn btn-primary">Back</a>
                <input type="submit" name="submit" class="btn btn-primary" value="Submit" />
                <button type="submit" class="btn btn-primary">Clear</button>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col-->
      </div>
      <!-- ./row -->

    </section>
    <!-- /.content -->
  </div>
@endsection
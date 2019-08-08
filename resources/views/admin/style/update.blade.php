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



$("#add_brand_modal").click(function(){
   event.preventDefault();
   $('#brandModal').modal('show');
});

$("#add_category_modal").click(function(){
   event.preventDefault();
   $('#categoryModal').modal('show');
});

$( "#add_brand" ).click(function() {

  var name = $('#brand_name').val();
    Pace.restart();
    Pace.track(function () {
      $.ajax({
              type: 'POST',
              url: "{{ route('brand.store') }}",
              data: {
                'name': name,

              },
              success: function(data) {

                var message = data.success;
                Swal.fire({
                  position: 'top-end',
                  type: 'success',
                  title: message,
                  showConfirmButton: false,
                  timer: 1500
                })

                $('#brandModal').modal('hide')
                $('#brand_name').val('');
              
                multiselect();
                
              },
              error: function(data){

                var message = data.responseJSON.errors.name.toString();

                Swal.fire({
                  position: 'top-end',
                  type: 'warning',
                  title: message,
                  showConfirmButton: false,
                  timer: 1500
                })
        
                $.each(data.responseJSON.errors, function(key, value){
                  $("div[name="+key+"]").addClass("has-error").append("<span class='help-block'>"+value+"</span>");
                });
              }
      });
    });
});

$( "#add_category" ).click(function() {

  var name = $('#category_name').val();
  Pace.restart();
    Pace.track(function () {
    $.ajax({
          type: 'POST',
          url: "{{ route('category.store') }}",
          data: {
            'name': name,

          },
          success: function(data) {

            var message = data.success;
            Swal.fire({
              position: 'top-end',
              type: 'success',
              title: message,
              showConfirmButton: false,
              timer: 1500
            })

            $('#categoryModal').modal('hide')
            $('#category_name').val('');
          
            multiselect();
            
          },
          error: function(data){

            var message = data.responseJSON.errors.name.toString();

            Swal.fire({
              position: 'top-end',
              type: 'warning',
              title: message,
              showConfirmButton: false,
              timer: 1500
            })
    
            $.each(data.responseJSON.errors, function(key, value){
              $("div[name="+key+"]").addClass("has-error").append("<span class='help-block'>"+value+"</span>");
            });
          }
    });
  });
});

$("#clear_field").click(function(){
   clearFields();
   event.preventDefault();
});

$('#insert_form').on('submit',function(event){
    
    event.preventDefault();
    Pace.restart();
    
    var form_data = $('#insert_form').serialize();
    var sku_style_id = $("#sku_style_id").val();
    var name = $("#name").val();
    var brand_id = $("#select_brand").val();
    var category_id = $("#select_category").val();
    var description = $("#description").val();
    var status = $("#status").val();
  
      Pace.track(function () {
                $.ajax({
                      type: 'patch',
                      url: "{{ url('/style/'.$styles->id) }}",
                      data: form_data
                             + "&sku_style_id=" + sku_style_id
                             + "&name=" + name
                             + "&brand_id=" + brand_id
                             + "&status=" + status
                             + "&category_id=" + category_id
                             + "&description=" + description,
                      success: function(data) {

                            // getId();
                            clearError();
                            // clearFields();
                            
                            
                             $('#error').html('<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Success!</strong> Style <strong>'+name+'</strong> succesfully updated.</div>');
                             window.setTimeout(function() {
                                            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                                                $(this).remove(); 
                                            });
                                    }, 4000);    

                      },
                      error: function(error){

                          clearError();
                     

                          $.each(error.responseJSON.errors, function(keys, value){                         
                                $("#"+key+"_this").addClass("has-error").append("<span class='help-block'>"+value+"</span>");
                          });

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
        },
        error: function() {
              alert('error loading items');
        }
      });
  }  

  function clearFields(){               
    $('#name').val('');
    $('#description').val('');
    $("#brand_id").val('').trigger('change');
    $("#category_id").val('').trigger('change');
    $("#status").val('').trigger('change');
        
  }
  
  function clearError(){
     $( ".has-error" ).removeClass("has-error");
     $( ".help-block" ).remove();
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
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" id="brand_id_this">
                      <label>Brand</label>
                      <a class="btn btn-primary pull-right btn-xs" id="add_brand_modal"><i class="fa fa-plus"></i> Add Brand</a>
                      <select class="select-ajax form-control" id="select_brand"></select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" id="category_id_this">
                      <label>Category</label>
                      <a class="btn btn-primary pull-right btn-xs" id="add_category_modal"><i class="fa fa-plus"></i> Add Category</a>
                      <select class="select-ajax form-control" id="select_category"></select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group" id="name_this">
                      <label for="name">Name</label>
                      <input type="text" value="{{ $styles->name }}" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                  </div>
                  <div class="col-md-2">
                   <div class="form-group" id="status_this">
                      <label>Status</label>
                      <select class="form-control select2" data-placeholder="Select Status" style="width: 100%;" id="status">
                            @if($styles->status == 'Active')
                            <option selected="selected" value="Active">Active</option>
                            <option value="Deactive">Deactive</option>
                            @else
                            <option selected="selected" value="Deactive">Deactive</option>
                            <option value="Active">Active</option>
                            @endif
                      </select>
                   </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group" id="description_this">
                      <label>Description</label>
                      <textarea class="form-control" rows="3" id="description" placeholder="Enter Description">{{ $styles->description }}</textarea>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <a href="{{ URL::previous() }}" class="btn btn-primary">Back</a>
                <button id="add" class="btn btn-primary">Submit</button>
                <button id="clear_field" class="btn btn-primary">Clear</button>
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
    <div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Brand</h4>
                  </div>
                  <div class="modal-body">
                  <div class="form-group">
                    <label for="title">Name</label>
                    <input type="text" class="form-control" id="brand_name" name="brand" placeholder="Name">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="add_brand" class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </div>
    </div>
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Category</h4>
                  </div>
                  <div class="modal-body">
                  <div class="form-group">
                    <label for="title">Name</label>
                    <input type="text" class="form-control" id="category_name" name="category" placeholder="Name">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="add_category" class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </div>
    </div>
  </div>
@endsection
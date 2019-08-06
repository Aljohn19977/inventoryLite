@extends('admin.partials.master')

@section('style')
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('admin/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('script')
<!-- Select2 -->
<script src="{{asset('admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
$(document).ready(function(){


    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

  $('.select2').select2(); 

//   function getId(){
     
//       $.ajax({
//       type: 'get',
//       url: '/getSkuId',
//       success: function(data) {
//           $('#sku_style_id').val(data.sku_style_id);
//       },
//       error: function(error){
//           alert('error');
//       }
//      });
//   }

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
    var brand_id = $("#brand_id").val();
    var category_id = $("#category_id").val();
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
                      <select class="form-control select2" data-placeholder="Select Brand" style="width: 100%;" id="brand_id">
                        @foreach ($brands as $brand)
                            @if($brand->id == $styles->brand_id)
                            <option selected="selected" value="{{ $styles->brand_id }}">{{ $styles->brand->name }}</option>
                            @else
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" id="category_id_this">
                      <label>Category</label>
                      <select class="form-control select2" data-placeholder="Select Category" style="width: 100%;" id="category_id">
                        @foreach ($categories as $category)
                            @if($category->id == $styles->category_id)
                            <option selected="selected" value="{{ $styles->category_id }}">{{ $styles->category->name }}</option>
                            @else
                            <option value="{{ $category_id }}">{{ $category->name }}</option>
                            @endif
                        @endforeach
                      </select>
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
                            @else
                            <option selected="selected" value="Deactive">Deactive</option>
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
  </div>
@endsection
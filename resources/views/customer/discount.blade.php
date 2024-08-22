@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Add Discount')

@section('navcustomer','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <button class="btn btn-success f-right" onclick="window.location = '{{url('customer')}}'"><i class=" text-center icofont icofont-arrow-left m-t-3 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back"></i>Back</button>
         <h1 class="">{{$customer_name}}</h1>

         <h5 class="card-header-text">Discount Details</h5>

{{--         <h5 class="card-header-text">Create Discount</h5>--}}
{{--         <h5 class=""><a href="{{ url('customer') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>--}}
     </div>  
     <div class="card-block">
         <div class="project-table">
             <div class="row">
              <div class="col-lg-3 col-md-3">
                 <div class="form-group">
                    <label class="form-control-label">Select Department</label>
                    <select name="ddldepartment" id="ddldepartment"  class="form-control select2" data-placeholder="Select Department" >
                        <option value="">Select Department</option>
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
               </div>

               <div class="col-lg-3 col-md-3">
                 <div class="form-group">
                    <label class="form-control-label">Select Sub-Department</label>
                    <select name="ddlsubdept" id="ddlsubdept"  class="form-control select2" data-placeholder="Select Sub-Department" >
                        <option value="">Select Sub-Department</option>
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
               </div>

               <div class="col-lg-3 col-md-3">
                 <div class="form-group">
                    <label class="form-control-label">Select Product</label>
                    <select name="product" id="product"  class="form-control select2" data-placeholder="Select Product" >
                        <option value="">Select Product</option>
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
               </div>

               <div class="col-lg-3 col-md-3">
                 <div class="form-group">
                      <label class="form-control-label">Discount Amount</label>
                      <input type="text" name="discount" id="discount" class="form-control" placeholder="Enter Discount Amount" />
                  </div>
               </div>
               
             </div>
             <div class="row">
               <div class="col-lg-12 col-md-12">
                <div class="form-group">
                  <label class="form-control-label"></label>
                 <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light m-t-25 f-right">
                        <i class="icofont icofont-plus "></i> &nbsp;Create Discount
                  </button>
                  <button style="display: none;" type="button" id="btnupdate" class="btn btn-md btn-primary waves-effect waves-light f-right">
                       <i class="icofont icofont-ui-edit "></i> &nbsp; Update Discount
                  </button>
                </div>
               </div>
             </div>
         </div>
     </div>
   </div>

    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Customer Discount List</h5>
         </div>  
       <div class="card-block">
    
           <div class="project-table">
       <table id="discountTable" class="table dt-responsive table-striped table-bordered nowrap" width="100%">
         <thead>
            <tr>
               <th>Product Image</th>
               <th>Product Code</th>
               <th>Product Name</th>
               <th>Discount Amount</th>
               <th>Date</th>
                <th>Time</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
      
         	
     
         </tbody>
       </table>
        </div>
    </div>
   </div>
</section>
@endsection


@section('scriptcode_three')

  <script type="text/javascript">

   $(".select2").select2();

   $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: true,
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });
   
  getDiscount();
  function getDiscount()
  {
     $.ajax({
      url : "{{url('/get-insert')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",id:'{{$customerID}}'},
      dataType:"json",
      success : function(result){
            $(".table tbody").empty();
               for(var count =0;count < result.length; count++){
                    $(".table tbody").append(
                      "<tr>" +
                        "<td class='text-center'><img src='../public/assets/images/products/"+((result[count].image != "") ? result[count].image : 'placeholder.jpg')+"' alt='"+result[count].image+"'/></td>" +
                         "<td>"+result[count].item_code+"</td>" +  
                        "<td>"+result[count].product_name+"</td>" +  
                        "<td>"+result[count].discount.toLocaleString()+"</td>" +
                        "<td>"+result[count].date+"</td>" +
                        "<td>"+result[count].time+"</td>" +
                        "<td>"+(result[count].status_id == 1 ? '<span class="tag tag-success">Active</span>' : '<span class="tag tag-danger">In-Active</span>' )+"</td>" +  
                        "<td class='action-icon'><i onclick='edit("+result[count].id+","+result[count].discount+","+result[count].department_id+","+result[count].sub_department_id+")' class='icofont icofont-ui-edit text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i onclick='deleteProduct("+result[count].customer_discount_id+")' class='text-danger icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                      "</tr>"
                     );
                }
              }
         });
  }


  function getProducts(dept,subdept)
  {
     $.ajax({
      url : "{{url('/get-products')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",custid:'{{$customerID}}',dept:dept,subdept:subdept},
      dataType:"json",
      success : function(result){
            $("#product").empty();
            $("#product").append("<option value=''>Select Product</option>");
               for(var count =0;count < result.length; count++){
                    $("#product").append(
                      "<option value="+result[count].id+">"+result[count].product_name+" - "+result[count].item_code+"</option>"
                     );
                }
              }
         });
  }
    load_department();
   function load_department()
    {
       $.ajax({
              url: "{{ url('get_departments')}}",
              type: 'POST',
              data:{_token:"{{ csrf_token() }}"},
              success:function(resp){

                $('#ddldepartment').empty(); 
                $("#ddldepartment").append("<option value=''>Select Department</option>");
                 $.each(resp, function( index, value ) {
                    $("#ddldepartment").append(
                      "<option value="+value.department_id+">"+value.department_name+"</option"
                    );
                 });      

              }

          });
    }

    function load_subdept(id)
    {
       $.ajax({
              url: "{{ url('get_sub_departments')}}",
              type: 'POST',
              data:{_token:"{{ csrf_token() }}",id:id},
              success:function(resp){

                $('#ddlsubdept').empty(); 
                $("#ddlsubdept").append("<option value=''>Select Sub Department</option>");
                 $.each(resp, function( index, value ) {
                    $("#ddlsubdept").append(
                      "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option"
                    );
                 });      

              }

          });
    }

    $('#ddldepartment').change(function(){
      load_subdept($('#ddldepartment').val());
    });

    $('#ddlsubdept').change(function(){
      getProducts($('#ddldepartment').val(),$('#ddlsubdept').val());
    });


  $('#btnsubmit').click(function(e){
      if($('#product').val() == "")
      {
        swal_alert("Error!","Product Field is required","error",false);
      }
      else if($('#discount').val() == "")
      {
        swal_alert("Error!","Discont Field is required ","error",false);
      }
      else
      {
           $.ajax({
                  url: "{{url('/discount-insert')}}",
                  type: 'POST',
                  data:{_token:"{{ csrf_token() }}",
                  customer_id:'{{$customerID}}',
                  product_id:$('#product').val(),
                  discount:$('#discount').val(),
                  },
                  success:function(resp){
                      if(resp == 1){
                         swal({
                                title: "Success",
                                text: "Discount Added Succussfully.",
                                type: "success"
                            },function(isConfirm){
                               if(isConfirm){
                                  getDiscount();
                                  $('#product').val('').change();
                                  $('#ddldepartment').val('').change();
                                  $('#ddlsubdept').val('').change();
                                  $('#discount').val('');
                                 //window.location="{{url('/showcustomers')}}";
                               }
                          });
                       }else{
                        swal({
                                title: "Error",
                                text: "Discount already exists for the particular period. Please Update!",
                                type: "error"
                            
                        },function(isConfirm){
                               if(isConfirm){
                                  getDiscount();
                                  $('#product').val('').change();
                                  $('#ddldepartment').val('').change();
                                  $('#ddlsubdept').val('').change();
                                  $('#discount').val('');
                                  
                               }
                          });
                      }
                }
              });
     }
  });

  $('#btnupdate').click(function(e){

    $.ajax({
            url: "{{url('/discount-update')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            customer_id:'{{$customerID}}',
            product_id:$('#product').val(),
            discount:$('#discount').val(),
            },
            success:function(resp){
              swal({
                        title: "Success",
                        text: "Product update successfully.",
                        type: "success"
                    
                },function(isConfirm){
                       if(isConfirm){
                          getDiscount();
                          $('#product').val('').change();
                          $('#discount').val('');
                          $("#btnsubmit").css('display','block');
                          $("#btnupdate").css('display','none');
                          $("#btnsubmit").removeClass('m-t-25');
                          
                       }
                  });
             
            }
          });
  });


   //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      
  });
  
  function swal_alert(title,msg,type,mode){
    
      swal({
            title: title,
            text: msg,
            type: type
         },function(isConfirm){
         if(isConfirm){
            if(mode === true){
              window.location = "{{url('/view-purchases')}}";
            }
          }
      });
  }

  function edit(product,discount,department,subDepartment)
  {
    load_subdept(department);
    getProducts(department,subDepartment);
    
    timeFunction()
    $("#ddldepartment").val(department).change();
    $("#ddlsubdept").val(subDepartment).change()
    $("#product").val(product).change()
    $("#discount").val(discount);
    $("#btnsubmit").css('display','none');
    $("#btnupdate").css('display','block');

    function timeFunction() 
    {
      setTimeout(function(){ console.log("function called"); $("#ddlsubdept").val(subDepartment).change();$("#product").val(product).change(); }, 2000);
    }
  }

  function deleteProduct(id)
  {

    swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this product again!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "delete it!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/delete-products')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                          console.log(resp);
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Product Succussfully Deleted.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                          getDiscount();
                                          $('#product').val('').change();
                                          $('#discount').val('');
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Product is safe :)", "error");
           }
        });
  }
  </script>

@endsection
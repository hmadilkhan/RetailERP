@extends('layouts.master-layout')

@section('title','Master')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Assign Rate</h5>
         <h5 class=""><a href="{{ url('get-masters') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
     </div>  
     <div class="card-block">
         <div class="project-table">
             <div class="row">
              <input type="hidden" name="updateID" id="updateID">
               <div class="col-lg-4 col-md-4">
                 <div class="form-group">
                    <label class="form-control-label">SelectF Finished Goods</label>
                    <select name="category" id="category"  class="form-control select2" data-placeholder="Select Finished Goods" >
                        <option value="">Select Finished Goods</option>
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
               </div>
               <div class="col-lg-4 col-md-4">
                 <div class="form-group">
                      <label class="form-control-label">Rate</label>
                      <input type="text" name="rate" id="rate" class="form-control" placeholder="Enter Amount" />
                  </div>
               </div>
               
             </div>
             <div class="row">
               <div class="col-lg-12 col-md-12 ">
                <div class="form-group">
                  <label class="form-control-label"></label>
                 <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light m-t-25 f-right">
                        <i class="icofont icofont-plus "></i> &nbsp;Add Rate
                  </button>
                  <button style="display: none;" type="button" id="btnupdate" class="btn btn-md btn-primary waves-effect waves-light f-right ">
                       <i class="icofont icofont-ui-edit "></i> &nbsp; Update Rate
                  </button>
                </div>
               </div>
             </div>
         </div>
     </div>
   </div>

    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Master Rate List</h5>
         </div>  
       <div class="card-block">
    
           <div class="project-table">
       <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
               <th>Category</th>
               <th>Rate</th>
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
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

   function addcategory()
   {
       if ($('#catname').val() == "") {
       swal({
              title: "Error Message",
              text: "Required Field can not be blank!",
              type: "warning"
         });

  }
  else
  {
     $.ajax({
            url: "{{url('/addCategory')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            catname:$('#catname').val(),
          },

            success:function(resp){
              console.log(resp);
                if(resp == 2){
                       swal({
                            title: "Already exist",
                            text: "Particular Category Already exist!",
                            type: "warning"
                       });
                      $("#depart-modal").modal("hide");
                     
                  }
                  else
                  {
                    swal({
                            title: "Operation Performed",
                            text: "Department Added Successfully!",
                            type: "success"
                     });
                     $("#depart-modal").modal("hide");
                     $("#depart").empty();
                     getCategories();

                  }
             }

          });   
   }
   }
  getRateList();
  function getRateList()
  {
     $.ajax({
      url : "{{url('/master-rate-list')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",masterid:'{{$masterID}}'},
      dataType:"json",
      success : function(result){
        console.log(result);
            $(".table tbody").empty();
               for(var count =0;count < result.length; count++){
                    $(".table tbody").append(
                      "<tr>" +
                         "<td>"+result[count].category+"</td>" +  
                        "<td>"+result[count].rate+"</td>" +  
                        "<td class='action-icon'><i onclick='edit("+result[count].id+","+result[count].finished_good_id+","+result[count].master_id+","+result[count].rate+")' class='icofont icofont-ui-edit text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i onclick='deleteProduct("+result[count].customer_discount_id+")' class='text-danger icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                      "</tr>"
                     );
                }
              }
         });
  }


  getCategories();
  function getCategories()
  {
     $.ajax({
      url : "{{url('/get-categories')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}"},
      dataType:"json",
      success : function(result){
            $("#category").empty();
            $("#category").append("<option value=''>Select Category</option>");
               for(var count =0;count < result.length; count++){
                    $("#category").append(
                      "<option value="+result[count].id+">"+result[count].product_name+"</option>"
                     );
                }
              }
         });
  }

   getMaster();
  function getMaster()
  {
     $.ajax({
      url : "{{url('/get-master')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}"},
      dataType:"json",
      success : function(result){
            $("#master").empty();
            $("#master").append("<option value=''>Select Master</option>");
               for(var count =0;count < result.length; count++){
                    $("#master").append(
                      "<option value="+result[count].id+">"+result[count].name+"</option>"
                     );
                }
              }
         });
  }


  $('#btnsubmit').click(function(e){
      if($('#category').val() == "")
      {
        swal_alert("Error!","Select Category First","error",false);
      }
      else if($('#rate').val() == "")
      {
        swal_alert("Error!","Select Rate First","error",false);
      }
      else
      {
           $.ajax({
                  url: "{{url('/master-rate-insert')}}",
                  type: 'POST',
                  data:{_token:"{{ csrf_token() }}",
                  categoryid:$('#category').val(),
                  masterid:'{{$masterID}}',
                  rate:$('#rate').val(),
                  },
                  success:function(resp){
                      if(resp == 2){
                         swal({
                                title: "Error",
                                text: "Rate already assigned for this master. Please update !",
                                type: "error"
                            
                        },function(isConfirm){
                               if(isConfirm){
                                  $('#category').val('').change();
                                  $('#master').val('').change();
                                  $('#rate').val('');
                                    getRateList();
                               }
                          });
                       }else{
                         swal({
                                title: "Success",
                                text: "Rate Added Successfully.",
                                type: "success"
                            },function(isConfirm){
                               if(isConfirm){
                                  $('#category').val('').change();
                                  $('#master').val('').change();
                                  $('#rate').val('');
                                  getRateList();
                                 //window.location="{{url('/showcustomers')}}";
                               }
                          });
                       
                      }
                }
              });
     }
  });

  $('#btnupdate').click(function(e){

    $.ajax({
            url: "{{url('/rate-update')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            categoryid:$('#category').val(),
            masterid:'{{$masterID}}',
            rate:$('#rate').val(),
            id:$('#updateID').val(),
            },
            success:function(resp){
              if(resp == 1)
              {
              swal({
                        title: "Success",
                        text: "Product update successfully.",
                        type: "success"
                    
                },function(isConfirm){
                       if(isConfirm){
                          getRateList();
                          $('#category').val('').change();
                          $('#master').val('').change();
                          $('#rate').val('');
                          $("#btnupdate").css('display','none');
                          $("#btnsubmit").removeClass('m-t-25');
                          
                       }
                  });
            }else{
              
          }
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

  function edit(id,category,master,rate)
  {
    $("#category").val(category).change();
    $("#rate").val(rate);
    $("#updateID").val(id);
    $("#btnsubmit").css('display','none');
    $("#btnupdate").css('display','block');
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

  $("#add_category").on('click',function(){
      $('#catname').val('');
      $("#depart-modal").modal("show");
  });
  </script>

@endsection
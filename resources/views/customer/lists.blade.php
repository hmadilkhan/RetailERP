@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Add Expense')

@section('navcustomer','active')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <section class="panels-wells">
    <div class="card">


        <div class="card-header">
            <h5 class="card-header-text">Upload Customer</h5>    
            <a href="{{ route('customer.create') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Customer" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE CUSTOMER</a>

            <button id="downloadsample" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Download Sample" class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-plus m-r-5" ></i> Download Sample</button>
			
        </div>
        <div class="card-block">
            <div class="row col-md-12 " >
              <form method='post' action='{{url('uploadFile')}}' enctype='multipart/form-data' >
              {{ csrf_field() }}
               <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                   <label for="vdimg" class="form-control-label">Select File </label>
                  <br/>
                      <label for="vdimg" class="custom-file">
                         <input type="file" name="file" id="vdimg" class="custom-file-input">
                        <span class="custom-file-control"></span>
                      </label>
                      <input type='submit' class="btn btn-primary m-l-10 m-t-1" name='submit' value='Import'>
                </div>
                </form>
           </div>
        </div>
    </div>
    <div class="card">

                     
     <div class="card-header">
         <h5 class="card-header-text">Customer List</h5>
         
			  <button id="downloadsample" onclick="openReport()"	  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Customer Report PDF" class="btn btn-danger f-right waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-file-pdf m-r-5" ></i>PDF</button>
			  <button id="downloadsample" onclick="openExcelReport()"	  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Customer Report Excel" class="btn btn-success f-right waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-file-excel m-r-5" ></i>EXCEL</button>

              <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>

              <button type="button" id="btn_activeall"  class="btn btn-success f-right m-r-10 invisible"><i class="icofont icofont-ui-check  f-18 "></i>&nbsp;Active</button>

              <br><br>
               
                        
         </div>      
       <div class="card-block"> 

          <ul class="nav nav-tabs md-tabs " role="tablist">
                <li class="nav-item">
                   <a class="nav-link active" data-toggle="tab" href="#home7" role="tab">Active</a>
                   <div class="slide"></div>
                </li>
                <li class="nav-item">
                   <a class="nav-link" data-toggle="tab" href="#profile7" role="tab">InActive</a>
                   <div class="slide"></div>
                </li>
            </ul>

            <div class="tab-content">
              <div class="tab-pane active m-t-10" id="home7" role="tabpanel">
                     <div class="project-table">
                 <table  class="table dt-responsive table-striped table-bordered nowrap" width="100%">
         <thead>
            <tr>
              <th>
                  <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="checkbox32" class="mainchk">
                        <span class="checkbox"></span>
                     </label>
                     <div class="captions"></div>
                  </div>
              </th>
               <th>Image</th>
               <th>Customer Name</th>
			   <th>Balance</th>
               <th>Mobile</th>
               <th>CNIC</th>
               @if(session("roleId") == 2)
                <th>Branch Name</th>
               @endif
               <!-- <th>Status</th> -->
               <th>Mobile App Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
      
           @if($details)
                        @foreach ($details as $value)
                          @if($value->status_id == 1)
                          <tr>
                            <td>
                               <div class="rkmd-checkbox checkbox-rotate">
                                 <label class="input-checkbox checkbox-primary">
                                                            <input type="checkbox" id="checkbox32" class="chkbx" data-id="{{$value->id}}">
                                                            <span class="checkbox"></span>
                                                        </label>
                                 <div class="captions"></div>
                              </div>
                            </td>
                            <td class="text-center">
                            <img width="42" height="42" src="{{ asset('public/assets/images/customers/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                </td>
                             <td>{{$value->name}}</td>
                             <td>{{number_format($value->balance,2)}}</td>
							 <td>{{$value->mobile}}</td>
                             <td>{{$value->nic}}</td> 
                              @if(session("roleId") == 2)
                                 <td>{{$value->branch_name}}</td>
                              @endif
							  <td>
								<div class="checkbox text-center">
								  <label>
										<input id="changeCheckbox{{$value->id}}" onchange="changeCheckbox('changeCheckbox{{$value->id}}','{{$value->id}}')" type="checkbox" {{($value->is_mobile_app_user == 1 ?  'checked' : '')}} data-toggle="toggle" data-size="mini" data-width="20" data-height="20">
								  </label>
								</div>
							  </td> 
                             <!-- <td>{{$value->status_name}}</td> -->
                                <td class="action-icon">
                               <!--  <a href="{{ url('/measurement') }}/{{ $value->id }}" class="p-r-10 f-18 text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Measurement"><i class="icofont icofont-sale-discount"></i></a> -->

                                <a href="{{ url('/discount-panel') }}/{{ $value->slug }}" class="p-r-10 f-18 text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Discount"><i class="icofont icofont-sale-discount"></i></a>

                                
                                  
                                <a href="{{ url('/ledgerDetails') }}/{{ $value->slug }}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ledger"><i class="icofont icofont-list"></i></a>

                                <a href="{{ url('/editcustomers') }}/{{ $value->slug }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                                <a href="{{ url('get-customer-receipts', $value->slug) }}" class="p-r-10 f-18 text-warning " data-toggle="tooltip" data-placement="top" title="" data-original-title="Customer Invoices"><i class="icofont icofont-list"></i></a>

                             </td>  
                         </tr>
                         @endif
                      @endforeach
                    @endif

                      </tbody>
                   </table>
                </div>
              </div>

              <div class="tab-pane m-t-10" id="profile7" role="tabpanel">
                 <div class="project-table">
                 <table  class="table dt-responsive table-striped table-bordered nowrap" width="100%">
         <thead>
            <tr>
              <th>
                  <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                                                <input type="checkbox" id="checkbox32" class="subchk">
                                                <span class="checkbox"></span>
                                            </label>
                     <div class="captions"></div>
                  </div>
              </th>
               <th>Image</th>
               <th>Customer Name</th>
               <th>Mobile</th>
               <th>CNIC</th>
               @if(session("roleId") == 2)
                <th>Branch Name</th>
               @endif
               <!-- <th>Status</th> -->
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
      
           @if($details)
                        @foreach ($details as $value)
                          @if($value->status_id == 2)
                          <tr>
                            <td>
                               <div class="rkmd-checkbox checkbox-rotate">
                                 <label class="input-checkbox checkbox-primary">
                                                            <input type="checkbox" id="checkbox32" class="subchkbx" data-id="{{$value->id}}">
                                                            <span class="checkbox"></span>
                                                        </label>
                                 <div class="captions"></div>
                              </div>
                            </td>
                            <td class="text-center">
                            <img width="42" height="42" src="{{ asset('public/assets/images/customers/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                </td>
                             <td>{{$value->name}} | {{$value->balance}}</td>
                             <td>{{$value->mobile}}</td>
                             <td>{{$value->nic}}</td> 
                              @if(session("roleId") == 2)
                                 <td>{{$value->branch_name}}</td>
                              @endif
                             <!-- <td>{{$value->status_name}}</td> -->
                                <td class="action-icon">
                                 <i  onclick="item_inactive('{{$value->id}}')" class="icofont icofont-ui-check text-success f-18" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                             </td>  
                         </tr>
                         @endif
                      @endforeach
                    @endif

                      </tbody>
                   </table>
                </div>
              </div>
              </div>
            </div>
    
          
    </div>
   </div>
</section>
@endsection


@section('scriptcode_three')

  <script type="text/javascript">
      var rem_id = [];

       $('.table').DataTable({

        bLengthChange: true,
        displayLength: 50,
        info: true,
		order:[3,"DESC"],
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

  function item_inactive(id){

      swal({
          title: "Are you sure?",
          text: "Do you want to active this customer !!!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Active it!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{ url('active-customer')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",id:id},
                        success:function(resp){

                            if(resp == 1){
                                 swal({
                                        title: "Activated",
                                        text: "Customer activated Successfully .",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('customer') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Customer is still inactive (:", "error");
           }
        });
  }

   //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this customer!",
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
                        url: "{{url('/inactivecustomer')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                          console.log(resp);
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove customer.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{route('customer.index')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Customer is safe :)", "error");
           }
        });
  });

 message("{{session('message')}}");
  function message(message)
  {
    if (message == 1) 
    {
      notify("Import Successful", "success")
    }
    else if(message == 2)
    {
      notify("File too large. File must be less than 2MB.", "danger")
    }
    else if(message == 3)
    {
      notify("Invalid File Extension.", "danger")
    }
  }

   //Welcome Message (not for login page)
     function notify(message, type) {
        $.growl({
            message: message
        }, {
            type: type,
            allow_dismiss: true,
            label: 'Cancel',
            className: 'alert-success btn-primary',
            placement: {
                from: 'top',
                align: 'center'
            },
            delay: 3000,
            animate: {
                enter: 'animated flipInX',
                exit: 'animated flipOutX'
            },
            offset: {
                x: 30,
                y: 30
            }
        });
      };

    $(".mainchk").on('click',function(){

      if($(this).is(":checked")){
         $("#btn_removeall").removeClass('invisible');

            $(".chkbx").each(function( index ) {
              $(this).attr("checked",true);
            });

      }else {
         $("#btn_removeall").addClass('invisible');
            $(".chkbx").each(function( index ) {
              $(this).attr("checked",false);
            });
      }    
     
  });


  $(".chkbx").on('click',function(){
        if($(this).is(":checked")){
          $("#btn_removeall").removeClass('invisible');

        }else {
          $("#btn_removeall").addClass('invisible');
        }

  });


  $(".subchk").on('click',function(){

      if($(this).is(":checked")){
         $("#btn_activeall").removeClass('invisible');

            $(".subchkbx").each(function( index ) {
              $(this).attr("checked",true);
            });

      }else {
         $("#btn_activeall").addClass('invisible');
            $(".subchkbx").each(function( index ) {
              $(this).attr("checked",false);
            });
      }    
     
  });


  $(".subchkbx").on('click',function(){
        if($(this).is(":checked")){
          $("#btn_activeall").removeClass('invisible');

        }else {
          $("#btn_activeall").addClass('invisible');
        }

  });

 $("#btn_activeall").on('click',function(){
  var customers = [];
    $(".subchkbx").each(function( index ) {

        if($(this).is(":checked")){
           if(jQuery.inArray($(this).data('id'), rem_id) == -1){
               rem_id.push($(this).data('id'));
            
           }
        }
      });

    $.ajax({
          url: "{{url('/customer-names')}}",
          type: "POST",
          data: {_token:"{{csrf_token()}}",ids:rem_id},
          async:false,
          success:function(resp){
            console.log(resp);
            for(var s=0;s < resp.length ;s++){
              customers.push(resp[s].name);
            }
          }
        });

    var names = customers.join();

     swal({
          title: "Delete",
          text: "Do you want to Active "+names+" ?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "YES",
          cancelButtonText: "NO",
          closeOnConfirm: false,
          closeOnCancel: false
        },function(isConfirm){
              if(isConfirm){

                 if(rem_id.length > 0){

                          $.ajax({
                              url: "{{url('/multiple-active-customer')}}",
                              type: "POST",
                              data: {_token:"{{csrf_token()}}",id:rem_id},
                              success:function(resp){
                    
                                  if (resp == 1) {
                                        swal({
                                              title: "Success!",
                                              text: "All Customers activated Successfully :)",
                                              type: "success"
                                         },function(isConfirm){
                                             if(isConfirm){
                                              window.location="{{url('/customer')}}";
                                             }
                                         });

                                   }else{
                                          swal("Alert!", "Customers not Deleted:)", "error");                       
                                   }

                              }

                             });
                 }
                 
              }else{
                  swal({
                        title: "Cancel!",
                        text: "Your all customer is safe:)",
                        type: "error"
                   },function(isConfirm){
                       if(isConfirm){
                        window.location="{{url('/customer')}}";
                       }
                   });
               
              }

        });

 });

  $("#btn_removeall").on('click',function(){
        var customers = [];
      $(".chkbx").each(function( index ) {

        if($(this).is(":checked")){
           if(jQuery.inArray($(this).data('id'), rem_id) == -1){
               rem_id.push($(this).data('id'));
            
           }
        }
      });

      $.ajax({
          url: "{{url('/customer-names')}}",
          type: "POST",
          data: {_token:"{{csrf_token()}}",ids:rem_id},
          async:false,
          success:function(resp){
            console.log(resp);
            for(var s=0;s < resp.length ;s++){
              customers.push(resp[s].name);
            }
          }
        });

      var names = customers.join();

      swal({
          title: "Delete",
          text: "Do you want to Delete "+names+" ?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "YES",
          cancelButtonText: "NO",
          closeOnConfirm: false,
          closeOnCancel: false
        },function(isConfirm){
              if(isConfirm){

                 if(rem_id.length > 0){

                          $.ajax({
                              url: "{{url('/all_customers_remove')}}",
                              type: "POST",
                              data: {_token:"{{csrf_token()}}",customerid:rem_id,statusid:2},
                              success:function(resp){
                    
                                  if (resp == 1) {
                                        swal({
                                              title: "Success!",
                                              text: "All Customers deleted Successfully :)",
                                              type: "success"
                                         },function(isConfirm){
                                             if(isConfirm){
                                              window.location="{{url('/customer')}}";
                                             }
                                         });

                                   }else{
                                          swal("Alert!", "Customers not Deleted:)", "error");                       
                                   }

                              }

                             });
                 }
                 
              }else{
                  swal({
                        title: "Cancel!",
                        text: "Your all customer is safe:)",
                        type: "error"
                   },function(isConfirm){
                       if(isConfirm){
                        window.location="{{url('/customer')}}";
                       }
                   });
               
              }

        });          


  });

  $('#downloadsample').click(function(){

        $.ajax({
        url: 'https://sabsoft.com.pk/Retail/public/assets/samples/sample_customer.csv',
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = 'sample_customer.csv';
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }



  });

    });
	
	function openReport(){
		window.open("{{url('customers-report-pdf')}}");
	}
	
	function openExcelReport(){
		window.open("{{url('export-customer-balance')}}");
	}
	
	function changeCheckbox(id,customerId){
		let value = "";
		if($('#' + id).is(":checked")){
			value = 1;
		}else{
			value = 0;
		}
		$.ajax({
			  url: "{{url('/mobile-app-status')}}",
			  type: "POST",
			  data: {_token:"{{csrf_token()}}",id:customerId,value:value},
			  success:function(resp){
				  console.log(resp)
			  }
		 });
	}
	
	

  </script>

@endsection
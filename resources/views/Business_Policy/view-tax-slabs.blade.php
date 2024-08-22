@extends('layouts.master-layout')

@section('title','Tax Slabs')

@section('breadcrumtitle','View Tax Slabs')

@section('navmanage','active') 

@section('navtaxslabs','active')

@section('content')

 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Tax Slabs List</h5>
         <a href="{{ url('/showtaxslabs-create') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Tax Slabs
              </a>
         </div>      
       <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active Tax Slabs</div>
                  </div>
                  <br/>
                      <br/>

     <table id="tbltax" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               </th>
               <th>Rule No</th>
               <th>Tax Slab Minimum</th>
               <th>Tax Slab Maximum</th>
               <th>Percentage</th>
               <th>Session Year</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
          @if($slabs)
            @foreach($slabs as $value)
                    <tr>
                          <td>{{$value->tax_id}}</td>
                          <td>{{$value->slab_min}}</td>
                          <td>{{$value->slab_max}}</td>
                          <td>{{$value->percentage}}</td>
                          <td>{{$value->year}}</td>
                          <td>{{$value->status_name}}</td>
                          <td class="action-icon">
                                <a class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit('{{$value->tax_id}}','{{$value->slab_min}}','{{$value->slab_max}}','{{$value->percentage}}','{{$value->year}}')"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->tax_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                             </td>  
                    </tr>
                    @endforeach
                    @endif
          
         </tbody>
     </table>
  </div>
</div>
</section>


 <!-- modals -->
 <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Update Modal</h4>
             </div>
             <div class="modal-body">
       <div class="row">
           <div class="col-lg-6 col-md-6">
            <input type="hidden" name="taxid" id="taxid" value="">
            
           <div class="form-group {{ $errors->has('slabmin') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Tax Slab Minimum</label>
                  <input class="form-control" type="Number" min="1" 
                   name="slabmin" id="slabmin" value="{{ old('slabmin') }}"  />
                    @if ($errors->has('slabmin'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>
                 <div class="col-lg-6 col-md-6">
            
           <div class="form-group {{ $errors->has('slabmax') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Tax Slab Maximum</label>
                  <input class="form-control" type="Number" min="1"
                   name="slabmax" id="slabmax" value="{{ old('slabmax') }}"  />
                    @if ($errors->has('slabmax'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>
        </div>
 <div class="row">
               <div class="col-lg-6 col-md-6">
            <div class="form-group {{ $errors->has('taxpercentage') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Tax Percentage</label>
                  <input class="form-control" type="Number" min="1" 
                   name="taxpercentage" id="taxpercentage" value="{{ old('taxpercentage') }}"  />
                    @if ($errors->has('taxpercentage'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>

                <div class="col-lg-6 col-md-6">
            <div class="form-group {{ $errors->has('year') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Session Year</label>
                      <input class="form-control" type="Number" min="1" 
                   name="year" id="year" value="{{ old('year') }}"  />
                    @if ($errors->has('year'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>

                </div>
             
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-info waves-effect waves-light" onClick="update()"><i class="icofont icofont-ui-edit"></i>&nbsp; Update</button>
             </div>
          </div>
           </div>
        </div> 

@endsection

@section('scriptcode_three')

<script type="text/javascript">
   $('#tbltax').DataTable( {

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Tax Rules',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );

     $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Do You want to In-Active Tax SLab?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "In-Active!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/inactive-taxslab')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        taxid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "success",
                                        text: "Tax Slab In-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/showtaxslabs-active') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Tax SLab is safe :)", "error");
           }
        });
  });


function edit(id,min,max,per,year){
$('#update-modal').modal('show');
$('#slabmin').val(min);
$('#slabmax').val(max);
$('#taxpercentage').val(per);
$('#year').val(year);
$('#taxid').val(id);
}

  
function update(){

 $.ajax({
                    url: "{{url('/update-taxslabs')}}",
                    type:"POST",
                   data:{_token:"{{ csrf_token() }}",
                   taxid:$('#taxid').val(),
                    slabmin:$('#slabmin').val(),
                    slabmax:$('#slabmax').val(),
                    taxpercentage:$('#taxpercentage').val(),
                    year:$('#year').val(),
          },
                    dataType:"json",
                    success:function(resp){
                 swal({
                      title: "Operation Performed",
                      text: "Tax SLab Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                          $('#update-modal').modal('hide');
                          window.location = "{{url('/showtaxslabs-active')}}";
                      }
                       });
                    }
                  });
}








$('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/showtaxslabs-inactive')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tbltax tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tbltax tbody").append(
                          "<tr>" +
                            "<td>"+result[count].tax_id+"</td>" +  
                            "<td>"+result[count].slab_min+"</td>" +  
                            "<td>"+result[count].slab_max+"</td>" +  
                            "<td>"+result[count].percentage+"</td>" +  
                            "<td>"+result[count].year+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].tax_id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/showtaxslabs-active') }}";
  }
});

function reactive(id){
swal({
          title: "Are you sure?",
          text: "You want to Re-Active Tax Slab!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "yes plx!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/reactive-taxslab')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        taxid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Re-Active",
                                        text: "Tax Slab Re-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/showtaxslabs-active') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled:)", "error");
           }
        });
}






</script>

@endsection

@extends('layouts.master-layout')

@section('title','PF Fund')

@section('breadcrumtitle','View PF Fund')

@section('navmanage','active') 

@section('navtaxslabs','active')

@section('content')

 <section class="panels-wells">
 <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create PF-Fund</h5>
            </div>
            <div class="card-block">

                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Percentage:</label>
                            <input type="text" name="charges" id="charges" class="form-control">
                            <div id="charges_message" class="form-control-feedback text-danger"></div>
                        </div>
                    </div>
					<div class="col-lg-4 col-md-4">
					 <label class="form-control-label"></label>
						<div class="button-group ">
						
							<button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light m-t-5 " onclick="submit()">
								<i class="icofont icofont-plus"> </i>
								Add PF-Fund
							</button>
						</div>
                    </div>
                </div>
            </div>
        </div>
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">PF-Fund List</h5>
         <a href="{{ url('/showtaxslabs-create') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Tax Slabs
              </a>
         </div>      
       <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active PF-Fund Slabs</div>
                  </div>
                  <br/>
                      <br/>

     <table id="tblpffund" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>ID</th>
               <th>Percentage</th>
               <th>Status</th>
            </tr>
         </thead>
         <tbody>
          @if($pffunds)
            @foreach($pffunds as $value)
                    <tr>
                          <td>{{$value->id}}</td>
                          <td>{{$value->rate}}</td>
                          <td>{{$value->status}}</td>
                    </tr>
			@endforeach
		 @endif
         </tbody>
     </table>
  </div>
</div>
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
	function submit()
	{
		$("#charges_message").html("");
		if($("#charges").val() == ""){
			$("#charges_message").html("Please enter percentage.");
		}else{
			$.ajax({
				url: "{{url('/insert-pf-fund')}}",
				type: 'POST',
				data:{
					_token:"{{ csrf_token() }}",
					rate:$("#charges").val(),
				},
				success:function(resp){
					if(resp.status == 200){
						 $("#charges").val("");
						 location.reload();
					}
					if(resp.status == 500){
						$("#charges_message").html(resp.message);
					}
				}

			});
		}
	}
   $('#tblpffund').DataTable( {

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
    getTable(0)
  }
  else{
	getTable(1)
  }
});

function getTable(status)
{
	$.ajax({
            url: "{{url('/get-pf-fund')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",status : status
          },
            success:function(result){
                if(result){
                   $("#tblpffund tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblpffund tbody").append(
                          "<tr>" +
                            "<td>"+result[count].id+"</td>" +  
                            "<td>"+result[count].rate+"</td>" +  
                            "<td>"+result[count].status+"</td>" +   
                          "</tr>"
                         );
                    }

                  }
             }
          });
}

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

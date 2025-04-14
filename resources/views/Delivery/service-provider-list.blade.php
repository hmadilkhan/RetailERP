@extends('layouts.master-layout')

@section('title','Delivery Service Provider')

@section('breadcrumtitle','Delivery Service Provider').

@section('navdelivery','active')

@section('navservices','active')

@section('content')
<section class="panels-wells">
<div class="card">

     <div class="card-header">
         <h5 class="card-header-text">Service Provider Detail</h5>
            <a href="{{ url('/service-provider-create') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Service Provider
              </a>
         </div>
       <div class="card-block">
             <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active Service Provider</div>
                  </div>
                  <br/>
                      <br/>
         <table id="tblservice" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Image</th>
			    @if(session("roleId") == 2)
					<th>Branch</th>
				@endif
               <th>Service Provider</th>
               <th>Category</th>
               <th>Contact Person</th>
               <th>Contact No.</th>
               <th>Percentage</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>

          @foreach($providers as $value)
                 <tr>
                     <td class="text-center">
                         <img width="50" height="50" src="{{ asset('storage/images/service-provider/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                     </td>
					@if(session("roleId") == 2)
						<td >{{$value->branch_name}}</td>
					@endif
                   <td >{{$value->provider_name}}</td>
                   <td >{{$value->category}}</td>
                   <td >{{$value->person}}</td>
                   <td >{{$value->contact}}</td>
                   <td >{{$value->type}}</td>
                   <td >{{$value->status_name}}</td>
                 <td class="action-icon">
                  <a href="{{ url('/service-provider-ledger', Crypt::encrypt($value->id)) }}" class="p-r-10 text-info f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ledger"><i class="icofont icofont-list"></i></a>

                     <a href="{{ url('/service-provider-edit') }}/{{ Crypt::encrypt($value->id) }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                   <a class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="deleteconfirm('{{ $value->id }}')"  data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></a>
                   @if($website != null && $value->category == 'Wallets')
                   <input type="hidden" id="walletId{{ $value->id }}" value="{{ Crypt::encrypt($value->id) }}"/>
                   {{-- <input type="hidden" id="websiteId{{ $value->bank_account_id }}" value="{{ Crypt::encrypt($website->id) }}"/> --}}
                   <input type="hidden" id="websiteWalletUniqueId{{ $value->id }}" value="{{ Crypt::encrypt($value->website_wallet_id) }}"/>
                  <a href="javascript:voide(0)" class="m-r-10" data-toggle="tooltip"
                     data-placement="top" title="" data-original-title="{{ isset($value->website_id) ?  'Unlink to website' : 'Link to website' }}"
                     onclick="website_setting({{ isset($value->website_id) ? $value->website_id : 0 }},'{{$value->provider_name}}',{{ $value->id }})">
                       <i class="icofont {{ isset($value->website_id) ?  'icofont-link text-success' : 'icofont-broken text-muted' }} f-20" ></i>
                     </a>
                @endif
                 </td>

                 </tr>
                  @endforeach

           </tbody>
     </table>
     </div>
          </div>

          <div class="modal fade modal-flex" id="website-detail-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Link to Website</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="wallet_md"/>
                        <div class="form-group">
                           <select id="website_md" class="form-control select2">
                               <option>Select</option>
                               @foreach($website as $val)
                                 <option value="{{ Crypt::encrypt($val->id) }}">{{ $val->name }}</option>
                               @endforeach
                           </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSubmit" class="btn btn-success waves-effect waves-light f-right">
                            Save
                        </button>
                    </div>

                </div>
            </div>
        </div>

</section>

@endsection

@section('scriptcode_three')

<script type="text/javascript">

   $('.select2').select2();
    $('#tblservice').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'',
          searchPlaceholder: 'Search Service Provider',
          lengthMenu: '<span></span> _MENU_'

        }

    });
    $('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/inacive-getserviceprovider')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tblservice tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblservice tbody").append(
                          "<tr>" +
                            "<td>"+result[count].provider_name+"</td>" +
                            "<td>"+result[count].category+"</td>" +
                            "<td>"+result[count].person+"</td>" +
                            "<td>"+result[count].contact+"</td>" +
                            "<td>"+result[count].percentage+"</td>" +
                            "<td>"+result[count].status_name+"</td>" +
                            "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          });
  }
  else{
 window.location="{{ url('/service-provider') }}";
  }
});

function reactive(id){
swal({
          title: "Are you sure?",
          text: "You want to Re-Active Service Provider!",
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
                        url: "{{url('/reactive-serviceprovider')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        providerid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Re-Active",
                                        text: "Service Provider Re-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/service-provider') }}";
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



// $('.alert-confirm').on('click',function(){
function deleteconfirm(id){
    // var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Do You want to In-Active Service Provider?",
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
                        url: "{{url('/inactive-serviceprovider')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        providerid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "success",
                                        text: "Service Provider In-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/service-provider') }}";
                                       }
                                   });
                             }
                        }

                    });

           }else {
              swal("Cancelled", "Operation Cancelled :)", "error");
           }
        });
}
  // });

  function website_setting(value,wallet,code){
        if(value == 0){
            $("#wallet_md").val($("#walletId"+code).val());
           $("#website-detail-modal").modal('show');
        }else{
        swal({
                title: "UnLink to Website",
                text: "Do you want to UnLink from website for this "+wallet+" wallet?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                        $.ajax({
                            url: '{{ route("walletUnLinkToWebsite") }}',
                            type:'POST',
                            data:{ _token:'{{ csrf_token() }}',
                                  uniqueId:$("#websiteWalletUniqueId"+code).val()},
                            dataType:'json',
                            async:true,
                            success:function(resp,textStatus, jqXHR){
                                if(jqXHR.status == 200){
                                    swal({
                                            title: "Success!",
                                            text: "",
                                            type: "success"
                                        }, function(isConfirm) {
                                            if (isConfirm) {
                                                window.location = "{{ url('/service-provider') }}";
                                            }
                                        });
                                }

                                if(jqXHR.status == 500){
                                    swal('Error!',resp,'error');
                                }
                            }
                        })
                }else{
                    swal("Cancel!","","error");
                }

            });
        }
    }

    $("#btnSubmit").on('click',function(){
        if($('#website_md').val() == ''){
            swal("Error!","Select website","error");
        }else{
            $.ajax({
                     url: '{{ route("walletLinkToWebsite") }}',
                     type:'POST',
                     data:{ _token:'{{ csrf_token() }}',
                            website:$("#website_md").val(),
                            wallet:$("#wallet_md").val()},
                     dataType:'json',
                     async:true,
                     success:function(resp,textStatus, jqXHR){
                        if(jqXHR.status == 200){
                                $("#website-detail-modal").modal('hide');
                                    swal({
                                            title: "Success!",
                                            text: "",
                                            type: "success"
                                        }, function(isConfirm) {
                                            if (isConfirm) {
                                                window.location = "{{ url('/service-provider') }}";
                                            }
                                        });
                            }

                            if(jqXHR.status == 500){
                                swal('Error!',resp,'error');
                            }
                        }
                    });
        }

    });

 </script>

@endsection

@extends('layouts.master-layout')

@section('title','Website Details')

@section('breadcrumtitle','Website Details')

@section('navwebsite','active')

@section('content')
<section class="panels-wells p-t-3">

    @if(Session::has('error'))
         <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
         <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Websites {{($mode == 1 ? 'in-active lists' : 'active lists')}}</h5>
         <a href="{{route('website.create')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Website</a>
     </div>
       <div class="card-block">
           		<div class="col-md-12 m-t-3 m-b-2">
		           <a href="{{($mode == 0  ? route('inactiveWebsitelists','in-active') :  route('inactiveWebsitelists'))}}"> <div class="captions">{{($mode == 0 ? 'Show In-Active Lists' : 'Show Active Lists')}}</div> </a>
                </div>
     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Company Name</th>
               <th>Type</th>
               <th>Website Name</th>
               <th>Domain</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
              @foreach($websites as $value)
				<tr>
				  <td class="text-center"><img width="42" height="42" src="{{ asset('storage/images/website/'.(!empty($value->logo) ? $value->logo : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->logo) ? $value->logo : 'placeholder.jpg' }}"></td>
				  <td>{{$value->company->name}}</td>
				  <td>{{$value->name}}</td>
				  <td><a href="{{$value->url}}" target="_blank" class="btn btn-link"><i class="icofont icofont-link"></i> Go to Website</a></td>
				  <td><label class="label label-{{ $value->status == 1 ? 'success' : 'default'}} text-dark p-5">{{ $value->status == 1 ? 'Active' : 'In-Active' }}</label></td>
				  <td class="action-icon">
                    <div class="form-group m-r-2">
                      <label>
                        <input type="checkbox" class="status-toggle" id="websiteStatus-{{ $value->id }}" onchange="websiteMode({{ $value->id }},'{{ addslashes($value->name) }}',{{ $value->status }})" data-toggle="toggle" data-size="mini" data-width="20" data-height="20" {{ $value->status == 1 ? 'checked' : '' }}>
                      </label>
					<form action="{{ route('websiteToggleStatus') }}" method="post" id="websiteTogglestatusForm{{ $value->id }}">
					    @csrf
                        <input type="hidden" name="id" value="{{ $value->id }}">
                        <input type="hidden" name="mode" id="websiteToggleStatusField{{ $value->id }}">
					</form>
                    </div>
					<a href="{{ route('website.edit',$value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
					{{-- <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="remove({{ $value->id }},'{{ addslashes($value->company->name) }}')" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
					<form action="{{ route('website.destroy',$value->id) }}" method="post" id="removeForm{{ $value->id }}">
					    @csrf
					    @method('DELETE')
					</form> --}}


				  </td>
				</tr>
             @endforeach
         </tbody>
     </table>
  </div>
</div>
</section>
@endsection

@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('scriptcode_three')
 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script type="text/javascript">
 	var table = $('.table').DataTable({
                        bLengthChange: true,
                        displayLength: 10,
                        info: false,
                        language: {
                        search:'',
                        searchPlaceholder: 'Search Website',
                        lengthMenu: '<span></span> _MENU_'

                        }

                    });

    table.on('draw', function() {
        $('.status-toggle').bootstrapToggle();
    });

    // function remove(webId,webName){
    //         swal({
    //             title: 'Remove Website',
    //             text:  'Are you sure remove this '+webName+' website?',
    //             type: "warning",
    //             showCancelButton: true,
    //             confirmButtonClass: 'btn btn-danger',
    //             confirmButtonText: "YES",
    //             cancelButtonText: "NO",
    //             closeOnConfirm: false,
    //             closeOnCancel: false
    //         },function(isConfirm){
    //             if(isConfirm){
    //                  $("#removeForm"+webId).submit();
    //             }else{
    //                 swal.close();
    //             }
    //         });
    // }

  function websiteMode(webId,webName,mode){
      let statusVal = (mode == 1 ? 0 : 1);
            swal({
                title: 'Remove Website',
                text:  'Are you sure '+(mode == 1 ? 'In-Active' : 'Active')+' this '+addslashes(webName)+' website?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     $("#websiteToggleStatusField"+webId).val(statusVal);
                     $("#websiteTogglestatusForm"+webId).submit();
                }else{
                    swal.close();
                }
            });
  }

function addslashes(str) {
    return str.replace(/\\/g, '\\\\')  // Escape backslashes
              .replace(/'/g, '\\\'')    // Escape single quotes
              .replace(/"/g, '\\"')     // Escape double quotes
              .replace(/\0/g, '\\0');   // Escape null byte
}
</script>
@endsection

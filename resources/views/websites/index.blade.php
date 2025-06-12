@extends('layouts.master-layout')

@section('title','Website Details')

@section('breadcrumtitle','Website Details')

@section('navwebsite','active')

@section('content')
<section class="panels-wells">

    @if(Session::has('error'))
         <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
         <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Websites</h5>
         <a href="{{route('website.create')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Website</a>
         </div>
       <div class="card-block">

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
                    <div class="form-group m-r-2" style="height:20px;width:20px;">
                      <label>
                        <input type="checkbox" id="websiteStatus-{{ $value->id }}" onchange="websiteMode({{ $value->id }})" data-toggle="toggle" data-size="mini" data-width="20" data-height="20" {{ $value->status == 1 ? 'checked' : '' }}>
                      </label>
                    </div>
					<a href="{{ route('website.edit',$value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="remove({{ $value->id }},'{{ addslashes($value->company->name) }}')" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
					<form action="{{ route('website.destroy',$value->id) }}" method="post" id="removeForm{{ $value->id }}">
					    @csrf
					    @method('DELETE')
					</form>
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
	$('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'',
          searchPlaceholder: 'Search Website',
          lengthMenu: '<span></span> _MENU_'

        }

    });

    function remove(webId,webName){
            swal({
                title: 'Remove Website',
                text:  'Are you sure remove slider from '+webName+' website?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     $("#removeForm"+webId).submit();
                }else{
                    swal.close();
                }
            });
    }
</script>
@endsection

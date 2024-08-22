@extends('layouts.master-layout')

@section('title','Website Details')

@section('breadcrumtitle','Website Details')

@section('navcompany','active')

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
               <th>Domain Name</th>
               <th>Logo</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
              @foreach($websites as $value)
				<tr>
				  <td class="text-center"><img width="42" height="42" src="{{ asset('public/assets/images/website/'.(!empty($value->logo) ? $value->logo : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->logo) ? $value->logo : 'placeholder.jpg' }}"></td>
				  <td>{{$value->company->name}}</td>
				  <td>{{$value->type}}</td>
				  <td>{{$value->web_name}}</td>
				  <td>{{$value->url}}</td>
				  <td>{{($value->status == 1 ? "Active" : "In-Active")}}</td>
				  <td class="action-icon">
					<a href="{{ route('website.edit',$value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
				  </td>
				</tr>
             @endforeach
         </tbody>
     </table>
  </div>
</div>
</section>
@endsection

@section('scriptcode_three')
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
</script>
@endsection
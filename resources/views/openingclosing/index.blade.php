@extends('layouts.master-layout')
@section('breadcrumtitle','Opening Closing')

@section('navbranchoperation','active')
@section('navuser','active')

@section('content')
 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Opening Closing List</h5>
         <a href="{{ url('/create-user') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Vendor" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE USER
              </a>
              <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
         </div>      
       <div class="card-block">
    
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
                     <thead>
                        <tr>
                          <th>Opening ID</th>
                           <th>User</th>
                           <th>Branch</th>
                           <th>Terminal</th>
                           <th>Amount</th>
                           <th>Status</th>
						   <th>Opening At</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
						@foreach($list as $list)
							<tr>
								<td>{{$list->opening_id}}</td>
								<td>{{$list->user_id}}</td>
								<td>{{!empty($list->branch) ?  $list->branch->branch_name : ''}}</td>
								<td>{{!empty($list->terminal) ? $list->terminal->terminal_name : ''}}</td>
								<td>{{$list->balance}}</td>
								<td>{{$list->status}}</td>
								<td>{{$list->date}}</td>
								<td></td>
							</tr>
						@endforeach

                     </tbody>
                 </table>
        </div>
    </div>
   </div>
</section>
@endsection
@section('scriptcode_three')

<script type="text/javascript">
   $('.table').DataTable({
        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Users',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });
</script>
@endsection
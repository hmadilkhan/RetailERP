@extends('layouts.master-layout')

@section('title','Branch Emails ')

@section('breadcrumtitle','Branch Emails ')

@section('navbranchoperation','active')
@section('navbranch','active')

@section('content')
	<section class="panels-wells">
		<div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Branch Emails </h5>
                <h5 class=""><a href="{{ url('branches') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>
            </div>
			<div class="card-block">
				@if( $errors->any() )
					<span class="text-danger m-b-5"> {{$errors->first()}} </span>
				@endif
				<form method="POST" action="{{url('save-email')}}">
					@csrf
                    <input type="hidden" name="branch_id" value="{{Crypt::decrypt($branchId)}}"/>
                    <input type="hidden" name="mode" id="mode" value="insert"/>
                    <input type="hidden" name="email_id" id="email_id" />
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Name</label>
                                <input class="form-control" type="text"
                                       name="name" id="name" placeholder="Enter Name" value="{{ old('name') }}"/>
                                @error('name')
                                    <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('email') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Email</label>
                                <input class="form-control" type="email"
                                       name="email" id="email" placeholder="Enter Email"  value="{{ old('email') }}"/>
                                @error('email')
                                  <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label"></label>
                                <button id="btnSubmit" type="submit"  class="btn btn-md btn-primary waves-effect waves-light m-t-20" >
                                    Save Email
                                </button>
								 <button id="btnCancel" style="display:none;" type="button"  class="btn btn-md btn-danger waves-effect waves-light m-t-20" >
                                    Cancel
                                </button>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
			</div>
		</div>	
	</section>
	
	<section class="panels-wells">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Filter Ledger Details</h5>
			</div>
			<div class="card-block">
				<div class="project-table">
					<table class="table table-striped nowrap dt-responsive" width="100%">
						<thead>
							<tr>
								<th>ID#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@if($emails)
								@foreach($emails as $email)
									<tr>
										<td>{{$email->id}}</td>
										<td>{{$email->name}}</td>
										<td>{{$email->email}}</td>
										<td>
											<a onclick="edit('{{$email->id}}','{{$email->name}}','{{$email->email}}')" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
											<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{$email->id}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>&nbsp;
										</td>
									</tr>
								@endforeach
							@endif
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
	$('.table').DataTable();
	
	function edit(id,name,email)
	{
		$("#email_id").val(id);
		$("#name").val(name);
		$("#email").val(email);
		$("#mode").val("update");
		$("#btnSubmit").html("Update Email");
		$("#btnCancel").css("display","block");
	}
	
	$("#btnCancel").click(function(){
		$("#email_id").val('');
		$("#name").val('');
		$("#email").val('');
		$("#mode").val("insert");
		$("#btnSubmit").html("Save Email");
		$("#btnCancel").css("display","none");
	})
	
	$('.alert-confirm').on('click',function(){
    var id= $(this).data("id");
        swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this imaginary file!",
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
                        url: "{{url('/delete-email')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove branch email.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/branch-emails') }}"+"/"+"{{$branchId}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your branch is safe :)", "error");
           }
        });
	});
</script>
@endsection
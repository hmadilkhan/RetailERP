@extends('layouts.master-layout')
@section('title','Edit Website')
@section('breadcrumtitle','Edit Website')
@section('navwebsite','active')
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 m-t-2">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit mr-2"></i>Edit Website</h4>
                </div>
                <div class="card-body" style="padding: 10px;">
                    <form method="POST" action="{{route('website.update',$website->id)}}" enctype="multipart/form-data">
                        @csrf
                        @method("PUT")
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company</label>
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-building mr-2"></i>{{ $website->company_name }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Website Type *</label>
                                    @php $oldWebType = old('type') ? old('type') : $website->type @endphp
                                    <select class="form-control" name="type">
                                        <option value="">Select Type</option>
                                        <option {{ $oldWebType == 'restaurant' ? 'selected' : '' }} value="restaurant">Restaurant</option>
                                        <option {{ $oldWebType == 'grocery' ? 'selected' : '' }} value="grocery">Grocery</option>
                                        <option {{ $oldWebType == 'boutique' ? 'selected' : '' }} value="boutique">Boutique</option>
                                        <option {{ $oldWebType == 'shopify' ? 'selected' : '' }} value="shopify">Shopify</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Website Name *</label>
                                    <input name="name" type="text" class="form-control" placeholder="Enter website name" value="{{ old('name') ? old('name') : $website->name }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Domain Name</label>
                                    <input name="url" type="url" class="form-control" placeholder="https://example.com" value="{{ old('url') ? old('url') : $website->url }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">UAN Number</label>
                                    <input name="uan_number" type="text" class="form-control" placeholder="Enter UAN number" value="{{ old('uan_number') ? old('uan_number') : $website->uan_number }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">WhatsApp Number</label>
                                    <input name="whatsapp" type="text" class="form-control" placeholder="Enter WhatsApp number" value="{{ old('whatsapp') ? old('whatsapp') : $website->whatsapp }}">
                                </div>
                            </div>
                        </div>

						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Github Token</label>
                                    <input name="github_token" type="text" class="form-control" placeholder="Enter Github Token" value="{{ old('github_token')  ? old('github_token') : $website->github_token }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">github Owner</label>
                                    <input name="github_owner" type="text" class="form-control" placeholder="Enter Github Owner" value="{{ old('github_owner') ? old('github_owner') : $website->github_owner }}">
                                </div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Github Repo</label>
                                    <input name="github_repo" type="text" class="form-control" placeholder="Enter Github Repo" value="{{ old('github_repo') ? old('github_repo') : $website->github_repo }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Github Branch</label>
                                    <input name="github_branch" type="text" class="form-control" placeholder="Enter Github Owner" value="{{ old('github_branch') ? old('github_branch') : $website->github_branch }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Logo</label>
                                    <div class="text-center mb-3">
                                        @php $logo = $website->logo != "" ? 'website/'.$website->logo : 'placeholder.jpg' @endphp
                                        <img id="preview" src="{{ asset('storage/images/'.$logo) }}" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <input type="file" name="logo" id="logo" class="form-control m-t-1" accept="image/*">
                                    @error('logo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Favicon</label>
                                    <div class="text-center mb-3">
                                        @php $favicon = $website->favicon != "" ? 'website/'.$website->favicon : 'placeholder.jpg' @endphp
                                        <img id="fpreview" src="{{ asset('storage/images/'.$favicon) }}" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <input type="file" name="favicon" id="favicon" class="form-control m-t-1" accept="image/*">
                                    @error('favicon')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('website.index') }}" class="btn btn-secondary  m-r-1">
                                <i class="fas fa-times "></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save "></i>Update Website
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	$(".select2").select2();

	function readURL(input,id) {
	  if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
		  $('#'+id).attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	  }
	}

	$("#logo").change(function() {
	  readURL(this,'preview');
	});

	$("#favicon").change(function() {
	  readURL(this,'fpreview');
	});
</script>
@endsection

@section('css_code')
<style>
.card { border: none; border-radius: 10px; }
.form-label { color: #495057; font-size: 14px; }
.form-control { border-radius: 6px; border: 1px solid #dee2e6; }
.form-control:focus { border-color: #ffc107; box-shadow: 0 0 0 0.2rem rgba(255,193,7,.25); }
.btn { border-radius: 6px; padding: 8px 20px; }
.img-thumbnail { border-radius: 8px; }
.alert-info { background-color: #e3f2fd; border-color: #bbdefb; color: #0d47a1; }
</style>
@endsection

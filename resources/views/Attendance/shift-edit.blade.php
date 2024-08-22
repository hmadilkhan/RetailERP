@extends('layouts.master-layout')

@section('title','Office Shift')

@section('breadcrumtitle','Office Shift')

@section('navattendance','active')

@section('navshift','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Edit Office Shift</h5>
            <div class="new-users-more text-left p-t-10">
        <a href="{{ url('/view-shift') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
        </div>
         </div>      
       <div class="card-block">
        <form method="post" action="{{url('/update-shift')}}" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}

       	 <div class="row">
       	 	     <div class="col-lg-6 col-md-6">
                           <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Branch:</label>
                                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
                                    <option value="">Select Branch</option>
                                    @if($getbranch)
                                      @foreach($getbranch as $value)
                                         <option {{$value->branch_id == $details[0]->branch_id ? 'selected="selected"' : '' }}
                                         value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                    @if ($errors->has('branch'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                            </div>
                        </div>

           <div class="col-lg-6 col-md-6">
               <div class="form-group {{ $errors->has('shiftname') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Shift Name:</label>
                  <input type="hidden" name="shiftid" value="{{$details[0]->shift_id}}">
                  <input class="form-control" type="text" 
                   name="shiftname" id="shiftname" value="{{$details[0]->shiftname}}"  />
                    @if ($errors->has('shiftname'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
				</div>
              </div>
          
        </div>
         <div class="row">
            <div class="col-lg-4 col-md-4">
            
               <div class="form-group {{ $errors->has('shiftstart') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Shift Start Time:</label>
			<!-- 	<input type="text" id="time" class="form-control floating-label" placeholder="Time"> -->
                  <input class="form-control" type="Time" 
                   name="shiftstart" id="shiftstart" value="{{$details[0]->shift_start}}"  />
                   <div class="form-control-feedback">Hint: Time format 12:00:PM</div>
                    @if ($errors->has('shiftstart'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
				</div>
              </div>
                 <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('shiftend') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Shift End Time:</label>
                  <input class="form-control" type="Time"
                   name="shiftend" id="shiftend" value="{{$details[0]->shift_end}}"  />
                   <div class="form-control-feedback">Hint: Time format 12:00:AM</div>
                    @if ($errors->has('shiftend'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
				</div>
              </div>
                     <div class="col-lg-2 col-md-2">
               <div class="form-group {{ $errors->has('gracetime') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">GT (Late Count):</label>
                  <input class="form-control" type="Number"
                   name="gracetime" id="gracetime" value="{{ $details[0]->grace_time_in }}"  />
                   <div class="form-control-feedback">Hint: Enter Minutes</div>
                    @if ($errors->has('gracetime'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>

                 <div class="col-lg-2 col-md-2">
               <div class="form-group {{ $errors->has('gracetimeearly') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">GT (Early Count):</label>
                  <input class="form-control" type="Number"
                   name="gracetimeearly" id="gracetimeearly" value="{{ $details[0]->grace_time_out }}"  />
                   <div class="form-control-feedback">Hint: Enter Minutes</div>
                    @if ($errors->has('gracetimeearly'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>



          </div>
     	<button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > Edit Shift </button>
                
               </form>  
           </div> 
 </div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
$(".select2").select2();
</script>
@endsection


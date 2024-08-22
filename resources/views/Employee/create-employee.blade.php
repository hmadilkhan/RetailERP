@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','View Employee')

@section('navemployees','active')

@section('navhire','active')

@section('navemployee','active')

@section('content')


<section class="panels-wells ">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Hire Employee</h5>
          <h6 class=""><a href="{{ url('/view-employee') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}

          <h5>Personal Details</h5>
           <div class="row">
           	 <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('empacc') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Employee ACC No.</label>
            <input type="number" min="1" name="empacc" id="empacc" class="form-control" value="{{ old('empacc') }}" onchange="accchk()" />
             @if ($errors->has('empacc'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
           	 <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('empname') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Employee Name</label>
            <input type="text" name="empname" id="empname" class="form-control" value="{{ old('empname') }}"/>
             @if ($errors->has('empname'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
          	 <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('fname') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Father Name</label>
            <input type="text" name="fname" id="fname" class="form-control" value="{{ old('fname') }}"/>
             @if ($errors->has('fname'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
        
           </div>
           <div class="row">
           	 <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('empnic') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Employee CNIC.</label>
            <input type="text" name="empnic" id="empnic" class="form-control" placeholder="42101-1234567-8" value="{{ old('empnic') }}"/>
             @if ($errors->has('empnic'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
           	 <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('empcontact') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Employee Contact</label>            
            <input type="text" name="empcontact" id="empcontact" placeholder="0311-1234567" class="form-control" value="{{ old('empcontact') }}"/>
             @if ($errors->has('empcontact'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
        <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Gender</label>
                 
                <select name="gender" id="gender" data-placeholder="Select Gender" class="form-control select2" >
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Transgender">Transgender</option>
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
           </div>
           <div class="row">
                     <div class="col-lg-12 col-md-12">
           <div class="form-group {{ $errors->has('empaddress') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Employee Address</label>
           <textarea class="form-control" name="empaddress" id="empaddress"></textarea>
             @if ($errors->has('empaddress'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
           </div>
           <h5>Office Details</h5>
         <div class="row">
       
                <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Branch</label>
                 
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" onchange="getshifts()" >
                    <option value="">Select Branch</option>
                    @if($getbranch)
                      @foreach($getbranch as $value)
                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                      @endforeach
                    @endif
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
          
                    <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Department</label>
                 <i id="btn_adddepart" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Department" ></i>
                <select name="department" id="department" data-placeholder="Select department" class="form-control select2" onchange="getdesg()" >
                    <option value="">Select department</option>
					@if(!empty($departments))
                      @foreach($departments as $value)
                        <option value="{{ $value->department_id }}">{{ $value->department_name }}</option>
                      @endforeach
                    @endif
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

               <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Designation</label>
                 <i id="btn_desg" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Designation" ></i>
                <select name="designation" id="designation" data-placeholder="Select Designation" class="form-control select2" >
                    <option value="">Select Designation</option>
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
            
        </div>

        
                 <div class="row">
                     <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Office Shift:</label>
                     <select name="officeshift" id="officeshift" data-placeholder="Select Office Shift" class="form-control select2" >
                    <option value="">Select Office Shift</option>
                </select>
                        </div>
                      </div>

                         <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('doj') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Date of Joining</label>
			<label class="form-control-label f-right m-l-10">New Hiring</label><input class="form-control-label f-right " type="checkbox" name="new_hiring"/>
            <input type="text" name="doj" id="doj" class="form-control" placeholder="23-12-2019" value="{{ old('doj') }}"/>
             @if ($errors->has('doj'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
              

 
                 

                  </div>
                  <h5>Salary Details</h5>
                  <div class="row">
          <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Salary Category:</label>
                     <select name="cat" id="cat" data-placeholder="Select Category" class="form-control select2" onchange="place()" >
                    <option value="">Select Category</option>
                    @if(!empty($category))
                      @foreach($category as $value)
                        <option value="{{ $value->id }}">{{ $value->category }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
						
                      </div>
					  
           
        @if(!empty($permission) && $permission[0]->taxes == 0)
             <div class="col-lg-4 col-md-4">
              <div class="form-group">
                <label class="form-control-label">Tax Applicable</label>
                 <input type="hidden" name="tax" value="0" readonly="true">
                <select name="tax" id="tax" data-placeholder="Select" class="form-control select2" onchange="togglediv()" disabled="">
                    <option value="">Select</option>
                    <option value="1">YES</option>
                    <option selected="" value="0">NO</option>
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
             </div>
        @else
                 <div class="col-lg-4 col-md-4">
              <div class="form-group">
                <label class="form-control-label">Tax Applicable</label>
                 
                <select name="tax" id="tax" data-placeholder="Select" class="form-control select2" onchange="togglediv()" >
                    <option value="">Select</option>
                    <option value="1">YES</option>
                    <option  value="0">NO</option>
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
        </div>
		<div class="col-lg-4 col-md-4" id="dvtaxes">
                      <div class="form-group"> 
                        <label class="form-control-label">Tax Slab:</label>
                     <select name="taxslab" id="taxslab" data-placeholder="Select Tax Slab" class="form-control select2" >
                    <option value="">Select Tax Slab</option>
                       @if($taxslabs)
                      @foreach($taxslabs as $value)
                        <option value="{{ $value->tax_id }}">{{ $value->slab_min }} -- {{ $value->slab_max}}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
        @endif

           </div>
                  <div class="row">
                    @if(!empty($permission) && $permission[0]->overtime == 0)
              <div class="col-lg-3 col-md-3">
                      <div class="form-group"> 
                        <label class="form-control-label">Over Time Amount:</label>
                    <input type="hidden" name="otamount" value="1">
                     <select name="otamount" id="otamount" data-placeholder="Over Time Amount" class="form-control select2" disabled="">
                    <option value="">Over Time Amount</option>
                       @if($otamount)
                      @foreach($otamount as $value)
                      @if($value->otamount_id == 1)
                        <option selected="" value="{{ $value->otamount_id }}">{{ $value->amount }}</option>
                        @else
                          <option value="{{ $value->otamount_id }}">{{ $value->amount }}</option>
                          @endif
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                             <div class="col-lg-3 col-md-3">
                      <div class="form-group"> 
                        <label class="form-control-label">Over Time Count Duration:</label>
                      <input type="hidden" name="otduration" value="1">
                     <select name="otduration" id="otduration" data-placeholder="Over Time Duration" class="form-control select2" disabled="disabled" >
                    <option value="">Over Time Duration</option>
                       @if($otduration)
                      @foreach($otduration as $value)
                      @if($value->otduration_id == 1)
                        <option selected="" value="{{ $value->otduration_id }}">{{ $value->duration." minutes"}}</option>
                        @else
                        <option value="{{ $value->otduration_id }}">{{ $value->duration." minutes"}}</option>
                        @endif

                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                      @else
                       <div class="col-lg-3 col-md-3">
                      <div class="form-group"> 
                        <label class="form-control-label">Over Time Amount:</label>
                        <i id="btn_otamount" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Amount"></i>
                     <select name="otamount" id="otamount" data-placeholder="Over Time Amount" class="form-control select2" >
                    <option value="">Over Time Amount</option>
                       @if($otamount)
                      @foreach($otamount as $value)
                          <option value="{{ $value->otamount_id }}">{{ $value->amount }}</option>
                          
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                             <div class="col-lg-3 col-md-3">
                      <div class="form-group"> 
                        <label class="form-control-label">Over Time Count Duration:</label>
                        <i id="btn_otduration" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Duration" ></i>
                     <select name="otduration" id="otduration" data-placeholder="Over Time Duration" class="form-control select2"  >
                    <option value="">Over Time Duration</option>
                       @if($otduration)
                      @foreach($otduration as $value)
                        <option value="{{ $value->otduration_id }}">{{ $value->duration." minutes"}}</option>
                        
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                      @endif

                      
					  <div class="col-lg-3 col-md-3">
						  <div class="form-group">
							<label class="form-control-label">PF Applicable</label>
							 <input type="hidden" name="tax" value="0" readonly="true">
							<select name="pf_enable" id="pf_enable" data-placeholder="Select" class="form-control select2">
								<option value="">Select</option>
								<option selected="" value="1">YES</option>
								<option  value="0">NO</option>
							</select>
							 <div class="form-control-feedback"></div>
							  </div>
						</div>
						
						<div class="col-lg-3 col-md-3">
						  <div class="form-group">
							<label class="form-control-label">Security Deposit</label>
							 <input type="hidden" name="tax" value="0" readonly="true">
							<select name="security_deposit" id="security_deposit" data-placeholder="Select" class="form-control select2">
								<option value="">Select</option>
								<option value="1">YES</option>
								<option selected="" value="0">NO</option>
							</select>
							 <div class="form-control-feedback"></div>
							  </div>
						</div>
						
						<div class="col-lg-3 col-md-3">
						   <div class="form-group {{ $errors->has('grosspay') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Gross Salary | Per day Salary</label>
							<input type="number" name="grosspay" id="grosspay" placeholder="100"   class="form-control" value="{{ old('grosspay') }}"/>
							 @if ($errors->has('grosspay'))
								<div class="form-control-feedback">Required field can not be blank.</div>
							@endif
						</div>
						</div>
						<div class="col-lg-3 col-md-3">
						   <div class="form-group {{ $errors->has('basicpay') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Basic Salary | Per day Salary</label>
							<input type="number" name="basicpay" id="basicpay" placeholder="100"   class="form-control" value="{{ old('basicpay') }}"/>
							 @if ($errors->has('basicpay'))
								<div class="form-control-feedback">Required field can not be blank.</div>
							@endif
						</div>
						</div>
						<div class="col-lg-3 col-md-3">
						   <div class="form-group {{ $errors->has('pf_fund') ? 'has-danger' : '' }} ">
							<label class="form-control-label">PF Fund</label>
							<input type="number" name="pf_fund" id="pf_fund" placeholder="100"   class="form-control" value="{{ old('pf_fund') }}"/>
							 @if ($errors->has('pf_fund'))
								<div class="form-control-feedback">Required field can not be blank.</div>
							@endif
						</div>
						</div>
						<div class="col-lg-3 col-md-3">
						   <div class="form-group {{ $errors->has('allowance') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Allowance</label>
							<input type="number" name="allowance" id="allowance" placeholder="100"   class="form-control" value="{{ old('allowance') }}"/>
							 @if ($errors->has('allowance'))
								<div class="form-control-feedback">Required field can not be blank.</div>
							@endif
						</div>
						</div>
                  </div>
         <div class="row">


                <div class="col-lg-4 col-md-4">
                    <a href="#">
                <img id="empimgs" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                </a>
             <div class="form-group{{ $errors->has('empimg') ? 'has-danger' : '' }} ">
                 <label for="empimg" class="form-control-label">Profile Picture</label>
                <br/>
                    <label for="empimg" class="custom-file">
                     <input type="file" name="empimg" id="empimg" class="custom-file-input">
                    <span class="custom-file-control"></span>
                    </label>
              </div>
              </div>
            <div class="col-lg-4 col-md-4">
                        <a href="#">
                            <img id="docimgs1" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                        </a>
                        <div class="form-group">
                            <label for="docimg1" class="form-control-label">Document Image 1</label>
                            <br/>
                            <label for="docimg1" class="custom-file">
                                <input type="file" name="docimg1" id="docimg1" class="custom-file-input" multiple="">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>
                    </div>

             <div class="col-lg-4 col-md-4">
                 <a href="#">
                     <img id="docimgs2" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                 </a>
                 <div class="form-group">
                     <label for="docimg2" class="form-control-label">Document Image 2</label>
                     <br/>
                     <label for="docimg2" class="custom-file">
                         <input type="file" name="docimg2" id="docimg2" class="custom-file-input" multiple="">
                         <span class="custom-file-control"></span>
                     </label>
                 </div>
             </div>


      <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-plus"> </i>
                        Hire Employee
                    </button>
                    <!--  <button type="button" id="btndraft" class="btn btn-md btn-default waves-effect waves-light f-right m-r-20">   <i class="icofont icofont-save"> </i>
                        Save as Pre-Hire Data
                    </button> -->
                    </div>       
                </div>  
            </div> 
               </form>  
           </div> 
 </div>

 <!-- modals -->
<div class="modal fade modal-flex" id="desg-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Add Designation</h4>
             </div>
             <div class="modal-body">
              <div class="row">
            <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Select Department</label>
                   <select name="departmodal" id="departmodal" data-placeholder="Select Department" class="form-control select2">
                    <option value="">Select Department</option>
                    @if($departments)
                      @foreach($departments as $value)
                        <option value="{{ $value->department_id }}">{{ $value->department_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
              </div>
               <div class="row">

                     <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Designation Name:</label>
                         <input type="text"  name="desgname" id="desgname" class="form-control" />
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light" onClick="adddesg()">Add Designation</button>
             </div>
          </div>
           </div>
        </div> 

<div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
		<h4 class="modal-title">Add Department</h4>
	 </div>
	 <div class="modal-body">
		 <div class="row">
			 <div class="col-md-12">
			  <div class="form-group"> 
				<label class="form-control-label">Select Branch:</label>
			 <select name="branch-modal" id="branch-modal" data-placeholder="Select Branch" class="form-control select2" >
			<option value="">Select Branch</option>
			@if($getbranch)
			  @foreach($getbranch as $value)
				<option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
			  @endforeach
			@endif
		</select>
				</div>
			  </div>
		  </div> 
	   <div class="row">
			 <div class="col-md-12">
			  <div class="form-group"> 
				<label class="form-control-label">Department Name:</label>
				 <input type="text"  name="depart" id="depart" class="form-control" />
				</div>
			  </div>
		  </div>   
	 </div>
	 <div class="modal-footer">
		<button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="adddepart()">Add Department</button>
	 </div>
  </div>
   </div>
</div> 

<div class="modal fade modal-flex" id="ot-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
		<h4 class="modal-title">Add OT Formula</h4>
	 </div>
	 <div class="modal-body">
	   <div class="row">
			 <div class="col-md-12">
			  <div class="form-group"> 
				<label class="form-control-label">OT Formula:</label>
				 <input type="text"  name="modalotofrmula" id="modalotofrmula" class="form-control" />
				</div>
			  </div>
		  </div>   
	 </div>
	 <div class="modal-footer">
		<button type="button" id="btn_otformula" class="btn btn-success waves-effect waves-light" onClick="addot()">Add OT Formula</button>
	 </div>
  </div>
   </div>
</div> 

<div class="modal fade modal-flex" id="otamount-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
		<h4 class="modal-title">Add Over Time Amount</h4>
	 </div>
	 <div class="modal-body">
	   <div class="row">
			 <div class="col-md-12">
			  <div class="form-group"> 
				<label class="form-control-label">Amount:</label>
				 <input type="number"  name="modalotamount" id="modalotamount" class="form-control" />
				</div>
			  </div>
		  </div>   
	 </div>
	 <div class="modal-footer">
		<button type="button" class="btn btn-success waves-effect waves-light" onClick="addotamount()">Add Over Time Amount</button>
	 </div>
  </div>
   </div>
</div> 

<div class="modal fade modal-flex" id="otduration-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
	 <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
		<h4 class="modal-title">Add Over Time Duration</h4>
	 </div>
	 <div class="modal-body">
	   <div class="row">
			 <div class="col-md-12">
			  <div class="form-group"> 
				<label class="form-control-label">Duration:</label>
				<div class="input-group">
			  <input type="number" id="modalotduration" class="form-control"  aria-describedby="basic-addon2" min="1" name="modalotduration"  value="{{ old('loan') }}">
			  <span class="input-group-addon" id="basic-addon2">minutes</span>
	   
		   </div>
				 <!-- <input type="number"  name="modalotduration" id="modalotduration" class="form-control" /> -->
				</div>
			  </div>
		  </div>   
	 </div>
	 <div class="modal-footer">
		<button type="button" class="btn btn-success waves-effect waves-light" onClick="addotduration()">Add Over Time Duration</button>
	 </div>
  </div>
   </div>
</div> 

<div class="modal fade modal-flex" id="cat-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Add Salary Category</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Category:</label>
                         <input type="text"  name="modalcategory" id="modalcategory" class="form-control" />
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_cat" class="btn btn-success waves-effect waves-light" onClick="addcat()">Add Category</button>
             </div>
          </div>
           </div>
        </div> 

</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

  $('#doj').bootstrapMaterialDatePicker({
      format: 'DD-MM-YYYY',
      time: false,
      clearButton: true,

    icons: {
        date: "icofont icofont-ui-calendar",
        up: "icofont icofont-rounded-up",
        down: "icofont icofont-rounded-down",
        next: "icofont icofont-rounded-right",
        previous: "icofont icofont-rounded-left"
      }
  });
  

  function readURL(input,id) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#'+id).attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

$("#empimg").change(function() {
  readURL(this,'empimgs');
});

  $("#docimg1").change(function() {
      readURL(this,'docimgs1');
  });

  $("#docimg2").change(function() {
      readURL(this,'docimgs2');
  });





function place(){
  if ($('#cat').val() == 1) {
	$("#basicpay").attr("placeholder","Please Enter One Day Salary");  
	$("#grosspay").attr("placeholder","Please Enter One Day Salary");  
  }
  else{
    $("#basicpay").attr("placeholder","Please Enter Month Salary");  
    $("#grosspay").attr("placeholder","Please Enter Month Salary");  
  }
  
}

$('#dvtaxes').hide(); 
function togglediv(){
  if ($('#tax').val() == 1) {
    $('#dvtaxes').show();
  }
  else{
   $('#dvtaxes').hide(); 
  }
}


$('#upload_form').on('submit', function(event){
event.preventDefault();

	if ($('#empacc').val() == "") {
		 swal({
            title: "Error Message",
            text: "Employee ACC No. Can not left blank!",
            type: "error"
              });
	}
	else if ($('#empname').val() == "") {
		 swal({
            title: "Error Message",
            text: "Employee Name Can not left blank!",
            type: "error"
              });
	}
	else if ($('#empnic').val() == "") {
		 swal({
				title: "Error Message",
				text: "Employee CNIC Can not left blank!",
				type: "error"
             });
	}
	else if ($('#branch').val() == "") {
		swal({
            title: "Error Message",
            text: "Please Select Branch!",
            type: "error"
              });
	}

	else if ($('#department').val() == "") {
		swal({
            title: "Error Message",
            text: "Please Select Department!",
            type: "error"
            });
	}
  else if ($('#designation').val() == "" || $('#designation').val() == null) {
    swal({
            title: "Error Message",
            text: "Please Select Designation!",
            type: "error"
             });
  }
  else if ($('#doj').val() == "") {
    swal({
            title: "Error Message",
            text: "Date of Joining can not left blank!",
            type: "error"
            });
  }
	else if ($('#basicpay').val() == "") {
		swal({
            title: "Error Message",
            text: "Employee Basic Pay Can not left blank!",
            type: "error"
            });
	}
  else if ($('#officeshift').val() == "" || $('#officeshift').val() == null) {
    swal({
            title: "Error Message",
             text: "Please Select Office Shift!",
            type: "error"
            });
  }
  else if ($('#cat').val() == "") {
    swal({
            title: "Error Message",
             text: "Please Select Salary Category!",
            type: "error"
            });
  }
  else if ($('#tax').val() == "") {

    swal({
            title: "Error Message",
             text: "Please Select Tax Applicable!",
            type: "error"
            });
  }
  else if ($('#tax').val() == 1 && $('#taxslab').val() == "" ) {

    swal({
            title: "Error Message",
             text: "Please Select Tax Slab!",
            type: "error"
            });

  }
	else{
	$("#btnsubmit").prop('disabled', true);
    $.ajax({
    	 url: "{{url('/insert-employee')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
		success:function(resp){
			if(resp.status == 200){
				 swal({
						title: "Success",
						text: resp.message,
						type: "success"
				   },function(isConfirm){
					   if(isConfirm){
							 window.location= "{{ url('/view-employee') }}";
					   }
				   });
			  }else if(resp.status == 500){
				  swal({
						title: "Error",
						text: resp.message,
						type: "error"
				   });
				   $("#btnsubmit").prop('disabled', false);
			  }
		 }

  });        
}

});

function accchk(){
	 $.ajax({
        url: "{{url('/chk-employee')}}",
        type: 'GET',
        data:{_token:"{{ csrf_token() }}",
        empacc:$('#empacc').val(),
    },
        success:function(resp){
        if(resp.length == 1){
              swal({
      title: "Message",
      text: resp[0].emp_acc+" Employee ACC already allocated to "+resp[0].emp_name + "!",
      type: "warning"
               },function(isConfirm){
                   if(isConfirm){
                         window.location= "{{ url('/show-employee') }}";
                   }
               });
            }
          }
    });
  }



$("#btn_desg").on('click',function(){
  $('#desgname').val('');
	$("#desg-modal").modal("show");
});

$("#btn_adddepart").on('click',function(){
  $('#depart').val('');
	$("#depart-modal").modal("show");
});

$("#btn_ot").on('click',function(){
  $('#modalotofrmula').val('');
  $("#ot-modal").modal("show");
});
$("#btn_otamount").on('click',function(){
  $('#modalotamount').val('');
  $("#otamount-modal").modal("show");
});
$("#btn_otduration").on('click',function(){
  $('#modalotduration').val('');
  $("#otduration-modal").modal("show");
});



$("#btn_category").on('click',function(){
  $('#modalcategory').val('');
  $("#cat-modal").modal("show");
});




function adddesg(){
	if ($('#desgname').val() == "") {
		swal({
            title: "Error Message",
            text: "Designation Can not left blank!",
            type: "error"
            });
	}
  else if($('#departmodal').val() == "") {
    swal({
            title: "Error Message",
            text: "Department Can not left blank!",
            type: "error"
            });
  }
	else{

	 $.ajax({
            url: "{{url('/store-desg')}}",
            type: 'POST',
       		data:{_token:"{{ csrf_token() }}",
       		dataType:"json",
       		desg:$('#desgname').val(),
            depart:$('#departmodal').val(),
       	},
            success:function(resp){            	
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Designation Created Successfully!",
                      type: "success"});

                      $("#desg-modal").modal("hide");

                      $("#designation").empty();
                     for(var count=0; count < resp.length; count++){
                     	$("#designation").append("<option value=''>Select Designation</option>");
                     	$("#designation").append(
                     		"<option value='"+resp[count].designation_id+"'>"+resp[count].designation_name+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Designation Already exsit!",
                            type: "warning"
                       });
                  }
                 
             }

          }); 
	}
}


function adddepart(){
	if ($('#depart').val() == "") {
		swal({
            title: "Error Message",
            text: "Department Can not left blank!",
            type: "error"
            });
	}
	else{

	 $.ajax({
            url: "{{url('/store-depart')}}",
            type: 'POST',
       		data:{_token:"{{ csrf_token() }}",
       		dataType:"json",
       		branch:$('#branch-modal').val(),
       		department:$('#depart').val(),
       	},
            success:function(resp){   
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Department Created Successfully!",
                      type: "success"});

                      $("#depart-modal").modal("hide");

                      $("#department").empty();
                    $("#departmodal").empty();
                     for(var count=0; count < resp.length; count++){
                     	$("#department").append("<option value=''>Select Department</option>");
                     	$("#department").append(
                     		"<option value='"+resp[count].department_id+"'>"+resp[count].department_name+"</option>");

                         $("#departmodal").append("<option value=''>Select Department</option>");
                         $("#departmodal").append(
                             "<option value='"+resp[count].department_id+"'>"+resp[count].department_name+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Department Already exsit!",
                            type: "warning"
                       });
                  }
                 
             }

          }); 
	}
}
function getdesg(){
   $.ajax({
            url: "{{url('/getdesg-departwise')}}",
            type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          departid:$('#department').val(),
        },
            success:function(resp){   
            $("#designation").empty();          
                     for(var count=0; count < resp.length; count++){
                      $("#designation").append("<option value=''>Select Designation</option>");
                      $("#designation").append(
                        "<option value='"+resp[count].designation_id+"'>"+resp[count].designation_name+"</option>");
                  }
             }
          }); 
}   


function getshifts(){
   $.ajax({
            url: "{{url('/getshifts')}}",
            type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          branchid:$('#branch').val(),
        },
            success:function(resp){   
              console.log(resp);
            $("#officeshift").empty();          
                     for(var count=0; count < resp.length; count++){
                      $("#officeshift").append("<option value=''>Select Office Shift</option>");
                      $("#officeshift").append(
                        "<option value='"+resp[count].shift_id+"'>"+resp[count].shiftname+"</option>");
                  }
             }
          }); 
   getdeparts();
}   

function getdeparts(){
   $.ajax({
            url: "{{url('/getdepart-branchwise')}}",
            type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          branchid:$('#branch').val(),
        },
            success:function(resp){   
              
            $("#department").empty();          
                     for(var count=0; count < resp.length; count++){
                      $("#department").append("<option value=''>Select Department</option>");
                      $("#department").append(
                        "<option value='"+resp[count].department_id+"'>"+resp[count].department_name+"</option>");
                  }
             }
          }); 
}   



function addot(){
  if ($('#modalotofrmula').val() == "") {
    swal({
            title: "Error Message",
            text: "OT Formula Can not left blank!",
            type: "error"
            });
  }
  else{

   $.ajax({
            url: "{{url('/insert-ot')}}",
            type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          otformula:$('#modalotofrmula').val(),
          chk:1,
        },
            success:function(resp){             
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "OT Formula Created Successfully!",
                      type: "success"});

                      $("#ot-modal").modal("hide");

                      $("#ot").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#ot").append("<option value=''>Select OT Formula</option>");
                      $("#ot").append(
                        "<option value='"+resp[count].OT_formulaid+"'>"+resp[count].OTFormula+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular OT Formula Already exsit!",
                            type: "warning"
                       });
                  }
                 
             }

          }); 
  }
}


function addotamount(){
  if ($('#modalotamount').val() == "") {
    swal({
            title: "Error Message",
            text: "Amount can not be left blank!",
            type: "error"
            });
  }
  else{

   $.ajax({
          url: "{{url('/insert-otamount')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          otamount:$('#modalotamount').val(),
        },
            success:function(resp){             
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Over Time Amount Created Successfully!",
                      type: "success"});

                      $("#otamount-modal").modal("hide");
                      $("#otamount").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#otamount").append("<option value=''>Over Time Amount</option>");
                      $("#otamount").append(
                        "<option value='"+resp[count].otamount_id+"'>"+resp[count].amount+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Over Time Amount Already exsit!",
                            type: "warning"
                       });
                  }
                 
             }

          }); 
  }
}

function addotduration(){
  if ($('#modalotduration').val() == "") {
    swal({
            title: "Error Message",
            text: "OT Duration Can not left blank!",
            type: "error"
            });
  }
  else{

   $.ajax({
            url: "{{url('/insert-otduration')}}",
            type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          duration:$('#modalotduration').val(),
        },
            success:function(resp){             
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "OT Duration Created Successfully!",
                      type: "success"});

                      $("#otduration-modal").modal("hide");
                      $("#otduration").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#otduration").append("<option value=''>Select OT Duration</option>");
                      $("#otduration").append(
                        "<option value='"+resp[count].otduration_id+"'>"+resp[count].duration+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular OT Duration Already exsit!",
                            type: "warning"
                       });
                  }
                 
             }

          }); 
  }
}

function addcat(){
  
  if ($('#modalcategory').val() == "") {
    swal({
            title: "Error Message",
            text: "Category Can not left blank!",
            type: "error"
            });
  }
  else{

   $.ajax({
          url: "{{url('/insert-category')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          cat:$('#modalcategory').val(),
        },
            success:function(resp){             
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Category Created Successfully!",
                      type: "success"});
                      $("#cat-modal").modal("hide");
                      $("#cat").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#cat").append("<option value=''>Select Category</option>");
                      $("#cat").append(
                        "<option value='"+resp[count].id+"'>"+resp[count].category+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Category Already exsit!",
                            type: "warning"
                       });
                  }
             }

          }); 
  }
}
var basicpaypercentage = 53.7;
var pffundpercentage = 8.33;
var allowance = 100 - (basicpaypercentage + pffundpercentage);
console.log(allowance);
$("#grosspay").change(function(){
	let grosspay = $("#grosspay").val();
	let basicpay = grosspay * (basicpaypercentage / 100);
	let pffund = basicpay * (pffundpercentage /100);
	let allowances = grosspay  - (basicpay + pffund);
	$("#basicpay").val( Math.round(basicpay));
	$("#pf_fund").val( Math.round(pffund));
	$("#allowance").val(Math.round(allowances));
})

 </script>

@endsection



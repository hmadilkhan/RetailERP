@extends('layouts.master-layout')

@section('title','HR Permission')

@section('breadcrumtitle','HR Permission')

@section('navmanage','active')

@section('navhrpermission','active')

@section('content')


 <section class="panels-wells">
@if(session()->has('success'))
  <div class="alert alert-success">
    {{ session()->get('success')}}
  </div>
    @endif

               <div class="card">
                  <div class="card-header">
                    <h3 class="card-header-text"> HR Permission </h3>
                    </div>
                  <div class="card-block">
                <form action="{{url('store-hrpermission')}}" method="POST">
                   {{ csrf_field() }}
                  <input type="hidden" name="id" value="{{(count($result) != 0 ? $result[0]->id : 0)}}">
                <div class="row">
                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="allowances" class="custom-control-input" {{(count($result) != 0  ? ($result[0]->allowances == 1 ? 'checked' : '') : '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Allowances</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="increment" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->increment == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Increment</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="promotion" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->promotion == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Promotion</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="bonus" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->bonus == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Bonus</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="advance" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->advance == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Advance</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="loan" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->loan == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Loan</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="leaves" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->leaves == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Leaves</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="qualification" class="custom-control-input f-18" {{(count($result) != 0 ? ($result[0]->qualification == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Qualification</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="switch_transfer" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->switch_transfer == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Switch & Transfer</span>
                                </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="overtime" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->overtime == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Overtime</span>
                                </label>
                        </div>
                     </div>
                  </div>


                  <div class="col-md-4 m-b-5">
                    <div class="form-group row">
                         <div class="col-md-10 has-success">
                              <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="taxes" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->taxes == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Taxes</span>
                                </label>
                        </div>
                     </div>
                  </div>

                 

                  
                  <button class="f-right btn btn-md btn-primary m-10">Submit</button>

                </div> <!-- ROW DIV END !-->
                   
                    
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

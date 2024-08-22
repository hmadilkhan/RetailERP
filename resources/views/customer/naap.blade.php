@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Add Expense')

@section('navcustomer','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Measurement Panel -( {{$customerName}} )</h5>
         <h5 class=""><a href="{{ url('customer') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>

     </div>      
     <div class="card-block">
    <div class="row">
      <div class="col-lg-12">
         <ul class="nav nav-tabs md-tabs " role="tablist">
                                          <li class="nav-item">
                                             <a class="nav-link active" data-toggle="tab" href="#home7" role="tab">Shalwar Qameez</a>
                                             <div class="slide"></div>
                                          </li>
                                          <li class="nav-item">
                                             <a class="nav-link" data-toggle="tab" href="#profile7" role="tab">Pant Shirt</a>
                                             <div class="slide"></div>
                                          </li>
                                        

                                       </ul>
                                       <!-- Tab panes -->
                                       <div class="tab-content">
                                          <div class="tab-pane active" id="home7" role="tabpanel">
                                            <br/>
                                             <p>
                                              <form id="formShalwar" >
            
                                               <div class="row">
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Chest</label>
                                                                <input class="form-control" type="text"
                                                                 name="chest" id="chest" value="{{$shalwar[0]->chest}}" />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Waist</label>
                                                                <input class="form-control" type="text"
                                                                 name="waistShalwar" id="waistShalwar" value="{{$shalwar[0]->waist}}"  />
                                                                
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Abdomen</label>
                                                                <input class="form-control" type="text"
                                                                 name="Abdomen" id="Abdomen"  value="{{ $shalwar[0]->abdomen}}"  />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Hips</label>
                                                                <input class="form-control" type="text"
                                                                 name="hips" id="hips"  value="{{ $shalwar[0]->hips}}" />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Shoulder</label>
                                                                <input class="form-control" type="text"
                                                                 name="shoulderShalwar" id="shoulderShalwar" value="{{ $shalwar[0]->shoulder}}" />
                                                                
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Sleeves</label>
                                                                <input class="form-control" type="text"
                                                                 name="sleeves" id="sleeves"  value="{{ $shalwar[0]->sleeves}}" />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                     <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Neck</label>
                                                                <input class="form-control" type="text"
                                                                 name="neck" id="neck" value="{{ $shalwar[0]->neck}}"    />
                                                            </div>
                                                    </div>
                                                      <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Kurta Length</label>
                                                                <input class="form-control" type="text"
                                                                 name="kurta" id="kurta" value="{{ $shalwar[0]->kurta_length}}"    />
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Shirt Length</label>
                                                                <input class="form-control" type="text"
                                                                 name="shirt" id="shirt"  value="{{ $shalwar[0]->shirt_length}}"   />
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Jacket Length</label>
                                                                <input class="form-control" type="text"
                                                                 name="jacket" id="jacket" value="{{ $shalwar[0]->jacket_length}}"   />
                                                            </div>
                                                    </div>
                                                        
                                                     <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Sherwani</label>
                                                                <input class="form-control" type="text"
                                                                 name="sherwani" id="sherwani" value="{{ $shalwar[0]->sherwani}}"     />
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pent / Shalwar</label>
                                                                <input class="form-control" type="text"
                                                                 name="pentshalwar" id="pentshalwar" value="{{ $shalwar[0]->pentshalwar}}"    />
                                                            </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Arm Hole</label>
                                                                <input class="form-control" type="text"
                                                                 name="arm_hole" id="arm_hole"  value="{{$shalwar[0]->arm_hole}}"  />
                                                            </div>
                                                    </div>
                                                        
                                                     <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Bicep</label>
                                                                <input class="form-control" type="text"
                                                                 name="bicep" id="bicep" value="{{ $shalwar[0]->bicep}}"  />
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">w/c Length</label>
                                                                <input class="form-control" type="text"
                                                                 name="wc_length" id="wc_length" value="{{$shalwar[0]->wc_length}}"  />
                                                            </div>
                                                    </div>
                                               </div>
                                               <div class="row">
                                                 <div class="col-lg-12 col-md-12">
                                                   <button  class="btn btn-md btn-circle btn-primary m-b-10 f-right" type="button" id="btnShalwar" ><i class="icofont icofont-plus"></i>&nbsp;Submit</button>
                                                 </div>
                                               </div>
                                               </form>
                                             </p>
                                          </div>
                                          <div class="tab-pane" id="profile7" role="tabpanel">
                                            <br/>
                                            <form method="POST">
                                               <div class="row">
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Waist</label>
                                                                <input class="form-control" type="text"
                                                                 name="waistPant" id="waistPant" value="{{ $pant[0]->waist}}"    />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Hip</label>
                                                                <input class="form-control" type="text"
                                                                 name="hip" id="hip" value="{{$pant[0]->hip}}" />
                                                                
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Thigh</label>
                                                                <input class="form-control" type="text"
                                                                 name="thy" id="thy" value="{{$pant[0]->thy}}"  />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Knee</label>
                                                                <input class="form-control" type="text"
                                                                 name="knee" id="knee" value="{{ $pant[0]->knee}}"  />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                 
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Caff</label>
                                                                <input class="form-control" type="text"
                                                                 name="caff" id="caff" value="{{ $pant[0]->caff }}" />
                                                               
                                                                
                                                            </div>
                                                    </div>
                                                     <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Fly</label>
                                                                <input class="form-control" type="text"
                                                                 name="fly" id="fly" value="{{ $pant[0]->fly}}" />
                                                            </div>
                                                    </div>
                                                      <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Length</label>
                                                                <input class="form-control" type="text"
                                                                 name="length" id="length" value="{{$pant[0]->length}}"  />
                                                            </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Bottom</label>
                                                                <input class="form-control" type="text"
                                                                 name="bottom" id="bottom"  value="{{$pant[0]->bottom}}" />
                                                            </div>
                                                    </div>
                                                  
                                               </div>
                                               <div class="row">
                                                 <div class="col-lg-12 col-md-12">
                                                   <button class="btn btn-md btn-circle btn-primary m-b-10 f-right" type="button" id="btnPant"><i class="icofont icofont-plus"></i>&nbsp;Submit</button>
                                                 </div>
                                               </div>
                                               </form>
                                          </div>
                                         
                                       </div>
                                     </div>
                                   </div>
     </div>
   </div>
</section>
@endsection


@section('scriptcode_three')

  <script type="text/javascript">



   $('#btnShalwar').click(function(e){
 
      $.ajax({
          url: "{{url('/measurementUpdate')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",chest:$('#chest').val(),waist:$('#waistShalwar').val(),abdomen:$('#Abdomen').val(),hips:$('#hips').val(),shoulder:$('#shoulderShalwar').val(),sleeves:$('#sleeves').val(),neck:$('#neck').val(),kurta:$('#kurta').val(),shirt:$('#shirt').val(),jacket:$('#jacket').val(),sherwani:$('#sherwani').val(),pentshalwar:$('#pentshalwar').val(),customer:'{{$customers}}'},
          success:function(resp){
              if(resp == 1){
                   swal({
                          title: "Success",
                          text: "Record Updated Successfully.",
                          type: "success"
                     },function(isConfirm){
                         if(isConfirm){
                            
                         }
                     });
               }
          },
    error: function (request, status, error) {
        alert(request.responseText);
    }
      });
   });


   $('#btnPant').click(function(e){
 
      $.ajax({
          url: "{{url('/measurementPantUpdate')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",waist:$('#waistPant').val(),hip:$('#hip').val(),thy:$('#thy').val(),knee:$('#knee').val(),caff:$('#caff').val(),fly:$('#fly').val(),length:$('#length').val(),bottom:$('#bottom').val(),arm_hole:$('#arm_hole').val(),bicep:$('#bicep').val(),wc_length:$('#wc_length').val(),customer:'{{$customers}}'},
          success:function(resp){
              if(resp == 1){
                   swal({
                          title: "Success",
                          text: "Record Updated Successfully.",
                          type: "success"
                     });
               }
          },
    error: function (request, status, error) {
        alert(request.responseText);
    }
      });
   });
 
  
  </script>

@endsection
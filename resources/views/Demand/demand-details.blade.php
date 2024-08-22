 @extends('layouts.master-layout')

@section('title','Demand Details')

@section('breadcrumtitle','View Demand')

 @section('navbranchoperation','active')
 @section('navdemand','active')

@section('content')
  <section class="panels-wells">
              <div class="card">
                  <div class="card-header">
     <h5 class="card-header-text">Demand Details</h5><br/>
                     
                                    </a></h5>
                 
    
                     </div>
                  <div class="card-block">

                     <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                           <h6>From :</h6>
                              <h6 >Branch Manager</h6>
                        <p >{{$sender[0]->branch_name}}</p>
                        <p >{{$sender[0]->branch_address}}</p>
                           
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6>TO:</h6>
                            <h6 >Administrator</h6>
                        <p >{{$reciver[0]->branch_name}}</p>
                        <p >{{$reciver[0]->branch_address}}</p>
                 
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Demand Number | <span>{{ $details == 0 ? '' : $details[0]->demand_id}}</span></h6>

                           <h6 class="text-uppercase txt-info">Created on :
                                    <span>{{ $details == 0 ? '' : $details[0]->date }}</span>
                                </h6>
                                 <h6 class="text-uppercase">Status:
                                    <span class="tag tag-default">{{ $details == 0 ? '' : $details[0]->status1 }}</span>
                                </h6>

                               
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table " id="tbl">
                              <thead>
                                 <tr class="thead-default">
                                    <th>Item Code</th>
                                    <th>Product Name</th>
                                    <th>Demanded Qty.</th>
                                     @if(session('roleId') == 2)
                                     
                    @if($details[0]->status1  == "In-Process" || $details[0]->status1  == "Completed")
                                    <th>Transfered Qty.</th>
                                    <th>Purchase Qty.</th>
                                    <th>Balance Qty.</th>
                                    @endif
                                     @endif
                                    <th>Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @if($details) 
                                 @foreach($details as $value)
                 <tr>
                  <td style="display: none;"><label>{{$value->productid}}</label></td>
                  <td ><label>{{$value->item_code}}<label></td>
                   <td ><label>{{$value->product_name}}</label></td>
                   <td ><label>{{$value->qty}}</label></td>

                     @if(session('roleId') == 2)

               @if($details[0]->status1  == "In-Process" || $details[0]->status1  == "Completed")
                   <td ><label>{{$value->transfer_qty}}</label></td>
                   <td ><label>{{$value->purchase_qty}}</label></td>

                   <td ><label>{{$value->qty - $value->transfer_qty - $value->purchase_qty}}</label></td>

                     @endif
                   @endif
                   

                   <td  >
                      @if($value->name == "Draft")
                    <span class="tag tag-default">  {{$value->name }}</span>
                  @elseif($value->name == "Pending")
                    <span class="tag tag-success">  {{$value->name }}</span>
                  @elseif($value->name == "Approved")
                     <span class="tag tag-info">  {{$value->name }}</span>
                  @elseif($value->name == "Cancel")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                       @elseif($value->name == "Delivered")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                    @elseif($value->name == "Completed")
                    <span class="tag tag-success">  {{$value->name }}</span>
                  @endif
                   </td>
                    <td style="display: none;" > <label>{{$value->uom_id}}</label> </td>

                  
                 </tr>
                  @endforeach
                @endif  
                              </tbody>
                           </table>
                        </div>
                     </div>
                      
                      

                  </div>
                  @if(session('roleId') == 2)

                  <div class="row m-t-50">
                       <div class="radial">
                           <button class="icofont icofont-money-bag fa-3x"
                            id="fa-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Purchase" onclick="purchase('{{$details[0]->doid}}')"></button>

                           <button class="icofont icofont-print fa-3x" id="fa-3" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print" onclick="generate_pdf()"></button>
                           <button class="icofont icofont-truck-loaded fa-3x" id="fa-4" data-toggle="tooltip" data-placement="top" title="" data-original-title="Transfer" onclick="transfer('{{$details[0]->doid}}')"></button>
                    @if($details[0]->status1  == "Pending")
                            <button class="icofont icofont-close fa-3x" id="fa-1" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reject Demand" onclick="update_demand_status()"></button>
                            @endif
                           <button class="fab">
                                <i class="icofont icofont-plus fa-3x" id="plus"></i>
                            </button>
                        </div>
                      </div>
                  @else
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <div class="button-group ">
                                      {{--                     <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>--}}
                                      {{--                        Export to Excel Sheet--}}
                                      {{--                     </button>--}}
                                      <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                                          Print Pdf
                                      </button>
                                  </div>
                              </div>
                          </div>
                          {{--                        <div class="col-sm-12">--}}
                          {{--                           <h6>Terms And Condition :</h6>--}}
                          {{--                           <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.--}}
                          {{--                              Duis aute irure dolor </p>--}}
                          {{--                        </div>--}}
                      </div>
                      <br>
                      @endif
               </div>
      </section>
 @endsection  

 @section('scriptcode_three')

  <!-- Fab buttons -->
   <script type="text/javascript" src="{{ asset('public/assets/pages/button-fab.js') }}"></script>


   <script type="text/javascript">
 

   function transfer(id){
    @if($details)
      @if($details[0]->status1 == "In-Process") {
        window.location="{{url('/view-transfer')}}"+"/"+id;
      }
      @else{
       window.location="{{url('/received-demandpanel')}}"+"/"+id; 
      }
      @endif
    @endif
   }

   function purchase(id){
   
    @if (sizeof($purchaseid) > 0){
      window.location="{{url('/view/{{$purchaseid[0]->purchase_id')}}"; 
    }
    @else{

    let poid = '';
    $('#tbl tbody tr').each(function(i){  
        let arr = [];
        $(this).find('label').each(function(j){
          arr.push($(this).text());
    });

        if (arr[7] != 0) {

        $.ajax({
          url:"{{url('/submitpo')}}",
          type:"POST",
          data:{_token:"{{csrf_token()}}",
          poid:poid,
          productid:arr[0],
          balance:arr[7],
          unit:arr[8],
          demandid:id,
          },
         async:false,
             success:function(resp){
              console.log(resp);
              poid = resp;
              window.location= "/erp/edit/"+poid;
              
              }
    });
        // demand ststaus change to complete
        $.ajax({
            url: "{{url('/updatestatusdemand')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            demandid:'{{$details[0]->doid}}',
            statusid:8,
          },
              success:function(id){
              }
                });         
}

  });

    }
    @endif
  
    
    
}

function update_demand_status()
{

           swal({
          title: "Delete",
          text: "Do you want to Reject Demand Order?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "YES",
          cancelButtonText: "NO",
          closeOnConfirm: false,
          closeOnCancel: false
        },function(isConfirm){
    if(isConfirm){
            $.ajax({
            url: "{{url('/updatestatusdemand')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            demandid:'{{$details[0]->doid}}',
            statusid:4,
          },
              success:function(id){
                  if (id == 1) {
                        swal({
                              title: "Success!",
                              text: "Demand Order Rejected Successfully :)",
                              type: "success"
                         },function(isConfirm){
                             if(isConfirm){
                              window.location="{{url('/received-demand')}}";
                             }
                         });

                   }else{
                          swal("Alert!", "Demand Order not Rejected:)", "error");                       
                   }

              }

             });         
                                 
                    }else {
                         swal("Cancel!", "Your Demand Order is safe:)", "error");
                    }
       });

}

   function generate_pdf()
   {
       window.location = "{{url('demandorderReport',$details[0]->doid)}}";

   }


   </script>
 @endsection


    
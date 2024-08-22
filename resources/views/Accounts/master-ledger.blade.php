@extends('layouts.master-layout')

@section('title','Master Ledger')

@section('breadcrumtitle','Master Ledger')

@section('navaccounts','active')
@section('navmasterledger','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Master Ledger Details</h5>
         <h5 id="master_name" class="f-right">Master Name</h5>
         
         </div>      
       <div class="card-block">
        <div class="row col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">Search By Master</label>
                        <select class="form-control select2" data-placeholder="Select Master" id="master" name="master">
                            <option value="">Select Master</option>
                             @if($masters)
                              @foreach($masters as $val)
                                <option value="{{$val->id}}">{{ $val->name }}</option>
                              @endforeach
                             @endif
                        </select>
                        <span class="help-block text-danger" id="vdbox"></span>
                    </div>
        </div>
           <div class="project-table">
                 <table id="item_table" class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
               
               <th>Date</th>  
               <th>Debit</th>
               <th>Credit</th>
              
               
            </tr>
         </thead>
         <tbody>
      
         	
     
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


   $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });
var debit = 0;
var credit = 0;
   $('#master').change(function(){
    debit = 0;
    credit = 0;
    $('#master_name').html($("#master option:selected").html());
      $.ajax({
            url : "{{url('/master-ledger-details')}}",
            type : "POST",
            dataType : 'json',
            data : {_token : "{{csrf_token()}}", id:$('#master').val()},
            success : function(result){
              console.log(result);
               $("#item_table tbody").empty();
               if(result){
                 $.each(result, function( index, value ) {
                  debit = debit  + value.debit;
                  credit = credit + value.credit;
                 $("#item_table tbody").append(
                            "<tr>" +
                              "<td>"+  new Date(value.created_at).getDate() + "-"+ (new Date(value.created_at).getMonth()+1) + "-"+ new Date(value.created_at).getFullYear()  +"</td>" +
                              "<td>"+(value.debit).toLocaleString() +"</td>" +
                              "<td>"+(value.credit).toLocaleString()+"</td>" + 
                            "</tr>"
                           );
                 });

                 $("#item_table tbody").append(
                    "<tr class='f-24'>"+
                              "<td>Total</td>" +
                              "<td>Rs. "+debit.toLocaleString() +"</td>" +
                              "<td>Rs. "+credit.toLocaleString() +"</td>" +
                    "</tr>"

                  );

             }
                
             }
        });
   });
  
  </script>

@endsection
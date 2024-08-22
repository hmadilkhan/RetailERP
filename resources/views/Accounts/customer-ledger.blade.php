@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Customer Ledger')

@section('navaccounts','active')
@section('navcustomerledger','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Customer Ledger Details</h5>
         <h5 id="customer_name" class="f-right">Customer Name</h5>
         
         </div>      
       <div class="card-block">
        <div class="row col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">Search By Customer</label>
                        <select class="form-control select2" data-placeholder="Select Customer" id="customer" name="customer">
                            <option value="">Select Customer</option>
                             @if($customers)
                              @foreach($customers as $val)
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
   $('#customer').change(function(){
    debit =0;
    credit = 0;
    $('#customer_name').html($("#customer option:selected").html());
      $.ajax({
            url : "{{url('/ledger-details')}}",
            type : "POST",
            dataType : 'json',
            data : {_token : "{{csrf_token()}}", id:$('#customer').val()},
            success : function(result){
               $("#item_table tbody").empty();
               if(result != ""){
                 $.each(result, function( index, value ) {
                  debit = debit  + value.debit;
                  credit = credit + value.credit;
                 $("#item_table tbody").append(
                            "<tr>" +
                              "<td>"+  new Date(value.created_at).getDate() + "-"+ (new Date(value.created_at).getMonth()+1) + "-"+ new Date(value.created_at).getFullYear()  +"</td>" +
                              "<td>"+(value.debit).toLocaleString() +"</td>" +
                              "<td>"+(value.credit).toLocaleString()+"</td>" + 
                              // "<td>"+value.credit+"</td>" +
                              // "<td class='action-icon'><i id='btn"+index+"' onclick='updateItem("+value.p_item_details_id+","+value.id+","+value.unit+","+value.quantity+","+value.price+","+value.total_amount+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"' onclick='deleteItem("+value.p_item_details_id+")' class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
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
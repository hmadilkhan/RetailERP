 @extends('layouts.master-layout')

 @section('title', 'Demand-Received Panel')

 @section('breadcrumtitle', 'View Demand')


 @section('navbranchoperation', 'active')
 @section('navrecdemand', 'active')

 @section('content')

     <div class="card">

         <div class="card-header">
             <h3 class="card-header-text">Demand Details
             </h3><br>
             <a href="{{ url('/received-demand') }}" id="btnback" name="btnback"><i
                     class="icofont icofont-arrow-left"></i>Back to List
             </a>
         </div>

         <div class="card-block">
             <div class="row invoive-info">
                 <div class="col-md-4 col-xs-12 invoice-client-info">
                     <h6>From :</h6>
                     <h6>Branch Manager</h6>
                     <p>{{ $sender[0]->branch_name }}</p>
                     <p>{{ $sender[0]->branch_address }}</p>

                 </div>
                 <div class="col-md-4 col-sm-6">
                     <h6>TO:</h6>
                     <h6>Administrator</h6>
                     <p id='branchto'>{{ $reciver[0]->branch_name }}</p>
                     <p>{{ $reciver[0]->branch_address }}</p>

                 </div>
                 <div class="col-md-4 col-sm-6">
                     <h6 class="m-b-20">Demand Number | <span
                             id='demandid'>{{ $details == 0 ? '' : $details[0]->demand_id }}</span></h6>
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
                     <table class="table invoice-detail-table">
                         <thead>
                             <tr class="thead-default">
                                 <th>Item Code</th>
                                 <th>Product Name</th>
                                 <th>Quantity</th>
                                 <th>Action</th>
                             </tr>
                         </thead>
                         <tbody>
                             @if ($details)
                                 @foreach ($details as $value)
                                     <tr>
                                         <td>{{ $value->item_code }}</td>
                                         <td>{{ $value->product_name }}</td>
                                         <td>{{ $value->qty }}</td>
                                         <td>
                                             <select class="select2 form-control" data-placeholder="Select Action"
                                                 id="status{{ $value->id }}" name="status"
                                                 onchange=" changeselection(this.id, '{{ $value->productid }}','{{ $value->id }}','{{ $value->item_code }}')">
                                                 <option value="">Select Status</option>
                                                 @foreach ($status as $value)
                                                     <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                 @endforeach
                                             </select>
                                         </td>
                                     </tr>
                                 @endforeach
                             @endif
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
     <div id='abc'>
     </div>

     <div class="row">
         <div class="col-lg-12 col-sm-12 ">
             <div class="form-group ">
                 <button type="button" id="btnFinalSubmit" class="btn btn-md btn-primary waves-effect waves-light  f-right"
                     onclick="update_status()"> <i class="icofont icofont-plus"> </i>
                     Submit Transfer Orders
                 </button>

             </div>
         </div>
     </div>
 @endsection

 @section('scriptcode_three')
     <script type="text/javascript">
         $(".select2").select2();

         var itcode = 0;

         function changeselection(id, productid, tableid, itemcode) {


             if ($('#' + id).val() == 4) {

                 $('#abc').html('');
                 updatedemand_item(4, tableid);

             } else if ($('#' + id).val() == 3) {

                 stock_details(itemcode, productid);

                 updatedemand_item(3, tableid);


             } else {
                 alert($('#' + id).val() + " " + productid);

             }


         }

         function updatedemand_item(statusid, tableid) {
             $.ajax({
                 url: "{{ url('/updateitemstatus') }}",
                 type: "PUT",
                 data: {
                     _token: "{{ csrf_token() }}",
                     id: tableid,
                     statusid: statusid
                 },
                 success: function(resp) {}
             });
         }

         function update_status() {
             $.ajax({
                 url: "{{ url('/updatestatus') }}",
                 type: "PUT",
                 data: {
                     _token: "{{ csrf_token() }}",
                     demandid: '{{ $details[0]->doid }}',
                     statusid: 7,
                 },
                 success: function(resp) {

                     swal({
                         title: "Transfer Created",
                         text: "Operation Completed Successfully!",
                         type: "success"
                     }, function(isConfirm) {
                         if (isConfirm) {
                             window.location = "{{ url('/gettransferorders') }}";
                         }
                     });

                 }
             });

         }



         function stock_details(itemcode, productid) {

             $('#abc').html('');
             itcode = itemcode;
             $.ajax({
                 url: "{{ url('/stock') }}",
                 type: "POST",
                 data: {
                     _token: "{{ csrf_token() }}",
                     itemcode: itemcode
                 },
                 dataType: 'json',
                 success: function(resp) {

                     var count = 0;
                     var filter = '{{ $sender[0]->branch_id }}';
                     $.each(resp, function(index, value) {
                         if (value.branch_id != filter) {
                             $('#abc').append(
                                 '<div class="col-xl-4 col-lg-4 grid-item"><div class="card"><div class="card-block txt-white bg-spark-chart"><div> <h5 class="d-inline-block" id="brnch' +
                                 count + '" data-id="' + value.branch_id + '" data-value="' + value
                                 .branch_name + '">Branch, ' + value.branch_name +
                                 '</h5></div><div class="text-center"><h1 class="f-w-100 f-90" id="stockvalue' +
                                 count + '" >' + value.stock +
                                 '</h1><h5 class="txt-white bg-spark-chart" >Stock in Hand</h5></div></div><div class="card-block"><label>Enter Transfer Quantity:</label> <input type="text" placeholder="0" name="transferqty' +
                                 count + '" id="transferqty' + count +
                                 '" class="form-control" /><br><a id="btntransfer' + count +
                                 '"class="btn btn-md btn-dark waves-effect waves-light f-right m-r-35" onclick="insert_tranfer(' +
                                 count + ',' + value.product_id + ',' + value.branch_id +
                                 ')" >Make Transfer Order </a></div></div></div>')
                             count++;
                         }
                     });
                 }
             });
         }

         function insert_tranfer(count, pid, branchid) {




             $.ajax({
                 url: "{{ url('/chk') }}",
                 type: "POST",
                 data: {
                     _token: "{{ csrf_token() }}",
                     branchfrom: $("#brnch" + count).data("id"),
                     qty: $("#transferqty" + count).val(),
                     itemcode: itcode,
                     id: count,
                 },
                 success: function(resp) {
                     if (resp == 1) {
                         swal({
                             title: "Error Message",
                             text: "Enter Quantity is not correct!",
                             type: "warning"
                         });
                         $("#transferqty" + count).focus();
                     } else {

                         $.ajax({
                             url: "{{ url('/transfer') }}",
                             type: "POST",
                             data: {
                                 _token: "{{ csrf_token() }}",
                                 demandid: '{{ $details[0]->doid }}',
                                 branchfrom: branchid,
                                 branchto: '{{ $sender[0]->branch_id }}',
                                 productid: pid,
                                 qty: $("#transferqty" + count).val(),
                             },
                             success: function(resp) {
                                 $('#transferqty' + count).attr("disabled", "disabled");
                                 $('#btntransfer' + count).addClass("disabled");
                                 let result = $("#stockvalue" + count).html() - $("#transferqty" +
                                     count).val();
                                 $("#stockvalue" + count).html(result);
                                 swal({
                                     title: "Transfer Created",
                                     text: "Transfer Order Created Successfully!",
                                     type: "success"
                                 });


                             }
                         });
                     }

                 }

             });

         }
     </script>
 @endsection

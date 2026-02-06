@extends('layouts.master-layout')

@section('title', 'Create GRN')

@section('breadcrumtitle', 'Create GRN')

@section('navtransfer', 'active')

@section('navchallanview', 'active')

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-header-text">Good Receiving Note
            </h3><br>
            <a href="{{ url('/challanlist') }}" id="btnback" name="btnback"><i class="icofont icofont-arrow-left"></i>Back to
                List
            </a>

        </div>


        <div class="card-block">
            <div class="row invoive-info">
                <div class="col-md-4 col-xs-12 invoice-client-info">
                    <h6>From :</h6>
                    <h6>Branch Manager</h6>
                    <p>{{ $details[0]->deliverd_by }}</p>
                    <p>{{ $details[0]->del_add }}</p>

                </div>
                <div class="col-md-4 col-sm-6">
                    <h6>TO:</h6>
                    <h6>Branch Manager</h6>
                    <p>{{ $details[0]->destination }}</p>
                    <p>{{ $details[0]->des_add }}</p>

                </div>
                <div class="col-md-4 col-sm-6">
                    <h6 class="m-b-20">GRN Number | <span>{{ $details == 0 ? '' : $details[0]->DC_No }}</span></h6>

                    <h6 class="text-uppercase txt-info">Created on :
                        <span>{{ $details == 0 ? '' : $details[0]->date }}</span>
                    </h6>



                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table" id="tbl">
                        <thead>
                            <tr class="thead-default">

                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>CP</th>
                                <th>Retail Price</th>
                                <th>Wholesale Price</th>
                                <th>Discount Price</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($details)
                                @foreach ($details as $value)
                                    <tr>
                                        <td style="display: none;"> <label>{{ $value->dc_item_id }}</label> </td>
                                        <td style="display: none;"> <label>{{ $value->product_id }}</label> </td>
                                        <td><label>{{ $value->product_name }}</label></td>

                                        <td><label>{{ $value->deliverd_qty }}</label></td>
                                        <td><label>{{ number_format(($value->shipment_charges == '' ? 0 : $value->shipment_charges) + $value->cost_price, 2) }}</label>
                                        </td>
                                        <td><input type="text" value="0" class="form-control" id="retailprice"
                                                name="retailprice"></td>
                                        <td><input type="text" value="0" class="form-control" id="Wholesale"
                                                name="Wholesale"></td>
                                        <td><input type="text" value="0" class="form-control" id="Discount"
                                                name="Discount"></td>

                                        <td style="display: none;"> <label>{{ $value->uom_id }}</label> </td>


                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-sm-12 ">
                    <div class="form-group ">
                        <button type="button" id="btnFinalSubmit"
                            class="btn btn-md btn-primary waves-effect waves-light  f-right" onclick="submit()"><i
                                class="icofont icofont-plus"></i>
                            Submit
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection
@section('scriptcode_three')
    <script type="text/javascript">
        let grn = '';

        function submit() {

            $('#tbl tbody tr').each(function(i) {
                let arr = [];
                $(this).find('input,label').each(function(j) {
                    if ($(this).text() != "") {
                        arr.push($(this).text());
                    } else {
                        arr.push($(this).val());
                    }
                });
                if (arr[5] == 0) {
                    swal({
                        title: "Error Message!",
                        text: "Retail Price Can not be 0!",
                        type: "error"
                    });
                } else {
                    $.ajax({
                        url: "{{ url('/submitgrn') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            dcitem_id: arr[0],
                            item_id: arr[1],
                            qty_rec: arr[3],
                            dc_id: '{{ $details[0]->DC_id }}',
                            grn: grn,
                            uom: arr[8],
                            cp: arr[4],
                            rp: arr[5],
                            wp: arr[6],
                            dp: arr[7],

                        },
                        async: false,
                        success: function(resp) {
                            grn = resp;
                            swal({
                                title: "Items Recived",
                                text: "Items Recived Successfully!",
                                type: "success"
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    window.location = "{{ url('/stock-list') }}";
                                }
                            });
                        }
                    });
                }

            });

        }
    </script>

@endsection

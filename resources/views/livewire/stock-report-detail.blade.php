<div>
    <style>
        @media (min-width: 992px) {
            .modal-xlg {
                max-width: 900px;
            }
        }
    </style>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade in " id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-xlg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ $name }}</h5>
                </div>
                <div class="modal-body">
                    <div class="">
                        <h5>Stock Details : </h5>
                        <table class="table table-bordered mt-3">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Stock Id</th>
                                    <th>GRN #</th>
                                    <th>Qty</th>
                                    <th colspan="2">Balance</th>
                                    <th colspan="2">Narration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($details))
                                    @foreach ($details as $stockDetail)
                                        <tr>
                                            <td>{{ date('d M y', strtotime($stockDetail->date)) }}</td>
                                            <td>{{ $stockDetail->stock_id }}</td>
                                            <td>{{ $stockDetail->grn_id }}</td>
                                            <td>{{ $stockDetail->qty }}</td>
                                            <td colspan="2">{{ $stockDetail->balance }}</td>
                                            <td colspan="2">{{ $stockDetail->narration }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="8">No Record Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="">
                        <h5>Sales Details : </h5>
                        <table class="table table-bordered mt-3">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Order#</th>
                                    <th>Receipt#</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($sales))
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ date('d M y', strtotime($sale->date)) }}</td>
                                            <td>{{ $sale->receipt_id }}</td>
                                            <td>{{ $sale->receipt_no }}</td>
                                            <td>{{ $sale->total_amount }}</td>
                                            <td>{{ $sale->order_status_name }}</td>
                                            <td class="text-center"><a href="{{url('order-detail',$sale->receipt_id)}}"><i class="icofont icofont-eye-alt icofont-1x text-info mx-2" ></i></a></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="8">No Record Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"
                        wire:click="$dispatch('hide-delete-modal')">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @script
        <script>
            window.addEventListener('show-delete-modal', event => {
                $('#deleteModal').modal('show');
            });

            window.addEventListener('hide-delete-modal', event => {
                $('#deleteModal').modal('hide');
            });
        </script>
    @endscript
</div>

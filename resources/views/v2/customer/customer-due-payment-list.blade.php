@extends('layouts.master-tailwind')

@section('title', 'Customer Receivables')
@section('page_title', 'Customer Receivables')
@section('page_subtitle', 'Track due receipts, filter by customer or payment type, edit due dates, and review payment history.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <input type="hidden" name="type" id="type" value="all">
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="draft" onclick="changeTab(this,'all')" class="receipt-tab rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white">All Receipts</button>
                    <button type="button" id="placed" onclick="changeTab(this,'today')" class="receipt-tab rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text">Today Receipts</button>
                    <button type="button" id="received" onclick="changeTab(this,'clear')" class="receipt-tab rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text">Clear Receipts</button>
                </div>
            </div>

            <div class="grid gap-4 px-5 py-5 lg:grid-cols-12 lg:items-end">
                <label class="block lg:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Customer</span>
                    <select id="customer_name" class="js-data-example-ajax v2-select2 mt-2 h-11 w-full rounded-lg border-erp-line text-sm" data-placeholder="Search for a Customer"></select>
                </label>

                <label class="block lg:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Payment Type</span>
                    <select id="payment_type" class="v2-select2 mt-2 h-11 w-full rounded-lg border-erp-line text-sm">
                        <option value="">Select Payment Type</option>
                        <option value="1">Cash</option>
                        <option value="2">Credit</option>
                    </select>
                </label>

                <label class="fromDate block lg:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">From Date</span>
                    <input type="date" id="from_date" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>

                <label class="fromDate block lg:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">To Date</span>
                    <input type="date" id="to_date" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>

                <div class="flex gap-3 lg:col-span-3">
                    <button type="button" class="btnSubmit flex-1 rounded-lg border border-erp bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
                    <button type="button" class="resetBtn rounded-lg border border-erp-line bg-white px-5 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</button>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="overflow-x-auto px-5 py-5">
                <table id="empTable" class="min-w-full table-auto text-sm" width="100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Terminal</th>
                            <th>Receipt No</th>
                            <th>Customer</th>
                            <th>Address</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Payment Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </section>
    </div>

    <div id="details-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Edit Due Date</h3>
                <button type="button" onclick="closeV2Modal('details-modal')" class="text-sm font-bold text-erp-mute">Close</button>
            </div>
            <form method="POST" id="editDueDate" class="space-y-4 px-5 py-5">
                @csrf
                <input type="hidden" name="cust_receipt_id" id="model-receipt_id">
                <div class="messages"></div>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Due Date</span>
                    <input class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="date" name="due_date" id="model_due_date" required>
                </label>
                <button type="submit" id="btnSave" class="w-full rounded-lg border border-erp bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
            </form>
        </div>
    </div>

    <div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Payment History</h3>
                <button type="button" onclick="closeV2Modal('payment-modal')" class="text-sm font-bold text-erp-mute">Close</button>
            </div>
            <div class="showData px-5 py-5 text-sm"></div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials._datatable')
    <script>
        let oTable;

        function openV2Modal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeV2Modal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        jQuery(function ($) {
            $('#payment_type').select2({ width: '100%' });
            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{ route("search-customer-by-names") }}',
                    dataType: 'json',
                    processResults: function (data) {
                        return {
                            results: $.map(data.items, function (item) {
                                return { text: item.name, id: item.name };
                            })
                        };
                    }
                },
                placeholder: 'Search for a Customer',
                minimumInputLength: 1,
                width: '100%'
            });

            oTable = $('#empTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                responsive: true,
                order: [0, 'desc'],
                ajax: {
                    url: "{{ route('get-customer-due-payment') }}",
                    data: function (data) {
                        data.type = $('#type').val();
                        data.customer_name = $('#customer_name').val();
                        data.from_date = $('#from_date').val();
                        data.to_date = $('#to_date').val();
                        data.payment_type = $('#payment_type').val();
                    }
                },
                columns: [
                    { data: 'Date' },
                    { data: 'Time' },
                    { data: 'Terminal' },
                    { data: 'Receipt No' },
                    { data: 'Customer Name' },
                    { data: 'address' },
                    {
                        render: function (data, type, full) {
                            return '<button type="button" onclick="showDueDateModel(\'' + full.due_date + '\',' + full.Order + ')" class="font-bold text-erp-dark">' + full.due_date + '</button>';
                        }
                    },
                    { data: 'Total Amount' },
                    {
                        render: function (data, type, full) {
                            return '<span class="font-bold text-rose-700">' + full.balance + '</span>';
                        }
                    },
                    {
                        render: function (data, type, full) {
                            return full.Payment == 1 ? 'Cash' : (full.Payment == 2 ? 'Credit' : 'WalkIn');
                        }
                    },
                    {
                        render: function (data, type, full) {
                            return '<div class="flex justify-center gap-2"><a href="{{ url('print') }}/' + full.receipt_no + '" class="font-bold text-erp-dark">Print</a><button type="button" onclick="showPaymentHistory(' + full.Order + ')" class="font-bold text-sky-700">History</button></div>';
                        }
                    }
                ]
            });

            $('.btnSubmit').on('click', function () {
                oTable.draw();
            });

            $('.resetBtn').on('click', function () {
                $('.js-data-example-ajax').val('').trigger('change');
                $('#payment_type').val('').trigger('change');
                $('#from_date, #to_date').val('');
                oTable.draw();
            });

            $('#editDueDate').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{ URL::to('customer-due-date') }}",
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.status == 'true') {
                            $('.messages').html('<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">' + response.message + '</div>');
                            setTimeout(function () { location.reload(); }, 500);
                        } else {
                            let message = '';
                            $.each(response.message, function (key, value) { message += value + '<br>'; });
                            $('.messages').html('<div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">' + message + '</div>');
                        }
                    }
                });
            });
        });

        function changeTab(button, type) {
            document.getElementById('type').value = type;
            document.querySelectorAll('.receipt-tab').forEach(tab => {
                tab.className = 'receipt-tab rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text';
            });
            button.className = 'receipt-tab rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white';

            jQuery('.js-data-example-ajax').val('').trigger('change');
            jQuery('#payment_type').val('').trigger('change');
            jQuery('#from_date, #to_date').val('');
            document.querySelectorAll('.fromDate').forEach(el => el.classList.toggle('hidden', type === 'today'));
            oTable.search('').draw();
        }

        function showPaymentHistory(order) {
            openV2Modal('payment-modal');
            jQuery.ajax({
                type: 'POST',
                url: "{{ URL::to('customer-payment-log') }}",
                data: { receipt_no: order, _token: "{{ csrf_token() }}" },
                success: function (response) {
                    jQuery('.showData').html(response);
                }
            });
        }

        function showDueDateModel(dueDate, order) {
            document.getElementById('model_due_date').value = dueDate;
            document.getElementById('model-receipt_id').value = order;
            openV2Modal('details-modal');
        }
    </script>
@endpush

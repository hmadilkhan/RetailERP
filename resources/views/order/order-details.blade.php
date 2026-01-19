@extends('layouts.master-layout')

@section('title', 'Orders')

@section('breadcrumtitle', 'Orders Panel')

@section('navbranchoperation', 'active')
@section('navorder', 'active')

@section('content')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            margin-top: -3.5rem;
            background-color: #f1f3f7;
        }

        .bg-success {
            --bs-bg-opacity: 1;
            background-color: rgb(76 175 80) !important;
        }

        .avatar-lg {
            height: 5rem;
            width: 5rem;
        }

        .font-size-18 {
            font-size: 18px !important;
        }

        .font-size-24 {
            font-size: 24px !important;
        }

        .font-size-28 {
            font-size: 28px !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        a {
            text-decoration: none !important;
        }

        .w-xl {
            min-width: 160px;
        }

        .card {
            margin-bottom: 24px;
            -webkit-box-shadow: 0 2px 3px #e4e8f0;
            box-shadow: 0 2px 3px #e4e8f0;
        }

        .card {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #eff0f2;
            border-radius: 1rem;
        }

        #myImg:hover {
            opacity: 0.7;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.9);
            /* Black w/ opacity */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* Caption of Modal Image */
        #caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
        }

        /* Add Animation */
        .modal-content,
        #caption {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(0)
            }

            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0)
            }

            to {
                transform: scale(1)
            }
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 45px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
        }
    </style>
    <div class="mt-5">
        <div class="row bg-transparent m-2 p-2">
            <div class="col-xl-12">
                <div class="card border shadow-none">
                    <div class="card-body">
                        <div class="col-xl-4 ">
                            <div class="  d-flex align-items-start  pb-2">
                                <div class="  bg-transparent py-2 px-4">
                                    <label class="">
                                        <h5 class="font-size-24 text-sm-end text-muted mb-5">Booking Date :
                                            {{ date('d M Y', strtotime($order->date)) . ' ' . date('H:i a', strtotime($order->time)) }}
                                        </h5>
                                    </label>

                                    <label
                                        class="label {{ Custom_Helper::getColorName($order->orderStatus->order_status_name) }} font-size-28 border border-white rounded my-5 mt-3">
                                        @if ($order->orderStatus->order_status_name == 'Ready for Delivery')
                                            Ready
                                        @elseif($order->orderStatus->order_status_name == 'Dispatch')
                                            Dispatch from workshop
                                        @elseif($order->orderStatus->order_status_name == 'Order Picked Up')
                                            Picked Up By Branch
                                        @else
                                            {{ $order->orderStatus->order_status_name }}
                                        @endif
                                    </label>
                                    @if ($order->delivery_date != '')
                                        <label class="mt-5">
                                            <h5 class="font-size-24 text-sm-end text-muted float-left ">Delivery Date :
                                                {{ date('d M Y', strtotime($order->delivery_date)) }}</h5>
                                        </label>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-4 ">
                            <h3 class="font-size-24 text-center mb-4">Branch #</h3>
                            <h1 class="fs-1 text-center mb-4">{{ $order->branchrelation->branch_name }}</h1>
                            <h2 class="font-size-28 text-center mb-2">Customer : {{ ucwords($order->customer->name) }}</h2>
                            @if ($order->web == 1)
                                <h3 class="font-size-24 text-center mb-2">Order ID : {{ $order->url_orderid }}</h3>
                            @else
                                <h3 class="font-size-24 text-center mb-2">Receipt No : {{ $order->receipt_no }}</h3>
                            @endif

                        </div>
                        <div class="col-xl-4">
                            <div class="flex-shrink-0 items-center ms-2 py-2 ">
                                <div class="text-sm-end mt-2 mt-sm-0 float-end">
                                    @if (auth()->user()->id == 929 && $order->orderStatus->order_status_name == 'Delivered')
                                        <a href="{{ url('change-order-status') }}/{{ $order->id }}/1"
                                            class="btn btn-danger text-white  ml-2">
                                            <i class="mdi mdi-cart-outline me-1"></i> Pending </a>
                                    @endif
                                    @if (session('roleId') == 19 or session('roleId') == 2)
                                        @if ($order->orderStatus->order_status_name == 'Pending')
                                            <a href="{{ url('change-order-status') }}/{{ $order->id }}/2"
                                                class="btn btn-warning text-white  ml-2">
                                                <i class="mdi mdi-cart-outline me-1"></i> Processing </a>
                                        @elseif($order->orderStatus->order_status_name == 'Processing')
                                            <a href="{{ url('change-order-status') }}/{{ $order->id }}/3"
                                                class="btn btn-primary  ml-2">
                                                <i class="mdi mdi-cart-outline me-1"></i> Ready </a>
                                        @elseif($order->orderStatus->order_status_name == 'Ready for Delivery')
                                            <a href="{{ url('change-order-status') }}/{{ $order->id }}/6"
                                                class="btn btn-success  ml-2">
                                                <i class="mdi mdi-cart-outline me-1"></i> Dispatch </a>
                                        @endif
                                    @endif
                                    @if (
                                        $order->orderStatus->order_status_id >= 6 &&
                                            (session('roleId') == 19 or session('roleId') == 20 or session('roleId') == 2))
                                        <a href="{{ url('voucher') }}/{{ $order->receipt_no }}"
                                            class="btn btn-danger  ml-2" target="_blank">
                                            <i class="mdi mdi-cloud-print me-1"></i> Print </a>
                                    @endif
                                    @if ($order->orderStatus->order_status_id >= 2 && session('company_id') == 102)
                                        <a href="{{ url('factory-operation-report') }}?order={{ $order->id }}"
                                            class="btn btn-danger  ml-2" target="_blank">
                                            <i class="mdi mdi-cloud-print me-1"></i> Operation Report </a>
                                    @endif
                                    <a href="{{ url('orders-view') }}" class="btn btn-default text-muted">
                                        <i class="mdi mdi-arrow-left me-1"></i> Back to Orders </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                @foreach ($order->orderdetails as $key => $item)
                    @if (session('roleId') == 19 && $item->mode == 'worker')
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <div class="d-flex align-items-start border-bottom pb-3">
                                    <div class="me-4">
                                        <img id="myImg{{ $key }}"
                                            src="{{ Custom_Helper::getProductImage(optional($item->inventory)->url, optional($item->inventory)->image) }}"
                                            alt="{{ $item->note }}"
                                            class="avatar-lg rounded productImage{{ $key }} "
                                            style="cursor:pointer;" onclick="showImage('{{ $key }}')">

                                    </div>
                                    <div class="flex-grow-1 align-self-center overflow-hidden">
                                        <div>
                                            <h5 class="text-truncate font-size-18"><a href="#"
                                                    class="code{{ $key }} text-dark fw-bold">({{ $item->inventory->item_code }})
                                                </a> <a href="#"
                                                    class="name{{ $key }} text-dark">{{ $item->item_name }} </a>
                                            </h5>
                                            <p class="mb-0 mt-1  fs-5"><span class="fw-bold">Comments : </span><span
                                                    class="comments{{ $key }} fs-5">{{ $item->note }}</span>
                                            </p>
                                        </div>

                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <div>
                                            @if ($order->orderStatus->order_status_id == 1 && $item->mode == '' && session('company_id') == 104)
                                                <label
                                                    class="label {{ Custom_Helper::getColorName($item->itemstatus->order_status_name) }} font-size-18 border border-white rounded my-5 mt-3">{{ $item->itemstatus->order_status_name }}</label>
                                                <a href="{{ url('sent-to-workshop') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}"
                                                    class="btn btn-warning text-white  ml-2">
                                                    <i class="mdi mdi-cart-outline me-1"></i> Sent to Workshop </a>
                                            @endif
                                            @if (session('roleId') == 19)
                                                @if ($item->status == 1)
                                                    <a href="{{ url('change-item-status') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}/2"
                                                        class="btn btn-warning text-white  ml-2">
                                                        <i class="mdi mdi-cart-outline me-1"></i> Processing </a>
                                                @elseif($item->status == 2)
                                                    <a href="{{ url('change-item-status') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}/3"
                                                        class="btn btn-primary  ml-2">
                                                        <i class="mdi mdi-cart-outline me-1"></i> Ready </a>
                                                @elseif($item->status == 3)
                                                    <a href="{{ url('change-item-status') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}/6"
                                                        class="btn btn-success  ml-2">
                                                        <i class="mdi mdi-cart-outline me-1"></i> Dispatch </a>
                                                @endif
                                            @endif


                                        </div>

                                    </div>

                                </div>

                                <div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Price</p>
                                                <h5 class="mb-0 mt-2">
                                                    {{ session('currency') . ' ' . number_format($item->item_price, 0) }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Quantity</p>
                                                <div class="d-inline-flex">
                                                    <select class="form-select form-select-sm w-xl" disabled>
                                                        <option value="1" selected>
                                                            {{ number_format($item->total_qty, 0) }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Total</p>
                                                <h5>{{ session('currency') . ' ' . number_format(($item->item_price != '' ? $item->item_price : 1) * $item->total_qty, 0) }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    @else
                        @if (!empty($item->inventory))
                            <div class="card border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-start border-bottom pb-3">
                                        <div class="me-4">
                                            <img id="myImg{{ $key }}"
                                                src="{{ Custom_Helper::getProductImage(optional($item->inventory)->url, optional($item->inventory)->image) }}"
                                                alt="{{ $item->note }}"
                                                class="avatar-lg rounded productImage{{ $key }} "
                                                style="cursor:pointer;" onclick="showImage('{{ $key }}')">

                                        </div>
                                        <div class="flex-grow-1 align-self-center overflow-hidden">
                                            <div>
                                                <h5 class="text-truncate font-size-18"><a href="#"
                                                        class="code{{ $key }} text-dark fw-bold">({{ $item->inventory->item_code }} )
                                                    </a> <a href="#"
                                                        class="name{{ $key }} text-dark">{{ $item->item_name }}
                                                        @if($item->inventory->department)
                                                            <span class="badge bg-primary ms-2">{{ $item->inventory->department->department_name }}</span>
                                                        @endif
                                                    </a>
                                                </h5>
                                                <p class="mb-0 mt-1  fs-5"><span class="fw-bold">Comments : </span><span
                                                        class="comments{{ $key }} fs-5">{{ $item->note }}</span>
                                                </p>
                                            </div>

                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <div>
                                                @if ($item->is_sale_return == 1)
                                                    <button
                                                        class="btn btn-sm btn-danger text-white  ml-2">
                                                        <i class="mdi mdi-cart-outline me-1"></i> Sales Return </button>
                                                @endif
                                                @if (
                                                    $order->orderStatus->order_status_id == 1 &&
                                                        $item->mode == '' &&
                                                        (session('company_id') == 104 or session('company_id') == 7))
                                                    <label
                                                        class="label {{ Custom_Helper::getColorName($item->itemstatus->order_status_name) }} font-size-18 border border-white rounded my-5 mt-3">{{ $item->itemstatus->order_status_name }}</label>
                                                    <a href="{{ url('sent-to-workshop') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}"
                                                        class="btn btn-warning text-white  ml-2">
                                                        <i class="mdi mdi-cart-outline me-1"></i> Sent to Workshop </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="row border-bottom pb-3">
                                            <div class="col-md-4">
                                                <div class="mt-3">
                                                    <p class="text-muted mb-2">Price</p>
                                                    <h5 class="mb-0 mt-2">
                                                        {{ session('currency') . ' ' . ($item->item_price != '' ? number_format($item->item_price, 0) : 0) }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mt-3">
                                                    <p class="text-muted mb-2">Quantity</p>
                                                    <div class="d-inline-flex">
                                                        <select class="form-select form-select-sm w-xl" disabled>
                                                            <option value="1" selected>
                                                                {{ number_format($item->total_qty, 0) }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mt-3">
                                                    <p class="text-muted mb-2">Total</p>
                                                    <h5>{{ session('currency') . ' ' . number_format(($item->item_price != '' ? $item->item_price : 1) * $item->total_qty, 0) }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h3 class="mb-0 mt-2 text-dark fw-bold">Item Logs</h3>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mt-3">
                                                    @if (!empty($item->statusLogs))
                                                        <ol class="list-group list-group-numbered">
                                                            @foreach ($item->statusLogs as $log)
                                                                <li class="list-group-item">Order
                                                                    <strong>{{ $log->status->order_status_name }}</strong>
                                                                    at
                                                                    {{ date('d M Y', strtotime($log->date)) . ' ' . date('H:i a', strtotime($log->time)) }}
                                                                </li>
                                                            @endforeach
                                                        </ol>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endif
                    <!-- end card -->
                @endforeach

            </div>



            <div class="col-xl-4">
                <div class="mt-5 mt-lg-0">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h5 class="font-size-16 mb-0">Order Details <span
                                    class="float-end">#{{ $order->id }}</span></h5>
                        </div>
                        <div class="card-body p-4 pt-2">

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr class="bg-light">
                                            <th>Branch :</th>
                                            <td class="text-end">
                                                <span class="fw-bold">
                                                    {{ $order->branchrelation->branch_name }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Receipt Number :</td>
                                            <td class="text-end">{{ $order->receipt_no }}</td>
                                        </tr>
                                        <tr>
                                            <td>Service Type :</td>
                                            <td class="text-end fw-bold">
                                                {{ $order->service?->serviceType?->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Customer : </td>
                                            <td class="text-end">{{ $order->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Contact Number :</td>
                                            <td class="text-end">{{ $order->customer->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <td>Membership Number :</td>
                                            <td class="text-end">{{ $order->customer->membership_card_no }}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Method :</td>
                                            <td class="text-end">{{ $order->payment->payment_mode }}</td>
                                        </tr>
                                        <tr>
                                            <td>Sales Person :</td>
                                            <td class="text-end">
                                                {{ !empty($provider) ? $provider->serviceprovider->provider_name : '-' }}
                                            </td>
                                        </tr>
                                        @if (!empty($wallet))
                                            <tr>
                                                <td>Wallet :</td>
                                                <td class="text-end">
                                                    {{ !empty($wallet) ? $wallet->serviceprovider->provider_name : '-' }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>Land Mark :</td>
                                            <td class="text-end">
                                                {{ !empty($order->address) ? $order->address->landmark : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Address :</td>
                                            <td class="text-end">
                                                {{ !empty($order->address) ? $order->address->address : '-' }}</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->
                        </div>
                    </div>
                </div>
                <div class="mt-5 mt-lg-0">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h5 class="font-size-16 mb-0">Delivery Detail</h5>
                        </div>
                        <div class="card-body p-4 pt-2">

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>

                                        <tr>
                                            <td>Area Name :</td>
                                            <td class="text-end">
                                                {{ $order->delivery_area_name != '' ? $order->delivery_area_name : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Instructions :</td>
                                            <td class="text-end">
                                                {{ $order->delivery_instructions != '' ? $order->delivery_instructions : '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->
                        </div>
                    </div>
                </div>
                @php
                    $taxName = '';
                    $taxAmount = 0;
                    if (!empty($order->orderAccountSub)) {
                        if ($order->orderAccountSub->sales_tax_amount > 0) {
                            $taxName = 'FBR';
                            $taxAmount = $order->orderAccountSub->sales_tax_amount;
                        } elseif ($order->orderAccountSub->srb > 0) {
                            $taxName = 'SRB';
                            $taxAmount = $order->orderAccountSub->srb;
                        }
                    }
                @endphp
                <div class="mt-5 mt-lg-0">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h5 class="font-size-16 mb-0">Order Summary </h5>
                        </div>
                        <div class="card-body p-4 pt-2">

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Sub Total :</td>
                                            <td class="text-end">{{ session('currency') . ' ' . $order->actual_amount }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Discount : </td>
                                            <td class="text-end">-
                                                {{ session('currency') . ' ' . (!empty($order->orderAccountSub) ? $order->orderAccountSub->discount_amount : '') }}
                                            </td>
                                        </tr>
                                        @if ($order->payment_id == 3)
                                            <tr>
                                                <td>Amount Received : </td>
                                                <td class="text-end">-
                                                    {{ session('currency') . ' ' . (!empty($order->orderAccountSub) ? $order->orderAccount->receive_amount + $received : '') }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>Shipping Charge :</td>
                                            <td class="text-end">
                                                {{ session('currency') . ' ' . (!empty($order->orderAccountSub) ? $order->orderAccountSub->delivery_charges_amount : '') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tax Charge: {{ '(' . $taxName . ')' }}</td>
                                            <td class="text-end">
                                                {{-- (!empty($order->orderAccountSub) ? $order->orderAccountSub->sales_tax_amount : '') --}}
                                                {{ session('currency') . ' ' . $taxAmount }}
                                            </td>
                                        </tr>
                                        <tr class="bg-light">
                                            <th>{{ $order->payment_id == 3 ? 'Balance :' : 'Total :' }}</th>
                                            <td class="text-end">
                                                <span class="fw-bold">
                                                    {{ session('currency') . ' ' . number_format($order->payment_id == 3 ? $order->total_amount - $order->orderAccount->receive_amount - $received : $order->total_amount, 0) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->
                        </div>
                    </div>
                </div>
                <div class="mt-5 mt-lg-0">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h5 class="font-size-16 mb-0">Order Status Logs </h5>
                        </div>
                        <div class="card-body p-4 pt-2">
                            @if($order->statusLogs->isEmpty())
                                <p class="text-muted text-center">No logs</p>
                            @else
                                @foreach ($order->statusLogs as $log)
                                    <div class="row">
                                        <!-- timeline item 1 left dot -->
                                        <div class="col-auto text-center flex-column d-none d-sm-flex">
                                            <div class="row h-50">
                                                <div class="col">&nbsp;</div>
                                                <div class="col">&nbsp;</div>
                                            </div>
                                            <h5 class="m-2">
                                                <span
                                                    class="badge rounded-pill {{ $log->status->order_status_name == 'Pending' ? 'bg-danger' : ($log->status->order_status_name == 'Processing' ? 'bg-warning' : ($log->status->order_status_name == 'Ready for Delivery' ? 'bg-primary' : ($log->status->order_status_name == 'Order Picked Up' ? 'bg-info' : 'bg-success'))) }} border">&nbsp;</span>
                                            </h5>
                                            <div class="row h-50">
                                                <div class="col border-end order">&nbsp;</div>
                                                <div class="col">&nbsp;</div>
                                            </div>
                                        </div>
                                        <!-- timeline item 1 event content -->
                                        <div class="col py-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="float-end text-muted">
                                                        {{ date('d M Y', strtotime($log->date)) . ' ' . date('H:i a', strtotime($log->time)) }}
                                                        <br /> <strong
                                                            class="card-title float-end text-primary">{{ !empty($log->user) ? $log->user->fullname : '' }}</strong>
                                                    </div>
                                                    @if ($log->status->order_status_name == 'Ready for Delivery')
                                                        <h4 class="card-title text-muted">Ready</h4>
                                                    @elseif($log->status->order_status_name == 'Dispatch')
                                                        <h4 class="card-title text-muted">Dispatch from workshop</h4>
                                                    @elseif($log->status->order_status_name == 'Order Picked Up')
                                                        <h4 class="card-title text-muted">Picked Up By Branch</h4>
                                                        <h5 class="card-title float-end text-primary">
                                                            {{ (!empty($log->branch) ? $log->branch->branch_name . ' | ' : '') . $log->name . ' | ' . $log->mobile }}
                                                        </h5>
                                                    @else
                                                        <h4 class="card-title text-muted">
                                                            {{ $log->status->order_status_name }}</h4>
                                                    @endif
                                                    {{-- <h4 class="card-title text-muted">{{($log->status->order_status_name == "Ready for Delivery" ? "Ready" : $log->status->order_status_name) }}</h4> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
            @if ($order->payment_id == 3 && session('roleId') == 2)
                <div class="col-xl-8">
                    <div class="mt-5 mt-lg-0">
                        <div class="card border shadow-none">
                            <div class="card-header bg-transparent border-bottom py-3 px-4">
                                <h5 class="font-size-16 mb-0">Payment Details</h5>
                            </div>
                            <div class="card-body p-4 pt-2">
                                <div class="project-table">
                                    <table id="ledgerTable" class="table table-striped nowrap dt-responsive"
                                        width="100%">
                                        <thead>
                                            <tr>
                                                <th>Date Time</th>
                                                <th>Mode / Narration</th>
                                                <th>Total Amount</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Total Balance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($ledgerDetails)
                                                @php
                                                    $receipt_balance = 0;
                                                    $total_amount = 0;
                                                    $credit = 0;
                                                    $debit = 0;
                                                @endphp
                                                @foreach ($ledgerDetails as $value)
                                                    <?php
                                                    $creditGreater = false;
                                                    if ($credit > $total_amount) {
                                                        $creditGreater = true;
                                                    }
                                                    if ($value->total_amount > 0) {
                                                        $total_amount += $value->total_amount;
                                                    } else {
                                                        $total_amount += $value->debit;
                                                    }
                                                    $credit += $value->credit;
                                                    $debit += $value->debit;
                                                    $receipt_balance = Custom_Helper::getLedgerCal($value, $receipt_balance, $total_amount, $credit, $debit, $creditGreater);
                                                    //
                                                    ?>
                                                    <tr {{ $value->received == 1 ? 'style=background-color:#FFDAB5;' : '' }}
                                                        class="">
                                                        <td>{{ date('d F Y', strtotime($value->created_at)) . ' ' . date('h:i A', strtotime($value->created_at)) }}
                                                        </td>
                                                        <td>{{ $value->payment_mode . ' / ' . $value->narration }}</td>
                                                        <td>{{ number_format($value->total_amount, 2) }}</td>
                                                        <td
                                                            class="{{ (float) $value->credit > 0 ? 'text-success' : '' }}">
                                                            {{ number_format($value->credit, 2) }}</td>
                                                        <td class="{{ (float) $value->debit > 0 ? 'text-danger' : '' }}">
                                                            {{ number_format($value->debit, 2) }}</td>
                                                        <td
                                                            class="{{ $total_amount > $credit ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($receipt_balance, 2) }}</td>
                                                        <td>
                                                            @if ($value->receipt_no != 0)
                                                                <a href="{{ url('print', $value->receipt_no) }}"
                                                                    class="text-success p-r-10 f-18" data-toggle="tooltip"
                                                                    data-placement="top" title=""
                                                                    data-original-title="View"><i
                                                                        class="icofont icofont-printer"></i></a>
                                                            @else
                                                                <a href="javascript:void(0)" class="text-primary"
                                                                    onclick="showEditManualPayment('{{ $value->cust_id }}','{{ $value->cust_account_id }}','{{ $value->debit }}','{{ $value->credit }}','{{ $value->narration }}')"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="Edit"><i
                                                                        class="icofont icofont-pen"></i></a>
                                                            @endif
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
                </div>
            @endif
        </div>
        <!-- end row -->
        <div id="myModal" class="modal">
            <img class="modal-content" id="img01">
            <span class="close">&times;</span>
            <div class="font-size-24" id="caption">Test</div>
        </div>
    </div>

@endsection
@section('scriptcode_three')
    <script>
        function showImage(id) {
            var modal = document.getElementById("myModal");

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementById("myImg" + id);
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            img.onclick = function() {
                console.log("Image is clicked")
                modal.style.display = "block";
                modalImg.src = this.src;
                captionText.innerHTML = "<b>Comments : </b>" + this.alt;
            }

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        // function showImage(id){

        // $("#myImg"+id).onclick(function(){
        // modal.style.display = "block";
        // modalImg.src = $(this).src;
        // captionText.innerHTML = "<b>Comments : </b>"+$(this).alt
        // })
        // }
    </script>
@endsection

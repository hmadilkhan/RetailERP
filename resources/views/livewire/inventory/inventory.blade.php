<div>
{{-- @extends('layouts.master-layout')

@section('title', 'Inventory')

@section('breadcrumtitle', 'View Inventory')
@section('navinventory', 'active')
@section('navinventorys', 'active') --}}

@section('content')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .select2-selection__rendered {
            line-height: 31px !important;
        }

        .select2-container .select2-selection--single {
            height: 35px !important;
        }

        .select2-selection__arrow {
            height: 34px !important;
        }
    </style>
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Upload Inventory</h5>
                <a href="{{ route('create-invent') }}" data-toggle="tooltip" data-placement="bottom" title=""
                    data-original-title="Create Inventory"
                    class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                        class="icofont icofont-plus m-r-5"></i> CREATE INVENTORY</a>

                <a href="{{ url('get-sample-csv') }}" data-toggle="tooltip" data-placement="bottom" title=""
                    data-original-title="Download Sample"
                    class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i
                        class="icofont icofont-plus m-r-5"></i> Download Sample</a>
            </div>
            <div class="card-block">
                @livewire('Inventory.inventory-upload')
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                @livewire('Inventory.inventory-filter')
            </div>
            <div id="table_data" class="card-block">
                {{-- @include('Inventory.inventory_table') --}}
                @livewire('Inventory.inventory-list')
            </div>
        </div>

        {{-- @include('Inventory.partials.inventory_table_modals') --}}
    </section>
@endsection
</div>
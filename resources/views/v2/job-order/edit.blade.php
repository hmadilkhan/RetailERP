@extends('layouts.master-tailwind')

@section('title', 'Job Order')
@section('page_title', 'Edit Job Order')
@section('page_subtitle', 'Update the recipe ingredients and costing.')

@section('content')
    <div class="mb-4">
        <a href="{{ url('/joborder') }}" class="text-sm font-bold text-erp-dark hover:text-erp">&larr; Back to list</a>
    </div>

    @include('v2.job-order.partials.form', [
        'isEdit' => true,
        'products' => $products,
        'raw' => $raw,
        'details' => $details,
        'general' => $general,
    ])
@endsection

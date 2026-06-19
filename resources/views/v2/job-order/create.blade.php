@extends('layouts.master-tailwind')

@section('title', 'Job Order')
@section('page_title', 'Create Job Order')
@section('page_subtitle', 'Build a recipe from raw materials and calculate its costing.')

@section('content')
    <div class="mb-4">
        <a href="{{ url('/joborder') }}" class="text-sm font-bold text-erp-dark hover:text-erp">&larr; Back to list</a>
    </div>

    @include('v2.job-order.partials.form', [
        'isEdit' => false,
        'products' => $products,
        'raw' => $raw,
    ])
@endsection

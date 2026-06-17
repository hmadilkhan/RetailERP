@extends('layouts.master-tailwind')

@section('title', 'Inventory')
@section('page_title', 'Create Inventory')
@section('page_subtitle', 'Create a product with pricing, stock opening, website catalogue data, and media from one focused form.')

@section('content')
    @include('v2.inventory.partials.form', [
        'isEdit' => false,
        'formAction' => route('insert'),
        'submitLabel' => 'Create Product',
    ])
@endsection

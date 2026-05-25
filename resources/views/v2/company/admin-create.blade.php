@extends('layouts.master-tailwind')

@section('title', 'Create Company')
@section('page_title', 'Create Company')
@section('page_subtitle', 'Create a company profile with contact, currency, package, logo, and display assets.')

@section('content')
    <form method="POST" action="{{ url('insert-company') }}" enctype="multipart/form-data">
        @csrf
        @include('v2.company.partials.admin-form', [
            'mode' => 'create',
            'submitLabel' => 'Create Company',
            'record' => null,
            'currencyname' => old('currency'),
        ])
    </form>
@endsection

@push('scripts')
    @include('v2.company.partials.admin-scripts')
@endpush

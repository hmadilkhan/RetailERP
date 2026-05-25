@extends('layouts.master-tailwind')

@section('title', 'Edit Company')
@section('page_title', 'Edit Company')
@section('page_subtitle', 'Update company details, package, currency, billing state, and visual assets.')

@section('content')
    <form method="POST" action="{{ route('company.update', $company[0]->company_id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="company_id" id="company_id" value="{{ $company[0]->company_id }}">
        <input type="hidden" name="prev_logo" id="prev_logo" value="{{ $company[0]->logo }}">
        <input type="hidden" name="pos_bg_logo" id="pos_bg_logo" value="{{ $company[0]->pos_background }}">
        <input type="hidden" name="prev_order_calling_display" id="prev_order_calling_display" value="{{ $company[0]->order_calling_display_image }}">

        @include('v2.company.partials.admin-form', [
            'mode' => 'edit',
            'submitLabel' => 'Update Company',
            'record' => $company[0],
        ])
    </form>
@endsection

@push('scripts')
    @include('v2.company.partials.admin-scripts')
@endpush

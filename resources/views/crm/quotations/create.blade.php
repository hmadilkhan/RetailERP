@extends('crm.layouts.app')

@section('title', 'Create Quotation')
@section('page_title', 'Create Quotation')
@section('page_subtitle', 'Build a premium proposal around this lead with structured line items, polished totals, and future-ready ERP alignment.')

@section('content')
    @include('crm.quotations.partials.form')
@endsection

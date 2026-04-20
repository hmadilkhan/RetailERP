@extends('crm.layouts.app')

@section('title', 'Edit Quotation')
@section('page_title', 'Edit Quotation')
@section('page_subtitle', 'Refine the proposal while keeping the CRM commercial history accurate, polished, and audit-friendly.')

@section('content')
    @include('crm.quotations.partials.form')
@endsection

@extends('crm.layouts.app')

@section('title', 'Create Lead')
@section('page_title', 'Create Lead')
@section('page_subtitle', 'Capture a new sales inquiry with clean structure, premium usability, and CRM-first organization.')

@section('content')
    @include('crm.leads.partials.form', [
        'action' => route('crm.leads.store'),
        'method' => 'POST',
        'submitLabel' => 'Create Lead',
    ])
@endsection

@extends('crm.layouts.app')

@section('title', 'Edit Lead')
@section('page_title', 'Edit Lead')
@section('page_subtitle', 'Refine lead details, keep ownership clear, and maintain a polished sales workflow.')

@section('content')
    @include('crm.leads.partials.form', [
        'action' => route('crm.leads.update', $lead),
        'method' => 'PUT',
        'submitLabel' => 'Update Lead',
    ])
@endsection

@extends('layouts.master-tailwind')

@section('title', 'Create Invoice Setup')
@section('page_title', 'Create Invoice Setup')
@section('page_subtitle', 'Add billing rates, invoice scope, cycle day, due period, and auto invoice settings.')

@section('content')
    @livewire('invoice-setup')
@endsection

@extends('layouts.master-tailwind')

@section('title', 'Edit Invoice Setup')
@section('page_title', 'Edit Invoice Setup')
@section('page_subtitle', 'Update billing rates, invoice scope, cycle day, due period, and auto invoice settings.')

@section('content')
    @livewire('invoice-setup', ['id' => $id])
@endsection

@extends('layouts.master-layout')

@section('title', 'Edit Invoice Setup')

@section('content')
    @livewire('invoice-setup', ['id' => $id])
@endsection

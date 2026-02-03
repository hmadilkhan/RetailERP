@extends('layouts.master-layout')

@section('title','Booking Slots')

@section('breadcrumtitle','Booking Slots')

@section('navwebsite','active')

@section('content')

<section class="panels-wells p-t-3">
    @livewire('booking-slots')
</section>

@endsection

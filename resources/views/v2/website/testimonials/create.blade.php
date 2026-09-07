@extends('layouts.master-tailwind')

@section('title', 'Create Testimonial')
@section('page_title', 'Create Testimonial')
@section('page_subtitle', 'Add a customer review to one of your storefronts.')

@section('content')
    @include('v2.website.testimonials.partials.form', [
        'testimonial' => null,
        'formAction' => route('testimonials.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Create Testimonial',
        'cancelUrl' => route('testimonials.index'),
    ])
@endsection

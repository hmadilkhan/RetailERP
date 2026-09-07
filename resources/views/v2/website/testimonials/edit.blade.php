@extends('layouts.master-tailwind')

@section('title', 'Edit Testimonial')
@section('page_title', 'Edit Testimonial')
@section('page_subtitle', 'Update the review shown on your storefront.')

@section('content')
    @include('v2.website.testimonials.partials.form', [
        'formAction' => route('testimonials.update', $testimonial->id),
        'formMethod' => 'PUT',
        'submitLabel' => 'Update Testimonial',
        'cancelUrl' => route('filterTestimonial', $testimonial->website_id),
    ])
@endsection

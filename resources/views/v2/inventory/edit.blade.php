@extends('layouts.master-tailwind')

@section('title', 'Inventory')
@section('page_title', 'Update Inventory')
@section('page_subtitle', 'Update product identity, pricing, catalogue visibility, media, and website content.')

@section('content')
    @include('v2.inventory.partials.form', [
        'isEdit' => true,
        'formAction' => route('update'),
        'submitLabel' => 'Save Changes',
    ])
@endsection

@extends('layouts.master-tailwind')

@section('title', 'Create User')
@section('page_title', 'Create User')
@section('page_subtitle', 'Create a staff account with company, branch, role, profile, and login credentials.')

@section('content')
    <form method="POST" action="{{ url('/store-user') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @include('v2.users.partials.form', [
            'mode' => 'create',
            'submitLabel' => 'Create User',
            'user' => null,
            'userBranches' => collect(),
        ])
    </form>
@endsection

@push('scripts')
    @include('v2.users.partials.scripts', ['mode' => 'create'])
@endpush

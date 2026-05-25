@extends('layouts.master-tailwind')

@section('title', 'Edit User')
@section('page_title', 'Edit User')
@section('page_subtitle', 'Update staff account authorization, profile information, login credentials, and image.')

@section('content')
    <form method="POST" action="{{ url('/user-update') }}" enctype="multipart/form-data" class="space-y-6">
        @method('PUT')
        @csrf

        <input type="hidden" name="id" id="id" value="{{ $userdetails[0]->id }}">
        <input type="hidden" name="prevImg" value="{{ $userdetails[0]->image }}">
        <input type="hidden" name="createdat" id="createdat" value="{{ $userdetails[0]->created_at }}">
        <input type="hidden" name="authid" id="authid" value="{{ $userdetails[0]->authorization_id }}">

        @include('v2.users.partials.form', [
            'mode' => 'edit',
            'submitLabel' => 'Update User',
            'user' => $userdetails[0],
            'userBranches' => $userBranches,
        ])
    </form>
@endsection

@push('scripts')
    @include('v2.users.partials.scripts', ['mode' => 'edit'])
@endpush

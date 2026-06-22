@extends('layouts.master-tailwind')

@section('title', 'Customer')
@section('page_title', 'Create Customer')
@section('page_subtitle', 'Add a customer profile with contact, credit, discount, and address details.')

@section('content')
    <form id="customerform" method="POST" enctype="multipart/form-data" action="{{ route('customer.store') }}">
        @csrf
        @include('v2.customer.partials.form', [
            'mode' => 'create',
            'submitLabel' => 'Create Customer',
            'customer' => null,
            'supplier' => collect(),
        ])
    </form>
@endsection

@push('scripts')
    <script>
        function restrictAlphabets(e) {
            const x = e.which || e.keyCode;
            return x >= 45 && x <= 57;
        }

        function previewImage(input, targetId) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById(targetId).src = event.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }

        document.getElementById('vdimg').addEventListener('change', function () {
            previewImage(this, 'vdpimg');
        });

        function clone_field() {
            const template = document.getElementById('supplierRowTemplate');
            document.getElementById('inputfieldClone').appendChild(template.content.cloneNode(true));
            return false;
        }

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove_row')) {
                event.target.closest('.supplier-row').remove();
            }
        });

        function CustomerType(select) {
            const supplier = document.getElementById('hideSupplierDiv');
            const rows = document.getElementById('inputfieldClone');
            if (select.value === '2') {
                supplier.classList.remove('hidden');
            } else {
                supplier.classList.add('hidden');
                rows.innerHTML = '';
            }
        }
    </script>
@endpush

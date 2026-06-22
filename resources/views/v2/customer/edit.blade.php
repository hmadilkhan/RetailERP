@extends('layouts.master-tailwind')

@section('title', 'Customer')
@section('page_title', 'Update Customer')
@section('page_subtitle', 'Review profile details, payment settings, and supplier notes before saving changes.')

@section('content')
    <form id="customerform" method="POST" action="{{ url('/updatecustomers') }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        @include('v2.customer.partials.form', [
            'mode' => 'edit',
            'submitLabel' => 'Update Customer',
            'customer' => $details[0],
            'supplier' => $supplier,
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

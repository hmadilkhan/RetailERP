<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? 'Admin') | ERP</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        erp: {
                            ink: '#0f172a',
                            panel: '#2f3437',
                            panelSoft: '#3b4145',
                            text: '#334155',
                            mute: '#64748b',
                            line: '#d8e1ec',
                            soft: '#f8fafc',
                            DEFAULT: '#4CAF50',
                            light: '#86efac',
                            dark: '#2E7D32'
                        }
                    },
                    boxShadow: {
                        panel: '0 18px 45px rgba(15, 23, 42, 0.10)',
                        menu: '0 20px 55px rgba(15, 23, 42, 0.20)'
                    }
                }
            }
        }
    </script>

    @php
        $tailwindOrdersAssets = request()->is('orders-view*');

        $tailwindLegacyAssets = request()->is('website/advertisement*')
            || request()->is('website/slider*')
            || request()->is('website/social-link*')
            || request()->is('website/terminal-assign*')
            || request()->is('website/branch-timings*')
            || request()->is('website/theme-setting*')
            || request()->is('website/testimonials*')
            || request()->is('website/customer-reviews*')
            || request()->is('website/booking-slots*')
            || request()->is('create-demand')
            || request()->is('edit-demand*')
            || request()->is('discount-panel*')
            || request()->is('ledgerDetails*')
            || request()->is('measurement*')
            || request()->is('create-customer-payment*')
            || request()->is('get-customer-receipts*')
            || request()->is('demand-details*')
            || request()->is('received-demandpanel*')
            || request()->is('view-transfer*')
            || request()->is('createdeliverychallan*')
            || request()->is('generate-po*')
            || request()->is('showtransferdetails*')
            || request()->is('createGRN*');

        $tailwindPurchaseAssets = request()->is('add-purchase') || request()->is('edit/*');
        $tailwindDashboardAssets = request()->is('dashboard');
        $tailwindDiscountAssets = request()->is('create-discount') || request()->is('edit-discount*');
        $tailwindFullWidthPage = request()->is('create-discount') || request()->is('edit-discount*');
    @endphp

    @if ($tailwindLegacyAssets)
        @include('partials.html-libs')
    @endif
    @php
        $tailwindSelect2Assets = request()->is('billing*')
            || request()->is('invoice-setup*')
            || request()->is('rooms')
            || request()->is('view-floors')
            || request()->is('view-kitchen-departments')
            || request()->is('view-accounts')
            || request()->is('view-cheque')
            || request()->is('cash-deposit')
            || request()->is('view-bank-discount')
            || request()->is('get-banks')
            || request()->is('create-bank')
            || request()->is('bankaccounts-details')
            || request()->is('expense-report')
            || request()->is('vendor-report')
            || request()->is('customer-report')
            || request()->is('erpreportdashboard')
            || request()->is('reports/item-sale-report')
            || request()->is('reports/consolidated-item-sale-report')
            || request()->is('vendors')
            || request()->is('BusinessPolicy')
            || request()->is('view-purchases')
            || request()->is('vendor-payment-view')
            || request()->is('delivery/lists')
            || request()->is('service-provider')
            || request()->is('drivers')
            || request()->is('vehicles')
            || request()->is('create-transferorder')
            || request()->is('edit_trf_details*')
            || request()->is('orders-view*')
            || request()->is('create-inventory')
            || request()->is('edit-invent*')
            || request()->is('customer*')
            || request()->is('editcustomers*')
            || request()->is('customer-due-payment')
            || request()->is('mobile-promotion');
    @endphp
    @if (($tailwindSelect2Assets || $tailwindPurchaseAssets || $tailwindDiscountAssets) && !$tailwindLegacyAssets)
        <link rel="stylesheet" href="{{ asset('components/select2/dist/css/select2.min.css') }}" />
    @endif
    @if ($tailwindDiscountAssets && !$tailwindLegacyAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('components/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datetimepicker.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/sweetalert/css/sweetalert.css') }}">
    @endif
    @if ($tailwindOrdersAssets && !$tailwindLegacyAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/themify-icons/themify-icons.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('components/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/sweetalert/css/sweetalert.css') }}">
    @endif
    @if ($tailwindPurchaseAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('components/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/sweetalert/css/sweetalert.css') }}">
    @endif
    @if ($tailwindDashboardAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('js/raphael-min.js') }}"></script>
        <script src="{{ asset('components/morris.js/morris.js') }}"></script>
    @endif
    @yield('css_code')
    @yield('scriptcode_one')
    @stack('styles')
    <style>
        @font-face {
            font-family: 'yaro';
            src: url('{{ asset('assets/fonts/YaroRg-Bold.woff2') }}') format('woff2'),
                 url('{{ asset('assets/fonts/YaroRg-Bold.woff') }}') format('woff');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .v2-sidebar.is-collapsed {
                width: 5rem;
            }

            .v2-sidebar.is-expanded {
                width: 18rem;
            }

            .v2-sidebar .v2-sidebar-full {
                max-width: 0;
                opacity: 0;
                overflow: hidden;
                white-space: nowrap;
                transition: max-width 0.2s ease, opacity 0.2s ease;
            }

            .v2-sidebar.is-expanded .v2-sidebar-full {
                max-width: 14rem;
                opacity: 1;
            }

            .v2-sidebar.is-collapsed .v2-brand-lines,
            .v2-sidebar.is-collapsed .v2-sidebar-section,
            .v2-sidebar.is-collapsed .v2-sidebar-chevron,
            .v2-sidebar.is-collapsed ul ul {
                display: none !important;
            }

            .v2-sidebar .v2-sidebar-compact,
            .v2-sidebar.is-collapsed .v2-sidebar-expanded {
                display: none !important;
            }

            .v2-sidebar.is-collapsed .v2-sidebar-compact {
                display: flex !important;
            }

            .v2-sidebar.is-expanded .v2-sidebar-expanded {
                display: block !important;
            }

            .v2-sidebar.is-collapsed .v2-brand-text {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }

            .v2-sidebar.is-collapsed nav {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .v2-sidebar.is-collapsed nav a,
            .v2-sidebar.is-collapsed nav button {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .v2-sidebar.is-collapsed .v2-sidebar-footer {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }
        @if ($tailwindSelect2Assets || $tailwindPurchaseAssets || $tailwindDiscountAssets)
            .billing-select2.select2-hidden-accessible + .select2-container,
            .v2-select2.select2-hidden-accessible + .select2-container {
                width: 100% !important;
                min-width: 0;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple {
                min-height: 2.5rem;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
                background: #fff;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
                display: flex;
                align-items: center;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple {
                align-items: flex-start;
                padding: 0.25rem 0.375rem;
            }
            .billing-select2-lg.select2-hidden-accessible + .select2-container .select2-selection--single,
            .v2-select2-lg.select2-hidden-accessible + .select2-container .select2-selection--single {
                min-height: 2.75rem;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection__rendered,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection__rendered {
                color: #334155;
                font-size: 0.875rem;
                line-height: 1.25rem;
                padding-left: 0.75rem;
                padding-right: 2rem;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple .select2-selection__rendered {
                padding: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple .select2-selection__choice {
                margin: 0;
                border: 1px solid #d8e1ec;
                border-radius: 0.375rem;
                background: #f8fafc;
                color: #334155;
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0.125rem 0.5rem 0.125rem 1.25rem;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple .select2-search__field {
                margin: 0.125rem 0;
                min-height: 1.75rem;
                font-size: 0.875rem;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection__placeholder,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection__placeholder {
                color: #64748b;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection__arrow,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection__arrow {
                height: 100%;
                right: 0.45rem;
            }
            .billing-select2.select2-hidden-accessible + .select2-container.select2-container--focus .select2-selection--single,
            .billing-select2.select2-hidden-accessible + .select2-container.select2-container--open .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--focus .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--open .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--focus .select2-selection--multiple,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--open .select2-selection--multiple {
                border-color: #4CAF50;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
            }
            .select2-container--default .billing-select2-dropdown.select2-dropdown,
            .select2-container--default .v2-select2-dropdown.select2-dropdown {
                border-color: #d8e1ec;
                border-radius: 0.5rem;
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
                overflow: hidden;
            }
            .billing-select2-dropdown .select2-search--dropdown,
            .v2-select2-dropdown .select2-search--dropdown {
                padding: 0.5rem;
            }
            .billing-select2-dropdown .select2-search__field,
            .v2-select2-dropdown .select2-search__field {
                border: 1px solid #d8e1ec !important;
                border-radius: 0.5rem;
                min-height: 2.25rem;
                font-size: 0.875rem;
                outline: none;
            }
            .billing-select2-dropdown .select2-results__option,
            .v2-select2-dropdown .select2-results__option {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            .billing-select2-dropdown .select2-results__option--highlighted[aria-selected],
            .v2-select2-dropdown .select2-results__option--highlighted[aria-selected] {
                background: #4CAF50;
            }
        @endif
        @if ($tailwindLegacyAssets || $tailwindDiscountAssets)
            .content-wrapper,
            .container-fluid {
                background: transparent !important;
            }
            .panels-wells,
            .container-fluid {
                padding: 0 !important;
            }
            .card {
                border: 1px solid #d8e1ec !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06) !important;
                overflow: hidden;
            }
            .card-header {
                background: #f8fafc !important;
                border-bottom: 1px solid #d8e1ec !important;
                padding: 1rem 1.25rem !important;
            }
            .card-header-text,
            .card-header h4,
            .card-header h5 {
                color: #0f172a !important;
                font-weight: 800 !important;
                letter-spacing: 0 !important;
                margin: 0 !important;
            }
            .card-block,
            .card-body {
                padding: 1.25rem !important;
            }
            .form-control {
                border-color: #d8e1ec !important;
                border-radius: 0.5rem !important;
                box-shadow: none !important;
            }
            .form-control:focus {
                border-color: #4CAF50 !important;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.12) !important;
            }
            .btn {
                border-radius: 0.5rem !important;
                font-weight: 700 !important;
            }
            .table thead th {
                background: #f8fafc !important;
                color: #64748b !important;
                font-size: 0.75rem !important;
                font-weight: 800 !important;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                border-bottom: 1px solid #d8e1ec !important;
            }
            .table tbody td {
                color: #334155;
                vertical-align: middle !important;
                border-color: #edf2f7 !important;
            }
            .f-right {
                float: right !important;
            }
            .d-none {
                display: none !important;
            }
            .m-t-2 {
                margin-top: 0.5rem !important;
            }
            .m-t-10 {
                margin-top: 1rem !important;
            }
            .m-b-10 {
                margin-bottom: 1rem !important;
            }
            .m-b-20 {
                margin-bottom: 1.25rem !important;
            }
            .p-t-30 {
                padding-top: 1.5rem !important;
            }
            .f-24 {
                font-size: 1.5rem !important;
            }
            .f-18 {
                font-size: 1.125rem !important;
            }
            .m-l-1 {
                margin-left: 0.25rem !important;
            }
            .pointer {
                cursor: pointer !important;
            }
            .badge-lg {
                padding: 0.45rem 0.65rem !important;
                font-size: 0.8125rem !important;
                line-height: 1.1 !important;
            }
            .badge-header3 {
                margin-left: 0.35rem;
                vertical-align: middle;
            }
        @endif
        @if ($tailwindFullWidthPage)
            html,
            body {
                overflow-x: hidden;
            }
            .v2-app-shell,
            .v2-top-header,
            .v2-main-content,
            .v2-page-heading {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                overflow-x: hidden !important;
            }
            .v2-app-shell {
                margin-left: 0 !important;
                transition: padding-left 0.3s ease !important;
            }
            .v2-top-header {
                left: auto !important;
                right: auto !important;
                margin-left: 0 !important;
            }
            .v2-main-content {
                margin-left: 0 !important;
                padding-left: clamp(1rem, 2vw, 2rem) !important;
                padding-right: clamp(1rem, 2vw, 2rem) !important;
            }
            @media (min-width: 1024px) {
                .v2-app-shell.v2-shell-collapsed {
                    padding-left: 5rem !important;
                }
                .v2-app-shell.v2-shell-expanded {
                    padding-left: 18rem !important;
                }
            }
            .v2-discount-page,
            .v2-discount-page form,
            .v2-discount-page .card,
            .v2-discount-page .card-block,
            .v2-discount-page .card-header {
                width: 100% !important;
                max-width: none !important;
                min-width: 0;
                box-sizing: border-box;
            }
            .v2-discount-page .card {
                float: none !important;
                clear: both;
            }
            .v2-discount-page .card-block::after,
            .v2-discount-page .card::after {
                content: "";
                display: table;
                clear: both;
            }
            .v2-discount-page [class*="col-lg-"],
            .v2-discount-page [class*="col-md-"] {
                max-width: 100%;
                box-sizing: border-box;
            }
            .v2-discount-page .m-l-30,
            .v2-discount-page .m-l-40 {
                margin-left: 0 !important;
            }
            .v2-discount-page .select2,
            .v2-discount-page .select2-container,
            .v2-discount-page .form-control,
            .v2-discount-page table {
                max-width: 100% !important;
            }
            .v2-discount-page .select2-hidden-accessible + .select2-container {
                width: 100% !important;
                min-width: 0;
            }
            .v2-discount-page .form-radio {
                display: grid;
                gap: 0.625rem;
            }
            .v2-radio-list {
                display: grid !important;
                grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr));
                gap: 0.75rem;
                width: 100%;
            }
            .v2-radio-row {
                position: static !important;
                display: flex !important;
                align-items: center !important;
                gap: 0.6rem !important;
                min-height: 3rem;
                width: 100% !important;
                margin: 0 !important;
                padding: 0.75rem 0.85rem !important;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
                background: #ffffff;
                color: #0f172a;
                font-size: 0.9rem;
                font-weight: 700;
                line-height: 1.35;
                cursor: pointer;
                transform: none !important;
            }
            .v2-radio-row:hover,
            .v2-radio-row.is-selected {
                border-color: #4CAF50;
                background: #f3fbf4;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.10);
            }
            .v2-radio-row input[type="radio"] {
                position: static !important;
                display: inline-block !important;
                flex: 0 0 auto;
                width: 1rem !important;
                height: 1rem !important;
                min-width: 1rem !important;
                margin: 0 !important;
                opacity: 1 !important;
                pointer-events: auto !important;
                accent-color: #4CAF50;
                transform: none !important;
            }
            .v2-radio-row span {
                display: inline-block;
                min-width: 0;
                overflow-wrap: anywhere;
            }
            .v2-inline-action {
                margin-left: auto;
                min-height: 1.8rem;
                padding: 0.25rem 0.55rem;
                border: 1px solid rgba(76, 175, 80, 0.28);
                border-radius: 0.45rem;
                background: #ffffff;
                color: #2E7D32;
                font-size: 0.75rem;
                font-weight: 800;
                line-height: 1.2;
                white-space: nowrap;
            }
            .v2-discount-page .radio,
            .v2-discount-page .rkmd-checkbox {
                display: flex;
                align-items: center;
                min-height: 2rem;
                margin: 0.25rem 0;
                color: #334155;
            }
            .v2-discount-page .radio label,
            .v2-discount-page .input-checkbox,
            .v2-discount-page .rkmd-checkbox label {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin: 0;
                cursor: pointer;
                line-height: 1.4;
            }
            .v2-discount-page .radio input[type="radio"],
            .v2-discount-page .input-checkbox input[type="checkbox"],
            .v2-discount-page .rkmd-checkbox input[type="checkbox"] {
                position: static !important;
                display: inline-block !important;
                width: 1rem;
                height: 1rem;
                min-width: 1rem;
                margin: 0 !important;
                opacity: 1 !important;
                cursor: pointer;
                accent-color: #4CAF50;
            }
            .v2-discount-page .radio .helper,
            .v2-discount-page .rkmd-checkbox .checkbox,
            .v2-discount-page .rkmd-checkbox .ripple {
                display: none !important;
            }
            .v2-discount-page .captions,
            .v2-discount-page .captions2 {
                display: inline-flex;
                align-items: center;
                min-height: 1.5rem;
                margin-left: 0.5rem;
                color: #334155;
                font-size: 0.875rem;
                line-height: 1.4;
            }
            .v2-discount-page .rkmd-checkbox > .input-checkbox + .captions,
            .v2-discount-page .rkmd-checkbox > .input-checkbox + .captions2 {
                margin-left: 0.5rem;
            }
            .v2-page-heading,
            .v2-discount-page {
                max-width: 1280px !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .v2-discount-page {
                padding-left: 0 !important;
                padding-right: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 2rem !important;
            }
            .v2-discount-page.panels-wells,
            .v2-discount-page .panels-wells {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            .v2-discount-grid {
                display: grid;
                grid-template-columns: minmax(0, 7fr) minmax(18rem, 4fr);
                gap: 1.5rem;
                align-items: start;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }
            .v2-discount-form-panel,
            .v2-discount-form-stack,
            .v2-discount-summary-panel {
                min-width: 0;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }
            .v2-discount-form-panel {
                display: block;
            }
            .v2-discount-form-stack {
                display: grid;
                gap: 1rem;
            }
            .v2-discount-page .card {
                margin-bottom: 0 !important;
                border: 1px solid #d8e1ec !important;
                border-radius: 0.625rem !important;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
                overflow: visible;
            }
            .v2-discount-page .card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                min-height: 3.25rem;
                padding: 0.9rem 1.125rem !important;
            }
            .v2-discount-page .card-header-text {
                margin: 0 !important;
                color: #0f172a !important;
                font-size: 0.95rem !important;
                font-weight: 800 !important;
                letter-spacing: 0 !important;
                line-height: 1.25;
                text-transform: none;
            }
            .v2-discount-page .card-block {
                padding: 1rem 1.125rem !important;
            }
            .v2-discount-page .form-group {
                margin-bottom: 1rem;
            }
            .v2-discount-page .form-control-label {
                display: inline-block;
                margin-bottom: 0.35rem;
                color: #334155;
                font-size: 0.8125rem;
                font-weight: 800;
                line-height: 1.35;
            }
            .v2-discount-page .form-control-feedback,
            .v2-discount-page .help-block {
                margin-top: 0.35rem;
                color: #64748b;
                font-size: 0.78rem;
                line-height: 1.35;
            }
            .v2-discount-page .form-control {
                min-height: 2.45rem;
                border-color: #d8e1ec !important;
                border-radius: 0.5rem !important;
                color: #0f172a;
                font-size: 0.875rem;
                box-shadow: none !important;
            }
            .v2-discount-page .form-control:focus {
                border-color: #4CAF50 !important;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.14) !important;
            }
            .v2-discount-page .select2-container .select2-selection--single,
            .v2-discount-page .select2-container .select2-selection--multiple {
                min-height: 2.45rem;
                border: 1px solid #d8e1ec !important;
                border-radius: 0.5rem !important;
                box-shadow: none !important;
            }
            .v2-discount-page .select2-container .select2-selection--single {
                display: flex;
                align-items: center;
            }
            .v2-discount-page .select2-container .select2-selection--single .select2-selection__rendered {
                width: 100%;
                padding-left: 0.75rem;
                padding-right: 2rem;
                color: #0f172a;
                line-height: 2.35rem;
            }
            .v2-discount-page .select2-container .select2-selection--single .select2-selection__arrow {
                height: 2.35rem;
                right: 0.5rem;
            }
            .v2-discount-page .select2-container .select2-selection--multiple .select2-selection__rendered {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
                padding: 0.35rem 0.45rem;
            }
            .v2-discount-page .select2-container .select2-selection--multiple .select2-selection__choice {
                margin: 0 !important;
                border: 0 !important;
                border-radius: 999px !important;
                background: #e8f5e9 !important;
                color: #1b5e20 !important;
                font-size: 0.78rem;
                font-weight: 700;
            }
            .v2-discount-page .radio label {
                width: 100%;
                min-height: 2.3rem;
                padding: 0.5rem 0.65rem;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
                background: #ffffff;
            }
            .v2-discount-page .radio label:hover,
            .v2-discount-page .rkmd-checkbox:hover .captions,
            .v2-discount-page .rkmd-checkbox:hover .captions2 {
                color: #1b5e20;
            }
            .v2-discount-page .radio a {
                margin-left: 0.35rem;
                font-weight: 800;
                cursor: pointer;
            }
            .v2-options-card .card-block {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.85rem 1rem;
            }
            .v2-options-card .card-block::after {
                display: none;
            }
            .v2-options-card .card-block > [class*="col-"] {
                float: none !important;
                width: auto !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .v2-options-card #divminChkBox {
                grid-column: 1 / -1;
                padding: 0.75rem 0.85rem;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
                background: #f8fafc;
            }
            .v2-days-card .card-block {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(8.75rem, 1fr));
                gap: 0.6rem 0.75rem;
            }
            .v2-days-header {
                align-items: center !important;
                flex-direction: row !important;
            }
            .v2-select-all-days {
                justify-content: flex-end;
                margin-left: auto !important;
            }
            .v2-select-all-days .input-checkbox {
                min-height: 2rem;
                padding: 0.35rem 0.65rem;
                border: 1px solid #d8e1ec;
                border-radius: 999px;
                background: #ffffff;
                color: #0f172a;
                font-size: 0.8125rem;
                font-weight: 800;
            }
            .v2-days-card .card-block::after {
                display: none;
            }
            .v2-days-card .card-block > .rkmd-checkbox {
                float: none !important;
                width: auto !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0.55rem 0.65rem !important;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
                background: #ffffff;
            }
            .v2-discount-actions-card {
                display: flex;
                justify-content: flex-end;
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
                padding: 0.25rem 0 0 !important;
            }
            .v2-discount-actions-card::after {
                display: none !important;
            }
            .v2-discount-actions-card .btn {
                min-width: 8rem;
                min-height: 2.6rem;
            }
            .v2-discount-summary-panel {
                align-self: start;
                overflow: hidden !important;
            }
            .v2-discount-summary-panel .card-header {
                background: #f8fafc !important;
            }
            .v2-discount-summary-panel #salename {
                min-height: 2.25rem;
                margin: 0;
                padding: 1rem 1.125rem 0.35rem;
                color: #0f172a;
                font-size: 1.25rem;
                font-weight: 800;
                line-height: 1.25;
                overflow-wrap: anywhere;
            }
            .v2-discount-summary-panel #description {
                display: grid;
                gap: 0.45rem;
                margin: 0 !important;
                padding: 0.4rem 1.125rem 1.125rem 1.6rem !important;
                color: #334155;
                font-size: 0.875rem;
                line-height: 1.45;
            }
            .v2-discount-page .modal-dialog {
                width: min(760px, calc(100vw - 2rem)) !important;
                max-width: min(760px, calc(100vw - 2rem)) !important;
                margin: 1.5rem auto !important;
            }
            .v2-discount-page .modal-content {
                border: 0 !important;
                border-radius: 0.625rem !important;
                overflow: hidden;
                box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22) !important;
            }
            .v2-discount-page .modal-header,
            .v2-discount-page .modal-footer {
                border-color: #d8e1ec !important;
            }
            .v2-discount-page .modal-body .row {
                display: flex;
                gap: 0.75rem;
                align-items: center;
                margin-left: 0;
                margin-right: 0;
            }
            .v2-discount-page .modal-body .row > [class*="col-"] {
                float: none;
                width: auto;
                padding-left: 0;
                padding-right: 0;
            }
            .v2-discount-page .modal-body .row > .col-md-9 {
                flex: 1 1 auto;
            }
            .v2-discount-page #divProd {
                height: auto !important;
                max-height: 52vh;
                margin-top: 1rem;
                overflow: auto !important;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
            }
            .v2-discount-page #inventtbl {
                margin-bottom: 0;
            }
            .v2-discount-page #inventtbl th,
            .v2-discount-page #inventtbl td {
                white-space: normal;
            }
            .v2-discount-page #inventtbl i {
                cursor: pointer;
            }
            @media (min-width: 1024px) {
                .v2-discount-summary-panel {
                    position: sticky;
                    top: 5.25rem;
                }
            }
            @media (max-width: 991.98px) {
                .v2-discount-grid {
                    grid-template-columns: 1fr;
                }
                .v2-discount-summary-panel {
                    position: static;
                }
                .v2-discount-page .card-header {
                    align-items: flex-start;
                    flex-direction: column;
                }
                .v2-radio-list,
                .v2-options-card .card-block {
                    grid-template-columns: 1fr;
                }
                .v2-discount-page .modal-body .row {
                    align-items: stretch;
                    flex-direction: column;
                }
                .v2-discount-page .modal-body .row > [class*="col-"],
                .v2-discount-page .modal-body .row .btn {
                    width: 100%;
                }
            }
        @endif
    </style>
    @livewireStyles
</head>

<body class="min-h-screen bg-[#f3f6f9] text-erp-text antialiased">
    @php
        $currentUrl = request()->path();
        $sidebarRoleId = session('roleId');

        if (auth()->check() && app()->bound('impersonate') && app('impersonate')->isImpersonating()) {
            $sidebarRoleId = \Illuminate\Support\Facades\DB::table('user_authorization')
                ->where('user_id', auth()->id())
                ->value('role_id') ?? $sidebarRoleId;
        }

        $sidebarPages = collect();
        if ($sidebarRoleId) {
            $companyPackage = \Illuminate\Support\Facades\DB::table('company')
                ->where('company_id', session('company_id'))
                ->whereNotNull('package_id')
                ->first();

            if ($companyPackage) {
                $sidebarPageIds = \Illuminate\Support\Facades\DB::select(
                    'SELECT page_id from role_settings WHERE role_id = ? and page_id IN (SELECT page_id FROM package_module_permissions where package_id = ?) ORDER BY page_id',
                    [$sidebarRoleId, $companyPackage->package_id]
                );
            } else {
                $sidebarPageIds = \Illuminate\Support\Facades\DB::select(
                    'SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',
                    [$sidebarRoleId]
                );
            }

            $sidebarPageIds = collect($sidebarPageIds)->pluck('page_id')->all();

            if (!empty($sidebarPageIds)) {
                $sidebarPages = \Illuminate\Support\Facades\DB::table('pages_details')
                    ->whereIn('id', $sidebarPageIds)
                    ->get();
            }
        }

        $sidebarLabel = function ($page) {
            return __('sidebar.' . \Illuminate\Support\Str::snake(strtolower($page->page_name)));
        };

        $sidebarUrlMatches = function ($pageUrl) use ($currentUrl) {
            $pageUrl = trim((string) $pageUrl, '/');

            if ($pageUrl === $currentUrl) {
                return true;
            }

            $activeAliases = [
                'branches' => ['createbranch', 'branch-edit/*', 'branch-emails/*'],
                'usersDetails' => ['userdetails', 'create-user', 'user-edit/*'],
                'userdetails' => ['usersDetails', 'create-user', 'user-edit/*'],
                'companies' => ['createcompany'],
                'company' => ['company/create', 'company/*/edit', 'company-edit/*'],
                'terminal-manager' => ['permission/*', 'printing-details/*', 'bind-terminals/*/*'],
                'terminals' => ['terminal-manager', 'permission/*', 'printing-details/*', 'bind-terminals/*/*'],
                'roles' => ['roles'],
                'pages' => ['pages'],
                'modules-permissions' => ['modules-permissions'],
                'website' => ['website/*', 'delivery/lists'],
                'website/advertisement/lists' => ['website/advertisement/*'],
                'website/slider/lists' => ['website/slider/*'],
                'website/social-link/lists' => ['website/social-link/*'],
                'delivery/lists' => ['delivery/*'],
                'website/terminal-assign/view' => ['website/terminal-assign/*'],
                'website/branch-timings/view' => ['website/branch-timings/*'],
                'website/theme-setting' => ['website/theme-setting/*'],
                'website/testimonials' => ['website/testimonials/*'],
                'website/customer-reviews/lists' => ['website/customer-reviews/*'],
                'invoice-setup' => ['invoice-setup/*'],
                'billing/summary' => ['billing/summary'],
                'billing/invoices' => ['billing/invoices/*'],
                'billing/delivery-history' => ['billing/delivery-history'],
            ];

            foreach (($activeAliases[$pageUrl] ?? []) as $pattern) {
                if (\Illuminate\Support\Str::is($pattern, $currentUrl)) {
                    return true;
                }
            }

            return false;
        };

        $sidebarIsActive = function ($page) use ($sidebarUrlMatches) {
            return $sidebarUrlMatches($page->page_url);
        };

        $sidebarHasActiveDescendant = function ($page) use (&$sidebarHasActiveDescendant, $sidebarPages, $sidebarUrlMatches) {
            return $sidebarPages->where('parent_id', $page->id)->contains(function ($child) use (&$sidebarHasActiveDescendant, $sidebarUrlMatches) {
                return $sidebarUrlMatches($child->page_url) || $sidebarHasActiveDescendant($child);
            });
        };

        $sidebarTopPages = $sidebarPages->filter(function ($page) {
            return (int) $page->parent_id === 0 || $page->page_mode === 'Label' || $page->page_mode === 'Parent';
        });
    @endphp

    <div x-data="{ sidebarOpen: false, sidebarExpanded: false }" class="min-h-screen">
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

        <aside
            @mouseenter="sidebarExpanded = true"
            @mouseleave="sidebarExpanded = false"
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarExpanded ? 'is-expanded lg:w-72' : 'is-collapsed lg:w-20'
            ]"
            x-cloak
            class="v2-sidebar fixed inset-y-0 left-0 z-40 flex w-72 transform flex-col bg-erp-panel text-white shadow-menu transition-all duration-300 lg:translate-x-0">
            <div class="border-b border-white/10 px-5 py-6">
                <a href="{{ url('/dashboard') }}"
                    class="v2-sidebar-compact hidden h-12 items-center justify-center rounded-lg bg-white/[0.04] text-3xl font-bold leading-none text-[#2faa4f] transition hover:bg-white/[0.08]"
                    style="font-family: yaro, Arial, sans-serif; letter-spacing: 0.02em;"
                    aria-label="Sabify dashboard">
                    S
                </a>
                <a href="{{ url('/dashboard') }}"
                    class="v2-sidebar-expanded group relative block overflow-hidden rounded-lg px-3 py-4 text-center transition hover:bg-white/[0.04]">
                    <span class="pointer-events-none absolute inset-x-8 top-4 h-8 rounded-full bg-[#2faa4f]/18 blur-xl transition group-hover:bg-[#2faa4f]/25"></span>
                    <span class="relative flex items-center justify-center gap-2">
                        <span class="v2-brand-lines h-2 w-2 rounded-full bg-[#2faa4f] shadow-[0_0_16px_rgba(47,170,79,0.85)]"></span>
                        <span class="v2-brand-lines h-px w-8 bg-gradient-to-r from-transparent to-[#2faa4f]/70"></span>
                        <span class="v2-brand-text text-[42px] font-bold leading-none text-[#2faa4f]"
                            style="font-family: yaro, Arial, sans-serif; letter-spacing: 0.02em;">
                            sabify
                        </span>
                        <span class="v2-brand-lines h-px w-8 bg-gradient-to-l from-transparent to-[#2faa4f]/70"></span>
                        <span class="v2-brand-lines h-2 w-2 rounded-full bg-[#2faa4f] shadow-[0_0_16px_rgba(47,170,79,0.85)]"></span>
                    </span>
                    <span class="v2-sidebar-full relative mt-2 block text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">
                        Retail ERP
                    </span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5">
                <ul class="space-y-1">
                    @forelse ($sidebarTopPages as $pages)
                        @if ($pages->page_mode == 'Label')
                            <li class="v2-sidebar-section px-3 pb-1 pt-5 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">
                                {{ $sidebarLabel($pages) }}
                            </li>
                        @elseif ($pages->page_mode == 'Parent')
                            @php
                                $children = $sidebarPages->where('parent_id', $pages->id);
                                $hasChildren = $children->isNotEmpty() && (int) $pages->icofont_arrow !== 0;
                                $isActive = $sidebarIsActive($pages);
                                $hasActiveChild = $sidebarHasActiveDescendant($pages)
                                    || (\Illuminate\Support\Str::snake(strtolower($pages->page_name)) === 'billing_section'
                                        && (\Illuminate\Support\Str::is('billing/*', $currentUrl) || \Illuminate\Support\Str::is('invoice-setup*', $currentUrl)));
                            @endphp

                            @if (!$hasChildren)
                                <li>
                                    <a href="{{ url('/' . $pages->page_url) }}" @click="sidebarOpen = false"
                                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-white text-erp-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                        <i class="{{ $pages->icofont }} w-5 shrink-0 text-center {{ $isActive ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                        <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ $sidebarLabel($pages) }}</span>
                                    </a>
                                </li>
                            @else
                                <li x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }">
                                    <button type="button" @click="open = !open"
                                        class="group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-semibold transition {{ $hasActiveChild ? 'bg-white text-erp-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                        <i class="{{ $pages->icofont }} w-5 shrink-0 text-center {{ $hasActiveChild ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                        <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ $sidebarLabel($pages) }}</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="v2-sidebar-chevron h-4 w-4 shrink-0 text-slate-500 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <ul x-show="open" x-cloak class="mt-1 space-y-1 border-l border-white/10 pl-3">
                                        @foreach ($children as $childs)
                                            @php
                                                $grandchildren = $sidebarPages->where('parent_id', $childs->id);
                                                $childHasChildren = $grandchildren->isNotEmpty() && (int) $childs->icofont_arrow !== 0;
                                                $childActive = $sidebarIsActive($childs);
                                                $hasActiveGrandchild = $sidebarHasActiveDescendant($childs);
                                            @endphp

                                            @if (!$childHasChildren)
                                                <li>
                                                    <a href="{{ url('/' . $childs->page_url) }}" @click="sidebarOpen = false"
                                                        class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $childActive ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                        <i class="{{ $childs->icofont }} w-5 shrink-0 text-center {{ $childActive ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                        <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ $sidebarLabel($childs) }}</span>
                                                    </a>
                                                </li>
                                            @else
                                                <li x-data="{ open: {{ $hasActiveGrandchild ? 'true' : 'false' }} }">
                                                    <button type="button" @click="open = !open"
                                                        class="group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-semibold transition {{ $hasActiveGrandchild ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                        <i class="{{ $childs->icofont }} w-5 shrink-0 text-center {{ $hasActiveGrandchild ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                        <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ $sidebarLabel($childs) }}</span>
                                                        <svg :class="open ? 'rotate-180' : ''" class="v2-sidebar-chevron h-4 w-4 shrink-0 text-slate-500 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>

                                                    <ul x-show="open" x-cloak class="mt-1 space-y-1 border-l border-white/10 pl-3">
                                                        @foreach ($grandchildren as $grandchild)
                                                            @php
                                                                $greatGrandchildren = $sidebarPages->where('parent_id', $grandchild->id);
                                                                $grandchildActive = $sidebarIsActive($grandchild);
                                                                $hasActiveGreatGrandchild = $sidebarHasActiveDescendant($grandchild);
                                                            @endphp
                                                            <li>
                                                                <a href="{{ url('/' . $grandchild->page_url) }}" @click="sidebarOpen = false"
                                                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $grandchildActive || $hasActiveGreatGrandchild ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                                    <i class="{{ $grandchild->icofont }} w-5 shrink-0 text-center {{ $grandchildActive || $hasActiveGreatGrandchild ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                                    <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ $sidebarLabel($grandchild) }}</span>
                                                                </a>

                                                                @if ($greatGrandchildren->isNotEmpty())
                                                                    <ul class="mt-1 space-y-1 border-l border-white/10 pl-3">
                                                                        @foreach ($greatGrandchildren as $grandgrandchild)
                                                                            @php
                                                                                $greatGrandchildActive = $sidebarIsActive($grandgrandchild);
                                                                            @endphp
                                                                            <li>
                                                                                <a href="{{ url('/' . $grandgrandchild->page_url) }}" @click="sidebarOpen = false"
                                                                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $greatGrandchildActive ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                                                    <i class="{{ $grandgrandchild->icofont }} w-5 shrink-0 text-center {{ $greatGrandchildActive ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                                                    <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ $sidebarLabel($grandgrandchild) }}</span>
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach

                                        @if (\Illuminate\Support\Str::snake(strtolower($pages->page_name)) === 'billing_section')
                                            <li>
                                                <a href="{{ route('billing.delivery-history') }}" @click="sidebarOpen = false"
                                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $currentUrl == 'billing/delivery-history' ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                    <i class="icofont icofont-history w-5 shrink-0 text-center {{ $currentUrl == 'billing/delivery-history' ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                    <span class="v2-sidebar-full min-w-0 flex-1 truncate">{{ __('sidebar.delivery_history') }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @empty
                        <li class="px-3 py-3 text-sm font-semibold text-slate-400">No menu items available.</li>
                    @endforelse

                    @if (session('roleId') == 1)
                        <li class="v2-sidebar-section px-3 pb-1 pt-5 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">
                            Admin Tools
                        </li>
                        <li>
                            <a href="{{ route('whatsapp.access.manager') }}" @click="sidebarOpen = false"
                                class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $currentUrl == 'whatsapp-access-manager' ? 'bg-white text-erp-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                <i class="icofont icofont-ui-social-link w-5 shrink-0 text-center {{ $currentUrl == 'whatsapp-access-manager' ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                <span class="v2-sidebar-full min-w-0 flex-1 truncate">WhatsApp Access</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="v2-sidebar-footer border-t border-white/10 p-4">
                <div class="v2-sidebar-compact hidden flex-col items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-lg bg-white/10 text-xs font-black text-white ring-1 ring-white/10"
                        title="{{ auth()->user()->fullname ?? auth()->user()->username ?? 'Admin' }}">
                        @if (!empty(session('image')))
                            <img class="h-full w-full object-cover"
                                src="{{ asset('storage/images/users/' . session('image')) }}"
                                alt="{{ auth()->user()->fullname ?? auth()->user()->username ?? 'Admin' }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->fullname ?? auth()->user()->username ?? 'A', 0, 2)) }}
                        @endif
                    </div>

                    @if (app()->bound('impersonate') && app('impersonate')->isImpersonating())
                        <a href="{{ route('impersonate.leave') }}"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 bg-white/10 text-white transition hover:bg-white/15"
                            title="Switch back to your account">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </a>
                    @else
                        <button type="button"
                            onclick="event.preventDefault(); document.getElementById('tailwind-logout-form').submit();"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-rose-300/20 bg-rose-500/10 text-rose-100 transition hover:bg-rose-500/20"
                            title="Logout">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h10" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="v2-sidebar-expanded rounded-lg bg-white/10 p-3">
                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500">Signed in</div>
                    <div class="mt-2 truncate text-sm font-semibold text-white">
                        {{ auth()->user()->fullname ?? auth()->user()->username ?? 'Admin' }}
                    </div>

                    @if (app()->bound('impersonate') && app('impersonate')->isImpersonating())
                        <a href="{{ route('impersonate.leave') }}"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-sm font-bold text-white transition hover:bg-white/15">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                            Switch back to your account
                        </a>
                    @else
                        <button type="button"
                            onclick="event.preventDefault(); document.getElementById('tailwind-logout-form').submit();"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-rose-300/20 bg-rose-500/10 px-3 py-2 text-sm font-bold text-rose-100 transition hover:bg-rose-500/20">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h10" />
                            </svg>
                            Logout
                        </button>
                    @endif
                </div>
            </div>
        </aside>

        <div :class="sidebarExpanded ? 'v2-shell-expanded lg:pl-72' : 'v2-shell-collapsed lg:pl-20'" class="v2-app-shell min-h-screen min-w-0 overflow-x-hidden transition-all duration-300">
            <header class="v2-top-header sticky top-0 z-20 w-full min-w-0 overflow-x-hidden border-b border-erp-line bg-white/90 backdrop-blur-xl">
                <div class="flex h-16 min-w-0 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-erp-line bg-white text-slate-600 transition hover:border-erp hover:text-erp-dark lg:hidden"
                            aria-label="Open ERP menu">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-erp-mute">@yield('title', $title ?? 'Admin')</div>
                            <div class="truncate text-sm font-semibold text-erp-ink">Retail operations center</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        
                        @php
                            $avatarName = trim(auth()->user()->fullname ?? auth()->user()->username ?? 'User');
                            $avatarInitials = collect(preg_split('/\s+/', $avatarName))
                                ->filter()
                                ->take(3)
                                ->map(fn ($part) => substr($part, 0, 1))
                                ->implode('');
                            $avatarImage = session('image');
                        @endphp
                        <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-erp-panel text-sm font-bold text-white ring-1 ring-slate-200">
                            @if (!empty($avatarImage))
                                <img class="h-full w-full object-cover"
                                    src="{{ asset('storage/images/users/' . $avatarImage) }}"
                                    alt="{{ $avatarName }}">
                            @else
                                {{ strtoupper($avatarInitials ?: substr($avatarName, 0, 3)) }}
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <main class="v2-main-content w-full min-w-0 overflow-x-hidden py-6 {{ $tailwindFullWidthPage ? 'px-0' : 'px-4 sm:px-6 lg:px-8' }}">
                <div class="v2-page-heading mb-6 border border-erp-line bg-white px-5 py-5 shadow-sm {{ $tailwindFullWidthPage ? 'rounded-none border-x-0' : 'rounded-lg' }}">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">
                                <a href="{{ url('/dashboard') }}" class="transition hover:text-erp-dark">ERP Dashboard</a>
                                <span class="text-slate-300">/</span>
                                <span class="text-erp-dark">@yield('title', $title ?? 'Admin')</span>
                            </div>
                            <h1 class="mt-3 text-2xl font-bold tracking-tight text-erp-ink sm:text-3xl">
                                @hasSection('page_title')
                                    @yield('page_title')
                                @else
                                    {{ $title ?? 'Admin Dashboard' }}
                                @endif
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-erp-mute">
                                @yield('page_subtitle', 'Manage retail operations, orders, products, customers, and daily performance from one focused workspace.')
                            </p>
                        </div>

                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-sm">
                        <p class="font-bold">Please review the highlighted form fields.</p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>
        </div>
    </div>

    <form id="tailwind-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if ($tailwindLegacyAssets)
        @include('partials.js-libs')
    @endif
    @if ($tailwindOrdersAssets && !$tailwindLegacyAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/tether/dist/js/tether.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datepicker/js/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert/js/sweetalert.js') }}"></script>
        <script src="{{ asset('components/chart.js/dist/Chart.js') }}"></script>
        <script>
            window.bootstrap = window.bootstrap || {};
            if (!window.bootstrap.Modal && window.jQuery && jQuery.fn.modal) {
                window.bootstrap.Modal = function (element) {
                    this.element = element;
                };
                window.bootstrap.Modal.prototype.show = function () {
                    jQuery(this.element).modal('show');
                };
                window.bootstrap.Modal.prototype.hide = function () {
                    jQuery(this.element).modal('hide');
                };
                window.bootstrap.Modal.getInstance = function (element) {
                    return new window.bootstrap.Modal(element);
                };
                jQuery(document).on('click', '[data-bs-dismiss="modal"]', function () {
                    jQuery(this).closest('.modal').modal('hide');
                });
            }
        </script>
    @endif
    @if ($tailwindPurchaseAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/tether/dist/js/tether.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/notification/js/bootstrap-growl.min.js') }}"></script>
        <script src="{{ asset('js/notification.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/plugins/datepicker/js/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert/js/sweetalert.js') }}"></script>
    @endif
    @if ($tailwindDiscountAssets && !$tailwindLegacyAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/tether/dist/js/tether.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datepicker/js/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert/js/sweetalert.js') }}"></script>
    @endif
    @if ($tailwindSelect2Assets && !$tailwindLegacyAssets && !$tailwindPurchaseAssets && !$tailwindDashboardAssets && !$tailwindOrdersAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.jQuery || !jQuery.fn.select2) {
                    return;
                }

                jQuery('.billing-select2:not(.select2-hidden-accessible), .v2-select2:not(.select2-hidden-accessible)').each(function () {
                    var $select = jQuery(this);
                    var isV2Select = $select.hasClass('v2-select2');
                    $select.select2({
                        allowClear: !$select.prop('required') && $select.find('option[value=""]').length > 0,
                        dropdownCssClass: isV2Select ? 'v2-select2-dropdown' : 'billing-select2-dropdown',
                        placeholder: $select.data('placeholder') || $select.find('option[value=""]').first().text() || '',
                        width: '100%'
                    });
                });
            });
        </script>
    @endif
    @if ($tailwindPurchaseAssets || $tailwindDashboardAssets || $tailwindOrdersAssets || $tailwindDiscountAssets)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.documentElement.style.overflowY = 'auto';
                document.documentElement.style.height = 'auto';
                document.documentElement.style.minHeight = '100%';
                document.body.style.overflowY = 'auto';
                document.body.style.height = 'auto';
                document.body.style.minHeight = '100vh';
            });
        </script>
    @endif
    @stack('scripts')
    @yield('scriptcode_three')
    @livewireScripts
</body>

</html>

@extends('layouts.master-layout')

@section('title', 'Billing Summary')
@section('breadcrumtitle', 'Billing Summary')

@section('content')
<section class="panels-wells">
    <style>
        .billing-summary-shell {
            position: relative;
        }

        .billing-summary-loader {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(248, 251, 255, 0.78);
            backdrop-filter: blur(3px);
            z-index: 5;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .billing-summary-loader.is-active {
            opacity: 1;
            pointer-events: auto;
        }

        .billing-summary-loader-card {
            min-width: 220px;
            padding: 22px 26px;
            border-radius: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f1f7ff 100%);
            box-shadow: 0 18px 45px rgba(31, 57, 84, 0.18);
            text-align: center;
        }

        .billing-summary-spinner {
            width: 48px;
            height: 48px;
            margin: 0 auto 14px;
            border-radius: 50%;
            border: 4px solid rgba(76, 175, 80, 0.18);
            border-top-color: #4CAF50;
            animation: billing-spin 0.9s linear infinite;
        }

        .billing-summary-fade {
            transition: opacity 0.18s ease;
        }

        .billing-summary-fade.is-loading {
            opacity: 0.4;
        }

        @keyframes billing-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="card" style="margin-top: 20px; border: 0; overflow: hidden; box-shadow: 0 18px 40px rgba(32, 56, 85, 0.12);margin-top:70px;">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="card-header-text text-white m-b-5">Company Billing Summary</h5>
                    <p class="m-b-0" style="color: rgba(255,255,255,0.82);">Track invoice totals, collections, and pending balances company-wise.</p>
                </div>
                <a href="{{ route('billing.invoices.index') }}" class="btn btn-light btn-sm" style="color: #white; margin-left: auto; border: 0; font-weight: 600; padding: 10px 16px;">
                    <i class="icofont icofont-list" style="color:white;"></i><label class="f-w-600" style="color:white;">View All Invoices</label>
                </a>
            </div>
        </div>
        <div class="card-block" style="background: linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);">
            <form method="GET" action="{{ route('billing.summary') }}" id="billing-summary-filter-form" class="card m-b-20" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                <div class="card-block">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="f-w-600">Filter by Company</label>
                            <select name="company_id" id="billing-summary-company-filter" class="form-control">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->company_id }}" {{ (string) $selectedCompanyId === (string) $company->company_id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary">
                                <i class="icofont icofont-search"></i> Apply Filter
                            </button>
                            <a href="{{ route('billing.summary') }}" class="btn btn-outline-secondary" style="margin-left: 8px;">
                                <i class="icofont icofont-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="billing-summary-shell">
                <div id="billing-summary-loader" class="billing-summary-loader">
                    <div class="billing-summary-loader-card">
                        <div class="billing-summary-spinner"></div>
                        <div class="f-w-600 text-dark">Loading company summary</div>
                        <small class="text-muted">Refreshing totals and invoice details...</small>
                    </div>
                </div>

                <div id="billing-summary-content" class="billing-summary-fade">
                    @include('Admin.Billing.partials.summary-content', ['summary' => $summary])
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scriptcode')
<script>
    (function () {
        var form = document.getElementById('billing-summary-filter-form');
        var companyFilter = document.getElementById('billing-summary-company-filter');
        var content = document.getElementById('billing-summary-content');
        var loader = document.getElementById('billing-summary-loader');

        if (!form || !companyFilter || !content || !loader || !window.fetch) {
            return;
        }

        var currentController = null;

        function setLoading(isLoading) {
            loader.classList.toggle('is-active', isLoading);
            content.classList.toggle('is-loading', isLoading);
        }

        function updateSummary(url) {
            if (currentController) {
                currentController.abort();
            }

            currentController = new AbortController();
            setLoading(true);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: currentController.signal
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    return response.text();
                })
                .then(function (html) {
                    content.innerHTML = html;
                    window.history.replaceState({}, '', url);
                })
                .catch(function (error) {
                    if (error.name !== 'AbortError') {
                        window.location.href = url;
                    }
                })
                .finally(function () {
                    setLoading(false);
                });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var url = form.action + '?' + new URLSearchParams(new FormData(form)).toString();
            updateSummary(url);
        });

        companyFilter.addEventListener('change', function () {
            if (form.requestSubmit) {
                form.requestSubmit();
                return;
            }

            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        });
    })();
</script>
@endsection

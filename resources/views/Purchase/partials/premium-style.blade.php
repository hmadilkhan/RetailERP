<style>
    .purchase-premium-page {
        color: #17202c;
    }

    .purchase-premium-page .purchase-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        padding: 22px 24px;
        border-radius: 8px;
        background: linear-gradient(135deg, #123d2b 0%, #2f7d32 58%, #78b845 100%);
        box-shadow: 0 18px 42px rgba(18, 61, 43, .18);
        color: #fff;
    }

    .purchase-premium-page .purchase-kicker {
        display: block;
        margin-bottom: 6px;
        color: rgba(255, 255, 255, .75);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .purchase-premium-page .purchase-hero h2 {
        margin: 0;
        color: #fff;
        font-size: 27px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .purchase-premium-page .purchase-hero p {
        margin: 7px 0 0;
        color: rgba(255, 255, 255, .78);
        font-size: 13px;
        font-weight: 600;
    }

    .purchase-premium-page .purchase-hero-badge {
        min-width: 150px;
        padding: 12px 14px;
        border: 1px solid rgba(255, 255, 255, .26);
        border-radius: 8px;
        background: rgba(255, 255, 255, .13);
        text-align: right;
    }

    .purchase-premium-page .purchase-hero-badge span {
        display: block;
        color: rgba(255, 255, 255, .72);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .purchase-premium-page .purchase-hero-badge strong {
        display: block;
        margin-top: 4px;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
    }

    .purchase-premium-page .purchase-card {
        overflow: hidden;
        border: 1px solid #e6ebf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .08);
    }

    .purchase-premium-page .purchase-card > .card-header,
    .purchase-premium-page .card-block > .card-header {
        margin: 0 0 18px;
        padding: 16px 20px;
        border-bottom: 1px solid #eef2f6;
        background: #fbfcfd;
    }

    .purchase-premium-page .card-header-text {
        margin: 0;
        color: #111827;
        font-weight: 800;
        letter-spacing: 0;
    }

    .purchase-premium-page #poNumber {
        color: #2f7d32;
        font-weight: 800;
    }

    .purchase-premium-page .card-block {
        padding: 22px;
    }

    .purchase-premium-page .form-group label,
    .purchase-premium-page .form-control-label {
        color: #4b5563;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .purchase-premium-page .form-control {
        min-height: 39px;
        border: 1px solid #d9e1ea;
        border-radius: 6px;
        color: #17202c;
        box-shadow: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .purchase-premium-page textarea.form-control {
        min-height: 106px;
        resize: vertical;
    }

    .purchase-premium-page .form-control:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, .13);
    }

    .purchase-premium-page .select2-container {
        width: 100% !important;
    }

    .purchase-premium-page .select2-container .select2-selection--single,
    .purchase-premium-page .select2-container .select2-selection--multiple {
        min-height: 39px;
        border: 1px solid #d9e1ea;
        border-radius: 6px;
        background: #fff;
    }

    .purchase-premium-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 12px;
        padding-right: 32px;
        color: #17202c;
        line-height: 38px;
    }

    .purchase-premium-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 6px;
    }

    .purchase-premium-page .select2-container--default.select2-container--focus .select2-selection--single,
    .purchase-premium-page .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, .13);
    }

    .purchase-premium-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin-top: 6px;
        border: 0;
        border-radius: 6px;
        background: #edf8ee;
        color: #2f7d32;
        font-weight: 700;
    }

    .purchase-premium-page .alert {
        border: 1px solid #cfe7ff;
        border-radius: 8px;
        color: #245174;
    }

    .purchase-premium-page #per-item-tax-allow {
        width: 24px;
        min-height: 24px;
        margin-top: 6px;
        box-shadow: none;
    }

    .purchase-premium-page #btnSubmit {
        min-width: 42px;
        height: 39px;
        margin-top: 26px !important;
        border: 0;
        border-radius: 6px;
        background: #2f7d32;
        box-shadow: 0 10px 20px rgba(47, 125, 50, .22);
    }

    .purchase-premium-page .invoice-detail-table {
        overflow: hidden;
        border: 1px solid #e6ebf1;
        border-radius: 8px;
        background: #fff;
    }

    .purchase-premium-page .invoice-detail-table thead th {
        border-bottom: 1px solid #e6ebf1;
        background: #f6f8fb;
        color: #4b5563;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .purchase-premium-page .invoice-detail-table tbody td,
    .purchase-premium-page .invoice-detail-table tfoot td {
        vertical-align: middle;
        border-color: #eef2f6;
        color: #17202c;
        font-weight: 600;
    }

    .purchase-premium-page .invoice-detail-table tfoot td {
        background: #fbfcfd;
        font-weight: 800;
    }

    .purchase-premium-page .invoice-total {
        width: 100%;
        border: 1px solid #e6ebf1;
        border-radius: 8px;
        background: #fbfcfd;
    }

    .purchase-premium-page .invoice-total th,
    .purchase-premium-page .invoice-total td {
        padding: 11px 14px !important;
        border-color: #eef2f6 !important;
        color: #4b5563;
        font-weight: 800;
    }

    .purchase-premium-page .invoice-total td {
        text-align: right;
        color: #111827;
    }

    .purchase-premium-page .invoice-total .txt-info th,
    .purchase-premium-page .invoice-total .txt-info td,
    .purchase-premium-page .invoice-total .txt-info h5 {
        color: #2f7d32;
        font-weight: 900;
    }

    .purchase-premium-page #btnFinalSubmit,
    .purchase-premium-page #btnDraft,
    .purchase-premium-page .modal-footer .btn {
        border: 0;
        border-radius: 6px;
        padding: 10px 16px;
        font-weight: 800;
    }

    .purchase-premium-page #btnFinalSubmit {
        background: #2f7d32;
        box-shadow: 0 10px 22px rgba(47, 125, 50, .22);
    }

    .purchase-premium-page #btnDraft {
        background: #eef2f6;
        color: #344054;
    }

    @media (max-width: 767px) {
        .purchase-premium-page .purchase-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 20px;
        }

        .purchase-premium-page .purchase-hero-badge {
            width: 100%;
            text-align: left;
        }

        .purchase-premium-page .card-block {
            padding: 16px;
        }

        .purchase-premium-page #btnFinalSubmit,
        .purchase-premium-page #btnDraft {
            width: 100%;
            margin-right: 0 !important;
            float: none !important;
        }
    }
</style>

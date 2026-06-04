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
        border-radius: 0.5rem;
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
        border-radius: 0.5rem;
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
        border: 1px solid #d8e1ec;
        border-radius: 0.5rem;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .08);
    }

    .purchase-premium-page .purchase-card > .card-header,
    .purchase-premium-page .card-block > .card-header {
        margin: 0 0 18px;
        padding: 16px 20px;
        border-bottom: 1px solid #eef2f6;
        background: #f8fafc;
    }

    .purchase-premium-page .card-header-text {
        margin: 0;
        color: #0f172a;
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
        color: #475569;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .purchase-premium-page .form-control,
    .purchase-premium-page .select2-container .select2-selection--single,
    .purchase-premium-page .select2-container .select2-selection--multiple {
        min-height: 39px;
        border: 1px solid #d8e1ec;
        border-radius: 0.5rem;
        color: #17202c;
        box-shadow: none;
    }

    .purchase-premium-page .form-control:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, .13);
    }

    .purchase-premium-page .select2-container {
        width: 100% !important;
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

    .purchase-premium-page #per-item-tax-allow {
        width: 24px;
        min-height: 24px;
        margin-top: 6px;
        box-shadow: none;
    }

    .purchase-premium-page #btnSubmit,
    .purchase-premium-page #btnFinalSubmit {
        border: 0;
        border-radius: 0.5rem;
        background: #2f7d32;
        font-weight: 800;
        box-shadow: 0 10px 22px rgba(47, 125, 50, .22);
    }

    .purchase-premium-page #btnDraft {
        border: 0;
        border-radius: 0.5rem;
        background: #eef2f6;
        color: #334155;
        font-weight: 800;
    }

    .purchase-premium-page .invoice-detail-table,
    .purchase-premium-page .invoice-total {
        overflow: hidden;
        border: 1px solid #d8e1ec;
        border-radius: 0.5rem;
        background: #fff;
    }

    .purchase-premium-page .invoice-detail-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .purchase-premium-page .invoice-total th,
    .purchase-premium-page .invoice-total td {
        padding: 11px 14px !important;
        border-color: #eef2f6 !important;
        font-weight: 800;
    }

    .purchase-premium-page .invoice-total td {
        text-align: right;
        color: #0f172a;
    }

    @media (max-width: 767px) {
        .purchase-premium-page .purchase-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .purchase-premium-page .purchase-hero-badge {
            width: 100%;
            text-align: left;
        }
    }
</style>

@extends('layouts.app-shell')

@php
    $selectedProductIds = array_map('strval', (array) old('sap_product_ids', []));
@endphp

@section('title', 'Client Registration')
@section('page-title', 'Client Registration')
@section('page-subtitle', 'Register client companies and capture support onboarding details.')

@push('styles')
    <style>
        .client-registration-page {
            display: grid;
            gap: 18px;
        }

        .client-registration-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 390px;
            gap: 18px;
            align-items: start;
        }

        .client-form-stack,
        .analytics-pane {
            display: grid;
            gap: 16px;
        }

        .analytics-pane {
            position: sticky;
            top: 18px;
        }

        .client-banner,
        .client-panel,
        .analytics-panel,
        .client-bottom-note {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .client-banner {
            min-height: 44px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: color-mix(in srgb, var(--blue-soft) 70%, var(--panel));
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.45;
        }

        .client-banner svg,
        .client-bottom-note svg {
            width: 17px;
            height: 17px;
            flex: 0 0 auto;
        }

        .client-flash {
            min-height: 40px;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0 13px;
            border: 1px solid #aee9c9;
            border-radius: 8px;
            background: #effbf5;
            color: #067143;
            font-size: 12px;
            font-weight: 900;
        }

        .client-errors {
            margin: 0;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .client-form {
            display: grid;
            gap: 16px;
        }

        .client-panel {
            padding: 18px;
        }

        .client-panel-header,
        .analytics-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .client-section-title {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: var(--blue);
            font-size: 12px;
            font-weight: 900;
        }

        .client-section-title svg,
        .analytics-title svg {
            width: 17px;
            height: 17px;
        }

        .section-icon {
            width: 26px;
            height: 26px;
            display: inline-grid;
            place-items: center;
            border-radius: 7px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .section-icon.location {
            background: var(--red-soft);
            color: var(--red);
        }

        .section-icon.business {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .section-icon.support {
            background: #ffe2ee;
            color: #db2777;
        }

        .section-icon.terms {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .client-form-grid {
            display: grid;
            gap: 14px;
        }

        .client-form-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .client-form-grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .client-field {
            display: grid;
            gap: 7px;
            min-width: 0;
        }

        .client-field.span-two {
            grid-column: 1 / -1;
        }

        .client-label {
            color: var(--ink);
            font-size: 11px;
            font-weight: 900;
            line-height: 1.35;
        }

        .required {
            color: var(--red);
        }

        .client-input,
        .client-select,
        .client-textarea {
            width: 100%;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 700;
            outline: none;
        }

        .client-input,
        .client-select {
            height: 38px;
            padding: 0 11px;
        }

        .client-textarea {
            min-height: 92px;
            resize: vertical;
            padding: 11px;
            line-height: 1.5;
        }

        .client-input:focus,
        .client-select:focus,
        .client-textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .select-wrap {
            position: relative;
        }

        .client-select {
            appearance: none;
            padding-right: 34px;
        }

        .select-wrap svg {
            position: absolute;
            top: 50%;
            right: 11px;
            width: 14px;
            height: 14px;
            color: var(--muted);
            pointer-events: none;
            transform: translateY(-50%);
        }

        .client-select:disabled {
            color: color-mix(in srgb, var(--muted) 70%, var(--panel));
            background: color-mix(in srgb, var(--panel) 88%, var(--canvas));
        }

        .phone-control {
            display: grid;
            grid-template-columns: 100px minmax(0, 1fr);
            gap: 8px;
        }

        .address-status {
            min-height: 18px;
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 750;
        }

        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            line-height: 1.5;
        }

        .terms-row input {
            width: 15px;
            height: 15px;
            margin-top: 2px;
            flex: 0 0 auto;
        }

        .terms-row a {
            color: var(--blue);
            font-weight: 900;
            text-decoration: none;
        }

        .client-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .client-button {
            min-width: 156px;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .client-button svg {
            width: 15px;
            height: 15px;
        }

        .client-button-primary {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
        }

        .product-picker-row { display: flex; align-items: stretch; gap: 8px; }
        .product-picker-row .client-input { min-width: 0; flex: 1; height: auto; min-height: 38px; padding: 9px 11px; resize: none; }
        .product-picker-button { min-width: 108px; padding: 0 12px; border: 1px solid var(--blue); border-radius: 6px; background: var(--blue-soft); color: var(--blue); font-size: 11px; font-weight: 900; }
        .product-picker-help { color: var(--muted); font-size: 10px; font-weight: 700; }
        .product-modal { position: fixed; z-index: 1200; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(4, 15, 39, .68); }
        .product-modal.active { display: flex; }
        .product-modal-dialog { width: min(560px, 100%); overflow: hidden; border: 1px solid var(--line); border-radius: 12px; background: var(--panel); box-shadow: 0 24px 65px rgba(0, 0, 0, .28); }
        .product-modal-header, .product-modal-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; }
        .product-modal-header { border-bottom: 1px solid var(--line); }
        .product-modal-header h3 { margin: 0; color: var(--ink); font-size: 15px; }
        .product-modal-close { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); font-size: 20px; }
        .product-modal-body { max-height: 330px; display: grid; gap: 8px; padding: 16px 18px; overflow-y: auto; }
        .product-modal-option { display: flex; align-items: center; gap: 10px; padding: 11px 12px; border: 1px solid var(--line); border-radius: 8px; color: var(--ink); font-size: 12px; font-weight: 750; cursor: pointer; }
        .product-modal-option:hover { border-color: var(--blue); background: var(--blue-soft); }
        .product-modal-option input { width: 16px; height: 16px; accent-color: var(--blue); }
        .product-modal-actions { justify-content: flex-end; border-top: 1px solid var(--line); }

        .client-bottom-note {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 14px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 750;
        }

        .note-side {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .analytics-panel {
            padding: 18px;
        }

        .analytics-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            color: var(--ink);
            font-size: 18px;
            font-weight: 900;
            line-height: 1.2;
        }

        .analytics-filter {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 7px;
            color: var(--ink);
            font-size: 12px;
            font-weight: 850;
        }

        .analytics-filter svg {
            width: 13px;
            height: 13px;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .overview-tile {
            min-height: 130px;
            display: grid;
            align-content: center;
            gap: 10px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
        }

        .overview-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .overview-icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
            flex: 0 0 auto;
        }

        .overview-icon svg {
            width: 24px;
            height: 24px;
        }

        .overview-icon.green {
            background: var(--green-soft);
            color: var(--green);
        }

        .overview-icon.violet {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .overview-icon.amber {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .overview-value {
            margin: 0;
            color: var(--ink);
            font-size: 27px;
            font-weight: 900;
            line-height: 1;
        }

        .overview-label {
            margin: 0;
            color: var(--ink);
            font-size: 12px;
            font-weight: 850;
            line-height: 1.35;
        }

        .overview-trend {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--green);
            font-size: 11px;
            font-weight: 900;
        }

        .overview-trend svg {
            width: 12px;
            height: 12px;
        }

        .industry-body {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr);
            gap: 22px;
            align-items: center;
            margin-top: 8px;
        }

        .donut-chart {
            width: 150px;
            height: 150px;
            position: relative;
            border-radius: 999px;
            background: conic-gradient(var(--industry-gradient));
        }

        .donut-chart::after {
            content: "";
            position: absolute;
            inset: 39px;
            border-radius: inherit;
            background: var(--panel);
            box-shadow: inset 0 0 0 1px var(--line);
        }

        .industry-legend {
            display: grid;
            gap: 13px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .industry-item {
            display: grid;
            grid-template-columns: 12px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .industry-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: var(--dot-color);
        }

        .industry-percent {
            color: var(--ink);
            font-weight: 900;
        }

        .empty-distribution {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            line-height: 1.5;
        }

        .report-link {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            color: var(--blue);
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
        }

        .report-link span {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .report-link svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 1440px) {
            .client-registration-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .analytics-pane {
                position: static;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 980px) {
            .client-form-grid.two,
            .client-form-grid.three,
            .analytics-pane,
            .overview-grid,
            .industry-body {
                grid-template-columns: 1fr;
            }

            .donut-chart {
                margin-inline: auto;
            }
        }

        @media (max-width: 640px) {
            .client-panel,
            .analytics-panel {
                padding: 14px;
            }

            .client-bottom-note,
            .client-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .client-button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="client-icon-building" viewBox="0 0 24 24">
            <path d="M6 20V5h9v15M15 9h3v11M9 8h3M9 12h3M9 16h3M5 20h15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="client-icon-location" viewBox="0 0 24 24">
            <path d="M12 21s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="11" r="2.2" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="client-icon-briefcase" viewBox="0 0 24 24">
            <path d="M9 7V5h6v2M5 8h14v11H5V8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M5 12h14M10 12v2h4v-2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="client-icon-phone" viewBox="0 0 24 24">
            <path d="M8 5 5.8 7.2c-.8.8-.9 2-.4 3 1.6 3.4 5 6.8 8.4 8.4 1 .5 2.2.4 3-.4L19 16l-3.7-2-1.3 1.3c-1.8-.9-3.4-2.5-4.3-4.3L11 9.7 8 5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="client-icon-shield" viewBox="0 0 24 24">
            <path d="M12 4 5.5 6.5v5.3c0 4.1 2.7 7.1 6.5 8.2 3.8-1.1 6.5-4.1 6.5-8.2V6.5L12 4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="client-icon-users" viewBox="0 0 24 24">
            <path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M18 11.5a2.4 2.4 0 0 0 0-4.5M19 18c0-1.5-.7-2.7-1.9-3.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="client-icon-user-plus" viewBox="0 0 24 24">
            <path d="M15 19c0-2.4-2-4.3-4.5-4.3S6 16.6 6 19M10.5 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM18 8v6M15 11h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="client-icon-gauge" viewBox="0 0 24 24">
            <path d="M5 17a7 7 0 1 1 14 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="m12 17 4-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="client-icon-trend" viewBox="0 0 24 24">
            <path d="m5 15 4-4 3 3 6-7M15 7h3v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
    </svg>

    <div class="client-registration-page" data-client-registration data-psgc-base-url="{{ $psgcBaseUrl }}" data-country-code-api-url="{{ $countryCodeApiUrl }}" data-selected-country-code="{{ old('contact_country_code', '+63') }}">
        <div class="client-registration-layout">
            <div class="client-form-stack">
                <div class="client-banner">
                    <svg><use href="#icon-info"></use></svg>
                    <span>Register your company to unlock faster support, track requests efficiently, and receive tailored assistance for your SAP Business One environment.</span>
                </div>

                <x-status-prompt />

                @if ($errors->any())
                    <ul class="client-errors">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form class="client-form" method="POST" action="{{ route('clients.store') }}">
                    @csrf

                    <section class="client-panel" aria-labelledby="company-information-title">
                        <div class="client-panel-header">
                            <h2 class="client-section-title" id="company-information-title">
                                <span class="section-icon"><svg><use href="#client-icon-building"></use></svg></span>
                                <span>Company Information</span>
                            </h2>
                        </div>

                        <div class="client-form-grid two">
                            <label class="client-field">
                                <span class="client-label">Company Name <span class="required">*</span></span>
                                <input class="client-input" name="company_name" type="text" value="{{ old('company_name') }}" placeholder="Enter company name" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Contact Person <span class="required">*</span></span>
                                <input class="client-input" name="contact_person" type="text" value="{{ old('contact_person') }}" placeholder="Enter full name" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Designation <span class="required">*</span></span>
                                <input class="client-input" name="designation" type="text" value="{{ old('designation') }}" placeholder="Enter designation" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Email Address <span class="required">*</span></span>
                                <input class="client-input" name="email_address" type="email" value="{{ old('email_address') }}" placeholder="Enter official email address" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Contact Number <span class="required">*</span></span>
                                <span class="phone-control">
                                    <span class="select-wrap">
                                        <select class="client-select" name="contact_country_code" aria-label="Country calling code" required>
                                            <option value="+63" selected>PH +63</option>
                                        </select>
                                        <svg><use href="#icon-chevron-down"></use></svg>
                                    </span>
                                    <input class="client-input" name="contact_number" type="text" value="{{ old('contact_number') }}" placeholder="Enter contact number" maxlength="40" required>
                                </span>
                            </label>
                        </div>
                    </section>

                    <section class="client-panel" aria-labelledby="address-details-title">
                        <div class="client-panel-header">
                            <h2 class="client-section-title" id="address-details-title">
                                <span class="section-icon location"><svg><use href="#client-icon-location"></use></svg></span>
                                <span>Address Details</span>
                            </h2>
                        </div>

                        <input type="hidden" id="region_name" name="region_name" value="{{ old('region_name') }}">
                        <input type="hidden" id="province_name" name="province_name" value="{{ old('province_name') }}">
                        <input type="hidden" id="city_municipality_name" name="city_municipality_name" value="{{ old('city_municipality_name') }}">
                        <input type="hidden" id="barangay_name" name="barangay_name" value="{{ old('barangay_name') }}">

                        <div class="client-form-grid three">
                            <label class="client-field">
                                <span class="client-label">Region <span class="required">*</span></span>
                                <span class="select-wrap">
                                    <select class="client-select" id="region_code" name="region_code" data-selected="{{ old('region_code') }}" required>
                                        <option value="">Loading regions...</option>
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Province</span>
                                <span class="select-wrap">
                                    <select class="client-select" id="province_code" name="province_code" data-selected="{{ old('province_code') }}" disabled>
                                        <option value="">Select province</option>
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <label class="client-field">
                                <span class="client-label">City/Municipality <span class="required">*</span></span>
                                <span class="select-wrap">
                                    <select class="client-select" id="city_municipality_code" name="city_municipality_code" data-selected="{{ old('city_municipality_code') }}" disabled required>
                                        <option value="">Select city/municipality</option>
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>
                        </div>

                        <div class="client-form-grid two" style="margin-top:14px">
                            <label class="client-field">
                                <span class="client-label">Barangay <span class="required">*</span></span>
                                <span class="select-wrap">
                                    <select class="client-select" id="barangay_code" name="barangay_code" data-selected="{{ old('barangay_code') }}" disabled required>
                                        <option value="">Select barangay</option>
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Building No/House/Block/Lot No/Subdivision/Village <span class="required">*</span></span>
                                <input class="client-input" name="building_details" type="text" value="{{ old('building_details') }}" placeholder="Enter building details" required>
                            </label>
                        </div>

                        <p class="address-status" id="address-status" aria-live="polite"></p>
                    </section>

                    <section class="client-panel" aria-labelledby="business-details-title">
                        <div class="client-panel-header">
                            <h2 class="client-section-title" id="business-details-title">
                                <span class="section-icon business"><svg><use href="#client-icon-briefcase"></use></svg></span>
                                <span>Business Details</span>
                            </h2>
                        </div>

                        <div class="client-form-grid two">
                            <label class="client-field">
                                <span class="client-label">Industry / Business Type <span class="required">*</span></span>
                                <span class="select-wrap">
                                    <select class="client-select" name="industry_business_type_id" required>
                                        <option value="">Select industry / business type</option>
                                        @foreach ($industries as $industry)
                                            <option value="{{ $industry->id }}" @selected((string) old('industry_business_type_id') === (string) $industry->id)>{{ $industry->industry }}</option>
                                        @endforeach
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <div class="client-field">
                                <span class="client-label">Product Used <span class="required">*</span></span>
                                <div class="product-picker-row">
                                    <textarea class="client-input" id="selected-products-display" rows="2" placeholder="Select one or more products" readonly></textarea>
                                    <button class="product-picker-button" id="open-product-modal" type="button">Select Products</button>
                                </div>
                                <div id="selected-products-inputs">
                                    @foreach ($selectedProductIds as $selectedProductId)
                                        <input type="hidden" name="sap_product_ids[]" value="{{ $selectedProductId }}">
                                    @endforeach
                                </div>
                                <span class="product-picker-help">Choose one or more products from the popup window.</span>
                            </div>

                            <label class="client-field">
                                <span class="client-label">Version Number format: (10.00.130) <span class="required">*</span></span>
                                <input class="client-input" name="version_number" type="text" value="{{ old('version_number') }}" placeholder="10.00.130" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Patch (PL 01) / Package Name (FP 2008) <span class="required">*</span></span>
                                <input class="client-input" name="patch_or_fp" type="text" value="{{ old('patch_or_fp') }}" placeholder="PL 01 or FP 2008" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Database Version (MSSQL 2019) <span class="required">*</span></span>
                                <input class="client-input" name="db_version" type="text" value="{{ old('db_version') }}" placeholder="MSSQL 2019" required>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Company Size / Number of Users <span class="required">*</span></span>
                                <span class="select-wrap">
                                    <select class="client-select" name="company_size" required>
                                        <option value="">Select number of users</option>
                                        @foreach ($companySizes as $value => $label)
                                            <option value="{{ $value }}" @selected(old('company_size') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>
                        </div>
                    </section>

                    <section class="client-panel" aria-labelledby="support-preferences-title">
                        <div class="client-panel-header">
                            <h2 class="client-section-title" id="support-preferences-title">
                                <span class="section-icon support"><svg><use href="#client-icon-phone"></use></svg></span>
                                <span>Support Preferences</span>
                            </h2>
                        </div>

                        <div class="client-form-grid two">
                            <label class="client-field">
                                <span class="client-label">Account Manager</span>
                                <span class="select-wrap">
                                    <select class="client-select" name="account_manager_id">
                                        <option value="">Select account manager</option>
                                        @foreach ($accountManagers as $accountManager)
                                            <option value="{{ $accountManager->id }}" @selected((string) old('account_manager_id') === (string) $accountManager->id)>{{ $accountManager->account_manager }}</option>
                                        @endforeach
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <label class="client-field">
                                <span class="client-label">Assign FC</span>
                                <span class="select-wrap">
                                    <select class="client-select" name="assign_fc_id">
                                        <option value="">Select FC</option>
                                        @foreach($assignFcs as $fc)
                                            <option value="{{ $fc->id }}" @selected((string) old('assign_fc_id') === (string) $fc->id)>{{ $fc->assign_fc }}</option>
                                        @endforeach
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <label class="client-field span-two">
                                <span class="client-label">Preferred Support Contact Method <span class="required">*</span></span>
                                <span class="select-wrap">
                                    <select class="client-select" name="preferred_support_method" required>
                                        <option value="">Select preferred support contact method</option>
                                        @foreach ($supportMethods as $value => $label)
                                            <option value="{{ $value }}" @selected(old('preferred_support_method') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <svg><use href="#icon-chevron-down"></use></svg>
                                </span>
                            </label>

                            <label class="client-field span-two">
                                <span class="client-label">Optional Notes or Additional Information</span>
                                <textarea class="client-textarea" name="additional_notes" placeholder="Additional details about your company (optional)">{{ old('additional_notes') }}</textarea>
                            </label>
                        </div>
                    </section>

                    <section class="client-panel" aria-labelledby="terms-title">
                        <div class="client-panel-header">
                            <h2 class="client-section-title" id="terms-title">
                                <span class="section-icon terms"><svg><use href="#client-icon-shield"></use></svg></span>
                                <span>Terms and Conditions</span>
                            </h2>
                        </div>

                        <label class="terms-row">
                            <input name="accepted_terms" type="checkbox" value="1" @checked(old('accepted_terms')) required>
                            <span>I agree to the <a href="#">Terms of Service</a> and acknowledge the <a href="#">Privacy Policy</a>. I consent to the collection and processing of my company's data for the purpose of providing support services.</span>
                        </label>
                    </section>

                    <div class="product-modal" id="product-selection-modal" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">
                        <div class="product-modal-dialog">
                            <div class="product-modal-header">
                                <h3 id="product-modal-title">Select Product Used</h3>
                                <button class="product-modal-close" id="close-product-modal" type="button" aria-label="Close">&times;</button>
                            </div>
                            <div class="product-modal-body">
                                @foreach ($sapProducts as $sapProduct)
                                    <label class="product-modal-option">
                                        <input type="checkbox" value="{{ $sapProduct->id }}" data-product-name="{{ $sapProduct->sap_product }}" @checked(in_array((string) $sapProduct->id, $selectedProductIds, true))>
                                        <span>{{ $sapProduct->sap_product }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="product-modal-actions">
                                <button class="client-button" id="cancel-product-selection" type="button">Cancel</button>
                                <button class="client-button client-button-primary" id="apply-product-selection" type="button">Apply Selection</button>
                            </div>
                        </div>
                    </div>

                    <div class="client-actions">
                        <button class="client-button client-button-primary" type="submit">
                            <svg><use href="#icon-check-circle"></use></svg>
                            <span>Register Client</span>
                        </button>
                        <button class="client-button" type="reset">
                            <svg><use href="#icon-clock"></use></svg>
                            <span>Clear Form</span>
                        </button>
                    </div>
                </form>

                <div class="client-bottom-note">
                    <span class="note-side">
                        <svg><use href="#client-icon-shield"></use></svg>
                        <span>Your information is secure with us. We respect your privacy and only use your data to provide better support.</span>
                    </span>
                    <span class="note-side">
                        <svg><use href="#icon-calendar"></use></svg>
                        <span>Support Hours: Monday - Friday, 8:30 AM - 6:00 PM (Excluding Holidays)</span>
                    </span>
                </div>
            </div>

            <aside class="analytics-pane" aria-label="Client registration analytics">
                <section class="analytics-panel">
                    <div class="analytics-heading">
                        <h2 class="analytics-title"><svg><use href="#client-icon-users"></use></svg><span>Registration Overview</span></h2>
                        <span class="analytics-filter">This Month <svg><use href="#icon-chevron-down"></use></svg></span>
                    </div>

                    <div class="overview-grid">
                        @foreach ($analytics['overview'] as $item)
                            <article class="overview-tile">
                                <div class="overview-main">
                                    <span class="overview-icon {{ $item['tone'] !== 'blue' ? $item['tone'] : '' }}">
                                        <svg><use href="#{{ $item['icon'] }}"></use></svg>
                                    </span>
                                    <p class="overview-value">{{ $item['value'] }}</p>
                                </div>
                                <p class="overview-label">{{ $item['label'] }}</p>
                                <span class="overview-trend"><svg><use href="#client-icon-trend"></use></svg>{{ $item['trend'] }}</span>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="analytics-panel">
                    <div class="analytics-heading">
                        <h2 class="analytics-title">Industry Distribution</h2>
                        <span class="analytics-filter">This Month <svg><use href="#icon-chevron-down"></use></svg></span>
                    </div>

                    <div class="industry-body">
                        <div class="donut-chart" style="--industry-gradient: {{ $analytics['industry']['gradient'] }}"></div>

                        @if ($analytics['industry']['segments'] === [])
                            <p class="empty-distribution">Industry distribution will appear once client registrations are saved.</p>
                        @else
                            <ul class="industry-legend">
                                @foreach ($analytics['industry']['segments'] as $segment)
                                    <li class="industry-item" style="--dot-color: {{ $segment['color'] }}">
                                        <span class="industry-dot"></span>
                                        <span>{{ $segment['label'] }}</span>
                                        <span class="industry-percent">{{ $segment['percent'] }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <a class="report-link" href="#">
                        <span><svg><use href="#icon-chart"></use></svg>View detailed report</span>
                        <svg><use href="#icon-arrow-right"></use></svg>
                    </a>
                </section>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const root = document.querySelector('[data-client-registration]');

            if (!root) {
                return;
            }

            const baseUrl = root.dataset.psgcBaseUrl.replace(/\/+$/, '');
            const countryCodeApiUrl = root.dataset.countryCodeApiUrl;
            const selectedCountryCode = root.dataset.selectedCountryCode || '+63';
            const countryCodeSelect = root.querySelector('select[name="contact_country_code"]');
            const status = document.getElementById('address-status');
            const form = root.querySelector('.client-form');
            const productModal = document.getElementById('product-selection-modal');
            const productDisplay = document.getElementById('selected-products-display');
            const productInputs = document.getElementById('selected-products-inputs');
            const productCheckboxes = Array.from(productModal.querySelectorAll('input[type="checkbox"]'));

            const loadCountryCodes = async () => {
                if (!countryCodeApiUrl || !countryCodeSelect) {
                    return;
                }

                try {
                    const response = await fetch(countryCodeApiUrl, { headers: { Accept: 'application/json' } });

                    if (!response.ok) {
                        throw new Error(`Country API returned ${response.status}`);
                    }

                    const countries = await response.json();
                    const options = countries.map(country => ({
                        iso: country.iso,
                        dialCode: country.dial_code,
                        name: country.name,
                    })).sort((a, b) => {
                        if (a.iso === 'PH') return -1;
                        if (b.iso === 'PH') return 1;
                        return a.name.localeCompare(b.name);
                    });

                    countryCodeSelect.innerHTML = '';
                    options.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.dialCode;
                        option.textContent = `${item.iso} ${item.dialCode}`;
                        option.selected = item.iso === 'PH'
                            ? selectedCountryCode === '+63'
                            : item.dialCode === selectedCountryCode && !countryCodeSelect.querySelector(`option[value="${CSS.escape(selectedCountryCode)}"]:checked`);
                        countryCodeSelect.appendChild(option);
                    });

                    if (!countryCodeSelect.value) {
                        countryCodeSelect.value = '+63';
                    }
                } catch (error) {
                    countryCodeSelect.value = '+63';
                }
            };

            loadCountryCodes();

            const selectedProductIds = () => Array.from(productInputs.querySelectorAll('input[name="sap_product_ids[]"]')).map(input => input.value);
            const updateProductDisplay = () => {
                const ids = selectedProductIds();
                productDisplay.value = productCheckboxes
                    .filter(checkbox => ids.includes(checkbox.value))
                    .map(checkbox => checkbox.dataset.productName)
                    .join(', ');
            };
            const closeProductModal = () => productModal.classList.remove('active');
            const openProductModal = () => {
                const ids = selectedProductIds();
                productCheckboxes.forEach(checkbox => checkbox.checked = ids.includes(checkbox.value));
                productModal.classList.add('active');
            };

            document.getElementById('open-product-modal').addEventListener('click', openProductModal);
            document.getElementById('close-product-modal').addEventListener('click', closeProductModal);
            document.getElementById('cancel-product-selection').addEventListener('click', closeProductModal);
            document.getElementById('apply-product-selection').addEventListener('click', () => {
                productInputs.innerHTML = '';
                productCheckboxes.filter(checkbox => checkbox.checked).forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = 'sap_product_ids[]'; input.value = checkbox.value;
                    productInputs.appendChild(input);
                });
                updateProductDisplay();
                closeProductModal();
            });
            productModal.addEventListener('click', event => { if (event.target === productModal) closeProductModal(); });
            document.addEventListener('keydown', event => { if (event.key === 'Escape') closeProductModal(); });
            updateProductDisplay();

            const fields = {
                region: document.getElementById('region_code'),
                province: document.getElementById('province_code'),
                city: document.getElementById('city_municipality_code'),
                barangay: document.getElementById('barangay_code'),
                regionName: document.getElementById('region_name'),
                provinceName: document.getElementById('province_name'),
                cityName: document.getElementById('city_municipality_name'),
                barangayName: document.getElementById('barangay_name'),
            };

            const initial = {
                region: fields.region.dataset.selected || '',
                province: fields.province.dataset.selected || '',
                city: fields.city.dataset.selected || '',
                barangay: fields.barangay.dataset.selected || '',
            };

            const romanRegions = {
                I: '1',
                II: '2',
                III: '3',
                'IV-A': '4A',
                V: '5',
                VI: '6',
                VII: '7',
                VIII: '8',
                IX: '9',
                X: '10',
                XI: '11',
                XII: '12',
                XIII: '13',
            };

            const setStatus = (message) => {
                if (status) {
                    status.textContent = message;
                }
            };

            const setLoading = (select, message) => {
                select.disabled = true;
                select.innerHTML = `<option value="">${message}</option>`;
            };

            const resetSelect = (select, hidden, message) => {
                select.disabled = true;
                select.innerHTML = `<option value="">${message}</option>`;

                if (hidden) {
                    hidden.value = '';
                }
            };

            const fetchPsgc = async (path) => {
                const response = await fetch(`${baseUrl}${path}`);

                if (!response.ok) {
                    throw new Error(`PSGC request failed: ${response.status}`);
                }

                return response.json();
            };

            const regionLabel = (region) => {
                const rawRegion = String(region.regionName || '').replace(/^Region\s+/i, '').trim();
                const converted = romanRegions[rawRegion] || '';

                if (converted) {
                    return `Region ${converted} - ${region.name}`;
                }

                if (region.regionName && region.regionName !== region.name) {
                    return `${region.regionName} - ${region.name}`;
                }

                return region.name;
            };

            const syncHidden = (select, hidden) => {
                if (!hidden) {
                    return;
                }

                const option = select.selectedOptions[0];
                hidden.value = option && select.value ? (option.dataset.name || option.textContent.trim()) : '';
            };

            const populateSelect = (select, hidden, items, placeholder, selectedValue, labelFormatter) => {
                select.innerHTML = `<option value="">${placeholder}</option>`;

                items.forEach((item) => {
                    const label = labelFormatter(item);
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.textContent = label;
                    option.dataset.name = label;
                    select.appendChild(option);
                });

                select.disabled = false;

                if (selectedValue && items.some((item) => item.code === selectedValue)) {
                    select.value = selectedValue;
                }

                syncHidden(select, hidden);
            };

            const loadBarangays = async (cityCode, selectedBarangay = '') => {
                resetSelect(fields.barangay, fields.barangayName, 'Select barangay');

                if (!cityCode) {
                    return;
                }

                setLoading(fields.barangay, 'Loading barangays...');

                try {
                    const barangays = await fetchPsgc(`/cities-municipalities/${cityCode}/barangays/`);
                    populateSelect(fields.barangay, fields.barangayName, barangays, 'Select barangay', selectedBarangay, (item) => item.name);
                    setStatus('');
                } catch (error) {
                    resetSelect(fields.barangay, fields.barangayName, 'Unable to load barangays');
                    setStatus('Unable to load barangays. Please try again.');
                }
            };

            const loadCities = async (path, selectedCity = '', selectedBarangay = '') => {
                resetSelect(fields.city, fields.cityName, 'Select city/municipality');
                resetSelect(fields.barangay, fields.barangayName, 'Select barangay');

                setLoading(fields.city, 'Loading cities/municipalities...');

                try {
                    const cities = await fetchPsgc(path);
                    populateSelect(fields.city, fields.cityName, cities, 'Select city/municipality', selectedCity, (item) => item.name);

                    if (fields.city.value) {
                        await loadBarangays(fields.city.value, selectedBarangay);
                    } else {
                        setStatus('');
                    }
                } catch (error) {
                    resetSelect(fields.city, fields.cityName, 'Unable to load cities/municipalities');
                    setStatus('Unable to load cities and municipalities. Please try again.');
                }
            };

            const loadProvinces = async (regionCode, selectedProvince = '', selectedCity = '', selectedBarangay = '') => {
                resetSelect(fields.province, fields.provinceName, 'Select province');
                resetSelect(fields.city, fields.cityName, 'Select city/municipality');
                resetSelect(fields.barangay, fields.barangayName, 'Select barangay');

                if (!regionCode) {
                    return;
                }

                setLoading(fields.province, 'Loading provinces...');

                try {
                    const provinces = await fetchPsgc(`/regions/${regionCode}/provinces/`);

                    if (provinces.length === 0) {
                        fields.province.innerHTML = '<option value="">Not applicable</option>';
                        fields.province.disabled = true;
                        fields.provinceName.value = '';
                        await loadCities(`/regions/${regionCode}/cities-municipalities/`, selectedCity, selectedBarangay);
                        return;
                    }

                    populateSelect(fields.province, fields.provinceName, provinces, 'Select province', selectedProvince, (item) => item.name);

                    if (fields.province.value) {
                        await loadCities(`/provinces/${fields.province.value}/cities-municipalities/`, selectedCity, selectedBarangay);
                    } else {
                        setStatus('');
                    }
                } catch (error) {
                    resetSelect(fields.province, fields.provinceName, 'Unable to load provinces');
                    setStatus('Unable to load provinces. Please try again.');
                }
            };

            const loadRegions = async () => {
                setLoading(fields.region, 'Loading regions...');

                try {
                    const regions = await fetchPsgc('/regions/');
                    populateSelect(fields.region, fields.regionName, regions, 'Select region', initial.region, regionLabel);

                    if (fields.region.value) {
                        await loadProvinces(fields.region.value, initial.province, initial.city, initial.barangay);
                    } else {
                        setStatus('');
                    }
                } catch (error) {
                    resetSelect(fields.region, fields.regionName, 'Unable to load regions');
                    setStatus('Unable to load regions from PSGC. Please refresh the page.');
                }
            };

            fields.region.addEventListener('change', async () => {
                syncHidden(fields.region, fields.regionName);
                await loadProvinces(fields.region.value);
            });

            fields.province.addEventListener('change', async () => {
                syncHidden(fields.province, fields.provinceName);
                await loadCities(`/provinces/${fields.province.value}/cities-municipalities/`);
            });

            fields.city.addEventListener('change', async () => {
                syncHidden(fields.city, fields.cityName);
                await loadBarangays(fields.city.value);
            });

            fields.barangay.addEventListener('change', () => {
                syncHidden(fields.barangay, fields.barangayName);
            });

            form?.addEventListener('reset', () => {
                window.setTimeout(() => {
                    initial.region = '';
                    initial.province = '';
                    initial.city = '';
                    initial.barangay = '';
                    fields.regionName.value = '';
                    fields.provinceName.value = '';
                    fields.cityName.value = '';
                    fields.barangayName.value = '';
                    productInputs.innerHTML = '';
                    productCheckboxes.forEach(checkbox => checkbox.checked = false);
                    updateProductDisplay();
                    closeProductModal();
                    loadRegions();
                }, 0);
            });

            loadRegions();
        })();
    </script>
@endpush

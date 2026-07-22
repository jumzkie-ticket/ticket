@extends('layouts.app-shell')

@php
    $orderCode = 'SO-'.($serviceOrder->created_at?->format('Y') ?? now()->format('Y')).'-'.str_pad((string) $serviceOrder->id, 4, '0', STR_PAD_LEFT);
    $remainingManDays = max(0, (int) $serviceOrder->man_days + (int) $serviceOrder->unused_man_days - (int) $serviceOrder->used_man_days);
@endphp

@section('title', 'View Service Order')
@section('page-title', 'View Service Order')
@section('page-subtitle', $orderCode.' / Complete service order information')

@push('styles')
    <style>
        .sov-page { display: grid; gap: 16px; }
        .sov-actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
        .sov-action-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .sov-button { min-height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 14px; border: 1px solid var(--line); border-radius: 8px; background: var(--panel); color: var(--ink); font-size: 12px; font-weight: 800; text-decoration: none; }
        .sov-button.primary { border-color: var(--primary); background: var(--primary); color: #ffffff; }
        .sov-button svg { width: 15px; height: 15px; }
        .sov-hero { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 20px; border: 1px solid var(--line); border-radius: 12px; background: var(--panel); box-shadow: var(--shadow-sm); }
        .sov-code { margin: 0; color: var(--ink); font-size: 20px; font-weight: 900; }
        .sov-client { margin: 6px 0 0; color: var(--muted); font-size: 13px; font-weight: 650; }
        .sov-status { min-height: 28px; display: inline-flex; align-items: center; padding: 0 11px; border-radius: 999px; background: var(--green-soft); color: var(--green); font-size: 11px; font-weight: 850; }
        .sov-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .sov-panel { min-width: 0; overflow: hidden; border: 1px solid var(--line); border-radius: 12px; background: var(--panel); box-shadow: var(--shadow-sm); }
        .sov-panel.full { grid-column: 1 / -1; }
        .sov-panel-title { display: flex; align-items: center; gap: 8px; margin: 0; padding: 14px 16px; border-bottom: 1px solid var(--line); background: var(--panel-subtle); color: var(--ink); font-size: 13px; font-weight: 850; }
        .sov-panel-title svg { width: 16px; height: 16px; color: var(--primary); }
        .sov-details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); margin: 0; }
        .sov-detail { min-width: 0; display: grid; gap: 5px; padding: 14px 16px; border-right: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .sov-detail:nth-child(2n) { border-right: 0; }
        .sov-detail.wide { grid-column: 1 / -1; border-right: 0; }
        .sov-detail dt { color: var(--muted); font-size: 10px; font-weight: 800; letter-spacing: .35px; text-transform: uppercase; }
        .sov-detail dd { margin: 0; color: var(--ink); font-size: 13px; font-weight: 700; line-height: 1.5; overflow-wrap: anywhere; }
        .sov-detail dd a { color: #ffffff; text-decoration: underline; text-underline-offset: 2px; }
        .sov-list { display: flex; gap: 6px; flex-wrap: wrap; }
        .sov-chip { display: inline-flex; padding: 5px 8px; border-radius: 6px; background: var(--primary-soft); color: #ffffff; font-size: 11px; font-weight: 750; }
        .sov-man-days { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .sov-stat { display: grid; gap: 5px; padding: 18px; border-right: 1px solid var(--line); text-align: center; }
        .sov-stat:last-child { border-right: 0; }
        .sov-stat-label { color: var(--muted); font-size: 11px; font-weight: 750; }
        .sov-stat-value { color: var(--green); font-size: 22px; font-weight: 900; }
        @media (max-width: 760px) {
            .sov-grid, .sov-details, .sov-man-days { grid-template-columns: 1fr; }
            .sov-panel.full, .sov-detail.wide { grid-column: auto; }
            .sov-detail, .sov-detail:nth-child(2n), .sov-stat { border-right: 0; }
            .sov-stat { border-bottom: 1px solid var(--line); }
            .sov-stat:last-child { border-bottom: 0; }
            .sov-hero { align-items: flex-start; flex-direction: column; }
        }
    </style>
@endpush

@section('content')
    <div class="sov-page">
        <div class="sov-actions">
            <div class="sov-action-group">
                <a class="sov-button primary" href="{{ route('service-order-details.edit', $serviceOrder) }}">
                    <svg aria-hidden="true"><use href="#icon-pencil"></use></svg>
                    <span>Edit Service Order</span>
                </a>
            </div>
        </div>

        <section class="sov-hero" aria-labelledby="serviceOrderCode">
            <div>
                <h2 class="sov-code" id="serviceOrderCode">{{ $orderCode }}</h2>
                <p class="sov-client">{{ $serviceOrder->client?->company_name ?? 'Client not set' }}</p>
            </div>
            <span class="sov-status">{{ $remainingManDays === 0 && $serviceOrder->man_days > 0 ? 'Completed' : 'Active' }}</span>
        </section>

        <div class="sov-grid">
            <section class="sov-panel" aria-labelledby="sovClientTitle">
                <h2 class="sov-panel-title" id="sovClientTitle"><svg><use href="#icon-client"></use></svg><span>Client Information</span></h2>
                <dl class="sov-details">
                    <div class="sov-detail"><dt>Client</dt><dd>{{ $serviceOrder->client?->company_name ?? 'Not set' }}</dd></div>
                    <div class="sov-detail"><dt>Industry / Business Type</dt><dd>{{ $serviceOrder->industryBusinessType?->industry ?? 'Not set' }}</dd></div>
                    <div class="sov-detail"><dt>Software Version</dt><dd>{{ $serviceOrder->software_version ?: 'Not set' }}</dd></div>
                    <div class="sov-detail"><dt>Patch or FP</dt><dd>{{ $serviceOrder->patch_or_fp ?: 'Not set' }}</dd></div>
                    <div class="sov-detail wide">
                        <dt>Products Used</dt>
                        <dd class="sov-list">
                            @forelse ($serviceOrder->sapProducts as $product)
                                <span class="sov-chip">{{ $product->sap_product }}</span>
                            @empty
                                <span>Not set</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="sov-panel" aria-labelledby="sovCoverageTitle">
                <h2 class="sov-panel-title" id="sovCoverageTitle"><svg><use href="#icon-doc"></use></svg><span>Product & Service</span></h2>
                <dl class="sov-details">
                    <div class="sov-detail"><dt>Support Start Date</dt><dd>{{ $serviceOrder->support_start_date?->format('m/d/Y') ?? 'Not set' }}</dd></div>
                    <div class="sov-detail"><dt>Support End Date</dt><dd>{{ $serviceOrder->support_end_date?->format('m/d/Y') ?? 'Not set' }}</dd></div>
                    <div class="sov-detail"><dt>CAS Accredited</dt><dd>{{ $serviceOrder->cas_accredited ? 'Yes' : 'No' }}</dd></div>
                    <div class="sov-detail"><dt>Created</dt><dd>{{ $serviceOrder->created_at?->format('m/d/Y h:i A') ?? 'Not set' }}</dd></div>
                    <div class="sov-detail wide">
                        <dt>Packages</dt>
                        <dd class="sov-list">
                            @forelse ($serviceOrder->packages as $package)
                                <span class="sov-chip">{{ $package->package }}</span>
                            @empty
                                <span>Not set</span>
                            @endforelse
                        </dd>
                    </div>
                    <div class="sov-detail wide"><dt>Support Inclusion</dt><dd>{{ $serviceOrder->support_inclusion ?: 'Not set' }}</dd></div>
                </dl>
            </section>

            <section class="sov-panel full" aria-labelledby="sovManDaysTitle">
                <h2 class="sov-panel-title" id="sovManDaysTitle"><svg><use href="#icon-calendar"></use></svg><span>Man-days</span></h2>
                <div class="sov-man-days">
                    <div class="sov-stat"><span class="sov-stat-label">Entitled Man-days</span><strong class="sov-stat-value">{{ number_format((float) $serviceOrder->man_days, 2) }}</strong></div>
                    <div class="sov-stat"><span class="sov-stat-label">Un-Used Man-days (Prev.Yrs)</span><strong class="sov-stat-value">{{ number_format((float) $serviceOrder->unused_man_days, 2) }}</strong></div>
                    <div class="sov-stat"><span class="sov-stat-label">Used Man-days</span><strong class="sov-stat-value">{{ number_format((float) $serviceOrder->used_man_days, 2) }}</strong></div>
                    <div class="sov-stat"><span class="sov-stat-label">Remaining Man-days</span><strong class="sov-stat-value">{{ number_format((float) $remainingManDays, 2) }}</strong></div>
                </div>
            </section>

            <section class="sov-panel" aria-labelledby="sovLicenseTitle">
                <h2 class="sov-panel-title" id="sovLicenseTitle"><svg><use href="#icon-list"></use></svg><span>License</span></h2>
                <dl class="sov-details">
                    <div class="sov-detail"><dt>Professional</dt><dd>{{ number_format((int) $serviceOrder->professional) }}</dd></div>
                    <div class="sov-detail"><dt>Limited</dt><dd>{{ number_format((int) $serviceOrder->limited) }}</dd></div>
                    <div class="sov-detail"><dt>Indirect</dt><dd>{{ number_format((int) $serviceOrder->indirect) }}</dd></div>
                    <div class="sov-detail"><dt>Starter</dt><dd>{{ number_format((int) $serviceOrder->starter) }}</dd></div>
                    <div class="sov-detail wide"><dt>MSSQL</dt><dd>{{ number_format((int) $serviceOrder->mssql) }}</dd></div>
                </dl>
            </section>

            <section class="sov-panel" aria-labelledby="sovNotesTitle">
                <h2 class="sov-panel-title" id="sovNotesTitle"><svg><use href="#icon-doc"></use></svg><span>Notes</span></h2>
                <dl class="sov-details">
                    <div class="sov-detail"><dt>Notes</dt><dd>{{ $serviceOrder->notes ?: 'No notes added.' }}</dd></div>
                    <div class="sov-detail"><dt>Attach Service Order</dt><dd>@if ($serviceOrder->attach_service_order_original_name)<a href="{{ route('service-order-details.attachment', $serviceOrder) }}" target="_blank" rel="noopener">{{ $serviceOrder->attach_service_order_original_name }}</a>@else No attachment added. @endif</dd></div>
                </dl>
            </section>
        </div>
    </div>
@endsection

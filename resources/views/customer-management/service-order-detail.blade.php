@extends('layouts.app-shell')

@section('title', 'Service Order List')
@section('page-title', 'Service Order List')
@section('page-subtitle', 'Home / Service Orders / Service Order List')

@push('styles')
    <style>
        .so-detail-page {
            display: grid;
            gap: 14px;
            align-items: start;
        }

        .so-workspace {
            min-width: 0;
            display: grid;
            gap: 14px;
        }

        .so-page-actions {
            display: flex;
            justify-content: flex-end;
        }

        .so-button,
        .so-icon-button {
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .so-button {
            padding: 0 14px;
        }

        .so-button.primary {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
        }

        .so-button svg,
        .so-icon-button svg {
            width: 15px;
            height: 15px;
        }

        .so-icon-button {
            width: 34px;
            flex: 0 0 34px;
        }

        .so-panel {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .so-panel-header {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
        }

        .so-panel-title {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: var(--ink);
            font-size: 14px;
            line-height: 1.2;
            font-weight: 950;
        }

        .so-panel-title svg {
            width: 16px;
            height: 16px;
            color: var(--blue);
        }

        .so-filter-body {
            display: grid;
            grid-template-columns: repeat(3, minmax(170px, 1fr)) auto;
            gap: 14px 18px;
            padding: 14px 16px 16px;
            align-items: end;
        }

        .so-field {
            min-width: 0;
            display: grid;
            gap: 7px;
        }

        .so-field label {
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .so-input {
            width: 100%;
            min-height: 38px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 750;
        }

        .so-filter-actions {
            display: flex;
            align-items: end;
            gap: 10px;
        }

        .so-table-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .so-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .so-table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
            color: var(--ink);
        }

        .so-table th,
        .so-table td {
            padding: 13px 10px;
            border-bottom: 1px solid var(--line);
            border-right: 1px solid var(--line);
            vertical-align: middle;
            text-align: left;
        }

        .so-table th:last-child,
        .so-table td:last-child {
            border-right: 0;
        }

        .so-table thead th {
            background: color-mix(in srgb, var(--canvas) 72%, var(--panel));
            color: var(--ink);
            font-size: 11px;
            font-weight: 950;
            white-space: nowrap;
        }

        .so-table tbody td {
            font-size: 12px;
            font-weight: 750;
        }

        .so-table-code {
            color: var(--blue);
            font-weight: 950;
            white-space: nowrap;
        }

        .so-products {
            display: grid;
            gap: 3px;
            min-width: 150px;
        }

        .so-muted {
            color: var(--muted);
        }

        .so-number-cell {
            text-align: center;
            white-space: nowrap;
        }

        .so-status {
            display: inline-flex;
            min-height: 24px;
            align-items: center;
            justify-content: center;
            padding: 0 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            white-space: nowrap;
        }

        .so-status.active {
            background: var(--green-soft);
            color: #11834c;
        }

        .so-status.completed {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .so-status.on-hold {
            background: var(--amber-soft);
            color: #a35f00;
        }

        .so-row-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .so-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 16px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 750;
        }

        .so-pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .so-page-link,
        .so-page-select {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .so-page-link {
            min-width: 34px;
            padding: 0 10px;
        }

        .so-page-link.active {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
        }

        .so-page-link.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .so-page-select {
            padding: 0 10px;
        }

        .so-analytics {
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr);
            gap: 14px;
            align-items: stretch;
            min-width: 0;
        }

        .so-analytics-header {
            min-height: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .so-analytics-title {
            margin: 0;
            color: var(--ink);
            font-size: 15px;
            font-weight: 950;
        }

        .so-year-filter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 850;
        }

        .so-year-filter select {
            width: 78px;
        }

        .so-analytics-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .so-analytics-card h2 {
            margin: 0;
            padding: 14px 16px 8px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 950;
        }

        .so-overview-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            padding: 10px;
        }

        .so-overview-item {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            min-height: 74px;
            padding: 11px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: color-mix(in srgb, var(--panel) 94%, var(--canvas));
        }

        .so-overview-icon {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .so-overview-icon.green {
            background: var(--green-soft);
            color: #11834c;
        }

        .so-overview-icon.amber {
            background: var(--amber-soft);
            color: #a35f00;
        }

        .so-overview-icon.violet {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .so-overview-icon svg {
            width: 19px;
            height: 19px;
        }

        .so-overview-label {
            margin: 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 850;
        }

        .so-overview-value {
            margin: 2px 0 0;
            color: var(--ink);
            font-size: 22px;
            line-height: 1;
            font-weight: 950;
        }

        .so-overview-percent {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
        }

        .so-empty {
            padding: 20px 16px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }

        @media (max-width: 1280px) {
            .so-overview-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .so-filter-body {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .so-filter-actions {
                grid-column: 1 / -1;
            }

            .so-analytics {
                grid-template-columns: 1fr;
            }

            .so-analytics-header {
                min-height: auto;
            }
        }

        @media (max-width: 640px) {
            .so-filter-body,
            .so-overview-grid {
                grid-template-columns: 1fr;
            }

            .so-page-actions,
            .so-page-actions .so-button,
            .so-filter-actions,
            .so-filter-actions .so-button,
            .so-table-footer {
                width: 100%;
            }

            .so-table-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .so-pagination {
                flex-wrap: wrap;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $formatOrderCode = function ($order) {
            $year = $order->support_start_date?->format('Y') ?? $order->created_at?->format('Y') ?? now()->format('Y');

            return 'SO-'.$year.'-'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
        };

        $resolveProductNames = function ($order) use ($sapProductsById) {
            $names = $order->sapProducts->pluck('sap_product')->filter()->values();

            return $names;
        };

        $resolveStatus = function ($order) {
            $supportInclusion = strtolower((string) $order->support_inclusion);

            if (str_contains($supportInclusion, 'hold')) {
                return ['label' => 'On Hold', 'class' => 'on-hold'];
            }

            $manDays = max((int) ($order->man_days ?? 0), 0) + max((int) ($order->unused_man_days ?? 0), 0);
            $remaining = $order->remaining_man_days;

            if ($manDays > 0 && $remaining === 0) {
                return ['label' => 'Completed', 'class' => 'completed'];
            }

            return ['label' => 'Active', 'class' => 'active'];
        };

        $overviewPercent = fn ($count) => $analytics['total'] > 0 ? number_format(($count / $analytics['total']) * 100, 2) : '0.00';
        $paginationStart = $serviceOrders->firstItem() ?? 0;
        $paginationEnd = $serviceOrders->lastItem() ?? 0;
    @endphp

    <x-status-prompt />

    <div class="so-detail-page" @if (session('status')) style="margin-top: 14px;" @endif>
        <aside class="so-analytics" aria-label="Service order analytics">
            <div class="so-analytics-header">
                <h2 class="so-analytics-title">Analytics</h2>
                <label class="so-year-filter" for="year">
                    <span>Year</span>
                    <select class="so-input" id="year" name="year" form="sod-filter-form" onchange="this.form.submit()">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected((string) ($filters['year'] ?? '') === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <section class="so-analytics-card" aria-labelledby="so-overview-title">
                <h2 id="so-overview-title">Service Order Overview</h2>
                <div class="so-overview-grid">
                    <div class="so-overview-item">
                        <span class="so-overview-icon violet"><svg aria-hidden="true"><use href="#icon-doc"></use></svg></span>
                        <span>
                            <p class="so-overview-label">Total Service Orders</p>
                            <p class="so-overview-value">{{ number_format($analytics['total']) }}</p>
                            <span class="so-overview-percent">All Time</span>
                        </span>
                    </div>
                    <div class="so-overview-item">
                        <span class="so-overview-icon green"><svg aria-hidden="true"><use href="#icon-check-circle"></use></svg></span>
                        <span>
                            <p class="so-overview-label">Active</p>
                            <p class="so-overview-value">{{ number_format($analytics['active']) }}</p>
                            <span class="so-overview-percent">{{ $overviewPercent($analytics['active']) }}%</span>
                        </span>
                    </div>
                    <div class="so-overview-item">
                        <span class="so-overview-icon"><svg aria-hidden="true"><use href="#icon-check-circle"></use></svg></span>
                        <span>
                            <p class="so-overview-label">Completed</p>
                            <p class="so-overview-value">{{ number_format($analytics['completed']) }}</p>
                            <span class="so-overview-percent">{{ $overviewPercent($analytics['completed']) }}%</span>
                        </span>
                    </div>
                    <div class="so-overview-item">
                        <span class="so-overview-icon amber"><svg aria-hidden="true"><use href="#icon-clock"></use></svg></span>
                        <span>
                            <p class="so-overview-label">On Hold</p>
                            <p class="so-overview-value">{{ number_format($analytics['on_hold']) }}</p>
                            <span class="so-overview-percent">{{ $overviewPercent($analytics['on_hold']) }}%</span>
                        </span>
                    </div>
                </div>
            </section>
        </aside>

        <div class="so-workspace">
            <div class="so-page-actions">
                <a class="so-button primary" href="{{ route('service-order-details.index') }}">
                    <svg aria-hidden="true"><use href="#icon-plus"></use></svg>
                    <span>New Service Order</span>
                </a>
            </div>

            <section class="so-panel" aria-labelledby="service-order-filters-title">
                <div class="so-panel-header">
                    <h2 class="so-panel-title" id="service-order-filters-title">
                        <svg aria-hidden="true"><use href="#icon-gauge"></use></svg>
                        <span>Filters</span>
                    </h2>
                </div>

                <form id="sod-filter-form" class="so-filter-body" method="GET" action="{{ route('service-order-details.detail') }}">
                    <div class="so-field">
                        <label for="client_id">Client Name</label>
                        <select class="so-input" id="client_id" name="client_id">
                            <option value="">Select client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected((string) ($filters['client_id'] ?? '') === (string) $client->id)>{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="so-field">
                        <label for="industry_business_type_id">Industry / Business Type</label>
                        <select class="so-input" id="industry_business_type_id" name="industry_business_type_id">
                            <option value="">Select industry...</option>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->id }}" @selected((string) ($filters['industry_business_type_id'] ?? '') === (string) $industry->id)>{{ $industry->industry }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="so-field">
                        <label for="sap_product_id">Product Used</label>
                        <select class="so-input" id="sap_product_id" name="sap_product_id">
                            <option value="">Select product...</option>
                            @foreach ($sapProducts as $product)
                                <option value="{{ $product->id }}" @selected((string) ($filters['sap_product_id'] ?? '') === (string) $product->id)>{{ $product->sap_product }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="so-field">
                        <label for="software_version">Software Version</label>
                        <select class="so-input" id="software_version" name="software_version">
                            <option value="">Select version...</option>
                            @foreach ($softwareVersions as $version)
                                <option value="{{ $version }}" @selected((string) ($filters['software_version'] ?? '') === (string) $version)>{{ $version }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="so-field">
                        <label for="patch_or_fp">Patch or FP</label>
                        <select class="so-input" id="patch_or_fp" name="patch_or_fp">
                            <option value="">Select patch...</option>
                            @foreach ($patchOrFps as $patch)
                                <option value="{{ $patch }}" @selected((string) ($filters['patch_or_fp'] ?? '') === (string) $patch)>{{ $patch }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="so-filter-actions">
                        <button class="so-button primary" type="submit">
                            <svg aria-hidden="true"><use href="#icon-eye"></use></svg>
                            <span>Search</span>
                        </button>
                        <a class="so-button" href="{{ route('service-order-details.detail') }}">Clear Filters</a>
                    </div>
                </form>
            </section>

            <section class="so-panel" aria-labelledby="service-order-list-title">
                <div class="so-panel-header">
                    <h2 class="so-panel-title" id="service-order-list-title">
                        <svg aria-hidden="true"><use href="#icon-list"></use></svg>
                        <span>Service Order List</span>
                    </h2>

                    <div class="so-table-toolbar">
                        <button class="so-button" type="button">
                            <svg aria-hidden="true"><use href="#icon-settings"></use></svg>
                            <span>Columns</span>
                            <svg aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
                        </button>
                        <button class="so-button" type="button">
                            <svg aria-hidden="true"><use href="#icon-report"></use></svg>
                            <span>Export</span>
                            <svg aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
                        </button>
                    </div>
                </div>

                <div class="so-table-wrap">
                    <table class="so-table">
                        <thead>
                            <tr>
                                <th rowspan="2">SO No.</th>
                                <th rowspan="2">Client Name</th>
                                <th rowspan="2">Industry / Business Type</th>
                                <th rowspan="2">Products Used</th>
                                <th rowspan="2">Support Period</th>
                                <th colspan="3" class="so-number-cell">Man-days</th>
                                <th rowspan="2">Status</th>
                                <th rowspan="2">Actions</th>
                            </tr>
                            <tr>
                                <th>Entitled</th>
                                <th>Used</th>
                                <th>Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serviceOrders as $order)
                                @php
                                    $productNames = $resolveProductNames($order);
                                    $manDays = max((float) ($order->man_days ?? 0), 0) + max((float) ($order->unused_man_days ?? 0), 0);
                                    $usedManDays = max((float) ($order->used_man_days ?? 0), 0);
                                    $remainingManDays = (float) $order->remaining_man_days;
                                    $status = $resolveStatus($order);
                                @endphp
                                <tr>
                                    <td><span class="so-table-code">{{ $formatOrderCode($order) }}</span></td>
                                    <td>{{ $order->client?->company_name ?? 'Not set' }}</td>
                                    <td>{{ $order->industryBusinessType?->industry ?? 'Not set' }}</td>
                                    <td>
                                        <span class="so-products">
                                            @forelse ($productNames as $productName)
                                                <span>{{ $productName }}</span>
                                            @empty
                                                <span class="so-muted">Not set</span>
                                            @endforelse
                                        </span>
                                    </td>
                                    <td>
                                        {{ $order->support_start_date?->format('m/d/Y') ?? 'Not set' }}
                                        <br>
                                        - {{ $order->support_end_date?->format('m/d/Y') ?? 'Not set' }}
                                    </td>
                                    <td class="so-number-cell">{{ number_format($manDays, 2) }}</td>
                                    <td class="so-number-cell">{{ number_format($usedManDays, 2) }}</td>
                                    <td class="so-number-cell">{{ number_format($remainingManDays, 2) }}</td>
                                    <td><span class="so-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                    <td>
                                        <div class="so-row-actions">
                                            <a class="so-icon-button" href="{{ route('service-order-details.show', $order) }}" aria-label="View {{ $formatOrderCode($order) }}">
                                                <svg aria-hidden="true"><use href="#icon-eye"></use></svg>
                                            </a>
                                            <a class="so-icon-button" href="{{ route('service-order-details.edit', $order) }}" aria-label="Edit {{ $formatOrderCode($order) }}">
                                                <svg aria-hidden="true"><use href="#icon-pencil"></use></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="so-empty">No service orders found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="so-table-footer">
                    <span>Showing {{ number_format($paginationStart) }} to {{ number_format($paginationEnd) }} of {{ number_format($serviceOrders->total()) }} entries</span>

                    <div class="so-pagination">
                        <select class="so-page-select" name="per_page" form="sod-filter-form" onchange="this.form.submit()">
                            @foreach ([8, 10, 15, 25] as $perPage)
                                <option value="{{ $perPage }}" @selected((int) request('per_page', 10) === $perPage)>{{ $perPage }} / page</option>
                            @endforeach
                        </select>
                        <a class="so-page-link {{ $serviceOrders->onFirstPage() ? 'disabled' : '' }}" href="{{ $serviceOrders->previousPageUrl() ?: '#' }}" aria-label="Previous page">&laquo;</a>
                        @for ($page = 1; $page <= $serviceOrders->lastPage(); $page++)
                            @if ($page <= 3 || $page === $serviceOrders->currentPage() || $page === $serviceOrders->lastPage())
                                <a class="so-page-link {{ $serviceOrders->currentPage() === $page ? 'active' : '' }}" href="{{ $serviceOrders->url($page) }}">{{ $page }}</a>
                            @elseif ($page === 4)
                                <span class="so-page-link disabled">...</span>
                            @endif
                        @endfor
                        <a class="so-page-link {{ $serviceOrders->hasMorePages() ? '' : 'disabled' }}" href="{{ $serviceOrders->nextPageUrl() ?: '#' }}" aria-label="Next page">&raquo;</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

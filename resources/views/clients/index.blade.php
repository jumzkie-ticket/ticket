@extends('layouts.app-shell')

@section('title', 'Clients')
@section('page-title', 'Clients')
@section('page-subtitle', 'Review and manage registered client companies.')

@push('styles')
    <style>
        .clients-page {
            display: grid;
            gap: 18px;
        }

        .clients-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .clients-card,
        .clients-panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .clients-card {
            min-height: 104px;
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            align-items: center;
            gap: 13px;
            padding: 16px;
        }

        .clients-card-icon {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .clients-card-icon.green {
            background: var(--green-soft);
            color: var(--green);
        }

        .clients-card-icon.amber {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .clients-card-icon.violet {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .clients-card-icon svg,
        .clients-title svg {
            width: 18px;
            height: 18px;
        }

        .clients-card-label {
            margin: 0 0 5px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .clients-card-value {
            margin: 0;
            color: var(--ink);
            font-size: 22px;
            font-weight: 900;
            line-height: 1;
        }

        .clients-layout {
            display: grid;
            grid-template-columns: 380px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .clients-panel-body {
            padding: 18px;
        }

        .clients-panel-header {
            padding: 18px;
            border-bottom: 1px solid var(--line);
        }

        .clients-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 14px;
            color: var(--ink);
            font-size: 16px;
            font-weight: 900;
        }

        .clients-title svg {
            color: var(--blue);
        }

        .clients-copy,
        .clients-detail-row span {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }

        .clients-form {
            display: grid;
            gap: 12px;
        }

        .clients-label {
            display: grid;
            gap: 7px;
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .clients-input,
        .clients-select {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 750;
            outline: none;
        }

        .clients-input:focus,
        .clients-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .clients-phone-control {
            display: grid;
            grid-template-columns: 82px minmax(0, 1fr);
            gap: 8px;
        }

        .clients-button,
        .clients-link-button {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 14px;
            border: 1px solid var(--blue);
            border-radius: 6px;
            background: var(--blue);
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .clients-link-button {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .clients-button svg,
        .clients-link-button svg {
            width: 15px;
            height: 15px;
        }

        .clients-product-picker { display: grid; gap: 7px; }
        .clients-product-row { display: flex; align-items: stretch; gap: 8px; }
        .clients-product-row textarea { min-width: 0; min-height: 58px; height: auto; padding: 9px 11px; resize: none; flex: 1; }
        .clients-product-select { flex: 0 0 104px; padding: 0 10px; border: 1px solid var(--blue); border-radius: 6px; background: var(--blue-soft); color: var(--blue); font-size: 11px; font-weight: 900; }
        .clients-product-help { color: var(--muted); font-size: 10px; font-weight: 700; }
        .clients-product-modal { position: fixed; z-index: 1200; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(4, 15, 39, .68); }
        .clients-product-modal.active { display: flex; }
        .clients-product-dialog { width: min(560px, 100%); overflow: hidden; border: 1px solid var(--line); border-radius: 12px; background: var(--panel); box-shadow: 0 24px 65px rgba(0, 0, 0, .28); }
        .clients-product-header, .clients-product-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; }
        .clients-product-header { border-bottom: 1px solid var(--line); }
        .clients-product-header h3 { margin: 0; color: var(--ink); font-size: 15px; }
        .clients-product-close { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); font-size: 20px; }
        .clients-product-options { max-height: 330px; display: grid; gap: 8px; padding: 16px 18px; overflow-y: auto; }
        .clients-product-option { display: flex; align-items: center; gap: 10px; padding: 11px 12px; border: 1px solid var(--line); border-radius: 8px; color: var(--ink); font-size: 12px; font-weight: 750; cursor: pointer; }
        .clients-product-option:hover { border-color: var(--blue); background: var(--blue-soft); }
        .clients-product-option input { width: 16px; height: 16px; accent-color: var(--blue); }
        .clients-product-actions { justify-content: flex-end; border-top: 1px solid var(--line); }

        .clients-flash {
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

        .clients-errors {
            margin: 0;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .clients-detail-stack {
            display: grid;
            gap: 12px;
        }

        .clients-detail-row {
            display: grid;
            gap: 4px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .clients-detail-row strong {
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.4;
        }

        .clients-table {
            width: 100%;
            border-collapse: collapse;
        }

        .clients-table th,
        .clients-table td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: middle;
        }

        .clients-table th {
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .clients-table td {
            color: var(--ink);
            font-size: 12px;
            font-weight: 800;
        }

        .clients-company {
            display: grid;
            gap: 4px;
            min-width: 170px;
        }

        .clients-company strong {
            font-size: 13px;
        }

        .clients-company span,
        .clients-muted {
            color: var(--muted);
            font-size: 11px;
            font-weight: 750;
            line-height: 1.4;
        }

        .clients-status {
            width: fit-content;
            min-height: 26px;
            display: inline-grid;
            place-items: center;
            padding: 0 9px;
            border-radius: 999px;
            background: var(--amber-soft);
            color: var(--amber);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .clients-status.status-active {
            background: var(--green-soft);
            color: var(--green);
        }

        .clients-status.status-inactive {
            background: var(--red-soft);
            color: var(--red);
        }

        .clients-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .clients-action-form {
            margin: 0;
        }

        .icon-action {
            width: 32px;
            height: 32px;
            display: inline-grid;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--panel);
            color: var(--ink);
            text-decoration: none;
        }

        .icon-action svg {
            width: 15px;
            height: 15px;
        }

        .icon-action:hover,
        .icon-action:focus-visible {
            border-color: var(--blue);
            color: var(--blue);
            outline: none;
        }

        .icon-action.danger {
            color: var(--red);
        }

        .clients-empty {
            padding: 34px 18px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }

        .clients-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px; color: var(--muted); font-size: 11px; font-weight: 750; }
        .clients-pagination-actions { display: flex; gap: 8px; }
        .clients-page-link { min-height: 34px; display: inline-flex; align-items: center; padding: 0 12px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--ink); font-weight: 850; text-decoration: none; }
        .clients-page-link.disabled { opacity: .45; pointer-events: none; }

        @media (max-width: 1220px) {
            .clients-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .clients-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 780px) {
            .clients-analytics {
                grid-template-columns: 1fr;
            }

            .clients-panel {
                overflow-x: auto;
            }

            .clients-table {
                min-width: 960px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="clients-page">
        <x-status-prompt />

        @if ($errors->any())
            <ul class="clients-errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <section class="clients-analytics" aria-label="Client analytics">
            <article class="clients-card">
                <span class="clients-card-icon"><svg><use href="#icon-client"></use></svg></span>
                <div>
                    <p class="clients-card-label">Total Clients</p>
                    <p class="clients-card-value">{{ $analytics['total'] }}</p>
                </div>
            </article>
            <article class="clients-card">
                <span class="clients-card-icon green"><svg><use href="#icon-check-circle"></use></svg></span>
                <div>
                    <p class="clients-card-label">Active Clients</p>
                    <p class="clients-card-value">{{ $analytics['active'] }}</p>
                </div>
            </article>
            <article class="clients-card">
                <span class="clients-card-icon amber"><svg><use href="#icon-calendar"></use></svg></span>
                <div>
                    <p class="clients-card-label">New This Month</p>
                    <p class="clients-card-value">{{ $analytics['new_this_month'] }}</p>
                </div>
            </article>
            <article class="clients-card">
                <span class="clients-card-icon violet"><svg><use href="#icon-list"></use></svg></span>
                <div>
                    <p class="clients-card-label">Industries</p>
                    <p class="clients-card-value">{{ $analytics['industries'] }}</p>
                </div>
            </article>
        </section>

        <div class="clients-layout">
            <section class="clients-panel" aria-labelledby="client-panel-title">
                <div class="clients-panel-body">
                    @if ($panelMode === 'view' && $selectedClient)
                        @php
                            $address = collect([
                                $selectedClient->building_details,
                                $selectedClient->barangay_name,
                                $selectedClient->city_municipality_name,
                                $selectedClient->province_name,
                                $selectedClient->region_name,
                            ])->filter()->implode(', ');
                        @endphp

                        <h2 class="clients-title" id="client-panel-title"><svg><use href="#icon-eye"></use></svg><span>View Client</span></h2>
                        <div class="clients-detail-stack">
                            <div class="clients-detail-row">
                                <span>Company Name</span>
                                <strong>{{ $selectedClient->company_name }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Contact Person</span>
                                <strong>{{ $selectedClient->contact_person }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Designation</span>
                                <strong>{{ $selectedClient->designation ?: 'Not set' }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Email Address</span>
                                <strong>{{ $selectedClient->email_address }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Contact Number</span>
                                <strong>{{ $selectedClient->contact_country_code }} {{ $selectedClient->contact_number }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Account Manager</span>
                                <strong>{{ $selectedClient->accountManager?->account_manager ?? 'Not assigned' }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Industry / Product</span>
                                <strong>{{ $selectedClient->industryBusinessType?->industry ?? 'Not set' }} / {{ $selectedClient->sapProducts->pluck('sap_product')->join(', ') ?: 'Not set' }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Version / Patch</span>
                                <strong>{{ $selectedClient->version_number }} / {{ $selectedClient->patch_or_fp }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Database Version</span>
                                <strong>{{ $selectedClient->db_version ?: 'Not set' }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Support Method</span>
                                <strong>{{ $supportMethods[$selectedClient->preferred_support_method] ?? $selectedClient->preferred_support_method }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Address</span>
                                <strong>{{ $address }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Status</span>
                                <strong>{{ $statuses[$selectedClient->status] ?? ucfirst($selectedClient->status) }}</strong>
                            </div>
                            <div class="clients-detail-row">
                                <span>Registered On</span>
                                <strong>{{ $selectedClient->created_at?->format('F j, Y g:i A') }}</strong>
                            </div>
                            <a class="clients-link-button" href="{{ route('clients.index') }}">
                                <svg><use href="#icon-list"></use></svg>
                                <span>Back to Client List</span>
                            </a>
                        </div>
                    @elseif ($panelMode === 'edit' && $selectedClient)
                        @php
                            $existingProductIds = $selectedClient->sapProducts->pluck('id')->all();
                            $selectedProductIds = array_map('strval', (array) old('sap_product_ids', $existingProductIds));
                        @endphp
                        <h2 class="clients-title" id="client-panel-title"><svg><use href="#icon-pencil"></use></svg><span>Edit Client</span></h2>
                        <form class="clients-form" method="POST" action="{{ route('clients.update', $selectedClient) }}">
                            @csrf
                            @method('PUT')

                            <label class="clients-label">
                                <span>Company Name</span>
                                <input class="clients-input" name="company_name" type="text" value="{{ old('company_name', $selectedClient->company_name) }}" required>
                            </label>

                            <label class="clients-label">
                                <span>Contact Person</span>
                                <input class="clients-input" name="contact_person" type="text" value="{{ old('contact_person', $selectedClient->contact_person) }}" required>
                            </label>

                            <label class="clients-label">
                                <span>Designation</span>
                                <input class="clients-input" name="designation" type="text" value="{{ old('designation', $selectedClient->designation) }}" required>
                            </label>

                            <label class="clients-label">
                                <span>Email Address</span>
                                <input class="clients-input" name="email_address" type="email" value="{{ old('email_address', $selectedClient->email_address) }}" required>
                            </label>

                            <label class="clients-label">
                                <span>Contact Number</span>
                                <span class="clients-phone-control">
                                    <select class="clients-select" name="contact_country_code" required>
                                        <option value="+63" @selected(old('contact_country_code', $selectedClient->contact_country_code) === '+63')>PH +63</option>
                                    </select>
                                    <input class="clients-input client-contact-number" name="contact_number" type="text" value="{{ old('contact_number', $selectedClient->contact_number) }}" maxlength="12" inputmode="numeric" pattern="\d{3}-\d{3}-\d{4}" required>
                                </span>
                            </label>

                            <label class="clients-label">
                                <span>Industry / Business Type</span>
                                <select class="clients-select" name="industry_business_type_id" required>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}" @selected((string) old('industry_business_type_id', $selectedClient->industry_business_type_id) === (string) $industry->id)>{{ $industry->industry }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <div class="clients-label clients-product-picker">
                                <span>Product Used</span>
                                <div class="clients-product-row">
                                    <textarea class="clients-input" id="edit-products-display" rows="2" readonly></textarea>
                                    <button class="clients-product-select" id="edit-products-open" type="button">Select Products</button>
                                </div>
                                <div id="edit-products-inputs">
                                    @foreach ($selectedProductIds as $selectedProductId)
                                        <input type="hidden" name="sap_product_ids[]" value="{{ $selectedProductId }}">
                                    @endforeach
                                </div>
                                <span class="clients-product-help">Choose one or more products from the popup window.</span>
                            </div>

                            <label class="clients-label">
                                <span>Version Number format: (10.00.130)</span>
                                <input class="clients-input" name="version_number" type="text" value="{{ old('version_number', $selectedClient->version_number) }}" required>
                            </label>

                            <label class="clients-label">
                                <span>Patch (PL 01) / Package Name (FP 2008)</span>
                                <input class="clients-input" name="patch_or_fp" type="text" value="{{ old('patch_or_fp', $selectedClient->patch_or_fp) }}" required>
                            </label>

                            <label class="clients-label">
                                <span>Database Version (MSSQL 2019)</span>
                                <input class="clients-input" name="db_version" type="text" value="{{ old('db_version', $selectedClient->db_version) }}" placeholder="MSSQL 2019" required>
                            </label>

                            <label class="clients-label">
                                <span>Company Size / Number of Users</span>
                                <select class="clients-select" name="company_size" required>
                                    @foreach ($companySizes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('company_size', $selectedClient->company_size) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="clients-label">
                                <span>Account Manager</span>
                                <select class="clients-select" name="account_manager_id">
                                    <option value="">Select account manager</option>
                                    @foreach ($accountManagers as $accountManager)
                                        <option value="{{ $accountManager->id }}" @selected((string) old('account_manager_id', $selectedClient->account_manager_id) === (string) $accountManager->id)>{{ $accountManager->account_manager }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="clients-label">
                                <span>Assign FC</span>
                                <select class="clients-select" name="assign_fc_id">
                                    <option value="">Select FC</option>
                                    @foreach($assignFcs as $fc)
                                        <option value="{{ $fc->id }}" @selected((string) old('assign_fc_id', $selectedClient->assign_fc_id) === (string) $fc->id)>{{ $fc->assign_fc }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="clients-label">
                                <span>Preferred Support Contact Method</span>
                                <select class="clients-select" name="preferred_support_method" required>
                                    @foreach ($supportMethods as $value => $label)
                                        <option value="{{ $value }}" @selected(old('preferred_support_method', $selectedClient->preferred_support_method) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="clients-label">
                                <span>Status</span>
                                <select class="clients-select" name="status" required>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $selectedClient->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <div class="clients-product-modal" id="edit-products-modal" role="dialog" aria-modal="true" aria-labelledby="edit-products-title">
                                <div class="clients-product-dialog">
                                    <div class="clients-product-header">
                                        <h3 id="edit-products-title">Select Product Used</h3>
                                        <button class="clients-product-close" id="edit-products-close" type="button" aria-label="Close">&times;</button>
                                    </div>
                                    <div class="clients-product-options">
                                        @foreach ($sapProducts as $sapProduct)
                                            <label class="clients-product-option">
                                                <input type="checkbox" value="{{ $sapProduct->id }}" data-product-name="{{ $sapProduct->sap_product }}" @checked(in_array((string) $sapProduct->id, $selectedProductIds, true))>
                                                <span>{{ $sapProduct->sap_product }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="clients-product-actions">
                                        <button class="clients-link-button" id="edit-products-cancel" type="button">Cancel</button>
                                        <button class="clients-button" id="edit-products-apply" type="button">Apply Selection</button>
                                    </div>
                                </div>
                            </div>

                            <button class="clients-button" type="submit">
                                <svg><use href="#icon-check-circle"></use></svg>
                                <span>Save Changes</span>
                            </button>
                            <a class="clients-link-button" href="{{ route('clients.index') }}">Cancel</a>
                        </form>
                    @else
                        <h2 class="clients-title" id="client-panel-title"><svg><use href="#icon-client"></use></svg><span>Client Details</span></h2>
                        <p class="clients-copy">Select a client from the list to view details or update client contact, product, support, and status information.</p>
                        <a class="clients-link-button" style="margin-top:16px" href="{{ route('clients.registration') }}">
                            <svg><use href="#icon-plus"></use></svg>
                            <span>Register New Client</span>
                        </a>
                    @endif
                </div>
            </section>

            <section class="clients-panel" aria-labelledby="client-list-title">
                <div class="clients-panel-header">
                    <h2 class="clients-title" id="client-list-title" style="margin-bottom:0"><svg><use href="#icon-list"></use></svg><span>Client List</span></h2>
                </div>

                <table class="clients-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Industry</th>
                            <th>Product Used</th>
                            <th>Account Manager</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>
                                    <div class="clients-company">
                                        <strong>{{ $client->company_name }}</strong>
                                        <span>{{ $client->email_address }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="clients-company">
                                        <strong>{{ $client->contact_person }}</strong>
                                        <span>{{ $client->designation ?: 'Not set' }}</span>
                                    </div>
                                </td>
                                <td>{{ $client->industryBusinessType?->industry ?? 'Not set' }}</td>
                                <td>{{ $client->sapProducts->pluck('sap_product')->join(', ') ?: 'Not set' }}</td>
                                <td>{{ $client->accountManager?->account_manager ?? 'Not assigned' }}</td>
                                <td>
                                    <span class="clients-status status-{{ $client->status }}">{{ $statuses[$client->status] ?? ucfirst($client->status) }}</span>
                                </td>
                                <td>
                                    <div class="clients-actions">
                                        <a class="icon-action" href="{{ route('clients.index', ['view' => $client->id]) }}" title="View" aria-label="View {{ $client->company_name }}">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </a>
                                        <a class="icon-action" href="{{ route('clients.index', ['edit' => $client->id]) }}" title="Edit" aria-label="Edit {{ $client->company_name }}">
                                            <svg><use href="#icon-pencil"></use></svg>
                                        </a>
                                        <form class="clients-action-form" method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action danger" type="submit" title="Delete" aria-label="Delete {{ $client->company_name }}">
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="clients-empty" colspan="7">No registered clients yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($clients->hasPages())
                    <div class="clients-pagination">
                        <span>Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} clients</span>
                        <span class="clients-pagination-actions">
                            <a class="clients-page-link {{ $clients->onFirstPage() ? 'disabled' : '' }}" href="{{ $clients->previousPageUrl() ?? '#' }}">Previous</a>
                            <a class="clients-page-link {{ $clients->hasMorePages() ? '' : 'disabled' }}" href="{{ $clients->nextPageUrl() ?? '#' }}">Next</a>
                        </span>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const contactNumber = document.querySelector('.client-contact-number');

            if (!contactNumber) {
                return;
            }

            const productModal = document.getElementById('edit-products-modal');
            const productDisplay = document.getElementById('edit-products-display');
            const productInputs = document.getElementById('edit-products-inputs');
            const productCheckboxes = Array.from(productModal.querySelectorAll('input[type="checkbox"]'));
            const selectedProductIds = () => Array.from(productInputs.querySelectorAll('input[name="sap_product_ids[]"]')).map(input => input.value);
            const updateProductDisplay = () => {
                const ids = selectedProductIds();
                productDisplay.value = productCheckboxes.filter(checkbox => ids.includes(checkbox.value)).map(checkbox => checkbox.dataset.productName).join(', ');
            };
            const closeProductModal = () => productModal.classList.remove('active');
            const openProductModal = () => {
                const ids = selectedProductIds();
                productCheckboxes.forEach(checkbox => checkbox.checked = ids.includes(checkbox.value));
                productModal.classList.add('active');
            };

            document.getElementById('edit-products-open').addEventListener('click', openProductModal);
            document.getElementById('edit-products-close').addEventListener('click', closeProductModal);
            document.getElementById('edit-products-cancel').addEventListener('click', closeProductModal);
            document.getElementById('edit-products-apply').addEventListener('click', () => {
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

            const formatContactNumber = (value) => {
                const digits = value.replace(/\D/g, '').slice(0, 10);

                if (digits.length <= 3) {
                    return digits;
                }

                if (digits.length <= 6) {
                    return `${digits.slice(0, 3)}-${digits.slice(3)}`;
                }

                return `${digits.slice(0, 3)}-${digits.slice(3, 6)}-${digits.slice(6)}`;
            };

            contactNumber.addEventListener('input', () => {
                contactNumber.value = formatContactNumber(contactNumber.value);
            });

            contactNumber.value = formatContactNumber(contactNumber.value);
        })();
    </script>
@endpush

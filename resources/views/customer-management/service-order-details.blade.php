@extends('layouts.app-shell')

@php
    $editingServiceOrder = $serviceOrder ?? null;
    $selectedProductIds = (array) old(
        'sap_product_ids',
        $editingServiceOrder
            ? $editingServiceOrder->sapProducts->pluck('id')->all()
            : [],
    );
    $selectedPackageIds = (array) old(
        'package_ids',
        $editingServiceOrder
            ? $editingServiceOrder->packages->pluck('id')->all()
            : [],
    );
@endphp

@section('title', $editingServiceOrder ? 'Edit Service Order' : 'Service Order')
@section('page-title', $editingServiceOrder ? 'Edit Service Order' : 'Service Order')
@section('page-subtitle', $editingServiceOrder ? 'Update the selected service order and coverage details.' : 'Capture service order details for customer support coverage.')

@section('content')
    <style>
        .sod-page { padding: 8px; }
        .sod-container { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .sod-section { min-width: 0; background: #f8fafc; border: 1px solid #edf1f7; border-radius: 12px; padding: 24px 20px; }
        .sod-section-title { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; color: #0f172a; font-size: 16px; font-weight: 750; }
        .sod-section-title svg { width: 18px; height: 18px; flex: 0 0 auto; }
        .sod-field-group { display: grid; gap: 15px; }
        .sod-field { display: flex; min-width: 0; flex-direction: column; gap: 7px; }
        .sod-field label { color: #17223b; font-size: 14px; font-weight: 500; }
        .sod-field label .required { color: #ef4444; }
        .sod-field input, .sod-field select, .sod-field textarea { width: 100%; min-height: 38px; padding: 8px 12px; border: 1px solid #d8dee9; border-radius: 6px; background: #ffffff; color: #0f172a; font-size: 14px; }
        .sod-field textarea { min-height: 66px; resize: vertical; }
        .sod-field input:focus, .sod-field select:focus, .sod-field textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(37, 99, 235, .08); }
        .sod-field input[readonly] { color: #64748b; background: #ffffff; }
        .sod-field small { color: #64748b; font-size: 11px; }
        .sod-selection-row { display: flex; align-items: center; gap: 10px; }
        .sod-selection-row input, .sod-selection-row textarea { min-width: 0; flex: 1; }
        .sod-selection-row textarea { min-height: 78px; resize: none; }
        .sod-selection-row .btn-secondary { flex: 0 0 auto; }
        .sod-badges { display: flex; gap: 8px; flex-wrap: wrap; }
        .sod-modal { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); display: none; align-items: center; justify-content: center; padding: 20px; z-index: 1000; }
        .sod-modal.active { display: flex; }
        .sod-modal-dialog { width: min(560px, 100%); background: white; border-radius: 12px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2); overflow: hidden; }
        .sod-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #111827; }
        .sod-modal-body { padding: 16px 20px; display: grid; gap: 10px; max-height: 320px; overflow-y: auto; }
        .sod-modal-option { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .sod-modal-actions { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid #e5e7eb; }
        .sod-badge { background: #e0e7ff; color: #4f46e5; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .sod-badge .remove { cursor: pointer; margin-left: 4px; }

        .man-days-section { margin-top: 16px; padding: 10px; border: 1px solid #dce5f0; border-radius: 10px; background: #ffffff; box-shadow: 0 2px 8px rgba(15, 23, 42, .04); }
        .man-days-header { display: flex; align-items: center; gap: 9px; margin: 0; padding: 0 8px 10px; color: #1764d8; font-size: 13px; font-weight: 750; }
        .man-days-header svg { width: 18px; height: 18px; }
        .man-days-table { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); overflow: hidden; border: 1px solid #d7e1ed; border-radius: 5px; background: #ffffff; }
        .man-days-col { min-width: 0; padding: 0 16px 12px; border-right: 1px solid #d7e1ed; text-align: left; }
        .man-days-col:last-child { border-right: none; }
        .man-days-col-header { margin: 0 -16px 10px; padding: 10px 12px; border-bottom: 1px solid #d7e1ed; background: linear-gradient(180deg, #f4f8ff, #edf4fd); color: #17468d; font-size: 12px; font-weight: 700; text-align: center; }
        .man-days-col input { width: 100%; min-height: 38px; padding: 8px 12px; border: 1px solid #d8dee9; border-radius: 6px; background: #ffffff; color: #17223b; font-size: 14px; }
        .man-days-col input[readonly] { color: #475569; background: #f8fafc; }
        .man-days-col input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(37, 99, 235, .08); }

        .license-section { margin-top: 16px; padding: 18px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; }
        .license-header { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; color: #17223b; font-size: 14px; font-weight: 700; }
        .license-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; }
        .license-card { display: grid; gap: 10px; padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; text-align: left; transition: all 0.2s; }
        .license-card:hover { border-color: #3b82f6; background: #eff6ff; }
        .license-card-icon { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 50%; background: #eff6ff; color: #2563eb; font-size: 16px; }
        .license-card-name { font-size: 14px; font-weight: 700; color: #111827; }
        .license-card-quantity { width: 100%; padding: 9px 10px; border: 1px solid #d1d5db; border-radius: 6px; background: #f8fafc; color: #111827; font-size: 15px; font-weight: 600; text-align: center; }
        .license-card-quantity:focus { outline: none; border-color: #3b82f6; background: #eff6ff; }

        .bottom-container { display: grid; grid-template-columns: 1.25fr 1fr; gap: 16px; margin-top: 16px; }
        .notes-section, .summary-section { min-width: 0; padding: 16px; border: 1px solid #e2e8f0; border-radius: 10px; }
        .notes-section { background: #ffffff; }
        .notes-section h3, .summary-section h3 { margin: 0 0 12px; color: #17396f; font-size: 13px; font-weight: 750; }
        .notes-fields { display: grid; grid-template-columns: minmax(0, 1fr) minmax(240px, .7fr); gap: 14px; align-items: start; }
        .notes-field { display: grid; gap: 7px; color: #17396f; font-size: 12px; font-weight: 700; }
        .notes-section textarea { width: 100%; min-height: 94px; padding: 12px; border: 1px solid #d8dee9; border-radius: 6px; background: #ffffff; color: #17223b; font: inherit; resize: vertical; }
        .service-order-file { width: 100%; min-height: 42px; padding: 8px; border: 1px solid #d8dee9; border-radius: 6px; background: #ffffff; color: #17223b; font: inherit; }
        .service-order-file-current { color: #64748b; font-size: 11px; font-weight: 500; overflow-wrap: anywhere; }
        .notes-section textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(37, 99, 235, .08); }
        .summary-section { background: linear-gradient(135deg, #fffdf8, #fffbef); }
        .summary-item { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 8px 0; }
        .summary-label { color: #334155; font-size: 13px; font-weight: 500; }
        .summary-value { color: #17223b; font-size: 13px; font-weight: 700; }
        .summary-value.positive { color: #16a34a; }

        .btn { padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1px solid transparent; }
        .btn-primary { background: #2563eb; color: #ffffff; border-color: #2563eb; }
        .btn-primary:hover { background: #1e4fd8; }
        .btn-secondary { background: white; color: #374151; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .btn-secondary:hover { background: #f3f4f6; }

        .notes-actions { display: flex; justify-content: flex-start; gap: 8px; margin-top: 12px; }
        @media (max-width: 640px) {
            .notes-fields { grid-template-columns: 1fr; }
            .notes-actions { flex-direction: column; align-items: stretch; }
            .notes-actions .btn { width: 100%; }
            .sod-selection-row { align-items: stretch; flex-direction: column; }
            .man-days-table { grid-template-columns: 1fr; }
            .man-days-col { border-right: 0; border-bottom: 1px solid #d7e1ed; }
            .man-days-col:last-child { border-bottom: 0; }
        }

        @media (max-width: 1024px) {
            .sod-container { grid-template-columns: 1fr; }
            .license-cards { grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); }
            .bottom-container { grid-template-columns: 1fr; }
        }
    </style>

    <div class="sod-page">
        <x-status-prompt />

        @if ($errors->any())
            <div class="alert danger" style="margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="serviceOrderForm" action="{{ $editingServiceOrder ? route('service-order-details.update', $editingServiceOrder) : route('service-order-details.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($editingServiceOrder)
                @method('PUT')
            @endif

            <!-- Client Information & Product & Service Sections -->
            <div class="sod-container">
                <!-- Client Information -->
                <div class="sod-section">
                    <div class="sod-section-title">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                        Client Information
                    </div>
                    <div class="sod-field-group">
                        <div class="sod-field">
                            <label for="client_id">Client Name <span class="required">*</span></label>
                            <select id="client_id" name="client_id" class="sod-field input" required>
                                <option value="">Search client...</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $editingServiceOrder?->client_id) == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sod-field">
                            <label for="industry_business_type_display">Industry / Business Type</label>
                            <input id="industry_business_type_display" name="industry_business_type_display" type="text" class="sod-field input" value="{{ old('industry_business_type_display', $editingServiceOrder?->industryBusinessType?->industry) }}" placeholder="Industry will appear here" readonly>
                            <input type="hidden" id="industry_business_type_id" name="industry_business_type_id" value="{{ old('industry_business_type_id', $editingServiceOrder?->industry_business_type_id) }}">
                        </div>

                        <div class="sod-field">
                            <label for="product_used_display">Product Used <span class="required">*</span></label>
                            <textarea id="product_used_display" name="product_used_display" class="sod-field input" rows="3" placeholder="Products will appear after selecting a client" readonly></textarea>
                            <div id="sap_product_ids_container">
                                @foreach ($selectedProductIds as $selectedProductId)
                                    <input type="hidden" name="sap_product_ids[]" value="{{ $selectedProductId }}">
                                @endforeach
                            </div>
                            <small style="color: #6b7280;">Products are loaded from the selected client's registered products.</small>
                        </div>

                        <div class="sod-field">
                            <label for="software_version_display">Software Version <span class="required">*</span></label>
                            <input id="software_version_display" name="software_version_display" type="text" class="sod-field input" value="{{ old('software_version', $editingServiceOrder?->software_version) }}" placeholder="Software version will appear here" readonly>
                            <input type="hidden" id="software_version" name="software_version" value="{{ old('software_version', $editingServiceOrder?->software_version) }}">
                        </div>

                        <div class="sod-field">
                            <label for="patch_or_fp_display">Patch or FP <span class="required">*</span></label>
                            <input id="patch_or_fp_display" name="patch_or_fp_display" type="text" class="sod-field input" value="{{ old('patch_or_fp', $editingServiceOrder?->patch_or_fp) }}" placeholder="Patch or FP will appear here" readonly>
                            <input type="hidden" id="patch_or_fp" name="patch_or_fp" value="{{ old('patch_or_fp', $editingServiceOrder?->patch_or_fp) }}">
                        </div>

                    </div>
                </div>

                <!-- Product & Service -->
                <div class="sod-section">
                    <div class="sod-section-title">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042L5.960 9H9a1 1 0 000-2H6.592l-.894-4.472A1 1 0 004.106 2H3.5H3zm9.474 0a1 1 0 10.904 1.989c.642-.158 1.288.155 1.707.521.419.366.646.934.646 1.490 0 .35-.067.68-.196.98M16 16a2 2 0 11-4 0 2 2 0 014 0zm-6 0a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Product & Service
                    </div>
                    <div class="sod-field-group">
                        <div class="sod-field">
                            <label for="support_start_date">Support Start Date <span class="required">*</span></label>
                            <input id="support_start_date" name="support_start_date" type="date" class="sod-field input" value="{{ old('support_start_date', $editingServiceOrder?->support_start_date?->format('Y-m-d') ?? date('Y-m-d', strtotime('+1 day'))) }}" required />
                        </div>

                        <div class="sod-field">
                            <label for="support_end_date">Support End Date <span class="required">*</span></label>
                            <input id="support_end_date" name="support_end_date" type="date" class="sod-field input" value="{{ old('support_end_date', $editingServiceOrder?->support_end_date?->format('Y-m-d') ?? date('Y-m-d', strtotime('+1 year'))) }}" required />
                        </div>

                        <div class="sod-field">
                            <label for="cas_accredited">CAS Accredited <span class="required">*</span></label>
                            <select id="cas_accredited" name="cas_accredited" class="sod-field input" required>
                                <option value="">Select...</option>
                                <option value="1" {{ old('cas_accredited', $editingServiceOrder === null ? null : (int) $editingServiceOrder->cas_accredited) == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('cas_accredited', $editingServiceOrder === null ? null : (int) $editingServiceOrder->cas_accredited) == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="sod-field">
                            <label for="support_inclusion">Support Inclusion <span class="required">*</span></label>
                            <textarea id="support_inclusion" name="support_inclusion" class="sod-field input" rows="4" placeholder="Enter support inclusion" required>{{ old('support_inclusion', $editingServiceOrder?->support_inclusion) }}</textarea>
                        </div>

                        <div class="sod-field">
                            <label for="package_selection_display">Package <span class="required">*</span></label>
                            <div class="sod-selection-row">
                                <textarea id="package_selection_display" class="sod-field input" rows="3" placeholder="No packages selected" readonly aria-describedby="package_selection_help"></textarea>
                                <button id="selectPackageButton" type="button" class="btn-secondary" onclick="openPackageModal()">Select Package</button>
                            </div>
                            <div id="package_ids_container">
                                @foreach ($selectedPackageIds as $selectedPackageId)
                                    <input type="hidden" name="package_ids[]" value="{{ $selectedPackageId }}">
                                @endforeach
                            </div>
                            <small id="package_selection_help" style="color: #6b7280;">Choose one or more packages from the popup.</small>
                        </div>
                    </div>
                </div>
            </div>

            <section class="man-days-section" aria-labelledby="manDaysTitle">
                <h2 class="man-days-header" id="manDaysTitle">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                    </svg>
                    <span>Man-days</span>
                </h2>
                <div class="man-days-table">
                    <label class="man-days-col" for="man_days">
                        <span class="man-days-col-header">Man-days (Entitled)</span>
                        <input type="number" id="man_days" name="man_days" min="0" step="1" value="{{ old('man_days', $editingServiceOrder?->man_days ?? 0) }}">
                    </label>
                    <label class="man-days-col" for="unused_man_days">
                        <span class="man-days-col-header">Un-Used Man-days (Prev.Yrs)</span>
                        <input type="number" id="unused_man_days" name="unused_man_days" min="0" step="1" value="{{ old('unused_man_days', $editingServiceOrder?->unused_man_days ?? 0) }}" readonly>
                    </label>
                    <label class="man-days-col" for="used_man_days">
                        <span class="man-days-col-header">Used Man-days</span>
                        <input type="number" id="used_man_days" name="used_man_days" min="0" step="1" value="{{ old('used_man_days', $editingServiceOrder?->used_man_days ?? 0) }}">
                    </label>
                    <label class="man-days-col" for="remainingDisplay">
                        <span class="man-days-col-header">Remaining Man-days</span>
                        <input type="text" id="remainingDisplay" value="0.00" readonly aria-live="polite">
                    </label>
                </div>
            </section>

            <section class="license-section" aria-labelledby="licenseTitle">
                <h2 class="license-header" id="licenseTitle">
                    <svg fill="currentColor" viewBox="0 0 20 20" style="width: 18px; height: 18px;" aria-hidden="true"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H2a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V5a1 1 0 100-2h-1.172a2 2 0 00-1.414.586L9 9.83 6.586 7.414A2 2 0 005.172 3H4zm5 9a1 1 0 100-2 1 1 0 000 2zm0 2a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                    <span>License</span>
                </h2>
                <div class="license-cards">
                    @foreach ($licenseTypes as $license)
                        <div class="license-card">
                            <div class="license-card-icon" aria-hidden="true">{{ substr($license, 0, 1) }}</div>
                            <div class="license-card-name">{{ $license }}</div>
                            @if ($license === 'MSSQL')
                                <input type="number" min="0" readonly class="license-card-quantity license-mssql" name="mssql" value="{{ old('mssql', $editingServiceOrder?->mssql ?? 0) }}" aria-label="{{ $license }} licenses">
                            @elseif ($license === 'Professional')
                                <input type="number" min="0" class="license-card-quantity license-input" name="professional" value="{{ old('professional', $editingServiceOrder?->professional ?? 0) }}" aria-label="{{ $license }} licenses">
                            @elseif ($license === 'Limited')
                                <input type="number" min="0" class="license-card-quantity license-input" name="limited" value="{{ old('limited', $editingServiceOrder?->limited ?? 0) }}" aria-label="{{ $license }} licenses">
                            @elseif ($license === 'Indirect')
                                <input type="number" min="0" class="license-card-quantity license-input" name="indirect" value="{{ old('indirect', $editingServiceOrder?->indirect ?? 0) }}" aria-label="{{ $license }} licenses">
                            @elseif ($license === 'Starter')
                                <input type="number" min="0" class="license-card-quantity license-input" name="starter" value="{{ old('starter', $editingServiceOrder?->starter ?? 0) }}" aria-label="{{ $license }} licenses">
                            @else
                                <input type="number" min="0" class="license-card-quantity license-input" name="{{ strtolower($license) }}" value="0" aria-label="{{ $license }} licenses">
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <div id="packageModal" class="sod-modal" role="dialog" aria-modal="true" aria-labelledby="packageModalTitle">
                <div class="sod-modal-dialog">
                    <div class="sod-modal-header">
                        <span id="packageModalTitle">Select Package</span>
                        <button type="button" class="btn-secondary" onclick="closePackageModal()" aria-label="Close package selection">&times;</button>
                    </div>
                    <div class="sod-modal-body">
                        @forelse ($packages as $package)
                            <label class="sod-modal-option">
                                <input type="checkbox" value="{{ $package->id }}">
                                <span>{{ $package->package }}</span>
                            </label>
                        @empty
                            <p style="margin: 0; color: #6b7280;">No packages are available.</p>
                        @endforelse
                    </div>
                    <div class="sod-modal-actions">
                        <button type="button" class="btn-secondary" onclick="closePackageModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="applySelectedPackages()">Apply Selection</button>
                    </div>
                </div>
            </div>

            <!-- Notes & Summary Sections -->
            <div class="bottom-container">
                <div class="notes-section">
                    <div class="notes-fields">
                        <label class="notes-field"><span>Notes</span><textarea name="notes" placeholder="Enter notes (optional)...">{{ old('notes', $editingServiceOrder?->notes) }}</textarea></label>
                        <label class="notes-field"><span>Attach Service Order</span><input class="service-order-file" name="attach_service_order" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">@if ($editingServiceOrder?->attach_service_order_original_name)<span class="service-order-file-current">Current: {{ $editingServiceOrder->attach_service_order_original_name }}</span>@endif</label>
                    </div>
                    <div class="notes-actions">
                        <a href="{{ $editingServiceOrder ? route('service-order-details.detail', ['service_order_id' => $editingServiceOrder->id]) : route('service-order-details.detail') }}" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{ $editingServiceOrder ? 'Update Service Order' : 'Save' }}</button>
                    </div>
                </div>

                <div class="summary-section">
                    <h3>Summary</h3>
                    <div class="summary-item">
                        <span class="summary-label">Entitled Man-days</span>
                        <span class="summary-value" id="summaryEntitled">0</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Used Man-days</span>
                        <span class="summary-value" id="summaryUsed">0</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Remaining Man-days</span>
                        <span class="summary-value positive" id="summaryRemaining">0</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Client data for auto-population
        const clientData = {{ Illuminate\Support\Js::from($clientData) }};
        
        const products = {
            @foreach ($sapProducts as $product)
            "{{ $product->id }}": "{{ $product->sap_product }}",
            @endforeach
        };
        const packages = @json($packages->pluck('package', 'id'));
        const isEditingServiceOrder = @json((bool) $editingServiceOrder);
        let clientSelectionInitialized = false;

        // Update remaining man-days calculation
        const manDaysInput = document.getElementById('man_days');
        const unusedManDaysInput = document.getElementById('unused_man_days');
        const usedManDaysInput = document.getElementById('used_man_days');
        const remainingDisplay = document.getElementById('remainingDisplay');
        const clientSelect = document.getElementById('client_id');
        const industryDisplay = document.getElementById('industry_business_type_display');
        const industryHidden = document.getElementById('industry_business_type_id');
        const softwareVersionDisplay = document.getElementById('software_version_display');
        const softwareVersionHidden = document.getElementById('software_version');
        const patchOrFpDisplay = document.getElementById('patch_or_fp_display');
        const patchOrFpHidden = document.getElementById('patch_or_fp');
        const productUsedDisplay = document.getElementById('product_used_display');
        const productHiddenContainer = document.getElementById('sap_product_ids_container');
        const packageSelectionDisplay = document.getElementById('package_selection_display');
        const packageHiddenContainer = document.getElementById('package_ids_container');
        const packageModal = document.getElementById('packageModal');
        const selectPackageButton = document.getElementById('selectPackageButton');
        const serviceOrderForm = document.getElementById('serviceOrderForm');
        const supportStartDateInput = document.getElementById('support_start_date');
        const supportEndDateInput = document.getElementById('support_end_date');

        function updateClientSelection() {
            const selectedClientId = clientSelect.value;
            const client = clientData[selectedClientId];
            const preserveStoredUnusedManDays = isEditingServiceOrder && !clientSelectionInitialized;
            
            if (!client) {
                industryDisplay.value = '';
                industryHidden.value = '';
                softwareVersionDisplay.value = softwareVersionHidden.value || '';
                softwareVersionHidden.value = softwareVersionHidden.value || '';
                patchOrFpDisplay.value = patchOrFpHidden.value || '';
                patchOrFpHidden.value = patchOrFpHidden.value || '';
                productHiddenContainer.innerHTML = '';
                productUsedDisplay.value = '';
                if (!preserveStoredUnusedManDays) {
                    unusedManDaysInput.value = 0;
                }
                updateManDays();
                clientSelectionInitialized = true;
                return;
            }

            const industryName = client.industry_name || '';
            industryDisplay.value = industryName;
            industryHidden.value = client.industry_business_type_id || '';
            softwareVersionDisplay.value = client.version_number || '';
            softwareVersionHidden.value = client.version_number || '';
            patchOrFpDisplay.value = client.patch_or_fp || '';
            patchOrFpHidden.value = client.patch_or_fp || '';
            if (!preserveStoredUnusedManDays) {
                unusedManDaysInput.value = client.unused_man_days || 0;
            }
            productHiddenContainer.innerHTML = '';
            (client.products || []).forEach(product => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'sap_product_ids[]';
                hiddenInput.value = product.id;
                productHiddenContainer.appendChild(hiddenInput);
            });
            productUsedDisplay.value = (client.products || []).map(product => product.name).join('\n');
            clientSelectionInitialized = true;
        }

        function updateManDays() {
            const entitled = Math.max(0, Number(manDaysInput.value) || 0);
            const unused = Math.max(0, Number(unusedManDaysInput.value) || 0);
            const used = Math.max(0, Number(usedManDaysInput.value) || 0);
            const remaining = Math.max(0, entitled + unused - used);
            
            remainingDisplay.value = remaining.toFixed(2);
            
            // Summary is a live readout of the Man-days fields above.
            document.getElementById('summaryEntitled').textContent = entitled.toFixed(2);
            document.getElementById('summaryUsed').textContent = used.toFixed(2);
            document.getElementById('summaryRemaining').textContent = remaining.toFixed(2);
        }

        function updateProductDisplay() {
            const selectedIds = Array.from(productHiddenContainer.querySelectorAll('input[name="sap_product_ids[]"]'))
                .map(input => input.value)
                .filter(Boolean);

            const selectedNames = selectedIds.map(id => products[id]).filter(Boolean);
            productUsedDisplay.value = selectedNames.join('\n');
        }

        function selectedPackageIds() {
            return Array.from(packageHiddenContainer.querySelectorAll('input[name="package_ids[]"]'))
                .map(input => input.value)
                .filter(Boolean);
        }

        function updatePackageDisplay() {
            const selectedNames = selectedPackageIds().map(id => packages[id]).filter(Boolean);
            packageSelectionDisplay.value = selectedNames.join('\n');
            packageSelectionDisplay.placeholder = selectedNames.length ? '' : 'No packages selected';
            packageSelectionDisplay.removeAttribute('aria-invalid');
            updatePackageManDays(selectedNames);
        }

        function updatePackageManDays(selectedNames) {
            const normalizedNames = selectedNames.map(name => name.toLowerCase());
            let entitled = null;

            if (normalizedNames.some(name => name.includes('premium helpdesk support plan'))) {
                entitled = 12;
            } else if (normalizedNames.some(name => name.includes('standard helpdesk support plan'))) {
                entitled = 6;
            } else if (normalizedNames.some(name => name.includes('basic helpdesk support plan'))) {
                entitled = 0;
            }

            if (entitled !== null) {
                manDaysInput.value = entitled;
                updateManDays();
            }
        }

        function openPackageModal() {
            const selectedIds = selectedPackageIds();

            packageModal.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = selectedIds.includes(checkbox.value);
            });

            packageModal.classList.add('active');
            packageModal.querySelector('input[type="checkbox"]')?.focus();
        }

        function closePackageModal() {
            packageModal.classList.remove('active');
            selectPackageButton.focus();
        }

        function applySelectedPackages() {
            const selectedIds = Array.from(packageModal.querySelectorAll('input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.value);

            packageHiddenContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'package_ids[]';
                hiddenInput.value = id;
                packageHiddenContainer.appendChild(hiddenInput);
            });

            updatePackageDisplay();
            closePackageModal();
        }

        function updateSupportEndDate() {
            const startDateValue = supportStartDateInput.value;

            if (!startDateValue) {
                return;
            }

            const startDate = new Date(startDateValue + 'T00:00:00');
            const endDate = new Date(startDate);
            endDate.setFullYear(endDate.getFullYear() + 1);

            const formatted = endDate.toISOString().split('T')[0];
            supportEndDateInput.value = formatted;
        }

        // Event listeners
        clientSelect.addEventListener('change', updateClientSelection);
        manDaysInput.addEventListener('input', updateManDays);
        usedManDaysInput.addEventListener('input', updateManDays);
        supportStartDateInput.addEventListener('change', updateSupportEndDate);
        serviceOrderForm.addEventListener('submit', event => {
            if (selectedPackageIds().length) {
                return;
            }

            event.preventDefault();
            packageSelectionDisplay.setAttribute('aria-invalid', 'true');
            openPackageModal();
        });
        packageModal.addEventListener('click', event => {
            if (event.target === packageModal) {
                closePackageModal();
            }
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && packageModal.classList.contains('active')) {
                closePackageModal();
            }
        });

        // MSSQL calculation: MSSQL = Professional + Limited + Starter
        function updateMssqlLicense() {
            const prof = parseInt(document.querySelector('input[name="professional"]')?.value || 0);
            const limited = parseInt(document.querySelector('input[name="limited"]')?.value || 0);
            const starter = parseInt(document.querySelector('input[name="starter"]')?.value || 0);
            const total = Math.max(0, prof + limited + starter);
            const mssqlInput = document.querySelector('input[name="mssql"]');
            if (mssqlInput) {
                mssqlInput.value = total;
            }
        }

        // Wire license input events
        document.querySelectorAll('.license-input').forEach(input => {
            input.addEventListener('input', updateMssqlLicense);
        });

        // Initialize on load
        updateClientSelection();
        updateManDays();
        updateProductDisplay();
        updatePackageDisplay();
        if (!isEditingServiceOrder) {
            updateSupportEndDate();
        }
        updateMssqlLicense();
    </script>
@endsection

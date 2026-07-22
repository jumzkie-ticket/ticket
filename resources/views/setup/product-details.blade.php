@extends('layouts.app-shell')

@section('title', 'Product Details')
@section('page-title', 'Product Details')
@section('page-subtitle', 'Maintain helpdesk support package pricing and coverage.')

@push('styles')
    <style>
        .product-details-page {
            display: grid;
            gap: 18px;
        }

        .product-analytics {
            display: grid;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
            padding: 18px;
            gap: 16px;
        }

        .product-panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .product-analytics-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .product-analytics-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: var(--ink);
            font-size: 16px;
            font-weight: 900;
        }

        .product-analytics-title svg {
            width: 18px;
            height: 18px;
            color: var(--blue);
        }

        .product-analytics-note {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .product-chart {
            display: grid;
            gap: 12px;
        }

        .product-bar-row {
            display: grid;
            grid-template-columns: minmax(170px, 260px) minmax(0, 1fr) minmax(118px, auto);
            align-items: center;
            gap: 14px;
            min-height: 62px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fbfdff;
        }

        .product-bar-label {
            display: block;
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .product-bar-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 7px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 850;
            text-transform: uppercase;
        }

        .product-bar-track {
            height: 16px;
            overflow: hidden;
            border-radius: 999px;
            background: #e8eef8;
        }

        .product-bar-fill {
            width: var(--bar-width);
            min-width: 0;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--blue), #2fbf71);
        }

        .product-bar-value {
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .product-bar-support-fee {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .product-chart-empty {
            min-height: 110px;
            display: grid;
            place-items: center;
            border: 1px dashed #cbd9ee;
            border-radius: 8px;
            background: #fbfdff;
            color: var(--muted);
            font-size: 12px;
            font-weight: 850;
            text-align: center;
        }

        .product-chart-badge {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .product-chart-badge svg {
            width: 18px;
            height: 18px;
        }

        .product-layout {
            display: grid;
            grid-template-columns: 380px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .product-panel-body {
            padding: 18px;
        }

        .product-panel-header {
            padding: 18px;
            border-bottom: 1px solid var(--line);
        }

        .product-panel-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: var(--ink);
            font-size: 16px;
            font-weight: 900;
        }

        .product-panel-title svg {
            width: 18px;
            height: 18px;
            color: var(--blue);
        }

        .product-panel-copy,
        .detail-row span {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }

        .product-form {
            display: grid;
            gap: 12px;
        }

        .product-label {
            display: grid;
            gap: 7px;
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .product-input {
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

        .product-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .number-prompt {
            display: none;
            color: var(--red);
            font-size: 11px;
            font-weight: 900;
        }

        .number-prompt.show {
            display: block;
        }

        .product-button,
        .product-link-button {
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

        .product-link-button {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .product-button svg,
        .product-link-button svg {
            width: 15px;
            height: 15px;
        }

        .product-flash {
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

        .product-errors {
            margin: 0;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .detail-stack {
            display: grid;
            gap: 12px;
        }

        .detail-row {
            display: grid;
            gap: 4px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .detail-row strong {
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.4;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-table th,
        .product-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: middle;
        }

        .product-table th {
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .product-table td {
            color: var(--ink);
            font-size: 12px;
            font-weight: 800;
        }

        .product-name {
            color: var(--ink);
            font-weight: 900;
        }

        .product-code {
            display: inline-grid;
            place-items: center;
            min-height: 26px;
            padding: 0 9px;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 11px;
            font-weight: 900;
        }

        .product-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .product-action-form {
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

        .product-empty {
            padding: 34px 18px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }

        @media (max-width: 1180px) {
            .product-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .product-analytics-header,
            .product-bar-row {
                align-items: stretch;
                grid-template-columns: 1fr;
            }

            .product-analytics-header {
                display: grid;
            }

            .product-chart-badge {
                display: none;
            }

            .product-bar-value {
                text-align: left;
            }

            .product-panel {
                overflow-x: auto;
            }

            .product-table {
                min-width: 780px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="product-details-page">
        <x-status-prompt />

        @if ($errors->any())
            <ul class="product-errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        {{-- Product Analytics --}}
        <div class="product-analytics">
            <div class="product-analytics-header">
                <h3 class="product-analytics-title"><svg><use href="#icon-graph"></use></svg><span>Product Analytics</span></h3>
                <p class="product-analytics-note">Total Amount VAT Inc per Product</p>
            </div>

            @if ($productDetails->isEmpty())
                <div class="product-chart">
                    <div class="product-chart-empty">No product analytics yet</div>
                    <div class="product-chart-meta">
                        <div><strong>Total Amount VAT Inc</strong></div>
                        <div>PHP 0.00</div>
                    </div>
                </div>
            @else
                @php
                    $max = $productDetails->max('total_amount_vat_inc') ?: 1;
                @endphp

                <div class="product-chart">
                    @foreach ($productDetails as $detail)
                        @php
                            $width = ($detail->total_amount_vat_inc / $max) * 100;
                            $widthStr = rtrim(rtrim(number_format($width, 2), '0'), '.');
                        @endphp
                        <div class="product-bar-row" title="{{ $detail->helpdesk_support_packages }}">
                            <div>
                                <div class="product-bar-label">{{ $detail->helpdesk_support_packages }}</div>
                                <div class="product-bar-meta">
                                    <span>{{ $detail->man_days }} man-days</span>
                                    <span>{{ $detail->helpdesk_coverage_months }} months</span>
                                </div>
                            </div>
                            <div>
                                    <div class="product-bar-track">
                                    <div class="product-bar-fill" style="--bar-width: {{ $widthStr }}%;"></div>
                                </div>
                                <div class="product-bar-support-fee">Fee PHP {{ number_format((float) $detail->helpdesk_support_fee, 2) }}</div>
                            </div>
                            <div class="product-bar-value">PHP {{ number_format((float) $detail->total_amount_vat_inc, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="product-layout">
            <section class="product-panel" aria-labelledby="product-detail-form-title">
                <div class="product-panel-body">
                    @if ($panelMode === 'view' && $selectedProductDetail)
                        <h2 class="product-panel-title" id="product-detail-form-title"><svg><use href="#icon-eye"></use></svg><span>View Product Detail</span></h2>
                        <div class="detail-stack" style="margin-top:16px">
                            <div class="detail-row">
                                <span>Helpdesk Support Packages</span>
                                <strong>{{ $selectedProductDetail->helpdesk_support_packages }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Man-days</span>
                                <strong>{{ $selectedProductDetail->man_days }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Helpdesk Coverage in months</span>
                                <strong>{{ $selectedProductDetail->helpdesk_coverage_months }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Helpdesk Support Fee</span>
                                <strong>PHP {{ number_format((float) $selectedProductDetail->helpdesk_support_fee, 2) }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Total Amount VAT Inc</span>
                                <strong>PHP {{ number_format((float) $selectedProductDetail->total_amount_vat_inc, 2) }}</strong>
                            </div>
                            <a class="product-link-button" href="{{ route('product-details') }}">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add New Product Detail</span>
                            </a>
                        </div>
                    @else
                        @php
                            $editing = $panelMode === 'edit' && $selectedProductDetail;
                            $formAction = $editing
                                ? route('product-details.update', $selectedProductDetail)
                                : route('product-details.store');
                        @endphp

                        <h2 class="product-panel-title" id="product-detail-form-title">
                            <svg><use href="{{ $editing ? '#icon-pencil' : '#icon-plus' }}"></use></svg>
                            <span>{{ $editing ? 'Edit Product Detail' : 'Add Product Detail' }}</span>
                        </h2>
                        <p class="product-panel-copy">Configure package coverage and helpdesk fee amounts for product support setup.</p>

                        <form class="product-form" method="POST" action="{{ $formAction }}" style="margin-top:16px">
                            @csrf
                            @if ($editing)
                                @method('PUT')
                            @endif

                            <label class="product-label">
                                <span>Helpdesk Support Packages</span>
                                <input class="product-input" name="helpdesk_support_packages" type="text" value="{{ old('helpdesk_support_packages', $selectedProductDetail?->helpdesk_support_packages) }}" placeholder="Enter support package" required>
                            </label>

                            <label class="product-label">
                                <span>Man-days</span>
                                <input class="product-input integer-input" name="man_days" type="text" inputmode="numeric" value="{{ old('man_days', $selectedProductDetail?->man_days) }}" placeholder="Enter man-days" required>
                                <span class="number-prompt">Only numbers are allowed.</span>
                            </label>

                            <label class="product-label">
                                <span>Helpdesk Coverage in months</span>
                                <input class="product-input" id="helpdesk_coverage_months" name="helpdesk_coverage_months" type="text" inputmode="numeric" value="{{ old('helpdesk_coverage_months', $selectedProductDetail?->helpdesk_coverage_months) }}" placeholder="Enter number of months" required>
                                <span class="number-prompt" id="coverage-prompt">Only numbers are allowed.</span>
                            </label>

                            <label class="product-label">
                                <span>Helpdesk Support Fee</span>
                                <input class="product-input currency-input" name="helpdesk_support_fee" type="text" inputmode="decimal" value="{{ old('helpdesk_support_fee', $selectedProductDetail ? 'PHP '.number_format((float) $selectedProductDetail->helpdesk_support_fee, 2) : '') }}" placeholder="PHP 0.00" required>
                                <span class="number-prompt">Only numbers are allowed.</span>
                            </label>

                            <label class="product-label">
                                <span>Total Amount VAT Inc</span>
                                <input class="product-input currency-input" name="total_amount_vat_inc" type="text" inputmode="decimal" value="{{ old('total_amount_vat_inc', $selectedProductDetail ? 'PHP '.number_format((float) $selectedProductDetail->total_amount_vat_inc, 2) : '') }}" placeholder="PHP 0.00" required>
                                <span class="number-prompt">Only numbers are allowed.</span>
                            </label>

                            <button class="product-button" type="submit">
                                <svg><use href="#icon-check-circle"></use></svg>
                                <span>{{ $editing ? 'Save Changes' : 'Save Product Detail' }}</span>
                            </button>

                            @if ($editing)
                                <a class="product-link-button" href="{{ route('product-details') }}">Cancel</a>
                            @endif
                        </form>
                    @endif
                </div>
            </section>

            <section class="product-panel" aria-labelledby="product-details-list-title">
                <div class="product-panel-header">
                    <h2 class="product-panel-title" id="product-details-list-title"><svg><use href="#icon-list"></use></svg><span>Product Detail List</span></h2>
                    <p class="product-panel-copy">Configured helpdesk support packages and VAT-inclusive totals.</p>
                </div>

                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Helpdesk Support Packages</th>
                            <th>Man-days</th>
                            <th>Coverage Months</th>
                            <th>Support Fee</th>
                            <th>Total Amount VAT Inc</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productDetails as $detail)
                            <tr>
                                <td class="product-name">{{ $detail->helpdesk_support_packages }}</td>
                                <td><span class="product-code">{{ $detail->man_days }}</span></td>
                                <td><span class="product-code">{{ $detail->helpdesk_coverage_months }}</span></td>
                                <td>PHP {{ number_format((float) $detail->helpdesk_support_fee, 2) }}</td>
                                <td>PHP {{ number_format((float) $detail->total_amount_vat_inc, 2) }}</td>
                                <td>
                                    <div class="product-actions">
                                        <a class="icon-action" href="{{ route('product-details', ['view' => $detail->id]) }}" title="View" aria-label="View {{ $detail->helpdesk_support_packages }}">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </a>
                                        <a class="icon-action" href="{{ route('product-details', ['edit' => $detail->id]) }}" title="Edit" aria-label="Edit {{ $detail->helpdesk_support_packages }}">
                                            <svg><use href="#icon-pencil"></use></svg>
                                        </a>
                                        <form class="product-action-form" method="POST" action="{{ route('product-details.destroy', $detail) }}" onsubmit="return confirm('Delete this product detail?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action danger" type="submit" title="Delete" aria-label="Delete {{ $detail->helpdesk_support_packages }}">
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="product-empty" colspan="6">No product details yet. Add a helpdesk support package to begin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const integerInputs = document.querySelectorAll('.integer-input, #helpdesk_coverage_months');
            const currencyInputs = document.querySelectorAll('.currency-input');

            const normalizeCurrencyValue = (value) => {
                const normalized = value.replace(/[^0-9.]/g, '');
                const firstDot = normalized.indexOf('.');
                return firstDot === -1
                    ? normalized
                    : `${normalized.slice(0, firstDot + 1)}${normalized.slice(firstDot + 1).replace(/\./g, '')}`;
            };

            const formatCurrency = (value) => {
                const cleaned = normalizeCurrencyValue(value);

                if (cleaned === '') {
                    return '';
                }

                const [wholePart, decimalPart = ''] = cleaned.split('.');
                const whole = Number(wholePart || 0).toLocaleString('en-US');

                if (cleaned.includes('.')) {
                    return `PHP ${whole}.${decimalPart.slice(0, 2)}`;
                }

                return `PHP ${whole}`;
            };

            const showPrompt = (input) => {
                const prompt = input.closest('.product-label')?.querySelector('.number-prompt');

                if (!prompt) {
                    return;
                }

                prompt.classList.add('show');
                window.setTimeout(() => prompt.classList.remove('show'), 2200);
            };

            integerInputs.forEach((input) => {
                input.addEventListener('input', () => {
                    const cleanValue = input.value.replace(/\D/g, '');

                    if (input.value !== cleanValue) {
                        input.value = cleanValue;
                        showPrompt(input);
                    }
                });
            });

            currencyInputs.forEach((input) => {
                input.addEventListener('focus', () => {
                    input.value = normalizeCurrencyValue(input.value);
                });

                input.addEventListener('input', () => {
                    const cleanValue = normalizeCurrencyValue(input.value);

                    if (input.value !== cleanValue) {
                        input.value = cleanValue;
                        showPrompt(input);
                    }
                });

                input.addEventListener('blur', () => {
                    input.value = formatCurrency(input.value);
                });

                input.form?.addEventListener('submit', () => {
                    input.value = normalizeCurrencyValue(input.value);
                });
            });
        })();
    </script>
@endpush

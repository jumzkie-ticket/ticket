@extends('layouts.app-shell')

@section('title', 'Product Used')
@section('page-title', 'Product Used')
@section('page-subtitle', 'Maintain the SAP products used by registered clients.')

@push('styles')
    <style>
        .lookup-page {
            display: grid;
            gap: 18px;
        }

        .lookup-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .analytics-card,
        .lookup-panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .analytics-card {
            min-height: 104px;
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            align-items: center;
            gap: 13px;
            padding: 16px;
        }

        .analytics-icon {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .analytics-icon.green {
            background: var(--green-soft);
            color: var(--green);
        }

        .analytics-icon.amber {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .analytics-icon.violet {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .analytics-icon svg {
            width: 18px;
            height: 18px;
        }

        .analytics-label {
            margin: 0 0 5px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .analytics-value {
            margin: 0;
            color: var(--ink);
            font-size: 22px;
            font-weight: 900;
            line-height: 1;
        }

        .lookup-grid {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .lookup-panel-body {
            padding: 18px;
        }

        .lookup-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 14px;
            color: var(--ink);
            font-size: 16px;
            font-weight: 900;
        }

        .lookup-title svg {
            width: 18px;
            height: 18px;
            color: var(--blue);
        }

        .lookup-copy,
        .detail-row span {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }

        .lookup-copy {
            margin-bottom: 16px;
        }

        .lookup-form {
            display: grid;
            gap: 12px;
        }

        .lookup-label {
            display: grid;
            gap: 7px;
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .lookup-input {
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

        .lookup-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .lookup-button,
        .lookup-link-button {
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

        .lookup-link-button {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .lookup-button svg,
        .lookup-link-button svg {
            width: 15px;
            height: 15px;
        }

        .lookup-flash {
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

        .lookup-errors {
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

        .lookup-table {
            width: 100%;
            border-collapse: collapse;
        }

        .lookup-table th,
        .lookup-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
        }

        .lookup-table th {
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .lookup-table td {
            color: var(--ink);
            font-size: 12px;
            font-weight: 800;
            vertical-align: middle;
        }

        .lookup-count {
            width: fit-content;
            min-width: 34px;
            display: inline-grid;
            place-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 11px;
            font-weight: 900;
        }

        .lookup-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .lookup-action-form {
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

        .icon-action:disabled {
            cursor: not-allowed;
            opacity: .45;
        }

        .lookup-empty {
            padding: 26px 16px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }

        @media (max-width: 1180px) {
            .lookup-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 960px) {
            .lookup-grid,
            .lookup-analytics {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="lookup-page">
        <x-status-prompt />

        @if ($errors->any())
            <ul class="lookup-errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <section class="lookup-analytics" aria-label="Product analytics">
            <article class="analytics-card">
                <span class="analytics-icon"><svg><use href="#icon-book"></use></svg></span>
                <div>
                    <p class="analytics-label">Total Products</p>
                    <p class="analytics-value">{{ $analytics['total'] }}</p>
                </div>
            </article>
            <article class="analytics-card">
                <span class="analytics-icon green"><svg><use href="#icon-check-circle"></use></svg></span>
                <div>
                    <p class="analytics-label">In Use</p>
                    <p class="analytics-value">{{ $analytics['in_use'] }}</p>
                </div>
            </article>
            <article class="analytics-card">
                <span class="analytics-icon amber"><svg><use href="#icon-inbox"></use></svg></span>
                <div>
                    <p class="analytics-label">Unused</p>
                    <p class="analytics-value">{{ $analytics['unused'] }}</p>
                </div>
            </article>
            <article class="analytics-card">
                <span class="analytics-icon violet"><svg><use href="#icon-client"></use></svg></span>
                <div>
                    <p class="analytics-label">Related Clients</p>
                    <p class="analytics-value">{{ $analytics['related_clients'] }}</p>
                </div>
            </article>
        </section>

        <div class="lookup-grid">
            <section class="lookup-panel" aria-labelledby="product-form-title">
                <div class="lookup-panel-body">
                    @if ($panelMode === 'view' && $selectedProduct)
                        <h2 class="lookup-title" id="product-form-title"><svg><use href="#icon-eye"></use></svg><span>View Product</span></h2>
                        <div class="detail-stack">
                            <div class="detail-row">
                                <span>SAP Product</span>
                                <strong>{{ $selectedProduct->sap_product }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Related Clients</span>
                                <strong>{{ $selectedProduct->clients_count }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Created</span>
                                <strong>{{ $selectedProduct->created_at?->format('F j, Y g:i A') }}</strong>
                            </div>
                            <a class="lookup-link-button" href="{{ route('sap-products.index') }}">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add New Product</span>
                            </a>
                        </div>
                    @elseif ($panelMode === 'edit' && $selectedProduct)
                        <h2 class="lookup-title" id="product-form-title"><svg><use href="#icon-pencil"></use></svg><span>Edit Product</span></h2>
                        <form class="lookup-form" method="POST" action="{{ route('sap-products.update', $selectedProduct) }}">
                            @csrf
                            @method('PUT')
                            <label class="lookup-label">
                                <span>SAP Product</span>
                                <input class="lookup-input" name="sap_product" type="text" value="{{ old('sap_product', $selectedProduct->sap_product) }}" placeholder="Enter SAP product" required>
                            </label>
                            <button class="lookup-button" type="submit">
                                <svg><use href="#icon-check-circle"></use></svg>
                                <span>Save Changes</span>
                            </button>
                            <a class="lookup-link-button" href="{{ route('sap-products.index') }}">Cancel</a>
                        </form>
                    @else
                        <h2 class="lookup-title" id="product-form-title"><svg><use href="#icon-book"></use></svg><span>Add Product</span></h2>
                        <p class="lookup-copy">These values populate the Product Used dropdown in Client Registration and are related to client records.</p>

                        <form class="lookup-form" method="POST" action="{{ route('sap-products.store') }}">
                            @csrf
                            <label class="lookup-label">
                                <span>SAP Product</span>
                                <input class="lookup-input" name="sap_product" type="text" value="{{ old('sap_product') }}" placeholder="Enter SAP product" required>
                            </label>
                            <button class="lookup-button" type="submit">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add Product</span>
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="lookup-panel" aria-labelledby="product-list-title">
                <div class="lookup-panel-body">
                    <h2 class="lookup-title" id="product-list-title"><svg><use href="#icon-client"></use></svg><span>Product List</span></h2>
                </div>

                <table class="lookup-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Related Clients</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sapProducts as $sapProduct)
                            <tr>
                                <td>{{ $sapProduct->sap_product }}</td>
                                <td><span class="lookup-count">{{ $sapProduct->clients_count }}</span></td>
                                <td>
                                    <div class="lookup-actions">
                                        <a class="icon-action" href="{{ route('sap-products.index', ['view' => $sapProduct->id]) }}" title="View" aria-label="View {{ $sapProduct->sap_product }}">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </a>
                                        <a class="icon-action" href="{{ route('sap-products.index', ['edit' => $sapProduct->id]) }}" title="Edit" aria-label="Edit {{ $sapProduct->sap_product }}">
                                            <svg><use href="#icon-pencil"></use></svg>
                                        </a>
                                        <form class="lookup-action-form" method="POST" action="{{ route('sap-products.destroy', $sapProduct) }}" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action danger" type="submit" title="{{ $sapProduct->clients_count > 0 ? 'Cannot delete while related clients exist' : 'Delete' }}" aria-label="Delete {{ $sapProduct->sap_product }}" @disabled($sapProduct->clients_count > 0)>
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="lookup-empty" colspan="3">No products yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <x-simple-pager :paginator="$sapProducts" />
            </section>
        </div>
    </div>
@endsection

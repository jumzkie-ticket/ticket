@extends('layouts.app-shell')

@section('title', 'Package')
@section('page-title', 'Package')
@section('page-subtitle', 'Setup / Package')

@push('styles')
    <style>
        .package-page {
            display: grid;
            gap: 18px;
        }

        .package-summary,
        .package-panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .package-summary {
            min-height: 96px;
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            align-items: center;
            gap: 13px;
            max-width: 280px;
            padding: 16px;
        }

        .package-summary-icon {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .package-summary-icon svg {
            width: 18px;
            height: 18px;
        }

        .package-summary-label {
            margin: 0 0 5px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .package-summary-value {
            margin: 0;
            color: var(--ink);
            font-size: 22px;
            font-weight: 900;
            line-height: 1;
        }

        .package-grid {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .package-panel-body {
            padding: 18px;
        }

        .package-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 14px;
            color: var(--ink);
            font-size: 16px;
            font-weight: 900;
        }

        .package-title svg {
            width: 18px;
            height: 18px;
            color: var(--blue);
        }

        .package-form,
        .package-detail {
            display: grid;
            gap: 12px;
        }

        .package-label,
        .package-detail-row {
            display: grid;
            gap: 7px;
        }

        .package-label {
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .package-input {
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

        .package-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .package-button,
        .package-link-button {
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

        .package-link-button {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .package-button svg,
        .package-link-button svg {
            width: 15px;
            height: 15px;
        }

        .package-flash {
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

        .package-errors {
            margin: 0;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .package-detail-row {
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .package-detail-row span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .package-detail-row strong {
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.4;
        }

        .package-table {
            width: 100%;
            border-collapse: collapse;
        }

        .package-table th,
        .package-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: middle;
        }

        .package-table th {
            color: var(--muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .package-table td {
            color: var(--ink);
            font-size: 12px;
            font-weight: 800;
        }

        .package-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .package-action-form {
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

        .package-empty {
            padding: 26px 16px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }

        @media (max-width: 960px) {
            .package-grid {
                grid-template-columns: 1fr;
            }

            .package-summary {
                max-width: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="package-page">
        <x-status-prompt />

        @if ($errors->any())
            <ul class="package-errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <section class="package-summary" aria-label="Package summary">
            <span class="package-summary-icon"><svg><use href="#icon-list"></use></svg></span>
            <div>
                <p class="package-summary-label">Total Packages</p>
                <p class="package-summary-value">{{ number_format($analytics['total']) }}</p>
            </div>
        </section>

        <div class="package-grid">
            <section class="package-panel" aria-labelledby="package-form-title">
                <div class="package-panel-body">
                    @if ($panelMode === 'view' && $selectedPackage)
                        <h2 class="package-title" id="package-form-title"><svg><use href="#icon-eye"></use></svg><span>View Package</span></h2>
                        <div class="package-detail">
                            <div class="package-detail-row">
                                <span>Package</span>
                                <strong>{{ $selectedPackage->package }}</strong>
                            </div>
                            <div class="package-detail-row">
                                <span>Created</span>
                                <strong>{{ $selectedPackage->created_at?->format('F j, Y g:i A') }}</strong>
                            </div>
                            <div class="package-detail-row">
                                <span>Updated</span>
                                <strong>{{ $selectedPackage->updated_at?->format('F j, Y g:i A') }}</strong>
                            </div>
                            <a class="package-link-button" href="{{ route('packages.index') }}">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add New Package</span>
                            </a>
                        </div>
                    @elseif ($panelMode === 'edit' && $selectedPackage)
                        <h2 class="package-title" id="package-form-title"><svg><use href="#icon-pencil"></use></svg><span>Edit Package</span></h2>
                        <form class="package-form" method="POST" action="{{ route('packages.update', $selectedPackage) }}">
                            @csrf
                            @method('PUT')
                            <label class="package-label">
                                <span>Package</span>
                                <input class="package-input" name="package" type="text" value="{{ old('package', $selectedPackage->package) }}" placeholder="Enter package" required>
                            </label>
                            <button class="package-button" type="submit">
                                <svg><use href="#icon-check-circle"></use></svg>
                                <span>Save Changes</span>
                            </button>
                            <a class="package-link-button" href="{{ route('packages.index') }}">Cancel</a>
                        </form>
                    @else
                        <h2 class="package-title" id="package-form-title"><svg><use href="#icon-list"></use></svg><span>Add Package</span></h2>
                        <form class="package-form" method="POST" action="{{ route('packages.store') }}">
                            @csrf
                            <label class="package-label">
                                <span>Package</span>
                                <input class="package-input" name="package" type="text" value="{{ old('package') }}" placeholder="Enter package" required>
                            </label>
                            <button class="package-button" type="submit">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add Package</span>
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="package-panel" aria-labelledby="package-list-title">
                <div class="package-panel-body">
                    <h2 class="package-title" id="package-list-title"><svg><use href="#icon-list"></use></svg><span>Package List</span></h2>
                </div>

                <table class="package-table">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packages as $package)
                            <tr>
                                <td>{{ $package->package }}</td>
                                <td>{{ $package->created_at?->format('M j, Y') }}</td>
                                <td>
                                    <div class="package-actions">
                                        <a class="icon-action" href="{{ route('packages.index', ['view' => $package->id]) }}" title="View" aria-label="View {{ $package->package }}">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </a>
                                        <a class="icon-action" href="{{ route('packages.index', ['edit' => $package->id]) }}" title="Edit" aria-label="Edit {{ $package->package }}">
                                            <svg><use href="#icon-pencil"></use></svg>
                                        </a>
                                        <form class="package-action-form" method="POST" action="{{ route('packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action danger" type="submit" title="Delete" aria-label="Delete {{ $package->package }}">
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="package-empty" colspan="3">No packages yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <x-simple-pager :paginator="$packages" />
            </section>
        </div>
    </div>
@endsection

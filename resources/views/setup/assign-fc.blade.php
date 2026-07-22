@extends('layouts.app-shell')

@section('title', 'Assign FC')
@section('page-title', 'Assign FC')
@section('page-subtitle', 'Maintain FC values assigned to registered clients.')

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
            height: 40px;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0;
            padding: 0 14px;
            border: 1px solid var(--blue);
            border-radius: 6px;
            background: var(--blue);
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            line-height: 1;
            text-decoration: none;
            vertical-align: middle;
        }

        .lookup-link-button {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .lookup-form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            min-height: 40px;
        }

        .lookup-button:hover,
        .lookup-button:focus-visible {
            filter: brightness(.94);
            outline: none;
        }

        .lookup-link-button:hover,
        .lookup-link-button:focus-visible {
            border-color: var(--blue);
            background: var(--blue-soft);
            color: var(--blue);
            outline: none;
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
            margin: 0 0 12px;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .lookup-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .lookup-table th,
        .lookup-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--line);
            text-align: left;
        }

        .lookup-table th {
            color: var(--muted);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .lookup-table td {
            color: var(--ink);
            font-weight: 700;
        }

        .lookup-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }

        .icon-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            color: var(--ink);
            background: var(--panel);
            border: 1px solid var(--line);
            text-decoration: none;
        }

        .icon-action svg {
            width: 14px;
            height: 14px;
        }

        .lookup-empty {
            padding: 16px 12px;
            color: var(--muted);
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="lookup-page">
        <div class="lookup-analytics">
            <div class="analytics-card">
                <div class="analytics-icon">
                    <svg><use href="#icon-list"></use></svg>
                </div>
                <div>
                    <p class="analytics-label">Total FC</p>
                    <p class="analytics-value">{{ number_format($analytics['total'] ?? 0) }}</p>
                </div>
            </div>
            <div class="analytics-card">
                <div class="analytics-icon green">
                    <svg><use href="#icon-list"></use></svg>
                </div>
                <div>
                    <p class="analytics-label">In Use</p>
                    <p class="analytics-value">{{ number_format($analytics['in_use'] ?? 0) }}</p>
                </div>
            </div>
            <div class="analytics-card">
                <div class="analytics-icon violet">
                    <svg><use href="#icon-list"></use></svg>
                </div>
                <div>
                    <p class="analytics-label">Unused</p>
                    <p class="analytics-value">{{ number_format($analytics['unused'] ?? 0) }}</p>
                </div>
            </div>
            <div class="analytics-card">
                <div class="analytics-icon amber">
                    <svg><use href="#icon-list"></use></svg>
                </div>
                <div>
                    <p class="analytics-label">Related Clients</p>
                    <p class="analytics-value">{{ number_format($analytics['related_clients'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="lookup-grid">
            <div class="lookup-panel">
                <div class="lookup-panel-body">
                    <h2 class="lookup-title" id="assign-fc-form-title"><svg><use href="#icon-pencil"></use></svg><span>@if($panelMode === 'edit') Edit @elseif($panelMode === 'view') View @else Add @endif Assign FC</span></h2>

                    @if ($errors->any())
                        <div class="lookup-errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <x-status-prompt />

                    <form class="lookup-form" method="POST" action="{{ $panelMode === 'edit' ? route('assign-fcs.update', $selected) : route('assign-fcs.store') }}">
                        @csrf
                        @if($panelMode === 'edit')
                            @method('PUT')
                        @endif

                        <label class="lookup-label">
                            <span>Assign FC</span>
                            <input class="lookup-input" name="assign_fc" type="text" value="{{ old('assign_fc', $selected->assign_fc ?? '') }}" placeholder="Enter assign FC" required>
                        </label>

                        <label class="lookup-label">
                            <span>Designation</span>
                            <input class="lookup-input" name="designation" type="text" value="{{ old('designation', $selected->designation ?? '') }}" placeholder="Enter designation" required>
                        </label>

                        <div class="lookup-form-actions">
                            <button class="lookup-button" type="submit"><svg><use href="#icon-check-circle"></use></svg><span>Save</span></button>
                            <a class="lookup-link-button" href="{{ route('assign-fcs.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lookup-panel">
                <div class="lookup-panel-body">
                    <h2 class="lookup-title"><svg><use href="#icon-list"></use></svg><span>Assign FC List</span></h2>

                    <table class="lookup-table">
                        <thead>
                            <tr>
                                <th>Assign FC</th>
                                <th>Designation</th>
                                <th>Clients</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignFcs as $fc)
                                <tr>
                                    <td>{{ $fc->assign_fc }}</td>
                                    <td>{{ $fc->designation ?: '—' }}</td>
                                    <td>{{ $fc->clients_count }}</td>
                                    <td>
                                        <div class="lookup-actions">
                                            <a class="icon-action" href="{{ route('assign-fcs.index', ['view' => $fc->id]) }}" title="View" aria-label="View {{ $fc->assign_fc }}">
                                                <svg><use href="#icon-eye"></use></svg>
                                            </a>
                                            <a class="icon-action" href="{{ route('assign-fcs.index', ['edit' => $fc->id]) }}" title="Edit" aria-label="Edit {{ $fc->assign_fc }}">
                                                <svg><use href="#icon-pencil"></use></svg>
                                            </a>
                                            <form method="POST" action="{{ route('assign-fcs.destroy', $fc) }}" onsubmit="return confirm('Delete this FC?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="icon-action" type="submit" title="Delete" aria-label="Delete {{ $fc->assign_fc }}">
                                                    <svg><use href="#icon-trash"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="lookup-empty" colspan="4">No FC values yet. Add one using the form.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <x-simple-pager :paginator="$assignFcs" />
                </div>
            </div>
        </div>
    </div>
@endsection

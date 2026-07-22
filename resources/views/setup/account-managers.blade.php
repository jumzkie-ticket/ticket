@extends('layouts.app-shell')

@section('title', 'Account Manager')
@section('page-title', 'Account Manager')
@section('page-subtitle', 'Maintain account managers assigned to registered clients.')

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
            vertical-align: middle;
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

        <section class="lookup-analytics" aria-label="Account Manager analytics">
            <article class="analytics-card">
                <span class="analytics-icon"><svg><use href="#icon-client"></use></svg></span>
                <div>
                    <p class="analytics-label">Total Managers</p>
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
                <span class="analytics-icon violet"><svg><use href="#icon-users"></use></svg></span>
                <div>
                    <p class="analytics-label">Related Clients</p>
                    <p class="analytics-value">{{ $analytics['related_clients'] }}</p>
                </div>
            </article>
        </section>

        <div class="lookup-grid">
            <section class="lookup-panel" aria-labelledby="account-manager-form-title">
                <div class="lookup-panel-body">
                    @if ($panelMode === 'view' && $selectedAccountManager)
                        <h2 class="lookup-title" id="account-manager-form-title"><svg><use href="#icon-eye"></use></svg><span>View Account Manager</span></h2>
                        <div class="detail-stack">
                            <div class="detail-row">
                                <span>Account Manager</span>
                                <strong>{{ $selectedAccountManager->account_manager }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Related Clients</span>
                                <strong>{{ $selectedAccountManager->clients_count }}</strong>
                            </div>
                            <div class="detail-row">
                                <span>Created</span>
                                <strong>{{ $selectedAccountManager->created_at?->format('F j, Y g:i A') }}</strong>
                            </div>
                            <a class="lookup-link-button" href="{{ route('account-managers.index') }}">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add New Account Manager</span>
                            </a>
                        </div>
                    @elseif ($panelMode === 'edit' && $selectedAccountManager)
                        <h2 class="lookup-title" id="account-manager-form-title"><svg><use href="#icon-pencil"></use></svg><span>Edit Account Manager</span></h2>
                        <form class="lookup-form" method="POST" action="{{ route('account-managers.update', $selectedAccountManager) }}">
                            @csrf
                            @method('PUT')
                            <label class="lookup-label">
                                <span>Account Manager</span>
                                <input class="lookup-input" name="account_manager" type="text" value="{{ old('account_manager', $selectedAccountManager->account_manager) }}" placeholder="Enter account manager" required>
                            </label>
                            <button class="lookup-button" type="submit">
                                <svg><use href="#icon-check-circle"></use></svg>
                                <span>Save Changes</span>
                            </button>
                            <a class="lookup-link-button" href="{{ route('account-managers.index') }}">Cancel</a>
                        </form>
                    @else
                        <h2 class="lookup-title" id="account-manager-form-title"><svg><use href="#icon-client"></use></svg><span>Add Account Manager</span></h2>
                        <p class="lookup-copy">These values populate the Account Manager dropdown in Client Registration and are related to client records.</p>

                        <form class="lookup-form" method="POST" action="{{ route('account-managers.store') }}">
                            @csrf
                            <label class="lookup-label">
                                <span>Account Manager</span>
                                <input class="lookup-input" name="account_manager" type="text" value="{{ old('account_manager') }}" placeholder="Enter account manager" required>
                            </label>
                            <button class="lookup-button" type="submit">
                                <svg><use href="#icon-plus"></use></svg>
                                <span>Add Account Manager</span>
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="lookup-panel" aria-labelledby="account-manager-list-title">
                <div class="lookup-panel-body">
                    <h2 class="lookup-title" id="account-manager-list-title"><svg><use href="#icon-list"></use></svg><span>Account Manager List</span></h2>
                </div>

                <table class="lookup-table">
                    <thead>
                        <tr>
                            <th>Account Manager</th>
                            <th>Related Clients</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accountManagers as $accountManager)
                            <tr>
                                <td>{{ $accountManager->account_manager }}</td>
                                <td><span class="lookup-count">{{ $accountManager->clients_count }}</span></td>
                                <td>
                                    <div class="lookup-actions">
                                        <a class="icon-action" href="{{ route('account-managers.index', ['view' => $accountManager->id]) }}" title="View" aria-label="View {{ $accountManager->account_manager }}">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </a>
                                        <a class="icon-action" href="{{ route('account-managers.index', ['edit' => $accountManager->id]) }}" title="Edit" aria-label="Edit {{ $accountManager->account_manager }}">
                                            <svg><use href="#icon-pencil"></use></svg>
                                        </a>
                                        <form class="lookup-action-form" method="POST" action="{{ route('account-managers.destroy', $accountManager) }}" onsubmit="return confirm('Delete this account manager?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action danger" type="submit" title="{{ $accountManager->clients_count > 0 ? 'Cannot delete while related clients exist' : 'Delete' }}" aria-label="Delete {{ $accountManager->account_manager }}" @disabled($accountManager->clients_count > 0)>
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="lookup-empty" colspan="3">No account managers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <x-simple-pager :paginator="$accountManagers" />
            </section>
        </div>
    </div>
@endsection

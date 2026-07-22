@extends('layouts.app-shell')

@section('title', 'User Registration')
@section('page-title', 'User Registration')
@section('page-subtitle', 'Create accounts and manage portal access from one workspace.')

@push('styles')
    <style>
        :root {
            --ink: #071b4d;
            --muted: #61708f;
            --line: #d8e2f2;
            --panel: #ffffff;
            --canvas: #f4f7fc;
            --blue: #1766ff;
            --navy: #0d377a;
            --blue-soft: #e8f0ff;
            --green: #20b96f;
            --green-soft: #dcf8e9;
            --violet: #765cff;
            --violet-soft: #eeeaff;
            --red: #e84d4d;
            --red-soft: #ffe3e0;
            --shadow: 0 16px 46px rgba(10, 33, 74, .08);
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-width: 320px;
            background: var(--canvas);
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--canvas);
            color: var(--ink);
            font-family: "Instrument Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 13px;
            letter-spacing: 0;
        }

        button,
        input,
        select {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        svg {
            display: block;
        }

        .user-registration-page {
            min-height: calc(100vh - 180px);
        }

        .shell {
            max-width: 1780px;
            margin: 0 auto;
        }

        .content-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--line);
        }

        .headline {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
        }

        .headline-icon,
        .stat-icon {
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .headline-icon {
            width: 42px;
            height: 42px;
        }

        .headline-icon svg {
            width: 22px;
            height: 22px;
        }

        h1 {
            margin: 0;
            color: #061845;
            font-size: 30px;
            line-height: 1.06;
            font-weight: 900;
        }

        .subtitle {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .button {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 15px;
            border: 1px solid transparent;
            border-radius: 7px;
            background: #ffffff;
            color: #153064;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .button svg {
            width: 17px;
            height: 17px;
        }

        .button-primary {
            background: var(--blue);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(23, 102, 255, .18);
        }

        .button-primary:hover,
        .button-primary:focus-visible {
            background: #0f55dc;
            outline: none;
        }

        .button-light {
            border-color: #ccd9ed;
            background: #ffffff;
            color: #17315f;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .user-chip {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            padding: 0 13px;
            border: 1px solid #ccd9ed;
            border-radius: 7px;
            background: #ffffff;
            color: #17315f;
            font-size: 12px;
            font-weight: 900;
        }

        .logout-form {
            margin: 0;
        }

        .button-danger {
            background: var(--red-soft);
            color: var(--red);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 16px;
            margin: 24px 0;
        }

        .stat-card,
        .directory,
        .modal-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .stat-card {
            min-height: 86px;
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            padding: 18px;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            color: var(--stat-color);
            background: var(--stat-bg);
        }

        .stat-icon svg {
            width: 17px;
            height: 17px;
        }

        .stat-value {
            margin: 0;
            color: #061845;
            font-size: 25px;
            line-height: 1;
            font-weight: 900;
        }

        .stat-label {
            margin: 6px 0 0;
            color: #52668d;
            font-size: 12px;
            font-weight: 850;
        }

        .directory {
            overflow: hidden;
        }

        .directory-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            padding: 24px 20px 18px;
        }

        .eyebrow {
            display: block;
            margin: 0 0 9px;
            color: var(--blue);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .section-title {
            margin: 0;
            color: #061845;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 900;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .select,
        .input {
            height: 42px;
            border: 1px solid #c7d6ee;
            border-radius: 7px;
            background: #ffffff;
            color: #18315e;
            font-size: 13px;
            font-weight: 750;
            outline: none;
        }

        .select {
            min-width: 136px;
            padding: 0 12px;
        }

        .search-input {
            width: 230px;
            padding: 0 13px;
        }

        .select:focus,
        .input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(23, 102, 255, .12);
        }

        .search-button {
            min-width: 104px;
            background: var(--navy);
            color: #ffffff;
        }

        .flash {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 20px 16px;
            min-height: 38px;
            padding: 0 12px;
            border: 1px solid #aee9c9;
            border-radius: 8px;
            background: #effbf5;
            color: #067143;
            font-weight: 850;
        }

        .error-list {
            margin: 0 20px 16px;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-weight: 750;
        }

        .table-wrap {
            padding: 0 18px 18px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 940px;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #e1e9f5;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            border-top: 1px solid #e1e9f5;
            padding: 14px 16px;
            text-align: left;
            vertical-align: middle;
        }

        thead th {
            border-top: 0;
            background: #f8fbff;
            color: #53698f;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        tbody td {
            color: #17315f;
            font-size: 13px;
            font-weight: 750;
        }

        .user-cell {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--navy);
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
        }

        .user-name {
            color: #071b4d;
            font-weight: 900;
            overflow-wrap: anywhere;
        }

        .role-pill {
            min-height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
            border-radius: 999px;
            background: var(--role-bg);
            color: var(--role-color);
            font-size: 11px;
            font-weight: 900;
        }

        .action-row {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .icon-button {
            width: 32px;
            height: 32px;
            display: inline-grid;
            place-items: center;
            border: 0;
            border-radius: 7px;
            background: var(--action-bg);
            color: var(--action-color);
            text-decoration: none;
        }

        .icon-button svg {
            width: 16px;
            height: 16px;
        }

        .inline-form {
            margin: 0;
        }

        .empty {
            height: 110px;
            text-align: center;
            color: var(--muted);
        }

        .table-footer {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 18px 18px;
            color: #52698f;
            font-size: 11px;
            font-weight: 750;
        }

        .pager {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .page-link {
            width: 30px;
            height: 30px;
            display: inline-grid;
            place-items: center;
            border: 1px solid #d5e1f1;
            border-radius: 6px;
            background: #ffffff;
            color: #143064;
            text-decoration: none;
            font-weight: 900;
        }

        .page-link.current {
            border-color: #aac6ff;
            background: #eef4ff;
            color: var(--blue);
        }

        .page-link.disabled {
            color: #a6b4ca;
            pointer-events: none;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 20;
            display: grid;
            place-items: center;
            padding: 22px;
            background: rgba(7, 27, 77, .46);
        }

        .modal-card {
            width: min(720px, 100%);
            max-height: calc(100vh - 44px);
            overflow-y: auto;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--line);
        }

        .modal-title {
            margin: 0;
            color: #061845;
            font-size: 20px;
            font-weight: 900;
        }

        .close-link {
            width: 34px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            border: 1px solid #ccd9ed;
            border-radius: 7px;
            color: #17315f;
            text-decoration: none;
        }

        .close-link svg {
            width: 16px;
            height: 16px;
        }

        .modal-body {
            padding: 22px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            color: #11275a;
            font-size: 12px;
            font-weight: 900;
        }

        .field .input,
        .field .select {
            width: 100%;
            padding: 0 12px;
        }

        .input[readonly],
        .select:disabled {
            background: #f8fbff;
            color: #465b80;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .input {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 8px;
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 6px;
            background: var(--blue-soft);
            color: var(--blue);
            transform: translateY(-50%);
        }

        .toggle-password svg {
            width: 16px;
            height: 16px;
        }

        .modal-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 20px;
        }

        @media (max-width: 960px) {
            .stats {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }

            .directory-head,
            .topbar {
                align-items: stretch;
                flex-direction: column;
            }

            .search-form {
                flex-wrap: wrap;
            }

            .topbar-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .user-registration-page {
                min-height: auto;
            }

            .stats,
            .form-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 24px;
            }

            .search-form,
            .select,
            .search-input,
            .search-button,
            .button,
            .user-chip,
            .topbar-actions,
            .logout-form {
                width: 100%;
            }

            .modal-actions {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="icon-user-plus" viewBox="0 0 24 24">
            <path d="M15 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M11 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M18 8v6M15 11h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-users" viewBox="0 0 24 24">
            <path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M18 11.5a2.4 2.4 0 0 0 0-4.5M19 18c0-1.5-.7-2.7-1.9-3.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-star" viewBox="0 0 24 24">
            <path d="m12 4 2.2 4.7 5.1.7-3.7 3.6.9 5.1L12 15.7 7.5 18l.9-5.1-3.7-3.6 5.1-.7L12 4Z" fill="currentColor"/>
        </symbol>
        <symbol id="icon-briefcase" viewBox="0 0 24 24">
            <path d="M8 8V6h8v2M5 8h14v11H5V8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-headset" viewBox="0 0 24 24">
            <path d="M5 13v-1a7 7 0 0 1 14 0v1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M5 13h3v5H5v-5ZM16 13h3v5h-3v-5ZM16 20h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-list" viewBox="0 0 24 24">
            <path d="M10 7h9M10 12h9M10 17h9M5 7h.01M5 12h.01M5 17h.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-search" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="m16 16 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-eye" viewBox="0 0 24 24">
            <path d="M3.5 12s3-5.5 8.5-5.5S20.5 12 20.5 12s-3 5.5-8.5 5.5S3.5 12 3.5 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="icon-edit" viewBox="0 0 24 24">
            <path d="m5 16-.8 3.8L8 19l9.7-9.7a2.1 2.1 0 0 0-3-3L5 16Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m13.5 7.5 3 3" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="icon-trash" viewBox="0 0 24 24">
            <path d="M5 7h14M10 11v5M14 11v5M8 7l1-2h6l1 2M7 7l1 12h8l1-12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-x" viewBox="0 0 24 24">
            <path d="m7 7 10 10M17 7 7 17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-check" viewBox="0 0 24 24">
            <path d="m5 12 4 4 10-10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-log-out" viewBox="0 0 24 24">
            <path d="M10 17v2H5V5h5v2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 8l4 4-4 4M8 12h10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-chevron-left" viewBox="0 0 24 24">
            <path d="m14 7-5 5 5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-chevron-right" viewBox="0 0 24 24">
            <path d="m10 7 5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
    </svg>

    @php
        $fieldLabels = [
            'all' => 'All fields',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'role' => 'Role',
        ];

        $modalRoleId = old('role_id', $selectedUser?->roles->first()?->id);
        $isViewMode = $modalMode === 'view';
        $isEditMode = $modalMode === 'edit';
        $isCreateMode = $modalMode === 'create';
        $modalTitle = $isViewMode ? 'User Details' : ($isEditMode ? 'Edit User Details' : 'Register New User');
    @endphp

    <div class="user-registration-page">
        <div class="shell">
            <div class="content-actions">
                <a class="button button-primary" href="{{ route('users.index', ['create' => 1]) }}">
                    <svg><use href="#icon-user-plus"></use></svg>
                    <span>New User</span>
                </a>
            </div>

            <section class="stats" aria-label="User analytics">
                <article class="stat-card" style="--stat-color: var(--navy); --stat-bg: var(--blue-soft)">
                    <span class="stat-icon"><svg><use href="#icon-users"></use></svg></span>
                    <div>
                        <p class="stat-value">{{ $stats['total_users'] }}</p>
                        <p class="stat-label">Total users</p>
                    </div>
                </article>
                <article class="stat-card" style="--stat-color: var(--blue); --stat-bg: var(--blue-soft)">
                    <span class="stat-icon"><svg><use href="#icon-star"></use></svg></span>
                    <div>
                        <p class="stat-value">{{ $stats['admins'] }}</p>
                        <p class="stat-label">Admins</p>
                    </div>
                </article>
                <article class="stat-card" style="--stat-color: var(--green); --stat-bg: var(--green-soft)">
                    <span class="stat-icon"><svg><use href="#icon-briefcase"></use></svg></span>
                    <div>
                        <p class="stat-value">{{ $stats['customers'] }}</p>
                        <p class="stat-label">Customers</p>
                    </div>
                </article>
                <article class="stat-card" style="--stat-color: var(--violet); --stat-bg: var(--violet-soft)">
                    <span class="stat-icon"><svg><use href="#icon-headset"></use></svg></span>
                    <div>
                        <p class="stat-value">{{ $stats['consultants'] }}</p>
                        <p class="stat-label">Consultants</p>
                    </div>
                </article>
            </section>

            <section class="directory" aria-labelledby="registered-users-title">
                <div class="directory-head">
                    <div>
                        <span class="eyebrow">Directory</span>
                        <h2 class="section-title" id="registered-users-title">Registered Users</h2>
                    </div>

                    <form class="search-form" method="GET" action="{{ route('users.index') }}">
                        <select class="select" name="field" aria-label="Search field">
                            @foreach ($fieldLabels as $value => $label)
                                <option value="{{ $value }}" @selected($field === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input class="input search-input" name="search" type="search" value="{{ $search }}" placeholder="Search users">
                        <button class="button search-button" type="submit">
                            <svg><use href="#icon-list"></use></svg>
                            <span>Search</span>
                        </button>
                    </form>
                </div>

                @if (session('status'))
                    <div class="flash">
                        <svg width="16" height="16"><use href="#icon-check"></use></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 24%">Name</th>
                                <th style="width: 30%">Email</th>
                                <th style="width: 16%">Role</th>
                                <th style="width: 16%">Created</th>
                                <th style="width: 14%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $role = $user->roles->first();
                                    $roleSlug = $role?->slug ?? 'none';
                                    $roleStyles = match ($roleSlug) {
                                        'admin' => ['#e8f0ff', '#1766ff'],
                                        'customer' => ['#dcf8e9', '#087344'],
                                        'consultant' => ['#eeeaff', '#5b40d6'],
                                        default => ['#f0f4fa', '#465b80'],
                                    };
                                    $initials = strtoupper(mb_substr($user->first_name, 0, 1) . mb_substr($user->last_name, 0, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <span class="user-avatar">{{ $initials }}</span>
                                            <span class="user-name">{{ $user->full_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="role-pill" style="--role-bg: {{ $roleStyles[0] }}; --role-color: {{ $roleStyles[1] }}">
                                            {{ $role?->name ?? 'No role' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-row">
                                            <a class="icon-button" style="--action-bg: var(--blue-soft); --action-color: var(--blue)" href="{{ route('users.index', ['view' => $user->id]) }}" aria-label="View {{ $user->full_name }}" title="View">
                                                <svg><use href="#icon-eye"></use></svg>
                                            </a>
                                            <a class="icon-button" style="--action-bg: var(--blue-soft); --action-color: var(--blue)" href="{{ route('users.index', ['edit' => $user->id]) }}" aria-label="Edit {{ $user->full_name }}" title="Edit">
                                                <svg><use href="#icon-edit"></use></svg>
                                            </a>
                                            <form class="inline-form" method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="icon-button" style="--action-bg: var(--red-soft); --action-color: var(--red)" type="submit" aria-label="Delete {{ $user->full_name }}" title="Delete">
                                                    <svg><use href="#icon-trash"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="empty" colspan="5">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <span>Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</span>
                    <div class="pager" aria-label="Pagination">
                        <a class="page-link {{ $users->onFirstPage() ? 'disabled' : '' }}" href="{{ $users->previousPageUrl() ?? '#' }}" aria-label="Previous page">
                            <svg width="15" height="15"><use href="#icon-chevron-left"></use></svg>
                        </a>
                        <span class="page-link current">{{ $users->currentPage() }}</span>
                        <a class="page-link {{ $users->hasMorePages() ? '' : 'disabled' }}" href="{{ $users->nextPageUrl() ?? '#' }}" aria-label="Next page">
                            <svg width="15" height="15"><use href="#icon-chevron-right"></use></svg>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @if ($modalMode)
        <div class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
            <section class="modal-card">
                <div class="modal-head">
                    <h2 class="modal-title" id="user-modal-title">{{ $modalTitle }}</h2>
                    <a class="close-link" href="{{ route('users.index') }}" aria-label="Close">
                        <svg><use href="#icon-x"></use></svg>
                    </a>
                </div>

                <div class="modal-body">
                    @if ($isViewMode)
                        <div class="form-grid">
                            <div class="field">
                                <label for="view-first-name">First Name</label>
                                <input class="input" id="view-first-name" value="{{ $selectedUser->first_name }}" readonly>
                            </div>
                            <div class="field">
                                <label for="view-last-name">Last Name</label>
                                <input class="input" id="view-last-name" value="{{ $selectedUser->last_name }}" readonly>
                            </div>
                            <div class="field full">
                                <label for="view-role">Role</label>
                                <input class="input" id="view-role" value="{{ $selectedUser->roles->first()?->name ?? 'No role' }}" readonly>
                            </div>
                            <div class="field full">
                                <label for="view-email">Email</label>
                                <input class="input" id="view-email" value="{{ $selectedUser->email }}" readonly>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <form class="inline-form" method="POST" action="{{ route('users.destroy', $selectedUser) }}" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="button button-danger" type="submit">
                                    <svg><use href="#icon-trash"></use></svg>
                                    <span>Delete</span>
                                </button>
                            </form>
                            <a class="button button-light" href="{{ route('users.index', ['edit' => $selectedUser->id]) }}">
                                <svg><use href="#icon-edit"></use></svg>
                                <span>Edit</span>
                            </a>
                            <a class="button button-primary" href="{{ route('users.index') }}">Close</a>
                        </div>
                    @else
                        <form method="POST" action="{{ $isEditMode ? route('users.update', $selectedUser) : route('users.store') }}">
                            @csrf
                            @if ($isEditMode)
                                @method('PUT')
                            @endif

                            <div class="form-grid">
                                <div class="field">
                                    <label for="first-name">First Name</label>
                                    <input class="input" id="first-name" name="first_name" value="{{ old('first_name', $selectedUser?->first_name) }}" required maxlength="80">
                                </div>
                                <div class="field">
                                    <label for="last-name">Last Name</label>
                                    <input class="input" id="last-name" name="last_name" value="{{ old('last_name', $selectedUser?->last_name) }}" required maxlength="80">
                                </div>
                                <div class="field full">
                                    <label for="role-id">Role</label>
                                    <select class="select" id="role-id" name="role_id" required>
                                        <option value="">Select role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected((int) $modalRoleId === $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field full">
                                    <label for="email">Email</label>
                                    <input class="input" id="email" name="email" type="email" value="{{ old('email', $selectedUser?->email) }}" required maxlength="255">
                                </div>
                                <div class="field">
                                    <label for="password">Password</label>
                                    <div class="password-wrap">
                                        <input class="input" id="password" name="password" type="password" @required($isCreateMode) minlength="8" autocomplete="new-password">
                                        <button class="toggle-password" type="button" data-password-toggle="password" aria-label="Show password">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="field">
                                    <label for="password-confirmation">Password Validation</label>
                                    <div class="password-wrap">
                                        <input class="input" id="password-confirmation" name="password_confirmation" type="password" @required($isCreateMode) minlength="8" autocomplete="new-password">
                                        <button class="toggle-password" type="button" data-password-toggle="password-confirmation" aria-label="Show password validation">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-actions">
                                <a class="button button-light" href="{{ route('users.index') }}">Cancel</a>
                                <button class="button button-primary" type="submit">
                                    <svg><use href="#icon-check"></use></svg>
                                    <span>{{ $isEditMode ? 'Update User' : 'Save User' }}</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    @endif

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);

                if (!input) {
                    return;
                }

                const showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
@endsection

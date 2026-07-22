@extends('layouts.app-shell')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')
@section('page-subtitle', 'Manage role names, permission groups, and assigned-user visibility.')

@push('styles')
    <style>
        :root {
            --ink: #071b4d;
            --muted: #61708f;
            --line: #d8e2f2;
            --panel: #ffffff;
            --canvas: #f4f7fc;
            --blue: #1766ff;
            --blue-soft: #e8f0ff;
            --green: #21b875;
            --green-soft: #def8ea;
            --amber: #f5a524;
            --amber-soft: #fff3d7;
            --violet: #7a65ff;
            --violet-soft: #eeeaff;
            --red: #e84d4d;
            --red-soft: #ffe5e5;
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
        input {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        svg {
            display: block;
        }

        .role-page {
            min-height: 0;
            padding: 0;
        }

        .page-header {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(520px, 1.35fr);
            gap: 18px;
            align-items: stretch;
            margin: 0 auto 18px;
            max-width: 1760px;
        }

        .header-title,
        .analytics-strip,
        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .header-title {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 118px;
            padding: 24px 26px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 10px;
            color: var(--blue);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .eyebrow svg {
            width: 17px;
            height: 17px;
        }

        h1 {
            margin: 0;
            color: #061845;
            font-size: 28px;
            line-height: 1.08;
            font-weight: 900;
        }

        .header-title p {
            margin: 9px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 650;
            line-height: 1.55;
        }

        .analytics-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(130px, 1fr));
            gap: 0;
            overflow: hidden;
        }

        .analytics-item {
            min-height: 118px;
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            padding: 18px;
            border-left: 1px solid var(--line);
        }

        .analytics-item:first-child {
            border-left: 0;
        }

        .analytics-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            color: var(--metric-color);
            background: var(--metric-bg);
        }

        .analytics-icon svg {
            width: 19px;
            height: 19px;
        }

        .analytics-value {
            margin: 0 0 4px;
            color: #061845;
            font-size: 24px;
            line-height: 1;
            font-weight: 900;
        }

        .analytics-label {
            margin: 0;
            color: #52668d;
            font-size: 11px;
            font-weight: 850;
            line-height: 1.35;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 18px;
            align-items: start;
            max-width: 1760px;
            margin: 0 auto 18px;
        }

        .panel {
            min-width: 0;
        }

        .panel-main {
            padding: 22px;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .title-line {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .title-line svg {
            width: 19px;
            height: 19px;
            color: var(--blue);
        }

        .panel-title {
            margin: 0;
            color: #071b4d;
            font-size: 16px;
            font-weight: 900;
        }

        .flash {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 38px;
            margin-bottom: 16px;
            padding: 0 12px;
            border: 1px solid #aee9c9;
            border-radius: 8px;
            background: #effbf5;
            color: #067143;
            font-weight: 850;
        }

        .error-list {
            margin: 0 0 16px;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-weight: 750;
        }

        .role-form {
            display: grid;
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        .field label,
        .permissions-label {
            color: #11275a;
            font-size: 12px;
            font-weight: 900;
        }

        .input {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #c6d5ec;
            border-radius: 6px;
            background: #ffffff;
            color: var(--ink);
            font-weight: 700;
            outline: none;
        }

        .input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(23, 102, 255, .12);
        }

        .permission-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 10px;
            margin-top: 8px;
        }

        .permission-check {
            min-height: 39px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 11px;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: #ffffff;
            color: #0f285f;
            font-size: 12px;
            font-weight: 850;
        }

        .permission-check input {
            width: 15px;
            height: 15px;
            accent-color: var(--blue);
            flex: 0 0 auto;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 16px;
            border-top: 1px solid #e4ebf6;
        }

        .button,
        .icon-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 6px;
            text-decoration: none;
            white-space: nowrap;
            transition: background .16s ease, border-color .16s ease, color .16s ease;
        }

        .button {
            min-height: 38px;
            gap: 8px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 900;
        }

        .button svg {
            width: 16px;
            height: 16px;
        }

        .button-primary {
            background: var(--blue);
            color: #ffffff;
        }

        .button-primary:hover,
        .button-primary:focus-visible {
            background: #0f55dc;
            outline: none;
        }

        .button-light {
            border-color: #cbd9ee;
            background: #ffffff;
            color: #17315f;
        }

        .button-light:hover,
        .button-light:focus-visible {
            background: #eef4ff;
            outline: none;
        }

        .about-panel {
            padding: 20px;
        }

        .about-copy {
            margin: 0 0 18px;
            color: #61708f;
            font-size: 12px;
            font-weight: 650;
            line-height: 1.6;
        }

        .side-stat-list {
            display: grid;
            gap: 14px;
            margin: 0;
            padding: 0 0 18px;
            border-bottom: 1px solid #e5edf8;
            list-style: none;
        }

        .side-stat {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            align-items: center;
            gap: 12px;
        }

        .side-stat-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            color: var(--metric-color);
            background: var(--metric-bg);
        }

        .side-stat-icon svg {
            width: 18px;
            height: 18px;
        }

        .side-stat strong {
            display: block;
            color: #061845;
            font-size: 20px;
            line-height: 1;
            font-weight: 900;
        }

        .side-stat span {
            display: block;
            margin-top: 4px;
            color: #4a5f86;
            font-size: 11px;
            font-weight: 850;
        }

        .guide-title {
            margin: 18px 0 10px;
            color: #071b4d;
            font-size: 13px;
            font-weight: 900;
        }

        .guide-list {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .guide-list li {
            padding: 10px 11px;
            border-left: 3px solid var(--guide-color);
            border-radius: 6px;
            background: #f8fbff;
        }

        .guide-list strong {
            display: block;
            margin-bottom: 4px;
            color: #10275f;
            font-size: 11px;
            font-weight: 900;
        }

        .guide-list span {
            color: #657492;
            font-size: 10px;
            font-weight: 650;
            line-height: 1.5;
        }

        .roles-table-panel {
            max-width: 1760px;
            margin: 0 auto;
            overflow: hidden;
        }

        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .search-form {
            position: relative;
        }

        .search-form svg {
            position: absolute;
            top: 50%;
            left: 10px;
            width: 15px;
            height: 15px;
            color: #6980a3;
            transform: translateY(-50%);
        }

        .search-input {
            width: 210px;
            height: 34px;
            padding: 0 11px 0 31px;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            color: #18315e;
            font-size: 12px;
            font-weight: 700;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(23, 102, 255, .12);
        }

        .roles-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .roles-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .roles-table th,
        .roles-table td {
            border-top: 1px solid #e4ebf6;
            padding: 14px 20px;
            text-align: left;
            vertical-align: middle;
        }

        .roles-table th {
            color: #53698f;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .roles-table td {
            color: #17315f;
            font-size: 12px;
            font-weight: 750;
        }

        .role-name-cell {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            align-items: center;
            gap: 12px;
        }

        .role-avatar {
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--avatar-bg);
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
        }

        .role-main {
            display: block;
            color: #071b4d;
            font-size: 13px;
            font-weight: 900;
            overflow-wrap: anywhere;
        }

        .role-sub {
            display: block;
            margin-top: 3px;
            color: #61708f;
            font-size: 11px;
            font-weight: 650;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .access-pill,
        .status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 23px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            white-space: nowrap;
        }

        .access-pill {
            padding: 0 9px;
            background: var(--amber-soft);
            color: #9b6200;
        }

        .access-pill.analytics {
            background: var(--blue-soft);
            color: #0f55dc;
        }

        .access-pill.full {
            background: var(--green-soft);
            color: #087344;
        }

        .status {
            gap: 6px;
            color: #0d7048;
        }

        .status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: currentColor;
        }

        .status.inactive {
            color: #aa3333;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .inline-form {
            margin: 0;
        }

        .icon-action {
            width: 32px;
            height: 32px;
            border-color: #d7e2f3;
            background: #ffffff;
            color: #0f285f;
        }

        .icon-action svg {
            width: 15px;
            height: 15px;
        }

        .icon-action:hover,
        .icon-action:focus-visible {
            background: #eef4ff;
            color: var(--blue);
            outline: none;
        }

        .icon-action.danger:hover,
        .icon-action.danger:focus-visible {
            border-color: #ffc8c8;
            background: var(--red-soft);
            color: var(--red);
        }

        .empty-row {
            height: 112px;
            text-align: center;
            color: #61708f;
        }

        .table-footer {
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 20px;
            border-top: 1px solid #e4ebf6;
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

        .detail-panel {
            max-width: 1760px;
            margin: 0 auto 18px;
            padding: 18px 20px;
            border-color: #b9cdf4;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr) auto;
            gap: 18px;
            align-items: start;
        }

        .detail-name {
            margin: 6px 0 5px;
            color: #061845;
            font-size: 22px;
            font-weight: 900;
        }

        .detail-meta {
            margin: 0;
            color: #61708f;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.5;
        }

        .permission-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin: 2px 0 0;
        }

        .permission-tag {
            min-height: 25px;
            display: inline-flex;
            align-items: center;
            padding: 0 9px;
            border: 1px solid #cbd9ee;
            border-radius: 999px;
            background: #f8fbff;
            color: #17315f;
            font-size: 11px;
            font-weight: 850;
        }

        @media (max-width: 1260px) {
            .page-header,
            .content-grid {
                grid-template-columns: 1fr;
            }

            .analytics-strip {
                grid-template-columns: repeat(2, minmax(150px, 1fr));
            }

            .analytics-item:nth-child(3) {
                border-left: 0;
                border-top: 1px solid var(--line);
            }

            .analytics-item:nth-child(4) {
                border-top: 1px solid var(--line);
            }

            .permission-grid {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .role-page {
                padding: 14px;
            }

            .header-title,
            .panel-main,
            .about-panel {
                padding: 16px;
            }

            h1 {
                font-size: 23px;
            }

            .analytics-strip,
            .permission-grid {
                grid-template-columns: 1fr;
            }

            .analytics-item,
            .analytics-item:first-child {
                border-left: 0;
                border-top: 1px solid var(--line);
            }

            .analytics-item:first-child {
                border-top: 0;
            }

            .table-toolbar,
            .table-actions,
            .table-footer,
            .form-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .search-input,
            .button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="icon-shield" viewBox="0 0 24 24">
            <path d="M12 3 19 6v5c0 4.4-2.8 8.3-7 9.7C7.8 19.3 5 15.4 5 11V6l7-3Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m9 12 2 2 4-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-chart" viewBox="0 0 24 24">
            <path d="M5 19V5M5 19h14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M8 16v-4M12 16V8M16 16v-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-users" viewBox="0 0 24 24">
            <path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M18 11.5a2.4 2.4 0 0 0 0-4.5M19 18c0-1.5-.7-2.7-1.9-3.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-lock" viewBox="0 0 24 24">
            <path d="M7 11h10v8H7v-8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 11V8a3 3 0 0 1 6 0v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-info" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 11v5M12 8h.01" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-save" viewBox="0 0 24 24">
            <path d="M5 5h11l3 3v11H5V5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M8 5v5h8M8 19v-5h8v5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-plus" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
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
        <symbol id="icon-check" viewBox="0 0 24 24">
            <path d="m5 12 4 4 10-10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-chevron-left" viewBox="0 0 24 24">
            <path d="m14 7-5 5 5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-chevron-right" viewBox="0 0 24 24">
            <path d="m10 7 5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-x" viewBox="0 0 24 24">
            <path d="m7 7 10 10M17 7 7 17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </symbol>
    </svg>

    @php
        $selectedPermissionIds = collect(old('permissions', $editingRole?->permissions->pluck('id')->all() ?? []))
            ->map(fn ($id) => (int) $id)
            ->all();
        $permissionTotal = $permissions->flatten()->count();
        $isEditing = $editingRole !== null;
        $avatarColors = ['#4d8cff', '#20bd72', '#8468ff', '#f5a524', '#18aac4'];
    @endphp

    <div class="role-page">
        <header class="page-header">
            <section class="header-title" aria-labelledby="page-title">
                <p class="eyebrow"><svg><use href="#icon-chart"></use></svg><span>Analytics per Role</span></p>
                <h1 id="page-title">Roles & Permissions</h1>
                <p>Manage role names, permission groups, and assigned-user visibility for the support system.</p>
            </section>

            <section class="analytics-strip" aria-label="Role analytics">
                <article class="analytics-item" style="--metric-color: var(--blue); --metric-bg: var(--blue-soft)">
                    <span class="analytics-icon"><svg><use href="#icon-shield"></use></svg></span>
                    <div>
                        <p class="analytics-value">{{ $stats['total_roles'] }}</p>
                        <p class="analytics-label">Total Roles</p>
                    </div>
                </article>
                <article class="analytics-item" style="--metric-color: var(--green); --metric-bg: var(--green-soft)">
                    <span class="analytics-icon"><svg><use href="#icon-chart"></use></svg></span>
                    <div>
                        <p class="analytics-value">{{ $stats['analytics_roles'] }}</p>
                        <p class="analytics-label">Analytics Roles</p>
                    </div>
                </article>
                <article class="analytics-item" style="--metric-color: var(--amber); --metric-bg: var(--amber-soft)">
                    <span class="analytics-icon"><svg><use href="#icon-users"></use></svg></span>
                    <div>
                        <p class="analytics-value">{{ $stats['assigned_users'] }}</p>
                        <p class="analytics-label">Assigned Users</p>
                    </div>
                </article>
                <article class="analytics-item" style="--metric-color: var(--violet); --metric-bg: var(--violet-soft)">
                    <span class="analytics-icon"><svg><use href="#icon-lock"></use></svg></span>
                    <div>
                        <p class="analytics-value">{{ $stats['permission_groups'] }}</p>
                        <p class="analytics-label">Permission Groups</p>
                    </div>
                </article>
            </section>
        </header>

        @if ($viewRole)
            <section class="panel detail-panel" aria-labelledby="view-role-title">
                <div class="detail-grid">
                    <div>
                        <p class="eyebrow"><svg><use href="#icon-eye"></use></svg><span>Role Details</span></p>
                        <h2 class="detail-name" id="view-role-title">{{ $viewRole->name }}</h2>
                        <p class="detail-meta">{{ $viewRole->users_count }} users assigned</p>
                    </div>
                    <div>
                        <p class="permissions-label">Permissions</p>
                        <div class="permission-tags">
                            @forelse ($viewRole->permissions as $permission)
                                <span class="permission-tag">{{ $permission->name }}</span>
                            @empty
                                <span class="permission-tag">No permissions assigned</span>
                            @endforelse
                        </div>
                    </div>
                    <a class="icon-action" href="{{ route('roles.index') }}" aria-label="Close role details" title="Close">
                        <svg><use href="#icon-x"></use></svg>
                    </a>
                </div>
            </section>
        @endif

        <div class="content-grid">
            <section class="panel panel-main" id="role-form" aria-labelledby="role-form-title">
                <div class="panel-heading">
                    <div class="title-line">
                        <svg><use href="#icon-shield"></use></svg>
                        <h2 class="panel-title" id="role-form-title">{{ $isEditing ? 'Edit Role' : 'Role Details' }}</h2>
                    </div>
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

                <form class="role-form" method="POST" action="{{ $isEditing ? route('roles.update', $editingRole) : route('roles.store') }}">
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="status" value="{{ old('status', $editingRole?->status ?? 'active') }}">

                    <div class="field">
                        <label for="name">Role Name</label>
                        <input class="input" id="name" name="name" type="text" value="{{ old('name', $editingRole?->name) }}" placeholder="Enter role name" required maxlength="120">
                    </div>

                    <div>
                        <p class="permissions-label">Permissions</p>
                        <div class="permission-grid">
                            @forelse ($permissions as $group => $groupPermissions)
                                @foreach ($groupPermissions as $permission)
                                    <label class="permission-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $selectedPermissionIds, true))>
                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            @empty
                                <span class="role-sub">Run the database seeder to add the default permission groups.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button button-primary" type="submit">
                            <svg><use href="#icon-save"></use></svg>
                            <span>{{ $isEditing ? 'Update Role' : 'Save Role' }}</span>
                        </button>
                        <a class="button button-light" href="{{ route('roles.index') }}">Cancel</a>
                    </div>
                </form>
            </section>

            <aside class="panel about-panel" aria-labelledby="about-roles-title">
                <div class="title-line" style="margin-bottom: 14px">
                    <svg><use href="#icon-info"></use></svg>
                    <h2 class="panel-title" id="about-roles-title">About Roles</h2>
                </div>
                <p class="about-copy">Roles define what actions users can perform in the system by assigning specific permissions and access levels.</p>

                <ul class="side-stat-list">
                    <li class="side-stat" style="--metric-color: var(--blue); --metric-bg: var(--blue-soft)">
                        <span class="side-stat-icon"><svg><use href="#icon-shield"></use></svg></span>
                        <span><strong>{{ $stats['total_roles'] }}</strong><span>Total Roles</span></span>
                    </li>
                    <li class="side-stat" style="--metric-color: var(--green); --metric-bg: var(--green-soft)">
                        <span class="side-stat-icon"><svg><use href="#icon-users"></use></svg></span>
                        <span><strong>{{ $stats['assigned_users'] }}</strong><span>Active Users</span></span>
                    </li>
                    <li class="side-stat" style="--metric-color: var(--violet); --metric-bg: var(--violet-soft)">
                        <span class="side-stat-icon"><svg><use href="#icon-lock"></use></svg></span>
                        <span><strong>{{ $stats['permission_groups'] }}</strong><span>Permission Groups</span></span>
                    </li>
                </ul>

                <h3 class="guide-title">Role Guide</h3>
                <ul class="guide-list">
                    <li style="--guide-color: var(--blue)"><strong>Permission Management</strong><span>Define which modules each role can access.</span></li>
                    <li style="--guide-color: var(--green)"><strong>Access Control</strong><span>Keep feature access consistent across users.</span></li>
                    <li style="--guide-color: var(--violet)"><strong>User Assignment</strong><span>Track how many users belong to each role.</span></li>
                </ul>
            </aside>
        </div>

        <section class="panel roles-table-panel" aria-labelledby="existing-roles-title">
            <div class="table-toolbar">
                <div class="title-line">
                    <svg><use href="#icon-users"></use></svg>
                    <h2 class="panel-title" id="existing-roles-title">Existing Roles</h2>
                </div>
                <div class="table-actions">
                    <form class="search-form" method="GET" action="{{ route('roles.index') }}">
                        <svg><use href="#icon-search"></use></svg>
                        <input class="search-input" name="search" type="search" value="{{ $search }}" placeholder="Search roles...">
                    </form>
                    <a class="button button-primary" href="{{ route('roles.index') }}#role-form">
                        <svg><use href="#icon-plus"></use></svg>
                        <span>Add Role</span>
                    </a>
                </div>
            </div>

            <div class="roles-table-wrap">
                <table class="roles-table">
                    <thead>
                        <tr>
                            <th style="width: 34%">Role Name</th>
                            <th style="width: 18%">Users Assigned</th>
                            <th style="width: 18%">Access Level</th>
                            <th style="width: 14%">Status</th>
                            <th style="width: 16%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            @php
                                $permissionCount = $role->permissions->count();
                                $hasAnalytics = $role->hasPermission('analytics');
                                $hasFullAccess = $permissionTotal > 0 && $permissionCount === $permissionTotal;
                                $accessText = $hasFullAccess ? 'Full Access' : ($hasAnalytics ? 'Analytics' : ($permissionCount > 0 ? 'Custom' : 'Limited'));
                                $accessClass = $hasFullAccess ? 'full' : ($hasAnalytics ? 'analytics' : '');
                                $avatar = strtoupper(mb_substr($role->name, 0, 1));
                                $avatarColor = $avatarColors[$loop->index % count($avatarColors)];
                            @endphp
                            <tr>
                                <td>
                                    <div class="role-name-cell">
                                        <span class="role-avatar" style="--avatar-bg: {{ $avatarColor }}">{{ $avatar }}</span>
                                        <span>
                                            <span class="role-main">{{ $role->name }}</span>
                                            <span class="role-sub">
                                                @if ($permissionCount === 0)
                                                    No permissions assigned
                                                @else
                                                    {{ $permissionCount }} {{ $permissionCount === 1 ? 'permission' : 'permissions' }} assigned
                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $role->users_count }}</td>
                                <td><span class="access-pill {{ $accessClass }}">{{ $accessText }}</span></td>
                                <td><span class="status {{ $role->status === 'active' ? '' : 'inactive' }}">{{ ucfirst($role->status) }}</span></td>
                                <td>
                                    <div class="action-group">
                                        <a class="icon-action" href="{{ route('roles.index', ['view' => $role->id]) }}" aria-label="View {{ $role->name }}" title="View">
                                            <svg><use href="#icon-eye"></use></svg>
                                        </a>
                                        <a class="icon-action" href="{{ route('roles.index', ['edit' => $role->id]) }}#role-form" aria-label="Edit {{ $role->name }}" title="Edit">
                                            <svg><use href="#icon-edit"></use></svg>
                                        </a>
                                        <form class="inline-form" method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action danger" type="submit" aria-label="Delete {{ $role->name }}" title="Delete">
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="empty-row" colspan="5">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>
                    Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }} roles
                </span>
                <div class="pager" aria-label="Pagination">
                    <a class="page-link {{ $roles->onFirstPage() ? 'disabled' : '' }}" href="{{ $roles->previousPageUrl() ?? '#' }}" aria-label="Previous page">
                        <svg width="15" height="15"><use href="#icon-chevron-left"></use></svg>
                    </a>
                    <span class="page-link current">{{ $roles->currentPage() }}</span>
                    <a class="page-link {{ $roles->hasMorePages() ? '' : 'disabled' }}" href="{{ $roles->nextPageUrl() ?? '#' }}" aria-label="Next page">
                        <svg width="15" height="15"><use href="#icon-chevron-right"></use></svg>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection

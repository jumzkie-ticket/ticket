@extends('layouts.app-shell')

@section('title', 'System Settings')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Configure system preferences and platform options.')

@push('styles')
    <style>
        .settings-page {
            display: grid;
            gap: 18px;
        }

        .settings-row {
            display: grid;
            gap: 16px;
        }

        .settings-row-top {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .settings-row-bottom {
            grid-template-columns: minmax(320px, 1.3fr) repeat(2, minmax(250px, .85fr));
        }

        .settings-card {
            min-width: 0;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: var(--shadow);
        }

        .settings-flash {
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

        .settings-flash svg {
            width: 16px;
            height: 16px;
        }

        .settings-errors {
            margin: 0;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .settings-card-header {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .settings-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: var(--icon-bg);
            color: var(--icon-color);
        }

        .settings-icon svg {
            width: 22px;
            height: 22px;
        }

        .settings-card-title {
            margin: 0;
            color: #071b4d;
            font-size: 15px;
            font-weight: 900;
        }

        .settings-card-subtitle {
            margin: 6px 0 0;
            color: #61708f;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .settings-fields {
            display: grid;
            gap: 16px;
        }

        .settings-field {
            display: grid;
            grid-template-columns: minmax(110px, .45fr) minmax(0, 1fr);
            align-items: center;
            gap: 14px;
        }

        .settings-label {
            color: #11275a;
            font-size: 12px;
            font-weight: 900;
        }

        .settings-input,
        .settings-select {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: #ffffff;
            color: #152b5d;
            font-size: 12px;
            font-weight: 750;
            outline: none;
        }

        .settings-input:focus,
        .settings-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .select-control,
        .color-control {
            position: relative;
            min-width: 0;
        }

        .settings-select {
            padding-right: 34px;
            appearance: none;
        }

        .select-control svg,
        .color-control svg {
            position: absolute;
            top: 50%;
            right: 12px;
            width: 14px;
            height: 14px;
            color: #071b4d;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .notification-list,
        .maintenance-list,
        .info-list {
            display: grid;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .notification-item,
        .maintenance-item {
            min-height: 58px;
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #e4ebf6;
        }

        .notification-item:first-child,
        .maintenance-item:first-child {
            border-top: 0;
        }

        .maintenance-item {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .notification-icon {
            color: var(--blue);
        }

        .notification-icon svg {
            width: 20px;
            height: 20px;
        }

        .setting-name {
            display: block;
            color: #10275f;
            font-size: 12px;
            font-weight: 900;
        }

        .setting-copy {
            display: block;
            margin-top: 4px;
            color: #66789c;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
        }

        .toggle {
            position: relative;
            width: 39px;
            height: 23px;
            display: inline-flex;
            flex: 0 0 auto;
        }

        .toggle input {
            position: absolute;
            inset: 0;
            opacity: 0;
        }

        .toggle span {
            width: 100%;
            border-radius: 999px;
            background: #b9bfcc;
            transition: background .16s ease;
        }

        .toggle span::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 17px;
            height: 17px;
            border-radius: 999px;
            background: #ffffff;
            box-shadow: 0 1px 4px rgba(10, 33, 74, .25);
            transition: transform .16s ease;
        }

        .toggle input:checked + span {
            background: var(--blue);
        }

        .toggle input:checked + span::after {
            transform: translateX(16px);
        }

        .toggle input:focus-visible + span {
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .16);
        }

        .color-stack {
            display: grid;
            gap: 10px;
            min-width: 0;
        }

        .color-control {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 42px;
            gap: 8px;
        }

        .color-input {
            padding-left: 46px;
        }

        .color-swatch {
            position: absolute;
            top: 50%;
            left: 12px;
            width: 22px;
            height: 22px;
            border-radius: 5px;
            background: #2563eb;
            transform: translateY(-50%);
        }

        .native-color-input {
            width: 42px;
            height: 38px;
            padding: 3px;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: #ffffff;
            cursor: pointer;
        }

        .native-color-input::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .native-color-input::-webkit-color-swatch {
            border: 0;
            border-radius: 4px;
        }

        .color-palette {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .color-palette-button {
            width: 28px;
            height: 28px;
            border: 2px solid #ffffff;
            border-radius: 6px;
            background: var(--palette-color);
            box-shadow: 0 0 0 1px #cbd9ee;
        }

        .color-palette-button:hover,
        .color-palette-button:focus-visible,
        .color-palette-button.active {
            box-shadow: 0 0 0 2px var(--blue);
            outline: none;
        }

        .theme-choice-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .theme-choice {
            position: relative;
            min-width: 0;
            cursor: pointer;
        }

        .theme-choice input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .theme-choice-content {
            min-height: 72px;
            display: grid;
            place-items: center;
            align-content: center;
            gap: 7px;
            padding: 10px 6px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            color: var(--muted);
            font-size: 11px;
            font-weight: 850;
            text-align: center;
            transition: border-color .16s ease, background .16s ease, color .16s ease, box-shadow .16s ease;
        }

        .theme-choice-content svg {
            width: 20px;
            height: 20px;
        }

        .theme-choice:hover .theme-choice-content {
            border-color: var(--blue);
            color: var(--blue);
        }

        .theme-choice input:checked + .theme-choice-content {
            border-color: var(--blue);
            background: var(--blue-soft);
            color: var(--blue);
            box-shadow: 0 0 0 2px rgba(var(--blue-rgb), .08);
        }

        .theme-choice input:focus-visible + .theme-choice-content {
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .15);
        }

        .appearance-preview {
            --preview-primary: var(--blue);
            --preview-canvas: #eef3f9;
            --preview-panel: #ffffff;
            --preview-line: #d9e2ee;
            --preview-ink: #13213a;
            min-height: 154px;
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: var(--preview-canvas);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .2);
        }

        .appearance-preview[data-theme="dark"] {
            --preview-canvas: #091321;
            --preview-panel: #111c2e;
            --preview-line: #2a3a51;
            --preview-ink: #e7edf7;
        }

        @media (prefers-color-scheme: dark) {
            .appearance-preview[data-theme="system"] {
                --preview-canvas: #091321;
                --preview-panel: #111c2e;
                --preview-line: #2a3a51;
                --preview-ink: #e7edf7;
            }
        }

        .appearance-preview-side {
            display: grid;
            align-content: start;
            gap: 8px;
            padding: 11px 8px;
            background: linear-gradient(165deg, color-mix(in srgb, var(--preview-primary) 62%, #102a56), #07162f);
        }

        .appearance-preview-logo {
            width: 28px;
            height: 24px;
            border-radius: 5px;
            background: rgba(255, 255, 255, .92);
        }

        .appearance-preview-nav {
            height: 6px;
            border-radius: 99px;
            background: rgba(255, 255, 255, .24);
        }

        .appearance-preview-nav.active {
            height: 18px;
            background: var(--preview-primary);
        }

        .appearance-preview-main {
            min-width: 0;
            display: grid;
            grid-template-rows: 32px 1fr;
        }

        .appearance-preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 10px;
            border-bottom: 1px solid var(--preview-line);
            background: var(--preview-panel);
        }

        .appearance-preview-title,
        .appearance-preview-avatar,
        .appearance-preview-line {
            display: block;
            border-radius: 99px;
        }

        .appearance-preview-title {
            width: 62px;
            height: 7px;
            background: var(--preview-ink);
        }

        .appearance-preview-avatar {
            width: 16px;
            height: 16px;
            background: var(--preview-primary);
        }

        .appearance-preview-content {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            padding: 10px;
        }

        .appearance-preview-panel {
            display: grid;
            align-content: start;
            gap: 7px;
            padding: 9px;
            border: 1px solid var(--preview-line);
            border-radius: 7px;
            background: var(--preview-panel);
        }

        .appearance-preview-line {
            width: 78%;
            height: 5px;
            background: color-mix(in srgb, var(--preview-ink) 20%, transparent);
        }

        .appearance-preview-line.primary {
            width: 44%;
            height: 15px;
            background: var(--preview-primary);
        }

        .appearance-preview-note {
            display: block;
            margin-top: 7px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
        }

        .logo-dropzone {
            min-height: 196px;
            display: grid;
            grid-template-rows: minmax(126px, auto) auto;
            overflow: hidden;
            border: 1px dashed #b7cff8;
            border-radius: 8px;
            background: #fbfdff;
        }

        .logo-preview {
            min-width: 0;
            min-height: 126px;
            display: grid;
            place-items: center;
            padding: 14px;
            border-bottom: 1px dashed #d5e2f6;
            color: var(--blue);
            font-size: 42px;
            font-weight: 900;
        }

        .logo-preview.has-logo {
            background: #ffffff;
        }

        .logo-preview sup {
            top: -1em;
            font-size: 16px;
        }

        .logo-image {
            width: min(100%, 560px);
            max-height: 150px;
            object-fit: contain;
        }

        .logo-image.is-hidden,
        .logo-placeholder.is-hidden {
            display: none;
        }

        .logo-upload {
            display: grid;
            align-content: center;
            gap: 9px;
            padding: 16px;
        }

        .upload-title {
            margin: 0;
            color: #17315f;
            font-size: 12px;
            font-weight: 900;
        }

        .upload-copy {
            margin: 0;
            color: #66789c;
            font-size: 11px;
            font-weight: 700;
        }

        .upload-button,
        .settings-action {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: #ffffff;
            color: var(--blue);
            font-size: 12px;
            font-weight: 900;
        }

        .upload-button {
            width: fit-content;
            padding: 0 13px;
        }

        .file-input {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            clip-path: inset(50%);
            white-space: nowrap;
        }

        .upload-button svg,
        .settings-action svg {
            width: 15px;
            height: 15px;
        }

        .maintenance-frequency {
            min-width: 160px;
        }

        .info-list {
            gap: 0;
        }

        .info-row {
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            border-top: 1px solid #e4ebf6;
            color: #17315f;
            font-size: 12px;
            font-weight: 800;
        }

        .info-row:first-child {
            border-top: 0;
        }

        .info-value {
            color: #52668d;
            font-weight: 750;
            text-align: right;
        }

        .environment-chip {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            padding: 0 10px;
            border-radius: 6px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 11px;
            font-weight: 900;
        }

        .settings-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .settings-action {
            min-width: 152px;
            min-height: 42px;
            padding: 0 18px;
        }

        .settings-action-primary {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
        }

        .settings-action-light {
            color: var(--blue);
        }

        @media (max-width: 1020px) {
            .settings-row-top,
            .settings-row-bottom {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .settings-card {
                padding: 16px;
            }

            .settings-card-header,
            .settings-field {
                grid-template-columns: 1fr;
            }

            .settings-icon {
                width: 40px;
                height: 40px;
            }

            .logo-dropzone {
                grid-template-rows: minmax(112px, auto) auto;
            }

            .logo-preview {
                min-height: 112px;
            }

            .notification-item {
                grid-template-columns: 28px minmax(0, 1fr) auto;
            }

            .settings-action {
                width: 100%;
            }

            .theme-choice-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $logoUrl = $settings->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings->logo_path)
            ? '/storage/'.ltrim($settings->logo_path, '/')
            : null;
        $currentPrimaryColor = strtoupper((string) old('primary_color', $settings->primary_color));
        $pickerPrimaryColor = preg_match('/^#[0-9A-Fa-f]{6}$/', $currentPrimaryColor) ? $currentPrimaryColor : '#2563EB';
        $colorPalette = [
            ['name' => 'Xceler8 Blue', 'hex' => '#2563EB'],
            ['name' => 'Azure', 'hex' => '#1766FF'],
            ['name' => 'Indigo', 'hex' => '#4F46E5'],
            ['name' => 'Violet', 'hex' => '#765CFF'],
            ['name' => 'Teal', 'hex' => '#0F9F9A'],
            ['name' => 'Emerald', 'hex' => '#20B96F'],
            ['name' => 'Amber', 'hex' => '#F5A524'],
            ['name' => 'Rose', 'hex' => '#E84D4D'],
        ];
    @endphp

    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="settings-icon-mail" viewBox="0 0 24 24">
            <path d="M4 6h16v12H4V6Z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
            <path d="m5 7 7 6 7-6" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="settings-icon-message" viewBox="0 0 24 24">
            <path d="M5 6h14v10H8l-3 3V6Z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
            <path d="M8 10h8M8 13h5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </symbol>
        <symbol id="settings-icon-palette" viewBox="0 0 24 24">
            <path d="M12 4a8 8 0 0 0 0 16h1.2a1.8 1.8 0 0 0 1.2-3.1 1.8 1.8 0 0 1 1.2-3.1H18A3 3 0 0 0 21 11a7 7 0 0 0-7-7h-2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M8.5 10h.01M11 7.5h.01M15 8.5h.01M7.5 13.5h.01" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
        </symbol>
        <symbol id="settings-icon-database" viewBox="0 0 24 24">
            <path d="M5 7c0-1.7 3.1-3 7-3s7 1.3 7 3-3.1 3-7 3-7-1.3-7-3Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M5 7v5c0 1.7 3.1 3 7 3s7-1.3 7-3V7M5 12v5c0 1.7 3.1 3 7 3s7-1.3 7-3v-5" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="settings-icon-upload" viewBox="0 0 24 24">
            <path d="M12 16V5M8 9l4-4 4 4M5 15v4h14v-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="settings-icon-check" viewBox="0 0 24 24">
            <path d="m5 12 4 4 10-10" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="settings-icon-refresh" viewBox="0 0 24 24">
            <path d="M19 12a7 7 0 1 1-2-4.9M19 5v5h-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="settings-icon-clipboard" viewBox="0 0 24 24">
            <path d="M9 5h6l1 2h3v13H5V7h3l1-2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 11h6M9 15h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
    </svg>

    <form class="settings-page" action="{{ route('system-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="email_notifications" value="0">
        <input type="hidden" name="ticket_alerts" value="0">
        <input type="hidden" name="system_announcements" value="0">
        <input type="hidden" name="weekly_reports" value="0">
        <input type="hidden" name="auto_backup" value="0">
        <input type="hidden" name="maintenance_mode" value="0">

        <x-status-prompt />

        @if ($errors->any())
            <ul class="settings-errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="settings-row settings-row-top">
            <section class="settings-card" aria-labelledby="general-settings-title">
                <div class="settings-card-header">
                    <span class="settings-icon" style="--icon-bg: var(--blue-soft); --icon-color: var(--blue)">
                        <svg><use href="#icon-settings"></use></svg>
                    </span>
                    <div>
                        <h2 class="settings-card-title" id="general-settings-title">General Settings</h2>
                        <p class="settings-card-subtitle">Configure basic system information.</p>
                    </div>
                </div>

                <div class="settings-fields">
                    <label class="settings-field">
                        <span class="settings-label">Company Name</span>
                        <input class="settings-input" type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}">
                    </label>
                    <label class="settings-field">
                        <span class="settings-label">System Name</span>
                        <input class="settings-input" type="text" name="system_name" value="{{ old('system_name', $settings->system_name) }}">
                    </label>
                    <label class="settings-field">
                        <span class="settings-label">Time Zone</span>
                        <span class="select-control">
                            <select class="settings-select" name="time_zone">
                                <option value="America/New_York" @selected(old('time_zone', $settings->time_zone) === 'America/New_York')>(UTC-05:00) Eastern Time (US & Canada)</option>
                                <option value="Asia/Singapore" @selected(old('time_zone', $settings->time_zone) === 'Asia/Singapore')>(UTC+08:00) Singapore</option>
                                <option value="Asia/Manila" @selected(old('time_zone', $settings->time_zone) === 'Asia/Manila')>(UTC+08:00) Manila</option>
                            </select>
                            <svg><use href="#icon-chevron-down"></use></svg>
                        </span>
                    </label>
                    <label class="settings-field">
                        <span class="settings-label">Date Format</span>
                        <span class="select-control">
                            <select class="settings-select" name="date_format">
                                <option value="F j, Y" @selected(old('date_format', $settings->date_format) === 'F j, Y')>May 19, 2024 (MMM DD, YYYY)</option>
                                <option value="Y-m-d" @selected(old('date_format', $settings->date_format) === 'Y-m-d')>2024-05-19 (YYYY-MM-DD)</option>
                                <option value="d/m/Y" @selected(old('date_format', $settings->date_format) === 'd/m/Y')>19/05/2024 (DD/MM/YYYY)</option>
                            </select>
                            <svg><use href="#icon-chevron-down"></use></svg>
                        </span>
                    </label>
                </div>
            </section>

            <section class="settings-card" aria-labelledby="notification-settings-title">
                <div class="settings-card-header">
                    <span class="settings-icon" style="--icon-bg: var(--blue-soft); --icon-color: var(--blue)">
                        <svg><use href="#icon-bell"></use></svg>
                    </span>
                    <div>
                        <h2 class="settings-card-title" id="notification-settings-title">Notification Settings</h2>
                        <p class="settings-card-subtitle">Manage how system notifications are delivered.</p>
                    </div>
                </div>

                <ul class="notification-list">
                    <li class="notification-item">
                        <span class="notification-icon"><svg><use href="#settings-icon-mail"></use></svg></span>
                        <span><span class="setting-name">Email Notifications</span><span class="setting-copy">Receive important updates via email.</span></span>
                        <label class="toggle" aria-label="Email Notifications">
                            <input type="checkbox" name="email_notifications" value="1" @checked(old('email_notifications', $settings->email_notifications))>
                            <span></span>
                        </label>
                    </li>
                    <li class="notification-item">
                        <span class="notification-icon"><svg><use href="#settings-icon-message"></use></svg></span>
                        <span><span class="setting-name">Ticket Alerts</span><span class="setting-copy">Get notified about ticket updates.</span></span>
                        <label class="toggle" aria-label="Ticket Alerts">
                            <input type="checkbox" name="ticket_alerts" value="1" @checked(old('ticket_alerts', $settings->ticket_alerts))>
                            <span></span>
                        </label>
                    </li>
                    <li class="notification-item">
                        <span class="notification-icon"><svg><use href="#icon-megaphone"></use></svg></span>
                        <span><span class="setting-name">System Announcements</span><span class="setting-copy">Receive system-wide announcements.</span></span>
                        <label class="toggle" aria-label="System Announcements">
                            <input type="checkbox" name="system_announcements" value="1" @checked(old('system_announcements', $settings->system_announcements))>
                            <span></span>
                        </label>
                    </li>
                    <li class="notification-item">
                        <span class="notification-icon"><svg><use href="#settings-icon-clipboard"></use></svg></span>
                        <span><span class="setting-name">Weekly Reports</span><span class="setting-copy">Receive weekly summary reports.</span></span>
                        <label class="toggle" aria-label="Weekly Reports">
                            <input type="checkbox" name="weekly_reports" value="1" @checked(old('weekly_reports', $settings->weekly_reports))>
                            <span></span>
                        </label>
                    </li>
                </ul>
            </section>
        </div>

        <div class="settings-row settings-row-bottom">
            <section class="settings-card" aria-labelledby="appearance-settings-title">
                <div class="settings-card-header">
                    <span class="settings-icon" style="--icon-bg: var(--violet-soft); --icon-color: var(--violet)">
                        <svg><use href="#settings-icon-palette"></use></svg>
                    </span>
                    <div>
                        <h2 class="settings-card-title" id="appearance-settings-title">Appearance / Branding</h2>
                        <p class="settings-card-subtitle">Customize the look and feel of the system.</p>
                    </div>
                </div>

                <div class="settings-fields">
                    <div class="settings-field">
                        <span class="settings-label">Theme</span>
                        <div class="theme-choice-grid" role="radiogroup" aria-label="Theme">
                            <label class="theme-choice">
                                <input type="radio" name="theme" value="light" @checked(old('theme', $settings->theme) === 'light')>
                                <span class="theme-choice-content">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"></path></svg>
                                    <span>Light</span>
                                </span>
                            </label>
                            <label class="theme-choice">
                                <input type="radio" name="theme" value="dark" @checked(old('theme', $settings->theme) === 'dark')>
                                <span class="theme-choice-content">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"></path></svg>
                                    <span>Dark</span>
                                </span>
                            </label>
                            <label class="theme-choice">
                                <input type="radio" name="theme" value="system" @checked(old('theme', $settings->theme) === 'system')>
                                <span class="theme-choice-content">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"></rect><path d="M8 21h8M12 17v4"></path></svg>
                                    <span>System</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <label class="settings-field">
                        <span class="settings-label">Primary Color</span>
                        <span class="color-stack">
                            <span class="color-control">
                                <span class="color-swatch" id="primary-color-preview" style="background: {{ $pickerPrimaryColor }}"></span>
                                <input class="settings-input color-input" id="primary_color" type="text" name="primary_color" value="{{ $currentPrimaryColor }}">
                                <input class="native-color-input" id="primary_color_picker" type="color" value="{{ $pickerPrimaryColor }}" aria-label="Primary color picker">
                            </span>
                            <span class="color-palette" aria-label="Suggested primary colors">
                                @foreach ($colorPalette as $color)
                                    <button
                                        class="color-palette-button {{ $pickerPrimaryColor === $color['hex'] ? 'active' : '' }}"
                                        type="button"
                                        data-color="{{ $color['hex'] }}"
                                        style="--palette-color: {{ $color['hex'] }}"
                                        aria-label="{{ $color['name'] }} {{ $color['hex'] }}"
                                        title="{{ $color['name'] }}"
                                    ></button>
                                @endforeach
                            </span>
                        </span>
                    </label>
                    <div class="settings-field">
                        <span class="settings-label">Live Preview</span>
                        <div>
                            <div class="appearance-preview" id="appearance-preview" data-theme="{{ old('theme', $settings->theme) }}">
                                <div class="appearance-preview-side">
                                    <span class="appearance-preview-logo"></span>
                                    <span class="appearance-preview-nav active"></span>
                                    <span class="appearance-preview-nav"></span>
                                    <span class="appearance-preview-nav"></span>
                                </div>
                                <div class="appearance-preview-main">
                                    <div class="appearance-preview-header">
                                        <span class="appearance-preview-title"></span>
                                        <span class="appearance-preview-avatar"></span>
                                    </div>
                                    <div class="appearance-preview-content">
                                        <span class="appearance-preview-panel">
                                            <span class="appearance-preview-line"></span>
                                            <span class="appearance-preview-line primary"></span>
                                            <span class="appearance-preview-line"></span>
                                        </span>
                                        <span class="appearance-preview-panel">
                                            <span class="appearance-preview-line"></span>
                                            <span class="appearance-preview-line"></span>
                                            <span class="appearance-preview-line primary"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="appearance-preview-note">Preview updates instantly. Save changes to apply the theme across all modules.</span>
                        </div>
                    </div>
                    <div class="settings-field">
                        <span class="settings-label">System Logo</span>
                        <div class="logo-dropzone">
                            <div class="logo-preview {{ $logoUrl ? 'has-logo' : '' }}" id="logo-preview" aria-label="System logo preview">
                                <img
                                    class="logo-image {{ $logoUrl ? '' : 'is-hidden' }}"
                                    id="logo-preview-image"
                                    @if ($logoUrl) src="{{ $logoUrl }}" @endif
                                    alt=""
                                >
                                <span class="logo-placeholder {{ $logoUrl ? 'is-hidden' : '' }}" id="logo-preview-placeholder">X<sup>8</sup></span>
                            </div>
                            <div class="logo-upload">
                                <p class="upload-title">Drag and drop your logo here</p>
                                <p class="upload-copy" id="logo-upload-copy">PNG, JPG or SVG (max. 2MB)</p>
                                <label class="upload-button" for="logo">
                                    <svg><use href="#settings-icon-upload"></use></svg>
                                    <span>Upload Logo</span>
                                </label>
                                <input class="file-input" id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.svg,image/png,image/jpeg,image/svg+xml">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="settings-card" aria-labelledby="backup-settings-title">
                <div class="settings-card-header">
                    <span class="settings-icon" style="--icon-bg: var(--green-soft); --icon-color: var(--green)">
                        <svg><use href="#settings-icon-database"></use></svg>
                    </span>
                    <div>
                        <h2 class="settings-card-title" id="backup-settings-title">Backup & Maintenance</h2>
                        <p class="settings-card-subtitle">Manage backups and system maintenance.</p>
                    </div>
                </div>

                <ul class="maintenance-list">
                    <li class="maintenance-item">
                        <span><span class="setting-name">Auto Backup</span><span class="setting-copy">Automatically backup system data.</span></span>
                        <label class="toggle" aria-label="Auto Backup">
                            <input type="checkbox" name="auto_backup" value="1" @checked(old('auto_backup', $settings->auto_backup))>
                            <span></span>
                        </label>
                    </li>
                    <li class="maintenance-item">
                        <span class="settings-label">Backup Frequency</span>
                        <span class="select-control maintenance-frequency">
                            <select class="settings-select" name="backup_frequency">
                                <option value="daily" @selected(old('backup_frequency', $settings->backup_frequency) === 'daily')>Daily</option>
                                <option value="weekly" @selected(old('backup_frequency', $settings->backup_frequency) === 'weekly')>Weekly</option>
                                <option value="monthly" @selected(old('backup_frequency', $settings->backup_frequency) === 'monthly')>Monthly</option>
                            </select>
                            <svg><use href="#icon-chevron-down"></use></svg>
                        </span>
                    </li>
                    <li class="maintenance-item">
                        <span><span class="setting-name">Maintenance Mode</span><span class="setting-copy">Put the system in maintenance mode.</span></span>
                        <label class="toggle" aria-label="Maintenance Mode">
                            <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings->maintenance_mode))>
                            <span></span>
                        </label>
                    </li>
                </ul>
            </section>

            <section class="settings-card" aria-labelledby="system-information-title">
                <div class="settings-card-header">
                    <span class="settings-icon" style="--icon-bg: var(--blue-soft); --icon-color: var(--blue)">
                        <svg><use href="#icon-info"></use></svg>
                    </span>
                    <div>
                        <h2 class="settings-card-title" id="system-information-title">System Information</h2>
                        <p class="settings-card-subtitle">View important system details.</p>
                    </div>
                </div>

                <ul class="info-list">
                    @foreach ($systemInfo as $label => $value)
                        <li class="info-row">
                            <span>{{ $label }}</span>
                            @if ($label === 'Environment')
                                <span class="environment-chip">{{ $value }}</span>
                            @else
                                <span class="info-value">{{ $value }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <div class="settings-actions">
            <button class="settings-action settings-action-primary" type="submit">
                <svg><use href="#settings-icon-check"></use></svg>
                <span>Save Changes</span>
            </button>
            <button class="settings-action settings-action-light" type="reset">
                <svg><use href="#settings-icon-refresh"></use></svg>
                <span>Reset</span>
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (() => {
            const logoInput = document.getElementById('logo');
            const preview = document.getElementById('logo-preview');
            const previewImage = document.getElementById('logo-preview-image');
            const placeholder = document.getElementById('logo-preview-placeholder');
            const uploadCopy = document.getElementById('logo-upload-copy');
            let objectUrl = null;

            if (!logoInput || !preview || !previewImage || !placeholder) {
                return;
            }

            const showPlaceholder = () => {
                previewImage.removeAttribute('src');
                previewImage.classList.add('is-hidden');
                placeholder.classList.remove('is-hidden');
                preview.classList.remove('has-logo');
            };

            previewImage.addEventListener('error', showPlaceholder);

            if (previewImage.getAttribute('src') && previewImage.complete && previewImage.naturalWidth === 0) {
                showPlaceholder();
            }

            logoInput.addEventListener('change', () => {
                const file = logoInput.files && logoInput.files[0];

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                if (!file) {
                    return;
                }

                objectUrl = URL.createObjectURL(file);
                previewImage.src = objectUrl;
                previewImage.alt = file.name ? `System logo preview: ${file.name}` : 'System logo preview';
                previewImage.classList.remove('is-hidden');
                placeholder.classList.add('is-hidden');
                preview.classList.add('has-logo');

                if (uploadCopy) {
                    uploadCopy.textContent = file.name ? `Selected: ${file.name}` : 'Selected logo ready to save';
                }
            });

            const primaryColorInput = document.getElementById('primary_color');
            const primaryColorPicker = document.getElementById('primary_color_picker');
            const primaryColorPreview = document.getElementById('primary-color-preview');
            const paletteButtons = Array.from(document.querySelectorAll('.color-palette-button'));
            const themeInputs = Array.from(document.querySelectorAll('input[name="theme"]'));
            const appearancePreview = document.getElementById('appearance-preview');
            const colorPattern = /^#[0-9A-Fa-f]{6}$/;

            const syncPrimaryColor = (color) => {
                if (!colorPattern.test(color)) {
                    return;
                }

                const normalizedColor = color.toUpperCase();

                if (primaryColorInput) {
                    primaryColorInput.value = normalizedColor;
                }

                if (primaryColorPicker) {
                    primaryColorPicker.value = normalizedColor;
                }

                if (primaryColorPreview) {
                    primaryColorPreview.style.background = normalizedColor;
                }

                if (appearancePreview) {
                    appearancePreview.style.setProperty('--preview-primary', normalizedColor);
                }

                const red = parseInt(normalizedColor.slice(1, 3), 16);
                const green = parseInt(normalizedColor.slice(3, 5), 16);
                const blue = parseInt(normalizedColor.slice(5, 7), 16);
                const rgb = `${red}, ${green}, ${blue}`;
                const root = document.documentElement;

                root.style.setProperty('--primary', normalizedColor);
                root.style.setProperty('--primary-rgb', rgb);
                root.style.setProperty('--blue', normalizedColor);
                root.style.setProperty('--blue-rgb', rgb);

                paletteButtons.forEach((button) => {
                    button.classList.toggle('active', button.dataset.color.toUpperCase() === normalizedColor);
                });
            };

            const syncTheme = (theme) => {
                if (!['light', 'dark', 'system'].includes(theme)) {
                    return;
                }

                const root = document.documentElement;
                root.classList.remove('app-theme-light', 'app-theme-dark', 'app-theme-system');
                root.classList.add(`app-theme-${theme}`);

                if (appearancePreview) {
                    appearancePreview.dataset.theme = theme;
                }
            };

            paletteButtons.forEach((button) => {
                button.addEventListener('click', () => syncPrimaryColor(button.dataset.color));
            });

            themeInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    if (input.checked) {
                        syncTheme(input.value);
                    }
                });
            });

            if (primaryColorPicker) {
                primaryColorPicker.addEventListener('input', () => syncPrimaryColor(primaryColorPicker.value));
            }

            if (primaryColorInput) {
                primaryColorInput.addEventListener('input', () => {
                    if (colorPattern.test(primaryColorInput.value)) {
                        syncPrimaryColor(primaryColorInput.value);
                    }
                });
            }

            const selectedTheme = themeInputs.find((input) => input.checked);
            if (selectedTheme) {
                syncTheme(selectedTheme.value);
            }

            if (primaryColorInput && colorPattern.test(primaryColorInput.value)) {
                syncPrimaryColor(primaryColorInput.value);
            }

            window.addEventListener('beforeunload', () => {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                }
            });
        })();
    </script>
@endpush

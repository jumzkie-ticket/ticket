@php
    try {
        $appearanceSettings = $appearanceSettings ?? \App\Models\SystemSetting::current();
    } catch (\Throwable) {
        $appearanceSettings = (object) \App\Models\SystemSetting::defaults();
    }

    $appearanceTheme = in_array($appearanceSettings->theme ?? 'light', ['light', 'dark', 'system'], true)
        ? $appearanceSettings->theme
        : 'light';
    $appearancePrimary = (string) ($appearanceSettings->primary_color ?? '#2563EB');
    $appearancePrimary = preg_match('/^#[0-9A-Fa-f]{6}$/', $appearancePrimary)
        ? strtoupper($appearancePrimary)
        : '#2563EB';
    [$appearancePrimaryR, $appearancePrimaryG, $appearancePrimaryB] = sscanf(ltrim($appearancePrimary, '#'), '%02x%02x%02x');
    $appearancePrimaryRgb = "{$appearancePrimaryR}, {$appearancePrimaryG}, {$appearancePrimaryB}";
    $appearanceSystemName = (string) ($appearanceSettings->system_name ?? 'XTI Ticket Support System');
    $appearanceCompanyName = (string) ($appearanceSettings->company_name ?? 'Xceler8 Technologies Inc.');
    $appearanceLogoUrl = null;

    try {
        if (
            ! empty($appearanceSettings->logo_path)
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($appearanceSettings->logo_path)
        ) {
            $appearanceLogoUrl = '/storage/'.ltrim($appearanceSettings->logo_path, '/');
        }
    } catch (\Throwable) {
        $appearanceLogoUrl = null;
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="app-theme-{{ $appearanceTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>@yield('title', 'Dashboard') | {{ $appearanceSystemName }}</title>

    @fonts

    <style>
        :root {
            --ink: #071b4d;
            --muted: #61708f;
            --line: #d8e2f2;
            --panel: #ffffff;
            --canvas: #f4f7fc;
            --blue: {{ $appearancePrimary }};
            --blue-rgb: {{ $appearancePrimaryRgb }};
            --navy: #061d4d;
            --navy-2: #092b68;
            --blue-soft: rgba({{ $appearancePrimaryRgb }}, .12);
            --green: #20b96f;
            --green-soft: #dcf8e9;
            --amber: #f5a524;
            --amber-soft: #fff3d7;
            --violet: #765cff;
            --violet-soft: #eeeaff;
            --red: #e84d4d;
            --red-soft: #ffe3e0;
            --teal: #18aac4;
            --shadow: 0 16px 46px rgba(10, 33, 74, .08);
            color-scheme: light;
        }

        :root.app-theme-dark {
            --ink: #e8eefc;
            --muted: #a8b5cc;
            --line: #263653;
            --panel: #101a2e;
            --canvas: #07111f;
            --blue-soft: rgba({{ $appearancePrimaryRgb }}, .22);
            --green-soft: rgba(32, 185, 111, .18);
            --amber-soft: rgba(245, 165, 36, .18);
            --violet-soft: rgba(118, 92, 255, .2);
            --red-soft: rgba(232, 77, 77, .18);
            --shadow: 0 18px 48px rgba(0, 0, 0, .26);
            color-scheme: dark;
        }

        @media (prefers-color-scheme: dark) {
            :root.app-theme-system {
                --ink: #e8eefc;
                --muted: #a8b5cc;
                --line: #263653;
                --panel: #101a2e;
                --canvas: #07111f;
                --blue-soft: rgba({{ $appearancePrimaryRgb }}, .22);
                --green-soft: rgba(32, 185, 111, .18);
                --amber-soft: rgba(245, 165, 36, .18);
                --violet-soft: rgba(118, 92, 255, .2);
                --red-soft: rgba(232, 77, 77, .18);
                --shadow: 0 18px 48px rgba(0, 0, 0, .26);
                color-scheme: dark;
            }
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

        .app-frame {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 230px minmax(0, 1fr);
        }

        .side-pane {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 22px;
            padding: 24px 18px 10px;
            overflow-y: auto;
            background: linear-gradient(180deg, #103f91 0%, #061b46 34%, #051738 100%);
            background: linear-gradient(180deg, color-mix(in srgb, var(--blue) 60%, #061b46) 0%, #061b46 34%, #051738 100%);
            color: #ffffff;
        }

        .brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 1px solid rgba(255, 255, 255, .42);
            background: rgba({{ $appearancePrimaryRgb }}, .28);
            color: #ffffff;
            text-decoration: none;
        }

        .brand strong {
            display: block;
            color: #ffffff;
            font-size: 15px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .brand span {
            display: block;
            margin-top: 3px;
            color: rgba(255, 255, 255, .86);
            font-size: 7px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .nav-groups {
            display: grid;
            gap: 10px;
        }

        .nav-group {
            display: grid;
            gap: 7px;
        }

        .nav-parent {
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 10px 0 12px;
            border-radius: 7px;
            color: #ffffff;
            list-style: none;
            cursor: pointer;
        }

        .nav-parent::-webkit-details-marker {
            display: none;
        }

        .nav-parent:hover,
        .nav-parent:focus-visible,
        .nav-group[open] > .nav-parent {
            background: rgba(255, 255, 255, .1);
            color: #ffffff;
            outline: none;
        }

        .nav-heading {
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .nav-parent svg {
            width: 14px;
            height: 14px;
            flex: 0 0 auto;
            transition: transform .18s ease;
        }

        .nav-group[open] > .nav-parent svg {
            transform: rotate(180deg);
        }

        .nav-group-items {
            display: grid;
            gap: 7px;
            padding-top: 1px;
        }

        .nav-item {
            min-height: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 0 22px;
            border-radius: 7px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 850;
            text-decoration: none;
        }

        .nav-item svg {
            width: 17px;
            height: 17px;
            flex: 0 0 auto;
        }

        .nav-item:hover,
        .nav-item:focus-visible,
        .nav-item.active {
            background: var(--blue);
            outline: none;
        }

        .support-card {
            display: grid;
            gap: 8px;
            margin-top: auto;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 7px;
            background: rgba({{ $appearancePrimaryRgb }}, .12);
        }

        .support-card h2 {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
        }

        .support-card svg {
            width: 16px;
            height: 16px;
        }

        .support-card p {
            margin: 0;
            color: #ffffff;
            font-size: 11px;
            font-weight: 850;
            line-height: 1.55;
        }

        .support-card small {
            color: rgba(255, 255, 255, .82);
            font-size: 10px;
            font-weight: 750;
        }

        .main-pane {
            min-width: 0;
            background: var(--canvas);
        }

        .main-header {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 32px;
            border-bottom: 1px solid var(--line);
            background: var(--panel);
        }

        .page-title {
            margin: 0;
            color: var(--ink);
            font-size: 27px;
            line-height: 1.1;
            font-weight: 900;
        }

        .page-subtitle {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 13px;
            flex-wrap: wrap;
        }

        .header-icon {
            position: relative;
            width: 34px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #061845;
        }

        .header-icon svg {
            width: 17px;
            height: 17px;
        }

        .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            min-width: 15px;
            height: 15px;
            display: inline-grid;
            place-items: center;
            border-radius: 999px;
            background: var(--red);
            color: #ffffff;
            font-size: 9px;
            font-weight: 900;
        }

        .profile-menu {
            position: relative;
            min-width: 178px;
        }

        .profile-toggle {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 12px;
            border: 1px solid #cbd9ee;
            border-radius: 7px;
            background: var(--panel);
            color: var(--ink);
            list-style: none;
            cursor: pointer;
        }

        .profile-toggle::-webkit-details-marker {
            display: none;
        }

        .profile-main {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .avatar {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border: 4px solid var(--blue-soft);
            border-radius: 999px;
            background: var(--blue);
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            flex: 0 0 auto;
        }

        .profile-name {
            overflow: hidden;
            color: #17315f;
            font-size: 12px;
            font-weight: 850;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-toggle svg {
            width: 14px;
            height: 14px;
            color: #17315f;
            flex: 0 0 auto;
        }

        .profile-menu[open] .profile-toggle {
            border-color: #aac6ff;
            box-shadow: 0 0 0 3px rgba({{ $appearancePrimaryRgb }}, .12);
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            z-index: 30;
            width: 100%;
            min-width: 178px;
            padding: 7px;
            border: 1px solid #cbd9ee;
            border-radius: 7px;
            background: var(--panel);
            box-shadow: 0 16px 38px rgba(10, 33, 74, .14);
        }

        .logout-form {
            margin: 0;
        }

        .profile-action {
            width: 100%;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 10px;
            border: 0;
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
            text-align: left;
        }

        .profile-action:hover,
        .profile-action:focus-visible {
            background: var(--blue-soft);
            color: var(--blue);
            outline: none;
        }

        .profile-action svg {
            width: 15px;
            height: 15px;
        }

        .content-area {
            min-width: 0;
            padding: 24px 32px 36px;
        }

        .app-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 0 32px 24px;
            color: #426392;
            font-size: 12px;
            font-weight: 650;
        }

        .version-chip {
            color: #61708f;
            font-size: 11px;
            font-weight: 850;
        }

        @media (max-width: 1120px) {
            .app-frame {
                grid-template-columns: 1fr;
            }

            .side-pane {
                position: static;
                height: auto;
                padding: 18px;
            }

            .nav-groups {
                grid-template-columns: repeat(2, minmax(230px, 1fr));
            }

            .support-card {
                margin-top: 0;
            }
        }

        @media (max-width: 720px) {
            .main-header {
                align-items: stretch;
                flex-direction: column;
                padding: 18px;
            }

            .content-area {
                padding: 18px;
            }

            .app-footer {
                align-items: flex-start;
                flex-direction: column;
                padding: 0 18px 18px;
            }

            .nav-groups {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 23px;
            }

            .header-actions,
            .profile-menu,
            .logout-form,
            .profile-action {
                width: 100%;
            }

            .profile-dropdown {
                position: static;
                margin-top: 8px;
            }

            .header-actions {
                justify-content: flex-start;
            }
        }
    </style>

    @stack('styles')

    <style>
        :root {
            --blue: {{ $appearancePrimary }};
            --blue-rgb: {{ $appearancePrimaryRgb }};
            --blue-soft: rgba({{ $appearancePrimaryRgb }}, .12);
        }

        :root.app-theme-dark {
            --ink: #e8eefc;
            --muted: #a8b5cc;
            --line: #263653;
            --panel: #101a2e;
            --canvas: #07111f;
            --blue-soft: rgba({{ $appearancePrimaryRgb }}, .22);
            --green-soft: rgba(32, 185, 111, .18);
            --amber-soft: rgba(245, 165, 36, .18);
            --violet-soft: rgba(118, 92, 255, .2);
            --red-soft: rgba(232, 77, 77, .18);
            --shadow: 0 18px 48px rgba(0, 0, 0, .26);
            color-scheme: dark;
        }

        @media (prefers-color-scheme: dark) {
            :root.app-theme-system {
                --ink: #e8eefc;
                --muted: #a8b5cc;
                --line: #263653;
                --panel: #101a2e;
                --canvas: #07111f;
                --blue-soft: rgba({{ $appearancePrimaryRgb }}, .22);
                --green-soft: rgba(32, 185, 111, .18);
                --amber-soft: rgba(245, 165, 36, .18);
                --violet-soft: rgba(118, 92, 255, .2);
                --red-soft: rgba(232, 77, 77, .18);
                --shadow: 0 18px 48px rgba(0, 0, 0, .26);
                color-scheme: dark;
            }
        }

        body,
        .main-pane {
            background: var(--canvas);
            color: var(--ink);
        }

        .main-header,
        .profile-toggle,
        .profile-dropdown,
        .profile-action,
        .metric-card,
        .panel,
        .settings-card,
        .header-title,
        .analytics-strip,
        .detail-panel,
        .logo-dropzone,
        .logo-preview.has-logo {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .date-filter,
        .panel-filter,
        .button-link,
        .button-light,
        .icon-action,
        .page-link,
        .settings-input,
        .settings-select,
        .settings-action-light,
        .upload-button,
        .native-color-input,
        .input,
        .permission-check,
        .search-input {
            border-color: var(--line);
            background: var(--panel);
            color: var(--ink);
        }

        .color-palette-button {
            border-color: var(--panel);
        }

        .nav-item.active,
        .nav-item:hover,
        .nav-item:focus-visible,
        .button-primary,
        .settings-action-primary {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
        }

        .button-primary:hover,
        .button-primary:focus-visible,
        .settings-action-primary:hover,
        .settings-action-primary:focus-visible {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
            filter: brightness(.94);
        }

        .page-title,
        .section-title,
        .panel-title,
        .settings-card-title,
        .settings-label,
        .setting-name,
        .metric-label,
        .metric-value,
        .panel-head h3,
        .ticket-code,
        .summary-value,
        .role-main,
        .analytics-value,
        .detail-name,
        .guide-title,
        .info-row,
        .profile-name,
        .header-icon,
        .profile-toggle svg {
            color: var(--ink);
        }

        .page-subtitle,
        .settings-card-subtitle,
        .setting-copy,
        .ticket-summary,
        .legend-item strong,
        .analytics-label,
        .role-sub,
        .about-copy,
        .guide-list span,
        .info-value,
        .recent-table thead th,
        .roles-table th,
        .app-footer,
        .version-chip {
            color: var(--muted);
        }

        .recent-table th,
        .recent-table td,
        .roles-table th,
        .roles-table td,
        .notification-item,
        .maintenance-item,
        .info-row,
        .table-footer,
        .form-actions,
        .analytics-item,
        .side-stat-list {
            border-color: var(--line);
        }

        .settings-input:focus,
        .settings-select:focus,
        .input:focus,
        .search-input:focus,
        .profile-menu[open] .profile-toggle {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        /* Shared appearance layer: loaded after every module's local styles. */
        :root {
            --primary: {{ $appearancePrimary }};
            --primary-rgb: {{ $appearancePrimaryRgb }};
            --primary-soft: rgba({{ $appearancePrimaryRgb }}, .11);
            --panel-subtle: #f8fafc;
            --panel-muted: #f1f5f9;
            --input-bg: #ffffff;
            --line-strong: #c8d3e1;
            --sidebar-start: color-mix(in srgb, var(--primary) 68%, #102a56);
            --sidebar-end: color-mix(in srgb, var(--primary) 14%, #07162f);
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, .05);
            --shadow: 0 10px 30px rgba(15, 23, 42, .07);
            --radius-sm: 8px;
            --radius: 12px;
            --blue: var(--primary);
            --blue-rgb: var(--primary-rgb);
            --blue-soft: var(--primary-soft);
        }

        :root.app-theme-dark {
            --ink: #e7edf7;
            --muted: #9eacc0;
            --line: #2a3a51;
            --line-strong: #3a4d68;
            --panel: #111c2e;
            --panel-subtle: #152238;
            --panel-muted: #1b2a42;
            --input-bg: #0d1829;
            --canvas: #091321;
            --primary-soft: rgba(var(--primary-rgb), .2);
            --sidebar-start: color-mix(in srgb, var(--primary) 34%, #101c30);
            --sidebar-end: #050b14;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, .2);
            --shadow: 0 14px 34px rgba(0, 0, 0, .24);
        }

        :root.app-theme-dark .content-area :is(
            label,
            legend,
            [class$="-label"],
            [class*="-label "],
            .settings-label,
            .field-label,
            .form-label
        ) {
            color: var(--ink) !important;
        }

        @media (prefers-color-scheme: dark) {
            :root.app-theme-system {
                --ink: #e7edf7;
                --muted: #9eacc0;
                --line: #2a3a51;
                --line-strong: #3a4d68;
                --panel: #111c2e;
                --panel-subtle: #152238;
                --panel-muted: #1b2a42;
                --input-bg: #0d1829;
                --canvas: #091321;
                --primary-soft: rgba(var(--primary-rgb), .2);
                --sidebar-start: color-mix(in srgb, var(--primary) 34%, #101c30);
                --sidebar-end: #050b14;
                --shadow-sm: 0 1px 2px rgba(0, 0, 0, .2);
                --shadow: 0 14px 34px rgba(0, 0, 0, .24);
            }

            :root.app-theme-system .content-area :is(
                label,
                legend,
                [class$="-label"],
                [class*="-label "],
                .settings-label,
                .field-label,
                .form-label
            ) {
                color: var(--ink) !important;
            }
        }

        body {
            background: var(--canvas);
            color: var(--ink);
            transition: background-color .2s ease, color .2s ease;
        }

        .app-frame {
            grid-template-columns: 248px minmax(0, 1fr);
        }

        .side-pane {
            gap: 20px;
            padding: 22px 16px 14px;
            background: linear-gradient(165deg, var(--sidebar-start) 0%, var(--sidebar-end) 72%);
            box-shadow: 8px 0 30px rgba(4, 14, 31, .08);
        }

        .brand {
            width: 100%;
            min-height: 118px;
            flex-direction: column;
            justify-content: center;
            gap: 9px;
            padding: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 12px;
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(8px);
        }

        .brand-logo-image,
        .brand-monogram {
            width: 100%;
            height: 66px;
            flex: 0 0 auto;
            border-radius: 9px;
        }

        .brand-logo-image {
            padding: 6px 10px;
            object-fit: contain;
            background: rgba(255, 255, 255, .96);
        }

        .brand .brand-monogram {
            width: 66px;
            display: grid;
            place-items: center;
            margin: 0;
            background: #ffffff;
            color: var(--primary);
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.3px;
        }

        .brand .brand-copy {
            width: 100%;
            min-width: 0;
            display: grid;
            gap: 3px;
            margin: 0;
            text-align: center;
        }

        .brand .brand-copy strong,
        .brand .brand-copy small {
            color: #ffffff;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .brand .brand-copy strong {
            font-size: 12px;
            line-height: 1.3;
            font-weight: 850;
            letter-spacing: .15px;
        }

        .brand .brand-copy small {
            color: rgba(255, 255, 255, .7);
            font-size: 9px;
            line-height: 1.35;
            font-weight: 650;
            letter-spacing: .1px;
        }

        .nav-groups {
            gap: 7px;
        }

        .nav-group {
            gap: 5px;
        }

        .nav-parent,
        .nav-item {
            border-radius: 9px;
        }

        .nav-parent {
            min-height: 36px;
        }

        .nav-heading {
            color: rgba(255, 255, 255, .66);
            font-size: 11px;
            letter-spacing: .75px;
        }

        .nav-group-items {
            gap: 4px;
        }

        .nav-item {
            min-height: 42px;
            padding-left: 18px;
            color: rgba(255, 255, 255, .82);
            font-size: 14px;
            line-height: 1.3;
            font-weight: 700;
        }

        .nav-item.active {
            background: var(--primary);
            box-shadow: 0 8px 20px rgba(var(--primary-rgb), .28);
            color: #ffffff;
        }

        .nav-item:hover:not(.active),
        .nav-item:focus-visible:not(.active) {
            background: rgba(255, 255, 255, .09);
            color: #ffffff;
        }

        .support-card {
            border-radius: 11px;
            background: rgba(255, 255, 255, .07);
        }

        .main-header {
            min-height: 82px;
            padding: 16px 30px;
            border-color: var(--line);
            background: color-mix(in srgb, var(--panel) 94%, transparent);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(12px);
        }

        .page-title {
            font-size: 25px;
            letter-spacing: -.5px;
        }

        .header-icon {
            border: 1px solid transparent;
            border-radius: 9px;
            color: var(--ink);
        }

        .header-icon:hover,
        .header-icon:focus-visible {
            border-color: var(--line);
            background: var(--panel-subtle);
            color: var(--primary);
            outline: none;
        }

        .profile-toggle,
        .profile-dropdown {
            border-color: var(--line);
            border-radius: 10px;
            background: var(--panel);
            box-shadow: var(--shadow-sm);
        }

        .profile-name,
        .profile-toggle svg {
            color: var(--ink);
        }

        .content-area {
            width: 100%;
            max-width: 1680px;
            margin: 0 auto;
            padding: 26px 30px 38px;
        }

        .content-area :is([class$="-card"], [class$="-panel"], [class$="-section"], [class$="-modal-dialog"]) {
            border-color: var(--line) !important;
            background: var(--panel) !important;
            color: var(--ink);
            box-shadow: var(--shadow-sm);
        }

        .content-area :is([class$="-title"], [class$="-name"], [class$="-label"], h2, h3, h4) {
            color: var(--ink);
        }

        .content-area .header-title h1 {
            color: var(--ink) !important;
        }

        .content-area .header-title .eyebrow {
            color: var(--primary) !important;
        }

        .content-area .header-title p {
            color: var(--muted) !important;
        }

        .content-area :is([class$="-copy"], [class$="-subtitle"], [class$="-meta"], [class$="-hint"], small) {
            color: var(--muted);
        }

        .content-area :is(input:not([type="checkbox"]):not([type="radio"]):not([type="color"]), select, textarea) {
            border-color: var(--line-strong) !important;
            background: var(--input-bg) !important;
            color: var(--ink) !important;
        }

        .content-area :is(input, select, textarea)::placeholder {
            color: var(--muted);
            opacity: .82;
        }

        .content-area :is(input, select, textarea):focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), .13) !important;
            outline: none;
        }

        .content-area :is(table, thead, tbody, tr, th, td) {
            border-color: var(--line) !important;
        }

        .content-area table {
            background: var(--panel);
            color: var(--ink);
        }

        .content-area thead th {
            background: var(--panel-muted) !important;
            color: var(--muted) !important;
        }

        .content-area tbody td {
            background: var(--panel) !important;
            color: var(--ink);
        }

        .content-area tbody tr:hover td {
            background: var(--panel-subtle) !important;
        }

        .content-area .man-days-header {
            color: var(--primary) !important;
        }

        .content-area .man-days-table {
            border-color: var(--line) !important;
            background: var(--panel) !important;
        }

        .content-area .man-days-col {
            border-color: var(--line) !important;
            background: var(--panel) !important;
        }

        .content-area .man-days-col-header {
            border-color: var(--line) !important;
            background: var(--panel-muted) !important;
            color: var(--ink) !important;
        }

        .content-area .summary-section .summary-value {
            color: var(--green) !important;
        }

        .content-area :is(button, a)[class*="primary"],
        .content-area :is(.button-primary, .btn-primary, .contact-button-primary, .client-button-primary, .product-button, .package-button) {
            border-color: var(--primary) !important;
            background: var(--primary) !important;
            color: #ffffff !important;
        }

        .content-area :is(button, a)[class*="primary"]:hover,
        .content-area :is(button, a)[class*="primary"]:focus-visible {
            filter: brightness(.94);
        }

        .content-area a:not([class]) {
            color: var(--primary);
        }

        .app-footer {
            padding: 0 30px 24px;
            color: var(--muted);
        }

        @media (max-width: 1120px) {
            .app-frame {
                grid-template-columns: 1fr;
            }

            .side-pane {
                box-shadow: none;
            }

            .brand {
                max-width: 340px;
            }
        }

        @media (max-width: 720px) {
            .content-area {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="icon-dashboard" viewBox="0 0 24 24">
            <path d="M5 11h6V5H5v6ZM13 19h6v-6h-6v6ZM5 19h6v-6H5v6ZM13 5v6h6V5h-6Z" fill="currentColor"/>
        </symbol>
        <symbol id="icon-plus" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-ticket" viewBox="0 0 24 24">
            <path d="M5 8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-book" viewBox="0 0 24 24">
            <path d="M5 5h6a3 3 0 0 1 3 3v11a3 3 0 0 0-3-3H5V5ZM19 5h-5a3 3 0 0 0-3 3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-megaphone" viewBox="0 0 24 24">
            <path d="M5 13h3l8 4V7l-8 4H5v2ZM8 13l1 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-users" viewBox="0 0 24 24">
            <path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M18 11.5a2.4 2.4 0 0 0 0-4.5M19 18c0-1.5-.7-2.7-1.9-3.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-client" viewBox="0 0 24 24">
            <path d="M8 19c0-2.2 1.8-4 4-4s4 1.8 4 4M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM5 20h14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-chart" viewBox="0 0 24 24">
            <path d="M5 19V5M5 19h14M9 16V9M13 16V7M17 16v-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-report" viewBox="0 0 24 24">
            <path d="M7 4h10v16H7V4Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M10 8h4M10 12h4M10 16h2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-doc" viewBox="0 0 24 24">
            <path d="M7 4h7l4 4v12H7V4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M14 4v5h4M10 13h5M10 16h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-gauge" viewBox="0 0 24 24">
            <path d="M5 17a7 7 0 1 1 14 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="m12 17 4-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-info" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 11v5M12 8h.01" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-role" viewBox="0 0 24 24">
            <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6v-9Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-settings" viewBox="0 0 24 24">
            <path d="M12 9.2a2.8 2.8 0 1 0 0 5.6 2.8 2.8 0 0 0 0-5.6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="m19 13.5 1.3 1-1.6 2.8-1.6-.7a7.6 7.6 0 0 1-1.4.8l-.2 1.7h-3.2l-.2-1.7a7.6 7.6 0 0 1-1.4-.8l-1.6.7-1.6-2.8 1.3-1a7.2 7.2 0 0 1 0-1.6l-1.3-1 1.6-2.8 1.6.7c.4-.3.9-.6 1.4-.8l.2-1.7h3.2l.2 1.7c.5.2 1 .5 1.4.8l1.6-.7 1.6 2.8-1.3 1a7.2 7.2 0 0 1 0 1.6Z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-headset" viewBox="0 0 24 24">
            <path d="M5 13v-1a7 7 0 0 1 14 0v1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M5 13h3v5H5v-5ZM16 13h3v5h-3v-5ZM16 20h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-calendar" viewBox="0 0 24 24">
            <path d="M7 4v3M17 4v3M5 8h14M6 6h12v13H6V6Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-bell" viewBox="0 0 24 24">
            <path d="M18 16H6l1.3-2.1V10a4.7 4.7 0 0 1 9.4 0v3.9L18 16ZM10 19h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-help" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M10 9a2.4 2.4 0 1 1 3.5 2.1c-.9.5-1.5 1-1.5 2.1M12 16.7h.01" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-log-out" viewBox="0 0 24 24">
            <path d="M10 17v2H5V5h5v2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 8l4 4-4 4M8 12h10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-chevron-down" viewBox="0 0 24 24">
            <path d="m7 10 5 5 5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-arrow-right" viewBox="0 0 24 24">
            <path d="M5 12h14M14 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-inbox" viewBox="0 0 24 24">
            <path d="M5 6h14v12H5V6Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m8 12 2 3h4l2-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-clock" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 8v5l3 2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-check-circle" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="m8.5 12 2.2 2.2 4.8-4.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-x-square" viewBox="0 0 24 24">
            <path d="M7 7h10v10H7V7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m9.5 9.5 5 5M14.5 9.5l-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-star" viewBox="0 0 24 24">
            <path d="m12 4 2.2 4.7 5.1.7-3.7 3.6.9 5.1L12 15.7 7.5 18l.9-5.1-3.7-3.6 5.1-.7L12 4Z" fill="currentColor"/>
        </symbol>
        <symbol id="icon-list" viewBox="0 0 24 24">
            <path d="M10 7h9M10 12h9M10 17h9M5 7h.01M5 12h.01M5 17h.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-eye" viewBox="0 0 24 24">
            <path d="M4 12s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.6" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="icon-pencil" viewBox="0 0 24 24">
            <path d="M5 19h4l10-10-4-4L5 15v4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m13.8 6.2 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-trash" viewBox="0 0 24 24">
            <path d="M5 7h14M10 11v6M14 11v6M8 7l1-3h6l1 3M7 7l1 13h8l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
    </svg>

    @php
        $currentUser = auth()->user();
        $initials = $currentUser
            ? strtoupper(mb_substr($currentUser->first_name, 0, 1).mb_substr($currentUser->last_name, 0, 1))
            : 'JD';
        $currentName = $currentUser?->full_name ?? 'Admin';
        $appVersion = \App\Support\SystemVersion::current();
        $navGroups = [
            [
                'label' => 'Main',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'icon-dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
                    ['label' => 'Create Ticket', 'icon' => 'icon-plus', 'href' => route('tickets.create'), 'active' => request()->routeIs('tickets.create')],
                    ['label' => 'My Ticket', 'icon' => 'icon-ticket', 'href' => route('tickets.index'), 'active' => request()->routeIs('tickets.index')],
                    ['label' => 'Knowledge Base', 'icon' => 'icon-book', 'href' => '#', 'active' => false],
                    ['label' => 'Announcement', 'icon' => 'icon-megaphone', 'href' => '#', 'active' => false],
                ],
            ],
            [
                'label' => 'Customer Management',
                'items' => [
                    ['label' => 'Clients', 'icon' => 'icon-client', 'href' => route('clients.index'), 'active' => request()->routeIs('clients.index')],
                    ['label' => 'Client Registration', 'icon' => 'icon-plus', 'href' => route('clients.registration'), 'active' => request()->routeIs('clients.registration')],
                    ['label' => 'Service Order', 'icon' => 'icon-list', 'href' => route('service-order-details.index'), 'active' => request()->routeIs('service-order-details.index')],
                    ['label' => 'Service Order List', 'icon' => 'icon-doc', 'href' => route('service-order-details.detail'), 'active' => request()->routeIs('service-order-details.detail')],
                    ['label' => 'Industry / Business Type', 'icon' => 'icon-list', 'href' => route('industry-business-types.index'), 'active' => request()->routeIs('industry-business-types.*')],
                    ['label' => 'Work Agreement', 'icon' => 'icon-doc', 'href' => route('work-agreements.index'), 'active' => request()->routeIs('work-agreements.*')],
                ],
            ],
            [
                'label' => 'Setup',
                'items' => [
                    ['label' => 'Account Manager', 'icon' => 'icon-client', 'href' => route('account-managers.index'), 'active' => request()->routeIs('account-managers.*')],
                    ['label' => 'Assign FC', 'icon' => 'icon-client', 'href' => route('assign-fcs.index'), 'active' => request()->routeIs('assign-fcs.*')],
                    ['label' => 'Package', 'icon' => 'icon-list', 'href' => route('packages.index'), 'active' => request()->routeIs('packages.*')],
                    ['label' => 'Product Details', 'icon' => 'icon-list', 'href' => route('product-details'), 'active' => request()->routeIs('product-details')],
                    ['label' => 'Product Used', 'icon' => 'icon-book', 'href' => route('sap-products.index'), 'active' => request()->routeIs('sap-products.*')],
                    ['label' => 'Security Level', 'icon' => 'icon-list', 'href' => route('security-levels.index'), 'active' => request()->routeIs('security-levels.*')],
                    ['label' => 'Ticket Status', 'icon' => 'icon-check-circle', 'href' => route('ticket-statuses.index'), 'active' => request()->routeIs('ticket-statuses.*')],
                ],
            ],
            [
                'label' => 'Analytics',
                'items' => [
                    ['label' => 'Analytics', 'icon' => 'icon-chart', 'href' => route('dashboard').'#analytics-overview', 'active' => false],
                    ['label' => 'Reports', 'icon' => 'icon-report', 'href' => '#', 'active' => false],
                    ['label' => 'SLA & Performance', 'icon' => 'icon-gauge', 'href' => '#', 'active' => false],
                ],
            ],
            [
                'label' => 'Admin',
                'items' => [
                    ['label' => 'About Us', 'icon' => 'icon-info', 'href' => route('about-us'), 'active' => request()->routeIs('about-us')],
                    ['label' => 'Role', 'icon' => 'icon-role', 'href' => route('roles.index'), 'active' => request()->routeIs('roles.*')],
                    ['label' => 'System Settings', 'icon' => 'icon-settings', 'href' => route('system-settings'), 'active' => request()->routeIs('system-settings')],
                    ['label' => 'User Registration', 'icon' => 'icon-users', 'href' => route('users.index'), 'active' => request()->routeIs('users.*')],
                ],
            ],
            [
                'label' => 'Support',
                'items' => [
                    ['label' => 'Contact Support', 'icon' => 'icon-headset', 'href' => route('contact-support'), 'active' => request()->routeIs('contact-support')],
                ],
            ],
        ];

        if ($currentUser?->roles->contains('slug', 'customer')) {
            $customerMenuItems = [
                'Dashboard',
                'Create Ticket',
                'My Ticket',
                'Knowledge Base',
                'Announcement',
                'Contact Support',
            ];

            $navGroups = collect($navGroups)
                ->map(function (array $group) use ($customerMenuItems): array {
                    $group['items'] = collect($group['items'])
                        ->filter(fn (array $item): bool => in_array($item['label'], $customerMenuItems, true))
                        ->values()
                        ->all();

                    return $group;
                })
                ->filter(fn (array $group): bool => count($group['items']) > 0)
                ->values()
                ->all();
        }
    @endphp

    <div class="app-frame">
        <aside class="side-pane" aria-label="Main menu">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="{{ $appearanceSystemName }} dashboard">
                @if ($appearanceLogoUrl)
                    <img class="brand-logo-image" src="{{ $appearanceLogoUrl }}" alt="">
                @else
                    <span class="brand-monogram" aria-hidden="true">X8</span>
                @endif
                <span class="brand-copy">
                    <strong>{{ $appearanceSystemName }}</strong>
                    <small>{{ $appearanceCompanyName }}</small>
                </span>
            </a>

            <nav class="nav-groups">
                @foreach ($navGroups as $group)
                    @php
                        $isGroupActive = collect($group['items'])->contains(fn ($item) => $item['active']);
                    @endphp
                    <details class="nav-group" @if ($isGroupActive) open @endif>
                        <summary class="nav-parent">
                            <span class="nav-heading">{{ $group['label'] }}</span>
                            <svg aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
                        </summary>
                        <div class="nav-group-items">
                            @foreach ($group['items'] as $item)
                                <a class="nav-item {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}" @if ($item['active']) aria-current="page" @endif>
                                    <svg><use href="#{{ $item['icon'] }}"></use></svg>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </nav>

            <section class="support-card" aria-labelledby="support-schedule-title">
                <h2 id="support-schedule-title"><svg><use href="#icon-calendar"></use></svg><span>Support Schedule</span></h2>
                <p>Monday - Friday</p>
                <p>8:30 AM - 6:00 PM</p>
                <small>Excluding Holidays</small>
            </section>
        </aside>

        <div class="main-pane">
            <header class="main-header">
                <div>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    <p class="page-subtitle">@yield('page-subtitle', 'Support operations at a glance.')</p>
                </div>

                <div class="header-actions">
                    <button class="header-icon" type="button" aria-label="Notifications">
                        <svg><use href="#icon-bell"></use></svg>
                        <span class="badge">3</span>
                    </button>
                    <button class="header-icon" type="button" aria-label="Help">
                        <svg><use href="#icon-help"></use></svg>
                    </button>
                    <details class="profile-menu">
                        <summary class="profile-toggle" aria-label="Open user menu">
                            <span class="profile-main">
                                <span class="avatar">{{ $initials }}</span>
                                <span class="profile-name">{{ $currentName }}</span>
                            </span>
                            <svg><use href="#icon-chevron-down"></use></svg>
                        </summary>
                        <div class="profile-dropdown">
                            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="profile-action" type="submit">
                                    <svg><use href="#icon-log-out"></use></svg>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>

            <main class="content-area">
                @yield('content')
            </main>

            <footer class="app-footer">
                <span>&copy; 2026 {{ $appearanceCompanyName }}</span>
                <span class="version-chip">Version {{ $appVersion }}</span>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>Xceler8 Support System</title>

    @fonts

    <style>
        :root {
            --ink: #071b4d;
            --muted: #61708f;
            --line: #d8e2f2;
            --panel: #ffffff;
            --canvas: #f5f8fc;
            --blue: #1766ff;
            --blue-dark: #061b49;
            --blue-darker: #031234;
            --green: #35c46f;
            --red: #ff6b64;
            --amber: #ffbc45;
            --cyan: #18aac4;
            --violet: #8468ff;
            --shadow: 0 18px 55px rgba(8, 34, 77, .08);
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
            font-size: 14px;
            letter-spacing: 0;
        }

        button,
        select {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 248px minmax(0, 1fr);
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            padding: 26px 20px 18px;
            background: linear-gradient(180deg, #0b4198 0%, var(--blue-dark) 28%, var(--blue-darker) 100%);
            color: #ffffff;
        }

        .brand {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 4px 0 24px;
            padding: 7px 10px 8px;
            border: 1px solid rgba(255, 255, 255, .44);
            background: rgba(20, 107, 255, .22);
            line-height: 1;
        }

        .brand strong {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: .6px;
        }

        .brand span {
            margin-top: 4px;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .6px;
        }

        .nav-section {
            margin-top: 24px;
        }

        .nav-heading {
            margin: 0 0 8px 6px;
            color: rgba(255, 255, 255, .72);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .nav-list {
            display: grid;
            gap: 6px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 42px;
            padding: 0 13px;
            border-radius: 8px;
            color: #ffffff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            stroke-width: 2;
        }

        .nav-link:hover,
        .nav-link:focus-visible {
            background: rgba(255, 255, 255, .12);
            outline: none;
        }

        .nav-link.active {
            background: var(--blue);
            box-shadow: 0 10px 24px rgba(23, 102, 255, .35);
        }

        .schedule-card {
            margin-top: 30px;
            padding: 16px;
            border: 1px solid rgba(129, 174, 255, .7);
            border-radius: 8px;
            background: rgba(13, 60, 137, .58);
        }

        .schedule-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 14px;
            font-weight: 800;
        }

        .schedule-title svg {
            width: 17px;
            height: 17px;
        }

        .schedule-card p {
            margin: 0;
            color: rgba(255, 255, 255, .94);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.8;
        }

        .dashboard {
            min-width: 0;
        }

        .topbar {
            min-height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 0 32px;
            border-bottom: 1px solid var(--line);
            background: #ffffff;
        }

        .page-title h1 {
            margin: 0;
            font-size: clamp(22px, 2vw, 32px);
            line-height: 1.05;
            font-weight: 900;
            color: #071747;
        }

        .page-title p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .icon-button {
            position: relative;
            width: 36px;
            height: 36px;
            display: inline-grid;
            place-items: center;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: var(--ink);
        }

        .icon-button svg {
            width: 19px;
            height: 19px;
        }

        .icon-button:hover,
        .icon-button:focus-visible {
            background: #eef4ff;
            outline: none;
        }

        .badge {
            position: absolute;
            top: 3px;
            right: 4px;
            min-width: 17px;
            height: 17px;
            display: grid;
            place-items: center;
            border: 2px solid #ffffff;
            border-radius: 999px;
            background: #ff3b30;
            color: #ffffff;
            font-size: 9px;
            font-weight: 900;
        }

        .profile-button {
            min-width: 218px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f8fbff;
            color: var(--ink);
        }

        .avatar {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 999px;
            background: var(--blue);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            box-shadow: inset 0 0 0 4px rgba(255, 255, 255, .36);
        }

        .profile-name {
            flex: 1;
            min-width: 0;
            text-align: left;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .content {
            padding: 26px 32px 34px;
        }

        .section-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 26px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 900;
        }

        .section-title svg {
            width: 22px;
            height: 22px;
            color: var(--blue);
        }

        .date-button,
        .select-button,
        .view-button {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 13px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            color: #10275f;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .date-button svg,
        .select-button svg,
        .view-button svg {
            width: 16px;
            height: 16px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(5, minmax(150px, 1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .metric-card,
        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .metric-card {
            min-height: 112px;
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 22px 20px;
        }

        .metric-card.blue {
            border-color: #a9c5ff;
        }

        .metric-card.amber {
            border-color: #ffc86f;
        }

        .metric-card.green {
            border-color: #9ddeba;
        }

        .metric-card.red {
            border-color: #ffb5ae;
        }

        .metric-card.violet {
            border-color: #c8bcff;
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 999px;
        }

        .metric-icon svg {
            width: 20px;
            height: 20px;
        }

        .blue .metric-icon {
            background: #e4edff;
            color: var(--blue);
        }

        .amber .metric-icon {
            background: #fff1d3;
            color: #f5a100;
        }

        .green .metric-icon {
            background: #dff8ea;
            color: var(--green);
        }

        .red .metric-icon {
            background: #ffe4e0;
            color: var(--red);
        }

        .violet .metric-icon {
            background: #ece7ff;
            color: var(--violet);
        }

        .metric-label {
            margin: 0 0 5px;
            font-size: 12px;
            font-weight: 900;
            color: #11265f;
        }

        .metric-value {
            margin: 0;
            font-size: 28px;
            line-height: 1;
            font-weight: 900;
            color: #061a4f;
        }

        .metric-delta {
            margin: 7px 0 0;
            color: #008a4c;
            font-size: 11px;
            font-weight: 800;
        }

        .metric-delta.down {
            color: #e34f49;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: minmax(340px, 1.35fr) minmax(260px, 1fr) minmax(320px, 1.12fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .panel {
            min-width: 0;
            padding: 20px;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }

        .panel-title {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
            color: #0a2055;
        }

        .trend-chart {
            width: 100%;
            min-height: 218px;
        }

        .axis-label {
            fill: #607196;
            font-size: 11px;
            font-weight: 700;
        }

        .chart-line {
            fill: none;
            stroke: var(--blue);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .chart-dot {
            fill: var(--blue);
            stroke: #ffffff;
            stroke-width: 3;
        }

        .grid-line {
            stroke: #dde6f3;
            stroke-width: 1;
        }

        .donut-wrap {
            display: grid;
            grid-template-columns: 170px minmax(0, 1fr);
            align-items: center;
            gap: 22px;
            min-height: 200px;
        }

        .donut {
            width: 148px;
            aspect-ratio: 1;
            border-radius: 999px;
            position: relative;
            justify-self: center;
        }

        .donut::after {
            content: "";
            position: absolute;
            inset: 42px;
            border-radius: inherit;
            background: #ffffff;
            box-shadow: inset 0 0 0 1px #edf2fa;
        }

        .donut.status {
            background: conic-gradient(var(--blue) 0 26.6%, var(--green) 26.6% 90.7%, var(--red) 90.7% 100%);
        }

        .donut.product {
            background: conic-gradient(var(--blue) 0 45.3%, var(--green) 45.3% 66.4%, var(--amber) 66.4% 78.9%, var(--violet) 78.9% 88.3%, var(--cyan) 88.3% 100%);
        }

        .legend {
            display: grid;
            gap: 13px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .legend li {
            display: grid;
            grid-template-columns: 11px minmax(0, 1fr);
            gap: 11px;
            color: #28406e;
            font-size: 12px;
            font-weight: 700;
        }

        .legend strong {
            display: block;
            margin-top: 4px;
            color: #061b4d;
            font-weight: 900;
        }

        .dot {
            width: 9px;
            height: 9px;
            margin-top: 3px;
            border-radius: 999px;
            background: var(--dot);
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: minmax(520px, 1fr) 360px;
            gap: 18px;
        }

        .table-panel {
            padding: 0;
            overflow: hidden;
        }

        .table-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tickets-table th,
        .tickets-table td {
            border-top: 1px solid var(--line);
            padding: 16px 20px;
            text-align: left;
            vertical-align: middle;
        }

        .tickets-table th {
            color: #52658c;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .tickets-table td {
            color: #17315f;
            font-size: 12px;
            font-weight: 700;
        }

        .ticket-id {
            display: block;
            margin-bottom: 5px;
            color: #09245b;
            font-size: 13px;
            font-weight: 900;
        }

        .ticket-note {
            display: block;
            color: #587095;
            font-weight: 600;
        }

        .status-pill {
            position: relative;
            display: block;
            width: min(250px, 100%);
            height: 24px;
            overflow: hidden;
            border-radius: 999px;
            color: #143064;
            font-size: 10px;
            font-weight: 900;
            line-height: 24px;
        }

        .status-pill span {
            position: relative;
            z-index: 1;
            padding-left: 0;
        }

        .status-pill::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: var(--pill-bg);
        }

        .status-pill.open {
            --pill-bg: #e1ebff;
        }

        .status-pill.resolved {
            --pill-bg: #dff7ea;
        }

        .status-pill.closed {
            --pill-bg: #ffe1de;
        }

        .summary-panel {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .summary-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .summary-title svg {
            width: 22px;
            height: 22px;
            color: var(--blue);
        }

        .summary-list {
            display: grid;
            margin: 0;
            padding: 0;
            border: 1px solid var(--line);
            border-radius: 8px;
            list-style: none;
            overflow: hidden;
        }

        .summary-list li {
            min-height: 48px;
            display: grid;
            grid-template-columns: 32px minmax(0, 1fr) auto 18px;
            align-items: center;
            gap: 10px;
            padding: 0 12px;
            border-top: 1px solid var(--line);
        }

        .summary-list li:first-child {
            border-top: 0;
        }

        .summary-icon {
            width: 27px;
            height: 27px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            color: var(--summary-color);
            background: color-mix(in srgb, var(--summary-color) 15%, white);
        }

        .summary-icon svg {
            width: 15px;
            height: 15px;
        }

        .summary-label {
            color: #334a74;
            font-size: 12px;
            font-weight: 700;
        }

        .summary-value {
            color: #051947;
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .summary-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: auto;
            padding-top: 22px;
            color: var(--blue);
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .summary-link svg {
            width: 20px;
            height: 20px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @supports not (background: color-mix(in srgb, #000 10%, white)) {
            .summary-icon {
                background: #eef4ff;
            }
        }

        @media (max-width: 1500px) {
            .metrics {
                grid-template-columns: repeat(3, minmax(180px, 1fr));
            }

            .chart-grid {
                grid-template-columns: repeat(2, minmax(300px, 1fr));
            }

            .chart-grid .panel:first-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 1180px) {
            .app-shell {
                grid-template-columns: 220px minmax(0, 1fr);
            }

            .sidebar {
                padding-inline: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
                padding: 22px 24px;
            }

            .top-actions {
                width: 100%;
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .content {
                padding: 24px;
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 920px) {
            .app-shell {
                display: block;
            }

            .sidebar {
                position: relative;
                height: auto;
                max-height: none;
            }

            .nav-section {
                margin-top: 18px;
            }

            .nav-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .metrics,
            .chart-grid {
                grid-template-columns: 1fr;
            }

            .donut-wrap {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .topbar,
            .content {
                padding-inline: 16px;
            }

            .section-bar,
            .table-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .profile-button {
                min-width: 0;
                width: 100%;
            }

            .nav-list {
                grid-template-columns: 1fr;
            }

            .metric-card {
                min-height: 96px;
            }

            .trend-chart {
                min-height: 190px;
            }

            .table-panel {
                overflow-x: auto;
            }

            .tickets-table {
                min-width: 720px;
            }

            .summary-list li {
                grid-template-columns: 32px minmax(0, 1fr) auto;
            }

            .summary-list li > svg {
                display: none;
            }
        }
    </style>
</head>
<body>
    <svg aria-hidden="true" width="0" height="0" style="position:absolute">
        <symbol id="icon-dashboard" viewBox="0 0 24 24">
            <path d="M4 13h6V4H4v9Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M14 20h6V4h-6v16Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M4 20h6v-3H4v3Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-plus" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-ticket" viewBox="0 0 24 24">
            <path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V8Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M9 9h6M9 15h6" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-book" viewBox="0 0 24 24">
            <path d="M5 5.5A2.5 2.5 0 0 1 7.5 3H20v16H7.5A2.5 2.5 0 0 0 5 21.5v-16Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M5 5.5A2.5 2.5 0 0 0 2.5 3H2v16h.5A2.5 2.5 0 0 1 5 21.5" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-megaphone" viewBox="0 0 24 24">
            <path d="M4 13h4l9 5V6l-9 5H4v2Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M8 13l2 6" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-user" viewBox="0 0 24 24">
            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="none" stroke="currentColor"/>
            <path d="M4 21a8 8 0 0 1 16 0" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-user-plus" viewBox="0 0 24 24">
            <path d="M10 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM3 21a7 7 0 0 1 12.8-3.9" fill="none" stroke="currentColor" stroke-linecap="round"/>
            <path d="M19 14v6M16 17h6" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-chart" viewBox="0 0 24 24">
            <path d="M4 20V4M4 20h16" fill="none" stroke="currentColor" stroke-linecap="round"/>
            <path d="M8 17V9M12 17V6M16 17v-4M20 17V8" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-file" viewBox="0 0 24 24">
            <path d="M7 3h7l4 4v14H7V3Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M14 3v5h4M9.5 13h5M9.5 17h5" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-speed" viewBox="0 0 24 24">
            <path d="M5 19a8 8 0 1 1 14 0" fill="none" stroke="currentColor" stroke-linecap="round"/>
            <path d="M12 14l4-4" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-info" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor"/>
            <path d="M12 10v6M12 7h.01" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-lock" viewBox="0 0 24 24">
            <path d="M7 10V8a5 5 0 0 1 10 0v2" fill="none" stroke="currentColor" stroke-linecap="round"/>
            <path d="M6 10h12v10H6V10Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-settings" viewBox="0 0 24 24">
            <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" fill="none" stroke="currentColor"/>
            <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.5-2.4 1a8 8 0 0 0-1.7-1L14.5 3h-5l-.4 3a8 8 0 0 0-1.7 1L5 6 3 9.5 5 11a7 7 0 0 0 0 2l-2 1.5L5 18l2.4-1a8 8 0 0 0 1.7 1l.4 3h5l.4-3a8 8 0 0 0 1.7-1L19 18l2-3.5-2-1.5c.1-.3.1-.7.1-1Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-headset" viewBox="0 0 24 24">
            <path d="M4 13v-1a8 8 0 0 1 16 0v1" fill="none" stroke="currentColor" stroke-linecap="round"/>
            <path d="M4 13h3v5H4v-5ZM17 13h3v5h-3v-5ZM17 18a5 5 0 0 1-5 3h-1" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-calendar" viewBox="0 0 24 24">
            <path d="M7 3v4M17 3v4M4 9h16" fill="none" stroke="currentColor" stroke-linecap="round"/>
            <path d="M5 5h14v15H5V5Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-bell" viewBox="0 0 24 24">
            <path d="M18 9a6 6 0 0 0-12 0c0 7-2 7-2 9h16c0-2-2-2-2-9Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M10 21h4" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-help" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor"/>
            <path d="M9.8 9a2.3 2.3 0 0 1 4.3 1.1c0 1.7-2.1 2-2.1 3.4M12 17h.01" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-chevron" viewBox="0 0 24 24">
            <path d="m7 10 5 5 5-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-arrow" viewBox="0 0 24 24">
            <path d="M5 12h14M14 7l5 5-5 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-bars" viewBox="0 0 24 24">
            <path d="M5 7h3M5 12h3M5 17h3M12 7h7M12 12h7M12 17h7" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-circle-check" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor"/>
            <path d="m8.5 12 2.2 2.2 4.8-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-circle-clock" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor"/>
            <path d="M12 7.5V12l3 2" fill="none" stroke="currentColor" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-star" viewBox="0 0 24 24">
            <path d="m12 4 2.2 4.7 5.1.7-3.7 3.6.9 5.1L12 15.7 7.5 18l.9-5.1-3.7-3.6 5.1-.7L12 4Z" fill="currentColor"/>
        </symbol>
        <symbol id="icon-box" viewBox="0 0 24 24">
            <path d="M7 7h10v10H7V7Z" fill="none" stroke="currentColor" stroke-linejoin="round"/>
        </symbol>
    </svg>

    <div class="app-shell">
        <aside class="sidebar" aria-label="Primary navigation">
            <div class="brand" aria-label="Xceler8 Technologies Inc.">
                <strong>XCELER8</strong>
                <span>TECHNOLOGIES INC.</span>
            </div>

            <nav>
                <section class="nav-section" aria-labelledby="main-nav">
                    <h2 class="nav-heading" id="main-nav">Main</h2>
                    <ul class="nav-list">
                        <li><a class="nav-link active" href="#"><svg><use href="#icon-dashboard"></use></svg><span>Dashboard</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-plus"></use></svg><span>Create Ticket</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-ticket"></use></svg><span>My Tickets</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-book"></use></svg><span>Knowledge Base</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-megaphone"></use></svg><span>Announcements</span></a></li>
                    </ul>
                </section>

                <section class="nav-section" aria-labelledby="client-nav">
                    <h2 class="nav-heading" id="client-nav">Client Management</h2>
                    <ul class="nav-list">
                        <li><a class="nav-link" href="#"><svg><use href="#icon-user"></use></svg><span>Clients</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-user-plus"></use></svg><span>Client Registration</span></a></li>
                    </ul>
                </section>

                <section class="nav-section" aria-labelledby="analytics-nav">
                    <h2 class="nav-heading" id="analytics-nav">Analytics</h2>
                    <ul class="nav-list">
                        <li><a class="nav-link" href="#"><svg><use href="#icon-chart"></use></svg><span>Analytics</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-file"></use></svg><span>Reports</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-speed"></use></svg><span>SLA &amp; Performance</span></a></li>
                    </ul>
                </section>

                <section class="nav-section" aria-labelledby="admin-nav">
                    <h2 class="nav-heading" id="admin-nav">Admin</h2>
                    <ul class="nav-list">
                        <li><a class="nav-link" href="#"><svg><use href="#icon-info"></use></svg><span>About Us</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-lock"></use></svg><span>Role</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-settings"></use></svg><span>System Settings</span></a></li>
                        <li><a class="nav-link" href="#"><svg><use href="#icon-user-plus"></use></svg><span>User Registration</span></a></li>
                    </ul>
                </section>

                <section class="nav-section" aria-labelledby="support-nav">
                    <h2 class="nav-heading" id="support-nav">Support</h2>
                    <ul class="nav-list">
                        <li><a class="nav-link" href="#"><svg><use href="#icon-headset"></use></svg><span>Contact Support</span></a></li>
                    </ul>
                </section>
            </nav>

            <div class="schedule-card">
                <p class="schedule-title"><svg><use href="#icon-calendar"></use></svg><span>Support Schedule</span></p>
                <p>Monday - Friday</p>
                <p>8:30 AM - 6:00 PM</p>
                <p>Excluding Holidays</p>
            </div>
        </aside>

        <main class="dashboard">
            <header class="topbar">
                <div class="page-title">
                    <h1>Welcome to Xceler8 Support System</h1>
                    <p>We're here to help you with your SAP Business One needs.</p>
                </div>

                <div class="top-actions" aria-label="Account actions">
                    <button class="icon-button" type="button" aria-label="Notifications">
                        <svg><use href="#icon-bell"></use></svg>
                        <span class="badge">3</span>
                    </button>
                    <button class="icon-button" type="button" aria-label="Help">
                        <svg><use href="#icon-help"></use></svg>
                    </button>
                    <button class="profile-button" type="button" aria-label="Open user menu">
                        <span class="avatar">JD</span>
                        <span class="profile-name">Admin</span>
                        <svg width="18" height="18" aria-hidden="true"><use href="#icon-chevron"></use></svg>
                    </button>
                </div>
            </header>

            <div class="content">
                <div class="section-bar">
                    <h2 class="section-title"><svg><use href="#icon-chart"></use></svg><span>Analytics Overview</span></h2>
                    <button class="date-button" type="button">
                        <svg><use href="#icon-calendar"></use></svg>
                        <span>May 19 - May 25, 2024</span>
                        <svg><use href="#icon-chevron"></use></svg>
                    </button>
                </div>

                <section class="metrics" aria-label="Ticket metrics">
                    <article class="metric-card blue">
                        <div class="metric-icon"><svg><use href="#icon-ticket"></use></svg></div>
                        <div>
                            <p class="metric-label">Total Tickets</p>
                            <p class="metric-value">128</p>
                            <p class="metric-delta">+ 12% vs last 30 days</p>
                        </div>
                    </article>
                    <article class="metric-card amber">
                        <div class="metric-icon"><svg><use href="#icon-circle-clock"></use></svg></div>
                        <div>
                            <p class="metric-label">Open Tickets</p>
                            <p class="metric-value">34</p>
                            <p class="metric-delta">+ 8% vs last 30 days</p>
                        </div>
                    </article>
                    <article class="metric-card green">
                        <div class="metric-icon"><svg><use href="#icon-circle-check"></use></svg></div>
                        <div>
                            <p class="metric-label">Resolved Tickets</p>
                            <p class="metric-value">82</p>
                            <p class="metric-delta">+ 15% vs last 30 days</p>
                        </div>
                    </article>
                    <article class="metric-card red">
                        <div class="metric-icon"><svg><use href="#icon-box"></use></svg></div>
                        <div>
                            <p class="metric-label">Closed Tickets</p>
                            <p class="metric-value">12</p>
                            <p class="metric-delta down">- 5% vs last 30 days</p>
                        </div>
                    </article>
                    <article class="metric-card violet">
                        <div class="metric-icon"><svg><use href="#icon-star"></use></svg></div>
                        <div>
                            <p class="metric-label">Customer Satisfaction</p>
                            <p class="metric-value">4.6 / 5</p>
                            <p class="metric-delta">+ 6% vs last 30 days</p>
                        </div>
                    </article>
                </section>

                <section class="chart-grid" aria-label="Ticket analytics charts">
                    <article class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">Tickets Trend</h3>
                            <button class="select-button" type="button">Last 7 Days <svg><use href="#icon-chevron"></use></svg></button>
                        </div>
                        <svg class="trend-chart" viewBox="0 0 720 260" role="img" aria-labelledby="trend-title">
                            <title id="trend-title">Ticket trend from May 19 to May 25</title>
                            <line class="grid-line" x1="76" y1="38" x2="680" y2="38"/>
                            <line class="grid-line" x1="76" y1="88" x2="680" y2="88"/>
                            <line class="grid-line" x1="76" y1="138" x2="680" y2="138"/>
                            <line class="grid-line" x1="76" y1="188" x2="680" y2="188"/>
                            <text class="axis-label" x="46" y="42">60</text>
                            <text class="axis-label" x="46" y="92">40</text>
                            <text class="axis-label" x="46" y="142">20</text>
                            <text class="axis-label" x="52" y="192">0</text>
                            <polyline class="chart-line" points="78,138 178,124 278,86 378,108 478,56 578,104 678,80"/>
                            <circle class="chart-dot" cx="78" cy="138" r="6"/>
                            <circle class="chart-dot" cx="178" cy="124" r="6"/>
                            <circle class="chart-dot" cx="278" cy="86" r="6"/>
                            <circle class="chart-dot" cx="378" cy="108" r="6"/>
                            <circle class="chart-dot" cx="478" cy="56" r="6"/>
                            <circle class="chart-dot" cx="578" cy="104" r="6"/>
                            <circle class="chart-dot" cx="678" cy="80" r="6"/>
                            <text class="axis-label" x="52" y="228">May 19</text>
                            <text class="axis-label" x="152" y="228">May 20</text>
                            <text class="axis-label" x="252" y="228">May 21</text>
                            <text class="axis-label" x="352" y="228">May 22</text>
                            <text class="axis-label" x="452" y="228">May 23</text>
                            <text class="axis-label" x="552" y="228">May 24</text>
                            <text class="axis-label" x="652" y="228">May 25</text>
                        </svg>
                    </article>

                    <article class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">Tickets by Status</h3>
                            <button class="select-button" type="button">Last 7 Days <svg><use href="#icon-chevron"></use></svg></button>
                        </div>
                        <div class="donut-wrap">
                            <div class="donut status" role="img" aria-label="Open 34, Resolved 82, Closed 12"></div>
                            <ul class="legend">
                                <li><span class="dot" style="--dot: var(--blue)"></span><span>Open<strong>34 (26.6%)</strong></span></li>
                                <li><span class="dot" style="--dot: var(--green)"></span><span>Resolved<strong>82 (64.1%)</strong></span></li>
                                <li><span class="dot" style="--dot: var(--red)"></span><span>Closed<strong>12 (9.3%)</strong></span></li>
                            </ul>
                        </div>
                    </article>

                    <article class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">Tickets by Product</h3>
                            <button class="select-button" type="button">Last 30 Days <svg><use href="#icon-chevron"></use></svg></button>
                        </div>
                        <div class="donut-wrap">
                            <div class="donut product" role="img" aria-label="Tickets by product"></div>
                            <ul class="legend">
                                <li><span class="dot" style="--dot: var(--blue)"></span><span>SAP B1<strong>58 (45.3%)</strong></span></li>
                                <li><span class="dot" style="--dot: var(--green)"></span><span>E-Sweldo Payroll<strong>28 (21.9%)</strong></span></li>
                                <li><span class="dot" style="--dot: var(--amber)"></span><span>HIS &amp; EMR<strong>16 (12.5%)</strong></span></li>
                                <li><span class="dot" style="--dot: var(--violet)"></span><span>Xceler8 Addon<strong>12 (9.4%)</strong></span></li>
                                <li><span class="dot" style="--dot: var(--cyan)"></span><span>SAP Cloud ERP<strong>14 (10.9%)</strong></span></li>
                            </ul>
                        </div>
                    </article>
                </section>

                <section class="bottom-grid" aria-label="Recent tickets and quick summary">
                    <article class="panel table-panel">
                        <div class="table-head">
                            <h3 class="panel-title">Recent Tickets</h3>
                            <button class="view-button" type="button">View All <svg><use href="#icon-arrow"></use></svg></button>
                        </div>
                        <table class="tickets-table">
                            <thead>
                                <tr>
                                    <th style="width: 32%">Ticket</th>
                                    <th style="width: 19%">Product</th>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 12%">Priority</th>
                                    <th style="width: 22%">Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="ticket-id">XB1-1048</span><span class="ticket-note">Sales order approval workflow</span></td>
                                    <td>SAP B1</td>
                                    <td><span class="status-pill open"><span>Open</span></span></td>
                                    <td>High</td>
                                    <td>Today, 10:24 AM</td>
                                </tr>
                                <tr>
                                    <td><span class="ticket-id">PAY-884</span><span class="ticket-note">Payroll report variance check</span></td>
                                    <td>E-Sweldo Payroll</td>
                                    <td><span class="status-pill resolved"><span>Resolved</span></span></td>
                                    <td>Medium</td>
                                    <td>Yesterday, 4:15 PM</td>
                                </tr>
                                <tr>
                                    <td><span class="ticket-id">EMR-277</span><span class="ticket-note">Patient billing integration queue</span></td>
                                    <td>HIS &amp; EMR</td>
                                    <td><span class="status-pill open"><span>Open</span></span></td>
                                    <td>High</td>
                                    <td>May 24, 2024</td>
                                </tr>
                                <tr>
                                    <td><span class="ticket-id">ADD-531</span><span class="ticket-note">Addon license renewal request</span></td>
                                    <td>Xceler8 Addon</td>
                                    <td><span class="status-pill closed"><span>Closed</span></span></td>
                                    <td>Low</td>
                                    <td>May 23, 2024</td>
                                </tr>
                            </tbody>
                        </table>
                    </article>

                    <aside class="panel summary-panel">
                        <div class="summary-title">
                            <svg><use href="#icon-bars"></use></svg>
                            <h3 class="panel-title">Quick Summary</h3>
                        </div>
                        <ul class="summary-list">
                            <li style="--summary-color: var(--blue)">
                                <span class="summary-icon"><svg><use href="#icon-ticket"></use></svg></span>
                                <span class="summary-label">Total Tickets (All Time)</span>
                                <span class="summary-value">1,245</span>
                                <svg><use href="#icon-chevron"></use></svg>
                            </li>
                            <li style="--summary-color: var(--green)">
                                <span class="summary-icon"><svg><use href="#icon-circle-check"></use></svg></span>
                                <span class="summary-label">Resolved (All Time)</span>
                                <span class="summary-value">1,056</span>
                                <svg><use href="#icon-chevron"></use></svg>
                            </li>
                            <li style="--summary-color: var(--red)">
                                <span class="summary-icon"><svg><use href="#icon-box"></use></svg></span>
                                <span class="summary-label">Closed (All Time)</span>
                                <span class="summary-value">189</span>
                                <svg><use href="#icon-chevron"></use></svg>
                            </li>
                            <li style="--summary-color: var(--violet)">
                                <span class="summary-icon"><svg><use href="#icon-circle-clock"></use></svg></span>
                                <span class="summary-label">Average Response Time</span>
                                <span class="summary-value">2.6 hrs</span>
                                <svg><use href="#icon-chevron"></use></svg>
                            </li>
                            <li style="--summary-color: var(--amber)">
                                <span class="summary-icon"><svg><use href="#icon-star"></use></svg></span>
                                <span class="summary-label">Customer Satisfaction</span>
                                <span class="summary-value">4.6 / 5</span>
                                <svg><use href="#icon-chevron"></use></svg>
                            </li>
                        </ul>
                        <a class="summary-link" href="#">
                            <span><svg style="display:inline-block;vertical-align:-5px;margin-right:8px"><use href="#icon-chart"></use></svg>View full analytics</span>
                            <svg><use href="#icon-arrow"></use></svg>
                        </a>
                    </aside>
                </section>
            </div>
        </main>
    </div>
</body>
</html>

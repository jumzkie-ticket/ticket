@extends('layouts.app-shell')

@section('title', 'Dashboard')
@section('page-title', 'Welcome to Xceler8 Support System')
@section('page-subtitle', "We're here to help you with your SAP Business One needs.")

@push('styles')
    <style>
        .dashboard {
            display: grid;
            gap: 18px;
        }

        .section-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .section-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            color: #061845;
            font-size: 16px;
            font-weight: 900;
        }

        .section-title svg {
            width: 18px;
            height: 18px;
            color: var(--blue);
        }

        .date-filter,
        .panel-filter,
        .button-link {
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #cbd9ee;
            border-radius: 7px;
            background: #ffffff;
            color: #17315f;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .date-filter {
            padding: 0 13px;
        }

        .panel-filter,
        .button-link {
            padding: 0 12px;
        }

        .date-filter svg,
        .panel-filter svg,
        .button-link svg {
            width: 15px;
            height: 15px;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(170px, 1fr));
            gap: 16px;
        }

        .metric-card,
        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: var(--shadow);
        }

        .metric-card {
            min-height: 106px;
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr);
            align-items: center;
            gap: 16px;
            padding: 20px;
            border-color: var(--tone-border);
        }

        .tone-blue {
            --tone-color: var(--blue);
            --tone-bg: var(--blue-soft);
            --tone-border: #a9c6ff;
        }

        .tone-amber {
            --tone-color: var(--amber);
            --tone-bg: var(--amber-soft);
            --tone-border: #ffd58a;
        }

        .tone-green {
            --tone-color: var(--green);
            --tone-bg: var(--green-soft);
            --tone-border: #aee9c9;
        }

        .tone-red {
            --tone-color: var(--red);
            --tone-bg: var(--red-soft);
            --tone-border: #ffb9b2;
        }

        .tone-violet {
            --tone-color: var(--violet);
            --tone-bg: var(--violet-soft);
            --tone-border: #c9bcff;
        }

        .metric-icon,
        .summary-icon {
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--tone-bg);
            color: var(--tone-color);
        }

        .metric-icon {
            width: 44px;
            height: 44px;
        }

        .metric-icon svg {
            width: 18px;
            height: 18px;
        }

        .metric-label {
            margin: 0 0 7px;
            color: #071b4d;
            font-size: 12px;
            font-weight: 900;
        }

        .metric-value {
            margin: 0;
            color: #061845;
            font-size: 28px;
            line-height: 1;
            font-weight: 900;
        }

        .metric-change {
            margin: 7px 0 0;
            color: var(--tone-color);
            font-size: 10px;
            font-weight: 850;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(340px, .9fr) minmax(360px, 1fr);
            gap: 18px;
        }

        .panel-head {
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px 0;
        }

        .panel-title {
            margin: 0;
            color: #071b4d;
            font-size: 13px;
            font-weight: 900;
        }

        .panel-body {
            padding: 14px 18px 18px;
        }

        .trend-chart {
            width: 100%;
            height: 230px;
        }

        .trend-chart text {
            fill: #52698f;
            font-size: 10px;
            font-weight: 750;
        }

        .chart-split {
            display: grid;
            grid-template-columns: 158px minmax(0, 1fr);
            align-items: center;
            gap: 22px;
            min-height: 210px;
        }

        .donut {
            position: relative;
            width: 142px;
            aspect-ratio: 1;
            border-radius: 999px;
            background: var(--donut);
        }

        .donut::after {
            content: "";
            position: absolute;
            inset: 40px;
            border-radius: 999px;
            background: #ffffff;
        }

        .legend {
            display: grid;
            gap: 16px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .legend-item {
            display: grid;
            grid-template-columns: 10px minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            color: #17315f;
            font-size: 11px;
            font-weight: 850;
        }

        .legend-dot {
            width: 9px;
            height: 9px;
            margin-top: 4px;
            border-radius: 999px;
            background: var(--dot);
        }

        .legend-item strong {
            display: block;
            margin-top: 5px;
            color: #071b4d;
            font-size: 10px;
            font-weight: 900;
        }

        .lower-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 350px;
            gap: 18px;
            align-items: start;
        }

        .table-panel {
            overflow: hidden;
        }

        .recent-table-wrap {
            overflow-x: auto;
            padding: 0 18px 18px;
        }

        .recent-table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .recent-table th,
        .recent-table td {
            border-top: 1px solid #e4ebf6;
            padding: 14px 10px;
            text-align: left;
            vertical-align: middle;
        }

        .recent-table thead th {
            color: #53698f;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .recent-table tbody td {
            color: #17315f;
            font-size: 12px;
            font-weight: 750;
        }

        .ticket-code {
            display: block;
            color: #061845;
            font-weight: 900;
        }

        .ticket-summary {
            display: block;
            margin-top: 4px;
            color: #61708f;
            font-size: 11px;
            font-weight: 650;
        }

        .status-track {
            width: min(220px, 100%);
            min-height: 22px;
            display: flex;
            align-items: center;
            padding: 0 8px;
            border-radius: 999px;
            background: var(--status-bg);
            color: var(--status-color);
            font-size: 10px;
            font-weight: 900;
        }

        .status-open {
            --status-bg: #e4edff;
            --status-color: #0f55dc;
        }

        .status-resolved {
            --status-bg: #ddf8e8;
            --status-color: #087344;
        }

        .status-closed {
            --status-bg: #ffe1de;
            --status-color: #b73832;
        }

        .summary-card {
            overflow: hidden;
        }

        .summary-list {
            display: grid;
            margin: 0;
            padding: 0 16px 12px;
            list-style: none;
        }

        .summary-row {
            min-height: 48px;
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr) auto 16px;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #e4ebf6;
        }

        .summary-icon {
            width: 25px;
            height: 25px;
        }

        .summary-icon svg {
            width: 14px;
            height: 14px;
        }

        .summary-label {
            color: #17315f;
            font-size: 11px;
            font-weight: 750;
            overflow-wrap: anywhere;
        }

        .summary-value {
            color: #061845;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .summary-row > svg {
            width: 14px;
            height: 14px;
            color: #17315f;
        }

        .summary-link {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 16px 16px;
            color: var(--blue);
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .summary-link svg {
            width: 16px;
            height: 16px;
        }

        @media (max-width: 1480px) {
            .metric-grid {
                grid-template-columns: repeat(3, minmax(190px, 1fr));
            }

            .analytics-grid,
            .lower-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 780px) {
            .section-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .metric-grid {
                grid-template-columns: 1fr;
            }

            .chart-split {
                grid-template-columns: 1fr;
                justify-items: start;
            }

            .date-filter {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="dashboard" id="analytics-overview">
        <div class="section-toolbar">
            <h2 class="section-title">
                <svg><use href="#icon-chart"></use></svg>
                <span>Analytics Overview</span>
            </h2>
            <button class="date-filter" type="button">
                <svg><use href="#icon-calendar"></use></svg>
                <span>May 19 - May 25, 2024</span>
                <svg><use href="#icon-chevron-down"></use></svg>
            </button>
        </div>

        <section class="metric-grid" aria-label="Ticket analytics">
            @foreach ($metricCards as $metric)
                <article class="metric-card tone-{{ $metric['tone'] }}">
                    <span class="metric-icon"><svg><use href="#{{ $metric['icon'] }}"></use></svg></span>
                    <div>
                        <p class="metric-label">{{ $metric['label'] }}</p>
                        <p class="metric-value">{{ $metric['value'] }}</p>
                        <p class="metric-change">{{ $metric['change'] }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="analytics-grid">
            <article class="panel">
                <div class="panel-head">
                    <h3 class="panel-title">Tickets Trend</h3>
                    <button class="panel-filter" type="button">Last 7 Days <svg><use href="#icon-chevron-down"></use></svg></button>
                </div>
                <div class="panel-body">
                    <svg class="trend-chart" viewBox="0 0 620 230" role="img" aria-label="Ticket trend from May 19 to May 25">
                        <g stroke="#e4ebf6" stroke-width="1">
                            <path d="M44 30H590"/>
                            <path d="M44 75H590"/>
                            <path d="M44 120H590"/>
                            <path d="M44 165H590"/>
                        </g>
                        <g fill="#52698f">
                            <text x="20" y="34">60</text>
                            <text x="20" y="79">40</text>
                            <text x="20" y="124">20</text>
                            <text x="25" y="169">0</text>
                        </g>
                        <polyline points="44,140 135,126 226,88 317,109 408,55 499,104 590,83" fill="none" stroke="var(--blue)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <g fill="var(--blue)" stroke="#ffffff" stroke-width="4">
                            <circle cx="44" cy="140" r="5"/>
                            <circle cx="135" cy="126" r="5"/>
                            <circle cx="226" cy="88" r="5"/>
                            <circle cx="317" cy="109" r="5"/>
                            <circle cx="408" cy="55" r="5"/>
                            <circle cx="499" cy="104" r="5"/>
                            <circle cx="590" cy="83" r="5"/>
                        </g>
                        <g fill="#52698f">
                            @foreach ($trend as $index => $point)
                                <text x="{{ 44 + ($index * 91) }}" y="202" text-anchor="middle">{{ $point['label'] }}</text>
                            @endforeach
                        </g>
                    </svg>
                </div>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <h3 class="panel-title">Tickets by Status</h3>
                    <button class="panel-filter" type="button">Last 7 Days <svg><use href="#icon-chevron-down"></use></svg></button>
                </div>
                <div class="panel-body">
                    <div class="chart-split">
                        <div class="donut" style="--donut: conic-gradient(var(--blue) 0 26.6%, #2fc56f 26.6% 90.7%, #ff5148 90.7% 100%)"></div>
                        <ul class="legend">
                            @foreach ($statusBreakdown as $item)
                                <li class="legend-item" style="--dot: {{ $item['color'] }}">
                                    <span class="legend-dot"></span>
                                    <span>{{ $item['label'] }}<strong>{{ $item['value'] }} ({{ $item['percent'] }})</strong></span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <h3 class="panel-title">Tickets by Product</h3>
                    <button class="panel-filter" type="button">Last 30 Days <svg><use href="#icon-chevron-down"></use></svg></button>
                </div>
                <div class="panel-body">
                    <div class="chart-split">
                        <div class="donut" style="--donut: conic-gradient(var(--blue) 0 45.3%, #2fc56f 45.3% 67.2%, #f4a51c 67.2% 79.7%, #765cff 79.7% 89.1%, #18aac4 89.1% 100%)"></div>
                        <ul class="legend">
                            @foreach ($productBreakdown as $item)
                                <li class="legend-item" style="--dot: {{ $item['color'] }}">
                                    <span class="legend-dot"></span>
                                    <span>{{ $item['label'] }}<strong>{{ $item['value'] }} ({{ $item['percent'] }})</strong></span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </article>
        </section>

        <section class="lower-grid">
            <article class="panel table-panel">
                <div class="panel-head">
                    <h3 class="panel-title">Recent Tickets</h3>
                    <a class="button-link" href="#">View All <svg><use href="#icon-arrow-right"></use></svg></a>
                </div>
                <div class="recent-table-wrap">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th style="width: 32%">Ticket</th>
                                <th style="width: 18%">Product</th>
                                <th style="width: 15%">Status</th>
                                <th style="width: 12%">Priority</th>
                                <th style="width: 23%">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTickets as $ticket)
                                @php $statusClass = strtolower($ticket['status']); @endphp
                                <tr>
                                    <td>
                                        <span class="ticket-code">{{ $ticket['ticket'] }}</span>
                                        <span class="ticket-summary">{{ $ticket['summary'] }}</span>
                                    </td>
                                    <td>{{ $ticket['product'] }}</td>
                                    <td><span class="status-track status-{{ $statusClass }}">{{ $ticket['status'] }}</span></td>
                                    <td>{{ $ticket['priority'] }}</td>
                                    <td>{{ $ticket['updated'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>

            <aside class="panel summary-card" aria-labelledby="quick-summary-title">
                <div class="panel-head">
                    <h3 class="section-title" id="quick-summary-title">
                        <svg><use href="#icon-list"></use></svg>
                        <span>Quick Summary</span>
                    </h3>
                </div>
                <ul class="summary-list">
                    @foreach ($quickSummary as $item)
                        <li class="summary-row tone-{{ $item['tone'] }}">
                            <span class="summary-icon"><svg><use href="#{{ $item['icon'] }}"></use></svg></span>
                            <span class="summary-label">{{ $item['label'] }}</span>
                            <span class="summary-value">{{ $item['value'] }}</span>
                            <svg><use href="#icon-chevron-down"></use></svg>
                        </li>
                    @endforeach
                </ul>
                <a class="summary-link" href="#analytics-overview">
                    <span>View full analytics</span>
                    <svg><use href="#icon-arrow-right"></use></svg>
                </a>
            </aside>
        </section>
    </div>
@endsection

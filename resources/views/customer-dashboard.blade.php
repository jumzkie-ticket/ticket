@extends('layouts.app-shell')

@section('title', 'Customer Home')
@section('page-title', 'Welcome back, '.$user->first_name.'!')
@section('page-subtitle', 'Manage your support requests and get help from the Xceler8 team.')

@push('styles')
<style>
    .customer-home { display: grid; gap: 20px; }
    .customer-hero { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(280px, .8fr); gap: 24px; padding: 28px; overflow: hidden; border: 1px solid color-mix(in srgb, var(--blue) 25%, var(--line)); border-radius: 14px; background: linear-gradient(135deg, color-mix(in srgb, var(--blue) 12%, var(--panel)), var(--panel) 64%); box-shadow: var(--shadow); }
    .customer-kicker { margin: 0 0 7px; color: var(--blue); font-size: 12px; font-weight: 900; letter-spacing: .7px; text-transform: uppercase; }
    .customer-hero h2 { max-width: 620px; margin: 0; color: var(--ink); font-size: clamp(25px, 3vw, 38px); line-height: 1.15; }
    .customer-hero-copy { max-width: 620px; margin: 12px 0 22px; color: var(--muted); font-size: 14px; line-height: 1.65; }
    .customer-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .customer-action { min-height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 16px; border: 1px solid var(--blue); border-radius: 8px; background: var(--blue); color: #fff; font-size: 12px; font-weight: 900; text-decoration: none; }
    .customer-action.secondary { background: var(--panel); color: var(--blue); }
    .customer-action svg { width: 16px; height: 16px; }
    .ticket-search { align-self: center; display: grid; gap: 11px; padding: 18px; border: 1px solid var(--line); border-radius: 11px; background: color-mix(in srgb, var(--panel) 92%, transparent); }
    .ticket-search label { color: var(--ink); font-size: 13px; font-weight: 900; }
    .ticket-search p { margin: -4px 0 2px; color: var(--muted); font-size: 11px; line-height: 1.45; }
    .ticket-search-row { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 8px; }
    .ticket-search input { min-width: 0; height: 41px; padding: 0 12px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); outline: none; }
    .ticket-search input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .ticket-search button { min-height: 41px; padding: 0 14px; border: 0; border-radius: 7px; background: var(--blue); color: #fff; font-size: 11px; font-weight: 900; cursor: pointer; }
    .customer-status-guide { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
    .customer-status-card { --status-color: var(--status-color-light); min-width: 0; min-height: 100px; display: flex; align-items: center; gap: 14px; padding: 17px 16px; border: 1px solid var(--line); border-radius: 11px; background: var(--panel); }
    :root.app-theme-dark .customer-status-card { --status-color: var(--status-color-dark); }
    @media (prefers-color-scheme: dark) { :root.app-theme-system .customer-status-card { --status-color: var(--status-color-dark); } }
    .customer-status-icon { width: 44px; height: 44px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 50%; background: color-mix(in srgb, var(--status-color) 20%, var(--panel)); color: var(--status-color); }
    .customer-status-icon svg { width: 20px; height: 20px; }
    .customer-status-copy { min-width: 0; }
    .customer-status-copy span { display: block; overflow: hidden; color: var(--muted); font-size: 9px; font-weight: 850; text-overflow: ellipsis; white-space: nowrap; }
    .customer-status-copy strong { display: block; margin-top: 4px; color: var(--ink); font-size: 21px; line-height: 1; font-weight: 900; }
    .customer-grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: 16px; align-items: start; }
    .customer-panel { border: 1px solid var(--line); border-radius: 11px; background: var(--panel); box-shadow: 0 8px 28px rgba(10, 33, 74, .04); }
    .customer-panel-head { min-height: 58px; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 0 17px; border-bottom: 1px solid var(--line); }
    .customer-panel-head h2 { margin: 0; color: var(--ink); font-size: 15px; font-weight: 900; }
    .customer-panel-head a { color: var(--blue); font-size: 11px; font-weight: 900; text-decoration: none; }
    .help-options { display: grid; gap: 10px; padding: 14px; }
    .help-option { display: flex; align-items: center; gap: 12px; padding: 13px; border: 1px solid var(--line); border-radius: 9px; color: var(--ink); text-decoration: none; }
    .help-option:hover { border-color: var(--blue); background: var(--blue-soft); }
    .help-option > svg { width: 20px; height: 20px; flex: 0 0 auto; color: var(--blue); }
    .help-option strong, .help-option span { display: block; }
    .help-option strong { font-size: 12px; }
    .help-option span { margin-top: 3px; color: var(--muted); font-size: 10px; line-height: 1.4; }
    @media (max-width: 960px) { .customer-hero, .customer-grid { grid-template-columns: 1fr; } .customer-status-guide { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 560px) { .customer-hero { padding: 20px; } .ticket-search-row { grid-template-columns: 1fr; } .customer-status-guide { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
@endpush

@section('content')
<div class="customer-home">
    <section class="customer-hero" aria-labelledby="customer-welcome-title">
        <div>
            <p class="customer-kicker">Customer Support Portal</p>
            <h2 id="customer-welcome-title">How can we help you today?</h2>
            <p class="customer-hero-copy">Submit a new support request, follow the progress of an existing ticket, or connect with our support team.</p>
            <div class="customer-actions">
                <a class="customer-action" href="{{ route('tickets.create') }}"><svg><use href="#icon-plus"></use></svg>Create New Ticket</a>
                <a class="customer-action secondary" href="{{ route('tickets.index') }}"><svg><use href="#icon-ticket"></use></svg>View My Tickets</a>
            </div>
        </div>
        <form class="ticket-search" method="GET" action="{{ route('tickets.index') }}">
            <label for="customer-ticket-search">Find a ticket</label>
            <p>Search using a ticket number, subject, product, or keyword.</p>
            <div class="ticket-search-row">
                <input id="customer-ticket-search" name="search" type="search" placeholder="Ticket ID or keyword" aria-label="Ticket ID or keyword">
                <button type="submit">Search</button>
            </div>
        </form>
    </section>

    <section class="customer-status-guide" aria-label="Customer Ticket Status Summary">
        @foreach ($statusGuide as $statusCard)
            <article class="customer-status-card" style="--status-color-light: {{ $statusCard['palette']['light'] }}; --status-color-dark: {{ $statusCard['palette']['dark'] }}">
                <span class="customer-status-icon" aria-hidden="true"><svg><use href="#{{ $statusCard['icon'] }}"></use></svg></span>
                <div class="customer-status-copy"><span>{{ $statusCard['label'] }}</span><strong>{{ $statusCard['value'] }}</strong></div>
            </article>
        @endforeach
    </section>

    <div class="customer-grid">
        <aside class="customer-panel" aria-labelledby="customer-help-title">
            <header class="customer-panel-head"><h2 id="customer-help-title">Need Help?</h2></header>
            <div class="help-options">
                <a class="help-option" href="{{ route('contact-support') }}"><svg><use href="#icon-headset"></use></svg><span><strong>Contact Support</strong><span>Send a message to our support team.</span></span></a>
                <a class="help-option" href="{{ route('tickets.create') }}"><svg><use href="#icon-plus"></use></svg><span><strong>Report an Issue</strong><span>Tell us what happened so we can help.</span></span></a>
                <a class="help-option" href="#"><svg><use href="#icon-book"></use></svg><span><strong>Knowledge Base</strong><span>Browse guides and common solutions.</span></span></a>
            </div>
        </aside>
    </div>
</div>
@endsection

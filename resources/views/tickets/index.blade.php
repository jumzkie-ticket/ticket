@extends('layouts.app-shell')

@section('title', 'My Tickets')
@section('page-title', 'My Tickets')
@section('page-subtitle', 'Track, monitor, and manage your support requests.')

@push('styles')
<style>
    .tickets-dashboard { display: grid; grid-template-columns: minmax(0, 1fr) 230px; gap: 16px; align-items: start; }
    .tickets-main { min-width: 0; display: grid; gap: 16px; }
    .ticket-card { border: 1px solid var(--line); border-radius: 11px; background: var(--panel); box-shadow: 0 8px 28px rgba(10, 33, 74, .04); }
    .ticket-toolbar { display: grid; grid-template-columns: minmax(240px, 1fr) 170px 190px auto; gap: 12px; align-items: end; padding: 18px; }
    .ticket-control { display: grid; gap: 5px; }
    .ticket-control span { color: var(--muted); font-size: 9px; font-weight: 800; }
    .ticket-input { width: 100%; height: 40px; padding: 0 12px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); outline: none; }
    .ticket-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .ticket-create { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 0 17px; border-radius: 7px; background: var(--blue); color: #fff; font-size: 11px; font-weight: 900; text-decoration: none; white-space: nowrap; }
    .ticket-create svg { width: 14px; height: 14px; }
    .ticket-metrics { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
    .ticket-metric { min-width: 0; min-height: 100px; display: flex; align-items: center; gap: 14px; padding: 17px 16px; border-radius: 11px; box-shadow: none; }
    .ticket-metric { --metric-color: var(--metric-color-light); }
    :root.app-theme-dark .ticket-metric { --metric-color: var(--metric-color-dark); }
    @media (prefers-color-scheme: dark) { :root.app-theme-system .ticket-metric { --metric-color: var(--metric-color-dark); } }
    .metric-icon { width: 44px; height: 44px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 50%; background: color-mix(in srgb, var(--metric-color) 20%, var(--panel)); color: var(--metric-color); }
    .metric-icon svg { width: 20px; height: 20px; }
    .metric-copy { min-width: 0; }
    .metric-copy span { display: block; overflow: hidden; color: var(--muted); font-size: 9px; font-weight: 850; text-overflow: ellipsis; white-space: nowrap; }
    .metric-copy strong { display: block; margin-top: 4px; color: var(--ink); font-size: 21px; line-height: 1; font-weight: 900; }
    .ticket-table-wrap { overflow-x: auto; }
    .ticket-table { width: 100%; min-width: 980px; border-collapse: collapse; table-layout: auto; }
    .submitted-title { margin: 0; padding: 22px 20px; border-bottom: 1px solid var(--line); color: var(--ink); font-size: 24px; line-height: 1.2; font-weight: 900; }
    .ticket-table th, .ticket-table td { padding: 18px 16px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
    .ticket-table th { color: var(--ink); font-size: 13px; line-height: 1.35; font-weight: 900; white-space: nowrap; }
    .ticket-table td { color: var(--ink); font-size: 14px; font-weight: 700; line-height: 1.5; }
    .ticket-table th:nth-child(1), .ticket-table td:nth-child(1) { width: 112px; }
    .ticket-table th:nth-child(2), .ticket-table td:nth-child(2) { width: 190px; }
    .ticket-table th:nth-child(4), .ticket-table td:nth-child(4) { width: 165px; }
    .ticket-table th:nth-child(5), .ticket-table td:nth-child(5), .ticket-table th:nth-child(6), .ticket-table td:nth-child(6) { width: 135px; }
    .ticket-table th:nth-child(7), .ticket-table td:nth-child(7) { width: 88px; }
    .ticket-code { color: var(--blue); font-size: 14px; font-weight: 900; white-space: nowrap; }
    .ticket-subject { min-width: 280px; }
    .ticket-status { --ticket-status-color: var(--ticket-status-light, var(--blue)); display: inline-flex; align-items: center; gap: 6px; padding: 7px 10px; border-radius: 6px; background: color-mix(in srgb, var(--ticket-status-color) 14%, var(--panel)); color: var(--ticket-status-color); font-size: 12px; font-weight: 900; white-space: nowrap; }
    .ticket-status::before { content: ''; width: 5px; height: 5px; border: 1.5px solid currentColor; border-radius: 50%; }
    :root.app-theme-dark .ticket-status { --ticket-status-color: var(--ticket-status-dark, var(--blue)); }
    @media (prefers-color-scheme: dark) { :root.app-theme-system .ticket-status { --ticket-status-color: var(--ticket-status-dark, var(--blue)); } }
    .ticket-view { min-height: 40px; padding: 0 17px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); font-size: 13px; font-weight: 900; }
    .ticket-empty { padding: 50px 20px !important; color: var(--muted) !important; text-align: center !important; }
    .ticket-pager { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 17px 20px; color: var(--muted); font-size: 13px; }
    .ticket-pager-links { display: flex; gap: 6px; }
    .ticket-page-link { min-height: 31px; display: inline-flex; align-items: center; padding: 0 10px; border: 1px solid var(--line); border-radius: 6px; color: var(--ink); font-weight: 850; text-decoration: none; }
    .ticket-page-link.disabled { opacity: .4; pointer-events: none; }
    .tickets-rail { display: grid; gap: 12px; }
    .rail-card { padding: 16px; }
    .rail-title { display: flex; align-items: center; gap: 8px; margin: 0 0 13px; color: var(--ink); font-size: 12px; font-weight: 900; }
    .rail-title span { color: var(--blue); font-size: 16px; }
    .activity-list { display: grid; gap: 13px; margin: 0; padding: 0; list-style: none; }
    .activity-item { position: relative; padding-left: 14px; color: var(--ink); font-size: 9px; line-height: 1.45; }
    .activity-item::before { content: ''; position: absolute; top: 5px; left: 0; width: 6px; height: 6px; border-radius: 50%; background: var(--blue); }
    .activity-item time { display: block; margin-top: 2px; color: var(--muted); }
    .guide-list { display: grid; gap: 10px; }
    .guide-row { display: grid; grid-template-columns: auto 1fr; gap: 9px; align-items: center; color: var(--muted); font-size: 10px; line-height: 1.4; }
    .guide-row .ticket-status { padding: 5px 8px; font-size: 9px; line-height: 1.2; }
    .ticket-modal { position: fixed; inset: 0; z-index: 1000; display: none; place-items: center; padding: 24px; background: rgba(7, 27, 77, .42); backdrop-filter: blur(3px); }
    .ticket-modal.active { display: grid; }
    .ticket-modal-card { width: min(980px, calc(100vw - 48px)); max-height: calc(100vh - 48px); overflow-y: auto; border: 1px solid var(--line); border-radius: 13px; background: var(--panel); box-shadow: 0 28px 80px rgba(7, 27, 77, .3); }
    .modal-head { position: sticky; top: 0; z-index: 1; display: flex; justify-content: space-between; align-items: center; min-height: 68px; padding: 19px 28px; border-bottom: 1px solid var(--line); background: var(--panel); }
    .modal-head h2 { margin: 0; font-size: 22px; line-height: 1.2; }
    .modal-close { width: 38px; height: 38px; border: 0; border-radius: 7px; background: transparent; color: var(--muted); font-size: 27px; }
    .modal-body { padding: 26px 28px; }
    .modal-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 2px 42px; margin: 0; padding-bottom: 20px; border-bottom: 1px solid var(--line); }
    .modal-summary div { display: grid; grid-template-columns: 125px minmax(0, 1fr); gap: 14px; align-items: center; padding: 9px 0; }
    .modal-summary dt { color: var(--muted); font-size: 13px; line-height: 1.4; font-weight: 850; }
    .modal-summary dd { margin: 0; font-size: 14px; line-height: 1.45; font-weight: 750; overflow-wrap: anywhere; }
    .classification-form { padding-bottom: 20px; border-bottom: 1px solid var(--line); }
    .classification-form .modal-summary { padding-bottom: 14px; border-bottom: 0; }
    .modal-select { width: 100%; min-height: 42px; padding: 0 12px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); font-size: 13px; font-weight: 750; outline: none; }
    .modal-select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .classification-save { min-height: 40px; display: block; margin-left: auto; padding: 0 17px; border: 0; border-radius: 7px; background: var(--blue); color: #fff; font-size: 12px; font-weight: 900; }
    .modal-section { padding: 20px 0; border-bottom: 1px solid var(--line); }
    .modal-section h3 { margin: 0 0 10px; font-size: 15px; }
    .modal-section p { margin: 0; font-size: 14px; line-height: 1.7; white-space: pre-line; }
    .resolution-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .resolution-head h3 { margin: 0; }
    .resolution-update { min-height: 42px; padding: 0 18px; border: 1px solid var(--blue); border-radius: 7px; background: var(--blue); color: #fff; font-size: 13px; font-weight: 900; }
    .resolution-form { display: grid; gap: 14px; }
    .resolution-form label { display: grid; gap: 5px; color: var(--ink); font-size: 9px; font-weight: 850; }
    .resolution-form input, .resolution-form textarea { width: 100%; padding: 9px 10px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--ink); font: inherit; }
    .resolution-form textarea { min-height: 120px; resize: vertical; }
    .resolution-help { margin-top: -8px; color: var(--muted); font-size: 8px; }
    .resolution-save { justify-self: end; min-height: 33px; padding: 0 13px; border: 0; border-radius: 6px; background: var(--blue); color: #fff; font-size: 9px; font-weight: 900; }
    .resolution-table-wrap { overflow-x: auto; }
    .resolution-table { width: 100%; border-collapse: collapse; border: 1px solid var(--line); }
    .resolution-table th, .resolution-table td { padding: 12px 14px; border: 1px solid var(--line); text-align: left; font-size: 13px; line-height: 1.45; }
    .resolution-table th { background: color-mix(in srgb, var(--canvas) 65%, var(--panel)); color: var(--ink); font-weight: 900; }
    .resolution-empty { color: var(--muted); text-align: center !important; }
    .resolution-recommendation { margin-top: 10px !important; color: var(--ink); }
    .resolution-actions { display: flex; gap: 6px; }
    .resolution-action { min-height: 33px; padding: 0 12px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--blue); font-size: 11px; font-weight: 900; }
    .resolution-action.edit { border-color: var(--blue); background: var(--blue); color: #fff; }
    .modal-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 17px 28px; }
    .resolution-modal-card { width: min(470px, 100%); }
    .resolution-modal-card .modal-head h2 { font-size: 20px; }
    .resolution-modal-card .resolution-form { gap: 17px; }
    .resolution-modal-card .resolution-form label { gap: 7px; font-size: 12px; }
    .resolution-modal-card .resolution-form input,
    .resolution-modal-card .resolution-form textarea { padding: 11px 12px; font-size: 13px; line-height: 1.5; }
    .resolution-modal-card .resolution-form textarea { min-height: 145px; }
    .resolution-modal-card .resolution-help { font-size: 11px; line-height: 1.45; }
    .resolution-modal-card .ticket-view,
    .resolution-modal-card .resolution-save { min-height: 38px; padding: 0 16px; font-size: 12px; }
    .attachment-link { padding: 0; border: 0; background: transparent; color: var(--blue); font: inherit; font-weight: 900; text-decoration: underline; cursor: pointer; }
    .ticket-modal.attachment-modal { padding: 20px; }
    .attachment-modal-card { width: min(920px, calc(100vw - 40px)); height: auto; max-height: calc(100vh - 40px); display: flex; flex-direction: column; overflow: hidden; }
    .attachment-preview-frame { min-height: 260px; height: min(72vh, 720px); display: grid; place-items: center; overflow: hidden; padding: 10px; background: #fff; }
    .attachment-preview { width: 100%; height: 100%; display: block; border: 0; background: #fff; }
    img.attachment-preview { width: auto; height: auto; max-width: 100%; max-height: 100%; object-fit: contain; object-position: center; background: #fff; }
    body.ticket-modal-open { overflow: hidden; }
    @media (max-width: 1180px) { .tickets-dashboard { grid-template-columns: 1fr; } .tickets-rail { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 900px) { .ticket-toolbar { grid-template-columns: 1fr 1fr; } .ticket-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 620px) { .ticket-toolbar, .tickets-rail { grid-template-columns: 1fr; } .ticket-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); } .ticket-modal { padding: 12px; } .ticket-modal-card { width: 100%; max-height: calc(100vh - 24px); } .modal-head, .modal-body, .modal-foot { padding-right: 18px; padding-left: 18px; } .modal-summary { grid-template-columns: 1fr; } .modal-summary div { grid-template-columns: 110px minmax(0, 1fr); } }
</style>
@endpush

@section('content')
@php($isCustomer = auth()->user()?->roles->contains('slug', 'customer') ?? false)
<div class="tickets-dashboard">
    <main class="tickets-main">
        <form class="ticket-card ticket-toolbar" method="GET" action="{{ route('tickets.index') }}">
            <label class="ticket-control"><span>Search</span><input class="ticket-input" name="search" type="search" value="{{ $search }}" placeholder="Search by ticket ID, subject, or keyword..."></label>
            <label class="ticket-control"><span>Status</span><select class="ticket-input" name="status" onchange="this.form.submit()"><option value="">All Statuses</option>@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="ticket-control"><span>Product</span><select class="ticket-input" name="product" onchange="this.form.submit()"><option value="">All Products</option>@foreach ($products as $item)<option value="{{ $item }}" @selected($selectedProduct === $item)>{{ $item }}</option>@endforeach</select></label>
            <a class="ticket-create" href="{{ route('tickets.create') }}"><svg><use href="#icon-plus"></use></svg>Create New Ticket</a>
        </form>

        <section class="ticket-metrics" aria-label="Ticket Status Guide">
            @foreach ($statusGuide as $statusCard)
                <article class="ticket-card ticket-metric" style="--metric-color-light: {{ $statusCard['palette']['light'] }}; --metric-color-dark: {{ $statusCard['palette']['dark'] }}">
                    <span class="metric-icon" aria-hidden="true"><svg><use href="#{{ $statusCard['icon'] }}"></use></svg></span>
                    <div class="metric-copy"><span>{{ $statusCard['label'] }}</span><strong>{{ $statusCard['value'] }}</strong></div>
                </article>
            @endforeach
        </section>

        <section class="ticket-card" aria-labelledby="submitted-tickets-title">
            <h2 id="submitted-tickets-title" class="submitted-title">Submitted Tickets</h2>
            <div class="ticket-table-wrap">
                <table class="ticket-table">
                    <thead><tr><th>Ticket ID</th><th>Company Name</th><th>Subject / Issue</th><th>Status</th><th>Date Created</th><th>Last Updated</th><th>Action</th></tr></thead>
                    <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td><span class="ticket-code">TKT-{{ str_pad((string) $ticket->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                            <td>{{ $ticket->company_name ?: 'Not set' }}</td>
                            <td class="ticket-subject">{{ $ticket->issue_encountered }}</td>
                            @php($ticketStatusPalette = $ticket->ticketStatus?->palette() ?? \App\Models\TicketStatus::paletteFor($ticket->status))
                            <td><span class="ticket-status" style="--ticket-status-light: {{ $ticketStatusPalette['light'] }}; --ticket-status-dark: {{ $ticketStatusPalette['dark'] }}">{{ $statuses[$ticket->status] ?? ucfirst($ticket->status) }}</span></td>
                            <td>{{ $ticket->created_at?->format('M d, Y') }}<br>{{ $ticket->created_at?->format('h:i A') }}</td>
                            <td>{{ $ticket->updated_at?->format('M d, Y') }}<br>{{ $ticket->updated_at?->format('h:i A') }}</td>
                            <td><button class="ticket-view" type="button" data-ticket-modal-open="ticket-modal-{{ $ticket->id }}">View</button></td>
                        </tr>
                    @empty
                        <tr><td class="ticket-empty" colspan="7">No tickets found. Create a ticket to get started.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="ticket-pager"><span>Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets</span>@if ($tickets->hasPages())<span class="ticket-pager-links"><a class="ticket-page-link {{ $tickets->onFirstPage() ? 'disabled' : '' }}" href="{{ $tickets->previousPageUrl() ?? '#' }}">Previous</a><a class="ticket-page-link {{ $tickets->hasMorePages() ? '' : 'disabled' }}" href="{{ $tickets->nextPageUrl() ?? '#' }}">Next</a></span>@endif</div>
        </section>
    </main>

    <aside class="tickets-rail">
        <section class="ticket-card rail-card"><h2 class="rail-title"><span>◴</span>Recent Activity</h2><ul class="activity-list">@forelse ($recentTickets as $ticket)<li class="activity-item"><strong>TKT-{{ str_pad((string) $ticket->id, 5, '0', STR_PAD_LEFT) }}</strong> is {{ strtolower($statuses[$ticket->status] ?? $ticket->status) }}<time>{{ $ticket->updated_at?->format('M d, Y h:i A') }}</time></li>@empty<li class="activity-item">No recent activity.</li>@endforelse</ul></section>
        <section class="ticket-card rail-card"><h2 class="rail-title"><span>ⓘ</span>Ticket Status Guide</h2><div class="guide-list">@forelse ($ticketStatusOptions as $statusOption) @php($statusPalette = $statusOption->palette())<div class="guide-row"><span class="ticket-status" style="--ticket-status-light: {{ $statusPalette['light'] }}; --ticket-status-dark: {{ $statusPalette['dark'] }}">{{ ucwords(str_replace('-', ' ', $statusOption->status)) }}</span><span>{{ $statusOption->description() }}</span></div>@empty<div class="guide-row"><span>No ticket statuses configured.</span></div>@endforelse</div></section>
    </aside>

    @foreach ($tickets as $ticket)
        <div class="ticket-modal" id="ticket-modal-{{ $ticket->id }}" role="dialog" aria-modal="true" aria-labelledby="ticket-modal-title-{{ $ticket->id }}" hidden>
            <article class="ticket-modal-card"><header class="modal-head"><h2 id="ticket-modal-title-{{ $ticket->id }}">View Ticket Details</h2><button class="modal-close" type="button" data-ticket-modal-close aria-label="Close">&times;</button></header><div class="modal-body">
                <form class="classification-form" method="POST" action="{{ route('tickets.classification.update', $ticket) }}">@csrf @method('PUT')<dl class="modal-summary"><div><dt>Ticket No.</dt><dd class="ticket-code">TKT-{{ str_pad((string) $ticket->id, 5, '0', STR_PAD_LEFT) }}</dd></div><div><dt>Status</dt><dd><select class="modal-select" name="ticket_status_id" required @disabled($isCustomer)>@foreach ($ticketStatusOptions as $statusOption)<option value="{{ $statusOption->id }}" @selected($ticket->ticket_status_id === $statusOption->id || (!$ticket->ticket_status_id && $ticket->status === $statusOption->status))>{{ $statusOption->status }}</option>@endforeach</select></dd></div><div><dt>Company</dt><dd>{{ $ticket->company_name ?: 'Not set' }}</dd></div><div><dt>Created Date</dt><dd>{{ $ticket->created_at?->format('M d, Y h:i A') }}</dd></div><div><dt>Product</dt><dd>{{ $ticket->product_related ?: 'Not set' }}</dd></div><div><dt>Created By</dt><dd>{{ $ticket->full_name }}</dd></div><div><dt>Security Level</dt><dd><select class="modal-select" name="security_level_id" @disabled($isCustomer)><option value="">Select Security Level</option>@foreach ($securityLevels as $securityLevel)<option value="{{ $securityLevel->id }}" @selected($ticket->security_level_id === $securityLevel->id)>{{ $securityLevel->level_no }}</option>@endforeach</select></dd></div></dl>@unless($isCustomer)<button class="classification-save" type="submit">Save Ticket Details</button>@endunless</form>
                <section class="modal-section"><h3>Description</h3><p>{{ $ticket->issue_encountered }}</p></section><section class="modal-section"><h3>Scenario / Steps</h3><p>{{ $ticket->scenario }}</p></section><section class="modal-section"><h3>Expected Result</h3><p>{{ $ticket->expected_result }}</p></section>@if ($ticket->other_information)<section class="modal-section"><h3>Other Information</h3><p>{{ $ticket->other_information }}</p></section>@endif
                <section class="modal-section resolution-section">
                    <div class="resolution-head"><h3>✓ &nbsp; Resolution / Steps</h3></div>
                    <div class="resolution-table-wrap"><table class="resolution-table"><thead><tr><th>Date</th><th>Description</th><th>Action</th></tr></thead><tbody>@forelse ($ticket->resolutions as $resolution)<tr><td>{{ $resolution->date?->format('M d, Y h:i A') }}</td><td>{{ $resolution->description }}</td><td><span class="resolution-actions"><button class="resolution-action" type="button" data-ticket-modal-open="resolution-view-modal-{{ $resolution->id }}">View</button>@unless($isCustomer)<button class="resolution-action edit" type="button" data-ticket-modal-open="resolution-edit-modal-{{ $resolution->id }}">Edit</button>@endunless</span></td></tr>@empty<tr><td class="resolution-empty" colspan="3">No resolution steps have been added.</td></tr>@endforelse</tbody></table></div>
                </section>
                <section class="modal-section"><h3>Contact Details</h3><p>{{ $ticket->full_name }} · {{ $ticket->contact_email }} · {{ $ticket->contact_phone }}</p></section>@if ($ticket->attachment_original_name)<section class="modal-section"><h3>Attachment</h3><p><button class="attachment-link" type="button" data-ticket-modal-open="attachment-modal-{{ $ticket->id }}">{{ $ticket->attachment_original_name }}</button></p></section>@endif
            </div><footer class="modal-foot">@unless($isCustomer)<button class="resolution-update" type="button" data-ticket-modal-open="resolution-modal-{{ $ticket->id }}">Update Resolution / Steps</button>@endunless<button class="ticket-view" type="button" data-ticket-modal-close>Close</button></footer></article>
        </div>
        @unless($isCustomer)
        <div class="ticket-modal" id="resolution-modal-{{ $ticket->id }}" role="dialog" aria-modal="true" aria-labelledby="resolution-modal-title-{{ $ticket->id }}" hidden>
            <article class="ticket-modal-card resolution-modal-card">
                <header class="modal-head"><h2 id="resolution-modal-title-{{ $ticket->id }}">Update Resolution / Steps</h2><button class="modal-close" type="button" data-ticket-modal-close aria-label="Close">&times;</button></header>
                <form method="POST" action="{{ route('tickets.resolutions.store', $ticket) }}">@csrf
                    <div class="modal-body resolution-form"><label>Date <input name="date" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" required></label><label>Description <textarea name="description" placeholder="Enter the resolution steps or details..." required></textarea></label><span class="resolution-help">You can include the steps taken, troubleshooting details, or resolution.</span></div>
                    <footer class="modal-foot"><button class="ticket-view" type="button" data-ticket-modal-close>Cancel</button><button class="resolution-save" type="submit">Save</button></footer>
                </form>
            </article>
        </div>
        @endunless
        @if ($ticket->attachment_original_name)
            <div class="ticket-modal attachment-modal" id="attachment-modal-{{ $ticket->id }}" role="dialog" aria-modal="true" aria-labelledby="attachment-modal-title-{{ $ticket->id }}" hidden>
                <article class="ticket-modal-card attachment-modal-card"><header class="modal-head"><h2 id="attachment-modal-title-{{ $ticket->id }}">Attachment: {{ $ticket->attachment_original_name }}</h2><button class="modal-close" type="button" data-ticket-modal-close aria-label="Close attachment preview">&times;</button></header><div class="attachment-preview-frame">@if (in_array(strtolower(pathinfo($ticket->attachment_original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']))<img class="attachment-preview" src="{{ route('tickets.attachment', $ticket) }}" alt="{{ $ticket->attachment_original_name }}">@else<iframe class="attachment-preview" src="{{ route('tickets.attachment', $ticket) }}" title="Preview of {{ $ticket->attachment_original_name }}"></iframe>@endif</div><footer class="modal-foot"><button class="ticket-view" type="button" data-ticket-modal-close>Close</button></footer></article>
            </div>
        @endif
        @foreach ($ticket->resolutions as $resolution)
            <div class="ticket-modal" id="resolution-view-modal-{{ $resolution->id }}" role="dialog" aria-modal="true" aria-labelledby="resolution-view-title-{{ $resolution->id }}" hidden>
                <article class="ticket-modal-card resolution-modal-card"><header class="modal-head"><h2 id="resolution-view-title-{{ $resolution->id }}">Resolution Step Details</h2><button class="modal-close" type="button" data-ticket-modal-close aria-label="Close">&times;</button></header><div class="modal-body"><dl class="modal-summary"><div><dt>Date</dt><dd>{{ $resolution->date?->format('M d, Y h:i A') }}</dd></div></dl><section class="modal-section"><h3>Description</h3><p>{{ $resolution->description }}</p></section></div><footer class="modal-foot"><button class="ticket-view" type="button" data-ticket-modal-close>Close</button></footer></article>
            </div>
            @unless($isCustomer)<div class="ticket-modal" id="resolution-edit-modal-{{ $resolution->id }}" role="dialog" aria-modal="true" aria-labelledby="resolution-edit-title-{{ $resolution->id }}" hidden>
                <article class="ticket-modal-card resolution-modal-card"><header class="modal-head"><h2 id="resolution-edit-title-{{ $resolution->id }}">Edit Resolution / Step</h2><button class="modal-close" type="button" data-ticket-modal-close aria-label="Close">&times;</button></header><form method="POST" action="{{ route('tickets.resolutions.update', [$ticket, $resolution]) }}">@csrf @method('PUT')<div class="modal-body resolution-form"><label>Date <input name="date" type="datetime-local" value="{{ $resolution->date?->format('Y-m-d\TH:i') }}" required></label><label>Description <textarea name="description" required>{{ $resolution->description }}</textarea></label></div><footer class="modal-foot"><button class="ticket-view" type="button" data-ticket-modal-close>Cancel</button><button class="resolution-save" type="submit">Save Changes</button></footer></form></article>
            </div>@endunless
        @endforeach
    @endforeach
</div>
@endsection

@push('scripts')
<script>
(() => {
    const close = modal => { modal.classList.remove('active'); modal.hidden = true; if (!document.querySelector('.ticket-modal.active')) document.body.classList.remove('ticket-modal-open'); };
    document.querySelectorAll('[data-ticket-modal-open]').forEach(button => button.addEventListener('click', () => { const modal = document.getElementById(button.dataset.ticketModalOpen); if (!modal) return; modal.hidden = false; modal.classList.add('active'); document.body.classList.add('ticket-modal-open'); modal.querySelector('[data-ticket-modal-close]')?.focus(); }));
    document.querySelectorAll('.ticket-modal').forEach(modal => { modal.querySelectorAll('[data-ticket-modal-close]').forEach(button => button.addEventListener('click', () => close(modal))); modal.addEventListener('click', event => { if (event.target === modal) close(modal); }); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') { const modal = document.querySelector('.ticket-modal.active'); if (modal) close(modal); } });
})();
</script>
@endpush

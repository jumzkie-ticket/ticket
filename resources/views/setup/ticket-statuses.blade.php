@extends('layouts.app-shell')

@section('title', 'Ticket Status')
@section('page-title', 'Ticket Status')
@section('page-subtitle', 'Setup / Ticket Status Guide')

@push('styles')
<style>
    .status-page { display: grid; gap: 18px; }
    .status-layout { display: grid; grid-template-columns: 360px minmax(0, 1fr); gap: 18px; align-items: start; }
    .status-panel { border: 1px solid var(--line); border-radius: 10px; background: var(--panel); box-shadow: var(--shadow); }
    .status-head { padding: 14px 16px; border-bottom: 1px solid var(--line); }
    .status-title { margin: 0; color: var(--ink); font-size: 20px; font-weight: 900; }
    .status-copy { margin: 5px 0 0; color: var(--muted); font-size: 13px; }
    .status-body { padding: 18px; }
    .status-form { display: grid; gap: 14px; }
    .status-label { display: grid; gap: 7px; color: var(--ink); font-size: 13px; font-weight: 900; }
    .status-input { width: 100%; min-height: 42px; padding: 0 12px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--ink); font-size: 13px; outline: none; }
    .status-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .status-buttons, .status-actions { display: flex; gap: 8px; }
    .status-button { min-height: 39px; display: inline-flex; align-items: center; justify-content: center; padding: 0 14px; border: 1px solid var(--blue); border-radius: 6px; background: var(--blue); color: #fff; font-size: 12px; font-weight: 900; text-decoration: none; }
    .status-button.secondary { border-color: var(--line); background: var(--panel); color: var(--ink); }
    .status-table { width: 100%; border-collapse: collapse; }
    .status-table th { padding: 10px 16px; }
    .status-table td { padding: 9px 16px; }
    .status-table th, .status-table td { border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
    .status-table th { color: var(--muted); font-size: 12px; font-weight: 900; text-transform: uppercase; }
    .status-table td { color: var(--ink); font-size: 14px; font-weight: 800; }
    .status-badge { --status-badge-color: var(--status-badge-light); display: inline-flex; align-items: center; gap: 6px; padding: 5px 8px; border-radius: 6px; background: color-mix(in srgb, var(--status-badge-color) 14%, var(--panel)); color: var(--status-badge-color); font-size: 12px; font-weight: 900; }
    .status-badge::before { content: ''; width: 6px; height: 6px; border: 1.5px solid currentColor; border-radius: 50%; }
    :root.app-theme-dark .status-badge { --status-badge-color: var(--status-badge-dark); }
    @media (prefers-color-scheme: dark) { :root.app-theme-system .status-badge { --status-badge-color: var(--status-badge-dark); } }
    .status-action { min-height: 28px; display: inline-flex; align-items: center; padding: 0 9px; border: 1px solid var(--line); border-radius: 5px; background: var(--panel); color: var(--blue); font-size: 11px; font-weight: 900; text-decoration: none; }
    button.status-action { color: var(--red); cursor: pointer; }
    .status-errors { margin: 0; padding: 12px 16px 12px 32px; border: 1px solid #ffc8c8; border-radius: 8px; background: #fff1f1; color: #9f2424; font-size: 12px; }
    .status-empty { padding: 35px !important; color: var(--muted) !important; text-align: center !important; }
    @media (max-width: 900px) { .status-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="status-page"><x-status-prompt />
    @if ($errors->any())<ul class="status-errors">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <div class="status-layout">
        <section class="status-panel"><header class="status-head"><h2 class="status-title">{{ $selectedTicketStatus ? 'Edit Ticket Status' : 'Add Ticket Status' }}</h2><p class="status-copy">Manage the values shown in the Ticket Status Guide.</p></header><div class="status-body"><form class="status-form" method="POST" action="{{ $selectedTicketStatus ? route('ticket-statuses.update', $selectedTicketStatus) : route('ticket-statuses.store') }}">@csrf @if ($selectedTicketStatus) @method('PUT') @endif<label class="status-label">Status<input class="status-input" name="status" type="text" value="{{ old('status', $selectedTicketStatus?->status) }}" placeholder="Enter ticket status" maxlength="100" required></label><div class="status-buttons"><button class="status-button" type="submit">{{ $selectedTicketStatus ? 'Save Changes' : 'Add Ticket Status' }}</button>@if ($selectedTicketStatus)<a class="status-button secondary" href="{{ route('ticket-statuses.index') }}">Cancel</a>@endif</div></form></div></section>
        <section class="status-panel"><header class="status-head"><h2 class="status-title">Ticket Status Guide</h2><p class="status-copy">{{ $ticketStatuses->total() }} configured statuses</p></header><table class="status-table"><thead><tr><th>Status</th><th>Action</th></tr></thead><tbody>@forelse ($ticketStatuses as $ticketStatus) @php($statusPalette = $ticketStatus->palette())<tr><td><span class="status-badge" style="--status-badge-light: {{ $statusPalette['light'] }}; --status-badge-dark: {{ $statusPalette['dark'] }}">{{ $ticketStatus->status }}</span></td><td><span class="status-actions"><a class="status-action" href="{{ route('ticket-statuses.index', ['edit' => $ticketStatus->id]) }}">Edit</a><form method="POST" action="{{ route('ticket-statuses.destroy', $ticketStatus) }}" onsubmit="return confirm('Delete this ticket status?')">@csrf @method('DELETE')<button class="status-action" type="submit">Delete</button></form></span></td></tr>@empty<tr><td class="status-empty" colspan="2">No ticket statuses configured.</td></tr>@endforelse</tbody></table></section>
    </div>
</div>
@endsection

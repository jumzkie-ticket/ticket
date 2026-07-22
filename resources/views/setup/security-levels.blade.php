@extends('layouts.app-shell')

@section('title', 'Security Level')
@section('page-title', 'Security Level')
@section('page-subtitle', 'Setup / Security Level')

@push('styles')
<style>
    .security-page { display: grid; gap: 18px; }
    .security-layout { display: grid; grid-template-columns: 360px minmax(0, 1fr); gap: 18px; align-items: start; }
    .security-panel { border: 1px solid var(--line); border-radius: 10px; background: var(--panel); box-shadow: var(--shadow); }
    .security-panel-head { padding: 17px 18px; border-bottom: 1px solid var(--line); }
    .security-title { margin: 0; color: var(--ink); font-size: 20px; font-weight: 900; }
    .security-copy { margin: 5px 0 0; color: var(--muted); font-size: 13px; }
    .security-body { padding: 18px; }
    .security-form { display: grid; gap: 14px; }
    .security-label { display: grid; gap: 7px; color: var(--ink); font-size: 12px; font-weight: 900; }
    .security-input { width: 100%; min-height: 41px; padding: 9px 11px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--ink); font: inherit; outline: none; }
    textarea.security-input { min-height: 92px; resize: vertical; }
    .security-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .security-buttons, .security-actions { display: flex; gap: 8px; }
    .security-button { min-height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0 14px; border: 1px solid var(--blue); border-radius: 6px; background: var(--blue); color: #fff; font-size: 11px; font-weight: 900; text-decoration: none; }
    .security-button.secondary { border-color: var(--line); background: var(--panel); color: var(--ink); }
    .security-table-wrap { overflow-x: auto; }
    .security-table { width: 100%; border-collapse: collapse; }
    .security-table th, .security-table td { padding: 16px 15px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
    .security-table th { color: var(--muted); font-size: 12px; font-weight: 900; text-transform: uppercase; }
    .security-table td { color: var(--ink); font-size: 14px; font-weight: 750; line-height: 1.5; }
    .security-table td:first-child, .security-table td:nth-child(3) { font-weight: 900; }
    .security-action { min-height: 34px; display: inline-flex; align-items: center; padding: 0 11px; border: 1px solid var(--line); border-radius: 5px; background: var(--panel); color: var(--blue); font-size: 12px; font-weight: 900; text-decoration: none; }
    button.security-action { color: var(--red); cursor: pointer; }
    .security-errors { margin: 0; padding: 12px 16px 12px 32px; border: 1px solid #ffc8c8; border-radius: 8px; background: #fff1f1; color: #9f2424; font-size: 12px; }
    .security-empty { padding: 35px !important; color: var(--muted) !important; text-align: center !important; }
    @media (max-width: 900px) { .security-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="security-page">
    <x-status-prompt />
    @if ($errors->any())<ul class="security-errors">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <div class="security-layout">
        <section class="security-panel">
            <header class="security-panel-head"><h2 class="security-title">{{ $selectedSecurityLevel ? 'Edit Security Level' : 'Add Security Level' }}</h2><p class="security-copy">Define the support security level and SLA.</p></header>
            <div class="security-body"><form class="security-form" method="POST" action="{{ $selectedSecurityLevel ? route('security-levels.update', $selectedSecurityLevel) : route('security-levels.store') }}">@csrf @if ($selectedSecurityLevel) @method('PUT') @endif
                <label class="security-label">Level No.<input class="security-input" name="level_no" type="text" value="{{ old('level_no', $selectedSecurityLevel?->level_no) }}" placeholder="Example: Level 1" required></label>
                <label class="security-label">Description<textarea class="security-input" name="description" maxlength="500" placeholder="Enter security level description" required>{{ old('description', $selectedSecurityLevel?->description) }}</textarea></label>
                <label class="security-label">SLA<input class="security-input" name="sla" type="text" value="{{ old('sla', $selectedSecurityLevel?->sla) }}" placeholder="Example: 4 Hours" required></label>
                <div class="security-buttons"><button class="security-button" type="submit">{{ $selectedSecurityLevel ? 'Save Changes' : 'Add Security Level' }}</button>@if ($selectedSecurityLevel)<a class="security-button secondary" href="{{ route('security-levels.index') }}">Cancel</a>@endif</div>
            </form></div>
        </section>
        <section class="security-panel">
            <header class="security-panel-head"><h2 class="security-title">Security Level List</h2><p class="security-copy">{{ $securityLevels->total() }} configured levels</p></header>
            <div class="security-table-wrap"><table class="security-table"><thead><tr><th>Level No.</th><th>Description</th><th>SLA</th><th>Action</th></tr></thead><tbody>@forelse ($securityLevels as $securityLevel)<tr><td>{{ $securityLevel->level_no }}</td><td>{{ $securityLevel->description }}</td><td>{{ $securityLevel->sla }}</td><td><span class="security-actions"><a class="security-action" href="{{ route('security-levels.index', ['edit' => $securityLevel->id]) }}">Edit</a><form method="POST" action="{{ route('security-levels.destroy', $securityLevel) }}" onsubmit="return confirm('Delete this security level?')">@csrf @method('DELETE')<button class="security-action" type="submit">Delete</button></form></span></td></tr>@empty<tr><td class="security-empty" colspan="4">No security levels configured.</td></tr>@endforelse</tbody></table></div>
        </section>
    </div>
</div>
@endsection

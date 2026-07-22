@extends('layouts.app-shell')

@php
    $agreement = $editingAgreement ?? null;
@endphp

@section('title', $agreement ? 'Edit Work Agreement' : 'Work Agreement')
@section('page-title', $agreement ? 'Edit Work Agreement' : 'Work Agreement')
@section('page-subtitle', 'Create and manage client work agreement forms.')

@push('styles')
    <style>
        .wa-page { display: grid; gap: 20px; }
        .wa-card { overflow: hidden; border: 1px solid var(--line); border-radius: 12px; background: var(--panel); box-shadow: var(--shadow); }
        .wa-card-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 22px; border-bottom: 1px solid var(--line); }
        .wa-card-title { display: flex; align-items: center; gap: 10px; margin: 0; color: var(--ink); font-size: 18px; font-weight: 900; }
        .wa-card-title svg { width: 20px; height: 20px; color: var(--blue); }
        .wa-form { padding: 22px; }
        .wa-top-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 20px; }
        .wa-field { display: grid; gap: 7px; min-width: 0; }
        .wa-field.full { grid-column: 1 / -1; }
        .wa-field label, .wa-section-label { color: var(--ink); font-size: 13px; font-weight: 850; }
        .wa-required { color: #ef4444; }
        .wa-input, .wa-select, .wa-textarea { width: 100%; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); font: inherit; font-size: 14px; }
        .wa-input, .wa-select { min-height: 42px; padding: 9px 12px; }
        .wa-textarea { min-height: 96px; padding: 11px 12px; resize: vertical; }
        .wa-textarea.address { min-height: 72px; }
        .wa-input[readonly], .wa-textarea[readonly] { background: color-mix(in srgb, var(--muted) 7%, var(--panel)); color: var(--muted); }
        .wa-input:focus, .wa-select:focus, .wa-textarea:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px color-mix(in srgb, var(--blue) 14%, transparent); }
        .wa-type { display: flex; flex-wrap: wrap; gap: 20px; padding: 16px 0 2px; }
        .wa-check { display: inline-flex; align-items: center; gap: 9px; color: var(--ink); font-size: 14px; font-weight: 800; cursor: pointer; }
        .wa-check input { width: 18px; height: 18px; accent-color: var(--blue); }
        .wa-content { display: grid; gap: 16px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--line); }
        .wa-signatures { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 18px; padding-top: 20px; border-top: 1px solid var(--line); }
        .wa-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
        .wa-button { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 18px; border: 1px solid transparent; border-radius: 7px; font-size: 13px; font-weight: 900; text-decoration: none; cursor: pointer; }
        .wa-button.primary { background: var(--blue); color: #fff; }
        .wa-button.secondary { border-color: var(--line); background: var(--panel); color: var(--ink); }
        .wa-errors { margin: 0 22px 18px; padding: 12px 16px; border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: #991b1b; font-size: 13px; }
        .wa-errors ul { margin: 0; padding-left: 18px; }
        .wa-table-wrap { overflow-x: auto; }
        .wa-table { width: 100%; min-width: 900px; border-collapse: collapse; }
        .wa-table th, .wa-table td { padding: 13px 16px; border-bottom: 1px solid var(--line); color: var(--ink); font-size: 13px; text-align: left; vertical-align: middle; }
        .wa-table th { background: color-mix(in srgb, var(--blue) 7%, var(--panel)); color: var(--muted); font-size: 11px; font-weight: 900; text-transform: uppercase; }
        .wa-number { color: var(--blue); font-weight: 900; }
        .wa-badge { display: inline-flex; padding: 4px 9px; border-radius: 999px; background: color-mix(in srgb, var(--blue) 14%, transparent); color: var(--blue); font-size: 11px; font-weight: 900; }
        .wa-row-actions { display: flex; gap: 7px; }
        .wa-icon-action { width: 34px; height: 34px; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); color: var(--ink); cursor: pointer; }
        .wa-icon-action svg { width: 16px; height: 16px; }
        .wa-empty { padding: 26px !important; color: var(--muted) !important; text-align: center !important; }
        @media (max-width: 760px) {
            .wa-top-grid, .wa-signatures { grid-template-columns: 1fr; }
            .wa-field.full { grid-column: auto; }
            .wa-actions { align-items: stretch; flex-direction: column; }
        }
    </style>
@endpush

@section('content')
    <div class="wa-page">
        <x-status-prompt />

        <section class="wa-card" aria-labelledby="work-agreement-form-title">
            <div class="wa-card-head">
                <h2 class="wa-card-title" id="work-agreement-form-title">
                    <svg><use href="#icon-doc"></use></svg>
                    <span>{{ $agreement ? 'Edit Work Agreement' : 'Work Agreement Form' }}</span>
                </h2>
            </div>

            @if ($errors->any())
                <div class="wa-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="wa-form" method="POST" action="{{ $agreement ? route('work-agreements.update', $agreement) : route('work-agreements.store') }}">
                @csrf
                @if ($agreement)
                    @method('PUT')
                @endif

                <div class="wa-top-grid">
                    <div class="wa-field">
                        <label for="agreement_date">Date <span class="wa-required">*</span></label>
                        <input class="wa-input" id="agreement_date" name="agreement_date" type="date" value="{{ old('agreement_date', $agreement?->agreement_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="wa-field">
                        <label for="work_agreement_no">No.</label>
                        <input class="wa-input" id="work_agreement_no" type="text" value="{{ $agreement?->work_agreement_no ?? $nextAgreementNumber }}" readonly>
                    </div>
                    <div class="wa-field">
                        <label for="assign_fc_id">Name <span class="wa-required">*</span></label>
                        <select class="wa-select" id="assign_fc_id" name="assign_fc_id" required>
                            <option value="">Select FC...</option>
                            @foreach ($assignFcs as $assignFc)
                                <option value="{{ $assignFc->id }}" @selected((string) old('assign_fc_id', $agreement?->assign_fc_id) === (string) $assignFc->id)>{{ $assignFc->assign_fc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wa-field">
                        <label for="client_id">Client <span class="wa-required">*</span></label>
                        <select class="wa-select" id="client_id" name="client_id" required>
                            <option value="">Select client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected((string) old('client_id', $agreement?->client_id) === (string) $client->id)>{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wa-field">
                        <label for="position">Position</label>
                        <input class="wa-input" id="position" type="text" placeholder="Position will appear here" readonly>
                    </div>
                    <div class="wa-field">
                        <label for="address">Address</label>
                        <textarea class="wa-textarea address" id="address" name="address" placeholder="Client address will appear here" readonly>{{ old('address', $agreement?->address) }}</textarea>
                    </div>
                </div>

                <div class="wa-type" role="group" aria-label="Billing type">
                    <label class="wa-check">
                        <input type="hidden" name="billable" value="0">
                        <input id="billable" name="billable" type="checkbox" value="1" @checked((bool) old('billable', $agreement?->billable ?? false))>
                        <span>Billable</span>
                    </label>
                    <label class="wa-check">
                        <input type="hidden" name="non_billable" value="0">
                        <input id="non_billable" name="non_billable" type="checkbox" value="1" @checked((bool) old('non_billable', $agreement?->non_billable ?? false))>
                        <span>Non-Billable</span>
                    </label>
                </div>

                <div class="wa-content">
                    @foreach ([
                        'scope' => ['Scope', 'Describe the work scope'],
                        'objective' => ['Objective', 'State the objective'],
                        'current_issue' => ['Current Issue', 'Describe the current issue'],
                        'proposed_solutions' => ['Proposed Solutions', 'Describe the proposed solutions'],
                    ] as $field => [$label, $placeholder])
                        <div class="wa-field full">
                            <label for="{{ $field }}">{{ $label }} <span class="wa-required">*</span></label>
                            <textarea class="wa-textarea" id="{{ $field }}" name="{{ $field }}" placeholder="{{ $placeholder }}" required>{{ old($field, $agreement?->{$field}) }}</textarea>
                        </div>
                    @endforeach

                    <div class="wa-field full">
                        <label for="note">Note</label>
                        <textarea class="wa-textarea" id="note" name="note" placeholder="Additional notes">{{ old('note', $agreement?->note) }}</textarea>
                    </div>
                    <div class="wa-field">
                        <label for="estimated_man_days">Estimated Man-days <span class="wa-required">*</span></label>
                        <input class="wa-input" id="estimated_man_days" name="estimated_man_days" type="number" min="0" step="0.01" value="{{ old('estimated_man_days', $agreement?->estimated_man_days) }}" required>
                    </div>
                </div>

                <div class="wa-signatures">
                    <div class="wa-field">
                        <label for="project_manager">Project Manager <span class="wa-required">*</span></label>
                        <select class="wa-select" id="project_manager" name="project_manager_assign_fc_id" required>
                            <option value="">Select project manager...</option>
                            @foreach ($assignFcs as $assignFc)
                                <option value="{{ $assignFc->id }}" @selected((string) old('project_manager_assign_fc_id', $agreement?->project_manager_assign_fc_id) === (string) $assignFc->id)>
                                    {{ $assignFc->assign_fc }}
                                </option>
                            @endforeach
                        </select>
                        <label for="project_manager_designation">Project Manager Designation</label>
                        <input class="wa-input" id="project_manager_designation" type="text" value="{{ $agreement?->projectManager?->designation }}" placeholder="Designation will appear here" readonly>
                    </div>
                    <div class="wa-field">
                        <label for="consultant">Consultant <span class="wa-required">*</span></label>
                        <select class="wa-select" id="consultant" name="consultant_assign_fc_id" required>
                            <option value="">Select consultant...</option>
                            @foreach ($assignFcs as $assignFc)
                                <option value="{{ $assignFc->id }}" @selected((string) old('consultant_assign_fc_id', $agreement?->consultant_assign_fc_id) === (string) $assignFc->id)>
                                    {{ $assignFc->assign_fc }}
                                </option>
                            @endforeach
                        </select>
                        <label for="consultant_designation">Consultant Designation</label>
                        <input class="wa-input" id="consultant_designation" type="text" value="{{ $agreement?->consultantFc?->designation }}" placeholder="Designation will appear here" readonly>
                    </div>
                    <div class="wa-field">
                        <label for="accepted_by">Accepted by <span class="wa-required">*</span></label>
                        <input class="wa-input" id="accepted_by" name="accepted_by" type="text" value="{{ old('accepted_by', $agreement?->accepted_by) }}" required>
                        <label for="accepted_by_designation">Accepted by Designation <span class="wa-required">*</span></label>
                        <input class="wa-input" id="accepted_by_designation" name="accepted_by_designation" type="text" value="{{ old('accepted_by_designation', $agreement?->accepted_by_designation) }}" required>
                    </div>
                </div>

                <div class="wa-actions">
                    @if ($agreement)
                        <a class="wa-button secondary" href="{{ route('work-agreements.index') }}">Cancel</a>
                    @endif
                    <button class="wa-button primary" type="submit">{{ $agreement ? 'Update Work Agreement' : 'Save Work Agreement' }}</button>
                </div>
            </form>
        </section>

        <section class="wa-card" aria-labelledby="work-agreement-list-title">
            <div class="wa-card-head">
                <h2 class="wa-card-title" id="work-agreement-list-title">
                    <svg><use href="#icon-list"></use></svg>
                    <span>Work Agreement List</span>
                </h2>
            </div>
            <div class="wa-table-wrap">
                <table class="wa-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Type</th>
                            <th>Man-days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($agreements as $item)
                            <tr>
                                <td class="wa-number">{{ $item->work_agreement_no }}</td>
                                <td>{{ $item->agreement_date->format('M d, Y') }}</td>
                                <td>{{ $item->client->company_name }}</td>
                                <td>{{ $item->assignFc->assign_fc }}</td>
                                <td>{{ $item->assignFc->designation ?: '—' }}</td>
                                <td><span class="wa-badge">{{ $item->billable ? 'Billable' : 'Non-Billable' }}</span></td>
                                <td>{{ number_format((float) $item->estimated_man_days, 2) }}</td>
                                <td>
                                    <div class="wa-row-actions">
                                        <a class="wa-icon-action" href="{{ route('work-agreements.index', ['edit' => $item->id]) }}" aria-label="Edit {{ $item->work_agreement_no }}" title="Edit">
                                            <svg><use href="#icon-pencil"></use></svg>
                                        </a>
                                        <form method="POST" action="{{ route('work-agreements.destroy', $item) }}" onsubmit="return confirm('Delete this work agreement?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="wa-icon-action" type="submit" aria-label="Delete {{ $item->work_agreement_no }}" title="Delete">
                                                <svg><use href="#icon-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="wa-empty" colspan="8">No work agreements yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-simple-pager :paginator="$agreements" />
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const assignFcData = @json($assignFcData);
            const clientData = @json($clientData);
            const assignFc = document.getElementById('assign_fc_id');
            const position = document.getElementById('position');
            const client = document.getElementById('client_id');
            const address = document.getElementById('address');
            const billable = document.getElementById('billable');
            const nonBillable = document.getElementById('non_billable');
            const projectManager = document.getElementById('project_manager');
            const projectManagerDesignation = document.getElementById('project_manager_designation');
            const consultant = document.getElementById('consultant');
            const consultantDesignation = document.getElementById('consultant_designation');

            const updatePosition = () => {
                position.value = assignFcData[assignFc.value]?.designation || '';
            };
            const updateAddress = () => {
                address.value = clientData[client.value]?.address || '';
            };
            const updateProjectManagerDesignation = () => {
                projectManagerDesignation.value = assignFcData[projectManager.value]?.designation || '';
            };
            const updateConsultantDesignation = () => {
                consultantDesignation.value = assignFcData[consultant.value]?.designation || '';
            };

            assignFc.addEventListener('change', updatePosition);
            client.addEventListener('change', updateAddress);
            projectManager.addEventListener('change', updateProjectManagerDesignation);
            consultant.addEventListener('change', updateConsultantDesignation);
            billable.addEventListener('change', () => {
                if (billable.checked) nonBillable.checked = false;
            });
            nonBillable.addEventListener('change', () => {
                if (nonBillable.checked) billable.checked = false;
            });

            updatePosition();
            if (!address.value) updateAddress();
            updateProjectManagerDesignation();
            updateConsultantDesignation();
        })();
    </script>
@endpush

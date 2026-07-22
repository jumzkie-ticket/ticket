@extends('layouts.app-shell')

@section('title', 'Create Ticket')
@section('page-title', 'Create Ticket')
@section('page-subtitle', 'Submit a new support request to our technical team.')

@push('styles')
<style>
    .ticket-page { max-width: 1120px; margin: 0 auto; display: grid; gap: 14px; }
    .ticket-intro, .ticket-card { border: 1px solid var(--line); border-radius: 14px; background: var(--panel); box-shadow: var(--shadow); }
    .ticket-intro { padding: 16px 20px; border-top: 4px solid var(--blue); }
    .ticket-intro-text { display: grid; gap: 8px; margin: 0; color: var(--muted); font-size: 12px; line-height: 1.5; }
    .ticket-intro-text p { margin: 0; }
    .ticket-intro-details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px 20px; }
    .ticket-intro-text strong { color: var(--ink); }
    .ticket-card { overflow: hidden; }
    .ticket-section { padding: 16px 20px 18px; }
    .ticket-section + .ticket-section { border-top: 1px solid var(--line); }
    .ticket-section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .ticket-section-number { width: 27px; height: 27px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 8px; background: var(--blue-soft); color: var(--blue); font-size: 12px; font-weight: 900; }
    .ticket-section-title { margin: 0; color: var(--ink); font-size: 15px; font-weight: 900; }
    .ticket-section-copy { margin: 3px 0 0; color: var(--muted); font-size: 11px; }
    .ticket-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 11px 16px; }
    .ticket-field { min-width: 0; display: grid; gap: 5px; }
    .ticket-field.full { grid-column: 1 / -1; }
    .ticket-label { color: var(--ink); font-size: 12px; font-weight: 850; }
    .ticket-required { color: #e24444; }
    .ticket-input, .ticket-textarea { width: 100%; border: 1px solid var(--line); border-radius: 9px; background: var(--panel); color: var(--ink); outline: none; transition: border-color .16s ease, box-shadow .16s ease; }
    .ticket-input { height: 38px; padding: 0 11px; }
    .ticket-textarea { min-height: 118px; padding: 12px 13px; resize: vertical; font: inherit; line-height: 1.55; }
    .ticket-textarea[rows="3"] { min-height: 70px; height: 70px; padding-top: 9px; padding-bottom: 9px; }
    .ticket-input:focus, .ticket-textarea:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .ticket-input[readonly] { background: var(--canvas); color: var(--muted); cursor: default; }
    .ticket-input::placeholder, .ticket-textarea::placeholder { color: #9aa7bc; }
    .company-picker { position: relative; }
    .company-picker .ticket-input { padding-right: 42px; }
    .company-toggle { position: absolute; top: 1px; right: 1px; width: 38px; height: 36px; display: grid; place-items: center; border: 0; border-radius: 0 8px 8px 0; background: transparent; color: var(--muted); }
    .company-toggle svg { width: 16px; height: 16px; }
    .company-options { position: absolute; z-index: 20; top: calc(100% + 6px); left: 0; right: 0; max-height: 230px; margin: 0; padding: 6px; overflow-y: auto; list-style: none; border: 1px solid var(--line); border-radius: 10px; background: var(--panel); box-shadow: 0 18px 40px rgba(8, 30, 70, .16); }
    .company-options[hidden] { display: none; }
    .company-option { width: 100%; padding: 10px 11px; border: 0; border-radius: 7px; background: transparent; color: var(--ink); text-align: left; font-weight: 750; }
    .company-option:hover, .company-option:focus { background: var(--blue-soft); color: var(--blue); outline: none; }
    .company-empty { padding: 12px; color: var(--muted); text-align: center; }
    .ticket-errors { margin: 0 0 18px; padding: 13px 16px 13px 34px; border: 1px solid #f2b8b5; border-radius: 9px; background: var(--red-soft); color: #a62828; line-height: 1.6; }
    .ticket-actions { display: flex; justify-content: flex-end; gap: 9px; padding: 13px 20px; border-top: 1px solid var(--line); background: color-mix(in srgb, var(--canvas) 55%, var(--panel)); }
    .ticket-button { min-height: 38px; padding: 0 16px; border: 1px solid var(--line); border-radius: 8px; background: var(--panel); color: var(--ink); font-weight: 850; }
    .ticket-button.primary { display: inline-flex; align-items: center; gap: 9px; border-color: var(--blue); background: var(--blue); color: #fff; box-shadow: 0 8px 20px rgba(var(--blue-rgb), .22); }
    .ticket-button svg { width: 16px; height: 16px; }
    @media (max-width: 720px) {
        .ticket-intro, .ticket-section { padding-left: 18px; padding-right: 18px; }
        .ticket-intro-details { grid-template-columns: 1fr; }
        .ticket-grid { grid-template-columns: 1fr; }
        .ticket-field.full { grid-column: auto; }
        .ticket-actions { padding: 16px 18px; }
    }
</style>
@endpush

@section('content')
<div class="ticket-page">
    <section class="ticket-intro" aria-label="Ticket submission information">
        <div class="ticket-intro-text">
            <p><strong>XTI Ticket Support System</strong> — All submitted tickets are evaluated, prioritized, and managed according to their assigned severity level.</p>
            <div class="ticket-intro-details">
                <p><strong>Support Hours:</strong> Monday to Friday, 8:30 AM to 6:00 PM, excluding holidays.</p>
                <p><strong>Important Notice:</strong> Tickets without a client response or update for more than 30 days will be automatically closed.</p>
            </div>
            <p>Please note that this form only collects personal information that you provide.</p>
        </div>
    </section>

    <section class="ticket-card">
        <x-status-prompt />

        <form method="POST" action="{{ route('tickets.store') }}" id="create-ticket-form" enctype="multipart/form-data">
            @csrf

            <div class="ticket-section">
                <div class="ticket-section-heading">
                    <span class="ticket-section-number">1</span>
                    <div><h2 class="ticket-section-title">Contact Information</h2><p class="ticket-section-copy">Search for a company to fill in its registered contact details.</p></div>
                </div>

                @if ($errors->any())
                    <ul class="ticket-errors">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                @endif

                <div class="ticket-grid">
                    <label class="ticket-field">
                        <span class="ticket-label">Company Name <span class="ticket-required">*</span></span>
                        <span class="company-picker" id="company-picker">
                            <input type="hidden" name="client_id" id="client-id" value="{{ old('client_id') }}">
                            <input class="ticket-input" id="company-search" type="search" autocomplete="off" placeholder="Search and select a company" role="combobox" aria-autocomplete="list" aria-controls="company-options" aria-expanded="false" required>
                            <button class="company-toggle" type="button" aria-label="Show companies"><svg><use href="#icon-chevron-down"></use></svg></button>
                            <ul class="company-options" id="company-options" role="listbox" hidden></ul>
                        </span>
                    </label>
                    <label class="ticket-field"><span class="ticket-label">Contact Person</span><input class="ticket-input" id="contact-person" type="text" readonly></label>
                    <label class="ticket-field"><span class="ticket-label">Contact Number</span><input class="ticket-input" id="client-contact-number" type="text" readonly></label>
                    <label class="ticket-field"><span class="ticket-label">Email Address</span><input class="ticket-input" id="email-address" type="text" readonly></label>
                </div>
            </div>

            <div class="ticket-section">
                <div class="ticket-section-heading"><span class="ticket-section-number">2</span><div><h2 class="ticket-section-title">Product Information</h2><p class="ticket-section-copy">Product details are based on the selected client record.</p></div></div>
                <div class="ticket-grid">
                    <label class="ticket-field"><span class="ticket-label">Product Related</span><input class="ticket-input" id="product-related" type="text" readonly></label>
                    <label class="ticket-field"><span class="ticket-label">Software Version</span><input class="ticket-input" id="software-version" type="text" readonly></label>
                    <label class="ticket-field"><span class="ticket-label">Patch/FP</span><input class="ticket-input" id="patch-or-fp" type="text" readonly></label>
                    <label class="ticket-field"><span class="ticket-label">Database Version</span><input class="ticket-input" id="database-version" type="text" readonly></label>
                </div>
            </div>

            <div class="ticket-section">
                <div class="ticket-section-heading"><span class="ticket-section-number">3</span><div><h2 class="ticket-section-title">Issue Details</h2><p class="ticket-section-copy">Tell us what happened and what you expected to happen.</p></div></div>
                <div class="ticket-grid">
                    <label class="ticket-field"><span class="ticket-label">Issue Encountered <span class="ticket-required">*</span></span><textarea class="ticket-textarea" name="issue_encountered" rows="3" placeholder="Describe the issue encountered" required>{{ old('issue_encountered') }}</textarea></label>
                    <label class="ticket-field"><span class="ticket-label">Scenario upon encountering the issue. Please provide the steps. <span class="ticket-required">*</span></span><textarea class="ticket-textarea" name="scenario" rows="3" placeholder="List the steps that led to the issue" required>{{ old('scenario') }}</textarea></label>
                    <label class="ticket-field"><span class="ticket-label">Expected Result <span class="ticket-required">*</span></span><textarea class="ticket-textarea" name="expected_result" rows="3" placeholder="Describe the result you expected" required>{{ old('expected_result') }}</textarea></label>
                    <label class="ticket-field"><span class="ticket-label">Other information that you need to add</span><textarea class="ticket-textarea" name="other_information" rows="3" placeholder="Add any other helpful information">{{ old('other_information') }}</textarea></label>
                    <label class="ticket-field full"><span class="ticket-label">Attachment (Screenshot or Files)</span><input class="ticket-input" name="attachment" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"></label>
                </div>
            </div>

            <div class="ticket-section">
                <div class="ticket-section-heading"><span class="ticket-section-number">4</span><div><h2 class="ticket-section-title">Your Contact Details</h2><p class="ticket-section-copy">Provide the details our support team should use for this ticket.</p></div></div>
                <div class="ticket-grid">
                    <label class="ticket-field"><span class="ticket-label">Full Name <span class="ticket-required">*</span></span><input class="ticket-input" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="Enter your full name" required></label>
                    <label class="ticket-field"><span class="ticket-label">Email address to contact to <span class="ticket-required">*</span></span><input class="ticket-input" name="contact_email" type="email" value="{{ old('contact_email') }}" placeholder="name@company.com" required></label>
                    <label class="ticket-field"><span class="ticket-label">Contact Number <span class="ticket-required">*</span></span><input class="ticket-input" name="contact_phone" type="text" value="{{ old('contact_phone') }}" placeholder="Enter contact number" maxlength="20" required></label>
                </div>
            </div>

            <div class="ticket-actions">
                <button class="ticket-button" type="reset">Clear Form</button>
                <button class="ticket-button primary" type="submit"><svg><use href="#icon-plus"></use></svg><span>Submit Ticket</span></button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const clients = {{ Illuminate\Support\Js::from($clientLookup) }};
    const picker = document.getElementById('company-picker');
    const search = document.getElementById('company-search');
    const list = document.getElementById('company-options');
    const clientId = document.getElementById('client-id');
    const fields = {
        contact_person: document.getElementById('contact-person'), email_address: document.getElementById('email-address'),
        contact_number: document.getElementById('client-contact-number'), product_related: document.getElementById('product-related'),
        software_version: document.getElementById('software-version'), patch_or_fp: document.getElementById('patch-or-fp'),
        database_version: document.getElementById('database-version'),
    };
    const fill = (client) => Object.entries(fields).forEach(([key, input]) => input.value = client?.[key] || '');
    const select = (client) => { clientId.value = client.id; search.value = client.company_name; fill(client); close(); };
    const close = () => { list.hidden = true; search.setAttribute('aria-expanded', 'false'); };
    const render = () => {
        const query = search.value.trim().toLowerCase();
        const matches = clients.filter(client => client.company_name.toLowerCase().includes(query));
        list.innerHTML = '';
        if (!matches.length) { const empty = document.createElement('li'); empty.className = 'company-empty'; empty.textContent = 'No companies found'; list.appendChild(empty); }
        matches.forEach(client => { const item = document.createElement('li'); const button = document.createElement('button'); button.type = 'button'; button.className = 'company-option'; button.setAttribute('role', 'option'); button.textContent = client.company_name; button.addEventListener('click', () => select(client)); item.appendChild(button); list.appendChild(item); });
        list.hidden = false; search.setAttribute('aria-expanded', 'true');
    };
    search.addEventListener('focus', render);
    search.addEventListener('input', () => { clientId.value = ''; fill(null); render(); });
    picker.querySelector('.company-toggle').addEventListener('click', () => list.hidden ? render() : close());
    document.addEventListener('click', event => { if (!picker.contains(event.target)) close(); });
    document.getElementById('create-ticket-form').addEventListener('reset', () => setTimeout(() => { clientId.value = ''; search.value = ''; fill(null); close(); }, 0));
    const selected = clients.find(client => String(client.id) === clientId.value); if (selected) select(selected);
})();
</script>
@endpush

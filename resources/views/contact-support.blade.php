@extends('layouts.app-shell')

@section('title', 'Contact Support')
@section('page-title', 'Contact Support')
@section('page-subtitle', 'Get in touch with our support team for assistance.')

@push('styles')
    <style>
        .contact-page {
            display: grid;
            gap: 18px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(360px, 1fr);
            gap: 18px;
            align-items: start;
        }

        .support-side-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .contact-card,
        .support-panel,
        .support-note {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .contact-card {
            padding: 24px;
        }

        .contact-card-header,
        .support-panel-header,
        .support-note {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
        }

        .contact-card-header {
            margin-bottom: 24px;
        }

        .support-panel {
            padding: 22px;
        }

        .support-panel-header {
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line);
        }

        .support-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .support-icon svg {
            width: 20px;
            height: 20px;
        }

        .support-icon.green {
            background: var(--green-soft);
            color: var(--green);
        }

        .support-icon.violet {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .support-icon.amber {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .contact-title,
        .support-title {
            margin: 0;
            color: var(--ink);
            font-size: 16px;
            font-weight: 900;
        }

        .contact-copy,
        .support-copy {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 650;
            line-height: 1.45;
        }

        .contact-flash {
            min-height: 40px;
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 16px;
            padding: 0 13px;
            border: 1px solid #aee9c9;
            border-radius: 8px;
            background: #effbf5;
            color: #067143;
            font-size: 12px;
            font-weight: 900;
        }

        .contact-errors {
            margin: 0 0 16px;
            padding: 12px 14px 12px 30px;
            border: 1px solid #ffc8c8;
            border-radius: 8px;
            background: #fff1f1;
            color: #9f2424;
            font-size: 12px;
            font-weight: 800;
        }

        .contact-form {
            display: grid;
            gap: 18px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field-label {
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .required {
            color: var(--red);
        }

        .field-input,
        .field-select,
        .field-textarea {
            width: 100%;
            border: 1px solid #cbd9ee;
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 700;
            outline: none;
        }

        .field-input,
        .field-select {
            height: 40px;
            padding: 0 12px;
        }

        .field-textarea {
            min-height: 104px;
            resize: vertical;
            padding: 12px;
            line-height: 1.5;
        }

        .field-input:focus,
        .field-select:focus,
        .field-textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(var(--blue-rgb), .12);
        }

        .select-wrap {
            position: relative;
        }

        .field-select {
            appearance: none;
            padding-right: 34px;
        }

        .select-wrap svg {
            position: absolute;
            top: 50%;
            right: 12px;
            width: 14px;
            height: 14px;
            color: var(--muted);
            pointer-events: none;
            transform: translateY(-50%);
        }

        .textarea-wrap {
            display: grid;
            gap: 6px;
        }

        .message-count {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            text-align: right;
        }

        .attachment-drop {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            min-height: 76px;
            padding: 14px;
            border: 1px dashed #b7cff8;
            border-radius: 8px;
            background: color-mix(in srgb, var(--panel) 90%, var(--blue-soft));
        }

        .attachment-input {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            clip-path: inset(50%);
            white-space: nowrap;
        }

        .attachment-title {
            margin: 0;
            color: var(--ink);
            font-size: 12px;
            font-weight: 800;
        }

        .attachment-copy {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 650;
        }

        .attachment-browse {
            color: var(--blue);
            font-weight: 900;
        }

        .contact-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .contact-button {
            min-width: 180px;
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 16px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--panel);
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
        }

        .contact-button svg {
            width: 16px;
            height: 16px;
        }

        .contact-button-primary {
            border-color: var(--blue);
            background: var(--blue);
            color: #ffffff;
        }

        .privacy-note {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 650;
        }

        .privacy-note svg {
            width: 14px;
            height: 14px;
        }

        .support-info-list,
        .channel-list,
        .quick-list {
            display: grid;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .support-info-item {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            min-height: 76px;
            border-top: 1px solid var(--line);
        }

        .support-info-item:first-child {
            border-top: 0;
        }

        .info-label,
        .item-copy {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
        }

        .info-value,
        .item-title {
            display: block;
            margin-top: 3px;
            color: var(--ink);
            font-size: 12px;
            font-weight: 900;
            line-height: 1.35;
        }

        .support-lower-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .channel-item,
        .quick-item {
            min-height: 54px;
            display: grid;
            grid-template-columns: 36px minmax(0, 1fr) 18px;
            align-items: center;
            gap: 12px;
            padding: 9px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: inherit;
            text-decoration: none;
        }

        .channel-list,
        .quick-list {
            gap: 9px;
        }

        .mini-icon {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: var(--blue-soft);
            color: var(--blue);
        }

        .mini-icon svg {
            width: 17px;
            height: 17px;
        }

        .mini-icon.green {
            background: var(--green-soft);
            color: var(--green);
        }

        .mini-icon.violet {
            background: var(--violet-soft);
            color: var(--violet);
        }

        .item-arrow {
            width: 16px;
            height: 16px;
            color: var(--muted);
        }

        .support-note {
            min-height: 86px;
            padding: 18px 22px;
        }

        @media (max-width: 1320px) {
            .contact-grid,
            .support-lower-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .contact-card,
            .support-panel {
                padding: 16px;
            }

            .form-row,
            .contact-card-header,
            .support-panel-header,
            .support-note {
                grid-template-columns: 1fr;
            }

            .contact-actions,
            .contact-button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="support-icon-send" viewBox="0 0 24 24">
            <path d="m4 12 16-7-7 16-2.5-6.5L4 12Z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
            <path d="m10.5 14.5 4-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </symbol>
        <symbol id="support-icon-mail" viewBox="0 0 24 24">
            <path d="M4 6h16v12H4V6Z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
            <path d="m5 7 7 6 7-6" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="support-icon-phone" viewBox="0 0 24 24">
            <path d="M8 5 5.8 7.2c-.8.8-.9 2-.4 3 1.6 3.4 5 6.8 8.4 8.4 1 .5 2.2.4 3-.4L19 16l-3.7-2-1.3 1.3c-1.8-.9-3.4-2.5-4.3-4.3L11 9.7 8 5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="support-icon-pin" viewBox="0 0 24 24">
            <path d="M12 21s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="11" r="2.2" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="support-icon-clock" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 8v5l3 2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="support-icon-paperclip" viewBox="0 0 24 24">
            <path d="m8 12.5 5.5-5.5a3.2 3.2 0 0 1 4.5 4.5L11 18.5a5 5 0 1 1-7.1-7.1l7.4-7.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="support-icon-refresh" viewBox="0 0 24 24">
            <path d="M19 12a7 7 0 1 1-2-4.9M19 5v5h-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="support-icon-shield" viewBox="0 0 24 24">
            <path d="M12 4 19 7v5.3c0 4-2.8 6.7-7 7.7-4.2-1-7-3.7-7-7.7V7l7-3Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="support-icon-message" viewBox="0 0 24 24">
            <path d="M5 6h14v10H8l-3 3V6Z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
            <path d="M8 10h8M8 13h5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </symbol>
        <symbol id="support-icon-file" viewBox="0 0 24 24">
            <path d="M7 4h7l3 3v13H7V4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M14 4v4h4M9 12h6M9 15h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="support-icon-lock" viewBox="0 0 24 24">
            <path d="M7 11h10v8H7v-8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 11V8a3 3 0 0 1 6 0v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
    </svg>

    <div class="contact-page">
        <div class="contact-grid">
            <section class="contact-card" aria-labelledby="contact-form-title">
                <div class="contact-card-header">
                    <span class="support-icon"><svg><use href="#support-icon-send"></use></svg></span>
                    <div>
                        <h2 class="contact-title" id="contact-form-title">Send Us a Message</h2>
                        <p class="contact-copy">Fill out the form below and our team will get back to you as soon as possible.</p>
                    </div>
                </div>

                @if (session('status'))
                    <div class="contact-flash">
                        <svg width="16" height="16"><use href="#icon-check-circle"></use></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <ul class="contact-errors">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form class="contact-form" method="POST" action="{{ route('contact-support.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <label class="field">
                            <span class="field-label">Full Name <span class="required">*</span></span>
                            <input class="field-input" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="Enter your full name" required>
                        </label>
                        <label class="field">
                            <span class="field-label">Email Address <span class="required">*</span></span>
                            <input class="field-input" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
                        </label>
                    </div>

                    <label class="field">
                        <span class="field-label">Subject <span class="required">*</span></span>
                        <input class="field-input" name="subject" type="text" value="{{ old('subject') }}" placeholder="Enter a short subject" required>
                    </label>

                    <div class="form-row">
                        <label class="field">
                            <span class="field-label">Priority <span class="required">*</span></span>
                            <span class="select-wrap">
                                <select class="field-select" name="priority" required>
                                    <option value="">Select priority</option>
                                    @foreach ($priorities as $value => $label)
                                        <option value="{{ $value }}" @selected(old('priority') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <svg><use href="#icon-chevron-down"></use></svg>
                            </span>
                        </label>

                        <label class="field">
                            <span class="field-label">Category <span class="required">*</span></span>
                            <span class="select-wrap">
                                <select class="field-select" name="category" required>
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <svg><use href="#icon-chevron-down"></use></svg>
                            </span>
                        </label>
                    </div>

                    <label class="field">
                        <span class="field-label">Message <span class="required">*</span></span>
                        <span class="textarea-wrap">
                            <textarea class="field-textarea" id="message" name="message" maxlength="2000" placeholder="Please describe your issue in detail..." required>{{ old('message') }}</textarea>
                            <span class="message-count" id="message-count">{{ strlen((string) old('message')) }} / 2000</span>
                        </span>
                    </label>

                    <div class="field">
                        <span class="field-label">Attachment (Optional)</span>
                        <label class="attachment-drop" for="attachment">
                            <span class="support-icon"><svg><use href="#support-icon-paperclip"></use></svg></span>
                            <span>
                                <span class="attachment-title" id="attachment-title">Drag and drop files here, or <span class="attachment-browse">click to browse</span></span>
                                <span class="attachment-copy">Max file size 10MB. Allowed types: PDF, DOC, DOCX, JPG, PNG, TXT</span>
                            </span>
                        </label>
                        <input class="attachment-input" id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,text/plain">
                    </div>

                    <div class="contact-actions">
                        <button class="contact-button contact-button-primary" type="submit">
                            <svg><use href="#support-icon-send"></use></svg>
                            <span>Submit Request</span>
                        </button>
                        <button class="contact-button" type="reset">
                            <svg><use href="#support-icon-refresh"></use></svg>
                            <span>Clear</span>
                        </button>
                    </div>

                    <p class="privacy-note"><svg><use href="#support-icon-lock"></use></svg><span>Your information is secure and will only be used to assist with your request.</span></p>
                </form>
            </section>

            <div class="support-side-grid">
                <section class="support-panel" aria-labelledby="support-info-title">
                    <div class="support-panel-header">
                        <span class="support-icon"><svg><use href="#icon-info"></use></svg></span>
                        <div>
                            <h2 class="support-title" id="support-info-title">Support Information</h2>
                        </div>
                    </div>

                    <ul class="support-info-list">
                        <li class="support-info-item">
                            <span class="support-icon"><svg><use href="#support-icon-mail"></use></svg></span>
                            <span><span class="info-label">Support Email</span><span class="info-value">{{ $supportInfo->support_email }}</span><span class="info-label">We typically respond within 4 hours</span></span>
                        </li>
                        <li class="support-info-item">
                            <span class="support-icon green"><svg><use href="#support-icon-phone"></use></svg></span>
                            <span><span class="info-label">Phone Number</span><span class="info-value">{{ $supportInfo->phone_number }}</span></span>
                        </li>
                        <li class="support-info-item">
                            <span class="support-icon violet"><svg><use href="#support-icon-pin"></use></svg></span>
                            <span><span class="info-label">Office Address</span><span class="info-value">{{ $supportInfo->office_address }}</span></span>
                        </li>
                        <li class="support-info-item">
                            <span class="support-icon amber"><svg><use href="#support-icon-clock"></use></svg></span>
                            <span><span class="info-label">Office Hours</span><span class="info-value">{{ $supportInfo->office_hours }}</span></span>
                        </li>
                    </ul>
                </section>

                <div class="support-lower-grid">
                    <section class="support-panel" aria-labelledby="support-channels-title">
                        <div class="support-panel-header">
                            <span class="support-icon"><svg><use href="#icon-headset"></use></svg></span>
                            <h2 class="support-title" id="support-channels-title">Support Channels</h2>
                        </div>

                        <ul class="channel-list">
                            <li>
                                <a class="channel-item" href="mailto:{{ $supportInfo->support_email }}">
                                    <span class="mini-icon"><svg><use href="#support-icon-mail"></use></svg></span>
                                    <span><span class="item-title">Email Support</span><span class="item-copy">Send us an email and we'll respond</span></span>
                                    <svg class="item-arrow"><use href="#icon-arrow-right"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a class="channel-item" href="tel:0288938888">
                                    <span class="mini-icon green"><svg><use href="#support-icon-phone"></use></svg></span>
                                    <span><span class="item-title">Phone Support</span><span class="item-copy">Speak with our support team</span></span>
                                    <svg class="item-arrow"><use href="#icon-arrow-right"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a class="channel-item" href="{{ route('contact-support') }}#contact-form-title">
                                    <span class="mini-icon violet"><svg><use href="#support-icon-message"></use></svg></span>
                                    <span><span class="item-title">Live Chat</span><span class="item-copy">Chat with us in real-time</span></span>
                                    <svg class="item-arrow"><use href="#icon-arrow-right"></use></svg>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <section class="support-panel" aria-labelledby="quick-help-title">
                        <div class="support-panel-header">
                            <span class="support-icon"><svg><use href="#icon-help"></use></svg></span>
                            <h2 class="support-title" id="quick-help-title">Need Quick Help?</h2>
                        </div>

                        <ul class="quick-list">
                            <li>
                                <a class="quick-item" href="#">
                                    <span class="mini-icon"><svg><use href="#support-icon-file"></use></svg></span>
                                    <span><span class="item-title">How to create a ticket</span><span class="item-copy">Step-by-step guide</span></span>
                                    <svg class="item-arrow"><use href="#icon-arrow-right"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a class="quick-item" href="#">
                                    <span class="mini-icon"><svg><use href="#support-icon-file"></use></svg></span>
                                    <span><span class="item-title">Track ticket status</span><span class="item-copy">Check your ticket updates</span></span>
                                    <svg class="item-arrow"><use href="#icon-arrow-right"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a class="quick-item" href="#">
                                    <span class="mini-icon"><svg><use href="#support-icon-message"></use></svg></span>
                                    <span><span class="item-title">Reset account password</span><span class="item-copy">Password reset instructions</span></span>
                                    <svg class="item-arrow"><use href="#icon-arrow-right"></use></svg>
                                </a>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>

        <section class="support-note" aria-labelledby="support-note-title">
            <span class="support-icon"><svg><use href="#support-icon-shield"></use></svg></span>
            <div>
                <h2 class="support-title" id="support-note-title">We're Here to Help</h2>
                <p class="support-copy">Our team is committed to providing you with the best support experience. We appreciate your patience and will respond as quickly as possible.</p>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const message = document.getElementById('message');
            const count = document.getElementById('message-count');
            const attachment = document.getElementById('attachment');
            const attachmentTitle = document.getElementById('attachment-title');

            if (message && count) {
                message.addEventListener('input', () => {
                    count.textContent = `${message.value.length} / 2000`;
                });
            }

            if (attachment && attachmentTitle) {
                attachment.addEventListener('change', () => {
                    const file = attachment.files && attachment.files[0];
                    attachmentTitle.textContent = file ? `Selected: ${file.name}` : 'Drag and drop files here, or click to browse';
                });
            }
        })();
    </script>
@endpush

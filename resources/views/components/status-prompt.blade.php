@props(['message' => session('status')])

@if ($message)
    <style>
        .app-status-prompt {
            min-height: 54px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding: 10px 14px;
            border: 1px solid color-mix(in srgb, var(--green, #20b96f) 48%, var(--line, #d8e2f2));
            border-radius: 9px;
            background: color-mix(in srgb, var(--green, #20b96f) 16%, var(--panel, #ffffff));
            color: var(--ink, #071b4d);
            box-shadow: var(--shadow-sm, 0 1px 2px rgba(15, 23, 42, .05));
        }

        .app-status-prompt-icon {
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 999px;
            background: var(--green, #20b96f);
            color: #ffffff;
        }

        .app-status-prompt-icon svg {
            width: 16px;
            height: 16px;
        }

        .app-status-prompt-message {
            min-width: 0;
            flex: 1;
            color: var(--ink, #071b4d);
            font-size: 12px;
            font-weight: 850;
            line-height: 1.4;
        }

        .app-status-prompt-close {
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            padding: 0;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: var(--muted, #61708f);
            font-size: 20px;
            line-height: 1;
        }

        .app-status-prompt-close:hover,
        .app-status-prompt-close:focus-visible {
            background: color-mix(in srgb, var(--green, #20b96f) 14%, transparent);
            color: var(--ink, #071b4d);
            outline: none;
        }
    </style>

    <div class="app-status-prompt" role="status" aria-live="polite">
        <span class="app-status-prompt-icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="m6 12 4 4 8-8" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </span>
        <span class="app-status-prompt-message">{{ $message }}</span>
        <button class="app-status-prompt-close" type="button" aria-label="Dismiss notification" onclick="this.closest('.app-status-prompt').remove()">&times;</button>
    </div>
@endif

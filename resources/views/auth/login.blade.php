<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sign in | Xceler8 Support System</title>

    @fonts

    <style>
        :root {
            --ink: #071b4d;
            --muted: #47638f;
            --line: #cbd9ee;
            --panel: #ffffff;
            --canvas: #fbfdff;
            --blue: #1766ff;
            --blue-dark: #0044b3;
            --navy: #082b60;
            --navy-deep: #062752;
            --blue-soft: #e8f0ff;
            --shadow: 0 24px 60px rgba(7, 27, 77, .1);
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
        input {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        svg {
            display: block;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-rows: 1fr auto;
            gap: 34px;
            padding: 24px 28px 22px;
        }

        .page-inner {
            width: min(876px, 100%);
            margin: 0 auto;
        }

        .login-wrap {
            align-self: center;
            display: grid;
            grid-template-columns: minmax(320px, 436px) minmax(340px, 410px);
            gap: 30px;
            align-items: center;
        }

        .welcome-panel {
            min-height: 498px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, #0d3a87 0%, #082b60 42%, #062752 100%);
            color: #ffffff;
        }

        .kicker {
            margin: 0 0 14px;
            color: #9fc5ff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .welcome-title {
            margin: 0;
            color: #ffffff;
            font-size: 52px;
            line-height: .98;
            font-weight: 900;
        }

        .welcome-copy {
            max-width: 360px;
            margin: 18px 0 28px;
            color: #ffffff;
            font-size: 16px;
            line-height: 1.55;
            font-weight: 650;
        }

        .feature-list {
            display: grid;
            gap: 14px;
            margin: 0 0 34px;
            padding: 0;
            list-style: none;
        }

        .feature-item {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
        }

        .feature-icon,
        .hours-icon {
            display: grid;
            place-items: center;
            border: 1px solid rgba(151, 188, 255, .72);
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            color: #bcd4ff;
        }

        .feature-icon {
            width: 42px;
            height: 42px;
        }

        .feature-icon svg {
            width: 23px;
            height: 23px;
        }

        .feature-title {
            display: block;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
        }

        .feature-copy {
            display: block;
            margin-top: 4px;
            color: #e7f0ff;
            font-size: 12px;
            line-height: 1.45;
            font-weight: 650;
        }

        .support-hours {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            min-height: 72px;
            padding: 14px 16px;
            border: 1px solid rgba(151, 188, 255, .38);
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
        }

        .hours-icon {
            width: 34px;
            height: 34px;
            border: 0;
            background: transparent;
            color: #ffffff;
        }

        .hours-icon svg {
            width: 18px;
            height: 18px;
        }

        .hours-title {
            display: block;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
        }

        .hours-copy {
            display: block;
            margin-top: 6px;
            color: #e7f0ff;
            font-size: 12px;
            font-weight: 650;
        }

        .signin-card {
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: var(--shadow);
        }

        .form-title {
            margin: 0;
            color: #061845;
            font-size: 33px;
            line-height: 1.08;
            font-weight: 900;
        }

        .form-subtitle {
            margin: 6px 0 32px;
            color: var(--muted);
            font-size: 16px;
            font-weight: 650;
        }

        .flash {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 16px;
            min-height: 38px;
            padding: 0 12px;
            border: 1px solid #aee9c9;
            border-radius: 8px;
            background: #effbf5;
            color: #067143;
            font-weight: 850;
        }

        .field {
            display: grid;
            gap: 9px;
            margin-bottom: 18px;
        }

        label {
            color: #061845;
            font-size: 13px;
            font-weight: 900;
        }

        .required {
            color: #e84d4d;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            width: 18px;
            height: 18px;
            color: #6c82a8;
            transform: translateY(-50%);
        }

        .input {
            width: 100%;
            height: 48px;
            padding: 0 48px;
            border: 1px solid #b8c8e2;
            border-radius: 7px;
            background: #ffffff;
            color: #061845;
            font-size: 15px;
            font-weight: 650;
            outline: none;
        }

        .input::placeholder {
            color: #6d7fa0;
        }

        .input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(23, 102, 255, .12);
        }

        .field-error {
            color: #9f2424;
            font-size: 11px;
            font-weight: 850;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 11px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #6c82a8;
            transform: translateY(-50%);
        }

        .toggle-password svg {
            width: 19px;
            height: 19px;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: -1px 0 20px;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #52668d;
            font-size: 13px;
            font-weight: 650;
        }

        .checkbox input {
            width: 17px;
            height: 17px;
            border-radius: 3px;
            accent-color: var(--blue);
        }

        .forgot-link {
            color: #005dff;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .button {
            width: 100%;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 16px;
            border: 1px solid transparent;
            border-radius: 7px;
            background: var(--blue);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 14px 28px rgba(23, 102, 255, .18);
        }

        .button svg {
            width: 18px;
            height: 18px;
        }

        .button:hover,
        .button:focus-visible {
            background: #0f55dc;
            outline: none;
        }

        .support-button {
            margin-top: 12px;
            border-color: #d7e2f3;
            background: #f8fbff;
            color: #061845;
            box-shadow: none;
        }

        .support-button:hover,
        .support-button:focus-visible {
            background: #eef4ff;
            color: var(--blue);
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            color: #426392;
            font-size: 12px;
            font-weight: 650;
        }

        .footer a {
            color: #005dff;
            font-weight: 900;
            text-decoration: none;
        }

        @media (max-width: 900px) {
            .page {
                gap: 24px;
                padding: 20px;
            }

            .login-wrap {
                grid-template-columns: 1fr;
            }

            .welcome-panel {
                min-height: auto;
            }
        }

        @media (max-width: 560px) {
            .page {
                padding: 16px;
            }

            .welcome-panel,
            .signin-card {
                padding: 22px;
            }

            .welcome-title {
                font-size: 40px;
            }

            .form-title {
                font-size: 29px;
            }

            .options,
            .footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="icon-arrow-right" viewBox="0 0 24 24">
            <path d="M5 12h14M14 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-mail" viewBox="0 0 24 24">
            <path d="M5 7h14v10H5V7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m6 8 6 5 6-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-lock" viewBox="0 0 24 24">
            <path d="M7 11h10v8H7v-8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 11V8a3 3 0 0 1 6 0v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </symbol>
        <symbol id="icon-eye" viewBox="0 0 24 24">
            <path d="M3.5 12s3-5.5 8.5-5.5S20.5 12 20.5 12s-3 5.5-8.5 5.5S3.5 12 3.5 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="icon-check" viewBox="0 0 24 24">
            <path d="m5 12 4 4 10-10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-support" viewBox="0 0 24 24">
            <path d="M5 13v-1a7 7 0 0 1 14 0v1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M5 13h3v5H5v-5ZM16 13h3v5h-3v-5ZM16 20h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-calendar" viewBox="0 0 24 24">
            <path d="M7 4v3M17 4v3M5 8h14M6 6h12v13H6V6Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-ticket" viewBox="0 0 24 24">
            <path d="M5 8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-tracking" viewBox="0 0 24 24">
            <path d="M6 16V8M6 16h12M18 16v-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="8" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
        <symbol id="icon-shield" viewBox="0 0 24 24">
            <path d="M12 4 5 7v5.2c0 4 2.8 6.9 7 8 4.2-1.1 7-4 7-8V7l-7-3Z" fill="currentColor"/>
        </symbol>
    </svg>

    <main class="page">
        <section class="page-inner login-wrap" aria-labelledby="login-title">
            <aside class="welcome-panel" aria-labelledby="welcome-title">
                <p class="kicker">SAP Business One Support Portal</p>
                <h1 class="welcome-title" id="welcome-title">Welcome Back</h1>
                <p class="welcome-copy">Access tickets, support history, and SAP Business One assistance from one secure workspace.</p>

                <ul class="feature-list">
                    <li class="feature-item">
                        <span class="feature-icon"><svg><use href="#icon-ticket"></use></svg></span>
                        <span>
                            <strong class="feature-title">Expert support</strong>
                            <span class="feature-copy">Certified SAP Business One consultants ready to help.</span>
                        </span>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><svg><use href="#icon-tracking"></use></svg></span>
                        <span>
                            <strong class="feature-title">Faster tracking</strong>
                            <span class="feature-copy">View requests, updates, and ticket progress in real time.</span>
                        </span>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><svg><use href="#icon-shield"></use></svg></span>
                        <span>
                            <strong class="feature-title">Secure access</strong>
                            <span class="feature-copy">Protected portal access for clients, consultants, and admins.</span>
                        </span>
                    </li>
                </ul>

                <div class="support-hours">
                    <span class="hours-icon"><svg><use href="#icon-calendar"></use></svg></span>
                    <span>
                        <strong class="hours-title">Support Hours</strong>
                        <span class="hours-copy">Monday - Friday, 8:30 AM - 6:00 PM</span>
                    </span>
                </div>
            </aside>

            <section class="signin-card">
                <h2 class="form-title" id="login-title">Sign in</h2>
                <p class="form-subtitle">Use your portal account to continue.</p>

                @if (session('status'))
                    <div class="flash">
                        <svg width="16" height="16"><use href="#icon-check"></use></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="field">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon"><use href="#icon-mail"></use></svg>
                            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@xceler8.test" autocomplete="username" required autofocus>
                        </div>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon"><use href="#icon-lock"></use></svg>
                            <input class="input" id="password" name="password" type="password" autocomplete="current-password" required>
                            <button class="toggle-password" type="button" data-password-toggle="password" aria-label="Show password">
                                <svg><use href="#icon-eye"></use></svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="options">
                        <label class="checkbox" for="remember">
                            <input id="remember" name="remember" type="checkbox" value="1">
                            <span>Remember me</span>
                        </label>
                        <a class="forgot-link" href="mailto:support@xceler8.test?subject=Password%20Reset%20Request">Forgot Password?</a>
                    </div>

                    <button class="button" type="submit">
                        <svg><use href="#icon-arrow-right"></use></svg>
                        <span>Sign In</span>
                    </button>

                    <a class="button support-button" href="mailto:support@xceler8.test">
                        <svg><use href="#icon-support"></use></svg>
                        <span>Contact Support</span>
                    </a>
                </form>
            </section>
        </section>

        <footer class="page-inner footer">
            <span>&copy; 2026 Xceler8 Technologies Inc.</span>
            <a href="mailto:support@xceler8.test">support@xceler8.test</a>
        </footer>
    </main>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);

                if (!input) {
                    return;
                }

                const showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>

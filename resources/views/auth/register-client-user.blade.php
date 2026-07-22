<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>Register New User | Xceler8 Support System</title>

    @fonts

    <style>
        :root {
            --ink: #071b4d;
            --muted: #52698f;
            --line: #cbd9ee;
            --blue: #1766ff;
            --navy: #082b60;
            --canvas: #f6f9fe;
            --danger: #a52b2b;
            color-scheme: light;
        }

        * { box-sizing: border-box; }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at 8% 8%, rgba(23, 102, 255, .1), transparent 28%),
                var(--canvas);
            color: var(--ink);
            font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
        }

        button, input { font: inherit; }

        .page {
            width: min(1080px, calc(100% - 36px));
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(270px, 340px) minmax(0, 1fr);
            align-items: center;
            gap: 28px;
            margin: 0 auto;
            padding: 32px 0;
        }

        .intro {
            align-self: stretch;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 42px 34px;
            border-radius: 12px;
            background: linear-gradient(145deg, #0d3a87, #062752);
            color: #fff;
        }

        .kicker {
            margin: 0 0 14px;
            color: #a9caff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .intro h1 {
            margin: 0;
            font-size: clamp(34px, 5vw, 50px);
            line-height: 1;
            font-weight: 900;
        }

        .intro-copy {
            margin: 20px 0 0;
            color: #e2ecff;
            font-size: 15px;
            line-height: 1.65;
            font-weight: 650;
        }

        .benefits {
            display: grid;
            gap: 14px;
            margin: 30px 0 0;
            padding: 0;
            list-style: none;
        }

        .benefits li {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }

        .check {
            width: 25px;
            height: 25px;
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, .14);
        }

        .check svg { width: 14px; height: 14px; }

        .card {
            padding: 32px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 24px 60px rgba(7, 27, 77, .1);
        }

        .card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
        }

        .card-title {
            margin: 0;
            font-size: 30px;
            line-height: 1.1;
            font-weight: 900;
        }

        .card-subtitle {
            margin: 7px 0 0;
            color: var(--muted);
            font-weight: 650;
        }

        .back-link {
            color: var(--blue);
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .error-summary {
            margin: 0 0 20px;
            padding: 12px 14px 12px 32px;
            border: 1px solid #f0bcbc;
            border-radius: 8px;
            background: #fff6f6;
            color: var(--danger);
            font-size: 12px;
            font-weight: 800;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 19px 18px;
        }

        .field { display: grid; gap: 8px; }
        .field.full { grid-column: 1 / -1; }

        label {
            color: #061845;
            font-size: 13px;
            font-weight: 900;
        }

        .required { color: #df3f3f; }

        .input {
            width: 100%;
            height: 47px;
            padding: 0 13px;
            border: 1px solid #b8c8e2;
            border-radius: 7px;
            background: #fff;
            color: #061845;
            font-weight: 650;
            outline: none;
        }

        .input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(23, 102, 255, .12);
        }

        .input[readonly] {
            background: #f2f6fc;
            color: #3e557d;
            cursor: default;
        }

        .password-wrap { position: relative; }
        .password-wrap .input { padding-right: 46px; }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 8px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #667da5;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .toggle-password svg { width: 19px; height: 19px; }

        .field-error {
            color: var(--danger);
            font-size: 11px;
            font-weight: 800;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 27px;
        }

        .button {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 22px;
            border: 1px solid #d3deef;
            border-radius: 7px;
            background: #f8fbff;
            color: var(--ink);
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            border-color: var(--blue);
            background: var(--blue);
            color: #fff;
            box-shadow: 0 12px 24px rgba(23, 102, 255, .2);
        }

        .button-primary:disabled { opacity: .55; cursor: not-allowed; }

        @media (max-width: 820px) {
            .page { grid-template-columns: 1fr; padding: 20px 0; }
            .intro { align-self: auto; padding: 28px; }
            .benefits { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 600px) {
            .page { width: min(100% - 24px, 1080px); }
            .card { padding: 24px 18px; }
            .card-head { flex-direction: column-reverse; gap: 12px; }
            .form-grid, .benefits { grid-template-columns: 1fr; }
            .field { grid-column: 1 / -1; }
            .actions { flex-direction: column-reverse; }
            .button { width: 100%; }
        }
    </style>
</head>
<body>
    <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
        <symbol id="icon-check" viewBox="0 0 24 24">
            <path d="m5 12 4 4 10-10" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="icon-eye" viewBox="0 0 24 24">
            <path d="M3.5 12s3-5.5 8.5-5.5S20.5 12 20.5 12s-3 5.5-8.5 5.5S3.5 12 3.5 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
        </symbol>
    </svg>

    <main class="page">
        <aside class="intro">
            <p class="kicker">Xceler8 Support Portal</p>
            <h1>Create your account</h1>
            <p class="intro-copy">Register under your company to securely access customer support and manage requests in one place.</p>
            <ul class="benefits">
                <li><span class="check"><svg><use href="#icon-check"></use></svg></span>Track support tickets</li>
                <li><span class="check"><svg><use href="#icon-check"></use></svg></span>Review ticket history</li>
                <li><span class="check"><svg><use href="#icon-check"></use></svg></span>Connect with consultants</li>
            </ul>
        </aside>

        <section class="card" aria-labelledby="register-title">
            <div class="card-head">
                <div>
                    <h2 class="card-title" id="register-title">Register New User</h2>
                    <p class="card-subtitle">All fields are required to create your customer account.</p>
                </div>
                <a class="back-link" href="{{ route('login') }}">Back to Sign In</a>
            </div>

            @if ($errors->any())
                <ul class="error-summary" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="field full">
                        <label for="company-name">Company Name <span class="required">*</span></label>
                        <input class="input" id="company-name" name="company_name" value="{{ old('company_name') }}" maxlength="255" autocomplete="organization" placeholder="Enter your registered company name" required>
                        @error('company_name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="first-name">First Name <span class="required">*</span></label>
                        <input class="input" id="first-name" name="first_name" value="{{ old('first_name') }}" maxlength="80" autocomplete="given-name" required autofocus>
                        @error('first_name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="last-name">Last Name <span class="required">*</span></label>
                        <input class="input" id="last-name" name="last_name" value="{{ old('last_name') }}" maxlength="80" autocomplete="family-name" required>
                        @error('last_name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field full">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required>
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field full">
                        <label for="role">Role</label>
                        <input class="input" id="role" name="role" value="Customer" readonly aria-readonly="true">
                    </div>

                    <div class="field">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="password-wrap">
                            <input class="input" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="toggle-password" type="button" data-password-toggle="password" aria-label="Show password"><svg><use href="#icon-eye"></use></svg></button>
                        </div>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="password-confirmation">Password Validation <span class="required">*</span></label>
                        <div class="password-wrap">
                            <input class="input" id="password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="toggle-password" type="button" data-password-toggle="password-confirmation" aria-label="Show password validation"><svg><use href="#icon-eye"></use></svg></button>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <a class="button" href="{{ route('login') }}">Cancel</a>
                    <button class="button button-primary" type="submit">Create Customer Account</button>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);

                if (!input) return;

                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>
